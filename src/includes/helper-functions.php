<?php
/**
 * Helper functions.
 *
 * @link       https://t.me/manzoorwanijk
 * @since      1.0.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login/includes
 */

/**
 * Wrapper function around cmb2_get_option
 *
 * @since  1.0.0
 * @param  string $key     Options array key.
 * @param  mixed  $default Optional default value.
 * @return mixed           Option value
 */
function wptelegram_login_get_option( $key = '', $default = false ) {
	if ( function_exists( 'cmb2_get_option' ) ) {
		// Use cmb2_get_option as it passes through some key filters.
		return cmb2_get_option( 'wptelegram_login', $key, $default );
	}
	// Fallback to get_option if CMB2 is not loaded yet.
	$opts = get_option( 'wptelegram_login', $default );
	$val  = $default;
	if ( 'all' === $key ) {
		$val = $opts;
	} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
		$val = $opts[ $key ];
	}
	return $val;
}

if ( ! function_exists( 'wptelegram_login_user_id' ) ) {
	/**
	 * Get the saved Telegram User ID for the given or current
	 *
	 * @since 1.0.0
	 *
	 * @param  integer $wp_user_id WP User ID.
	 *
	 * @return integer  Telegram User ID.
	 */
	function wptelegram_login_user_id( $wp_user_id = 0 ) {
		if ( ! absint( $wp_user_id ) ) {
			$wp_user_id = get_current_user_id();
		}
		return absint( get_user_meta( $wp_user_id, WPTELEGRAM_USER_ID_META_KEY, true ) );
	}
}

if ( ! function_exists( 'wptelegram_login' ) ) {
	/**
	 * Get or display the login button
	 *
	 * @since 1.0.0
	 *
	 * @param  array   $args Shortcode Params.
	 * @param  boolean $echo Whether to display or return.
	 *
	 * @return NULL|string        The html output
	 */
	function wptelegram_login( $args = array(), $echo = true ) {
		$output = \WPTelegram\Login\shared\Shared::login_shortcode( $args );
		if ( $echo ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'wp_get_current_url' ) ) {
	/**
	 * Get the current URL
	 *
	 * A fix for WordPress installed in subdirectory
	 *
	 * @source https://roots.io/routing-wp-requests/
	 *
	 * @since 1.3.5
	 *
	 * @return string        The current URL
	 */
	function wp_get_current_url() {

		$current_uri = trim( esc_url_raw( add_query_arg( array() ) ), '/' );

		$home_path = trim( wp_parse_url( home_url(), PHP_URL_PATH ), '/' );

		if ( $home_path && strpos( $current_uri, $home_path ) === 0 ) {

			$current_uri = trim( substr( $current_uri, strlen( $home_path ) ), '/' );
		}

		return home_url( $current_uri );
	}
}

if ( ! function_exists( 'wptelegram_get_jed_locale_data' ) ) {

	/**
	 * Returns Jed-formatted localization data.
	 *
	 * @source gutenberg_get_jed_locale_data()
	 *
	 * @since 1.5.0
	 *
	 * @param  string $domain Translation domain.
	 *
	 * @return array
	 */
	function wptelegram_get_jed_locale_data( $domain ) {
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
}
