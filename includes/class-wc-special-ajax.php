<?php
if (!defined('ABSPATH')) exit;

class WC_Special_Ajax {
    public function __construct() {
        add_action('wp_ajax_load_special_variation', [$this, 'load_special_variation']);
        add_action('wp_ajax_nopriv_load_special_variation', [$this, 'load_special_variation']);
    }

    public function load_special_variation() {
        check_ajax_referer('woocommerce-special-product', 'security');

        $product_id = intval($_POST['product_id']);
        $variant = sanitize_text_field($_POST['variant']);

        // Daten abrufen
        $variants_data = get_post_meta($product_id, '_special_variants', true);
        $variant_list = explode("\n", $variants_data);
        $result = [];

        foreach ($variant_list as $entry) {
            $parts = explode('|', $entry);
            if (trim($parts[1]) == $variant) {
                $result = [
                    'price' => trim($parts[2]),
                    'stock' => trim($parts[3])
                ];
                break;
            }
        }

        wp_send_json_success($result);
    }
}

new WC_Special_Ajax();
