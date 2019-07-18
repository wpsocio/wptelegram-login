import React, { useState } from 'react';
import { __, sprintf } from '../i18n';
import { Form, Button } from 'react-bootstrap';
import Card from './Card';
import BotTestResult from './BotTestResult';
import FormField from './FormField';
import { Form as ReactFinalForm } from 'react-final-form';
import { validate, getFieldLabel, updateFieldValue } from '../fields';
import { submitForm, handleTestToken } from '../utils/DataUtils';
import Instructions from './Instructions';

export default ({setValues}) => {
  const [botUsernameReadOnly, setBotUsernameReadOnly] = useState(true);
  const [avatarMetaKeyReadOnly, setAvatarMetaKeyReadOnly] = useState(true);

  const [testingBotToken, setTestingBotToken] = useState(false);
  // The result string
  const [botTokenTestResult, setBotTokenTestResult] = useState('');
  // e.g. "succes" or "danger"
  const [botTokenTestResultType, setBotTokenTestResultType] = useState('');
  if ('could_not_connect' === botTokenTestResult) {
    setBotTokenTestResult(__( 'Could not connect' ));
  }

  const { wptelegram_login: { settings: {saved_opts: initialValues, select_opts} } } = window;

  return (
    <ReactFinalForm
      initialValues={initialValues}
      onSubmit={submitForm}
      validate={validate}
      mutators={{updateFieldValue}}
      render={(props) => {
        const { handleSubmit, submitting, pristine, submitSucceeded, submitFailed, submitError, values, errors, form : { mutators, getState } } = props;
        return (
          <Form onSubmit={handleSubmit}>
            {setValues(getState())}
            <Instructions />            
            <Card title={__('Telegram Options')}>
              <FormField
                name="bot_token"
                label={getFieldLabel('bot_token')}
                desc={__('Please read the instructions above')}
                after={<BotTestResult result={botTokenTestResult} type={botTokenTestResultType}/>}
                controlProps={{
                  id: 'bot_token',
                  type: 'text',
                }}
                inputGroupProps={{
                  append: () => (
                    <Button
                      variant="outline-secondary"
                      size="sm"
                      disabled={testingBotToken || errors.bot_token}
                      onClick={() => handleTestToken({bot_token: values.bot_token,setTestingBotToken, setBotTokenTestResult, setBotTokenTestResultType, mutators})}
                    >
                      {testingBotToken ? __( 'Please wait...') : __('Test Token')}
                    </Button>
                  ),
                  style: {maxWidth:'400px'}
                }}
              />
              <FormField
                name="bot_username"
                label={getFieldLabel('bot_username')}
                desc={sprintf(__('Use %s above to set automatically.'), __('Test Token'))}
                controlProps={{
                  id: 'bot_username',
                  type: 'text',
                  readOnly: botUsernameReadOnly,
                  onDoubleClick: () => botUsernameReadOnly && setBotUsernameReadOnly(false),
                  onBlur: () => null, // avoid validation on blur
                }}
                inputGroupProps={{
                  prepend: (InputGroup) => <InputGroup.Text>@</InputGroup.Text>,
                  style: {maxWidth:'300px'},
                }}
              />
            </Card>

            <Card title={__('Login Options')}>
              <FormField
                name="disable_signup"
                label={getFieldLabel('disable_signup')}
                _label={__('Disable Sign up')}
                desc={__('If checked, only the existing users who have connected their Telegram will be able to login.')}
                controlProps={{
                  id: 'disable_signup',
                  type: 'checkbox',
                }}
              />
              {!values.disable_signup && <FormField
                name="user_role"
                label={getFieldLabel('user_role')}
                desc={__('The default role to assign for the new users')}
                options={select_opts.user_role}
                controlProps={{
                  id: 'user_role',
                  type: 'select',
                  style: {width:'auto'},
                }}
              />}

              <FormField
                name="redirect_to"
                label={getFieldLabel('redirect_to')}
                desc={__('Redirect location after login')}
                options={{
                  default: __('Default'),
                  homepage: __('Homepage'),
                  current_page: __('Current page'),
                  custom_url: __('Custom URL'),
                }}
                controlProps={{
                  type: 'radio',
                }}
              />

              {values.redirect_to === 'custom_url' && <FormField
                name="redirect_url"
                label={getFieldLabel('redirect_url')}
                controlProps={{
                  id: 'redirect_url',
                  type: 'url',
                  placeholder: 'http://',
                  size: 'sm',
                  onBlur: () => null, // avoid validation on blur
                }}
              />}

              <FormField
                name="avatar_meta_key"
                label={getFieldLabel('avatar_meta_key')}
                desc={__('The user meta key to be used to save Telegram photo URL.')}
                controlProps={{
                  id: 'avatar_meta_key',
                  type: 'text',
                  readOnly: avatarMetaKeyReadOnly,
                  size: 'sm',
                  onDoubleClick: () => avatarMetaKeyReadOnly && setAvatarMetaKeyReadOnly(false),
                  onBlur: () => null, // avoid validation on blur
                  style: {maxWidth:'200px'},
                }}
              />
            </Card>

            <Card title={__('Button Options')}>
              <FormField
                name="button_style"
                label={getFieldLabel('button_style')}
                options={{
                  large: __('Large'),
                  medium: __('Medium'),
                  small: __('Small'),
                }}
                controlProps={{
                  type: 'radio',
                }}
              />
              <FormField
                name="show_user_photo"
                label={getFieldLabel('show_user_photo')}
                _label={__('Display Telegram user profile photo beside button')}
                controlProps={{
                  id: 'show_user_photo',
                  type: 'checkbox',
                }}
              />
              <FormField
                name="corner_radius"
                label={getFieldLabel('corner_radius')}
                desc={__('Leave empty for default')}
                parse={(val) => {
                  if(!parseInt(val)) return '';
                  return Math.min(Math.round(val), 20);
                }}
                controlProps={{
                  id: 'corner_radius',
                  type: 'number',
                  size: 'sm',
                  min: 1,
                  onBlur: () => null, // avoid validation on blur
                  style: {maxWidth:'100px'},
                }}
              />
              <FormField
                name="show_if_user_is"
                label={getFieldLabel('show_if_user_is')}
                desc={__('Who can see the login button')}
                options={select_opts.show_if_user_is}
                controlProps={{
                  id: 'show_if_user_is',
                  type: 'select',
                  style: {width:'auto'},
                }}
              />
              <FormField
                name="hide_on_default"
                label={getFieldLabel('hide_on_default')}
                _label={__('Hide the button on default WordPress login/register page')}
                controlProps={{
                  id: 'hide_on_default',
                  type: 'checkbox',
                }}
              />
            </Card>

            <Card title={'Error Message'}>
              <FormField
                name="show_message_on_error"
                label={getFieldLabel('show_message_on_error')}
                _label={__('Display an error message if Telegram is blocked by user\'s ISP')}
                controlProps={{
                  id: 'show_message_on_error',
                  type: 'checkbox',
                }}
              />

              {values.show_message_on_error && <FormField
                name="custom_error_message"
                label={getFieldLabel('custom_error_message')}
                desc={__('Leave empty for default')}
                controlProps={{
                  id: 'custom_error_message',
                  type: 'text',
                  size: 'sm',
                  onBlur: () => null, // avoid validation on blur
                }}
              />}
            </Card>
            <div className="mt-2">
              <Button type="submit" disabled={submitting}>{__('Save Changes')}</Button>
              {submitFailed ? <span className="ml-2 text-danger">{submitError}</span> : null }
              {pristine && submitSucceeded ? <span className="ml-2 text-success">{__('Changes saved successfully.')}</span> : null }
            </div>
          </Form>
        );
      }}
    />
  );
}