<?php
/**
 *
 * @link              https://t.me/manzoorwanijk
 * @since             1.0.0
 * @package           WPTelegram_Login
 *
 * @wordpress-plugin
 * Plugin Name:       WP Telegram Login Dev
 * Plugin URI:        https://t.me/WPTelegram
 * Description:       Development Environment for WP Telegram Login. Versioned high to avoid auto update.
 * Version:           999.999.999
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

if ( ! defined( 'WPTELEGRAM_DEV' ) ) {
	define( 'WPTELEGRAM_DEV', true );
}

$settings_file = plugin_dir_path( __FILE__ ) . 'src/admin/settings/dist/settings-dist.js';
$main_file     = plugin_dir_path( __FILE__ ) . 'src/wptelegram-login.php';

if ( file_exists( $settings_file ) && file_exists( $main_file ) ) {
	require $main_file;
} else {
	add_action( 'admin_notices', 'wptelegram_login_npm_build_notice' );
}
/**
 * Display build admin notice.
 */
function wptelegram_login_npm_build_notice() {
	$class   = 'notice notice-error';
	$message = sprintf(
		esc_html( '%1$s is active but requires a build. Please run %2$s and then %3$s or %4$s to create a build.' ),
		'WP Telegram Login Dev',
		'<code>npm install</code>',
		'<code>npm run dev</code>',
		'<code>npm run build</code>'
	);

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
}
