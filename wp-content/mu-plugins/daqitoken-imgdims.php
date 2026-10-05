<?php
/**
 * Plugin Name: DaqiToken Image Dimensions
 * Description: Injects intrinsic width/height into <img> tags that lack them, eliminating layout shift (CLS).
 * Version: 1.0.0
 * Author: DaqiToken
 */

if (!defined('ABSPATH')) {
    exit;
}

final class DaqiToken_ImgDims
{
    /** @var array<string, array{0:int,1:int}|null> */
    private static $cache = [];
    private static $sitePath = '';

    public static function boot(): void
    {
        self::$sitePath = rtrim(ABSPATH, '/');
        add_action('template_redirect', [self::class, 'start'], 1);
    }

    public static function start(): void
    {
        if (is_admin() || is_feed() || is_robots() || (defined('REST_REQUEST') && REST_REQUEST)) {
            return;
        }
        ob_start([self::class, 'rewrite']);
    }

    public static function rewrite($html)
    {
        if (!is_string($html) || $html === '' || stripos($html, '<img') === false) {
            return $html;
        }
        return preg_replace_callback('/<img\b[^>]*>/i', [self::class, 'fixTag'], $html);
    }

    public static function fixTag($m)
    {
        $tag = $m[0];

        if (!preg_match('/\bsrc=("|\')([^"\']+)\1/i', $tag, $sm)) {
            return $tag;
        }
        $src = html_entity_decode($sm[2], ENT_QUOTES);
        if (stripos($src, 'data:') === 0) {
            return $tag;
        }

        $hasW = preg_match('/\bwidth=("|\')(\d+)\1/i', $tag, $wm);
        $hasH = preg_match('/\bheight=("|\')(\d+)\1/i', $tag, $hm);
        if ($hasW && $hasH) {
            return $tag;
        }

        $dims = self::intrinsic($src);
        if (!$dims) {
            return $tag;
        }
        [$iw, $ih] = $dims;
        if ($iw < 1 || $ih < 1) {
            return $tag;
        }

        if ($hasW) {
            $h = max(1, (int) round(((int) $wm[2]) * $ih / $iw));
            return self::inject($tag, null, $h);
        }
        if ($hasH) {
            $w = max(1, (int) round(((int) $hm[2]) * $iw / $ih));
            return self::inject($tag, $w, null);
        }
        return self::inject($tag, $iw, $ih);
    }

    private static function inject($tag, $w, $h)
    {
        $add = '';
        if ($w !== null && !preg_match('/\bwidth=/i', $tag)) {
            $add .= ' width="' . $w . '"';
        }
        if ($h !== null && !preg_match('/\bheight=/i', $tag)) {
            $add .= ' height="' . $h . '"';
        }
        if ($add === '') {
            return $tag;
        }
        return preg_replace('/<img\b/i', '<img' . $add, $tag, 1);
    }

    private static function intrinsic($src)
    {
        $path = self::localPath($src);
        if (!$path) {
            return null;
        }
        if (array_key_exists($path, self::$cache)) {
            return self::$cache[$path];
        }
        self::$cache[$path] = self::readDims($path);
        return self::$cache[$path];
    }

    private static function localPath($src)
    {
        $p = parse_url($src, PHP_URL_PATH);
        if (!$p) {
            return null;
        }
        $p = rawurldecode($p);
        $pos = strpos($p, '/wp-content/');
        if ($pos === false) {
            return null;
        }
        $full = self::$sitePath . substr($p, $pos);
        if (!is_file($full)) {
            return null;
        }
        return $full;
    }

    private static function readDims($path)
    {
        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'svg') {
            return self::svgDims($path);
        }
        $info = @getimagesize($path);
        if (is_array($info) && $info[0] > 0 && $info[1] > 0) {
            return [(int) $info[0], (int) $info[1]];
        }
        return null;
    }

    private static function svgDims($path)
    {
        $head = @file_get_contents($path, false, null, 0, 8192);
        if ($head === false) {
            return null;
        }
        if (
            preg_match('/\bwidth=("|\')([\d.]+)(px)?\1/i', $head, $w) &&
            preg_match('/\bheight=("|\')([\d.]+)(px)?\1/i', $head, $h)
        ) {
            $iw = (float) $w[2];
            $ih = (float) $h[2];
            if ($iw > 0 && $ih > 0) {
                return [(int) round($iw), (int) round($ih)];
            }
        }
        if (preg_match('/\bviewBox=("|\')\s*[-\d.]+\s+[-\d.]+\s+([\d.]+)\s+([\d.]+)\s*\1/i', $head, $vb)) {
            $iw = (float) $vb[2];
            $ih = (float) $vb[3];
            if ($iw > 0 && $ih > 0) {
                return [(int) round($iw), (int) round($ih)];
            }
        }
        return null;
    }
}

DaqiToken_ImgDims::boot();
