<?php
/**
 * Plugin Name: Tenders-SA for WordPress
 * Plugin URI:  https://tenders-sa.org/wordpress
 * Description: Display South African government tender data — shortcodes, widgets, and full-page templates powered by the Tenders-SA Developer API v2.
 * Version:     1.0.0
 * Requires at least: 5.8
 * Tested up to: 6.5
 * Requires PHP: 7.4
 * Author:      Tenders-SA
 * Author URI:  https://tenders-sa.org
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tendersa-for-wp
 * Domain Path: /languages
 *
 * @package TendersaForWp
 */

defined('ABSPATH') || exit;

define('TENDERSA_VERSION', '1.0.0');
define('TENDERSA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TENDERSA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TENDERSA_PLUGIN_BASENAME', plugin_basename(__FILE__));

spl_autoload_register(function ($class) {
    $prefix = 'TendersaForWp\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) return;

    $relative_class = substr($class, strlen($prefix));
    $file = TENDERSA_PLUGIN_DIR . 'src/' . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) require $file;
});

register_activation_hook(__FILE__, ['TendersaForWp\\Plugin', 'activate']);
register_deactivation_hook(__FILE__, ['TendersaForWp\\Plugin', 'deactivate']);

add_action('plugins_loaded', ['TendersaForWp\\Plugin', 'init']);
