<?php
/**
 * The assets manager of the plugin.
 *
 * @link       https://t.me/manzoorwanijk
 * @since      1.0.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 */

namespace WPTelegram\Login\includes;

/**
 * The assets manager of the plugin.
 *
 * Loads the plugin assets.
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 * @author     Manzoor Wani <@manzoorwanijk>
 */
class AssetManager extends BaseClass {

	const ADMIN_MAIN_JS_HANDLE = 'wptelegram-login--main';
	const BLOCKS_JS_HANDLE     = 'wptelegram-login--blocks';
	const WP_LOGIN_JS_HANDLE   = 'wptelegram-login--wp-login';

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.9.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_admin_styles( $hook_suffix ) {

		if ( ! defined( 'WPTELEGRAM_LOADED' ) ) {
			wp_enqueue_style(
				$this->plugin->name() . '-menu',
				$this->plugin->assets()->url( sprintf( '/css/admin-menu%s.css', wp_scripts_get_suffix() ) ),
				array(),
				$this->plugin->version(),
				'all'
			);
		}

		$entrypoint = self::ADMIN_MAIN_JS_HANDLE;

		// Load only on settings page.
		if ( $this->is_settings_page( $hook_suffix ) && $this->plugin->assets()->has_asset( $entrypoint, Assets::ASSET_EXT_CSS ) ) {
			wp_enqueue_style(
				$entrypoint,
				$this->plugin->assets()->get_asset_url( $entrypoint, Assets::ASSET_EXT_CSS ),
				array(),
				$this->plugin->assets()->get_asset_version( $entrypoint, Assets::ASSET_EXT_CSS ),
				'all'
			);
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.9.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_admin_scripts( $hook_suffix ) {
		// Load only on settings page.
		if ( $this->is_settings_page( $hook_suffix ) ) {
			$entrypoint = self::ADMIN_MAIN_JS_HANDLE;

			wp_enqueue_script(
				$entrypoint,
				$this->plugin->assets()->get_asset_url( $entrypoint ),
				$this->plugin->assets()->get_asset_dependencies( $entrypoint ),
				$this->plugin->assets()->get_asset_version( $entrypoint ),
				true
			);

			// Pass data to JS.
			$data = $this->get_dom_data();
			// Not to expose bot token to non-admins.
			if ( current_user_can( 'manage_options' ) ) {
				$data['savedSettings'] = \WPTelegram\Login\includes\restApi\SettingsController::get_default_settings();
			}
			$data['uiData']['user_role'] = self::user_role_options_cb();

			wp_add_inline_script(
				$entrypoint,
				sprintf( 'var wptelegram_login = %s;', json_encode( $data ) ), // phpcs:ignore WordPress.WP.AlternativeFunctions
				'before'
			);
		}
	}

	/**
	 * Get the common DOM data.
	 *
	 * @return array
	 */
	private function get_dom_data() {
		$data = array(
			'pluginInfo' => array(
				'title'       => $this->plugin->title(),
				'name'        => $this->plugin->name(),
				'version'     => $this->plugin->version(),
				'description' => __( 'With this plugin, you can let the users login to your website with their Telegram and make it simple for them to get connected.', 'wptelegram-login' ),
			),
			'api'        => array(
				'admin_url'      => admin_url(),
				'nonce'          => wp_create_nonce( 'wptelegram-login' ),
				'use'            => 'BROWSER', // or may be 'SERVER'?
				'rest_namespace' => 'wptelegram-login/v1',
				'wp_rest_url'    => esc_url_raw( rest_url() ),
			),
			'assets'     => array(
				'logoUrl'   => $this->plugin->assets()->url( '/icons/icon-128x128.png' ),
				'tgIconUrl' => $this->plugin->assets()->url( '/icons/tg-icon.svg' ),
			),
			'uiData'     => array(
				'show_if_user_is' => self::get_show_if_user_is_options(),
			),
			'i18n'       => Utils::get_jed_locale_data( 'wptelegram-login' ),
		);

		return $data;
	}

	/**
	 * Callback for user_role option.
	 *
	 * @return array
	 */
	public static function user_role_options_cb() {

		$data = array();

		foreach ( get_editable_roles() as $role_name => $role_info ) {
			$data[] = array(
				'value' => $role_name,
				'label' => translate_user_role( $role_info['name'] ),
			);
		}
		return $data;
	}

	/**
	 * Get options for show_if_user_is dropdown.
	 *
	 * @return array
	 */
	public static function get_show_if_user_is_options() {

		$data = array(
			array(
				'value' => '0',
				'label' => __( 'Any', 'wptelegram-login' ),
			),
			array(
				'value' => 'logged_out',
				'label' => __( 'Logged out', 'wptelegram-login' ),
			),
			array(
				'value' => 'logged_in',
				'label' => __( 'Logged in', 'wptelegram-login' ),
			),
		);

		return array_merge( $data, self::user_role_options_cb() );
	}

	/**
	 * Register the scripts for the login page
	 *
	 * @since    1.9.0
	 */
	public function login_enqueue_scripts() {

		$hide_on_default = WPTG_Login()->options()->get( 'hide_on_default' );

		if ( $hide_on_default ) {
			return;
		}

		$entrypoint = self::WP_LOGIN_JS_HANDLE;

		wp_enqueue_script(
			$entrypoint,
			$this->plugin->assets()->get_asset_url( $entrypoint ),
			$this->plugin->assets()->get_asset_dependencies( $entrypoint ),
			$this->plugin->assets()->get_asset_version( $entrypoint ),
			true
		);

		// don't load styles for dev env.
		if ( defined( 'WP_PLUGINS_DEV_LOADED' ) ) {
			return;
		}

		if ( $this->plugin->assets()->has_asset( $entrypoint, Assets::ASSET_EXT_CSS ) ) {
			wp_enqueue_style(
				$entrypoint,
				$this->plugin->assets()->get_asset_url( $entrypoint, Assets::ASSET_EXT_CSS ),
				array(),
				$this->plugin->assets()->get_asset_version( $entrypoint, Assets::ASSET_EXT_CSS ),
				'all'
			);
		}
	}

	/**
	 * Enqueue assets for the Gutenberg block
	 *
	 * @since 1.5.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function is_settings_page( $hook_suffix ) {
		return ( false !== strpos( $hook_suffix, '_page_' . $this->plugin->name() ) );
	}

	/**
	 * Enqueue assets for the Gutenberg block
	 *
	 * @since    1.4.1
	 */
	public function enqueue_block_editor_assets() {
		$entrypoint = self::BLOCKS_JS_HANDLE;

		wp_enqueue_script(
			$entrypoint,
			$this->plugin->assets()->get_asset_url( $entrypoint ),
			$this->plugin->assets()->get_asset_dependencies( $entrypoint ),
			$this->plugin->assets()->get_asset_version( $entrypoint ),
			true
		);

		$data = $this->get_dom_data();

		$data['assets'] = array_merge(
			$data['assets'],
			array(
				'loginImageUrl'  => $this->plugin->assets()->url( '/icons/telegram-login.svg' ),
				'loginAvatarUrl' => $this->plugin->assets()->url( '/icons/telegram-login-avatar.svg' ),
			)
		);

		wp_add_inline_script(
			$entrypoint,
			sprintf( 'var wptelegram_login = %s;', json_encode( $data ) ), // phpcs:ignore WordPress.WP.AlternativeFunctions
			'before'
		);

		// don't load styles for dev env.
		if ( defined( 'WP_PLUGINS_DEV_LOADED' ) ) {
			return;
		}

		if ( $this->plugin->assets()->has_asset( $entrypoint, Assets::ASSET_EXT_CSS ) ) {
			wp_enqueue_style(
				$entrypoint,
				$this->plugin->assets()->get_asset_url( $entrypoint, Assets::ASSET_EXT_CSS ),
				array(),
				$this->plugin->assets()->get_asset_version( $entrypoint, Assets::ASSET_EXT_CSS ),
				'all'
			);
		}
	}
}
