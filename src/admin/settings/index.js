/**
 * External dependencies
 */
import React from 'react';
import ReactDOM from 'react-dom';
/**
 * Internal dependencies
 */
import App from './App';
import { initI18n } from './i18n';

initI18n();

ReactDOM.render( <App />, document.getElementById( 'wptelegram-login-settings' ) );
