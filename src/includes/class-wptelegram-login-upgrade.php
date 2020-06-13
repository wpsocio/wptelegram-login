<?php
/**
 * Do the necessary db upgrade
 *
 * @link       https://t.me/manzoorwanijk
 * @since      x.y.z
 *
 * @package    WPTelegram_Login
 * @subpackage WPTelegram_Login/includes
 */

/**
 * Do the necessary db upgrade.
 *
 * Do the nececessary the incremental upgrade.
 *
 * @package    WPTelegram_Login
 * @subpackage WPTelegram_Login/includes
 * @author     Manzoor Wani <@manzoorwanijk>
 */
class WPTelegram_Login_Upgrade {

	/**
	 * The plugin class instance.
	 *
	 * @since    x.y.z
	 * @access   private
	 * @var      WPTelegram_Login $plugin The plugin class instance.
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since x.y.z
	 * @param WPTelegram_Login $plugin The plugin class instance.
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Do the necessary db upgrade, if needed
	 *
	 * @since    1.5.1
	 */
	public function do_upgrade() {

		$current_version = get_option( 'wptelegram_login_ver', '1.5.0' );

		$plugin_settings = WPTG_Login()->options()->get_data();
		$is_new_install  = empty( $plugin_settings );

		if ( ! version_compare( $current_version, $this->plugin->version(), '<' ) ) {
			return;
		}

		do_action( 'wptelegram_login_before_do_upgrade', $current_version );

		$version_upgrades = array();

		if ( ! $is_new_install ) {
			// the sequential upgrades
			// subsequent upgrade depends upon the previous one.
			$version_upgrades = array(
				'1.5.1', // first upgrade.
			);
		}

		// always.
		if ( ! in_array( $this->plugin->version(), $version_upgrades, true ) ) {
			$version_upgrades[] = $this->plugin->version();
		}

		foreach ( $version_upgrades as $target_version ) {

			if ( version_compare( $current_version, $target_version, '<' ) ) {

				$this->upgrade_to( $target_version );

				$current_version = $target_version;
			}
		}

		do_action( 'wptelegram_login_after_do_upgrade', $current_version );
	}

	/**
	 * Upgrade to a specific version
	 *
	 * @since 1.5.1
	 *
	 * @param string $version The plugin verion to upgrade to.
	 */
	private function upgrade_to( $version ) {

		// 2.0.1 becomes 201
		$_version = str_replace( '.', '', $version );

		$method = array( $this, "upgrade_to_{$_version}" );

		if ( is_callable( $method ) ) {

			call_user_func( $method );
		}

		update_option( 'wptelegram_login_ver', $version );
	}

	/**
	 * Upgrade to version 1.5.1
	 *
	 * @since    1.5.1
	 */
	private function upgrade_to_151() {

		$options = array(
			'disable_signup',
			'show_user_photo',
			'hide_on_default',
			'show_message_on_error',
		);

		// Convert checkboxes to boolean.
		foreach ( $options as $key ) {
			$value = WPTG_Login()->options()->get( $key );
			WPTG_Login()->options()->set( $key, 'on' === $value );
		}
	}
}
