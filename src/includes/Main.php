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
 * @package    WPTelegram
 * @subpackage WPTelegram\Login\includes
 */

namespace WPTelegram\Login\includes;

use \WPTelegram\Login\admin\Admin;
use \WPTelegram\Login\shared\Shared;
use \WPTelegram\Login\shared\LoginHandler;

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
 * @package    WPTelegram
 * @subpackage WPTelegram\Login\includes
 * @author     Manzoor Wani <@manzoorwanijk>
 */
class Main {

	/**
	 * The single instance of the class.
	 *
	 * @since 1.0.0
	 * @var   Main $instance Class instance.
	 */
	protected static $instance = null;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * The assets handler.
	 *
	 * @since    1.9.0
	 * @access   protected
	 * @var      string    $assets    The assets handler.
	 */
	protected $assets;

	/**
	 * Main class Instance.
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return Main instance.
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

		$this->plugin_name = 'wptelegram_login';

		$this->load_dependencies();
		$this->set_options();
		$this->set_assets();

		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_shared_hooks();

		$this->run();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		* Helper functions
		*/
		require_once $this->dir( '/includes/helper-functions.php' );

		$this->loader = new Loader();
	}

	/**
	 * Set the plugin options
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_options() {

		$this->options = new Options( $this->plugin_name );

	}

	/**
	 * Set the assets handler.
	 *
	 * @since    1.9.0
	 * @access   private
	 */
	private function set_assets() {
		$this->assets = new Assets( $this->dir( '/assets' ), $this->url( '/assets' ) );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new I18n();

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

		$plugin_admin = new Admin( $this );

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
	 * Register all of the hooks related to the shared functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_shared_hooks() {

		$upgrade = new Upgrade( $this );

		$this->loader->add_action( 'after_setup_theme', $upgrade, 'do_upgrade' );

		$shared = new Shared( $this );

		$this->loader->add_action( 'register_form', $shared, 'add_telegram_login_button' );
		$this->loader->add_action( 'login_form', $shared, 'add_telegram_login_button' );

		$this->loader->add_shortcode( 'wptelegram-login', get_class( $shared ), 'login_shortcode' );

		$this->loader->add_filter( 'render_block', $shared, 'render_login_block', 11, 2 );

		$this->loader->add_filter( 'get_avatar_url', $shared, 'custom_avatar_url', 10, 2 );

		$login_handler = new LoginHandler( $this );

		$this->loader->add_action( 'init', $login_handler, 'telegram_login' );

		$asset_manager = new AssetManager( $this );

		$this->loader->add_action( 'admin_enqueue_scripts', $asset_manager, 'enqueue_admin_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $asset_manager, 'enqueue_admin_scripts' );

		$this->loader->add_action( 'enqueue_block_editor_assets', $asset_manager, 'enqueue_block_editor_assets' );

		$this->loader->add_action( 'login_enqueue_scripts', $asset_manager, 'login_enqueue_scripts' );
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
	 *
	 * @return Options The options instance.
	 */
	public function options() {

		return $this->options;
	}

	/**
	 * Get the plugin assets handler.
	 *
	 * @since    1.9.0
	 * @access   public
	 *
	 * @return Assets The assets instance.
	 */
	public function assets() {

		return $this->assets;
	}

	/**
	 * The title of the plugin.
	 *
	 * @since     1.5.1
	 * @return    string    The title of the plugin.
	 */
	public function title() {
		// Set here instead of constructor
		// to be able to translate it.
		if ( ! $this->title ) {
			$this->title = __( 'WP Telegram Login', 'wptelegram-login' );
		}
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
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}
}
