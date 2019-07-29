/**
 * Internal dependencies
 */
import { sendAjaxRequest } from './ajax';

export const testBotToken = ( args ) => {
	const { bot_token, setTestingBotToken, setBotTokenTestResult, setBotTokenTestResultType, mutators: { updateFieldValue } } = args;

	setTestingBotToken( true );

	const options = {
		type: 'GET',
		url: `https://api.telegram.org/bot${bot_token}/getMe`,
		crossDomain: true,
		error: ( jqXHR ) => {
			console.log( 'ERROR', jqXHR );

			setBotTokenTestResultType( 'danger' );

			if ( jqXHR.responseText ) {
				const { description, error_code } = JSON.parse( jqXHR.responseText );

				updateFieldValue( 'bot_username', '' );

				setBotTokenTestResult( `${error_code} (${description})` );
			} else {
				setBotTokenTestResult( 'could_not_connect' );
			}
		},
		success: ( { result } ) => {
			updateFieldValue( 'bot_username', result.username );

			setBotTokenTestResultType( 'success' );

			setBotTokenTestResult( `${result.first_name} (${result.username})` );
		},
		complete: () => setTestingBotToken( false ),
	};

	return sendAjaxRequest( options );
};
