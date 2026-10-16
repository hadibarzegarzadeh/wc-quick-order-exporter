<?php
/**
 * Plugin Name: WooCommerce Quick Order Exporter
 * Description: Easily export WooCommerce orders to a CSV file based on date ranges and order statuses.
 * Version: 1.0.0
 * Author: Hadi Barzegarzadeh
 * Text Domain: wc-quick-order-exporter
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WC_QOE_VERSION', '1.0.0');
define('WC_QOE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WC_QOE_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once WC_QOE_PLUGIN_DIR . 'includes/class-wc-qoe-admin.php';
require_once WC_QOE_PLUGIN_DIR . 'includes/class-wc-qoe-exporter.php';

function wc_qoe_init() {
    new WC_QOE_Admin();
}
add_action('plugins_loaded', 'wc_qoe_init');
