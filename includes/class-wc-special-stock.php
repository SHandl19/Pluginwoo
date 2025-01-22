<?php
if (!defined('ABSPATH')) exit; // Sicherheitscheck

class WC_Special_Stock {
    public function __construct() {
        add_action('woocommerce_process_product_meta', [$this, 'update_special_stock'], 99, 1);
    }

    /**
     * Aktualisiert den Lagerstatus basierend auf den Spezial-Varianten, beeinflusst KEINE anderen Produktarten.
     */
    public function update_special_stock($post_id) {
        $product = wc_get_product($post_id);

        // 🛑 Nur für Spezial-Produkte ausführen!
        if (!$product || $product->get_type() !== 'special_product') {
            return;
        }

        $variants = get_post_meta($post_id, '_special_variants', true);
        $has_stock = false;
        $total_stock = 0;
        $prices = [];

        if (!empty($variants) && is_array($variants)) {
            foreach ($variants as $variant) {
                $quantity = isset($variant['quantity']) ? intval($variant['quantity']) : 0;
                $price = isset($variant['price']) ? floatval($variant['price']) : 0;
                $stock_status = (!empty($variant['stock']) && $quantity > 0) ? 'instock' : 'outofstock';

                if ($quantity > 0) {
                    $has_stock = true;
                    $total_stock += $quantity;
                }

                if ($price > 0) {
                    $prices[] = $price;
                }
            }
        }

        // ✅ Nur Spezial-Produkte aktualisieren
        update_post_meta($post_id, '_manage_stock', 'yes');
        update_post_meta($post_id, '_stock', $total_stock);
        update_post_meta($post_id, '_stock_status', $has_stock ? 'instock' : 'outofstock');
        wc_update_product_stock_status($post_id, $has_stock ? 'instock' : 'outofstock');

        // 🔄 Preise für Spezial-Produkte setzen
        if (!empty($prices)) {
            update_post_meta($post_id, '_price', min($prices));
            update_post_meta($post_id, '_min_price', min($prices));
            update_post_meta($post_id, '_max_price', max($prices));
        }

        // 🛠 WooCommerce Cache bereinigen
        wc_delete_product_transients($post_id);
        clean_post_cache($post_id);
        wp_cache_delete($post_id, 'post_meta');

        // 🔥 FIX: Verhindert "Bestand wurde nicht aktualisiert" Meldung
        delete_post_meta($post_id, '_edit_lock');

        // 🛠 Debugging
        error_log("✅ Spezial-Produkt aktualisiert: Produkt-ID: $post_id - Bestand: $total_stock - Status: " . ($has_stock ? 'instock' : 'outofstock'));
    }
}

new WC_Special_Stock();
