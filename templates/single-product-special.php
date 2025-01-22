<?php
defined('ABSPATH') || exit;
global $product;

if ($product->get_type() !== 'special_product') {
    return;
}

wc_get_template_part('content', 'single-product');
?>
