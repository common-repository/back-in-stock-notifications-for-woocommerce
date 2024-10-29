<?php

/**
 * Plugin Name: Back in stock notifications for WooCommerce
 * Plugin URI: https://www.getinnovation.dev/wordpres-plugins/woocommerce-stock-notify-me/
 * Description: Woocommerce subscribe system for out of stock products. 
 * Version: 1.0.1
 * Requires at least: 5.8.6
 * Requires PHP: 7.3
 * Author: Get Innovation Dev.
 * Author URI: https://getinnovation.dev/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: back-in-stock-notifications-for-woocommerce
 * Domain Path: /languages
 *
 * WC tested up to: 8.2.1
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}


defined('WSNM_PATH') or define('WSNM_PATH', plugin_dir_path(__FILE__));
defined('WSNM_URL')  or define('WSNM_URL',  plugin_dir_url(__FILE__));
defined('WSNM_BASE') or define('WSNM_BASE', plugin_basename(__FILE__));
$version = get_file_data(WSNM_PATH . basename(WSNM_BASE), array('Version'), 'plugin');
$textDomain = get_file_data(WSNM_PATH . basename(WSNM_BASE), array('Text Domain'), 'plugin');
$pluginName = get_file_data(WSNM_PATH . basename(WSNM_BASE), array('Plugin Name'), 'plugin');

/**
 * Currently plugin version.
 */
defined('WSNM_VERSION') or define('WSNM_VERSION', $version[0]);

/**
 * The unique identifier.
 */
defined('WSNM_DOMAIN') or define('WSNM_DOMAIN', $textDomain[0]);

/**
 * Plugin Name
 */
defined('WSNM_NAME') or define('WSNM_NAME', $pluginName[0]);


/**
 * activate_wsnm
 *
 * @return void
 */
function activate_wsnm()
{
    require_once WSNM_PATH . 'includes/class-wsnm-activator.php';
    WSNM_Woo_Stock_Notify_Me_Activator::activate();
}


/**
 * deactivate_wsnm
 * 
 * @return void
 */
function deactivate_wsnm()
{
    require_once WSNM_PATH . 'includes/class-wsnm-deactivator.php';
    WSNM_Woo_Stock_Notify_Me_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wsnm');
register_deactivation_hook(__FILE__, 'deactivate_wsnm');

/**
 * Include the main class.
 */
require WSNM_PATH . 'includes/class-wsnm.php';


/**
 * @return void
 */
function run_WSNM_Woo_Stock_Notify_Me(): void
{
    $plugin = new WSNM_Woo_Stock_Notify_Me();
    $plugin->run();
}

run_WSNM_Woo_Stock_Notify_Me();