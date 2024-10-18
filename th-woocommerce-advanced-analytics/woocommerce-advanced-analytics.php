<?php
/*
Plugin Name: WooCommerce Advanced Analytics Dashboard
Description: Provides an advanced analytics dashboard for WooCommerce stores.
Version: 1.0
Author: Taha Ahmadpour
*/

// Ensure WooCommerce is active before enabling this plugin
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    exit;
}

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'admin/class-analytics-dashboard.php';
require_once plugin_dir_path(__FILE__) . 'admin/class-analytics-scripts.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-analytics-data.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-analytics-reports.php';

// Initialize the plugin
new WooCommerce_Advanced_Analytics();
