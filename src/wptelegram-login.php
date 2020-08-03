<?php
/**
 * The main plugin file.
 *
 * @link              https://t.me/manzoorwanijk
 * @since             1.0.0
 * @package           WPTelegram_Login
 *
 * @wordpress-plugin
 * Plugin Name:       WP Telegram Login
 * Plugin URI:        https://t.me/WPTelegram
 * Description:       Let the users login to your WordPress website with their Telegram and make it simple for them to get connected and let them receive their email notifications on Telegram.
 * Version:           1.7.1
 * Author:            Manzoor Wani
 * Author URI:        https://t.me/manzoorwanijk
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
define( 'WPTELEGRAM_LOGIN_VER', '1.7.1' );

define( 'WPTELEGRAM_LOGIN_BASENAME', plugin_basename( __FILE__ ) );

define( 'WPTELEGRAM_LOGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

defined( 'WPTELEGRAM_LOGIN_URL' ) || define( 'WPTELEGRAM_LOGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );

// Telegram user ID meta key.
if ( ! defined( 'WPTELEGRAM_USER_META_KEY' ) ) {
	// Common for all WP Telegram plugins.
	define( 'WPTELEGRAM_USER_META_KEY', 'wptelegram_user_id' );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wptelegram-login-activator.php
 */
function activate_wptelegram_login() {
	require_once WPTELEGRAM_LOGIN_DIR . '/includes/class-wptelegram-login-activator.php';
	WPTelegram_Login_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wptelegram-login-deactivator.php
 */
function deactivate_wptelegram_login() {
	require_once WPTELEGRAM_LOGIN_DIR . '/includes/class-wptelegram-login-deactivator.php';
	WPTelegram_Login_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wptelegram_login' );
register_deactivation_hook( __FILE__, 'deactivate_wptelegram_login' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require WPTELEGRAM_LOGIN_DIR . '/includes/class-wptelegram-login.php';

/**
 * Begins execution of the plugin and acts as the main instance of WPTelegram_Login.
 *
 * Returns the main instance of WPTelegram_Login to prevent the need to use globals.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function WPTG_Login() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName -- Ignore  snake_case

	return WPTelegram_Login::instance();
}

// Fire.
WPTG_Login();

define( 'WPTELEGRAM_LOGIN_LOADED', true );
