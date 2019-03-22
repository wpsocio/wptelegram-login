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
	 */
	protected static $_instance = null;

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
	 * @return Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
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

		$this->title =  __( 'WP Telegram Login', 'wptelegram-login' );

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
		require_once WPTELEGRAM_LOGIN_DIR . '/includes/class-wptelegram-login-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once WPTELEGRAM_LOGIN_DIR . '/includes/class-wptelegram-login-i18n.php';

		/**
		 * The class responsible for plugin options
		 */
		require_once WPTELEGRAM_LOGIN_DIR . '/includes/class-wptelegram-login-options.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once WPTELEGRAM_LOGIN_DIR . '/admin/class-wptelegram-login-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once WPTELEGRAM_LOGIN_DIR . '/public/class-wptelegram-login-public.php';

		/**
		 * Helper functions
		 */
		require_once WPTELEGRAM_LOGIN_DIR . '/includes/helper-functions.php';

		/**
		 * CMB2 library responsible for rendering fields
		 */
		if ( file_exists( WPTELEGRAM_LOGIN_DIR . '/includes/cmb2/init.php' ) ) {
			require_once WPTELEGRAM_LOGIN_DIR . '/includes/cmb2/init.php';
		}

		/**
		 * Our widget class
		 */
		require_once WPTELEGRAM_LOGIN_DIR . '/public/widgets/class-wptelegram-login-widget-primary.php';

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

		$plugin_i18n = new WPTelegram_Login_i18n();

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

		$plugin_admin = new WPTelegram_Login_Admin( $this->get_plugin_title(), $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// $this->loader->add_action( 'init', $plugin_admin, 'register_gutenberg_block' );

		$this->loader->add_action( 'enqueue_block_editor_assets', $plugin_admin, 'enqueue_block_editor_assets' );

		$this->loader->add_action( 'cmb2_admin_init', $plugin_admin, 'create_options_page', 11 );

		$this->loader->add_action( 'widgets_init', $plugin_admin, 'register_widgets' );

		$this->loader->add_filter( 'manage_users_columns', $plugin_admin, 'register_custom_user_column' );

		$this->loader->add_filter( 'manage_users_custom_column', $plugin_admin, 'register_custom_user_column_view', 10, 3 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WPTelegram_Login_Public( $this->get_plugin_title(), $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'login_enqueue_scripts', $plugin_public, 'login_enqueue_scripts' );

		$this->loader->add_action( 'init', $plugin_public, 'telegram_login' );

		$this->loader->add_action( 'register_form', $plugin_public, 'add_telegram_login_button' );
		$this->loader->add_action( 'login_form', $plugin_public, 'add_telegram_login_button' );

		$this->loader->add_shortcode( 'wptelegram-login', get_class( $plugin_public ), 'login_shortcode' );

		$this->loader->add_filter( 'render_block', $plugin_public, 'render_login_block', 11, 2 );

		$this->loader->add_filter( 'wptelegram_notify_user_chat_id', $plugin_public, 'user_telegram_chat_id', 10, 2 );

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
	 * @since     1.0.0
	 * @return    string    The title of the plugin.
	 */
	public function get_plugin_title() {
		return $this->title;
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
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

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
