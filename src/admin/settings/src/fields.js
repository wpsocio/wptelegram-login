import * as yup from 'yup';

import { __, sprintf } from './i18n';
import { FORM_ERROR } from 'final-form';

export const validate = async (values) => {
	try {
		await validationSchema.validate(values, { abortEarly: false });
	} catch (err) {
		const errors = err.inner.reduce(
			(formError, innerError) => ({
				...formError,
				[innerError.path]: innerError.message,
			}),
			{}
		);

		return errors;
	}
};

const validationSchema = yup.object({
	bot_token: yup
		.string()
		.matches(/^\d{9,11}:[a-z0-9_-]{35}$/i, {
			message: () => getErrorMessage('bot_token', 'invalid'),
			excludeEmptyString: true,
		})
		.required(() => getErrorMessage('bot_token', 'required')),
	bot_username: yup
		.string()
		.matches(/^[a-z][a-z0-9_]{3,30}[a-z0-9]$/i, {
			message: () => getErrorMessage('bot_username', 'invalid'),
			excludeEmptyString: true,
		})
		.required(() => getErrorMessage('bot_username', 'required')),
	avatar_meta_key: yup
		.string()
		.matches(/^[a-z0-9_]+$/i, {
			message: () => getErrorMessage('avatar_meta_key', 'invalid'),
			excludeEmptyString: true,
		})
		.required(() => getErrorMessage('avatar_meta_key', 'required')),
	disable_signup: yup.bool(),
	user_role: yup.string(),
	redirect_to: yup.string(),
	redirect_url: yup.string().url(),
	button_style: yup.string(),
	show_user_photo: yup.bool(),
	corner_radius: yup.string().matches(/^[1-2]?[0-9]?$/, {
		message: () => getErrorMessage('corner_radius', 'invalid'),
		excludeEmptyString: true,
	}),
	show_if_user_is: yup.string(),
	hide_on_default: yup.bool(),
	show_message_on_error: yup.bool(),
	custom_error_message: yup.string(),
});

export const getErrorMessage = (fieldName, errorType = 'invalid') => {
	let message;

	switch (errorType) {
		case 'invalid':
			message = __('Invalid %s');
			break;
		case 'required':
			message = __('%s is required.');
			break;

		default:
			return { [FORM_ERROR]: __('Changes could not be saved.') };
	}

	return sprintf(message, getFieldLabel(fieldName));
};

const fieldLabels = {
	bot_token: () => __('Bot Token'),
	bot_username: () => __('Bot Username'),
	disable_signup: () => __('Sign up'),
	user_role: () => __('User Role'),
	redirect_to: () => __('Redirect to'),
	redirect_url: () => __('Custom URL'),
	avatar_meta_key: () => __('Avatar URL Meta Key'),
	random_email: () => __('Random Email'),
	button_style: () => __('Button Style'),
	show_user_photo: () => __('Show User Photo'),
	corner_radius: () => __('Corner Radius'),
	show_if_user_is: () => __('Show if user is'),
	hide_on_default: () => __('Hide on default login'),
	show_message_on_error: () => __('Show error message'),
	custom_error_message: () => __('Error message text'),
};

export const getFieldLabel = (name) => fieldLabels[name]();

export const formatValue = (val, name) => {
	switch (name) {
		case 'custom_error_message':
			return shallowCleanUp(val);
		case 'avatar_meta_key':
		case 'bot_username':
			return sanitizeKey(val);
		default:
			return deepCleanUp(val);
	}
};

export const sanitizeKey = (val) => {
	if ('string' === typeof val) {
		return val.replace(/[^a-z0-9_]/gi, '');
	}
	return val;
};

export const shallowCleanUp = (val) => {
	if ('string' === typeof val) {
		return val.replace(/[\n\t\r]/g, '');
	}
	return val;
};

export const deepCleanUp = (val) => {
	if ('string' === typeof val) {
		return shallowCleanUp(val)
			.replace(/\s/g, '')
			.trim();
	}
	return val;
};
