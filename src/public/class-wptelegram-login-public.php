<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://t.me/manzoorwanijk
 * @since      1.0.0
 *
 * @package    WPTelegram_Login
 * @subpackage WPTelegram_Login/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    WPTelegram_Login
 * @subpackage WPTelegram_Login/public
 * @author     Manzoor Wani <@manzoorwanijk>
 */
class WPTelegram_Login_Public {

	/**
	 * The plugin class instance.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      WPTelegram_Login $plugin The plugin class instance.
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param WPTelegram_Login $plugin The plugin class instance.
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Register the scripts for the login page
	 *
	 * @since    1.0.0
	 */
	public function login_enqueue_scripts() {

		$hide_on_default = WPTG_Login()->options()->get( 'hide_on_default' );

		if ( $hide_on_default ) {
			return;
		}

		if ( is_rtl() ) {
			wp_enqueue_style( $this->plugin->name() . '-login', $this->plugin->url( '/public/css/wptelegram-login-public-login-rtl' ) . $this->plugin->suffix() . '.css', array(), $this->plugin->version() );
		} else {
			wp_enqueue_style( $this->plugin->name() . '-login', $this->plugin->url( '/public/css/wptelegram-login-public-login' ) . $this->plugin->suffix() . '.css', array(), $this->plugin->version() );
		}

		wp_enqueue_script( $this->plugin->name() . '-login', $this->plugin->url( '/public/js/wptelegram-login-public-login' ) . $this->plugin->suffix() . '.js', array( 'jquery' ), $this->plugin->version(), false );
	}

	/**
	 * Handle Telegram login on init
	 *
	 * @since    1.0.0
	 */
	public function telegram_login() {

		$bot_token    = WPTG_Login()->options()->get( 'bot_token' );
		$random_email = WPTG_Login()->options()->get( 'random_email' );

		if ( ! $this->is_valid_login_request() || ! $bot_token ) {
			return;
		}

		do_action( 'wptelegram_login_init' );

		$input = $_GET; // phpcs:disable WordPress.Security.NonceVerification.Recommended

		// Remove any unwanted fields.
		$input = $this->filter_input_fields( $input );

		try {
			$auth_data = $this->get_authorization_data( $input );

			do_action( 'wptelegram_login_pre_save_user_data', $auth_data );

			$wp_user_id = $this->save_telegram_user_data( $auth_data );

			do_action( 'wptelegram_login_after_save_user_data', $wp_user_id, $auth_data );

		} catch ( Exception $e ) {
			// phpcs:ignore WordPress.Security.EscapeOutput
			wp_die( $e->getMessage(), __( 'Error:', 'wptelegram-login' ), array( 'back_link' => true ) );
		}

		$user = wp_get_current_user();

		if ( ! $user->exists() ) { // ! is user logged in

			do_action( 'wptelegram_login_before_user_login', $wp_user_id );

			// Login the user.
			wp_clear_auth_cookie();
			$user = wp_set_current_user( $wp_user_id );
			wp_set_auth_cookie( $wp_user_id, true );

			do_action( 'wptelegram_login_after_user_login', $wp_user_id );

			/**
			 * Fires after the user has successfully logged in.
			 *
			 * @since 1.3.4
			 *
			 * @param string  $user_login Username.
			 * @param WP_User $user       WP_User object of the logged-in user.
			 */
			do_action( 'wp_login', $user->user_login, $user );
			do_action( 'wptelegram_login', $user->user_login, $user );
		}

		if ( $random_email ) {
			$this->may_be_generate_email( $user );
		}

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
	 * @since    1.0.0
	 *
	 * @param array $input The data passed.
	 *
	 * @return array
	 */
	public function filter_input_fields( $input ) {

		$desired_fields = array(
			'id'         => '',
			'first_name' => '',
			'last_name'  => '',
			'username'   => '',
			'photo_url'  => '',
			'auth_date'  => '',
			'hash'       => '',
		);

		return array_intersect_key( $input, $desired_fields );
	}

	/**
	 * Fetch the auth data based on the input.
	 *
	 * @since 1.0.0
	 *
	 * @param array $auth_data The input data.
	 *
	 * @throws Exception The exception.
	 *
	 * @return array
	 */
	public function get_authorization_data( $auth_data ) {

		$bot_token = WPTG_Login()->options()->get( 'bot_token' );

		$check_hash = $auth_data['hash'];
		unset( $auth_data['hash'] );

		$data_check_arr = array();
		foreach ( $auth_data as $key => $value ) {
			$data_check_arr[] = $key . '=' . $value;
		}
		// Sort in alphabetical order.
		sort( $data_check_arr );

		$data_check_string = implode( "\n", $data_check_arr );
		$secret_key        = hash( 'sha256', $bot_token, true );
		$hash              = hash_hmac( 'sha256', $data_check_string, $secret_key );

		if ( strcmp( $hash, $check_hash ) !== 0 ) {
			throw new Exception( __( 'Unauthorized! Data is NOT from Telegram', 'wptelegram-login' ) );
		}

		if ( ( time() - $auth_data['auth_date'] ) > 86400 ) {
			throw new Exception( __( 'Invalid! The data is outdated', 'wptelegram-login' ) );
		}
		return $auth_data;
	}

	/**
	 * Generate a random email address for the user if needed.
	 *
	 * @since x.y.z
	 *
	 * @param WP_User $user Current user.
	 */
	public function may_be_generate_email( $user ) {

		if ( $user->exists() && ! $user->user_email ) {
			$host = wp_parse_url( get_site_url(), PHP_URL_HOST );
			$host = apply_filters( 'wptelegram_login_random_email_host', $host, $user );

			$random_user = apply_filters( 'wptelegram_login_random_email_user', 'auto-generated', $user );

			$random_email = $this->unique_email( $random_user, $host );
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
	 * @since 1.0.0
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
			$i++;
		}
		$email = sprintf( '%1$s@%2$s', $user, $host );
		if ( ! email_exists( $email ) ) {
			return $email;
		}
		$new_email = sprintf( '%1$s%2$s@%3$s', $user, $i, $host );
		if ( ! email_exists( $new_email ) ) {
			return $new_email;
		}
		return call_user_func( array( $this, __FUNCTION__ ), $email, $host );
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

		$data = array_map( 'htmlspecialchars', $data );

		// Check if the request is from a logged in user.
		$cur_user = wp_get_current_user();

		// Check if the user is signing in again.
		$ret_user = $this->is_returning_user( $data['id'] );

		if ( $cur_user->exists() ) { // Logged in user.

			// Signed in user and the Telegram user not same.
			if ( $ret_user instanceof WP_User && $cur_user->ID !== $ret_user->ID ) {
				throw new Exception( __( 'The Telegram User ID is already associated with another existing user. Please contact the admin', 'wptelegram-login' ) );
			}

			$wp_user_id = $this->save_user_data( $data, $cur_user->ID );

		} elseif ( $ret_user instanceof WP_User ) { // Existing logged out.

			$wp_user_id = $this->save_user_data( $data, $ret_user->ID );

		} else { // New user.

			// Whether to allow create new account.
			$disable_signup = WPTG_Login()->options()->get( 'disable_signup' );

			$disable_signup = (bool) apply_filters( 'wptelegram_login_disable_signup', $disable_signup, $data );

			if ( $disable_signup ) {

				throw new Exception( __( 'Sign up via Telegram is disabled. You must first create an account and connect it to Telegram to be able to use Telegram Login', 'wptelegram-login' ) );

			}

			$wp_user_id = $this->save_user_data( $data );
		}
		return $wp_user_id;
	}

	/**
	 * Whether the user is a returning user.
	 *
	 * @since 1.0.0
	 *
	 * @param int $tg_user_id Telegram User ID.
	 *
	 * @return boolean|WP_User User object or false
	 */
	public function is_returning_user( $tg_user_id ) {
		$args  = array(
			'meta_key'   => "{$this->plugin->name()}_user_id", // phpcs:ignore
			'meta_value' => $tg_user_id, // phpcs:ignore
			'number'     => 1,
		);
		$users = get_users( $args );
		if ( ! empty( $users ) ) {
			return reset( $users );
		}
		return false;
	}

	/**
	 * Save or update the user data.
	 *
	 * @since 1.0.0
	 *
	 * @param  array    $data          The user details.
	 * @param  int|NULL $ex_wp_user_id Existing WP User ID.
	 *
	 * @throws Exception The exception.
	 *
	 * @return int|WP_Error The newly created user's ID or a WP_Error object if the user could not be created
	 */
	public function save_user_data( $data, $ex_wp_user_id = null ) {

		$data = apply_filters( 'wptelegram_login_save_user_data', $data, $ex_wp_user_id );

		// The data fields received.
		$id         = $data['id'];
		$first_name = $data['first_name'];
		$last_name  = isset( $data['last_name'] ) ? $data['last_name'] : '';
		$username   = isset( $data['username'] ) ? $data['username'] : '';
		$photo_url  = isset( $data['photo_url'] ) ? $data['photo_url'] : '';

		if ( is_null( $ex_wp_user_id ) ) { // New user.

			// If no username, use the sanitized first_name.
			if ( empty( $username ) ) {
				$username = sanitize_user( $first_name, true );
			}

			$unique_username = $this->unique_username( $username );

			$user_login = apply_filters( 'wptelegram_login_unique_username', $unique_username );

			$user_pass = wp_generate_password();

			$role = WPTG_Login()->options()->get( 'user_role' );

			$userdata = compact( 'user_pass', 'user_login', 'first_name', 'last_name', 'role' );

			$userdata = apply_filters( 'wptelegram_login_insert_user_data', $userdata );

			$wp_user_id = wp_insert_user( $userdata );

			do_action( 'wptelegram_login_after_insert_user', $wp_user_id, $userdata );

		} else { // Update.

			$ID = $ex_wp_user_id; // phpcs:ignore WordPress.NamingConventions.ValidVariableName -- Ignore  snake_case

			$userdata = compact( 'ID', 'first_name', 'last_name' );

			$userdata = apply_filters( 'wptelegram_login_update_user_data', $userdata );

			$wp_user_id = wp_update_user( $userdata );

			do_action( 'wptelegram_login_after_update_user', $wp_user_id, $userdata );
		}

		if ( is_wp_error( $wp_user_id ) ) {
			throw new Exception( __( 'Telegram sign in could not be completed.', 'wptelegram-login' ) . ' ' . $wp_user_id->get_error_message() );
		}

		// Save the telegram user ID.
		update_user_meta( $wp_user_id, "{$this->plugin->name()}_user_id", $id );

		if ( ! empty( $photo_url ) ) {
			$meta_key = WPTG_Login()->options()->get( 'avatar_meta_key' );
			if ( ! empty( $meta_key ) ) {
				update_user_meta( $wp_user_id, $meta_key, esc_url_raw( $photo_url ) );
			}
		}

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
			$i++;
		}
		if ( ! username_exists( $username ) ) {
			return $username;
		}
		$new_username = sprintf( '%s%s', $username, $i );
		if ( ! username_exists( $new_username ) ) {
			return $new_username;
		}
		return call_user_func( array( $this, __FUNCTION__ ), $username );
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

		// Apply default WP filter.
		$redirect_to = apply_filters( 'login_redirect', $redirect_to, $redirect_to, $user );

		// Apply plugin specific filter.
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

	/**
	 * Render the login button
	 * on WP login and register page
	 *
	 * @since 1.0.0
	 */
	public function add_telegram_login_button() {
		$hide_on_default = WPTG_Login()->options()->get( 'hide_on_default' );
		$show_if_user_is = WPTG_Login()->options()->get( 'show_if_user_is' );
		if ( $hide_on_default || ! self::is_to_be_displayed( $show_if_user_is ) ) {
			return;
		}
		?>
		<div id="wptelegram-login-wrap">
			<div class="wptelegram-login-or">
				<span><?php esc_html_e( 'Or', 'wptelegram-login' ); ?></span>
			</div>
			<?php echo self::login_shortcode(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<?php
	}

	/**
	 * Render the login block.
	 *
	 * @since 1.5.0
	 *
	 * @param string $block_content The block HTML.
	 * @param array  $block         The block data.
	 * @return string The HTML.
	 */
	public function render_login_block( $block_content, $block ) {

		if ( 'wptelegram/login' === $block['blockName'] ) {

			$block_content = preg_replace( '/<img[^>]+?>(.*?<img[^>]+?>)?/i', self::login_shortcode( $block['attrs'] ), $block_content );
		}

		return $block_content;
	}

	/**
	 * Registers shortcode to display the login
	 *
	 * @since    1.0.0
	 *
	 * @param array $atts shortcode params.
	 */
	public static function login_shortcode( $atts = array() ) {

		$defaults = array(
			'button_style'    => 'large',
			'show_user_photo' => '1',
			'corner_radius'   => '',
			'show_if_user_is' => 'logged_out',
			'bot_username'    => '',
		);

		// Use global options.
		foreach ( $defaults as $key => $default ) {
			$defaults[ $key ] = WPTG_Login()->options()->get( $key, $default );
		}

		$args = shortcode_atts( $defaults, $atts, 'wptelegram-login' );

		$args = array_map( 'sanitize_text_field', $args );

		if ( ! self::is_to_be_displayed( $args['show_if_user_is'] ) ) {
			return;
		}

		// Default.
		$redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : home_url(); // phpcs:ignore

		switch ( WPTG_Login()->options()->get( 'redirect_to' ) ) {
			case 'homepage':
				$redirect_to = home_url();
				break;

			case 'current_page':
				global $pagenow;
				// Prevent redirect to login page.
				if ( 'wp-login.php' !== $pagenow ) {
					$redirect_to = wp_get_current_url();
				}
				break;

			case 'custom_url':
				// Prevent redirect to login page.
				if ( filter_var( WPTG_Login()->options()->get( 'redirect_url' ), FILTER_VALIDATE_URL ) ) {
					$redirect_to = WPTG_Login()->options()->get( 'redirect_url' );
				}
				break;
		}

		// Can be used to fix the wrong URL in case
		// the website is in subdirectory the URL is invalid.
		$redirect_to = apply_filters( 'wptelegram_login_redirect_to', $redirect_to );

		$button_style = $args['button_style'];

		$show_user_photo = (bool) $args['show_user_photo'];

		$corner_radius = $args['corner_radius'];

		$bot_username = $args['bot_username'];

		$args = array(
			'action'      => 'wptelegram_login',
			'redirect_to' => urlencode_deep( $redirect_to ),
		);

		// The actual URL to be passed to Telegram as call back.
		$callback_url = add_query_arg( $args, home_url() );

		// can be used to fix the wrong URL in case
		// the website is in subdirectory the URL is invalid.
		$callback_url = apply_filters( 'wptelegram_login_telegram_callback_url', $callback_url );

		$login_options = compact(
			'button_style',
			'show_user_photo',
			'corner_radius',
			'bot_username',
			'callback_url'
		);

		set_query_var( 'login_options', $login_options );

		ob_start();
		$overridden_template = locate_template( 'wptelegram-login/login-view.php' );
		if ( $overridden_template ) {
			/**
			 * The locate_template() returns path to file.
			 * if either the child theme or the parent theme have overridden the template.
			 */
			if ( self::is_valid_template( $overridden_template ) ) {
				load_template( $overridden_template );
			}
		} else {
			/*
			 * If neither the child nor parent theme have overridden the template,
			 * we load the template from the 'partials' sub-directory of the directory this file is in.
			 */
			load_template( dirname( __FILE__ ) . '/partials/login-view.php' );
		}
		$html = ob_get_contents();
		ob_get_clean();
		return $html;
	}

	/**
	 * Get the Telegram data of a user if exists.
	 *
	 * @since    1.0.0
	 *
	 * @param  string $show_if_user_is When to show the button.
	 * @return boolean
	 */
	public static function is_to_be_displayed( $show_if_user_is = 'logged_out' ) {

		$bot_token = WPTG_Login()->options()->get( 'bot_token' );

		// If settings not saved.
		if ( empty( $bot_token ) ) {
			return false;
		}

		/**
		 * Filters when to show the login button
		 *
		 * Possible values:
		 * "logged_out", "logged_in", "author", "subscriber" etc.
		 *
		 * You can also pass a user role e.g "editor" or a comma separated list or an array of roles
		 * to display the button for specific user roles
		 *
		 * Passing an empty value will display the button
		 * for both logged in and logged out users
		 *
		 * @since 1.0.0
		 */
		$show_if_user_is = apply_filters( 'wptelegram_login_show_if_user_is', $show_if_user_is );

		$user = wp_get_current_user();

		$is_logged_in = $user->exists();

		// Using the different convention just to make the things meaningful :).
		if ( 'logged_in' === $show_if_user_is ) {
			if ( $is_logged_in ) {
				return true;
			}
			return false;
		}

		if ( 'logged_out' === $show_if_user_is ) {
			if ( $is_logged_in ) {
				return false;
			}
			return true;
		}

		if ( ! empty( $show_if_user_is ) ) {
			// now since a non empty value
			// it won't be displayed until something magical happens.
			$display = false;

			if ( ! is_array( $show_if_user_is ) ) {
				$show_if_user_is = explode( ',', $show_if_user_is );
			}

			// Remove any unwanted spaces.
			$show_if_user_is = array_map( 'trim', $show_if_user_is );

			// Check if user has one of the roles.
			foreach ( $show_if_user_is as $role ) {
				if ( in_array( $role, (array) $user->roles, true ) ) {
					$display = true;
					break;
				}
			}
			if ( ! $display ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Check whether the template path is valid
	 *
	 * @since  1.0.0
	 * @param  string $template The template path.
	 * @return bool
	 */
	private static function is_valid_template( $template ) {
		/**
		 * Only allow templates that are in the active theme directory,
		 * parent theme directory, or the /wp-includes/theme-compat/ directory
		 * (prevent directory traversal attacks)
		 */
		$valid_paths = array_map(
			'realpath',
			array(
				get_stylesheet_directory(),
				get_template_directory(),
				ABSPATH . WPINC . '/theme-compat/',
			)
		);

		$path = realpath( $template );

		foreach ( $valid_paths as $valid_path ) {
			if ( preg_match( '/\A' . preg_quote( $valid_path, '/' ) . '/', $path ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Pass the user ID to WP Telegram.
	 *
	 * @since 1.2.7
	 *
	 * @param string $url         Avatar URL.
	 * @param mixed  $id_or_email user id or email.
	 *
	 * @return string
	 */
	public function custom_avatar_url( $url, $id_or_email ) {

		$user = false;

		if ( is_numeric( $id_or_email ) ) {

			$id   = (int) $id_or_email;
			$user = get_user_by( 'id', $id );

		} elseif ( is_object( $id_or_email ) ) {

			if ( ! empty( $id_or_email->user_id ) ) {

				$id   = (int) $id_or_email->user_id;
				$user = get_user_by( 'id', $id );
			}
		} else {
			$user = get_user_by( 'email', $id_or_email );
		}

		if ( $user && $user instanceof WP_User ) {

			$meta_key = (string) WPTG_Login()->options()->get( 'avatar_meta_key', 'wptg_login_avatar' );

			// Make sure the meta key is not empty.
			if ( $meta_key ) {

				$avatar = get_user_meta( $user->ID, $meta_key, true );

				if ( ! empty( $avatar ) ) {
					return $avatar;
				}
			}
		}
		return $url;
	}

	/**
	 * Do the necessary db upgrade, if needed
	 *
	 * @since    1.5.1
	 */
	public function do_upgrade() {

		$current_version = get_option( 'wptelegram_login_ver', '1.5.0' );

		if ( ! version_compare( $current_version, $this->plugin->version(), '<' ) ) {
			return;
		}

		do_action( 'wptelegram_login_before_do_upgrade', $current_version );

		// the sequential upgrades
		// subsequent upgrade depends upon the previous one.
		$version_upgrades = array(
			'1.5.1', // first upgrade.
		);

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

		if ( empty( WPTG_Login()->options()->get_data() ) ) {
			return;
		}

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

	/**
	 * Pass the user ID to WP Telegram
	 *
	 * @since 1.0.0
	 *
	 * @param string $chat_id user chat ID.
	 * @param string $email   user email.
	 *
	 * @return mixed
	 */
	public function user_telegram_chat_id( $chat_id, $email ) {
		$user = get_user_by( 'email', $email );
		if ( $user && $user instanceof WP_User && empty( $chat_id ) ) {
			$chat_id = wptelegram_login_user_id( $user->ID );
		}
		return $chat_id;
	}
}
