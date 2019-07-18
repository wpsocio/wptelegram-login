import React from 'react';
import { Form, Col, Row, InputGroup, ListGroup } from 'react-bootstrap';
import HelpText from './HelpText';
import { Field as RFF_Field } from 'react-final-form';
import { formatValue } from '../fields';

export default (props) => {
  const { label, desc, after, controlProps, options = {} } = props;

  let field;

  if ('radio' === controlProps.type) {
    field = Object.keys(options).map(key => controlledField({...props, type: controlProps.type, value: key, _label: options[key]}));
  } else if ('checkbox' === controlProps.type) {
    field = controlledField({...props, type: controlProps.type});
  } else {
    field = controlledField(props);
  }

  return (
    <FieldWrapper {...{ label, desc, after, controlProps }}>
      {field}
    </FieldWrapper>
  );
};

const controlledField = (props) => {
  return (
    <RFF_Field key={props.value || null} parse={formatValue} {...props} component={fieldComponent} />      
  );
};

const fieldComponent = props => {

  const { input, meta, controlProps, _label, options = {}, inputGroupProps } = props;

  let field;

  if (['radio','checkbox'].includes(controlProps.type)) {
    
    field = (
      <Form.Check
        {...input}
        {...controlProps}
        inline
        // checked={input.value === key}
        label={_label}
        id={'radio' === controlProps.type ? `${input.name}_${input.value}` : input.name}
        value={input.value}
      />
    );
  } else if ('select' === controlProps.type) {
    field = (
      <Form.Control
        {...input}
        {...controlProps}
        as="select"
        // isValid={meta.touched && !meta.error}
        isInvalid={meta.touched && (meta.error || meta.submitError)}
      >
        {Object.keys(options).map(key => (
          <option key={key} value={key}>{options[key]}</option>
        ))}
      </Form.Control>
    );
  } else {
    field = (
      <Form.Control
        {...input}
        {...controlProps}
        isValid={meta.touched && !(meta.error || meta.submitError)}
        isInvalid={meta.touched && (meta.error || meta.submitError)}
      />
    );
  }

  if (inputGroupProps) {
    return inputGroup(field, props);
  }
  return (
    <div>
      {field}
      <Form.Control.Feedback type="invalid">
        {meta.error || meta.submitError}
      </Form.Control.Feedback>
    </div>
  );
};

const FieldWrapper = ({ label, desc, before, after, controlProps, children }) => {
  return (
    <ListGroup.Item>
      <Form.Group as={Row}>
        <Form.Label column sm={3} htmlFor={controlProps.id ? controlProps.id : null}>
          {label || null}
        </Form.Label>
        <Col sm={9}>
          {before}
          {children}
          {desc ? <HelpText>{desc}</HelpText> : null}
          {after}
        </Col>
      </Form.Group>
    </ListGroup.Item>
  );
};

const inputGroup = (field, props) => {
  const { inputGroupProps: { prepend, append, style }, meta } = props;

  return (
    <InputGroup style={style}>
      {prepend && <InputGroup.Prepend>{prepend(InputGroup)}</InputGroup.Prepend>}
      {field}
      {append && <InputGroup.Append>{append(InputGroup)}</InputGroup.Append>}
      <Form.Control.Feedback type="invalid">
        {meta.error || meta.submitError}
      </Form.Control.Feedback>
    </InputGroup>
  );
};