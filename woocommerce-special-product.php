<?php
/**
 * Plugin Name: WooCommerce Spezial-Produkt
 * Plugin URI:  https://deine-webseite.de
 * Description: Fügt eine neue WooCommerce-Produktart mit individuellen Varianten & Swatches hinzu.
 * Version: 1.1.0
 * Author: Dein Name
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) exit;

// Autoload Klassen
require_once plugin_dir_path(__FILE__) . 'includes/class-wc-special-product.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-wc-special-tab.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-wc-special-ajax.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-wc-special-swatches.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-wc-special-stock.php';
/*require_once plugin_dir_path(__FILE__) . 'includes/class-wc-special-price.php';*/

// Plugin aktivieren
function wc_special_product_activate() {
    if (!class_exists('WooCommerce')) {
        wp_die('WooCommerce muss installiert und aktiviert sein.');
    }
}
register_activation_hook(__FILE__, 'wc_special_product_activate');
function enqueue_admin_scripts() {
    wp_enqueue_script('special-product-admin', plugin_dir_url(__FILE__) . 'assets/admin.js', ['jquery'], null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_admin_scripts');
