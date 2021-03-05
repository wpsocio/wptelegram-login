<?php
/**
 * Utility methods.
 *
 * @link       https://t.me/manzoorwanijk
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
			array(
				get_stylesheet_directory(),
				get_template_directory(),
				ABSPATH . WPINC . '/theme-compat/',
			)
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

		$locale = array(
			'' => array(
				'domain' => $domain,
				'lang'   => is_admin() ? get_user_locale() : get_locale(),
			),
		);

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

		$current_uri = trim( esc_url_raw( add_query_arg( array() ) ), '/' );

		$home_path = trim( wp_parse_url( home_url(), PHP_URL_PATH ), '/' );

		if ( $home_path && strpos( $current_uri, $home_path ) === 0 ) {

			$current_uri = trim( substr( $current_uri, strlen( $home_path ) ), '/' );
		}

		return home_url( $current_uri );
	}
}
