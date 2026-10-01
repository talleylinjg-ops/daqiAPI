<?php
/**
 * Plugin Name: DaqiToken Performance
 * Description: Front-end performance hints: resource preconnect, LCP image preload/fetchpriority, async image decoding, and avatar lazy-loading.
 * Version: 1.0.0
 * Author: DaqiToken
 */

if (!defined('ABSPATH')) {
    exit;
}

final class DaqiToken_Perf
{
    public static function boot(): void
    {
        add_filter('wp_resource_hints', [self::class, 'resourceHints'], 10, 2);
        add_filter('wp_get_attachment_image_attributes', [self::class, 'imageAttrs'], 20, 3);
        add_filter('get_avatar', [self::class, 'avatar'], 20, 6);
        add_action('wp_head', [self::class, 'preloadLcp'], 2);
    }

    /**
     * Preconnect to third-party origins that are on the critical path:
     * web fonts and comment avatars.
     */
    public static function resourceHints($urls, $relation)
    {
        if ($relation !== 'preconnect') {
            return $urls;
        }
        $existing = array_map('strtolower', (array) $urls);
        foreach (['https://fonts.gstatic.com', 'https://fonts.googleapis.com', 'https://secure.gravatar.com'] as $hint) {
            if (!in_array(strtolower($hint), $existing, true)) {
                $urls[] = $hint;
            }
        }
        return $urls;
    }

    /** Decode images asynchronously to keep the main thread free during load. */
    public static function imageAttrs($attr, $attachment, $size)
    {
        if (empty($attr['decoding'])) {
            $attr['decoding'] = 'async';
        }
        return $attr;
    }

    /** Lazy-load + async-decode comment avatars (they are always below the fold). */
    public static function avatar($avatar, $id_or_email, $size, $default, $alt, $args)
    {
        if (!is_string($avatar) || stripos($avatar, '<img') === false) {
            return $avatar;
        }
        if (stripos($avatar, 'loading=') === false) {
            $avatar = str_replace('<img ', '<img loading="lazy" ', $avatar);
        }
        if (stripos($avatar, 'decoding=') === false) {
            $avatar = str_replace('<img ', '<img decoding="async" ', $avatar);
        }
        return $avatar;
    }

    /**
     * Preload the most likely Largest Contentful Paint image (product main
     * image on product pages, featured image on posts) so it starts fetching
     * alongside the render-blocking CSS instead of after HTML parse.
     */
    public static function preloadLcp()
    {
        if (is_admin() || is_feed()) {
            return;
        }

        $url = $srcset = $sizes = '';

        if (function_exists('is_product') && is_product()) {
            global $product;
            if ($product && method_exists($product, 'get_image_id') && ($id = $product->get_image_id())) {
                $size   = 'woocommerce_single';
                $url    = wp_get_attachment_image_url($id, $size);
                $srcset = wp_get_attachment_image_srcset($id, $size);
                $sizes  = wp_get_attachment_image_sizes($id, $size);
            }
        } elseif (is_singular('post')) {
            $id = get_post_thumbnail_id();
            if ($id) {
                $size   = 'large';
                $url    = wp_get_attachment_image_url($id, $size);
                $srcset = wp_get_attachment_image_srcset($id, $size);
                $sizes  = wp_get_attachment_image_sizes($id, $size);
            }
        }

        if (!$url) {
            return;
        }

        printf(
            '<link rel="preload" as="image" href="%s"%s%s fetchpriority="high">' . "\n",
            esc_url($url),
            $srcset ? ' imagesrcset="' . esc_attr($srcset) . '"' : '',
            $sizes ? ' imagesizes="' . esc_attr($sizes) . '"' : ''
        );
    }
}

DaqiToken_Perf::boot();
