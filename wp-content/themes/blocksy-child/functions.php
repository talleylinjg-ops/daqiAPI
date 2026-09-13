<?php
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('blocksy-parent', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('blocksy-child', get_stylesheet_uri(), array('blocksy-parent'), '1.3.7');
});

add_action('admin_head', function () {
    echo '<style>#wf-onboarding-banner, #wf-onboarding-disabled-overlay, .wf-onboarding-banner-wrapper { display: none !important; }</style>';
});

add_filter('wp_kses_allowed_html', function ($allowed, $context) {
    if ($context !== 'comment') {
        return $allowed;
    }
    $allowed['img'] = array(
        'src' => true, 'alt' => true, 'class' => true, 'width' => true, 'height' => true,
        'loading' => true, 'style' => true, 'title' => true,
    );
    $allowed['table'] = array('border' => true, 'cellpadding' => true, 'cellspacing' => true, 'class' => true, 'style' => true);
    $allowed['thead'] = array('class' => true);
    $allowed['tbody'] = array('class' => true);
    $allowed['tfoot'] = array('class' => true);
    $allowed['tr'] = array('class' => true);
    $allowed['td'] = array('colspan' => true, 'rowspan' => true, 'class' => true, 'style' => true);
    $allowed['th'] = array('colspan' => true, 'rowspan' => true, 'class' => true, 'style' => true);
    $allowed['ul'] = array('class' => true);
    $allowed['ol'] = array('class' => true);
    $allowed['li'] = array('class' => true);
    $allowed['blockquote'] = array('class' => true);
    $allowed['strong'] = array('class' => true);
    $allowed['em'] = array('class' => true);
    $allowed['br'] = array();
    $allowed['a'] = array('href' => true, 'title' => true, 'rel' => true, 'target' => true);
    $allowed['span'] = array('class' => true, 'style' => true);
    return $allowed;
}, 10, 2);

add_filter('body_class', function ($classes) {
    if (is_singular('post') || is_home() || is_category()) {
        $classes[] = 'nova-dark-page';
    }
    if (function_exists('is_bbpress') && is_bbpress()) {
        $classes[] = 'nova-dark-page';
    }
    return $classes;
});

add_action('pre_get_posts', function ($query) {
    if (!is_admin() && $query->is_main_query() && ($query->is_home() || $query->is_category())) {
        $query->set('posts_per_page', 50);
    }
}, 999);

add_action('after_setup_theme', function () {
    remove_filter('login_redirect', 'bbp_redirect_login', 2);
});

function blocksy_child_reply_cta() {
    $langs = array(
        'en' => 'English', 'es' => 'Español', 'zh' => '中文', 'de' => 'Deutsch',
        'fr' => 'Français', 'pt' => 'Português', 'nl' => 'Nederlands', 'pl' => 'Polski',
        'ru' => 'Русский', 'ar' => 'العربية', 'ja' => '日本語', 'ko' => '한국어',
        'th' => 'ไทย', 'he' => 'עברית', 'vi' => 'Tiếng Việt', 'id' => 'Indonesia', 'kk' => 'Қазақша',
    );
    $reply = array(
        'en' => array('Join the discussion — your experience helps other travelers and developers.', 'Keep replies on-topic and friendly. Be kind, stay helpful.'),
        'es' => array('Únete a la conversación: tu experiencia ayuda a otros viajeros y desarrolladores.', 'Mantén las respuestas con el tema y amables. Sé amable, sé útil.'),
        'zh' => array('加入讨论吧——你的经验能帮助其他旅行者和开发者。', '回复请保持话题相关与友善。'),
        'de' => array('Mach mit in der Diskussion — deine Erfahrung hilft anderen Reisenden und Entwicklern.', 'Bleib beim Thema und freundlich.'),
        'fr' => array('Participez à la discussion — votre expérience aide d\'autres voyageurs et développeurs.', 'Restez dans le sujet et soyez aimable.'),
        'pt' => array('Junte-se à discussão — sua experiência ajuda outros viajantes e desenvolvedores.', 'Mantenha as respostas no assunto e amigáveis.'),
        'nl' => array('Doe mee aan de discussie — jouw ervaring helpt andere reizigers en ontwikkelaars.', 'Blijf bij het onderwerp en wees vriendelijk.'),
        'pl' => array('Dołącz do dyskusji — Twoje doświadczenie pomaga innym podróżnikom i programistom.', 'Trzymaj się tematu i bądź miły.'),
        'ru' => array('Присоединяйтесь к обсуждению — ваш опыт помогает другим путешественникам и разработчикам.', 'Оставайтесь в теме и будьте доброжелательны.'),
        'ar' => array('انضم إلى النقاش — تجربتك تساعد المسافرين والمطورين الآخرين.', 'حافظ على الردود في الموضوع وكن لطيفًا.'),
        'ja' => array('ディスカッションに参加しましょう — あなたの経験が他の旅行者や開発者の助けになります。', 'トピックに沿った親切な返信をお願いします。'),
        'ko' => array('토론에 참여하세요 — 당신의 경험이 다른 여행자와 개발자에게 도움이 됩니다.', '주제에 맞게 친절한 답변을 남겨주세요.'),
        'th' => array('ร่วมพูดคุย — ประสบการณ์ของคุณช่วยเหลือนักเดินทางและนักพัฒนาคนอื่น', 'รักษาการตอบให้ตรงหัวข้อและเป็นมิตร'),
        'he' => array('הצטרפו לדיון — הניסיון שלכם עוזר למטיילים ולמפתחים אחרים.', 'הישארו בנושא והיו אדיבים.'),
        'vi' => array('Tham gia thảo luận — kinh nghiệm của bạn giúp ích cho các du khách và nhà phát triển khác.', 'Giữ câu trả lời đúng chủ đề và thân thiện.'),
        'id' => array('Ikut serta dalam diskusi — pengalaman Anda membantu pelancong dan pengembang lain.', 'Tetap pada topik dan bersikap ramah.'),
        'kk' => array('Пікірталасқа қосылыңыз — сіздің тәжірибеңіз басқа саяхатшылар мен әзірлеушілерге көмектеседі.', 'Тақырыптан ауытқымай, сыпайы болыңыз.'),
    );
    ?>
    <div class="reply-cta">
        <span class="reply-cta-title">💬 <?php echo esc_html__('Join the discussion', 'blocksy-child'); ?></span>
        <div class="cta-langs">
            <?php foreach ($langs as $code => $label): $active = ($code === 'en') ? ' active' : ''; ?>
                <button type="button" class="cta-lang<?php echo esc_attr($active); ?>" data-lang="<?php echo esc_attr($code); ?>"><?php echo esc_html($label); ?></button>
            <?php endforeach; ?>
        </div>
        <div class="cta-all">
            <?php foreach ($langs as $code => $label):
                $active = ($code === 'en') ? ' row-active' : '';
                $dir = ($code === 'ar' || $code === 'he') ? 'rtl' : 'ltr'; ?>
                <div class="cta-row<?php echo esc_attr($active); ?>" data-lang-row="<?php echo esc_attr($code); ?>" dir="<?php echo esc_attr($dir); ?>">
                    <div class="cta-lang-body">
                        <p class="cta-text"><?php echo esc_html($reply[$code][0]); ?></p>
                        <p class="cta-hint"><?php echo esc_html($reply[$code][1]); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

add_action('bbp_theme_before_reply_form', function () {
    if (!function_exists('is_bbpress') || !is_bbpress()) {
        return;
    }
    blocksy_child_reply_cta();
});

add_filter('woocommerce_product_single_add_to_cart_text', function ($text) {
    return __('Add to cart', 'woocommerce');
});

add_filter('woocommerce_product_add_to_cart_text', function ($text, $product) {
    if ($product && $product->is_in_stock()) {
        return __('Add to cart', 'woocommerce');
    }
    return $text;
}, 10, 2);

add_filter('woocommerce_loop_add_to_cart_args', function ($args, $product) {
    if ($product && $product->is_in_stock()) {
        $args['aria-label'] = esc_html(sprintf('Add to cart &ldquo;%s&rdquo;', $product->get_name()));
    }
    return $args;
}, 10, 2);

add_filter('woocommerce_loop_add_to_cart_link', function ($button, $product) {
    if (!$product || !$product->is_in_stock()) {
        return $button;
    }
    $link = $product->get_permalink();
    if (!$link) {
        return $button;
    }
    $buy = '<a href="' . esc_url($link) . '" class="button alt buy-now-link">' . esc_html__('Buy now', 'woocommerce') . '</a>';
    return $button . $buy;
}, 10, 2);

add_action('woocommerce_before_add_to_cart_button', function () {
    echo '<input type="hidden" name="is_buy_now" value="1">';
});

add_action('woocommerce_after_add_to_cart_button', function () {
    global $product;
    if (!$product || !$product->is_in_stock()) {
        return;
    }
    $pid = $product->get_id();
    $url = add_query_arg(array('add-to-cart' => $pid, 'buy_now' => '1'), $product->get_permalink());
    echo '<a href="' . esc_url($url) . '" class="button alt buy-now-link" rel="nofollow">' . esc_html__('Buy now', 'woocommerce') . '</a>';
});

add_filter('woocommerce_add_to_cart_redirect', function ($url, $product) {
    if (isset($_REQUEST['buy_now'])) {
        return wc_get_checkout_url();
    }
    return $url;
}, 10, 2);

add_action('wp_footer', function () {
    if (!(is_singular('post') || is_home() || is_category() || (function_exists('is_bbpress') && is_bbpress()))) {
        return;
    }
    ?>
    <script>
    (function () {
        function ready(fn) {
            if (document.readyState !== 'loading') { fn(); }
            else { document.addEventListener('DOMContentLoaded', fn); }
        }
        ready(function () {
            var root = document.querySelector('.nova-post-wrap, .nova-blog-page, .reply-cta');
            if (!root) { return; }

            // Share buttons
            var btns = document.querySelectorAll('.nova-post-wrap .share-btn');
            for (var i = 0; i < btns.length; i++) {
                btns[i].addEventListener('click', function (e) {
                    var btn = e.currentTarget;
                    var url = btn.getAttribute('data-href');
                    if (url) {
                        window.open(url, '_blank', 'noopener,width=600,height=500');
                        return;
                    }
                    var pageUrl = window.location.href;
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(pageUrl);
                    } else {
                        var ta = document.createElement('textarea');
                        ta.value = pageUrl;
                        document.body.appendChild(ta);
                        ta.select();
                        document.execCommand('copy');
                        document.body.removeChild(ta);
                    }
                    var label = btn.getAttribute('title') || 'your clipboard';
                    var msg = document.createElement('div');
                    msg.className = 'share-msg';
                    msg.textContent = 'Link copied — paste it into ' + label + '.';
                    var wrap = btn.closest('.share-btns');
                    wrap.insertBefore(msg, wrap.firstChild);
                    setTimeout(function () {
                        msg.style.transition = 'opacity 0.4s';
                        msg.style.opacity = '0';
                        setTimeout(function () { msg.remove(); }, 420);
                    }, 2200);
                });
            }

            // Multilingual CTA switcher (works in posts and forum replies)
            var ctaContainers = document.querySelectorAll('.nova-post-wrap, .reply-cta');
            for (var c = 0; c < ctaContainers.length; c++) {
                var container = ctaContainers[c];
                var langBtns = container.querySelectorAll('.cta-lang');
                var applyLang = function (code, scroll) {
                    var all = container.querySelectorAll('.cta-lang');
                    for (var k = 0; k < all.length; k++) { all[k].classList.remove('active'); }
                    var rows = container.querySelectorAll('.cta-row');
                    for (var m = 0; m < rows.length; m++) { rows[m].classList.remove('row-active'); }
                    var btn = container.querySelector('.cta-lang[data-lang="' + code + '"]');
                    if (btn) { btn.classList.add('active'); }
                    var target = container.querySelector('.cta-row[data-lang-row="' + code + '"]');
                    if (target) {
                        target.classList.add('row-active');
                        if (scroll) { target.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
                    }
                };
                for (var j = 0; j < langBtns.length; j++) {
                    langBtns[j].addEventListener('click', function (e) {
                        applyLang(e.currentTarget.getAttribute('data-lang'), true);
                    });
                }
                // Auto-select language from the visitor's browser (fallback: English)
                var autoLang = 'en';
                var navLangs = [];
                if (navigator.languages && navigator.languages.length) {
                    navLangs = navigator.languages.slice();
                } else if (navigator.language) {
                    navLangs = [navigator.language];
                }
                for (var a = 0; a < navLangs.length; a++) {
                    var candidate = (navLangs[a] || '').toLowerCase();
                    candidate = candidate.split('-')[0].split('_')[0];
                    var matchBtn = container.querySelector('.cta-lang[data-lang="' + candidate + '"]');
                    if (matchBtn) { autoLang = candidate; break; }
                }
                applyLang(autoLang, false);
            }
        });
    })();
    </script>
    <?php
});

add_action('blocksy:content:top', function () {
    if (is_admin() || wp_is_json_request()) {
        return;
    }
    if (
        !function_exists('is_product') ||
        !(is_product() || is_shop() || is_product_category() || is_product_tag())
    ) {
        return;
    }
    $allowed_top = array('token', 'esim', 'vpn');
    $top = get_terms(array('taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => 0, 'orderby' => 'name', 'order' => 'ASC'));
    if (is_wp_error($top) || empty($top)) {
        return;
    }
    $top = array_values(array_filter($top, function ($t) use ($allowed_top) {
        return in_array($t->slug, $allowed_top, true);
    }));
    if (empty($top)) {
        return;
    }
    $order_map = array_flip($allowed_top);
    usort($top, function ($a, $b) use ($order_map) {
        return $order_map[$a->slug] <=> $order_map[$b->slug];
    });
    $shop_url = get_permalink(wc_get_page_id('shop'));
    ?>
    <aside class="prod-cat-sidebar" id="prod-cat-sidebar">
        <div class="pcs-head">
            <a href="<?php echo esc_url($shop_url ? $shop_url : home_url('/shop/')); ?>">PRODUCT CATEGORY</a>
        </div>
        <ul class="pcs-list">
            <?php foreach ($top as $t):
                $child = get_terms(array('taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => $t->term_id, 'orderby' => 'name', 'order' => 'ASC'));
                $link = get_term_link($t);
                $active = is_tax('product_cat', $t->slug);
                ?>
                <li class="pcs-item<?php echo $active ? ' current' : ''; ?>">
                    <a class="pcs-parent" href="<?php echo esc_url($link); ?>"><?php echo esc_html($t->name); ?><span class="pcs-count"><?php echo (int) $t->count; ?></span></a>
                    <?php if (!is_wp_error($child) && !empty($child)): ?>
                        <ul class="pcs-sub">
                            <?php foreach ($child as $c):
                                $clink = get_term_link($c);
                                $cactive = is_tax('product_cat', $c->slug);
                                ?>
                                <li class="<?php echo $cactive ? 'current' : ''; ?>"><a href="<?php echo esc_url($clink); ?>"><?php echo esc_html($c->name); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>
    <style>
    .prod-cat-sidebar {
        position: static;
        width: 220px;
        flex: 0 0 220px;
        background: #ffffff;
        border-right: 1px solid #e5e7eb;
        align-self: flex-start;
        overflow: visible;
        z-index: 5;
        padding: 18px 14px;
        font-family: -apple-system, 'Segoe UI', sans-serif;
        display: none;
    }
    .prod-cat-sidebar:after {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 1px;
        background: #e5e7eb;
        display: none;
    }
    .prod-cat-sidebar .pcs-head a {
        display: block;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #0b8a86;
        text-decoration: none;
        margin-bottom: 14px;
    }
    .prod-cat-sidebar .pcs-list { list-style: none; margin: 0; padding: 0; }
    .prod-cat-sidebar .pcs-item { margin-bottom: 2px; }
    .prod-cat-sidebar .pcs-parent {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 10px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        color: #1a2b28;
        text-decoration: none;
        transition: background 0.15s;
    }
    .prod-cat-sidebar .pcs-parent:hover,
    .prod-cat-sidebar .pcs-item.current > .pcs-parent {
        background: #eaf7f4;
        color: #0b8a86;
    }
    .prod-cat-sidebar .pcs-count {
        font-size: 11px;
        font-weight: 600;
        color: #94a3b8;
        background: #f1f5f9;
        border-radius: 20px;
        padding: 1px 8px;
    }
    .prod-cat-sidebar .pcs-sub {
        list-style: none;
        margin: 0 0 4px;
        padding: 0 0 0 10px;
        border-left: 1px solid #e2e8f0;
        margin-left: 16px;
        max-height: 300px;
        overflow-y: auto;
    }
    .prod-cat-sidebar .pcs-sub li a {
        display: block;
        padding: 5px 10px;
        font-size: 13px;
        color: #475569;
        text-decoration: none;
        border-radius: 6px;
    }
    .prod-cat-sidebar .pcs-sub li a:hover,
    .prod-cat-sidebar .pcs-sub li.current a {
        background: #f1f5f9;
        color: #0b8a86;
    }
    @media (min-width: 1300px) {
        .prod-cat-sidebar { display: block; }
        .site-main { display: flex; align-items: flex-start; }
        .site-main .ct-container,
        .site-main .ct-container-full,
        .site-main .ct-container-narrow {
            flex: 1 1 auto;
            min-width: 0;
        }
    }
    </style>
    <?php
});

add_filter('woocommerce_structured_data_product', function ($markup, $product) {
    if (is_array($markup) && isset($markup['@type']) && $markup['@type'] === 'Product' && empty($markup['brand'])) {
        $markup['brand'] = array(
            '@type' => 'Brand',
            'name'  => 'DaqiToken',
            '@id'   => home_url('/#organization'),
        );
    }
    return $markup;
}, 30, 2);

add_filter('wpseo_schema_graph', function ($graph, $context) {
    $site_url = home_url('/');
    $contact_url = home_url('/contact/');
    $social = array(
        $site_url . 'blog/',
        $site_url . 'forum/',
    );
    foreach ($graph as &$node) {
        if (empty($node['@type']) || $node['@type'] !== 'Organization') {
            continue;
        }
        if (empty($node['sameAs'])) {
            $node['sameAs'] = $social;
        }
        if (empty($node['contactPoint'])) {
            $node['contactPoint'] = array(
                '@type'          => 'ContactPoint',
                'contactType'    => 'customer support',
                'email'          => 'support@daqitoken.com',
                'url'            => $contact_url,
                'availableLanguage' => 'English',
            );
        }
    }
    unset($node);
    return $graph;
}, 30, 2);

add_action('wp_enqueue_scripts', function () {
    if (is_admin()) {
        return;
    }
    $defer_handles = array('jquery-core', 'jquery-migrate');
    foreach ($defer_handles as $handle) {
        wp_script_add_data($handle, 'strategy', 'defer');
    }
}, 100);

add_action('wp_enqueue_scripts', function () {
    wp_dequeue_script('wp-embed');
}, 100);

add_action('wp_enqueue_scripts', function () {
    if (function_exists('is_bbpress') && is_bbpress()) {
        return;
    }
    wp_dequeue_style('bbp-default');
    wp_dequeue_style('bbp-default-css');
    wp_dequeue_style('ct-bbpress-styles');
}, 110);

add_filter('style_loader_tag', function ($tag, $handle) {
    if (is_admin()) {
        return $tag;
    }
    if (function_exists('is_bbpress') && is_bbpress()) {
        return $tag;
    }
    if ($handle === 'bbp-default' || $handle === 'bbp-default-css' || $handle === 'ct-bbpress-styles') {
        return '';
    }
    return $tag;
}, 10, 2);
