<?php
if (!defined('ABSPATH')) exit; // Sicherheitscheck

// Warte, bis WooCommerce geladen ist, bevor die Produktklasse registriert wird
add_action('plugins_loaded', function () {
    if (class_exists('WC_Product')) {
        class WC_Product_Special extends WC_Product {
            public function get_type() {
                return 'special_product';
            }
        }
    }
});

class WC_Special_Product {
    public function __construct() {
        // Produktart in WooCommerce registrieren
        add_filter('product_type_selector', [$this, 'add_special_product_type']);
        
        // Speichern der Produktart beim Aktualisieren des Produkts
        add_action('woocommerce_process_product_meta', [$this, 'save_special_product_type']);

        // Produktklasse zu WooCommerce hinzufügen
        add_filter('woocommerce_product_class', [$this, 'set_special_product_class'], 10, 2);
    }

    /**
     * Fügt die Produktart zur Auswahl im Backend hinzu
     */
    public function add_special_product_type($types) {
        $types['special_product'] = __('Spezial-Produkt', 'woocommerce');
        return $types;
    }

    /**
     * Speichert die Produktart nach dem Speichern des Produkts
     */
    public function save_special_product_type($post_id) {
        if (isset($_POST['product-type']) && $_POST['product-type'] === 'special_product') {
            update_post_meta($post_id, '_product_type', 'special_product');
        }
    }

    /**
     * Setzt die richtige Produktklasse für die Produktart
     */
    public function set_special_product_class($classname, $product_type) {
        if ($product_type === 'special_product') {
            return 'WC_Product_Special';
        }
        return $classname;
    }
}

new WC_Special_Product();
