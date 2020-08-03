'use strict';

jQuery( document ).ready( function ( $ ) {
	var loginForm = $( 'input[type="password"]' ).closest( 'form' );

	if ( 0 === loginForm.length ) {
		return;
	}

	var loginWrap = $( '#wptelegram-login-wrap' );
	var overflow = $( '<div class="wptelegram-login-clear"></div>' );
	loginForm.css( {
		position: 'relative',
	} );
	loginWrap.css( 'display', 'block' );
	loginForm.append( overflow );
	overflow.append( $( 'p.forgetmenot' ), $( 'p.submit' ) );
	loginForm.append( loginWrap );
} );
