<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpsocio.com
 * @since      1.0.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\public
 */

namespace WPTelegram\Login\shared;

use WPTelegram\Login\includes\BaseClass;
use WPTelegram\Login\includes\Utils;
use WP_User;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\public
 * @author     WP Socio
 */
class Shared extends BaseClass {

	/**
	 * Render the login button
	 * on WP login and register page
	 *
	 * @since 1.0.0
	 */
	public function add_telegram_login_button() {
		$hide_on_default = WPTG_Login()->options()->get( 'hide_on_default' );
		$show_if_user_is = WPTG_Login()->options()->get( 'show_if_user_is' );
		if ( $hide_on_default || ! self::is_to_be_displayed( $show_if_user_is ) ) {
			return;
		}
		?>
		<div id="wptelegram-login-wrap">
			<div class="wptelegram-login-or">
				<span><?php esc_html_e( 'Or', 'wptelegram-login' ); ?></span>
			</div>
			<?php echo self::login_shortcode(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</div>
		<?php
	}

	/**
	 * Render the login block.
	 *
	 * @since 1.5.0
	 *
	 * @param string $block_content The block HTML.
	 * @param array  $block         The block data.
	 * @return string The HTML.
	 */
	public function render_login_block( $block_content, $block ) {

		$output = self::login_shortcode( $block['attrs'] );

		$block_content = preg_replace( '/<img[^>]+?>(.*?<img[^>]+?>)?/i', $output, $block_content );

		return $block_content;
	}

	/**
	 * Registers shortcode to display the login
	 *
	 * @since    1.0.0
	 *
	 * @param array $atts shortcode params.
	 */
	public static function login_shortcode( $atts = [] ) {

		$defaults = [
			'button_style'    => 'large',
			'show_user_photo' => '1',
			'corner_radius'   => '',
			'lang'            => '',
			'show_if_user_is' => 'logged_out',
			'bot_username'    => '',
		];

		// Use global options.
		foreach ( $defaults as $key => $default ) {
			$defaults[ $key ] = WPTG_Login()->options()->get( $key, $default );
		}

		$args = shortcode_atts( $defaults, $atts, 'wptelegram-login' );

		$args = array_map( 'sanitize_text_field', $args );

		if ( ! self::is_to_be_displayed( $args['show_if_user_is'] ) ) {
			return '';
		}

		// Default.
		$redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : home_url(); // phpcs:ignore

		switch ( WPTG_Login()->options()->get( 'redirect_to' ) ) {
			case 'homepage':
				$redirect_to = home_url();
				break;

			case 'current_page':
				global $pagenow;
				// Prevent redirect to login page.
				if ( 'wp-login.php' !== $pagenow ) {
					$redirect_to = Utils::wp_get_current_url();
				}
				break;

			case 'custom_url':
				// Prevent redirect to login page.
				if ( filter_var( WPTG_Login()->options()->get( 'redirect_url' ), FILTER_VALIDATE_URL ) ) {
					$redirect_to = WPTG_Login()->options()->get( 'redirect_url' );
				}
				break;
		}

		/**
		 * Filters the redirect URL for the login button
		 *
		 * It can be used to fix the wrong URL in case the website is in subdirectory and the URL is invalid.
		 *
		 * @since 1.0.0
		 *
		 * @param string $redirect_to The redirect URL.
		 */
		$redirect_to = apply_filters( 'wptelegram_login_redirect_to', $redirect_to );

		$button_style = $args['button_style'];

		$show_user_photo = (bool) $args['show_user_photo'];

		$corner_radius = $args['corner_radius'];

		$lang = $args['lang'];

		$bot_username = $args['bot_username'];

		$args = [
			'action'      => 'wptelegram_login',
			'redirect_to' => urlencode_deep( $redirect_to ),
		];

		// The actual URL to be passed to Telegram as call back.
		$callback_url = add_query_arg( $args, home_url() );

		/**
		 * Filters the callback URL for the login button
		 *
		 * It can be used to fix the wrong URL in case the website is in subdirectory and the URL is invalid.
		 *
		 * @since 1.0.0
		 *
		 * @param string $callback_url The callback URL.
		 */
		$callback_url = apply_filters( 'wptelegram_login_telegram_callback_url', $callback_url );

		$login_options = compact(
			'button_style',
			'show_user_photo',
			'corner_radius',
			'lang',
			'bot_username',
			'callback_url'
		);

		set_query_var( 'login_options', $login_options );

		ob_start();
		$overridden_template = locate_template( 'wptelegram-login/login-view.php' );
		if ( $overridden_template ) {
			/**
			 * The locate_template() returns path to file.
			 * if either the child theme or the parent theme have overridden the template.
			 */
			if ( Utils::is_valid_theme_template( $overridden_template ) ) {
				load_template( $overridden_template, false );
			}
		} else {
			/*
			 * If neither the child nor parent theme have overridden the template,
			 * we load the template from the 'partials' sub-directory of the directory this file is in.
			 */
			load_template( __DIR__ . '/partials/login-view.php', false );
		}
		$html = ob_get_contents();
		ob_get_clean();
		return $html;
	}

	/**
	 * Get the Telegram data of a user if exists.
	 *
	 * @since    1.0.0
	 *
	 * @param  string $show_if_user_is When to show the button.
	 * @return boolean
	 */
	public static function is_to_be_displayed( $show_if_user_is = 'logged_out' ) {

		$bot_token = WPTG_Login()->options()->get( 'bot_token' );

		// If settings not saved.
		if ( empty( $bot_token ) ) {
			return false;
		}

		$current_user_telegram_id = wptelegram_login_user_id();

		$show_if_connected = false;
		/**
		 * Filters whether to show the button if user is already connected.
		 *
		 * - [Examples](./examples/show_if_user_connected.md)
		 *
		 * @param boolean $show_if_connected        Whether to show the button if user is already connected.
		 * @param int     $current_user_telegram_id The current user's Telegram ID.
		 */
		$show_if_connected = apply_filters( 'wptelegram_login_show_if_user_connected', $show_if_connected, $current_user_telegram_id );

		if ( $current_user_telegram_id && ! $show_if_connected ) {
			return;
		}

		/**
		 * Filters when to show the login button
		 *
		 * Possible values:
		 * "logged_out", "logged_in", "author", "subscriber" etc.
		 *
		 * You can also pass a user role e.g "editor" or a comma separated list or an array of roles
		 * to display the button for specific user roles
		 *
		 * Passing an empty value will display the button
		 * for both logged in and logged out users
		 *
		 * @since 1.0.0
		 *
		 * @param string $show_if_user_is When to show the button.
		 */
		$show_if_user_is = apply_filters( 'wptelegram_login_show_if_user_is', $show_if_user_is );

		if ( 'any' === $show_if_user_is ) {
			return true;
		}

		$user = wp_get_current_user();

		$is_logged_in = $user->exists();

		// Using the different convention just to make the things meaningful :).
		if ( 'logged_in' === $show_if_user_is ) {
			return $is_logged_in;
		}

		if ( 'logged_out' === $show_if_user_is ) {
			return ! $is_logged_in;
		}

		if ( ! empty( $show_if_user_is ) ) {
			// now since a non empty value
			// it won't be displayed until something magical happens.
			$display = false;

			if ( ! is_array( $show_if_user_is ) ) {
				$show_if_user_is = explode( ',', $show_if_user_is );
			}

			// Remove any unwanted spaces.
			$show_if_user_is = array_map( 'trim', $show_if_user_is );

			// Check if user has one of the roles.
			foreach ( $show_if_user_is as $role ) {
				if ( in_array( $role, (array) $user->roles, true ) ) {
					$display = true;
					break;
				}
			}
			if ( ! $display ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Pass the user ID to WP Telegram.
	 *
	 * @since 1.2.7
	 *
	 * @param string $url         Avatar URL.
	 * @param mixed  $id_or_email user id or email.
	 *
	 * @return string
	 */
	public function custom_avatar_url( $url, $id_or_email ) {

		$use_telegram_avatar = true;

		/**
		 * Filters whether to use the Telegram avatar.
		 *
		 * Pass `false` to disable the Telegram avatar.
		 *
		 * - [Examples](./examples/use_telegram_avatar.md)
		 *
		 * @param boolean $use_telegram_avatar Whether to use the Telegram avatar.
		 * @param string  $url                 Avatar URL.
		 * @param mixed   $id_or_email         user id or email.
		 */
		$use_telegram_avatar = apply_filters( 'wptelegram_login_use_telegram_avatar', $use_telegram_avatar, $url, $id_or_email );

		if ( ! $use_telegram_avatar ) {
			return $url;
		}

		$user = false;

		if ( is_numeric( $id_or_email ) ) {

			$id   = (int) $id_or_email;
			$user = get_user_by( 'id', $id );

		} elseif ( is_object( $id_or_email ) ) {

			if ( ! empty( $id_or_email->user_id ) ) {

				$id   = (int) $id_or_email->user_id;
				$user = get_user_by( 'id', $id );
			}
		} else {
			$user = get_user_by( 'email', $id_or_email );
		}

		if ( $user && $user instanceof WP_User ) {

			$meta_key = (string) WPTG_Login()->options()->get( 'avatar_meta_key', 'wptg_login_avatar' );

			// Make sure the meta key is not empty.
			if ( $meta_key ) {

				$avatar_url = get_user_meta( $user->ID, $meta_key, true );

				if ( ! empty( $avatar_url ) ) {
					/**
					 * Filters the custom avatar URL.
					 *
					 * @param string $avatar_url  The custom avatar URL.
					 * @param string $url         Avatar URL.
					 * @param mixed  $id_or_email user id or email.
					 */
					return apply_filters( 'wptelegram_login_custom_avatar_url', $avatar_url, $url, $id_or_email );
				}
			}
		}
		return $url;
	}
}
