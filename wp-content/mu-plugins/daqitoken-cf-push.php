<?php
/**
 * Plugin Name: DaqiToken CF Static Push
 * Description: Publish/update a post or product -> push its rendered HTML snapshot plus newly referenced uploads to the Cloudflare R2 static layer, so anonymous visitors get the new page even when the origin is asleep. Config: /etc/daqitoken-cf.ini
 * Version: 1.0.0
 * Author: DaqiToken
 */

if (!defined('ABSPATH')) {
    exit;
}

final class DaqiToken_CF_Push
{
    private const CONFIG_FILE = '/etc/daqitoken-cf.ini';
    private const LOG_FILE = '/tmp/daqitoken-push.log';
    private const POST_TYPES = ['post', 'product', 'page'];
    private const ASSET_RE = '#(?:https?:)?//[^/"\s]+(/wp-content/uploads/[^"\'\s)\]\?]+)#i';
    private const ASSET_EXTS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'otf', 'pdf', 'mp4', 'webm'];

    /** @var int[] */
    private static array $queue = [];

    /** @var array<string,bool>|null */
    private static $knownKeys = null;

    public static function boot(): void
    {
        add_action('save_post', [self::class, 'onSavePost'], 20, 3);
        add_action('shutdown', [self::class, 'onShutdown'], 99);

        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::add_command('daqitoken push', [self::class, 'cliPush']);
            WP_CLI::add_command('daqitoken push-url', [self::class, 'cliPushUrl']);
            WP_CLI::add_command('daqitoken push-batch', [self::class, 'cliPushBatch']);
        }
    }

    private static function config(): ?array
    {
        static $cfg = false;
        if ($cfg !== false) {
            return $cfg ?: null;
        }
        if (!is_readable(self::CONFIG_FILE)) {
            $cfg = [];
            return null;
        }
        $raw = @parse_ini_file(self::CONFIG_FILE, false, INI_SCANNER_RAW);
        if (!is_array($raw) || empty($raw['enabled'])) {
            $cfg = [];
            return null;
        }
        $cfg = $raw;
        return $cfg;
    }

    private static function log(string $msg): void
    {
        @file_put_contents(
            self::LOG_FILE,
            '[' . gmdate('Y-m-d H:i:s') . '] ' . $msg . "\n",
            FILE_APPEND
        );
    }

    public static function onSavePost($post_id, $post = null, $update = false): void
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }
        $post = $post ?: get_post($post_id);
        if (!$post || $post->post_status !== 'publish') {
            return;
        }
        if (!in_array($post->post_type, self::POST_TYPES, true)) {
            return;
        }
        if (!self::config()) {
            return;
        }
        self::$queue[$post_id] = $post_id;
    }

    public static function onShutdown(): void
    {
        if (empty(self::$queue)) {
            return;
        }
        $ids = array_values(self::$queue);
        self::$queue = [];

        // Flush the editor response first, then push in the background.
        if (function_exists('fastcgi_finish_request')) {
            @fastcgi_finish_request();
        }
        @ignore_user_abort(true);
        @set_time_limit(180);

        foreach ($ids as $id) {
            try {
                self::pushPost((int) $id);
            } catch (Throwable $e) {
                self::log('ERR post=' . $id . ' ' . $e->getMessage());
            }
        }
    }

    public static function cliPush(array $args, array $assoc): void
    {
        if (!self::config()) {
            WP_CLI::error('Config ' . self::CONFIG_FILE . ' missing or disabled.');
        }
        $id = isset($args[0]) ? (int) $args[0] : 0;
        if (!$id) {
            WP_CLI::error('Usage: wp daqitoken push <ID>');
        }
        self::pushPost($id);
        WP_CLI::success('pushed post ' . $id);
    }

    public static function cliPushUrl(array $args, array $assoc): void
    {
        if (!self::config()) {
            WP_CLI::error('Config ' . self::CONFIG_FILE . ' missing or disabled.');
        }
        $url = isset($args[0]) ? trim($args[0]) : '';
        if ($url === '' || !preg_match('#^https?://#i', $url)) {
            WP_CLI::error('Usage: wp daqitoken push-url <URL>');
        }
        self::pushUrl($url);
        WP_CLI::success('pushed url ' . $url);
    }

    public static function cliPushBatch(array $args, array $assoc): void
    {
        if (!self::config()) {
            WP_CLI::error('Config ' . self::CONFIG_FILE . ' missing or disabled.');
        }
        $file = isset($args[0]) ? trim($args[0]) : '';
        if ($file === '' || !is_readable($file)) {
            WP_CLI::error('Usage: wp daqitoken push-batch <file-with-one-url-per-line>');
        }
        $urls = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $n = 0;
        foreach ((array) $urls as $url) {
            $url = trim($url);
            if ($url === '' || !preg_match('#^https?://#i', $url)) {
                continue;
            }
            self::pushUrl($url, true);
            $n++;
        }
        WP_CLI::success('pushed ' . $n . ' urls');
    }

    private static function snapshotKey(string $url): string
    {
        $path = (string) wp_parse_url($url, PHP_URL_PATH);
        if ($path === '') {
            $path = '/';
        }
        if (strlen($path) > 1) {
            $path = rtrim($path, '/');
        }
        $path = ltrim($path, '/');
        return $path === '' ? 'index' : $path;
    }

    private static function fetchHtml(string $url): ?string
    {
        $resp = wp_remote_get($url, [
            'timeout' => 30,
            'redirection' => 3,
            'user-agent' => 'Mozilla/5.0 (compatible; daqitoken-cf-push/1.0)',
            'headers' => [
                'Cache-Control' => 'no-cache',
                // Matches nginx $skip_cache rules so the origin page cache is bypassed
                // and freshly generated HTML (SEO/GEO filters) is captured.
                'Cookie' => 'comment_author=daqitoken-push',
            ],
        ]);
        if (is_wp_error($resp)) {
            self::log('fetch fail ' . $url . ' ' . $resp->get_error_message());
            return null;
        }
        $code = (int) wp_remote_retrieve_response_code($resp);
        $ct = (string) wp_remote_retrieve_header($resp, 'content-type');
        if ($code !== 200 || (stripos($ct, 'text/html') === false)) {
            self::log('fetch bad ' . $url . ' code=' . $code . ' ct=' . $ct);
            return null;
        }
        return wp_remote_retrieve_body($resp);
    }

    /**
     * List all object keys currently in the bucket (paginated), cached briefly.
     * Lets us skip re-uploading assets that already exist.
     *
     * @return array<string,bool>|null null when listing fails (caller uploads anyway)
     */
    private static function knownKeys(): ?array
    {
        if (self::$knownKeys !== null) {
            return self::$knownKeys;
        }
        $cfg = self::config();
        if (!$cfg) {
            return null;
        }
        $keys = [];
        $cursor = '';
        for ($i = 0; $i < 20; $i++) {
            $url = sprintf(
                'https://api.cloudflare.com/client/v4/accounts/%s/r2/buckets/%s/objects?per_page=1000',
                rawurlencode($cfg['account_id']),
                rawurlencode($cfg['bucket'])
            );
            if ($cursor !== '') {
                $url .= '&cursor=' . rawurlencode($cursor);
            }
            $resp = wp_remote_get($url, [
                'timeout' => 30,
                'headers' => ['Authorization' => 'Bearer ' . $cfg['api_token']],
            ]);
            if (is_wp_error($resp) || (int) wp_remote_retrieve_response_code($resp) !== 200) {
                return null;
            }
            $data = json_decode(wp_remote_retrieve_body($resp), true);
            if (!is_array($data) || !isset($data['result']) || !is_array($data['result'])) {
                return null;
            }
            foreach ($data['result'] as $o) {
                if (isset($o['key'])) {
                    $keys[$o['key']] = true;
                }
            }
            $truncated = !empty($data['result_info']['is_truncated']);
            $cursor = (string) ($data['result_info']['cursor'] ?? '');
            if (!$truncated || $cursor === '') {
                break;
            }
        }
        self::$knownKeys = $keys;
        return $keys;
    }

    private static function r2Put(string $key, string $body, string $contentType): bool
    {
        $cfg = self::config();
        if (!$cfg) {
            return false;
        }
        $endpoint = sprintf(
            'https://api.cloudflare.com/client/v4/accounts/%s/r2/buckets/%s/objects/%s',
            rawurlencode($cfg['account_id']),
            rawurlencode($cfg['bucket']),
            rawurlencode($key)
        );
        $resp = wp_remote_request($endpoint, [
            'method' => 'PUT',
            'timeout' => 60,
            'headers' => [
                'Authorization' => 'Bearer ' . $cfg['api_token'],
                'Content-Type' => $contentType,
            ],
            'body' => $body,
        ]);
        if (is_wp_error($resp)) {
            self::log('r2 put fail ' . $key . ' ' . $resp->get_error_message());
            return false;
        }
        $code = (int) wp_remote_retrieve_response_code($resp);
        if ($code < 200 || $code >= 300) {
            self::log('r2 put bad ' . $key . ' code=' . $code);
            return false;
        }
        return true;
    }

    private static function pushUrl(string $url, bool $withAssets = true): void
    {
        $html = self::fetchHtml($url);
        if ($html === null) {
            return;
        }
        $key = self::snapshotKey($url);
        if (self::r2Put($key, $html, 'text/html; charset=utf-8')) {
            self::log('snapshot ' . $key . ' (' . strlen($html) . 'b)');
        }
        if ($withAssets) {
            self::pushAssets($html);
        }
    }

    private static function pushAssets(string $html): void
    {
        if (!preg_match_all(self::ASSET_RE, $html, $m)) {
            return;
        }
        $known = self::knownKeys();
        $paths = array_unique($m[1]);
        $done = [];
        $uploaded = 0;
        $skipped = 0;
        foreach ($paths as $path) {
            $path = strtok($path, '?');
            if ($path === false || $path === '' || isset($done[$path])) {
                continue;
            }
            $done[$path] = true;
            $ext = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));
            if (!in_array($ext, self::ASSET_EXTS, true)) {
                continue;
            }
            $key = ltrim(rawurldecode($path), '/');
            if ($known !== null && isset($known[$key])) {
                $skipped++;
                continue;
            }
            $local = ABSPATH . ltrim($path, '/');
            if (!is_file($local) || !is_readable($local)) {
                continue;
            }
            if (filesize($local) > 15 * 1024 * 1024) {
                continue;
            }
            $ct = wp_check_filetype($local)['type'] ?: 'application/octet-stream';
            if (self::r2Put($key, (string) file_get_contents($local), $ct)) {
                self::log('asset ' . $key);
                $uploaded++;
            }
        }
        self::log('assets done uploaded=' . $uploaded . ' skipped=' . $skipped);
    }

    public static function pushPost(int $postId): void
    {
        $post = get_post($postId);
        if (!$post || $post->post_status !== 'publish') {
            return;
        }
        $primary = [];
        $archives = [];

        $permalink = get_permalink($postId);
        if ($permalink) {
            $primary[] = $permalink;
        }

        $archives[] = home_url('/');
        if ($post->post_type === 'product') {
            $archives[] = home_url('/shop/');
            $taxes = ['product_cat', 'product_tag'];
        } elseif ($post->post_type === 'post') {
            $taxes = ['category', 'post_tag'];
        } else {
            $taxes = [];
        }
        foreach ($taxes as $tax) {
            $terms = get_the_terms($postId, $tax);
            if (is_array($terms)) {
                foreach ($terms as $term) {
                    $link = get_term_link($term);
                    if (!is_wp_error($link)) {
                        $archives[] = $link;
                    }
                }
            }
        }

        // Detail page: snapshot + referenced uploads (new product images matter).
        foreach (array_unique($primary) as $u) {
            self::pushUrl($u, true);
        }
        // Archive pages may reference hundreds of products/images: snapshot only.
        foreach (array_diff(array_unique($archives), array_unique($primary)) as $u) {
            self::pushUrl($u, false);
        }
        self::log('push done post=' . $postId . ' type=' . $post->post_type
            . ' primary=' . count(array_unique($primary)) . ' archives=' . count(array_unique($archives)));
    }
}

DaqiToken_CF_Push::boot();
