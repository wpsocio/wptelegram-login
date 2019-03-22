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

$atts = '';
if ( '' !== $login_options['corner_radius'] ) {
	$atts .= ' data-radius="' . $login_options['corner_radius'] . '"';
}
if ( empty( $login_options['show_user_photo'] ) ) {
	$atts .= ' data-userpic="false"';
}

$error_message = '';
if ( 'on' == WPTG_Login()->options()->get( 'show_message_on_error' ) ) {
	$error_message = esc_attr( WPTG_Login()->options()->get( 'custom_error_message' ) );
	if ( empty( $error_message ) ) {
		$error_message = sprintf( '%s %s', __( 'Error loading Telegram Login!', 'wptelegram-login' ), __( 'May be your ISP blocks Telegram!', 'wptelegram-login' ) );
	}

	$atts .= ' data-error-message="' . $error_message . '" onerror="(function(script){if(script.dataset.errorMessage){var doc=document,div=doc.createElement(\'div\'),span=doc.createElement(\'span\');span.appendChild(doc.createTextNode(script.dataset.errorMessage));div.setAttribute(\'class\', \'error-message\');div.appendChild(span);Object.assign(div.style,{overflow:\'scroll\',border:\'1px solid rgb(221, 221, 221)\',textAlign:\'center\',display:\'inline-block\',padding:\'5px\'});script.parentElement.appendChild(div);}})(this)"';
}

$html = <<<HTML
<script async src="https://telegram.org/js/telegram-widget.js?5" data-telegram-login="{$login_options['bot_username']}" data-size="{$login_options['button_style']}" data-auth-url="{$login_options['callback_url']}" data-request-access="write" {$atts}></script>
HTML;
?>
<div class="wptelegram-login-output-wrap container">
	<?php echo $html; ?>
</div>