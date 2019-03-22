jQuery( document ).ready( function( $ ) {
	var loginForm = $( '#loginform,#registerform' ),
		loginWrap = $( '#wptelegram-login-wrap' ),
		overflow  = $( '<div class="wptelegram-login-clear"></div>' );
	if ( 0 == loginForm.length ) {
		return;
	}
	loginForm.css({'position': 'relative','paddingBottom': '92px'});
	loginWrap.css('display','block');

	loginForm.append( overflow );
	overflow.append( $( 'p.forgetmenot' ), $( 'p.submit' ) );

	loginForm.append( loginWrap );
} );