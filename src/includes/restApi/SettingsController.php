<?php
/**
 * Plugin settings endpoint for WordPress REST API.
 *
 * @link       https://manzoorwani.dev
 * @since      1.5.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 */

namespace WPTelegram\Login\includes\restApi;

/**
 * Class to handle the settings endpoint.
 *
 * @since 1.5.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 * @author     Manzoor Wani <@manzoorwanijk>
 */
class SettingsController extends RESTController {

	/**
	 * The plugin settings/options.
	 *
	 * @var string
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
					'permission_callback' => [ $this, 'settings_permissions' ],
					'args'                => self::get_settings_params( 'view' ),
				],
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'update_settings' ],
					'permission_callback' => [ $this, 'settings_permissions' ],
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
	public function settings_permissions() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get the default settings.
	 *
	 * @return array
	 */
	public static function get_default_settings() {

		$settings = WPTG_Login()->options()->get_data();

		// If we have somethings saved.
		if ( ! empty( $settings ) ) {
			return $settings;
		}

		// Get the default values.
		$settings = self::get_settings_params();

		foreach ( $settings as $key => $args ) {
			$settings[ $key ] = isset( $args['default'] ) ? $args['default'] : '';
		}

		return $settings;
	}

	/**
	 * Get settings via API.
	 *
	 * @since 1.5.0
	 */
	public function get_settings() {
		return rest_ensure_response( self::get_default_settings() );
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
		return [
			'bot_token'             => [
				'type'              => 'string',
				'required'          => ( 'edit' === $context ),
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => [ __CLASS__, 'validate_param' ],
			],
			'bot_username'          => [
				'type'              => 'string',
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
