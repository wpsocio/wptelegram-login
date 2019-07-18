import React from 'react';
import { Card, ListGroup } from 'react-bootstrap';

export default ({title, children}) => {
  return (
    <Card className="mw-100 p-0">
      {title ? <Card.Header>{title}</Card.Header> : null}
      <ListGroup variant="flush">
        {children}
      </ListGroup>
    </Card>
  );
}