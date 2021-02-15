<?php

spl_autoload_register( 'wptelegram_login_autoloader' );

/**
 * Autoloader.
 *
 * @param string $class_name The requested classname.
 * @return void
 */
function wptelegram_login_autoloader( $class_name ) {

	$namespace = 'WPTelegram\Login';

	if ( 0 !== strpos( $class_name, $namespace ) ) {
		return;
	}

	$class_name = str_replace( $namespace, '', $class_name );
	$class_name = str_replace( '\\', DIRECTORY_SEPARATOR, $class_name );

	$path = WPTELEGRAM_LOGIN_DIR . $class_name . '.php';

	include_once $path;
}
