import React from 'react';
import { Card, ListGroup } from 'react-bootstrap';
import { __, sprintf } from '../i18n';

export default () => {
  const { wptelegram_login: { title } } = window;

  return (
    <Card border="info" className="mw-100 p-0 text-center">
      <Card.Header as="h6" className="text-center">{title}</Card.Header>
      <Card.Body>
        <Card.Text className="text-justify">
          {__('Let the users login to your WordPress website with their Telegram and make it simple for them to get connected and let them receive their email notifications on Telegram.')}
        </Card.Text>
      </Card.Body>
      <ListGroup variant="flush">
        <ListGroup.Item>
          <div><span>{sprintf( __( 'Do you like %s?' ), title )}</span></div>
          <div><a href="https://wordpress.org/support/plugin/wptelegram-login/reviews/#new-post" target="_blank" className="text-center text-info ml-1" style={{textDecoration: 'none'}}><span style={{color:'orange',fontSize:'1.5rem'}}>★★★★★</span></a></div>
        </ListGroup.Item>
        <ListGroup.Item>
          <div><span>{__( 'Need help?' )}</span></div>
          <div><span style={{fontWeight: '600'}}>{__( 'Get LIVE support on Telegram' )}</span></div>
        </ListGroup.Item>
        <ListGroup.Item action href="https://t.me/WPTelegramChat" target="_blank" variant="primary">
          <span className="font-weight-bold font-italic">@WPTelegramChat</span>
        </ListGroup.Item>
      </ListGroup>
      <Card.Footer>🙂</Card.Footer>
    </Card>
  );
}