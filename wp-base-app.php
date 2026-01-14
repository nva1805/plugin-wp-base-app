<?php
/**
 * Plugin Name:       WP Base App
 * Plugin URI:        https://example.com/wp-base-app
 * Description:       A professional WordPress plugin base with modern architecture, routing, and best practices
 * Version:           1.0.0
 * Author:            Your Name
 * Author URI:        https://example.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-base-app
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

/**
 * Plugin defines
 */
define('WP_BASE_APP_VERSION', '1.0.0');
define('WP_BASE_APP_PATH', plugin_dir_path(__FILE__));
define('WP_BASE_APP_URL', plugin_dir_url(__FILE__));
define('WP_BASE_APP_TEMPLATES', WP_BASE_APP_PATH . 'templates');
define('THEME_TEMPLATES', get_stylesheet_directory() . '/templates');


/**
 * Composer Autoloader
 */
require_once WP_BASE_APP_PATH . 'vendor/autoload.php';

function run_wp_base_app()
{
  new WPBaseApp\Plugin();
}

add_action('plugins_loaded', 'run_wp_base_app', 10);
register_activation_hook(__FILE__, 'flush_rewrite_rules');
register_deactivation_hook(__FILE__, 'flush_rewrite_rules');

