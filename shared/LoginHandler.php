<?php
/**
 * The login handling functionality of the plugin.
 *
 * @link       https://wpsocio.com
 * @since      1.0.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\public
 */

namespace WPTelegram\Login\shared;

use WPTelegram\Login\includes\BaseClass;
use WPTelegram\Login\includes\Utils;
use WP_User;
use Exception;

/**
 * The login handling functionality of the plugin.
 *
 * The login handling functionality of the plugin.
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\public
 * @author     WP Socio
 */
class LoginHandler extends BaseClass {

	/**
	 * Handle Telegram login on init
	 *
	 * @since    1.0.0
	 */
	public function telegram_login() {

		$bot_token = WPTG_Login()->options()->get( 'bot_token' );

		if ( ! $this->is_valid_login_request() || ! $bot_token ) {
			return;
		}

		/**
		 * Fires before the login process starts.
		 */
		do_action( 'wptelegram_login_init' );

		$input = wp_unslash( $_GET ); // phpcs:disable WordPress.Security.NonceVerification.Recommended

		// Remove any unwanted fields.
		$input = $this->cleanup_input( $input );

		try {
			$auth_data = $this->validate_auth_data( $input );

			/**
			 * Fires before the user data is saved after validation.
			 *
			 * @param array $auth_data The authenticated user data.
			 */
			do_action( 'wptelegram_login_pre_save_user_data', $auth_data );

			$wp_user_id = $this->save_telegram_user_data( $auth_data );

			/**
			 * Fires after the user data is authenticated and saved.
			 *
			 * @param int   $wp_user_id The WordPress user ID.
			 * @param array $auth_data  The authenticated user data.
			 */
			do_action( 'wptelegram_login_after_save_user_data', $wp_user_id, $auth_data );

		} catch ( Exception $e ) {
			// phpcs:ignore WordPress.Security.EscapeOutput
			wp_die( $e->getMessage(), esc_html__( 'Error:', 'wptelegram-login' ), [ 'back_link' => true ] );
		}

		$user = wp_get_current_user();

		if ( ! $user->exists() ) { // ! is user logged in

			/**
			 * Fires before the user is logged in after the data is authenticated and saved.
			 *
			 * @param int $wp_user_id The WordPress user ID.
			 */
			do_action( 'wptelegram_login_before_user_login', $wp_user_id );

			// Login the user.
			wp_clear_auth_cookie();
			$user = wp_set_current_user( $wp_user_id );
			wp_set_auth_cookie( $wp_user_id, true );

			/**
			 * Fires after the user is successfully logged in.
			 *
			 * - [Examples](./examples/after_user_login.md)
			 *
			 * @param int $wp_user_id The WordPress user ID.
			 */
			do_action( 'wptelegram_login_after_user_login', $wp_user_id );

			$user_login = $user->user_login;

			/**
			 * Fires after the user has successfully logged in.
			 *
			 * @since 1.3.4
			 *
			 * @ignore -- This action is documented in by WP Core.
			 *
			 * @param string  $user_login Username.
			 * @param WP_User $user       WP_User object of the logged-in user.
			 */
			do_action( 'wp_login', $user_login, $user );
			/**
			 * Fires after the user has successfully logged in.
			 *
			 * @since 1.3.4
			 *
			 * @param string  $user_login Username.
			 * @param WP_User $user       WP_User object of the logged-in user.
			 */
			do_action( 'wptelegram_login', $user_login, $user );
		}

		$random_email = WPTG_Login()->options()->get( 'random_email' );

		if ( $random_email ) {
			$this->may_be_generate_email( $user );
		}

		/**
		 * Fires before the user is redirected after the login.
		 *
		 * - [Examples](./examples/before_redirect.md)
		 *
		 * @param WP_User $user The logged in user.
		 */
		do_action( 'wptelegram_login_before_redirect', $user );

		$this->redirect( $user );
	}

	/**
	 * Check if the Telegram Login request is valid
	 *
	 * @since    1.0.0
	 *
	 * @return boolean
	 */
	public function is_valid_login_request() {

		if ( isset( $_GET['action'], $_GET['hash'], $_GET['auth_date'] ) && 'wptelegram_login' === $_GET['action'] ) {
			return true;
		}
		return false;
	}

	/**
	 * Filter the input by removing any unwanted fields
	 * Especially in case of the query type permalinks.
	 *
	 * @since    1.10.6
	 *
	 * @param array $input The data passed.
	 *
	 * @return array
	 */
	private function cleanup_input( $input ) {

		$validation_query_params = [
			// Common fields.
			'auth_date',
			'hash',
			/**
			 * Normal login fields.
			 *
			 * @link https://core.telegram.org/widgets/login#receiving-authorization-data
			 */
			'id',
			'first_name',
			'last_name',
			'username',
			'photo_url',
			/**
			 * WebAppInitData
			 *
			 * @link https://core.telegram.org/bots/webapps#webappinitdata
			 */
			'query_id',
			'user',
			'receiver',
			'chat',
			'chat_type',
			'start_param',
			'can_send_after',
			'chat_instance',
			// Misc.
			'source',
		];

		/**
		 * Filter the validation query parameters that the plugin uses.
		 *
		 * @param array $validation_query_params The validation query parameters.
		 * @param array $input                   The input data.
		 */
		$validation_query_params = apply_filters( 'wptelegram_login_validation_query_params', $validation_query_params, $input );

		$clean_input = array_intersect_key( $input, array_flip( $validation_query_params ) );

		/**
		 * Filter the cleaned input from the login request.
		 *
		 * @param array $clean_input The cleaned input.
		 * @param array $input       The input data.
		 */
		return apply_filters( 'wptelegram_login_clean_input', $clean_input, $input );
	}

	/**
	 * Fetch the auth data based on the input.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input_data The input data.
	 *
	 * @throws Exception The exception.
	 *
	 * @return array
	 */
	public function validate_auth_data( $input_data ) {

		// create a copy of the data.
		$auth_data = $input_data;

		$bot_token = WPTG_Login()->options()->get( 'bot_token' );

		$incoming_hash = sanitize_text_field( $auth_data['hash'] );
		$data_source   = ! empty( $auth_data['source'] ) ? sanitize_text_field( $auth_data['source'] ) : '';

		unset( $auth_data['hash'], $auth_data['source'] );

		$secret_key = self::get_secret_key( $data_source, $bot_token );

		$generated_hash = self::hash_auth_data( $auth_data, $secret_key );

		if ( ! hash_equals( $generated_hash, $incoming_hash ) ) {
			throw new Exception( esc_html__( 'Unauthorized! Data is NOT from Telegram', 'wptelegram-login' ) );
		}

		if ( ( time() - intval( $auth_data['auth_date'] ) ) > DAY_IN_SECONDS ) {
			throw new Exception( esc_html__( 'Invalid! The data is outdated', 'wptelegram-login' ) );
		}

		$auth_data = Utils::sanitize( $auth_data );

		if ( 'WebAppData' === $data_source ) {
			$auth_data = ! empty( $auth_data['user'] ) ? Utils::sanitize( json_decode( $auth_data['user'], true ) ) : [];
		}

		/**
		 * Filter the validated auth data.
		 *
		 * @param array $auth_data    The valid auth data.
		 * @param array $input_data   The input data.
		 */
		return apply_filters( 'wptelegram_login_valid_auth_data', $auth_data, $input_data );
	}

	/**
	 * Generate a hash for the incoming auth data.
	 *
	 * @since 1.10.0
	 *
	 * @param array  $auth_data The auth data received.
	 * @param string $secret_key The secret key to use for HMAC hashing.
	 *
	 * @return string
	 */
	public static function hash_auth_data( $auth_data, $secret_key ) {

		$data_check_arr = [];

		foreach ( $auth_data as $key => $value ) {
			$data_check_arr[] = $key . '=' . $value;
		}

		// Sort in alphabetical order.
		sort( $data_check_arr );

		$data_check_string = implode( "\n", $data_check_arr );

		$generated_hash = bin2hex( hash_hmac( 'sha256', $data_check_string, $secret_key, true ) );

		/**
		 * Filter the generated hash for the incoming auth data.
		 *
		 * @param string $generated_hash The generated hash.
		 * @param array  $auth_data      The auth data received.
		 * @param string $secret_key     The secret key.
		 */
		return apply_filters( 'wptelegram_login_hash_auth_data', $generated_hash, $auth_data, $secret_key );
	}

	/**
	 * Get the secret key for the data source.
	 *
	 * @since 1.10.0
	 *
	 * @param string $data_source The data source.
	 * @param string $bot_token The bot token.
	 *
	 * @return string
	 */
	public static function get_secret_key( $data_source, $bot_token ) {
		$secret_key = '';

		switch ( $data_source ) {
			case 'WebAppData':
				/**
				 * The data from web app uses HMAC-SHA-256 signature of the bot token
				 * with the constant string `WebAppData` used as a key.
				 *
				 * @link https://core.telegram.org/bots/webapps#validating-data-received-via-the-web-app
				 */
				$secret_key = hash_hmac( 'sha256', $bot_token, 'WebAppData', true );

				break;

			default:
				/**
				 * The data from normal login uses SHA256 hash of the bot token.
				 *
				 * @link https://core.telegram.org/widgets/login#checking-authorization
				 */
				$secret_key = hash( 'sha256', $bot_token, true );

				break;
		}

		/**
		 * Filter the secret key for the data source.
		 *
		 * @param string $secret_key  The secret key.
		 * @param string $data_source The data source.
		 * @param string $bot_token   The bot token.
		 */
		return apply_filters( 'wptelegram_login_get_secret_key', $secret_key, $data_source, $bot_token );
	}

	/**
	 * Generate a random email address for the user if needed.
	 *
	 * @since 1.6.0
	 *
	 * @param WP_User $user Current user.
	 */
	public function may_be_generate_email( $user ) {

		if ( $user->exists() && ! $user->user_email ) {
			$host = wp_parse_url( get_site_url(), PHP_URL_HOST );
			/**
			 * Filter the host for the random email.
			 *
			 * @param string $host The host for the random email.
			 * @param WP_User $user The current user.
			 */
			$host = apply_filters( 'wptelegram_login_random_email_host', $host, $user );

			$random_user = 'auto-generated';
			/**
			 * Filter the username for the random email.
			 *
			 * @param string $random_user The username for the random email.
			 * @param WP_User $user       The current user.
			 */
			$random_user = apply_filters( 'wptelegram_login_random_email_user', $random_user, $user );

			$random_email = $this->unique_email( $random_user, $host );

			/**
			 * Filter the randomly generated email.
			 *
			 * @param string $random_email The random email.
			 * @param WP_User $user        The current user.
			 * @param string  $random_user The username for the random email.
			 * @param string  $host        The host for the random email.
			 */
			$random_email = apply_filters( 'wptelegram_login_random_email', $random_email, $user, $random_user, $host );

			wp_update_user(
				[
					'ID'         => $user->ID,
					'user_email' => $random_email,
				]
			);
		}
	}

	/**
	 * Recursive function to generate a unique email.
	 *
	 * @since 1.6.0
	 *
	 * If the email already exists, will add a numerical suffix which will increase until a unique email is found.
	 *
	 * @param string $user Initial username for email.
	 * @param string $host Email host.
	 *
	 * @return string The unique email.
	 */
	public function unique_email( $user, $host ) {
		static $i;
		if ( is_null( $i ) ) {
			$i = 1;
		} else {
			++$i;
		}
		$email = sprintf( '%1$s@%2$s', $user, $host );
		if ( ! email_exists( $email ) ) {
			return $email;
		}
		$new_email = sprintf( '%1$s%2$s@%3$s', $user, $i, $host );
		if ( ! email_exists( $new_email ) ) {
			return $new_email;
		}
		return call_user_func( [ $this, __FUNCTION__ ], $user, $host );
	}

	/**
	 * Save/update user's data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data The user data received from Telegram.
	 * @throws Exception The exception.
	 */
	public function save_telegram_user_data( $data ) {

		if ( empty( $data['id'] ) || empty( $data['first_name'] ) ) {
			throw new Exception( esc_html__( 'Invalid! The data is incomplete', 'wptelegram-login' ) );
		}

		$data = array_map( 'htmlspecialchars', $data );

		// Check if the request is from a logged in user.
		$cur_user = wp_get_current_user();

		// Check if the user is signing in again.
		$ret_user = Utils::get_user_by_telegram_id( $data['id'] );

		$existing_user_id = null;

		if ( $cur_user->exists() ) { // Logged in user.

			// Signed in user and the Telegram user not same.
			if ( $ret_user instanceof WP_User && $cur_user->ID !== $ret_user->ID ) {
				throw new Exception( esc_html__( 'The Telegram User ID is already associated with another existing user. Please contact the admin', 'wptelegram-login' ) );
			}

			$existing_user_id = $cur_user->ID;

		} elseif ( $ret_user instanceof WP_User ) { // Existing logged out.

			$existing_user_id = $ret_user->ID;

		} else { // New user.

			// Whether to allow create new account.
			$disable_signup = WPTG_Login()->options()->get( 'disable_signup' );

			/**
			 * Filters whether to disable sign up via Telegram.
			 *
			 * It means that the user must first create an account and connect it to Telegram to be able to use Telegram Login.
			 *
			 * @param bool  $disable_signup Whether to disable sign up via Telegram.
			 * @param array $data           The user details.
			 */
			$disable_signup = (bool) apply_filters( 'wptelegram_login_disable_signup', $disable_signup, $data );

			if ( $disable_signup ) {

				throw new Exception( esc_html__( 'Sign up via Telegram is disabled. You must first create an account and connect it to Telegram to be able to use Telegram Login', 'wptelegram-login' ) );

			}
		}

		$always_update = true;
		/**
		 * Whether to always update the existing user data.
		 *
		 * Pass `false` if you do not want to update user profile for existing users.
		 *
		 * - [Examples](./examples/always_update_user_data.md)
		 *
		 * @param bool     $always_update    Whether to always update the user data.
		 * @param array    $data             The user details.
		 * @param int|NULL $existing_user_id Existing WP User ID.
		 */
		$always_update_user_data = apply_filters( 'wptelegram_login_always_update_user_data', $always_update, $data, $existing_user_id );

		if ( $existing_user_id && ! $always_update_user_data ) {
			return $existing_user_id;
		}

		return $this->save_user_data( $data, $existing_user_id );
	}

	/**
	 * Save or update the user data.
	 *
	 * @since 1.0.0
	 *
	 * @param  array    $data       The user details.
	 * @param  int|NULL $wp_user_id Existing WP User ID.
	 *
	 * @throws Exception The exception.
	 *
	 * @return int|WP_Error The newly created user's ID or a WP_Error object if the user could not be created
	 */
	public function save_user_data( $data, $wp_user_id = null ) {

		/**
		 * Filter the user data before saving the user in the database.
		 *
		 * @param array    $data       The user details.
		 * @param int|NULL $wp_user_id Existing WP User ID.
		 */
		$data = apply_filters( 'wptelegram_login_save_user_data', $data, $wp_user_id );

		// The data fields received.
		$id          = $data['id'];
		$first_name  = $data['first_name'];
		$last_name   = isset( $data['last_name'] ) ? $data['last_name'] : '';
		$tg_username = isset( $data['username'] ) ? $data['username'] : '';
		$photo_url   = isset( $data['photo_url'] ) ? $data['photo_url'] : '';
		$username    = $tg_username;

		if ( is_null( $wp_user_id ) ) { // New user.

			// If no username, use the sanitized first_name and id.
			if ( empty( $username ) ) {
				$username = sanitize_user( $first_name . $id, true );
			}

			$unique_username = $this->unique_username( $username );

			/**
			 * Filter the unique username before creating the user.
			 *
			 * @param string $unique_username The unique username.
			 */
			$user_login = apply_filters( 'wptelegram_login_unique_username', $unique_username );

			$user_pass = wp_generate_password();

			$role = WPTG_Login()->options()->get( 'user_role' );

			// Create the user without first and last name to avoid wp_insert_user() failing on multi-byte characters.
			$userdata = compact( 'user_pass', 'user_login', 'role' );

			/**
			 * Filter the user data before inserting the user into the database.
			 *
			 * @param array $userdata The user data.
			 */
			$userdata = apply_filters( 'wptelegram_login_insert_user_data', $userdata );

			$wp_user_id = wp_insert_user( $userdata );

			if ( is_wp_error( $wp_user_id ) ) {
				throw new Exception( esc_html__( 'Telegram sign in could not be completed.', 'wptelegram-login' ) . ' ' . esc_html( $wp_user_id->get_error_message() ) );
			}

			/**
			 * Fires after the user is successfully inserted into the database.
			 *
			 * @param int   $wp_user_id The WordPress user ID.
			 * @param array $userdata   The user data.
			 */
			do_action( 'wptelegram_login_after_insert_user', $wp_user_id, $userdata );

		}

		/* Update the user */

		$ID = $wp_user_id; // phpcs:ignore WordPress.NamingConventions.ValidVariableName -- Ignore  snake_case

		$userdata = compact( 'ID', 'first_name', 'last_name' );

		/**
		 * Filter the user data before updating the user in the database.
		 *
		 * - [Examples](./examples/update_user_data.md)
		 *
		 * @param array $userdata The user data.
		 */
		$userdata = apply_filters( 'wptelegram_login_update_user_data', $userdata );

		if ( ! empty( $userdata ) ) {

			// Ensure that ID is not removed.
			$userdata['ID'] = $wp_user_id;

			$wp_user_id = wp_update_user( $userdata );

			if ( is_wp_error( $wp_user_id ) ) {
				throw new Exception( esc_html__( 'Telegram sign in could not be completed.', 'wptelegram-login' ) . ' ' . esc_html( $wp_user_id->get_error_message() ) );
			}

			/**
			 * Fires after the user is successfully updated in the database.
			 *
			 * @param int   $wp_user_id The WordPress user ID.
			 * @param array $userdata   The user data.
			 */
			do_action( 'wptelegram_login_after_update_user', $wp_user_id, $userdata );
		}

		// Save the telegram user ID and username.
		update_user_meta( $wp_user_id, WPTELEGRAM_USER_ID_META_KEY, $id );
		update_user_meta( $wp_user_id, WPTELEGRAM_USERNAME_META_KEY, $tg_username );

		if ( ! empty( $photo_url ) ) {
			$meta_key = WPTG_Login()->options()->get( 'avatar_meta_key' );
			if ( ! empty( $meta_key ) ) {
				update_user_meta( $wp_user_id, $meta_key, esc_url_raw( $photo_url ) );
			}
		}

		/**
		 * Fires after the user meta is updated.
		 *
		 * @param int $wp_user_id The WordPress user ID.
		 */
		do_action( 'wptelegram_login_after_update_user_meta', $wp_user_id );

		return $wp_user_id;
	}

	/**
	 * Recursive function to generate a unique username.
	 *
	 * @since 1.0.0
	 *
	 * If the username already exists, will add a numerical suffix which will increase until a unique username is found.
	 *
	 * @param string $username The Telegram username.
	 *
	 * @return string The unique username.
	 */
	public function unique_username( $username ) {
		static $i;
		if ( is_null( $i ) ) {
			$i = 1;
		} else {
			++$i;
		}
		if ( ! username_exists( $username ) ) {
			return $username;
		}
		$new_username = sprintf( '%s%s', $username, $i );
		if ( ! username_exists( $new_username ) ) {
			return $new_username;
		}
		return call_user_func( [ $this, __FUNCTION__ ], $username );
	}

	/**
	 * Redirect the user to a proper location.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_User $user The logged in user.
	 */
	private function redirect( $user ) {
		$redirect_to = isset( $_REQUEST['redirect_to'] ) ? remove_query_arg( 'reauth', wp_unslash( $_REQUEST['redirect_to'] ) ) : ''; // phpcs:ignore

		/**
		 * Filter the redirect URL after login.
		 *
		 * @param string  $redirect_to The redirect URL.
		 * @param WP_User $user The logged in user.
		 */
		$redirect_to = apply_filters( 'wptelegram_login_user_redirect_to', $redirect_to, $user );

		if ( ( empty( $redirect_to ) || 'wp-admin/' === $redirect_to || admin_url() === $redirect_to ) ) {
			// If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
			if ( is_multisite() && ! get_active_blog_for_user( $user->ID ) && ! is_super_admin( $user->ID ) ) {

				$redirect_to = user_admin_url();

			} elseif ( is_multisite() && ! $user->has_cap( 'read' ) ) {

				$redirect_to = get_dashboard_url( $user->ID );

			} elseif ( ! $user->has_cap( 'edit_posts' ) ) {

				$redirect_to = $user->has_cap( 'read' ) ? admin_url( 'profile.php' ) : home_url();

			}
			wp_safe_redirect( $redirect_to );
			exit();
		}
		wp_safe_redirect( $redirect_to );
		exit();
	}
}
