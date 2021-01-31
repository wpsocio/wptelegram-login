<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://t.me/manzoorwanijk
 * @since      1.0.0
 *
 * @package    WPTelegram_Login
 * @subpackage WPTelegram_Login/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WPTelegram_Login
 * @subpackage WPTelegram_Login/admin
 * @author     Manzoor Wani <@manzoorwanijk>
 */
class WPTelegram_Login_Admin {

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
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_styles( $hook_suffix ) {

		if ( ! defined( 'WPTELEGRAM_LOADED' ) ) {
			wp_enqueue_style(
				$this->plugin->name(),
				$this->plugin->url( '/admin/css/admin' ) . $this->plugin->suffix() . '.css',
				array(),
				$this->plugin->version(),
				'all'
			);
		}

		// Load only on settings page.
		if ( $this->is_settings_page( $hook_suffix ) ) {
			wp_enqueue_style( $this->plugin->name() . '-bootstrap', $this->plugin->url( '/admin/css/bootstrap/bootstrap' ) . $this->plugin->suffix() . '.css', array(), $this->plugin->version(), 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_scripts( $hook_suffix ) {

		wp_enqueue_script( $this->plugin->name(), $this->plugin->url( '/admin/js/wptelegram-login-admin' ) . $this->plugin->suffix() . '.js', array( 'jquery' ), $this->plugin->version(), false );

		// script localization.
		$translation_array = array(
			'title'   => $this->plugin->title(),
			'name'    => $this->plugin->name(),
			'version' => $this->plugin->version(),
			'api'     => array(
				'ajax' => array(
					'nonce' => wp_create_nonce( 'wptelegram-login' ),
					'use'   => 'server', // or may be 'browser'?
					'url'   => admin_url( 'admin-ajax.php' ),
				),
				'rest' => array(
					'nonce' => wp_create_nonce( 'wp_rest' ),
					'url'   => esc_url_raw( rest_url( 'wptelegram-login/v1' ) ),
				),
			),
		);

		wp_localize_script(
			$this->plugin->name(),
			'wptelegram_login',
			$translation_array
		);

		// Load only on settings page.
		if ( $this->is_settings_page( $hook_suffix ) ) {

			// Avoid caching for development.
			$version = defined( 'WPTELEGRAM_DEV' ) && WPTELEGRAM_DEV ? gmdate( 'y.m.d-is', filemtime( $this->plugin->dir( '/admin/settings/dist/settings-dist.js' ) ) ) : $this->plugin->version();

			wp_enqueue_script( $this->plugin->name() . '-settings', $this->plugin->url( '/admin/settings/dist/settings-dist.js' ), array( 'jquery' ), $version, true );

			// Pass data to JS.
			$data = array(
				'settings' => array(
					'saved_opts'  => current_user_can( 'manage_options' ) ? WPTelegram_Login_Settings_Controller::get_default_settings() : array(), // Not to expose bot token to non-admins.
					'assets'      => array(
						'logo_url' => $this->plugin->url( '/admin/icons/icon-128x128.png' ),
						'tg_icon'  => $this->plugin->url( '/admin/icons/tg-icon.svg' ),
					),
					'select_opts' => array(
						'user_role'       => self::user_role_options_cb(),
						'show_if_user_is' => self::get_show_if_user_is_options(),
					),
					'i18n'        => wptelegram_get_jed_locale_data( 'wptelegram-login' ),
				),
			);

			wp_add_inline_script(
				$this->plugin->name(),
				sprintf( 'Object.assign(wptelegram_login, %s);', json_encode( $data ) ), // phpcs:ignore WordPress.WP.AlternativeFunctions
				'before'
			);

			// For Facebook like button.
			wp_add_inline_script(
				$this->plugin->name() . '-settings',
				'(function(d, s, id) {'
				. '  var js, fjs = d.getElementsByTagName(s)[0];'
				. '  if (d.getElementById(id)) return;'
				. '  js = d.createElement(s); js.id = id;'
				. '  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.9";'
				. '  fjs.parentNode.insertBefore(js, fjs);'
				. '}(document, "script", "facebook-jssdk"));',
				'after'
			);

			// For Twitter Follow button.
			wp_enqueue_script( $this->plugin->name() . '-twitter', 'https://platform.twitter.com/widgets.js', array(), $this->plugin->version(), true );
		}

		// If the block editor assets are loaded.
		if ( did_action( 'enqueue_block_editor_assets' ) ) {
			$data = array(
				'blocks' => array(
					'assets'      => array(
						'login_image_url'  => $this->plugin->url( '/admin/icons/telegram-login.svg' ),
						'login_avatar_url' => $this->plugin->url( '/admin/icons/telegram-login-avatar.svg' ),
					),
					'select_opts' => array(
						'show_if_user_is' => self::get_show_if_user_is_options( true ),
					),
				),
			);

			wp_add_inline_script(
				$this->plugin->name(),
				sprintf( 'Object.assign(wptelegram_login, %s);', json_encode( $data ) ), // phpcs:ignore WordPress.WP.AlternativeFunctions
				'before'
			);
		}
	}

	/**
	 * Format the Twitter script.
	 *
	 * @since 1.5.0
	 *
	 * @param string $tag    The `<script>` tag for the enqueued script.
	 * @param string $handle The script's registered handle.
	 * @param string $src    The script's source URL.
	 *
	 * @return string
	 */
	public function format_twitter_script_tag( $tag, $handle, $src ) {
		if ( $this->plugin->name() . '-twitter' !== $handle ) {
			return $tag;
		}
		// phpcs:ignore WordPress.WP.EnqueuedResources
		return '<script async src="' . $src . '" charset="utf-8"></script>' . PHP_EOL;
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

		wp_enqueue_style(
			$this->plugin->name() . '-block',
			$this->plugin->url( '/admin/blocks/dist/blocks-build.css' ),
			array( 'wp-edit-blocks' ),
			$this->plugin->version()
		);

		wp_enqueue_script(
			$this->plugin->name() . '-block',
			$this->plugin->url( '/admin/blocks/dist/blocks-build.js' ),
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
			$this->plugin->version(),
			true
		);
	}

	/**
	 * Registers custom category for blocks.
	 *
	 * @since 1.6.3
	 *
	 * @param array $categories The block categories.
	 * @return array
	 */
	public function register_block_category( $categories ) {
		$slugs = wp_list_pluck( $categories, 'slug' );
		$slug  = 'wptelegram';
		if ( in_array( $slug, $slugs, true ) ) {
			return $categories;
		}
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => $slug,
					'title' => __( 'WP Telegram', 'wptelegram-login' ),
					'icon'  => null,
				),
			)
		);
	}

	/**
	 * Register WP REST API routes.
	 *
	 * @since 1.5.0
	 */
	public function register_rest_routes() {
		$controller = new WPTelegram_Login_Settings_Controller();
		$controller->register_routes();
	}

	/**
	 * Register user fields in WP REST API.
	 *
	 * @since x.y.z
	 */
	public function register_user_fields() {
		register_rest_field(
			'user',
			WPTELEGRAM_USER_ID_META_KEY,
			array(
				'get_callback'    => function ( $object ) {
					return current_user_can( 'list_users' ) ? get_user_meta( $object['id'], WPTELEGRAM_USER_ID_META_KEY, true ) : '';
				},
				'update_callback' => function ( $value, $object ) {
					update_user_meta( $object->ID, WPTELEGRAM_USER_ID_META_KEY, $value );
				},
				'schema'          => array(
					'type'        => 'string',
					'arg_options' => array(
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => function ( $chat_id ) {
							return self::is_valid_chat_id( $chat_id );
						},
					),
				),
			)
		);

		register_rest_field(
			'user',
			WPTELEGRAM_USERNAME_META_KEY,
			array(
				'get_callback' => function ( $object ) {
					return current_user_can( 'list_users' ) ? get_user_meta( $object['id'], WPTELEGRAM_USERNAME_META_KEY, true ) : '';
				},
				'schema'       => array(
					'type' => 'string',
				),
			),
		);
	}

	/**
	 * Add custom params to WP REST user collection.
	 *
	 * @since x.y.z
	 *
	 * @param array $query_params JSON Schema-formatted collection parameters.
	 */
	public function rest_user_collection_params( $query_params ) {
		$query_params['telegram_users_only'] = array(
			'description' => __( 'Limit result set to users who have their Telegram accounts connected.' ),
			'type'        => 'boolean',
		);
		return $query_params;
	}

	/**
	 * Modifies WP REST user query if needed.
	 *
	 * @since x.y.z
	 *
	 * @param array           $prepared_args Array of arguments for WP_User_Query.
	 * @param WP_REST_Request $request       The current request.
	 */
	public function modify_rest_user_query( $prepared_args, $request ) {
		if ( ! $request->get_param( 'telegram_users_only' ) ) {
			return $prepared_args;
		}
		$prepared_args['meta_query'] = array(
			'relation' => 'AND',
			array(
				'key'     => WPTELEGRAM_USER_ID_META_KEY,
				'compare' => 'EXISTS',
			),
			array(
				'key'     => WPTELEGRAM_USER_ID_META_KEY,
				'compare' => '!=',
				'value'   => '',
			),
		);

		return $prepared_args;
	}

	/**
	 * Register the admin menu.
	 *
	 * @since 1.5.0
	 */
	public function add_plugin_admin_menu() {

		if ( defined( 'WPTELEGRAM_LOADED' ) && WPTELEGRAM_LOADED ) {
			add_submenu_page(
				'wptelegram',
				esc_html( $this->plugin->title() ),
				esc_html__( 'Telegram Login', 'wptelegram-login' ),
				'manage_options',
				$this->plugin->name(),
				array( $this, 'display_plugin_admin_page' )
			);
		} else {
			add_menu_page(
				esc_html( $this->plugin->title() ),
				esc_html( $this->plugin->title() ),
				'manage_options',
				$this->plugin->name(),
				array( $this, 'display_plugin_admin_page' ),
				'none'
			);
		}
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since 1.5.0
	 */
	public function display_plugin_admin_page() {
		?>
			<div id="wptelegram-login-settings"></div>
		<?php
	}

	/**
	 * Create our widget.
	 *
	 * @since    1.0.0
	 */
	public function register_widgets() {
		register_widget( 'WPTelegram_Login_Widget_Primary' );
	}

	/**
	 * Callback for user_role option.
	 *
	 * @return array
	 */
	public static function user_role_options_cb() {

		$data = array();

		foreach ( get_editable_roles() as $role_name => $role_info ) {
			$data[ $role_name ] = translate_user_role( $role_info['name'] );
		}
		return $data;
	}

	/**
	 * Callback for show_if_user_is option.
	 *
	 * @return mixed
	 */
	public static function show_if_user_is_options_cb() {

		return self::get_show_if_user_is_options( false );
	}

	/**
	 * Get options for show_if_user_is dropdown.
	 *
	 * @param boolean $for_block_editor Whether the options are for block editor.
	 *
	 * @return array
	 */
	public static function get_show_if_user_is_options( $for_block_editor = false ) {

		$data = array(
			'0'          => __( 'Any', 'wptelegram-login' ),
			'logged_out' => __( 'Logged out', 'wptelegram-login' ),
			'logged_in'  => __( 'Logged in', 'wptelegram-login' ),
		);

		$data = array_merge( $data, self::user_role_options_cb() );

		if ( $for_block_editor ) {
			$_data = array();
			foreach ( $data as $key => $label ) {
				$_data[] = array(
					'value' => $key,
					'label' => $label,
				);
			}

			return $_data;
		}

		return $data;
	}

	/**
	 * Adds User Telegram ID field to user profile.
	 *
	 * @since    1.8.2
	 * @param WP_User $user Currently-listed user.
	 * @return void
	 */
	public function add_user_profile_fields( $user ) {
		$telegram_id  = get_the_author_meta( WPTELEGRAM_USER_ID_META_KEY, $user->ID );
		$field_name   = WPTELEGRAM_USER_ID_META_KEY;
		$bot_username = WPTG_Login()->options()->get( 'bot_username' );
		if ( empty( $bot_username ) ) {
			return;
		}
		$is_current_user = get_current_user_id() === $user->ID;
		?>
		<h2><?php esc_html_e( 'Telegram Info', 'wptelegram-login' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Here you can connect this account to Telegram.', 'wptelegram-login' ); ?></p>
		<table class="form-table">
			<tr>
				<th>
					<label for="<?php echo esc_attr( $field_name ); ?>"><?php esc_html_e( 'Telegram Chat ID', 'wptelegram-login' ); ?></label>
				</th>
				<td>
					<input type="text" name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $telegram_id ); ?>" class="regular-text" />
					<p style="color:#f10e0e;"><b><?php esc_html_e( 'INSTRUCTIONS!', 'wptelegram-login' ); ?></b></p>
					<ul style="list-style-type: disc;">
						<li>
							<?php
								/* translators: %s is bot username */
								printf(
									$is_current_user // phpcs:ignore
									? __( 'Get your Chat ID from %s and enter it above.', 'wptelegram-login' )
									: __( 'Ask the user to get the Chat ID from %s and enter it above.', 'wptelegram-login' ),
									'<a href="https://t.me/MyChatInfoBot" target="_blank">@MyChatInfoBot</a>'
								);
							?>
						</li>
						<li>
							<?php
								/* translators: %s is bot username */
								printf(
									$is_current_user // phpcs:ignore
									? __( 'Start a conversation with %s to receive notifications.', 'wptelegram-login' )
									: __( 'Ask the user to start a conversation with %s to receive notifications.', 'wptelegram-login' ),
									'<a href="https://t.me/' . $bot_username . '"  target="_blank">@' . $bot_username . '</a>'
								);
							?>
						</li>
					</ul>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Validate the profile fields.
	 *
	 * @since   1.8.2
	 * @param   WP_Error $errors WP_Error object (passed by reference).
	 * @param   bool     $update Whether this is a user update.
	 * @param   stdClass $user   User object (passed by reference).
	 */
	public function validate_user_profile_fields( &$errors, $update = null, &$user = null ) {

		if ( isset( $_POST[ WPTELEGRAM_USER_ID_META_KEY ] ) ) { // phpcs:ignore

			$chat_id = sanitize_text_field( $_POST[ WPTELEGRAM_USER_ID_META_KEY ] ); // phpcs:ignore

			if ( $chat_id && ! self::is_valid_chat_id( $chat_id ) ) {

				$errors->add( 'invalid_chat_id', __( 'Error:', 'wptelegram-login' ) . ' ' . __( 'Please Enter a valid Chat ID', 'wptelegram-login' ) );
			}
		}
	}

	/**
	 * Update user fields.
	 *
	 * @since   1.8.2
	 * @param integer $user_id The user ID.
	 * @return void
	 */
	public function update_user_profile_fields( $user_id ) {
		if ( current_user_can( 'edit_user', $user_id ) && isset( $_POST[ WPTELEGRAM_USER_ID_META_KEY ] ) ) { // phpcs:ignore
			$chat_id = $_POST[ WPTELEGRAM_USER_ID_META_KEY ]; // phpcs:ignore
			$chat_id = sanitize_text_field( $chat_id );

			if ( empty( $chat_id ) ) {
				delete_user_meta( $user_id, WPTELEGRAM_USER_ID_META_KEY );
			} elseif ( self::is_valid_chat_id( $chat_id ) ) {
				update_user_meta( $user_id, WPTELEGRAM_USER_ID_META_KEY, $chat_id );
			}
		}
	}

	/**
	 * Whether a given chat ID is valid.
	 *
	 * @param integer $chat_id The Telegram chat ID.
	 * @return bool
	 */
	public static function is_valid_chat_id( $chat_id ) {
		return (bool) preg_match( '/^\-?[^0\D]\d{6,51}$/', $chat_id );
	}

	/**
	 * Register the column to be displayed in user list table.
	 *
	 * @since    1.0.0
	 * @param  array $columns The table columns.
	 * @return array
	 */
	public function register_custom_user_column( $columns ) {
		$columns[ WPTELEGRAM_USER_ID_META_KEY ] = __( 'Telegram User ID', 'wptelegram-login' );
		return $columns;
	}

	/**
	 * Register the output for User Telegram ID.
	 *
	 * @since    1.0.0
	 * @param string $output      Custom column output. Default empty.
	 * @param string $column_name Column name.
	 * @param int    $user_id     ID of the currently-listed user.
	 * @return string|null
	 */
	public function register_custom_user_column_view( $output, $column_name, $user_id ) {

		if ( WPTELEGRAM_USER_ID_META_KEY === $column_name ) {

			$user = get_user_by( 'id', $user_id );

			if ( $user && $user instanceof WP_User ) {

				return $user->{WPTELEGRAM_USER_ID_META_KEY};
			}
		}
		return $output;
	}
}
