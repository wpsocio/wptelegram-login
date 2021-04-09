<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://manzoorwani.dev
 * @since      1.0.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 */

namespace WPTelegram\Login\includes;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 * @author     Manzoor Wani <@manzoorwanijk>
 */
class I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wptelegram-login',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
