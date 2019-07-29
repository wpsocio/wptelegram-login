/**
 * Internal dependencies
 */
import { getErrorMessage } from '../fields';
import { sendAjaxRequest } from './ajax';

const { wptelegram_login: { api: { rest } } } = window;

export const fetchInitialValues = () => {
	const options = {
		type: 'GET',
		url: `${rest.url}/settings`,
	};

	return sendAjaxRequest( options );
};

export const submitForm = async ( values ) => {
	return await new Promise( ( resolve ) => {
		const options = {
			url: `${rest.url}/settings`,
			data: JSON.stringify( values ),
			error: ( jqXHR ) => {
				console.log( 'ERROR', jqXHR );

				const { code, data } = JSON.parse( jqXHR.responseText );
				const errors = {};

				if ( code ) {
					if ( 'rest_invalid_param' === code ) {
						Object.keys( data.params ).forEach( ( key ) => {
							errors[ key ] = getErrorMessage( key );
						} );
					} else if ( 'rest_missing_callback_param' === code ) {
						Object.keys( data.params ).forEach( ( key ) => {
							errors[ key ] = getErrorMessage( key, 'required' );
						} );
					}
				}

				Object.assign( errors, getErrorMessage( 'form', 'unknown' ) );

				resolve( errors );
			},
			success: () => {
				window.wptelegram_login.settings.saved_opts = values;

				resolve( {} );
			},
		};

		return sendAjaxRequest( options );
	} );
};
