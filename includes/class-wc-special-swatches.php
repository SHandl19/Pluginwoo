<?php
if (!defined('ABSPATH')) exit;

class WC_Special_Swatches {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function enqueue_scripts() {
        wp_enqueue_script('special-swatches', plugin_dir_url(__FILE__) . '../assets/custom-swatches.js', ['jquery'], null, true);
    }
}

new WC_Special_Swatches();
