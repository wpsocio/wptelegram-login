import React from 'react';
import { Form, Row } from 'react-bootstrap';

export default (props) => {
  return (
    <fieldset className="border pr-3 pb-3 pl-3 my-4">
      {
        props.title ?
        <Form.Group as={Row} className="border-bottom bg-light">
            <h3 className="m-0 p-2 small font-weight-normal text-secondary text-uppercase">{props.title}</h3>
        </Form.Group> : null
      }
      {props.children}
    </fieldset>
  );
}