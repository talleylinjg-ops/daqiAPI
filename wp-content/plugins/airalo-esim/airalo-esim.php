<?php
/**
 * Plugin Name: Airalo eSIM Gateway
 * Description: Links WooCommerce products to Airalo Partner API. Purchases Airalo eSIMs on paid orders and delivers QR codes + installation guides to customers.
 * Version: 1.0.0
 * Author: Kvisa
 */

if (!defined('ABSPATH')) exit;

define('AIRALO_OPTION', 'airalo_plugin_settings');

function airalo_plugin_settings() {
    $defaults = array(
        'client_id'     => '',
        'client_secret' => '',
        'sandbox'       => '1',
    );
    $saved = get_option(AIRALO_OPTION, array());
    return array_merge($defaults, is_array($saved) ? $saved : array());
}

function airalo_plugin_base_url() {
    $s = airalo_plugin_settings();
    return empty($s['sandbox']) ? 'https://partners-api.airalo.com' : 'https://sandbox-partners-api.airalo.com';
}

function airalo_plugin_get_token() {
    $cached = get_transient('airalo_access_token');
    if ($cached) return $cached;

    $s = airalo_plugin_settings();
    if (empty($s['client_id']) || empty($s['client_secret'])) {
        return null;
    }

    $resp = wp_remote_post(airalo_plugin_base_url() . '/v2/token', array(
        'timeout' => 20,
        'headers' => array('Accept' => 'application/json'),
        'body'    => array(
            'grant_type'    => 'client_credentials',
            'client_id'     => $s['client_id'],
            'client_secret' => $s['client_secret'],
        ),
    ));
    if (is_wp_error($resp)) return null;
    $code = wp_remote_retrieve_response_code($resp);
    $json = json_decode(wp_remote_retrieve_body($resp), true);
    if ($code !== 200 || empty($json['data']['access_token'])) return null;

    $expires = 86400 - 300;
    set_transient('airalo_access_token', $json['data']['access_token'], $expires);
    return $json['data']['access_token'];
}

function airalo_plugin_api($method, $path, $params = array(), $isForm = false) {
    $token = airalo_plugin_get_token();
    if (!$token) return array('status' => 401, 'json' => array('meta' => array('message' => 'no token')));

    $url = airalo_plugin_base_url() . '/' . ltrim($path, '/');
    $args = array(
        'method'  => $method,
        'timeout' => 30,
        'headers' => array(
            'Accept'        => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ),
    );
    if ($method === 'GET' && $params) {
        $url .= '?' . http_build_query($params);
    } elseif ($method === 'POST' && $params) {
        $args['headers']['Content-Type'] = $isForm ? 'application/x-www-form-urlencoded' : 'application/json';
        $args['body'] = $isForm ? http_build_query($params) : wp_json_encode($params);
    }

    $resp = wp_remote_request($url, $args);
    if (is_wp_error($resp)) return array('status' => 0, 'json' => array('meta' => array('message' => $resp->get_error_message())));
    $code = wp_remote_retrieve_response_code($resp);
    $json = json_decode(wp_remote_retrieve_body($resp), true);
    return array('status' => $code, 'json' => $json);
}

function airalo_plugin_fetch_packages($country = '') {
    $params = array('limit' => 100);
    if ($country) $params['filter[country]'] = strtoupper($country);
    $res = airalo_plugin_api('GET', '/v2/packages', $params);
    if (empty($res['json']['data'])) return array();
    return $res['json']['data'];
}

function airalo_plugin_purchase($packageId, $quantity = 1, $description = '') {
    $params = array(
        'package_id' => $packageId,
        'quantity'   => (string) $quantity,
        'type'       => 'sim',
    );
    if ($description) $params['description'] = $description;
    return airalo_plugin_api('POST', '/v2/orders', $params, true);
}

function airalo_plugin_product_package_id($productId) {
    $meta = get_post_meta($productId, '_airalo_package_id', true);
    return $meta ? $meta : '';
}

function airalo_plugin_order_deliver($orderId) {
    $order = wc_get_order($orderId);
    if (!$order) return;

    $delivered = get_post_meta($orderId, '_airalo_delivered', true);
    if ($delivered) return;

    $items = $order->get_items();
    $results = array();
    $ok = true;

    foreach ($items as $item) {
        $pid = $item->get_product_id();
        $pkg = airalo_plugin_product_package_id($pid);
        if (!$pkg) continue;

        $qty = max(1, (int) $item->get_quantity());
        $desc = 'WP order ' . $orderId . ' / product ' . $pid;
        $res = airalo_plugin_purchase($pkg, $qty, $desc);

        if (empty($res['json']['data']) || empty($res['json']['data']['sims'])) {
            $ok = false;
            $results[] = array('product' => $pid, 'package' => $pkg, 'error' => isset($res['json']['meta']['message']) ? $res['json']['meta']['message'] : 'failed');
            continue;
        }

        $data = $res['json']['data'];
        $sims = array();
        foreach ($data['sims'] as $sim) {
            $sims[] = array(
                'iccid'              => isset($sim['iccid']) ? $sim['iccid'] : '',
                'lpa'                => isset($sim['lpa']) ? $sim['lpa'] : '',
                'qrcode'             => isset($sim['qrcode']) ? $sim['qrcode'] : '',
                'qrcode_url'         => isset($sim['qrcode_url']) ? $sim['qrcode_url'] : '',
                'direct_apple_install' => isset($sim['direct_apple_installation_url']) ? $sim['direct_apple_installation_url'] : '',
            );
        }
        $results[] = array(
            'product'    => $pid,
            'package'    => $pkg,
            'package_name' => isset($data['package']) ? $data['package'] : '',
            'data'       => isset($data['data']) ? $data['data'] : '',
            'validity'   => isset($data['validity']) ? $data['validity'] : '',
            'price'      => isset($data['price']) ? $data['price'] : '',
            'code'       => isset($data['code']) ? $data['code'] : '',
            'sims'       => $sims,
        );
    }

    if (!$results) return;

    update_post_meta($orderId, '_airalo_delivery', $results);
    update_post_meta($orderId, '_airalo_delivered', $ok ? 'yes' : 'partial');
    if ($ok) {
        $order->add_order_note(__('eSIM delivered via Airalo Partner API.', 'airalo-esim'));
        airalo_plugin_send_email($order, $results);
    } else {
        $order->add_order_note(__('eSIM delivery had errors. Check order meta.', 'airalo-esim'));
    }
}

function airalo_plugin_send_email($order, $results) {
    $recipient = $order->get_billing_email();
    if (!$recipient) return;

    $rows = '';
    foreach ($results as $r) {
        $rows .= '<h3>' . esc_html($r['package_name']) . ' (' . esc_html($r['data']) . ' / ' . esc_html($r['validity']) . ' days)</h3>';
        foreach ($r['sims'] as $sim) {
            if (!empty($sim['qrcode_url'])) {
                $rows .= '<p><img src="' . esc_url($sim['qrcode_url']) . '" alt="eSIM QR code" style="max-width:260px;height:auto;"/></p>';
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

function airalo_plugin_order_completed($orderId) {
    airalo_plugin_order_deliver($orderId);
}
add_action('woocommerce_order_status_completed', 'airalo_plugin_order_completed');
add_action('woocommerce_order_status_processing', 'airalo_plugin_order_completed');

function airalo_plugin_order_display($order) {
    $delivery = get_post_meta($order->get_id(), '_airalo_delivery', true);
    if (!$delivery) return;

    echo '<h2>eSIM Delivery</h2>';
    foreach ($delivery as $r) {
        echo '<h3>' . esc_html($r['package_name']) . ' (' . esc_html($r['data']) . ' / ' . esc_html($r['validity']) . ' days)</h3>';
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
add_action('woocommerce_view_order', 'airalo_plugin_order_display');
add_action('woocommerce_order_details_after_order_table', 'airalo_plugin_order_display');

function airalo_plugin_admin_menu() {
    add_menu_page('Airalo eSIM', 'Airalo eSIM', 'manage_options', 'airalo-esim', 'airalo_plugin_settings_page', 'dashicons-smartphone', 56);
    add_submenu_page('airalo-esim', 'Packages', 'Browse Packages', 'manage_options', 'airalo-esim-packages', 'airalo_plugin_packages_page');
}
add_action('admin_menu', 'airalo_plugin_admin_menu');

function airalo_plugin_settings_page() {
    if (isset($_POST['airalo_save'])) {
        update_option(AIRALO_OPTION, array(
            'client_id'     => sanitize_text_field($_POST['client_id']),
            'client_secret' => sanitize_text_field($_POST['client_secret']),
            'sandbox'       => isset($_POST['sandbox']) ? '1' : '',
        ));
        delete_transient('airalo_access_token');
        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
    }
    $s = airalo_plugin_settings();
    $token = airalo_plugin_get_token();
    ?>
    <div class="wrap">
        <h1>Airalo eSIM Settings</h1>
        <form method="post">
            <table class="form-table">
                <tr><th>Client ID</th><td><input type="text" name="client_id" value="<?php echo esc_attr($s['client_id']); ?>" style="width:400px;"/></td></tr>
                <tr><th>Client Secret</th><td><input type="password" name="client_secret" value="<?php echo esc_attr($s['client_secret']); ?>" style="width:400px;"/></td></tr>
                <tr><th>Sandbox Mode</th><td><input type="checkbox" name="sandbox" <?php checked($s['sandbox'], '1'); ?>/> Use sandbox API</td></tr>
            </table>
            <p><button type="submit" name="airalo_save" class="button button-primary">Save</button></p>
        </form>
        <h2>API Status: <?php echo $token ? '<span style="color:green">Connected</span>' : '<span style="color:red">Not connected</span>'; ?></h2>
        <p>Link a WooCommerce product to an Airalo package by editing the product and setting the "Airalo Package ID" field (custom field <code>_airalo_package_id</code>).</p>
    </div>
    <?php
}

function airalo_plugin_packages_page() {
    $country = isset($_GET['country']) ? sanitize_text_field($_GET['country']) : '';
    echo '<div class="wrap"><h1>Browse Airalo Packages</h1>';
    echo '<form method="get"><input type="hidden" name="page" value="airalo-esim-packages"/>Country (ISO code, e.g. US): <input type="text" name="country" value="' . esc_attr($country) . '"/> <button class="button">Search</button></form>';

    $packages = airalo_plugin_fetch_packages($country);
    if (!$packages) {
        echo '<p>No packages returned. Check API connection or country code.</p></div>';
        return;
    }
    echo '<table class="widefat striped"><thead><tr><th>Package ID</th><th>Title</th><th>Data</th><th>Validity</th><th>Net Price (USD)</th><th>Retail Price (USD)</th><th>Margin</th></tr></thead><tbody>';
    foreach ($packages as $pkg) {
        $title = isset($pkg['title']) ? $pkg['title'] : '';
        $countryCode = isset($pkg['country_code']) ? $pkg['country_code'] : '';
        foreach ($pkg['operators'] as $op) {
            foreach ($op['packages'] as $pk) {
                $net = isset($pk['net_price']) ? $pk['net_price'] : '';
                $retail = isset($pk['prices']['recommended_retail_price']['USD']) ? $pk['prices']['recommended_retail_price']['USD'] : '';
                $margin = ($net !== '' && $retail !== '') ? round($retail - $net, 2) : '';
                echo '<tr><td><code>' . esc_html($pk['id']) . '</code></td><td>' . esc_html($title) . ' / ' . esc_html($op['title']) . ' - ' . esc_html($pk['title']) . '</td><td>' . esc_html($pk['data']) . '</td><td>' . esc_html($pk['day']) . 'd</td><td>$' . esc_html($net) . '</td><td>$' . esc_html($retail) . '</td><td><strong>$' . esc_html($margin) . '</strong></td></tr>';
            }
        }
    }
    echo '</tbody></table></div>';
}

function airalo_plugin_product_fields() {
    global $post;
    $pkg = airalo_plugin_product_package_id($post->ID);
    echo '<div class="options_group">';
    woocommerce_wp_text_input(array(
        'id'    => '_airalo_package_id',
        'label' => 'Airalo Package ID',
        'value' => $pkg,
        'description' => 'Paste the Airalo package ID (e.g. kallur-digital-7days-1gb). Found in Airalo eSIM > Browse Packages.',
    ));
    echo '</div>';
}
add_action('woocommerce_product_options_general_product_data', 'airalo_plugin_product_fields');

function airalo_plugin_product_save($productId) {
    if (isset($_POST['_airalo_package_id'])) {
        update_post_meta($productId, '_airalo_package_id', sanitize_text_field($_POST['_airalo_package_id']));
    }
}
add_action('woocommerce_process_product_meta', 'airalo_plugin_product_save');
