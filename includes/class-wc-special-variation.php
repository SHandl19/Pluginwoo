<?php
class WC_Special_Variation {
    public function __construct() {
        add_action('woocommerce_product_options_general_product_data', [$this, 'add_variation_fields']);
        add_action('woocommerce_process_product_meta', [$this, 'save_variation_fields']);
        add_action('wp_ajax_load_special_variations', [$this, 'load_special_variations']);
    }

    public function add_variation_fields() {
        global $post;
        echo '<div class="options_group">';
        woocommerce_wp_text_input([
            'id' => '_special_sizes',
            'label' => __('Spezialgrößen (Kinder/Erwachsene)', 'woocommerce'),
            'desc_tip' => 'true',
            'description' => __('Trenne Größen mit Komma (z. B. XS, S, M, L, XL)', 'woocommerce')
        ]);
        echo '</div>';
    }

    public function save_variation_fields($post_id) {
        if (isset($_POST['_special_sizes'])) {
            update_post_meta($post_id, '_special_sizes', sanitize_text_field($_POST['_special_sizes']));
        }
    }

    public function load_special_variations() {
        check_ajax_referer('woocommerce-special-product', 'security');
        $product_id = intval($_POST['product_id']);
        $sizes = get_post_meta($product_id, '_special_sizes', true);
        wp_send_json_success(explode(',', $sizes));
    }
}
new WC_Special_Variation();
