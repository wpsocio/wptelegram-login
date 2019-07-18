<?php
/**
 * Plugin settings endpoint for WordPress REST API.
 *
 * @link       https://t.me/manzoorwanijk
 * @since      1.5.0
 *
 * @package    WPTelegram_Login
 * @subpackage WPTelegram_Login/includes
 */

/**
 * Class to handle the settings endpoint.
 *
 * @since 1.5.0
 *
 * @package    WPTelegram_Login
 * @subpackage WPTelegram_Login/includes
 * @author     Manzoor Wani <@manzoorwanijk>
 */
class WPTelegram_Login_Settings_Controller extends WPTelegram_Login_REST_Controller {

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
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'settings_permissions' ),
					'args'                => self::get_settings_params( 'view' ),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'update_settings' ),
					'permission_callback' => array( $this, 'settings_permissions' ),
					'args'                => self::get_settings_params( 'edit' ),
				),
			)
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
	 * @param WP_REST_Request $request WP REST API request.
	 */
	public function update_settings( WP_REST_Request $request ) {

		$settings = array();

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
		return array(
			'bot_token'             => array(
				'type'              => 'string',
				'required'          => ( 'edit' === $context ),
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => array( __CLASS__, 'validate_param' ),
			),
			'bot_username'          => array(
				'type'              => 'string',
				'required'          => ( 'edit' === $context ),
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => array( __CLASS__, 'validate_param' ),
			),
			'disable_signup'        => array(
				'type'              => 'boolean',
				'default'           => false,
				'validate_callback' => 'rest_validate_request_arg',
			),
			'user_role'             => array(
				'type'              => 'string',
				'default'           => get_option( 'default_role' ),
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'redirect_to'           => array(
				'type'              => 'string',
				'default'           => 'default',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
				'enum'              => array( 'default', 'homepage', 'current_page', 'custom_url' ),
			),
			'redirect_url'          => array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'avatar_meta_key'       => array(
				'type'              => 'string',
				'default'           => 'wptg_login_avatar',
				'sanitize_callback' => 'sanitize_key',
				'validate_callback' => array( __CLASS__, 'validate_param' ),
			),
			'button_style'          => array(
				'type'              => 'string',
				'default'           => 'large',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
				'enum'              => array( 'large', 'medium', 'small' ),
			),
			'show_user_photo'       => array(
				'type'              => 'boolean',
				'default'           => true,
				'validate_callback' => 'rest_validate_request_arg',
			),
			'corner_radius'         => array(
				'type'              => 'number',
				'maximum'           => 20,
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => array( __CLASS__, 'validate_param' ),
			),
			'show_if_user_is'       => array(
				'type'              => 'string',
				'default'           => 'logged_out',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'hide_on_default'       => array(
				'type'              => 'boolean',
				'default'           => false,
				'validate_callback' => 'rest_validate_request_arg',
			),
			'show_message_on_error' => array(
				'type'              => 'boolean',
				'default'           => false,
				'validate_callback' => 'rest_validate_request_arg',
			),
			'custom_error_message'  => array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
			),
		);
	}

	/**
	 * Update settings.
	 *
	 * @since 1.5.0
	 *
	 * @param mixed           $value   Value of the param.
	 * @param WP_REST_Request $request WP REST API request.
	 * @param string          $key     Param key.
	 */
	public static function validate_param( $value, WP_REST_Request $request, $key ) {
		switch ( $key ) {
			case 'bot_token':
				$pattern = '/\A\d{9}:[\w-]{35}\Z/';
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
