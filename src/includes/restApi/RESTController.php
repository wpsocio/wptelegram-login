<?php
/**
 * WP REST API functionality of the plugin.
 *
 * @link       https://wpsocio.com
 * @since      1.5.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 */

namespace WPTelegram\Login\includes\restApi;

/**
 * Base class for all the endpoints.
 *
 * @since 1.5.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\includes
 * @author     WP Socio
 */
abstract class RESTController extends \WP_REST_Controller {

	/**
	 * The namespace of this controller's route.
	 *
	 * @since 1.5.0
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
