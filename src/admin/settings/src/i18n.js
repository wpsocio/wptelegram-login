import { setLocaleData } from '@wordpress/i18n';
import { settings } from 'plugin-data';

const { i18n: locale_data } = settings;

export const initI18n = () => {
	setLocaleData(locale_data);
};

export { __, sprintf } from '@wordpress/i18n';
