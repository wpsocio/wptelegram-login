<?php
/**
 * Plugin settings endpoint for WordPress REST API.
 *
 * @link       https://wpsocio.com
 * @since      1.5.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 */

namespace WPTelegram\Login\includes\restApi;

use WPTelegram\Login\includes\Utils;
use WPSocio\WPUtils\Options;

/**
 * Class to handle the settings endpoint.
 *
 * @since 1.5.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 * @author     WP Socio
 */
class SettingsController extends RESTController {

	/**
	 * The plugin settings/options.
	 *
	 * @var Options
	 */
	protected $settings;

	/**
	 * Constructor
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		$this->rest_base = 'settings';
		$this->settings  = WPTG_Login()->options();
	}

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @since 1.5.0
	 */
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ $this, 'get_settings' ],
					'permission_callback' => [ __CLASS__, 'settings_permissions' ],
					'args'                => self::get_settings_params( 'view' ),
				],
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'update_settings' ],
					'permission_callback' => [ __CLASS__, 'settings_permissions' ],
					'args'                => self::get_settings_params( 'edit' ),
				],
			]
		);
	}

	/**
	 * Check request permissions.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public static function settings_permissions() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get the settings for REST API.
	 *
	 * @return array
	 */
	public static function get_rest_settings() {

		$settings = WPTG_Login()->options()->get_data();

		// If we have something saved.
		if ( ! empty( $settings ) ) {
			return $settings;
		}

		return Utils::get_default_settings();
	}

	/**
	 * Get settings via API.
	 *
	 * @since 1.5.0
	 */
	public function get_settings() {
		return rest_ensure_response( self::get_rest_settings() );
	}

	/**
	 * Update settings.
	 *
	 * @since 1.5.0
	 *
	 * @param \WP_REST_Request $request WP REST API request.
	 */
	public function update_settings( \WP_REST_Request $request ) {

		$settings = [];

		foreach ( self::get_settings_params() as $key => $args ) {
			$value = $request->get_param( $key );

			if ( null !== $value || isset( $args['default'] ) ) {

				$settings[ $key ] = null === $value ? $args['default'] : $value;
			}
		}

		WPTG_Login()->options()->set_data( $settings )->update_data();

		return rest_ensure_response( $settings );
	}

	/**
	 * Retrieves the query params for the settings.
	 *
	 * @since 1.5.0
	 *
	 * @param string $context The context for the values.
	 * @return array Query parameters for the settings.
	 */
	public static function get_settings_params( $context = 'edit' ) {
		$default_bot_token    = '';
		$default_bot_username = '';

		// Load Bot Token from WP Telegram if available.
		if ( defined( 'WPTELEGRAM_LOADED' ) && function_exists( 'WPTG' ) && self::settings_permissions() ) {
			// Avoid intelephense error for directly calling WPTG().
			$wptg_options = call_user_func( 'WPTG' )->options();

			$default_bot_token    = $wptg_options->get( 'bot_token', $default_bot_token );
			$default_bot_username = $wptg_options->get( 'bot_username', $default_bot_username );
		}

		return [
			'bot_token'             => [
				'type'              => 'string',
				'default'           => $default_bot_token,
				'required'          => ( 'edit' === $context ),
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => [ __CLASS__, 'validate_param' ],
			],
			'bot_username'          => [
				'type'              => 'string',
				'default'           => $default_bot_username,
				'required'          => ( 'edit' === $context ),
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => [ __CLASS__, 'validate_param' ],
			],
			'disable_signup'        => [
				'type'    => 'boolean',
				'default' => false,
			],
			'user_role'             => [
				'type'              => 'string',
				'default'           => get_option( 'default_role' ),
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
			],
			'redirect_to'           => [
				'type'              => 'string',
				'default'           => 'default',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
				'enum'              => [ 'default', 'homepage', 'current_page', 'custom_url' ],
			],
			'redirect_url'          => [
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			],
			'avatar_meta_key'       => [
				'type'              => 'string',
				'default'           => 'wptg_login_avatar',
				'sanitize_callback' => 'sanitize_key',
				'validate_callback' => [ __CLASS__, 'validate_param' ],
			],
			'random_email'          => [
				'type'    => 'boolean',
				'default' => false,
			],
			'button_style'          => [
				'type'              => 'string',
				'default'           => 'large',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
				'enum'              => [ 'large', 'medium', 'small' ],
			],
			'show_user_photo'       => [
				'type'    => 'boolean',
				'default' => true,
			],
			'corner_radius'         => [
				'type'              => 'number',
				'maximum'           => 20,
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => [ __CLASS__, 'validate_param' ],
			],
			'lang'                  => [
				'type'              => 'string',
				'default'           => '',
				'validate_callback' => 'rest_validate_request_arg',
				'sanitize_callback' => 'sanitize_text_field',
			],
			'show_if_user_is'       => [
				'type'              => 'string',
				'default'           => 'logged_out',
				'validate_callback' => 'rest_validate_request_arg',
				'sanitize_callback' => 'sanitize_text_field',
			],
			'hide_on_default'       => [
				'type'    => 'boolean',
				'default' => false,
			],
			'show_message_on_error' => [
				'type'    => 'boolean',
				'default' => false,
			],
			'custom_error_message'  => [
				'type'              => 'string',
				'validate_callback' => 'rest_validate_request_arg',
				'sanitize_callback' => 'sanitize_text_field',
			],
		];
	}

	/**
	 * Update settings.
	 *
	 * @since 1.5.0
	 *
	 * @param mixed            $value   Value of the param.
	 * @param \WP_REST_Request $request WP REST API request.
	 * @param string           $key     Param key.
	 */
	public static function validate_param( $value, \WP_REST_Request $request, $key ) {
		switch ( $key ) {
			case 'bot_token':
				$pattern = '/\A\d{9,11}:[\w-]{35}\Z/';
				break;
			case 'bot_username':
				$pattern = '/\A[a-z]\w{3,30}[^\W_]\Z/i';
				break;
			case 'avatar_meta_key':
				$pattern = '/\A[a-z0-9_]+\Z/i';
				break;
			case 'corner_radius':
				$pattern = '/\A[1-2]?[0-9]?\Z/i';
				break;
		}

		return (bool) preg_match( $pattern, $value );
	}
}
