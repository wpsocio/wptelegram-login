<?php
/**
 * The main plugin file.
 *
 * @link              https://wpsocio.com
 * @since             1.0.0
 * @package           WPTelegram\Login
 *
 * @wordpress-plugin
 * Plugin Name:       WP Telegram Login
 * Plugin URI:        https://t.me/WPTelegram
 * Description:       Let the users login to your WordPress website with their Telegram and make it simple for them to get connected and let them receive their email notifications on Telegram.
 * Version:           1.11.4
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            WP Socio
 * Author URI:        https://wpsocio.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wptelegram-login
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'WPTELEGRAM_LOGIN_VER', '1.11.4' );

defined( 'WPTELEGRAM_LOGIN_MAIN_FILE' ) || define( 'WPTELEGRAM_LOGIN_MAIN_FILE', __FILE__ );

defined( 'WPTELEGRAM_LOGIN_BASENAME' ) || define( 'WPTELEGRAM_LOGIN_BASENAME', plugin_basename( WPTELEGRAM_LOGIN_MAIN_FILE ) );

define( 'WPTELEGRAM_LOGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

define( 'WPTELEGRAM_LOGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );

// Telegram user ID meta key.
if ( ! defined( 'WPTELEGRAM_USER_ID_META_KEY' ) ) {
	// Common for all WP Telegram plugins.
	define( 'WPTELEGRAM_USER_ID_META_KEY', 'wptelegram_user_id' );
}
if ( ! defined( 'WPTELEGRAM_USERNAME_META_KEY' ) ) {
	// Common for all WP Telegram plugins.
	define( 'WPTELEGRAM_USERNAME_META_KEY', 'wptelegram_username' );
}

/**
 * Include autoloader.
 */
require WPTELEGRAM_LOGIN_DIR . '/autoload.php';
require_once dirname( WPTELEGRAM_LOGIN_MAIN_FILE ) . '/vendor/autoload.php';

/**
 * The code that runs during plugin activation.
 */
function activate_wptelegram_login() {
	\WPTelegram\Login\includes\Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_wptelegram_login() {
	\WPTelegram\Login\includes\Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wptelegram_login' );
register_deactivation_hook( __FILE__, 'deactivate_wptelegram_login' );

/**
 * Begins execution of the plugin and acts as the main instance of WPTelegram\Login.
 *
 * Returns the main instance of WPTelegram\Login to prevent the need to use globals.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 *
 * @return \WPTelegram\Login\includes\Main
 */
function WPTG_Login() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName -- Ignore  snake_case

	return \WPTelegram\Login\includes\Main::instance();
}

$requirements = new \WPTelegram\Login\includes\Requirements( WPTELEGRAM_LOGIN_MAIN_FILE );

if ( $requirements->satisfied() ) {
	// Fire.
	WPTG_Login()->init();

	define( 'WPTELEGRAM_LOGIN_LOADED', true );
} else {
	add_filter( 'after_plugin_row_' . WPTELEGRAM_LOGIN_BASENAME, [ $requirements, 'display_requirements' ] );
}
