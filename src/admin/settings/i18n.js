/**
 * WordPress dependencies
 */
import { setLocaleData } from '@wordpress/i18n';

export const initI18n = () => {
	const { wptelegram_login: { settings: { i18n: locale_data } } } = window;

	setLocaleData( locale_data );
};

export { __, sprintf } from '@wordpress/i18n';
