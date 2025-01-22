<?php
if (!defined('ABSPATH')) {
    exit;
}

// Metabox hinzufügen
function special_product_add_meta_box() {
    add_meta_box(
        'special_product_data',
        __('Spezielle Produktdaten', 'special-product'),
        'special_product_meta_box_callback',
        'product', // Nur für WooCommerce-Produkte
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'special_product_add_meta_box');

function special_product_meta_box_callback($post) {
    echo '<button id="update-variants-btn" data-product-id="' . esc_attr($post->ID) . '">Eigenschaften aktualisieren</button>';
    echo '<div id="variants-table"></div>';
}
