<?php
/**
 * The base class of the plugin.
 *
 * @link       https://t.me/manzoorwanijk
 * @since      1.0.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 */

namespace WPTelegram\Login\includes;

use WPTelegram\Login\includes\Main;

/**
 * The base class of the plugin.
 *
 * The base class of the plugin.
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 * @author     Manzoor Wani <@manzoorwanijk>
 */
abstract class BaseClass {

	/**
	 * The plugin class instance.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Main $plugin The plugin class instance.
	 */
	protected $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param Main $plugin The plugin class instance.
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;
	}
}
