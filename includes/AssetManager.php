<?php
/**
 * The assets manager of the plugin.
 *
 * @link       https://wpsocio.com
 * @since      1.0.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 */

namespace WPTelegram\Login\includes;

use WPTelegram\Login\includes\restApi\SettingsController;
use WPSocio\WPUtils\JsDependencies;

/**
 * The assets manager of the plugin.
 *
 * Loads the plugin assets.
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 * @author     WP Socio
 */
class AssetManager extends BaseClass {

	const ADMIN_SETTINGS_ENTRY = 'js/settings/index.ts';
	const BLOCKS_ENTRY         = 'js/blocks/index.ts';
	const WP_LOGIN_ENTRY       = 'js/wp-login/index.ts';
	const WEB_APP_LOGIN_ENTRY  = 'js/web-app-login/index.ts';

	const ASSET_ENTRIES = [
		'admin-settings' => [
			'entry' => self::ADMIN_SETTINGS_ENTRY,
		],
		'blocks'         => [
			'entry'      => self::BLOCKS_ENTRY,
			'style-deps' => [
				'wp-components',
			],
		],
		'wp-login'       => [
			'entry' => self::WP_LOGIN_ENTRY,
		],
		'web-app-login'  => [
			'entry'         => self::WEB_APP_LOGIN_ENTRY,
			'external-deps' => [
				self::WEB_APP_EXTERNAL_SCRIPT_HANDLE => 'https://telegram.org/js/telegram-web-app.js',
			],
		],
	];

	const WEB_APP_EXTERNAL_SCRIPT_HANDLE = 'wptelegram-login-web-app-script';

	const WPTELEGRAM_MENU_HANDLE = 'wptelegram-menu';

	/**
	 * Register the assets.
	 *
	 * @since    1.9.7
	 */
	public function register_assets() {

		$build_dir = $this->plugin()->dir( '/assets/build' );

		$js_dependencies = new JsDependencies( $build_dir );

		$assets = $this->plugin()->assets();

		foreach ( self::ASSET_ENTRIES as $name => $data ) {
			$entry = $data['entry'];

			// If we have external dependencies, register them first.
			if ( ! empty( $data['external-deps'] ) ) {
				foreach ( $data['external-deps'] as $handle => $src ) {
					wp_register_script(
						$handle,
						$src,
						[],
						$this->plugin()->version(),
						true
					);
				}
				$dependencies = array_merge( $js_dependencies->get( $entry ), array_keys( $data['external-deps'] ) );
			} else {
				$dependencies = $js_dependencies->get( $entry );
			}

			$assets->register(
				$entry,
				[
					'handle'              => $this->plugin()->name() . '-' . $name,
					'script-dependencies' => $dependencies,
					'style-dependencies'  => $data['style-deps'] ?? [],
					'script-args'         => $data['in-footer'] ?? true,
				]
			);
		}

		if ( ! defined( 'WPTELEGRAM_LOADED' ) ) {
			wp_register_style(
				self::WPTELEGRAM_MENU_HANDLE,
				$this->plugin()->url( sprintf( '/assets/static/css/admin-menu%s.css', wp_scripts_get_suffix() ) ),
				[],
				$this->plugin()->version(),
				'all'
			);
		}
	}

	/**
	 * Add inline script for a given entry.
	 *
	 * @param string $entry Entrypoint.
	 *
	 * @return void
	 */
	public function add_inline_script( string $entry ): void {
		$handle = $this->plugin()->assets()->get_entry_script_handle( $entry );

		if ( $handle ) {
			$data = $this->get_inline_script_data_str( $entry );

			wp_add_inline_script( $handle, $data, 'before' );
		}
	}

	/**
	 * Enqueue the assets for the admin area.
	 *
	 * @since    1.10.8
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_admin_assets( $hook_suffix ) {

		if ( ! defined( 'WPTELEGRAM_LOADED' ) ) {
			wp_enqueue_style( self::WPTELEGRAM_MENU_HANDLE );
		}

		$assets = $this->plugin()->assets();

		// Load only on settings page.
		if ( $this->is_settings_page( $hook_suffix ) ) {
			$assets->enqueue( self::ADMIN_SETTINGS_ENTRY );
			$this->add_inline_script( self::ADMIN_SETTINGS_ENTRY );
		}
	}

	/**
	 * Get the inline script data as a string.
	 *
	 * @param string $for The JS entry point for which the data is needed.
	 *
	 * @return string
	 */
	public function get_inline_script_data_str( string $for ): string {

		$data = $this->get_inline_script_data( $for );

		return $data ? sprintf( 'var %s = %s;', $this->plugin()->name(), wp_json_encode( $data ) ) : '';
	}

	/**
	 * Get the inline script data.
	 *
	 * @param string $for The JS entry point for which the data is needed.
	 *
	 * @return array
	 */
	public function get_inline_script_data( string $for ) {
		$shared_data = [
			'pluginInfo' => [
				'title'       => $this->plugin()->title(),
				'name'        => $this->plugin()->name(),
				'version'     => $this->plugin()->version(),
				'description' => __( 'With this plugin, you can let the users login to your website with their Telegram and make it simple for them to get connected.', 'wptelegram-login' ),
			],
			'api'        => [
				'admin_url'      => admin_url(),
				'nonce'          => wp_create_nonce( 'wptelegram-login' ),
				'use'            => 'BROWSER', // or may be 'SERVER'?
				'rest_namespace' => 'wptelegram-login/v1',
				'wp_rest_url'    => esc_url_raw( rest_url() ),
			],
			'assets'     => [
				'logoUrl'   => $this->plugin()->url( '/assets/static/icons/icon-128x128.png' ),
				'tgIconUrl' => $this->plugin()->url( '/assets/static/icons/tg-icon.svg' ),
			],
			'uiData'     => [
				'show_if_user_is'   => self::get_show_if_user_is_options(),
				'lang'              => self::get_language_options(),
				'wptelegram_active' => defined( 'WPTELEGRAM_LOADED' ),
			],
			'i18n'       => Utils::get_jed_locale_data( 'wptelegram-login' ),
		];

		$data = [];

		$settings = SettingsController::get_rest_settings();

		// Not to expose bot token to non-admins.
		if ( self::ADMIN_SETTINGS_ENTRY === $for && current_user_can( 'manage_options' ) ) {
			$data = $shared_data;

			$data['savedSettings'] = $settings;
			// Add UI data for settings page.
			$data['uiData']['user_role'] = self::user_role_options_cb();
		}

		if ( self::BLOCKS_ENTRY === $for ) {
			$data = $shared_data;

			$data['assets'] = array_merge(
				$data['assets'],
				[
					'loginImageUrl'  => $this->plugin()->url( '/assets/static/icons/telegram-login.svg' ),
					'loginAvatarUrl' => $this->plugin()->url( '/assets/static/icons/telegram-login-avatar.svg' ),
				]
			);

			$data['savedSettings'] = array_intersect_key(
				$settings,
				array_flip(
					[
						'button_style',
						'show_user_photo',
						'corner_radius',
						'show_if_user_is',
					]
				)
			);
		}

		if ( self::WEB_APP_LOGIN_ENTRY === $for ) {

			$query_params = $this->get_webapp_login_params();

			$redirect_to       = rawurlencode( $query_params['redirect_to'] );
			$confirm_login     = (bool) $query_params['confirm_login'];
			$is_user_logged_in = is_user_logged_in();
			$login_auth_url    = add_query_arg(
				array_filter(
					[
						'action'      => 'wptelegram_login',
						'source'      => 'WebAppData',
						'redirect_to' => $redirect_to,
					]
				),
				site_url()
			);

			$i18n = [
				'popup' => [
					'title'   => __( 'Login with Telegram', 'wptelegram-login' ),
					'message' => __( 'Do you want to login via Telegram for a better experience?', 'wptelegram-login' ),
					'buttons' => [
						[
							'id'   => 'login',
							'text' => __( 'Login', 'wptelegram-login' ),
							'type' => 'default',
						],
						[
							'id'   => 'cancel',
							'text' => __( 'Cancel', 'wptelegram-login' ),
							'type' => 'cancel',
						],
					],
				],
			];

			$data = compact( 'is_user_logged_in', 'login_auth_url', 'confirm_login', 'i18n' );

			/**
			 * Filters the data for the web app login.
			 *
			 * This can be used to customize the messages etc. for the web app login UI.
			 *
			 * @param array $data The data for the web app login.
			 */
			$data = apply_filters( 'wptelegram_login_web_app_login_data', $data );

			$data = [ 'web_app_data' => $data ];
		}

		/**
		 * Filters the inline script data for the settings page.
		 *
		 * @ignore Only used internally.
		 *
		 * @param array  $data The inline script data.
		 * @param string $for  The JS entry point for which the data is needed.
		 * @param object $this The plugin instance.
		 */
		return apply_filters( 'wptelegram_login_inline_script_data', $data, $for, $this->plugin() );
	}

	/**
	 * Callback for user_role option.
	 *
	 * @return array
	 */
	public static function user_role_options_cb() {
		// get_editable_roles may not exist for Block Widgets.
		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}
		$data = [];

		foreach ( get_editable_roles() as $role_name => $role_info ) {
			$data[] = [
				'value' => $role_name,
				'label' => translate_user_role( $role_info['name'] ),
			];
		}
		return $data;
	}

	/**
	 * Get options for show_if_user_is dropdown.
	 *
	 * @return array
	 */
	public static function get_show_if_user_is_options() {

		$data = [
			[
				'value' => '0',
				'label' => __( 'Any', 'wptelegram-login' ),
			],
			[
				'value' => 'logged_out',
				'label' => __( 'Logged out', 'wptelegram-login' ),
			],
			[
				'value' => 'logged_in',
				'label' => __( 'Logged in', 'wptelegram-login' ),
			],
		];

		return array_merge( $data, self::user_role_options_cb() );
	}

	/**
	 * Get options for lang dropdown.
	 *
	 * @return array
	 */
	public static function get_language_options() {

		require_once ABSPATH . 'wp-admin/includes/translation-install.php';

		$translations = wp_get_available_translations();

		$langs = [];

		foreach ( $translations as $translation ) {
			$iso = current( $translation['iso'] );

			if ( 'en' === $iso ) {
				continue;
			}

			$total_pieces = count( explode( ' ', $translation['english_name'] ) );

			$langs[ $iso ][ $total_pieces ] = $translation;
		}

		$data = [
			[
				'value' => 'en',
				'label' => 'English (en)',
			],
		];

		foreach ( $langs as $iso => $lang_list ) {
			ksort( $lang_list );

			$translation = array_shift( $lang_list );

			// Remove everything inside the parentheses.
			$name = preg_replace( '/\s+\(.+$/', '', $translation['english_name'] );

			$label = sprintf( '%s (%s)', $name, $iso );

			$data[] = [
				'value' => $iso,
				'label' => $label,
			];
		}

		usort(
			$data,
			function ( $a, $b ) {
				return strcmp( $a['label'], $b['label'] );
			}
		);

		array_unshift(
			$data,
			[
				'value' => '0',
				'label' => __(
					'Default',
					'wptelegram-login'
				),
			]
		);

		/**
		 * Filters the language options for the settings page.
		 *
		 * @param array $data         The language options.
		 * @param array $translations The available translations.
		 */
		return apply_filters( 'wptelegram_login_language_options', $data, $translations );
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

		$this->plugin()->assets()->enqueue( self::WP_LOGIN_ENTRY );
	}

	/**
	 * Get the query params for the webapp login.
	 *
	 * @return array
	 */
	private function get_webapp_login_params() {

		$defaults = [
			'action'        => '',
			'confirm_login' => '1',
			'redirect_to'   => '',
		];

		// Using $_SERVER['QUERY_STRING'] to avoid a bug in Telegram Mini Apps which pass HTML/URL encoded query string ¯\_(ツ)_/¯.

		$query_string = ! empty( $_SERVER['QUERY_STRING'] )
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- We are sanitizing the input below.
			? wp_unslash( $_SERVER['QUERY_STRING'] )
			: '';

		$query_string = html_entity_decode(
			str_replace( [ '&amp%3B', '&amp;' ], '&', $query_string )
		);

		$args = wp_parse_args( $query_string, $defaults );

		// Sanitize each value.
		return array_map( 'sanitize_text_field', $args );
	}

	/**
	 * Register the scripts for public facing pages
	 *
	 * @since    1.10.4
	 */
	public function enqueue_public_scripts() {

		$query_params = $this->get_webapp_login_params();

		$assets = $this->plugin()->assets();

		if ( 'wptelegram_login_webapp' === $query_params['action'] ) {

			// This should not be needed, but it doesn't seem to work without loading the dependency.
			wp_enqueue_script( self::WEB_APP_EXTERNAL_SCRIPT_HANDLE );
			$assets->enqueue( self::WEB_APP_LOGIN_ENTRY );

			$this->add_inline_script( self::WEB_APP_LOGIN_ENTRY );
		}
	}

	/**
	 * Check if the current page is the settings page.
	 *
	 * @since 1.5.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function is_settings_page( $hook_suffix ) {
		return ( false !== strpos( $hook_suffix, '_page_' . $this->plugin()->name() ) );
	}

	/**
	 * Enqueue assets for blocks
	 *
	 * @since    1.10.8
	 */
	public function enqueue_block_assets() {

		$is_block_editor = (bool) did_action( 'enqueue_block_editor_assets' );

		$this->plugin()->assets()->enqueue(
			self::BLOCKS_ENTRY,
			// We need JS only for the block editor.
			[ 'skip-script' => ! $is_block_editor ]
		);
		$this->add_inline_script( self::BLOCKS_ENTRY );
	}
}
