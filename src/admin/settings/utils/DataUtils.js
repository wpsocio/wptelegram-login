import { getErrorMessage } from '../fields';

const { jQuery: $, wptelegram_login: { api: { rest } } } = window;

export const fetchInitialValues = () => {

    const options = {
      type: 'GET',
      url: `${rest.url}/settings`,
    };

    return sendAjaxRequest(options);
};

export const handleTestToken = (args) => {

  const { bot_token, setTestingBotToken, setBotTokenTestResult, setBotTokenTestResultType, mutators: { updateFieldValue } } = args;

  setTestingBotToken(true);
  
  const options = {
    type: 'GET',
    url: `https://api.telegram.org/bot${bot_token}/getMe`,
    crossDomain:true,
    error: ( jqXHR ) => {

      console.log('ERROR', jqXHR);

      setBotTokenTestResultType('danger');

      if (jqXHR.responseText) {
        const { description, error_code } = JSON.parse( jqXHR.responseText );
      
        updateFieldValue('bot_username', '' );
  
        setBotTokenTestResult(`${error_code} (${description})`);
      } else {
        setBotTokenTestResult('could_not_connect');        
      }
    },
    success: ({result}) => {

      updateFieldValue('bot_username', result.username );

      setBotTokenTestResultType('success');

      setBotTokenTestResult(`${result.first_name} (${result.username})`);
    },
    complete: () => setTestingBotToken(false),
  };

  return sendAjaxRequest(options);
}

export const submitForm = async (values) => {
  return await new Promise((resolve) => {

    const options = {
      url: `${rest.url}/settings`,
      data: JSON.stringify( values ),
      error: ( jqXHR ) => {
        const { code, data } = JSON.parse( jqXHR.responseText );
        let errors = {};

        if (code) {
          if ('rest_invalid_param' === code) {
            Object.keys(data.params).map(key => {
              errors[key] = getErrorMessage(key);
            });
          } else if ('rest_missing_callback_param' === code) {
            Object.keys(data.params).map(key => {
              errors[key] = getErrorMessage(key, 'required');
            });
          }
        }

        Object.assign(errors,getErrorMessage('form', 'unknown'));
        
        console.log('ERROR', jqXHR);
        
        resolve(errors);
      },
      success: () => {
        window.wptelegram_login.settings.saved_opts = values;

        resolve({});
      },
    };

    return sendAjaxRequest(options);
  });
};

export const sendAjaxRequest = (options, crossDomain = false) => {

  const defaults = {
    type: 'POST',
    contentType: 'application/json; charset=utf-8',
    dataType: 'json',
    crossDomain: crossDomain,
    global: false, // Avoid the mess created by Wordfence. https://wordpress.org/support/topic/wordfence-corrupts-json-response-from-jquery-ajax-if-not-200/
  };

  if (!crossDomain) {
    defaults.beforeSend = xhr => {
			xhr.setRequestHeader( 'X-WP-Nonce', rest.nonce );
		}
  }

  return $.ajax({...defaults, ...options});
};