<?php
/**
 * Plugin Name: Token Gateway
 * Description: Links WooCommerce TOKEN top-up products to One API. Auto-creates gateway accounts, credits quota on paid orders, and shows an API console shortcode.
 * Version: 1.0.0
 * Author: Kvisa
 */

if (!defined('ABSPATH')) exit;

define('TOKEN_PLUGIN_OPTION', 'token_plugin_settings');

function token_plugin_settings() {
    $defaults = array(
        'oneapi_base' => 'http://127.0.0.1:3000',
        'admin_token' => '',
    );
    $saved = get_option(TOKEN_PLUGIN_OPTION, array());
    return array_merge($defaults, is_array($saved) ? $saved : array());
}

function token_plugin_product_tiers() {
    return array(
        10  => 5000000,
        50  => 25000000,
        100 => 50000000,
    );
}

function token_plugin_http($method, $apiPath, $body = null, $authToken = null) {
    $settings = token_plugin_settings();
    $url = rtrim($settings['oneapi_base'], '/') . '/' . ltrim($apiPath, '/');
    if ($authToken === null) {
        $authToken = $settings['admin_token'];
    }
    $headers = array(
        'Authorization' => 'Bearer ' . $authToken,
        'Content-Type' => 'application/json',
    );
    $args = array(
        'method' => $method,
        'headers' => $headers,
        'timeout' => 20,
    );
    if ($body !== null) {
        $args['body'] = wp_json_encode($body);
    }
    $resp = wp_remote_request($url, $args);
    if (is_wp_error($resp)) {
        return null;
    }
    $code = wp_remote_retrieve_response_code($resp);
    $json = json_decode(wp_remote_retrieve_body($resp), true);
    return array('status' => $code, 'json' => $json);
}

function token_plugin_search_user($keyword) {
    $res = token_plugin_http('GET', '/api/user/search?keyword=' . rawurlencode($keyword));
    if ($res && $res['json'] && !empty($res['json']['success']) && !empty($res['json']['data'])) {
        return $res['json']['data'];
    }
    return array();
}

function token_plugin_gateway_username($userId) {
    $user = get_userdata($userId);
    if (!$user) return '';
    return $user->user_login;
}

function token_plugin_ensure_account($userId) {
    $username = token_plugin_gateway_username($userId);
    if (!$username) return array('error' => 'no wp user');

    $hits = token_plugin_search_user($username);
    foreach ($hits as $u) {
        if (isset($u['username']) && strtolower($u['username']) === strtolower($username)) {
            return array('user' => $u);
        }
    }

    $email = '';
    $wpUser = get_userdata($userId);
    if ($wpUser) $email = $wpUser->user_email;

    $password = wp_generate_password(20, true, false);
    $res = token_plugin_http('POST', '/api/user/', array(
        'username' => substr($username, 0, 12),
        'password' => $password,
        'email' => substr($email, 0, 50),
        'display_name' => substr($username, 0, 12),
    ));
    if ($res && $res['json'] && !empty($res['json']['success'])) {
        $again = token_plugin_search_user($username);
        foreach ($again as $u) {
            if (isset($u['username']) && strtolower($u['username']) === strtolower($username)) {
                return array('user' => $u);
            }
        }
        return array('error' => 'account created but could not be located');
    }
    $msg = ($res && $res['json'] && !empty($res['json']['message'])) ? $res['json']['message'] : 'create failed';
    return array('error' => $msg);
}

function token_plugin_tier_for_order($order) {
    $total = (float) $order->get_total();
    $tiers = token_plugin_product_tiers();
    $best = null;
    foreach ($tiers as $price => $quota) {
        if (abs($total - $price) < 0.01) {
            $best = array('price' => $price, 'quota' => $quota);
        }
    }
    return $best;
}

add_action('woocommerce_order_status_completed', 'token_plugin_on_order_completed', 10, 1);
add_action('woocommerce_payment_complete', 'token_plugin_on_order_completed', 10, 1);
function token_plugin_on_order_completed($orderId) {
    $order = wc_get_order($orderId);
    if (!$order) return;

    if (get_post_meta($orderId, '_token_plugin_credited', true)) {
        return;
    }

    $tier = token_plugin_tier_for_order($order);
    if (!$tier) return;

    $userId = $order->get_user_id();
    if (!$userId) {
        $order->add_order_note(__('Token: order has no WooCommerce user, cannot credit.', 'token-plugin'));
        return;
    }

    $acct = token_plugin_ensure_account($userId);
    if (!empty($acct['error'])) {
        $order->add_order_note('Token: account sync failed - ' . $acct['error']);
        return;
    }

    $gatewayUser = $acct['user'];
    $gatewayId = isset($gatewayUser['id']) ? $gatewayUser['id'] : 0;
    if (!$gatewayId) {
        $order->add_order_note(__('Token: gateway account id missing.', 'token-plugin'));
        return;
    }

    $res = token_plugin_http('POST', '/api/topup', array(
        'user_id' => $gatewayId,
        'quota' => $tier['quota'],
        'remark' => 'woocommerce order ' . $orderId . ' (' . $tier['price'] . ' USD)',
    ));

    if ($res && $res['json'] && !empty($res['json']['success'])) {
        $order->add_order_note(sprintf(__('Token: credited %d quota for $%s.', 'token-plugin'), $tier['quota'], $tier['price']));
        update_post_meta($orderId, '_token_plugin_credited', 1);
    } else {
        $msg = ($res && $res['json'] && !empty($res['json']['message'])) ? $res['json']['message'] : 'topup failed';
        $order->add_order_note('Token: topup failed - ' . $msg);
    }
}

function token_plugin_ensure_api_token($wpUserId, $gatewayUserId, $buyerAccessToken = null) {
    $stored = get_user_meta($wpUserId, '_oneapi_api_token_key', true);
    if ($stored) {
        return array('key' => $stored, 'cached' => true);
    }
    $payload = array(
        'name'             => 'woocommerce-api',
        'expired_time'     => -1,
        'unlimited_quota'  => true,
    );
    if (empty($buyerAccessToken)) {
        $payload['user_id'] = (int) $gatewayUserId;
    }
    $res = token_plugin_http('POST', '/api/token/', $payload, $buyerAccessToken);
    if ($res && $res['json'] && !empty($res['json']['success']) && !empty($res['json']['data']['key'])) {
        $key = $res['json']['data']['key'];
        token_plugin_http('GET', '/api/token/?p=0&size=1', null, $buyerAccessToken);
        update_user_meta($wpUserId, '_oneapi_api_token_key', $key);
        return array('key' => $key, 'cached' => false);
    }
    $msg = ($res && $res['json'] && !empty($res['json']['message'])) ? $res['json']['message'] : 'token create failed';
    return array('error' => $msg);
}

add_shortcode('token_plugin_console', 'token_plugin_console_render');
function token_plugin_console_render() {
    if (!is_user_logged_in()) {
        return '<p>Please <a href="' . esc_url(wp_login_url(get_permalink())) . '">log in</a> to view your API key and balance.</p>';
    }

    $userId = get_current_user_id();
    $username = token_plugin_gateway_username($userId);
    if (!$username) return '<p>Account error.</p>';

    $hits = token_plugin_search_user($username);
    $me = null;
    foreach ($hits as $u) {
        if (isset($u['username']) && strtolower($u['username']) === strtolower($username)) {
            $me = $u;
            break;
        }
    }
    if (!$me) {
        $acct = token_plugin_ensure_account($userId);
        if (!empty($acct['error'])) return '<p>Account sync failed: ' . esc_html($acct['error']) . '</p>';
        $me = $acct['user'];
    }

    $gatewayId = isset($me['id']) ? (int) $me['id'] : 0;
    $buyerAccessToken = isset($me['access_token']) ? $me['access_token'] : '';
    $apiToken = token_plugin_ensure_api_token($userId, $gatewayId, $buyerAccessToken);
    if (!empty($apiToken['error'])) return '<p>API token create failed: ' . esc_html($apiToken['error']) . '</p>';
    $apiKey = $apiToken['key'];
    $quota = isset($me['quota']) ? (int) $me['quota'] : 0;
    $used = isset($me['used_quota']) ? (int) $me['used_quota'] : 0;
    $balance = $quota - $used;
    $usd = round($balance / 500000, 2);

    ob_start();
    ?>
    <div style="font-family:inherit;max-width:720px;margin:0 auto;">
      <h3>Your API Credentials</h3>
      <table style="width:100%;border-collapse:collapse;font-size:14px;">
        <tr><td style="padding:8px;border:1px solid #ddd;font-weight:600;">API Key</td>
            <td style="padding:8px;border:1px solid #ddd;word-break:break-all;"><?php echo esc_html($apiKey); ?></td></tr>
        <tr><td style="padding:8px;border:1px solid #ddd;font-weight:600;">Balance</td>
            <td style="padding:8px;border:1px solid #ddd;">$<?php echo esc_html($usd); ?> (<?php echo number_format($balance); ?> credits)</td></tr>
        <tr><td style="padding:8px;border:1px solid #ddd;font-weight:600;">Used</td>
            <td style="padding:8px;border:1px solid #ddd;"><?php echo number_format($used); ?> credits</td></tr>
      </table>
      <p style="font-size:13px;color:#666;">Use the API key above as <code>Authorization: Bearer &lt;your key&gt;</code> to call the gateway.</p>
    </div>
    <?php
    return ob_get_clean();
}

add_action('admin_menu', 'token_plugin_admin_menu');
function token_plugin_admin_menu() {
    add_options_page('Token Gateway', 'Token', 'manage_options', 'token-plugin', 'token_plugin_settings_page');
}

function token_plugin_settings_page() {
    if (isset($_POST['submit'])) {
        check_admin_referer('token_plugin_save');
        $settings = array(
            'oneapi_base' => sanitize_text_field($_POST['oneapi_base']),
            'admin_token' => sanitize_text_field($_POST['admin_token']),
        );
        update_option(TOKEN_PLUGIN_OPTION, $settings);
        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
    }
    $settings = token_plugin_settings();
    ?>
    <div class="wrap">
      <h1>Token Gateway</h1>
      <form method="post">
        <?php wp_nonce_field('token_plugin_save'); ?>
        <table class="form-table">
          <tr><th>One API Base URL</th>
              <td><input type="text" name="oneapi_base" value="<?php echo esc_attr($settings['oneapi_base']); ?>" style="width:320px;" /></td></tr>
          <tr><th>One API Admin Token</th>
              <td><input type="password" name="admin_token" value="<?php echo esc_attr($settings['admin_token']); ?>" style="width:320px;" /></td></tr>
        </table>
        <?php submit_button(); ?>
      </form>
      <p>Add the shortcode <code>[token_plugin_console]</code> to any page to show the buyer's API key and balance.</p>
      <p>TOKEN products should be priced at $10 / $50 / $100 to map to the configured quota tiers.</p>
    </div>
    <?php
}
