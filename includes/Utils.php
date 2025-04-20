<?php
/**
 * Utility methods.
 *
 * @link       https://wpsocio.com
 * @since      2.0.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 */

namespace WPTelegram\Login\includes;

use WPTelegram\Login\includes\restApi\SettingsController;

/**
 * Utility methods.
 *
 * Utility methods.
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 * @author     WP Socio
 */
class Utils extends \WPSocio\WPUtils\Helpers {

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

	/**
	 * Get the default settings.
	 *
	 * @return array
	 */
	public static function get_default_settings() {

		// Get the default values.
		$settings = SettingsController::get_settings_params();

		foreach ( $settings as $key => $args ) {
			$settings[ $key ] = isset( $args['default'] ) ? $args['default'] : '';
		}

		return $settings;
	}

	/**
	 * Get the user by Telegram ID.
	 *
	 * @since 1.10.3
	 *
	 * @param int|string $tg_user_id Telegram User ID.
	 *
	 * @return WP_User|false User object or false
	 */
	public static function get_user_by_telegram_id( $tg_user_id ) {
		$args = [
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_key'   => WPTELEGRAM_USER_ID_META_KEY,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'meta_value' => $tg_user_id,
			'number'     => 1,
		];

		$users = get_users( $args );

		$user = reset( $users );

		/**
		 * Filter the user found by its Telegram ID.
		 *
		 * @param WP_User|false $user       The user object or false.
		 * @param int|string    $tg_user_id Telegram User ID.
		 */
		return apply_filters( 'wptelegram_login_get_user_by_telegram_id', $user, $tg_user_id );
	}

	/**
	 * Get the hook details to use for intercepting the login.
	 *
	 * @since 1.11.10
	 *
	 * @return array Hook and priority.
	 */
	public static function get_intercept_details() {

		$hook_and_priority = [ 'wp_loaded', 20 ];

		/**
		 * Filter the hook and priority to use for intercepting the login request.
		 *
		 * - [Examples](./examples/intercept_request_on.md)
		 *
		 * @param array $hook_and_priority A tuple containing the hook name and priority.
		 */
		$details = apply_filters( 'wptelegram_login_intercept_request_on', $hook_and_priority );

		return [
			'hook'     => is_string( $details[0] ?? null ) ? $details[0] : $hook_and_priority[0],
			'priority' => is_int( $details[1] ?? null ) ? $details[1] : $hook_and_priority[1],
		];
	}
}
