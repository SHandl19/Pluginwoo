<?php
if (!defined('ABSPATH')) exit; // Sicherheitscheck

class WC_Special_Price {
    public function __construct() {
        // Preisbereich beim Speichern aktualisieren
        add_action('woocommerce_process_product_meta', [$this, 'update_special_price']);

        // Spezialpreis-Spalte in der Admin-Produktübersicht hinzufügen
        add_filter('manage_edit-product_columns', [$this, 'add_special_price_column']);
        add_action('manage_product_posts_custom_column', [$this, 'display_special_price_column'], 10, 2);
    }

    /**
     * Berechnet und speichert den Preisbereich der Spezialvarianten
     */
    public function update_special_price($post_id) {
        $variants = get_post_meta($post_id, '_special_variants', true);

        if (!is_array($variants) || empty($variants)) {
            delete_post_meta($post_id, '_special_price_range');
            return;
        }

        // Preise sammeln
        $prices = [];
        foreach ($variants as $variant) {
            if (!empty($variant['price'])) {
                $prices[] = floatval($variant['price']);
            }
        }

        // Preisbereich berechnen
        if (!empty($prices)) {
            $min_price = min($prices);
            $max_price = max($prices);
            $price_range = ($min_price == $max_price) ? wc_price($min_price) : wc_price($min_price) . ' – ' . wc_price($max_price);
        } else {
            $price_range = '-';
        }

        // Speichert den berechneten Preisbereich als Meta-Wert
        update_post_meta($post_id, '_special_price_range', $price_range);
    }

    /**
     * Fügt eine neue Spalte für Spezial-Preise in der Admin-Produktübersicht hinzu
     */
    public function add_special_price_column($columns) {
        $new_columns = [];
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'price') {
                $new_columns['special_price'] = __('Spezial-Preis', 'woocommerce');
            }
        }
        return $new_columns;
    }

    /**
     * Zeigt den Spezial-Preisbereich in der Admin-Produktübersicht
     */
    public function display_special_price_column($column, $post_id) {
        if ($column === 'special_price') {
            $price_range = get_post_meta($post_id, '_special_price_range', true);
            echo (!empty($price_range)) ? esc_html($price_range) : '-';
        }
    }
}

new WC_Special_Price();
