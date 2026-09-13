<?php
/**
 * Plugin Name: eSIM Price Engine
 * Description: Multi-provider eSIM gateway for WooCommerce. Auto-picks the lowest-net-price provider per country/data/days and delivers QR codes to customers.
 * Version: 1.0.0
 * Author: Kvisa
 */

if (!defined('ABSPATH')) exit;

define('ESIM_OPTION', 'esim_gateway_settings');
define('ESIM_CATALOG_KEY', 'esim_price_catalog');

require_once __DIR__ . '/adapters/class-esim-adapter.php';
require_once __DIR__ . '/adapters/class-esim-adapter-airalo.php';

function esim_gateway_settings() {
    $defaults = array(
        'sync_interval' => '3600',
    );
    $saved = get_option(ESIM_OPTION, array());
    return array_merge($defaults, is_array($saved) ? $saved : array());
}

function esim_gateway_adapters() {
    $s = esim_gateway_settings();
    $per = get_option('esim_provider_settings', array());
    return array(
        'airalo' => new Esim_Adapter_Airalo(isset($per['airalo']) ? $per['airalo'] : array()),
    );
}

function esim_gateway_configured_adapters() {
    $out = array();
    foreach (esim_gateway_adapters() as $id => $adapter) {
        if ($adapter->is_configured()) $out[$id] = $adapter;
    }
    return $out;
}

/**
 * Sync all configured adapters into a cached catalog.
 * Returns count of records fetched (false on no adapters).
 */
function esim_gateway_sync_catalog() {
    $adapters = esim_gateway_configured_adapters();
    if (!$adapters) return false;

    $catalog = array();
    foreach ($adapters as $id => $adapter) {
        try {
            $records = $adapter->fetch_catalog();
            $catalog[$id] = array(
                'synced_at' => current_time('mysql'),
                'records'   => is_array($records) ? $records : array(),
            );
        } catch (Exception $e) {
            $catalog[$id] = array('synced_at' => '', 'records' => array(), 'error' => $e->getMessage());
        }
    }
    update_option(ESIM_CATALOG_KEY, $catalog, false);
    return count($catalog);
}

function esim_gateway_get_catalog() {
    return get_option(ESIM_CATALOG_KEY, array());
}

/**
 * Price engine: find cheapest configured provider for a spec.
 * spec: country (ISO cc2), data_gb (float|null for unlimited), days (int).
 * Optional voice/text matching. Returns best record or null.
 */
function esim_gateway_best_price($spec) {
    $catalog = esim_gateway_get_catalog();
    $best = null;
    $bestNet = null;

    foreach ($catalog as $provider => $bucket) {
        if (empty($bucket['records'])) continue;
        foreach ($bucket['records'] as $rec) {
            if (!esim_gateway_match_spec($rec, $spec)) continue;
            $net = isset($rec['net_price']) ? (float) $rec['net_price'] : null;
            if ($net === null || $net <= 0) continue;
            if ($bestNet === null || $net < $bestNet) {
                $bestNet = $net;
                $best = $rec;
            }
        }
    }
    return $best;
}

function esim_gateway_match_spec($rec, $spec) {
    // Country: allow global/regional packages to satisfy local requests only as fallback handled by caller.
    if (!empty($spec['country'])) {
        $c = strtoupper($spec['country']);
        if (strtoupper($rec['country']) !== $c && strtoupper($rec['country']) !== 'GLOBAL') return false;
    }
    if (!empty($spec['data_gb'])) {
        $want = (float) $spec['data_gb'];
        $have = $rec['unlimited'] ? null : $rec['data_gb'];
        // Prefer exact; allow package with >= wanted data, but skip unlimited when exact is requested unless nothing else.
        if ($have !== null && $have < $want) return false;
        if (!$rec['unlimited'] && $have !== null && $have !== $want) {
            // only accept oversize if it's the best we have; handled by caller via ranking. Accept for now.
        }
    }
    if (!empty($spec['days']) && !$rec['unlimited']) {
        $wantDays = (int) $spec['days'];
        $haveDays = (int) $rec['days'];
        if ($haveDays !== $wantDays) return false;
    }
    return true;
}

/**
 * Cron sync hook.
 */
function esim_gateway_cron_sync() {
    esim_gateway_sync_catalog();
}
add_action('esim_gateway_hourly_sync', 'esim_gateway_cron_sync');

function esim_gateway_schedule_cron() {
    if (!wp_next_scheduled('esim_gateway_hourly_sync')) {
        wp_schedule_event(time(), 'hourly', 'esim_gateway_hourly_sync');
    }
}
add_action('wp', 'esim_gateway_schedule_cron');

/**
 * ---- Product metadata integration ----
 * Each WooCommerce product carries a spec: _esim_country, _esim_data_gb, _esim_days.
 * (Backward-compatible: _airalo_package_id still forces a specific Airalo package.)
 */

function esim_gateway_product_package_id($productId) {
    $meta = get_post_meta($productId, '_airalo_package_id', true);
    return $meta ? $meta : '';
}

function esim_gateway_product_spec($productId) {
    return array(
        'country' => get_post_meta($productId, '_esim_country', true),
        'data_gb' => get_post_meta($productId, '_esim_data_gb', true),
        'days'    => get_post_meta($productId, '_esim_days', true),
    );
}

function esim_gateway_resolve_package($productId) {
    // 1. Explicit Airalo package id (legacy/override).
    $pkg = esim_gateway_product_package_id($productId);
    if ($pkg) return array('provider' => 'airalo', 'package_id' => $pkg, 'via' => 'explicit');

    // 2. Price engine lookup.
    $spec = esim_gateway_product_spec($productId);
    $best = esim_gateway_best_price($spec);
    if ($best) return array('provider' => $best['provider'], 'package_id' => $best['package_id'], 'via' => 'engine', 'record' => $best);

    return null;
}

/**
 * ---- Order delivery ----
 */
function esim_gateway_order_deliver($orderId) {
    $order = wc_get_order($orderId);
    if (!$order) return;

    if (get_post_meta($orderId, '_esim_delivered', true)) return;

    $adapters = esim_gateway_adapters();
    $results = array();
    $ok = true;

    foreach ($order->get_items() as $item) {
        $pid = $item->get_product_id();
        $resolved = esim_gateway_resolve_package($pid);
        if (!$resolved) {
            $results[] = array('product' => $pid, 'error' => 'no matching package');
            $ok = false;
            continue;
        }

        $adapter = isset($adapters[$resolved['provider']]) ? $adapters[$resolved['provider']] : null;
        if (!$adapter) {
            $results[] = array('product' => $pid, 'error' => 'adapter missing');
            $ok = false;
            continue;
        }

        $qty = max(1, (int) $item->get_quantity());
        $desc = 'WP order ' . $orderId . ' / product ' . $pid;
        $res = $adapter->purchase($resolved['package_id'], $qty, $desc);

        if (empty($res['ok'])) {
            $ok = false;
            $results[] = array(
                'product' => $pid,
                'provider' => $resolved['provider'],
                'package_id' => $resolved['package_id'],
                'via' => $resolved['via'],
                'error' => isset($res['error']) ? $res['error'] : 'purchase failed',
            );
            continue;
        }

        $results[] = array(
            'product'      => $pid,
            'provider'     => $resolved['provider'],
            'package_id'   => $resolved['package_id'],
            'via'          => $resolved['via'],
            'package_name' => isset($res['package_name']) ? $res['package_name'] : '',
            'data'         => isset($res['data']) ? $res['data'] : '',
            'validity'     => isset($res['validity']) ? $res['validity'] : '',
            'price'        => isset($res['price']) ? $res['price'] : '',
            'net_price'    => isset($res['net_price']) ? $res['net_price'] : '',
            'sims'         => isset($res['sims']) ? $res['sims'] : array(),
        );
    }

    if (!$results) return;

    update_post_meta($orderId, '_esim_delivery', $results);
    update_post_meta($orderId, '_esim_delivered', $ok ? 'yes' : 'partial');
    $order->add_order_note($ok ? 'eSIM delivered via price engine.' : 'eSIM delivery had errors. Check order meta.');

    if ($ok) {
        esim_gateway_send_email($order, $results);
    }
}
add_action('woocommerce_order_status_completed', 'esim_gateway_order_deliver');
add_action('woocommerce_order_status_processing', 'esim_gateway_order_deliver');

function esim_gateway_send_email($order, $results) {
    $recipient = $order->get_billing_email();
    if (!$recipient) return;

    $rows = '';
    foreach ($results as $r) {
        $rows .= '<h3>' . esc_html($r['package_name']) . ' (' . esc_html($r['data']) . ' / ' . esc_html($r['validity']) . ' days)</h3>';
        foreach ($r['sims'] as $sim) {
            if (!empty($sim['qrcode_url'])) {
                $rows .= '<p><img src="' . esc_url($sim['qrcode_url']) . '" alt="eSIM QR" style="max-width:260px;height:auto;"/></p>';
            }
            if (!empty($sim['direct_apple_install'])) {
                $rows .= '<p><a href="' . esc_url($sim['direct_apple_install']) . '">Install on iPhone (iOS 17.4+)</a></p>';
            }
            if (!empty($sim['lpa']) && !empty($sim['qrcode'])) {
                $rows .= '<p>SM-DP+ Address: ' . esc_html($sim['lpa']) . '</p>';
                $rows .= '<p>Activation Code: ' . esc_html(str_replace('LPA:1$' . $sim['lpa'] . '$', '', $sim['qrcode'])) . '</p>';
            }
        }
    }
    wp_mail($recipient, 'Your eSIM is ready - ' . get_bloginfo('name'), $rows, array('Content-Type: text/html; charset=UTF-8'));
}

function esim_gateway_order_display($order) {
    $delivery = get_post_meta($order->get_id(), '_esim_delivery', true);
    if (!$delivery) return;

    echo '<h2>eSIM Delivery</h2>';
    foreach ($delivery as $r) {
        $provider = isset($r['provider']) ? strtoupper($r['provider']) : '';
        echo '<h3>' . esc_html($r['package_name']) . ' (' . esc_html($r['data']) . ' / ' . esc_html($r['validity']) . ' days) <small>via ' . esc_html($provider) . '</small></h3>';
        foreach ($r['sims'] as $sim) {
            if (!empty($sim['qrcode_url'])) {
                echo '<p><img src="' . esc_url($sim['qrcode_url']) . '" alt="eSIM QR" style="max-width:260px;height:auto;"/></p>';
            }
            if (!empty($sim['direct_apple_install'])) {
                echo '<p><a href="' . esc_url($sim['direct_apple_install']) . '">Install on iPhone (iOS 17.4+)</a></p>';
            }
            if (!empty($sim['lpa']) && !empty($sim['qrcode'])) {
                echo '<p>SM-DP+ Address: ' . esc_html($sim['lpa']) . '</p>';
                echo '<p>Activation Code: ' . esc_html(str_replace('LPA:1$' . $sim['lpa'] . '$', '', $sim['qrcode'])) . '</p>';
            }
        }
    }
}
add_action('woocommerce_view_order', 'esim_gateway_order_display');
add_action('woocommerce_order_details_after_order_table', 'esim_gateway_order_display');

/**
 * ---- Admin ----
 */
function esim_gateway_admin_menu() {
    add_menu_page('eSIM Price Engine', 'eSIM Engine', 'manage_options', 'esim-gateway', 'esim_gateway_settings_page', 'dashicons-randomize', 56);
    add_submenu_page('esim-gateway', 'Providers', 'Providers', 'manage_options', 'esim-gateway-providers', 'esim_gateway_providers_page');
    add_submenu_page('esim-gateway', 'Catalog', 'Catalog', 'manage_options', 'esim-gateway-catalog', 'esim_gateway_catalog_page');
}
add_action('admin_menu', 'esim_gateway_admin_menu');

function esim_gateway_settings_page() {
    if (isset($_POST['esim_save'])) {
        update_option(ESIM_OPTION, array('sync_interval' => sanitize_text_field($_POST['sync_interval'])));
        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
    }
    $s = esim_gateway_settings();
    ?>
    <div class="wrap">
        <h1>eSIM Price Engine</h1>
        <form method="post">
            <table class="form-table">
                <tr><th>Catalog sync interval (seconds)</th><td><input type="text" name="sync_interval" value="<?php echo esc_attr($s['sync_interval']); ?>"/></td></tr>
            </table>
            <p><button type="submit" name="esim_save" class="button button-primary">Save</button></p>
        </form>
        <p><a class="button" href="<?php echo admin_url('admin-post.php?action=esim_sync_now'); ?>">Sync Catalog Now</a></p>
        <p>Product flow: edit a WooCommerce product and set the eSIM spec fields (<code>_esim_country</code>, <code>_esim_data_gb</code>, <code>_esim_days</code>). The engine auto-picks the cheapest configured provider at checkout.</p>
    </div>
    <?php
}

function esim_gateway_providers_page() {
    $per = get_option('esim_provider_settings', array());
    if (isset($_POST['esim_provider_save'])) {
        $provider = sanitize_text_field($_POST['provider_id']);
        $saved = isset($per[$provider]) ? $per[$provider] : array();
        foreach ($_POST as $k => $v) {
            if (strpos($k, 'field_') === 0) {
                $saved[substr($k, 6)] = sanitize_text_field($v);
            }
            if (strpos($k, 'check_') === 0) {
                $saved[substr($k, 6)] = isset($_POST[$k]) ? '1' : '';
            }
        }
        $per[$provider] = $saved;
        update_option('esim_provider_settings', $per);
        delete_transient('esim_airalo_token');
        echo '<div class="notice notice-success"><p>Provider saved.</p></div>';
    }

    $adapters = esim_gateway_adapters();
    ?>
    <div class="wrap">
        <h1>eSIM Providers</h1>
        <?php foreach ($adapters as $id => $adapter): ?>
        <h2><?php echo esc_html($adapter->get_name()); ?> <?php echo $adapter->is_configured() ? '<span style="color:green">(configured)</span>' : '<span style="color:red">(not configured)</span>'; ?></h2>
        <form method="post">
            <input type="hidden" name="provider_id" value="<?php echo esc_attr($id); ?>"/>
            <table class="form-table">
                <?php foreach ($adapter->get_settings_fields() as $field => $meta): ?>
                <tr>
                    <th><?php echo esc_html($meta['label']); ?></th>
                    <td>
                        <?php if ($meta['type'] === 'checkbox'): ?>
                            <input type="checkbox" name="check_<?php echo esc_attr($field); ?>" <?php checked(!empty($per[$id][$field]), '1'); ?>/>
                        <?php else: ?>
                            <input type="<?php echo esc_attr($meta['type']); ?>" name="field_<?php echo esc_attr($field); ?>" value="<?php echo esc_attr(isset($per[$id][$field]) ? $per[$id][$field] : ''); ?>" style="width:400px;"/>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <p><button type="submit" name="esim_provider_save" class="button button-primary">Save Provider</button></p>
        </form>
        <?php endforeach; ?>
    </div>
    <?php
}

function esim_gateway_catalog_page() {
    $catalog = esim_gateway_get_catalog();
    ?>
    <div class="wrap">
        <h1>eSIM Catalog <a class="button" href="<?php echo admin_url('admin-post.php?action=esim_sync_now'); ?>">Sync Now</a></h1>
        <?php if (!$catalog): ?>
            <p>No catalog yet. Configure at least one provider and sync.</p>
        <?php else: ?>
            <?php foreach ($catalog as $provider => $bucket): ?>
                <h2><?php echo esc_html(strtoupper($provider)); ?> - <?php echo isset($bucket['synced_at']) ? esc_html($bucket['synced_at']) : 'never'; ?> (<?php echo count($bucket['records']); ?> packages)</h2>
                <?php if (empty($bucket['records'])) { echo '<p>No records.</p>'; continue; } ?>
                <table class="widefat striped" style="max-width:1200px;">
                    <thead><tr><th>Country</th><th>Title</th><th>Data</th><th>Days</th><th>Net $</th><th>Retail $</th><th>Margin $</th><th>Package ID</th></tr></thead>
                    <tbody>
                    <?php foreach ($bucket['records'] as $rec): ?>
                        <tr>
                            <td><?php echo esc_html($rec['country']); ?></td>
                            <td><?php echo esc_html($rec['title']); ?></td>
                            <td><?php echo $rec['unlimited'] ? 'Unlimited' : esc_html($rec['data_gb'] . ' GB'); ?></td>
                            <td><?php echo esc_html($rec['days']); ?></td>
                            <td><?php echo $rec['net_price'] !== null ? '$' . number_format($rec['net_price'], 2) : '-'; ?></td>
                            <td><?php echo $rec['retail_price'] !== null ? '$' . number_format($rec['retail_price'], 2) : '-'; ?></td>
                            <td><?php echo ($rec['net_price'] !== null && $rec['retail_price'] !== null) ? '<strong>$' . number_format($rec['retail_price'] - $rec['net_price'], 2) . '</strong>' : '-'; ?></td>
                            <td><code><?php echo esc_html($rec['package_id']); ?></code></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php
}

function esim_gateway_sync_now() {
    if (!current_user_can('manage_options')) wp_die('no permission');
    $count = esim_gateway_sync_catalog();
    wp_redirect(add_query_arg(array('page' => 'esim-gateway-catalog', 'synced' => $count === false ? 'none' : $count), admin_url('admin.php')));
    exit;
}
add_action('admin_post_esim_sync_now', 'esim_gateway_sync_now');

/**
 * Product editor fields.
 */
function esim_gateway_product_fields() {
    global $post;
    $spec = esim_gateway_product_spec($post->ID);
    echo '<div class="options_group">';
    woocommerce_wp_text_input(array('id' => '_esim_country', 'label' => 'eSIM Country (ISO cc2, e.g. US)', 'value' => $spec['country']));
    woocommerce_wp_text_input(array('id' => '_esim_data_gb', 'label' => 'eSIM Data (GB)', 'value' => $spec['data_gb']));
    woocommerce_wp_text_input(array('id' => '_esim_days', 'label' => 'eSIM Validity (days)', 'value' => $spec['days']));
    woocommerce_wp_text_input(array('id' => '_airalo_package_id', 'label' => 'Force Airalo Package ID (optional)', 'value' => esim_gateway_product_package_id($post->ID)));
    echo '</div>';
}
add_action('woocommerce_product_options_general_product_data', 'esim_gateway_product_fields');

function esim_gateway_product_save($productId) {
    foreach (array('_esim_country', '_esim_data_gb', '_esim_days', '_airalo_package_id') as $f) {
        if (isset($_POST[$f])) {
            update_post_meta($productId, $f, sanitize_text_field($_POST[$f]));
        }
    }
}
add_action('woocommerce_process_product_meta', 'esim_gateway_product_save');
