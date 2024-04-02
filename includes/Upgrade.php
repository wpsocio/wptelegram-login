<?php
/**
 * Do the necessary db upgrade
 *
 * @link       https://wpsocio.com
 * @since      1.7.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 */

namespace WPTelegram\Login\includes;

/**
 * Do the necessary db upgrade.
 *
 * Do the nececessary the incremental upgrade.
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 * @author     WP Socio
 */
class Upgrade extends BaseClass {

	/**
	 * Do the necessary db upgrade, if needed
	 *
	 * @since    1.5.1
	 */
	public function do_upgrade() {

		$current_version = get_option( 'wptelegram_login_ver', '1.5.0' );

		if ( ! version_compare( $current_version, $this->plugin()->version(), '<' ) ) {
			return;
		}

		if ( ! defined( 'WPTELEGRAM_LOGIN_DOING_UPGRADE' ) ) {
			define( 'WPTELEGRAM_LOGIN_DOING_UPGRADE', true );
		}

		$plugin_settings = WPTG_Login()->options()->get_data();
		$is_new_install  = empty( $plugin_settings );

		/**
		 * Fires before the upgrade process.
		 *
		 * @ignore Nothing to see here.
		 */
		do_action( 'wptelegram_login_before_do_upgrade', $current_version );

		$version_upgrades = [];

		if ( ! $is_new_install ) {
			// the sequential upgrades
			// subsequent upgrade depends upon the previous one.
			$version_upgrades = [
				'1.5.1', // first upgrade.
				'1.7.0',
			];
		}

		// always.
		if ( ! in_array( $this->plugin()->version(), $version_upgrades, true ) ) {
			$version_upgrades[] = $this->plugin()->version();
		}

		foreach ( $version_upgrades as $target_version ) {

			if ( version_compare( $current_version, $target_version, '<' ) ) {

				$this->upgrade_to( $target_version, $is_new_install );

				$current_version = $target_version;
			}
		}
		/**
		 * Fires after the upgrade process.
		 *
		 * @ignore Nothing to see here.
		 */
		do_action( 'wptelegram_login_after_do_upgrade', $current_version );
	}

	/**
	 * Upgrade to a specific version
	 *
	 * @since 1.5.1
	 *
	 * @param string  $version        The plugin version to upgrade to.
	 * @param boolean $is_new_install Whether it's a fresh install of the plugin.
	 */
	private function upgrade_to( $version, $is_new_install ) {

		// 2.0.1 becomes 2_0_1
		$_version = str_replace( '.', '_', $version );

		$method = [ $this, "upgrade_to_{$_version}" ];

		// No upgrades for fresh installations.
		if ( ! $is_new_install && is_callable( $method ) ) {

			call_user_func( $method );
		}

		update_option( 'wptelegram_login_ver', $version );
	}

	/**
	 * Upgrade to version 1.5.1
	 *
	 * @since    1.5.1
	 */
	private function upgrade_to_1_5_1() {

		$options = [
			'disable_signup',
			'show_user_photo',
			'hide_on_default',
			'show_message_on_error',
		];

		// Convert checkboxes to boolean.
		foreach ( $options as $key ) {
			$value = WPTG_Login()->options()->get( $key );
			WPTG_Login()->options()->set( $key, 'on' === $value );
		}
	}

	/**
	 * Upgrade to version 1.7.0
	 * Changes telegram user id meta key to share between other plugins.
	 *
	 * @since    1.7.0
	 */
	private function upgrade_to_1_7_0() {
		$old_meta_key = $this->plugin()->name() . '_user_id';

		$args  = [
			'fields'       => 'ID',
			'meta_key'     => $old_meta_key, // phpcs:ignore
			'meta_compare' => 'EXISTS',
			'number'       => -1,
		];
		$users = get_users( $args );

		foreach ( $users as $id ) {
			// get the existing value.
			$meta_value = get_user_meta( $id, $old_meta_key, true );
			// use the new meta key to retain existing value.
			update_user_meta( $id, WPTELEGRAM_USER_ID_META_KEY, $meta_value );
			// housekeeping.
			delete_user_meta( $id, $old_meta_key );
		}
	}
}
