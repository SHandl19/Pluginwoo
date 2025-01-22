<?php
if (!defined('ABSPATH')) exit; // Sicherheitscheck

class WC_Special_Stock {
    public function __construct() {
        add_action('woocommerce_process_product_meta', [$this, 'update_stock_status'], 99, 1);
    }

    /**
     * Aktualisiert den Lagerstatus basierend auf den Spezial-Varianten.
     */
    public function update_stock_status($post_id) {
        if (get_post_type($post_id) !== 'product') {
            return;
        }

        $variants = get_post_meta($post_id, '_special_variants', true);
        $has_stock = false;
        $total_stock = 0;

        if (!empty($variants) && is_array($variants)) {
            foreach ($variants as $variant) {
                if (!empty($variant['stock']) && $variant['stock'] === 'instock') {
                    $has_stock = true;
                    $total_stock += (isset($variant['quantity']) ? intval($variant['quantity']) : 0);
                }
            }
        }

        // WooCommerce-Lagerverwaltung setzen
        update_post_meta($post_id, '_manage_stock', 'yes');
        update_post_meta($post_id, '_stock', $total_stock);
        update_post_meta($post_id, '_stock_status', $has_stock ? 'instock' : 'outofstock');

        // 🚀 Nutze WooCommerce-Methoden für Bestandsaktualisierung
        wc_update_product_stock_status($post_id, $has_stock ? 'instock' : 'outofstock');
        
        // 🚀 WooCommerce-Caches löschen, um die Änderung sofort zu sehen
        wc_delete_product_transients($post_id);
        clean_post_cache($post_id);
        wp_cache_delete($post_id, 'post_meta');

        // 🔍 Debugging
        error_log("✅ WooCommerce-Lager aktualisiert: Produkt-ID: $post_id - Bestand: $total_stock - Status: " . ($has_stock ? 'instock' : 'outofstock'));
    }
}

new WC_Special_Stock();
