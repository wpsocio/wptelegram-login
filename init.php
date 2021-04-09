<?php
/**
 *
 * @link              https://manzoorwani.dev
 * @since             1.0.0
 * @package           WPTelegram_Login
 *
 * @wordpress-plugin
 * Plugin Name:       WP Telegram Login Dev
 * Plugin URI:        https://t.me/WPTelegram
 * Description:       ❌ DO NOT DELETE ❌ Development Environment for WP Telegram Login. Versioned high to avoid auto update.
 * Version:           999.999.999
 * Author:            Manzoor Wani
 * Author URI:        https://manzoorwani.dev
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

require plugin_dir_path( __FILE__ ) . 'src/wptelegram-login.php';
