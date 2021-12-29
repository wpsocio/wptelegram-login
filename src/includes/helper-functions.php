<?php
/**
 * Helper functions.
 *
 * @link       https://wpsocio.com
 * @since      1.0.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login/includes
 */

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
	function wptelegram_login( $args = [], $echo = true ) {
		$output = \WPTelegram\Login\shared\Shared::login_shortcode( $args );
		if ( $echo ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			return $output;
		}
	}
}
