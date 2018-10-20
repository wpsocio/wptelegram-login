<?php

/**
 * Provide a public-facing view for the login
 *
 * Available vars:
 * $posts, $post, $wp_did_header, $wp_query, $wp_rewrite,
 * $wpdb, $wp_version, $wp, $id, $comment, $user_ID
 *
 * $login_options = array(
 * 	'button_style',
 * 	'show_user_photo',
 * 	'corner_radius',
 * 	'bot_username',
 * 	'callback_url',
 * );
 *
 * @link       https://t.me/manzoorwanijk
 * @since      1.0.0
 *
 * @package    WPTelegram_Login
 * @subpackage WPTelegram_Login/public/partials
 */
// This file should primarily consist of HTML with a little bit of PHP.

$atts = ' ';
if ( '' !== $login_options['corner_radius'] ) {
	$atts .= 'data-radius="' . $login_options['corner_radius'] . '" ';
}
if ( empty( $login_options['show_user_photo'] ) ) {
	$atts .= 'data-userpic="false" ';
}

$html = <<<HTML
<script async src="https://telegram.org/js/telegram-widget.js?4" data-telegram-login="{$login_options['bot_username']}" data-size="{$login_options['button_style']}" {$atts} data-auth-url="{$login_options['callback_url']}" data-request-access="write"></script>
HTML;
?>
<div class="wptelegram-login-output-wrap container" id="wptelegram-login-output-wrap">
	<?php echo $html; ?>
</div>