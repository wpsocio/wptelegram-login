<?php
/**
 * The base class of the plugin.
 *
 * @link       https://wpsocio.com
 * @since      1.0.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 */

namespace WPTelegram\Login\includes;

/**
 * The base class of the plugin.
 *
 * The base class of the plugin.
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 * @author     WP Socio
 */
abstract class BaseClass {

	/**
	 * Instances of the class.
	 *
	 * @since  1.10.11
	 * @access protected
	 * @var    self $instances The instances.
	 */
	protected static $instances = [];

	/**
	 * The plugin class instance.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Main $plugin The plugin class instance.
	 */
	protected $plugin;

	/**
	 * Base class Instance.
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.10.11
	 *
	 * @return static
	 */
	public static function instance() {
		if ( ! isset( self::$instances[ static::class ] ) ) {
			self::$instances[ static::class ] = new static();
		}
		return self::$instances[ static::class ];
	}

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {

		$this->plugin = Main::instance();
	}

	/**
	 * Get the instance of the plugin.
	 *
	 * @since     1.9.6
	 * @return    Main    The plugin class instance.
	 */
	protected function plugin() {
		return $this->plugin;
	}
}
