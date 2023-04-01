<?php
/**
 * The main plugin file.
 *
 * @link              https://wpsocio.com
 * @since             1.0.0
 * @package           WPTelegram_Login
 *
 * @wordpress-plugin
 * Plugin Name:       WP Telegram Login Dev
 * Plugin URI:        https://t.me/WPTelegram
 * Description:       ❌ DO NOT DELETE ❌ Development Environment for WP Telegram Login. Versioned high to avoid auto update.
 * Version:           999.999.999
 * Requires at least: 5.9
 * Requires PHP:      7.0
 * Author:            WP Socio
 * Author URI:        https://wpsocio.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wptelegram-login
 * Domain Path:       /languages
 * Update URI:        false
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'WPTELEGRAM_DEV' ) ) {
	define( 'WPTELEGRAM_DEV', true );
}

define( 'WPTELEGRAM_LOGIN_MAIN_FILE', __FILE__ );

define( 'WPTELEGRAM_LOGIN_BASENAME', plugin_basename( WPTELEGRAM_LOGIN_MAIN_FILE ) );

require plugin_dir_path( __FILE__ ) . 'src/wptelegram-login.php';

register_activation_hook( __FILE__, 'activate_wptelegram_login' );
register_deactivation_hook( __FILE__, 'deactivate_wptelegram_login' );
