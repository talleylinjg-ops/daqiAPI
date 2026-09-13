<?php
/**
 * Abstract eSIM provider adapter.
 * Each provider implements: fetch_catalog() and purchase().
 * Catalog items are normalized to a common shape for the price engine.
 */
abstract class Esim_Adapter {

    protected $settings;

    public function __construct($settings) {
        $this->settings = $settings;
    }

    /** Unique provider id, e.g. 'airalo'. */
    abstract public function get_id();

    /** Display name. */
    abstract public function get_name();

    /** Whether this adapter is configured (credentials present). */
    abstract public function is_configured();

    /**
     * Fetch full catalog. Returns array of normalized package records:
     * array(
     *   'provider'   => provider id,
     *   'package_id' => provider package id,
     *   'title'      => string,
     *   'country'    => ISO cc2 or 'global',
     *   'data_gb'    => int|null (null for unlimited),
     *   'days'       => int,
     *   'unlimited'  => bool,
     *   'net_price'  => float USD (cost to you),
     *   'retail_price' => float USD (min selling price),
     *   'voice'      => int|null,
     *   'text'       => int|null,
     * )
     */
    abstract public function fetch_catalog();

    /**
     * Purchase package(s). Returns array with 'ok' + 'sims' or 'error'.
     * sims[]: { iccid, qrcode, qrcode_url, lpa, direct_apple_install }
     */
    abstract public function purchase($packageId, $quantity = 1, $description = '');
}
