<?php
/**
 * Airalo adapter.
 * Base URL: partners-api.airalo.com (prod) / sandbox-partners-api.airalo.com (sandbox)
 * Docs: https://share.apidog.com/e776ed42-a7e7-4c52-9ea2-b65d4758fea2/
 */

if (!class_exists('Esim_Adapter')) return;

class Esim_Adapter_Airalo extends Esim_Adapter {

    public function get_id() { return 'airalo'; }
    public function get_name() { return 'Airalo'; }

    public function is_configured() {
        return !empty($this->settings['client_id']) && !empty($this->settings['client_secret']);
    }

    public function fetch_catalog() {
        $res = $this->api('GET', '/v2/packages', array('limit' => 1000));
        if (empty($res['json']['data'])) return array();
        return $this->normalize($res['json']['data']);
    }

    public function purchase($packageId, $quantity = 1, $description = '') {
        $params = array(
            'package_id' => $packageId,
            'quantity'   => (string) $quantity,
            'type'       => 'sim',
        );
        if ($description) $params['description'] = $description;
        $res = $this->api('POST', '/v2/orders', $params, true);

        if (empty($res['json']['data']) || empty($res['json']['data']['sims'])) {
            return array(
                'ok'    => false,
                'error' => isset($res['json']['meta']['message']) ? $res['json']['meta']['message'] : 'airalo order failed (status ' . $res['status'] . ')',
            );
        }

        $data = $res['json']['data'];
        $sims = array();
        foreach ($data['sims'] as $sim) {
            $sims[] = array(
                'iccid'                => isset($sim['iccid']) ? $sim['iccid'] : '',
                'lpa'                  => isset($sim['lpa']) ? $sim['lpa'] : '',
                'qrcode'               => isset($sim['qrcode']) ? $sim['qrcode'] : '',
                'qrcode_url'           => isset($sim['qrcode_url']) ? $sim['qrcode_url'] : '',
                'direct_apple_install' => isset($sim['direct_apple_installation_url']) ? $sim['direct_apple_installation_url'] : '',
            );
        }
        return array(
            'ok'           => true,
            'order_code'   => isset($data['code']) ? $data['code'] : '',
            'package_name' => isset($data['package']) ? $data['package'] : '',
            'data'         => isset($data['data']) ? $data['data'] : '',
            'validity'     => isset($data['validity']) ? $data['validity'] : '',
            'price'        => isset($data['price']) ? $data['price'] : '',
            'net_price'    => isset($data['net_price']) ? $data['net_price'] : '',
            'sims'         => $sims,
        );
    }

    public function get_settings_fields() {
        return array(
            'client_id'     => array('label' => 'Client ID', 'type' => 'text'),
            'client_secret' => array('label' => 'Client Secret', 'type' => 'password'),
            'sandbox'       => array('label' => 'Sandbox Mode', 'type' => 'checkbox'),
        );
    }

    public function get_base_url() {
        return empty($this->settings['sandbox']) ? 'https://partners-api.airalo.com' : 'https://sandbox-partners-api.airalo.com';
    }

    private function api($method, $path, $params = array(), $isForm = false) {
        $token = $this->get_token();
        if (!$token) return array('status' => 401, 'json' => array());

        $url = $this->get_base_url() . '/' . ltrim($path, '/');
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
        if (is_wp_error($resp)) return array('status' => 0, 'json' => array());
        return array(
            'status' => wp_remote_retrieve_response_code($resp),
            'json'   => json_decode(wp_remote_retrieve_body($resp), true),
        );
    }

    private function get_token() {
        $cache_key = 'esim_airalo_token';
        $cached = get_transient($cache_key);
        if ($cached) return $cached;

        if (!$this->is_configured()) return null;

        $resp = wp_remote_post($this->get_base_url() . '/v2/token', array(
            'timeout' => 20,
            'headers' => array('Accept' => 'application/json'),
            'body'    => array(
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->settings['client_id'],
                'client_secret' => $this->settings['client_secret'],
            ),
        ));
        if (is_wp_error($resp)) return null;
        $json = json_decode(wp_remote_retrieve_body($resp), true);
        if (empty($json['data']['access_token'])) return null;

        set_transient($cache_key, $json['data']['access_token'], 86400 - 300);
        return $json['data']['access_token'];
    }

    private function normalize($data) {
        $out = array();
        foreach ($data as $pkg) {
            $country = isset($pkg['country_code']) ? $pkg['country_code'] : 'global';
            foreach ($pkg['operators'] as $op) {
                $opTitle = isset($op['title']) ? $op['title'] : '';
                foreach ($op['packages'] as $pk) {
                    $net = isset($pk['net_price']) ? (float) $pk['net_price'] : null;
                    $retail = null;
                    if (isset($pk['prices']['recommended_retail_price']['USD'])) {
                        $retail = (float) $pk['prices']['recommended_retail_price']['USD'];
                    } elseif (isset($pk['price'])) {
                        $retail = (float) $pk['price'];
                    }
                    $out[] = array(
                        'provider'    => $this->get_id(),
                        'package_id'  => isset($pk['id']) ? $pk['id'] : '',
                        'title'       => $opTitle . ' - ' . (isset($pk['title']) ? $pk['title'] : ''),
                        'country'     => $country,
                        'data_gb'     => $this->parse_gb(isset($pk['data']) ? $pk['data'] : ''),
                        'days'        => isset($pk['day']) ? (int) $pk['day'] : 0,
                        'unlimited'   => !empty($pk['is_unlimited']),
                        'net_price'   => $net,
                        'retail_price' => $retail,
                        'voice'       => isset($pk['voice']) ? $pk['voice'] : null,
                        'text'        => isset($pk['text']) ? $pk['text'] : null,
                    );
                }
            }
        }
        return $out;
    }

    private function parse_gb($data) {
        if ($data === null) return null;
        if (stripos($data, 'unlimited') !== false) return null;
        if (preg_match('/([\d.]+)\s*GB/i', $data, $m)) return (float) $m[1];
        if (preg_match('/([\d.]+)\s*MB/i', $data, $m)) return (float) $m[1] / 1024;
        return null;
    }
}
