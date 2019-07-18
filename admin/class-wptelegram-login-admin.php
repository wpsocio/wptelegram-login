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
	 * Title of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $title    Title of the plugin
	 */
	protected $title;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The suffix to be used for JS and CSS files
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $suffix    The suffix to be used for JS and CSS files
	 */
	private $suffix;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $title       Title of the plugin.
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $title, $plugin_name, $version ) {
		$this->title       = $title;
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// Use minified libraries if SCRIPT_DEBUG is turned off.
		$this->suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_styles( $hook_suffix ) {

		wp_enqueue_style( $this->plugin_name, WPTELEGRAM_LOGIN_URL . '/admin/css/wptelegram-login-admin' . $this->suffix . '.css', array(), $this->version, 'all' );

		// Load only on settings page.
		if ( $this->is_settings_page( $hook_suffix ) ) {
			wp_enqueue_style( $this->plugin_name . '-bootstrap', WPTELEGRAM_LOGIN_URL . '/admin/css/bootstrap/bootstrap' . $this->suffix . '.css', array(), $this->version, 'all' );

			wp_dequeue_style( 'forms-css' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_scripts( $hook_suffix ) {

		wp_enqueue_script( $this->plugin_name, WPTELEGRAM_LOGIN_URL . '/admin/js/wptelegram-login-admin.js', array( 'jquery' ), $this->version, false );

		// script localization.
		$translation_array = array(
			'title'   => $this->title,
			'name'    => $this->plugin_name,
			'version' => $this->version,
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
			$this->plugin_name,
			'wptelegram_login',
			$translation_array
		);

		// Load only on settings page.
		if ( $this->is_settings_page( $hook_suffix ) ) {

			wp_enqueue_script( $this->plugin_name . '-settings', WPTELEGRAM_LOGIN_URL . '/admin/settings/settings-build' . $this->suffix . '.js', array( 'jquery' ), $this->version, true );

			// Pass data to JS.
			$data = array(
				'settings' => array(
					'saved_opts'  => current_user_can( 'manage_options' ) ? WPTelegram_Login_Settings_Controller::get_default_settings() : array(), // Not to expose bot token to non-admins.
					'assets'      => array(
						'logo_url' => WPTELEGRAM_LOGIN_URL . '/admin/icons/icon-100x100.svg',
						'tg_icon'  => WPTELEGRAM_LOGIN_URL . '/admin/icons/tg-icon.svg',
					),
					'select_opts' => array(
						'user_role'       => self::user_role_options_cb(),
						'show_if_user_is' => self::get_show_if_user_is_options(),
					),
					'i18n'        => wptelegram_get_jed_locale_data( 'wptelegram-login' ),
				),
			);

			wp_add_inline_script(
				$this->plugin_name,
				sprintf( 'Object.assign(wptelegram_login, %s);', json_encode( $data ) ),
				'before'
			);

			// For Facebook like button.
			wp_add_inline_script(
				$this->plugin_name . '-settings',
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
			wp_enqueue_script( $this->plugin_name . '-twitter', 'https://platform.twitter.com/widgets.js', array(), $this->version, true );
		}

		// If the block editor assets are loaded.
		if ( did_action( 'enqueue_block_editor_assets' ) ) {
			$data = array(
				'blocks' => array(
					'assets'      => array(
						'login_image_url'  => WPTELEGRAM_LOGIN_URL . '/admin/icons/telegram-login.svg',
						'login_avatar_url' => WPTELEGRAM_LOGIN_URL . '/admin/icons/telegram-login-avatar.svg',
					),
					'select_opts' => array(
						'show_if_user_is' => self::get_show_if_user_is_options( true ),
					),
				),
			);

			wp_add_inline_script(
				$this->plugin_name,
				sprintf( 'Object.assign(wptelegram_login, %s);', json_encode( $data ) ),
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
		if ( $this->plugin_name . '-twitter' !== $handle ) {
			return $tag;
		}
		return '<script async src="' . $src . '" charset="utf-8"></script>' . PHP_EOL;
	}

	/**
	 * Enqueue assets for the Gutenberg block
	 *
	 * @since 1.5.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function is_settings_page( $hook_suffix ) {
		return ( false !== strpos( $hook_suffix, '_page_' . $this->plugin_name ) );
	}

	/**
	 * Enqueue assets for the Gutenberg block
	 *
	 * @since    1.4.1
	 */
	public function enqueue_block_editor_assets() {

		wp_enqueue_style(
			$this->plugin_name . '-block',
			WPTELEGRAM_LOGIN_URL . '/admin/blocks/blocks-build' . $this->suffix . '.css',
			array( 'wp-edit-blocks' ),
			$this->version
		);

		wp_enqueue_script(
			$this->plugin_name . '-block',
			WPTELEGRAM_LOGIN_URL . '/admin/blocks/blocks-build' . $this->suffix . '.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
			$this->version,
			true
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
	 * Register the admin menu.
	 *
	 * @since 1.5.0
	 */
	public function add_plugin_admin_menu() {

		if ( defined( 'WPTELEGRAM_LOADED' ) && WPTELEGRAM_LOADED ) {
			add_submenu_page(
				'wptelegram',
				esc_html( $this->title ),
				esc_html__( 'Telegram Login', 'wptelegram-login' ),
				'manage_options',
				$this->plugin_name,
				array( $this, 'display_plugin_admin_page' )
			);
		} else {
			add_menu_page(
				esc_html( $this->title ),
				esc_html( $this->title ),
				'manage_options',
				$this->plugin_name,
				array( $this, 'display_plugin_admin_page' ),
				WPTELEGRAM_LOGIN_URL . '/admin/icons/icon-16x16-white.svg'
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
	 * Register the column to be displayed in user list table.
	 *
	 * @since    1.0.0
	 * @param  array $columns The table columns.
	 * @return array
	 */
	public function register_custom_user_column( $columns ) {
		$columns['telegram_chat_id'] = __( 'Telegram User ID', 'wptelegram-login' );
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

		if ( 'telegram_chat_id' === $column_name ) {

			$user = get_user_by( 'id', $user_id );

			if ( $user && $user instanceof WP_User ) {

				return $user->wptelegram_login_user_id;
			}
		}
		return $output;
	}
}
