<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://t.me/manzoorwanijk
 * @since      1.0.0
 *
 * @package    WPTelegram_Login
 * @subpackage WPTelegram_Login/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WPTelegram_Login
 * @subpackage WPTelegram_Login/includes
 * @author     Manzoor Wani <@manzoorwanijk>
 */
class WPTelegram_Login {

	/**
	 * The single instance of the class.
	 *
	 * @since 1.0.0
	 * @var   WPTelegram_Login $instance Class instance.
	 */
	protected static $instance = null;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WPTelegram_Login_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Title of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $title    Title of the plugin
	 */
	protected $title;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The plugin options
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $options    The plugin options
	 */
	protected $options;

	/**
	 * Main class Instance.
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return WPTelegram_Login instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->version = WPTELEGRAM_LOGIN_VER;

		$this->title = __( 'WP Telegram Login', 'wptelegram-login' );

		$this->plugin_name = strtolower( __CLASS__ );

		$this->load_dependencies();
		$this->set_options();

		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		$this->run();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WPTelegram_Login_Loader. Orchestrates the hooks of the plugin.
	 * - WPTelegram_Login_i18n. Defines internationalization functionality.
	 * - WPTelegram_Login_Admin. Defines all hooks for the admin area.
	 * - WPTelegram_Login_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once $this->dir( '/includes/class-wptelegram-login-loader.php' );

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once $this->dir( '/includes/class-wptelegram-login-i18n.php' );

		/**
		 * The class responsible for plugin options
		 */
		require_once $this->dir( '/includes/class-wptelegram-login-options.php' );

		/**
		 * The classes responsible for WP REST API of the plugin.
		 */
		require_once $this->dir( '/includes/rest-api/class-wptelegram-login-rest-controller.php' );
		require_once $this->dir( '/includes/rest-api/class-wptelegram-login-settings-controller.php' );

		/**
		 * The class responsible for plugin upgrades.
		 */
		require_once $this->dir( '/includes/class-wptelegram-login-upgrade.php' );

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once $this->dir( '/admin/class-wptelegram-login-admin.php' );

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once $this->dir( '/public/class-wptelegram-login-public.php' );

		/**
		 * Helper functions
		 */
		require_once $this->dir( '/includes/helper-functions.php' );

		/**
		 * Our widget class
		 */
		require_once $this->dir( '/public/widgets/class-wptelegram-login-widget-primary.php' );

		$this->loader = new WPTelegram_Login_Loader();

	}

	/**
	 * Set the plugin options
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_options() {

		$this->options = new WPTelegram_Login_Options( $this->plugin_name );

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WPTelegram_Login_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WPTelegram_Login_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WPTelegram_Login_Admin( $this );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_filter( 'script_loader_tag', $plugin_admin, 'format_twitter_script_tag', 10, 3 );

		$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_admin, 'enqueue_block_editor_assets' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu', 11 );

		$this->loader->add_action( 'rest_api_init', $plugin_admin, 'register_rest_routes' );

		$this->loader->add_action( 'rest_api_init', $plugin_admin, 'register_user_fields' );

		$this->loader->add_action( 'widgets_init', $plugin_admin, 'register_widgets' );

		$this->loader->add_action( 'show_user_profile', $plugin_admin, 'add_user_profile_fields' );
		$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'add_user_profile_fields' );
		$this->loader->add_filter( 'user_profile_update_errors', $plugin_admin, 'validate_user_profile_fields', 10, 3 );
		$this->loader->add_action( 'personal_options_update', $plugin_admin, 'update_user_profile_fields' );
		$this->loader->add_action( 'edit_user_profile_update', $plugin_admin, 'update_user_profile_fields' );
		$this->loader->add_filter( 'manage_users_columns', $plugin_admin, 'register_custom_user_column' );
		$this->loader->add_filter( 'manage_users_custom_column', $plugin_admin, 'register_custom_user_column_view', 10, 3 );

		$this->loader->add_filter( 'block_categories', $plugin_admin, 'register_block_category', 10, 1 );

		$this->loader->add_filter( 'rest_user_collection_params', $plugin_admin, 'rest_user_collection_params', 10, 1 );

		$this->loader->add_filter( 'rest_user_query', $plugin_admin, 'modify_rest_user_query', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_upgrade = new WPTelegram_Login_Upgrade( $this );

		$this->loader->add_action( 'after_setup_theme', $plugin_upgrade, 'do_upgrade' );

		$plugin_public = new WPTelegram_Login_Public( $this );

		$this->loader->add_action( 'login_enqueue_scripts', $plugin_public, 'login_enqueue_scripts' );

		$this->loader->add_action( 'init', $plugin_public, 'telegram_login' );

		$this->loader->add_action( 'register_form', $plugin_public, 'add_telegram_login_button' );
		$this->loader->add_action( 'login_form', $plugin_public, 'add_telegram_login_button' );

		$this->loader->add_shortcode( 'wptelegram-login', get_class( $plugin_public ), 'login_shortcode' );

		$this->loader->add_filter( 'render_block', $plugin_public, 'render_login_block', 11, 2 );

		$this->loader->add_filter( 'get_avatar_url', $plugin_public, 'custom_avatar_url', 10, 2 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	private function run() {
		$this->loader->run();
	}

	/**
	 * Get the plugin options
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function options() {

		return $this->options;
	}

	/**
	 * The title of the plugin.
	 *
	 * @since     1.5.1
	 * @return    string    The title of the plugin.
	 */
	public function title() {
		return $this->title;
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.5.1
	 * @return    string    The name of the plugin.
	 */
	public function name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.5.1
	 * @return    string    The version number of the plugin.
	 */
	public function version() {
		return $this->version;
	}

	/**
	 * Retrieve directory path to the plugin.
	 *
	 * @since 1.5.1
	 * @param string $path Path to append.
	 * @return string Directory with optional path appended
	 */
	public function dir( $path = '' ) {
		return WPTELEGRAM_LOGIN_DIR . $path;
	}

	/**
	 * Retrieve URL path to the plugin.
	 *
	 * @since 1.5.1
	 * @param string $path Path to append.
	 * @return string URL with optional path appended
	 */
	public function url( $path = '' ) {
		return WPTELEGRAM_LOGIN_URL . $path;
	}

	/**
	 * The suffix to use for plugin assets.
	 *
	 * @since 1.5.1
	 *
	 * @return string The suffix to use.
	 */
	public function suffix() {
		return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WPTelegram_Login_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}
}
