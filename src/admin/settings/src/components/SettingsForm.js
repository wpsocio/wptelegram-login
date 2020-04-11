import React from 'react';
import { settings } from 'plugin-data';

import { Form as ReactFinalForm } from 'react-final-form';
import { validate } from '../fields';
import * as mutators from '../mutators';
import { submitForm } from '../utils/FormUtils';
import FormRenderer from './FormRenderer';

const { saved_opts: initialValues } = settings;

const SettingsForm = ({ setFormState }) => {
	return (
		<ReactFinalForm
			initialValues={initialValues}
			onSubmit={submitForm}
			validate={validate}
			mutators={mutators}
			render={(props) => <FormRenderer {...props} setFormState={setFormState} />}
		/>
	);
};

export default SettingsForm;
