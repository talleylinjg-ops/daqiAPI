<?php
/**
 * Plugin Name: DaqiToken SEO & GEO
 * Description: Generative Engine Optimization (GEO) and on-page SEO helpers: allow AI crawlers in robots.txt, enrich Organization entity, provide automatic meta-description fallbacks, and emit discussion/collection/product structured data for bbPress and WooCommerce.
 * Version: 1.1.0
 * Author: DaqiToken
 */

if (!defined('ABSPATH')) {
    exit;
}

final class DaqiToken_SEO_GEO
{
    /** AI / answer-engine crawlers we explicitly welcome. */
    private const AI_BOTS = [
        'GPTBot',
        'OAI-SearchBot',
        'ChatGPT-User',
        'ClaudeBot',
        'Claude-Web',
        'anthropic-ai',
        'PerplexityBot',
        'Perplexity-User',
        'Google-Extended',
        'Applebot-Extended',
        'CCBot',
        'Amazonbot',
        'Bytespider',
        'cohere-ai',
        'DuckAssistBot',
        'YouBot',
        'Meta-ExternalAgent',
        'MistralAI-User',
        'Diffbot',
    ];

    public static function boot(): void
    {
        add_filter('robots_txt', [self::class, 'robotsTxt'], 20, 2);
        add_filter('wp_robots', [self::class, 'robotsMeta'], 20);
        add_filter('wpseo_schema_graph', [self::class, 'schemaGraph'], 20, 2);
        add_filter('wpseo_metadesc', [self::class, 'metaDescFallback'], 20);
        add_filter('wpseo_opengraph_desc', [self::class, 'metaDescFallback'], 20);
        add_filter('wpseo_twitter_description', [self::class, 'metaDescFallback'], 20);
        add_filter('wpseo_title', [self::class, 'titleFallback'], 20);
        add_shortcode('daqitoken_destinations', [self::class, 'destinationsShortcode']);
        add_filter('wpseo_schema_graph', [self::class, 'destinationsSchema'], 21, 2);
        add_filter('wpseo_schema_graph', [self::class, 'forumSchema'], 22, 2);
        add_filter('wpseo_schema_graph', [self::class, 'schemaEnhance'], 23, 2);
        add_filter('woocommerce_structured_data_product', [self::class, 'productStructuredData'], 30, 2);
        add_filter('wpseo_canonical', [self::class, 'duplicateCanonical'], 20);
        add_filter('wpseo_sitemap_entry', [self::class, 'excludeDuplicateFromSitemap'], 10, 3);
    }

    /**
     * Duplicate product IDs mapped to the single canonical product. The mapped
     * (duplicate) URL keeps returning 200 for old links but declares the
     * canonical product and is dropped from the sitemap, so ranking signals
     * consolidate on one URL. Pair with WooCommerce catalog visibility
     * "hidden" and a canonical category assignment on the target product.
     */
    private const DUP_PRODUCT_CANONICAL = [
        138 => 39, // japan-esim-1gb-7-days-2 -> japan-esim-1gb-7-days
    ];

    public static function duplicateCanonical($canonical)
    {
        if (function_exists('is_singular') && is_singular('product')) {
            $id = get_queried_object_id();
            if (isset(self::DUP_PRODUCT_CANONICAL[$id])) {
                $target = get_permalink(self::DUP_PRODUCT_CANONICAL[$id]);
                if ($target) {
                    return $target;
                }
            }
        }
        return $canonical;
    }

    public static function excludeDuplicateFromSitemap($url, $type, $post)
    {
        $id = 0;
        if (is_object($post) && isset($post->ID)) {
            $id = (int) $post->ID;
        } elseif (is_numeric($post)) {
            $id = (int) $post;
        }
        if ($id > 0 && in_array($id, array_keys(self::DUP_PRODUCT_CANONICAL), true)) {
            return false;
        }
        return $url;
    }

    /**
     * Explicitly allow known AI / answer-engine crawlers. The generic
     * "User-agent: *" group already allows them; per-bot groups make the
     * intent unambiguous and survive stricter default groups.
     */
    public static function robotsTxt($output, $public)
    {
        if (!$public) {
            return $output;
        }
        $out = (string) $output;

        // Inject transactional / thin-path rules into the primary "*" group
        // (directives attach to the most recent User-agent line, so they must
        // live inside that group, not at the end of the file).
        $extra = "Disallow: /?s=\n"
            . "Disallow: /search/\n"
            . "Disallow: /cart/\n"
            . "Disallow: /checkout/\n"
            . "Disallow: /my-account/\n"
            . "Disallow: /wc-api/\n";
        $out = preg_replace('/^User-agent:\s*\*\s*$/mi', "User-agent: *\n" . $extra, $out, 1);

        $out = rtrim($out) . "\n\n";
        $out .= "# AI / answer-engine crawlers explicitly allowed\n";
        foreach (self::AI_BOTS as $bot) {
            $out .= "User-agent: {$bot}\nAllow: /\n\n";
        }
        return rtrim($out) . "\n";
    }

    /**
     * Keep internal search result pages out of the index (thin, duplicate
     * content). Complements the robots.txt disallow because external links
     * and cached URLs can still expose them.
     */
    public static function robotsMeta($robots)
    {
        if (is_search()) {
            $robots['noindex'] = true;
            $robots['follow'] = true;
        }
        return $robots;
    }

    /**
     * Enrich the Organization entity in Yoast's schema graph so answer engines
     * can disambiguate what DaqiToken is and sells. No invented social profiles.
     *
     * Yoast (>= v15) builds a single @graph and exposes it via "wpseo_schema_graph".
     */
    public static function schemaGraph($graph, $context = null)
    {
        if (!is_array($graph)) {
            return $graph;
        }
        foreach ($graph as $index => $node) {
            if (!is_array($node) || empty($node['@type'])) {
                continue;
            }
            $types = (array) $node['@type'];
            if (!in_array('Organization', $types, true)) {
                continue;
            }
            $node['slogan'] = 'Digital life, connected.';
            $node['knowsAbout'] = [
                'Travel eSIM',
                'eSIM data plans',
                'Mobile connectivity',
                'Virtual private network',
                'WireGuard',
                'Large language model API gateway',
                'AI token credits',
                'Roaming charges',
            ];
            $node['areaServed'] = [
                '@type' => 'Place',
                'name'  => 'Worldwide',
            ];
            if (empty($node['description'])) {
                $node['description'] = 'DaqiToken is an online-only digital connectivity store selling instant travel eSIM data plans for 200+ countries, private WireGuard VPN service on dedicated nodes, and prepaid TOKEN credits for a unified OpenAI-compatible LLM API gateway.';
            }
            // Prefer the square site icon (512x512) as the Organization logo;
            // a wide social-share banner is not a suitable logo asset.
            if (function_exists('get_site_icon_url')) {
                $icon = get_site_icon_url(512);
                if ($icon) {
                    $node['logo'] = [
                        '@type'  => 'ImageObject',
                        'url'    => $icon,
                        'width'  => 512,
                        'height' => 512,
                    ];
                }
            }
            $graph[$index] = $node;
        }
        return $graph;
    }

    /**
     * When Yoast has no meta description (country eSIM categories, bbPress
     * forum/topics), synthesize a useful one. Never overrides existing text.
     */
    public static function metaDescFallback($desc)
    {
        $desc = is_string($desc) ? trim($desc) : '';
        if ($desc !== '') {
            // Yoast can emit long, auto-generated descriptions (term or first
            // paragraph). Cap every non-empty description at a snippet-safe
            // length so meta, OpenGraph and Twitter stay within limits.
            return self::trimText($desc, 158);
        }

        // Country / product categories.
        if (function_exists('is_product_category') && is_product_category()) {
            $term = get_queried_object();
            if ($term && !empty($term->name)) {
                $name = $term->name;
                if ($term->slug === 'esim') {
                    return 'Travel eSIM plans for 200+ countries. Instant QR delivery, no roaming fees, prepaid data from $2.4. See coverage and buy in minutes with DaqiToken.';
                }
                if ($term->slug === 'vpn') {
                    return 'DaqiToken VPN plans: private WireGuard on dedicated nodes, zero logs, multiple regions. One account, fast setup, 14-day refund on unused plans.';
                }
                if ($term->slug === 'token') {
                    return 'Buy TOKEN credits for the DaqiToken unified LLM gateway. Prepaid, OpenAI-compatible access to DeepSeek, Kimi and Qwen, credited in 1-2 minutes.';
                }
                // Country categories: prefer the unique, hand-tuned term description.
                $termDesc = isset($term->description) ? trim(wp_strip_all_tags($term->description)) : '';
                if ($termDesc !== '') {
                    return self::trimText($termDesc, 158);
                }
                $min = self::minCategoryPrice((int) $term->term_id);
                $price = $min > 0 ? ' from $' . number_format($min, 2) : '';
                return self::trimText(sprintf(
                    '%1$s eSIM plans for instant data on arrival. Prepaid %1$s data%2$s, QR delivered by email in minutes, no roaming fees. Works on eSIM-compatible phones.',
                    $name,
                    $price
                ), 158);
            }
        }

        // bbPress forum archive / single forum / single topic.
        if (function_exists('bbp_is_forum_archive') && (bbp_is_forum_archive() || is_post_type_archive('forum'))) {
            return 'DaqiToken community forum: ask questions and share real-world tips on travel eSIM, VPN, device compatibility, roaming and AI API tools.';
        }
        if (function_exists('bbp_is_single_forum') && bbp_is_single_forum()) {
            $forum = get_queried_object();
            if ($forum && !empty($forum->post_content)) {
                return self::trimText($forum->post_content, 155);
            }
        }
        if (function_exists('bbp_is_single_topic') && bbp_is_single_topic()) {
            $topic = get_queried_object();
            if ($topic && !empty($topic->post_content)) {
                return self::trimText($topic->post_content, 155);
            }
        }

        // Static utility pages that carry no editable SEO description.
        if (function_exists('is_page') && is_page()) {
            $slug = get_post_field('post_name', get_queried_object_id());
            if ($slug === 'privacy-policy') {
                return self::trimText('How DaqiToken handles your data: what we collect for orders and accounts, how long we keep it, and the privacy choices available to you.', 158);
            }
            if ($slug === 'refund_returns') {
                return self::trimText('DaqiToken refund and returns policy: eligibility, the 14-day window for unused digital eSIM plans, VPN subscriptions and TOKEN credits, and how to request a refund.', 158);
            }
        }

        return $desc;
    }

    /**
     * Replace Yoast's default "%%term_title%% Archives" titles on the
     * categories and forum archives that carry the most search value.
     */
    public static function titleFallback($title)
    {
        $site = get_bloginfo('name');

        if (function_exists('is_product_category') && is_product_category()) {
            $term = get_queried_object();
            if ($term && !empty($term->slug)) {
                if ($term->slug === 'esim') {
                    $title = 'Travel eSIM Plans for 200+ Countries | ' . $site;
                } elseif ($term->slug === 'vpn') {
                    $title = 'VPN Plans: Dedicated WireGuard Nodes | ' . $site;
                } elseif ($term->slug === 'token') {
                    $title = 'TOKEN Credits: Unified LLM Gateway | ' . $site;
                } else {
                    $title = $term->name . ' eSIM Plans: Prices & Coverage | ' . $site;
                }
            }
        } elseif (function_exists('bbp_is_forum_archive') && (bbp_is_forum_archive() || is_post_type_archive('forum'))) {
            $title = 'Community Forum: eSIM, VPN & AI Questions | ' . $site;
        } elseif (is_post_type_archive('topic')) {
            $title = 'Forum Topics & Discussions | ' . $site;
        }

        // Drop the trailing brand suffix on over-long titles so the keyword
        // part fits a SERP snippet. Short titles keep the brand for
        // attribution in answer-engine results.
        if (function_exists('mb_strlen') && mb_strlen($title) > 60) {
            foreach ([' | ', ' – ', ' — ', ' - '] as $sep) {
                $suffix = $sep . $site;
                if (substr($title, -strlen($suffix)) === $suffix) {
                    $title = substr($title, 0, -strlen($suffix));
                    break;
                }
            }
        }

        return $title;
    }

    /** Country product categories grouped into regions for the Destinations hub. */
    private const REGIONS = [
        'Asia' => ['japan', 'thailand', 'singapore', 'hong-kong', 'south-korea', 'india', 'indonesia', 'malaysia', 'philippines', 'vietnam', 'nepal', 'maldives', 'sri-lanka', 'taiwan'],
        'Europe' => ['united-kingdom', 'france', 'germany', 'italy', 'spain', 'netherlands', 'belgium', 'switzerland', 'austria', 'sweden', 'norway', 'denmark', 'finland', 'portugal', 'poland', 'greece', 'ireland', 'czech-republic', 'croatia', 'hungary', 'ukraine', 'turkey'],
        'Americas' => ['united-states', 'canada', 'mexico', 'brazil', 'argentina', 'chile'],
        'Middle East & Africa' => ['egypt', 'kenya', 'morocco', 'south-africa', 'israel', 'qatar', 'saudi-arabia', 'united-arab-emirates'],
        'Oceania' => ['australia', 'new-zealand'],
    ];

    /**
     * [daqitoken_destinations] - region-grouped links to every country eSIM
     * category, used by the /destinations/ hub. Cached for 6 hours.
     */
    public static function destinationsShortcode($atts = [])
    {
        $cached = get_transient('daqitoken_destinations_html');
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => 17]);
        $bySlug = [];
        if (!is_wp_error($terms)) {
            foreach ($terms as $t) {
                $bySlug[$t->slug] = $t;
            }
        }

        $html = '<div class="daqitoken-destinations">';
        foreach (self::REGIONS as $region => $slugs) {
            $rows = '';
            foreach ($slugs as $slug) {
                if (!isset($bySlug[$slug])) {
                    continue;
                }
                $t = $bySlug[$slug];
                $url = get_term_link($t);
                if (is_wp_error($url)) {
                    continue;
                }
                $min = self::minCategoryPrice((int) $t->term_id);
                $price = $min > 0
                    ? ' <span class="dest-price">from $' . rtrim(rtrim(number_format($min, 2, '.', ''), '0'), '.') . '</span>'
                    : '';
                $rows .= '<li><a href="' . esc_url($url) . '">' . esc_html($t->name) . ' eSIM</a>' . $price . '</li>';
            }
            if ($rows === '') {
                continue;
            }
            $html .= '<section class="dest-region"><h3>' . esc_html($region) . '</h3><ul class="dest-list">' . $rows . '</ul></section>';
        }
        $html .= '</div>';

        set_transient('daqitoken_destinations_html', $html, 6 * HOUR_IN_SECONDS);
        return $html;
    }

    /**
     * Add an ItemList of destinations to the schema graph on /destinations/.
     */
    public static function destinationsSchema($graph, $context = null)
    {
        if (!is_array($graph) || !function_exists('is_page') || !is_page('destinations')) {
            return $graph;
        }
        $terms = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => 17]);
        $bySlug = [];
        if (!is_wp_error($terms)) {
            foreach ($terms as $t) {
                $bySlug[$t->slug] = $t;
            }
        }
        $items = [];
        $pos = 1;
        foreach (self::REGIONS as $slugs) {
            foreach ($slugs as $slug) {
                if (!isset($bySlug[$slug])) {
                    continue;
                }
                $url = get_term_link($bySlug[$slug]);
                if (is_wp_error($url)) {
                    continue;
                }
                $items[] = [
                    '@type'    => 'ListItem',
                    'position' => $pos++,
                    'name'     => $bySlug[$slug]->name . ' eSIM',
                    'url'      => $url,
                ];
            }
        }
        if ($items) {
            $graph[] = [
                '@type'           => 'ItemList',
                '@id'             => home_url('/destinations/#destinations'),
                'name'            => 'eSIM Destinations',
                'numberOfItems'   => count($items),
                'itemListElement' => $items,
            ];
        }
        return $graph;
    }

    /**
     * Emit discussion / collection structured data for bbPress so answer
     * engines can parse forum threads and forum hubs. Topics become
     * DiscussionForumPosting nodes (with their replies as comments); forum
     * archives and single forums become CollectionPage nodes with an ItemList.
     */
    public static function forumSchema($graph, $context = null)
    {
        if (!is_array($graph)) {
            return $graph;
        }

        if (function_exists('bbp_is_single_topic') && bbp_is_single_topic()) {
            $topic = get_queried_object();
            if ($topic instanceof WP_Post) {
                $url = get_permalink($topic);
                $author = function_exists('bbp_get_topic_author_display_name')
                    ? bbp_get_topic_author_display_name($topic->ID)
                    : get_the_author_meta('display_name', $topic->post_author);

                $node = [
                    '@type'           => 'DiscussionForumPosting',
                    '@id'             => $url . '#discussion',
                    'url'             => $url,
                    'headline'        => wp_strip_all_tags(get_the_title($topic)),
                    'datePublished'   => get_post_time('c', true, $topic),
                    'dateModified'    => get_post_modified_time('c', true, $topic),
                    'text'            => self::trimText($topic->post_content, 5000),
                    'author'          => ['@type' => 'Person', 'name' => $author],
                    'isPartOf'        => ['@id' => $url . '#webpage'],
                ];

                $replyCount = function_exists('bbp_get_topic_reply_count') ? (int) bbp_get_topic_reply_count($topic->ID) : 0;
                $node['interactionStatistic'] = [[
                    '@type'               => 'InteractionCounter',
                    'interactionType'     => ['@type' => 'CommentAction'],
                    'userInteractionCount' => $replyCount,
                ]];

                $comments = self::topicComments($topic->ID);
                if ($comments) {
                    $node['comment'] = $comments;
                }

                $graph[] = $node;
            }
            return $graph;
        }

        $isForumArchive = function_exists('bbp_is_forum_archive')
            && (bbp_is_forum_archive() || is_post_type_archive('forum'));
        $isSingleForum = function_exists('bbp_is_single_forum') && bbp_is_single_forum();
        if (!$isForumArchive && !$isSingleForum) {
            return $graph;
        }

        $items = [];
        $pos = 1;
        if ($isSingleForum) {
            $forum = get_queried_object();
            $topics = $forum instanceof WP_Post ? get_posts([
                'post_type'   => 'topic',
                'post_parent' => $forum->ID,
                'post_status' => 'publish',
                'numberposts' => 50,
                'orderby'     => 'modified',
                'order'       => 'DESC',
            ]) : [];
            foreach ($topics as $t) {
                $items[] = [
                    '@type'    => 'ListItem',
                    'position' => $pos++,
                    'name'     => wp_strip_all_tags(get_the_title($t)),
                    'url'      => get_permalink($t),
                ];
            }
        } else {
            $forums = get_posts([
                'post_type'   => 'forum',
                'post_status' => 'publish',
                'numberposts' => -1,
                'orderby'     => 'menu_order title',
                'order'       => 'ASC',
            ]);
            foreach ($forums as $f) {
                $items[] = [
                    '@type'    => 'ListItem',
                    'position' => $pos++,
                    'name'     => wp_strip_all_tags(get_the_title($f)),
                    'url'      => get_permalink($f),
                ];
            }
        }

        foreach ($graph as $i => $n) {
            if (empty($n['@type'])) {
                continue;
            }
            $types = (array) $n['@type'];
            if (!in_array('WebPage', $types, true) && !in_array('CollectionPage', $types, true)) {
                continue;
            }
            $graph[$i]['@type'] = array_values(array_unique(array_merge($types, ['CollectionPage'])));
            if ($items) {
                $graph[$i]['mainEntity'] = [
                    '@type'           => 'ItemList',
                    'numberOfItems'   => count($items),
                    'itemListElement' => $items,
                ];
            }
            break;
        }

        return $graph;
    }

    /**
     * Cross-cutting schema enrichment: sitelinks SearchAction on the WebSite
     * node and product offer completeness (brand, item condition, return
     * policy) for Google merchant listings.
     */
    public static function schemaEnhance($graph, $context = null)
    {
        if (!is_array($graph)) {
            return $graph;
        }
        $home = untrailingslashit(home_url());

        foreach ($graph as $i => $n) {
            if (empty($n['@type'])) {
                continue;
            }
            $types = (array) $n['@type'];

            if (in_array('WebSite', $types, true) && empty($n['potentialAction'])) {
                $graph[$i]['potentialAction'] = [
                    '@type'       => 'SearchAction',
                    'target'      => [
                        '@type'       => 'EntryPoint',
                        'urlTemplate' => $home . '/?s={search_term_string}',
                    ],
                    'query-input' => 'required name=search_term_string',
                ];
            }

            if (in_array('Product', $types, true)) {
                if (empty($n['brand'])) {
                    $graph[$i]['brand'] = ['@type' => 'Organization', 'name' => 'DaqiToken'];
                }
                if (!empty($n['offers']) && is_array($n['offers'])) {
                    $offers = $n['offers'];
                    $isList = isset($offers[0]);
                    $offers = $isList ? $offers : [$offers];
                    foreach ($offers as $k => $o) {
                        if (!is_array($o)) {
                            continue;
                        }
                        if (empty($o['itemCondition'])) {
                            $offers[$k]['itemCondition'] = 'https://schema.org/NewCondition';
                        }
                        if (empty($o['hasMerchantReturnPolicy'])) {
                            $offers[$k]['hasMerchantReturnPolicy'] = [
                                '@type'                 => 'MerchantReturnPolicy',
                                'applicableCountry'     => 'US',
                                'returnPolicyCategory'  => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                                'merchantReturnDays'    => 14,
                            ];
                        }
                    }
                    $graph[$i]['offers'] = $isList ? $offers : $offers[0];
                }
            }
        }

        return $graph;
    }

    /**
     * WooCommerce product structured data: brand, item condition and a
     * 14-day return policy so offers qualify for richer merchant listings.
     */
    public static function productStructuredData($markup, $product = null)
    {
        if (!is_array($markup) || empty($markup['@type']) || $markup['@type'] !== 'Product') {
            return $markup;
        }
        if (empty($markup['brand'])) {
            $markup['brand'] = ['@type' => 'Brand', 'name' => 'DaqiToken'];
        }
        if (!empty($markup['offers']) && is_array($markup['offers'])) {
            $isList = isset($markup['offers'][0]);
            $offers = $isList ? $markup['offers'] : [$markup['offers']];
            foreach ($offers as $k => $o) {
                if (!is_array($o)) {
                    continue;
                }
                if (empty($o['itemCondition'])) {
                    $offers[$k]['itemCondition'] = 'https://schema.org/NewCondition';
                }
                if (empty($o['hasMerchantReturnPolicy'])) {
                    $offers[$k]['hasMerchantReturnPolicy'] = [
                        '@type'                => 'MerchantReturnPolicy',
                        'applicableCountry'    => 'US',
                        'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                        'merchantReturnDays'   => 14,
                    ];
                }
            }
            $markup['offers'] = $isList ? $offers : $offers[0];
        }
        return $markup;
    }

    /** bbPress topic replies as schema.org Comment nodes (bounded). */
    private static function topicComments(int $topicId, int $limit = 20): array
    {
        $replies = get_posts([
            'post_type'   => 'reply',
            'post_parent' => $topicId,
            'post_status' => 'publish',
            'numberposts' => $limit,
            'orderby'     => 'date',
            'order'       => 'ASC',
        ]);

        $out = [];
        foreach ($replies as $r) {
            if (!$r instanceof WP_Post) {
                continue;
            }
            $out[] = [
                '@type'         => 'Comment',
                'text'          => self::trimText($r->post_content, 2000),
                'datePublished' => get_post_time('c', true, $r),
                'author'        => [
                    '@type' => 'Person',
                    'name'  => get_the_author_meta('display_name', $r->post_author),
                ],
            ];
        }
        return $out;
    }

    private static function minCategoryPrice(int $termId): float
    {
        if (!function_exists('wc_get_products')) {
            return 0.0;
        }
        $ids = get_posts([
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 20,
            'fields'         => 'ids',
            'tax_query'      => [[
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => [$termId],
            ]],
        ]);
        $min = 0.0;
        foreach ($ids as $id) {
            $p = (float) get_post_meta($id, '_price', true);
            if ($p > 0 && ($min === 0.0 || $p < $min)) {
                $min = $p;
            }
        }
        return $min;
    }

    private static function trimText(string $html, int $max): string
    {
        $t = wp_strip_all_tags(strip_shortcodes($html));
        $t = preg_replace('/\s+/', ' ', (string) $t);
        $t = trim((string) $t);
        if (function_exists('mb_strlen') && mb_strlen($t) > $max) {
            $t = mb_substr($t, 0, $max - 1);
            $t = preg_replace('/\s+\S*$/', '', $t) . '…';
        }
        return $t;
    }
}

DaqiToken_SEO_GEO::boot();
