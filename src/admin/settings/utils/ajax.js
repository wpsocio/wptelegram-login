const { jQuery: $, wptelegram_login: { api: { rest } } } = window;

export const sendAjaxRequest = ( options ) => {
	const defaults = {
		type: 'POST',
		contentType: 'application/json; charset=utf-8',
		dataType: 'json',
		global: false, // Avoid the mess created by Wordfence. https://wordpress.org/support/topic/wordfence-corrupts-json-response-from-jquery-ajax-if-not-200/
	};

	const settings = { ...defaults, ...options };
	const { crossDomain } = settings;

	if ( ! crossDomain ) {
		// set WP Nonce Header
		settings.beforeSend = ( xhr ) => {
			xhr.setRequestHeader( 'X-WP-Nonce', rest.nonce );
		};
	}

	return $.ajax( settings );
};
