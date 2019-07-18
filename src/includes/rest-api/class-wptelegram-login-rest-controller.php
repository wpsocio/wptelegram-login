<?php
/**
 * WP REST API functionality of the plugin.
 *
 * @link       https://t.me/manzoorwanijk
 * @since      x.y.z
 *
 * @package    WPTelegram_Login
 * @subpackage WPTelegram_Login/includes
 */

/**
 * Base class for all the endpoints.
 *
 * @since x.y.z
 *
 * @package    WPTelegram_Login
 * @subpackage WPTelegram_Login/includes
 * @author     Manzoor Wani <@manzoorwanijk>
 */
abstract class WPTelegram_Login_REST_Controller extends WP_REST_Controller {

	/**
	 * The namespace of this controller's route.
	 *
	 * @since x.y.z
	 * @var string
	 */
	protected $namespace = 'wptelegram-login/v1';

	/**
	 * The base of this controller's route.
	 *
	 * @var string
	 */
	protected $rest_base;
}
