<?php
/**
 * Utility methods.
 *
 * @link       https://manzoorwani.dev
 * @since      2.0.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 */

namespace WPTelegram\Login\includes;

/**
 * Utility methods.
 *
 * Utility methods.
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 * @author     Manzoor Wani <@manzoorwanijk>
 */
class Utils {

	/**
	 * Check whether the template path is valid.
	 *
	 * @since 1.9.4
	 * @param string $template The template path.
	 *
	 * @return bool
	 */
	public static function is_valid_template( $template ) {
		/**
		 * Only allow templates that are in the active theme directory,
		 * parent theme directory, or the /wp-includes/theme-compat/ directory
		 * (prevent directory traversal attacks)
		 */
		$valid_paths = array_map(
			'realpath',
			[
				get_stylesheet_directory(),
				get_template_directory(),
				ABSPATH . WPINC . '/theme-compat/',
			]
		);

		$path = realpath( $template );

		foreach ( $valid_paths as $valid_path ) {
			if ( preg_match( '#\A' . preg_quote( $valid_path, '#' ) . '#', $path ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns Jed-formatted localization data.
	 *
	 * @source gutenberg_get_jed_locale_data()
	 *
	 * @since 1.9.4
	 *
	 * @param  string $domain Translation domain.
	 *
	 * @return array
	 */
	public static function get_jed_locale_data( $domain ) {
		$translations = get_translations_for_domain( $domain );

		$locale = [
			'' => [
				'domain' => $domain,
				'lang'   => is_admin() ? get_user_locale() : get_locale(),
			],
		];

		if ( ! empty( $translations->headers['Plural-Forms'] ) ) {
			$locale['']['plural_forms'] = $translations->headers['Plural-Forms'];
		}

		foreach ( $translations->entries as $msgid => $entry ) {
			$locale[ $msgid ] = $entry->translations;
		}

		return $locale;
	}

	/**
	 * Get the current URL
	 *
	 * A fix for WordPress installed in subdirectory
	 *
	 * @source https://roots.io/routing-wp-requests/
	 *
	 * @since 1.9.4
	 *
	 * @return string        The current URL
	 */
	public static function wp_get_current_url() {

		$current_uri = trim( esc_url_raw( add_query_arg( [] ) ), '/' );

		$home_path = trim( wp_parse_url( home_url(), PHP_URL_PATH ), '/' );

		if ( $home_path && strpos( $current_uri, $home_path ) === 0 ) {

			$current_uri = trim( substr( $current_uri, strlen( $home_path ) ), '/' );
		}

		return home_url( $current_uri );
	}

	/**
	 * Update the menu structure to make WP Telegram the top level link.
	 */
	public static function update_menu_structure() {
		global $admin_page_hooks;

		if ( ! defined( 'WPTELEGRAM_LOADED' ) && empty( $admin_page_hooks['wptelegram'] ) ) {
			add_menu_page(
				__( 'WP Telegram', 'wptelegram-login' ),
				__( 'WP Telegram', 'wptelegram-login' ),
				'manage_options',
				'wptelegram',
				null,
				'',
				80
			);
			add_action( 'admin_menu', [ __CLASS__, 'remove_wptelegram_menu' ], 20 );
		}
	}

	/**
	 * Update the menu structure to remove WP Telegram top level link.
	 */
	public static function remove_wptelegram_menu() {
		global $submenu;

		if ( ! current_user_can( 'manage_options' ) || empty( $submenu['wptelegram'] ) ) {
			return;
		}

		$key = null;
		foreach ( $submenu['wptelegram'] as $submenu_key => $submenu_item ) {
			if ( 'wptelegram' === $submenu_item[2] ) {
				$key = $submenu_key;
				break;
			}
		}

		if ( null === $key ) {
			return;
		}

		unset( $submenu['wptelegram'][ $key ] );
	}
}
