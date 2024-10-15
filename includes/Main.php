<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wpsocio.com
 * @since      1.0.0
 *
 * @package    WPTelegram
 * @subpackage WPTelegram\Login\includes
 */

namespace WPTelegram\Login\includes;

use WPTelegram\Login\admin\Admin;
use WPTelegram\Login\shared\Shared;
use WPTelegram\Login\shared\LoginHandler;
use WPSocio\WPUtils\ViteWPReactAssets as Assets;
use WPSocio\WPUtils\Options;

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
 * @author     WP Socio
 */
class Main {

	/**
	 * Whether the dependencies have been initiated.
	 *
	 * @since 1.10.8
	 * @var   bool $initiated Whether the dependencies have been initiated.
	 */
	private static $initiated = false;

	/**
	 * The single instance of the class.
	 *
	 * @since 1.0.0
	 * @var   Main $instance Class instance.
	 */
	protected static $instance = null;

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
	 * @var      Options    $options    The plugin options
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
	 * The asset manager.
	 *
	 * @since    1.9.7
	 * @access   protected
	 * @var      AssetManager $asset_manager The asset manager.
	 */
	protected $asset_manager;

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

		$this->set_locale();
	}

	/**
	 * Registers the initial hooks.
	 *
	 * @since    1.9.7
	 * @access   private
	 */
	public function init() {

		if ( self::$initiated ) {
			return;
		}
		self::$initiated = true;

		$plugin_upgrade = Upgrade::instance();

		// First lets do the upgrades, if needed.
		add_action( 'plugins_loaded', [ $plugin_upgrade, 'do_upgrade' ], 10 );

		// Then lets hook everything up.
		add_action( 'plugins_loaded', [ $this, 'hookup' ], 20 );
	}

	/**
	 * Whether an upgrade is going on.
	 *
	 * @since 1.10.11
	 *
	 * @return bool
	 */
	public function doing_upgrade() {
		return defined( 'WPTELEGRAM_LOGIN_DOING_UPGRADE' ) && WPTELEGRAM_LOGIN_DOING_UPGRADE;
	}

	/**
	 * Registers the initial hooks.
	 *
	 * @since    1.9.7
	 * @access   public
	 */
	public function hookup() {

		$plugin_admin = Admin::instance();

		// Ensure that the menu is always added.
		add_action( 'admin_menu', [ $plugin_admin, 'add_plugin_admin_menu' ] );
		add_action( 'admin_menu', [ Utils::class, 'update_menu_structure' ], 5 );

		if ( $this->doing_upgrade() ) {
			return;
		}
		$this->define_admin_hooks();
		$this->define_shared_hooks();
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
	}

	/**
	 * Set the plugin options
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_options() {

		$this->options = new Options( $this->plugin_name );

		$settings = $this->options->get_data();

		// If we have nothing saved.
		if ( empty( $settings ) ) {
			// Save the default settings.
			$default_settings = Utils::get_default_settings();
			$this->options->set_data( $default_settings )->update_data();
		}
	}

	/**
	 * Get the plugin options
	 *
	 * @since    1.0.0
	 * @access   public
	 *
	 * @return Options
	 */
	public function options() {
		if ( ! $this->options ) {
			$this->set_options();
		}
		return $this->options;
	}

	/**
	 * Set the assets handler.
	 *
	 * @since    1.9.0
	 * @access   private
	 */
	private function set_assets() {
		$this->assets = new Assets(
			$this->dir( '/assets/build' ),
			$this->url( '/assets/build' )
		);
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
		if ( ! $this->assets ) {
			$this->set_assets();
		}

		return $this->assets;
	}

	/**
	 * Set the asset manager.
	 *
	 * @since    1.9.7
	 * @access   private
	 */
	private function set_asset_manager() {
		$this->asset_manager = AssetManager::instance();
	}

	/**
	 * Get the plugin assets manager.
	 *
	 * @since    1.9.7
	 * @access   public
	 *
	 * @return AssetManager The asset manager.
	 */
	public function asset_manager() {
		if ( ! $this->asset_manager ) {
			$this->set_asset_manager();
		}

		return $this->asset_manager;
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new I18n();

		add_action( 'after_setup_theme', [ $plugin_i18n, 'load_plugin_textdomain' ] );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = Admin::instance();

		add_action( 'rest_api_init', [ $plugin_admin, 'register_rest_routes' ] );

		add_action( 'rest_api_init', [ $plugin_admin, 'register_user_fields' ] );

		add_action( 'widgets_init', [ $plugin_admin, 'register_widgets' ] );

		// wp-admin user pages.
		add_action( 'show_user_profile', [ Admin::class, 'wp_add_telegram_fields' ] );
		add_action( 'edit_user_profile', [ Admin::class, 'wp_add_telegram_fields' ] );
		add_filter( 'user_profile_update_errors', [ $plugin_admin, 'validate_user_profile_fields' ], 10, 1 );
		add_action( 'personal_options_update', [ $plugin_admin, 'update_user_profile_fields' ] );
		add_action( 'edit_user_profile_update', [ $plugin_admin, 'update_user_profile_fields' ] );
		add_filter( 'manage_users_columns', [ $plugin_admin, 'register_custom_user_column' ] );
		add_filter( 'manage_users_custom_column', [ $plugin_admin, 'register_custom_user_column_view' ], 10, 3 );

		// Woocommerce account page.
		add_action( 'woocommerce_edit_account_form', [ Admin::class, 'wc_add_telegram_fields' ] );
		add_action( 'woocommerce_save_account_details_errors', [ $plugin_admin, 'validate_user_profile_fields' ], 10, 1 );
		add_action( 'woocommerce_save_account_details', [ $plugin_admin, 'update_user_profile_fields' ], 10, 1 );

		add_filter( 'block_categories_all', [ $plugin_admin, 'register_block_category' ], 5, 1 );

		add_filter( 'rest_user_collection_params', [ $plugin_admin, 'rest_user_collection_params' ], 10, 1 );

		add_filter( 'rest_user_query', [ $plugin_admin, 'modify_rest_user_query' ], 10, 2 );
	}

	/**
	 * Register all of the hooks related to the shared functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_shared_hooks() {

		$shared = Shared::instance();

		add_action( 'register_form', [ $shared, 'add_telegram_login_button' ] );
		add_action( 'login_form', [ $shared, 'add_telegram_login_button' ] );

		add_shortcode( 'wptelegram-login', [ Shared::class, 'login_shortcode' ] );

		add_filter( 'render_block_wptelegram/login', [ $shared, 'render_login_block' ], 11, 2 );

		add_filter( 'get_avatar_url', [ $shared, 'custom_avatar_url' ], 10, 2 );

		$login_handler = LoginHandler::instance();

		$hook_and_priority = [ 'init', 5 ];
		/**
		 * Filter the hook and priority to use for intercepting the login request.
		 *
		 * - [Examples](./examples/intercept_request_on.md)
		 *
		 * @param array $hook_and_priority The hook and priority.
		 */
		list( $login_intercept_hook, $priority ) = apply_filters( 'wptelegram_login_intercept_request_on', $hook_and_priority );

		add_action( $login_intercept_hook, [ $login_handler, 'telegram_login' ], $priority );

		$asset_manager = $this->asset_manager();

		// Register hooks early on init.
		add_action( 'init', [ $asset_manager, 'register_assets' ], 5 );

		add_action( 'admin_enqueue_scripts', [ $asset_manager, 'enqueue_admin_assets' ] );

		add_action( 'enqueue_block_assets', [ $asset_manager, 'enqueue_block_assets' ] );

		add_action( 'login_enqueue_scripts', [ $asset_manager, 'login_enqueue_scripts' ] );

		add_action( 'wp_enqueue_scripts', [ $asset_manager, 'enqueue_public_scripts' ] );
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
}
