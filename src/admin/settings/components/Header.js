import React from 'react';
import { __ } from '../i18n';
import { Card } from 'react-bootstrap';
import SocialIcons from './SocialIcons';

export default () => {

  const { wptelegram_login: { title, version, settings: { assets } } } = window;

  return (
    <Card border="info" className="mw-100 p-0">
      <Card.Header className="text-nowrap">
        <img
          src={assets.logo_url}
          width="30"
          height="30"
          className="d-inline-block align-middle mr-2"
          alt={title}
        />
        <div className="d-inline-block">
          <h6 className="d-inline-block">{title}</h6>
          {' '}
          <small className="text-secondary font-italic">v{version}</small>
        </div>
      </Card.Header>
      <Card.Body className="pb-1">
        {/* <Card.Title>{title}</Card.Title> */}
        {/* <Card.Subtitle className="mb-2 text-muted">v.{version}</Card.Subtitle> */}
        <Card.Text className="text-secondary font-italic text-justify">
          {__('With this plugin, you can let the users login to your website with their Telegram and make it simple for them to get connected.')}
        </Card.Text>
        {/* <Button variant="primary">Go somewhere</Button> */}
      </Card.Body>
      <Card.Body className="pb-1">
        <SocialIcons />
      </Card.Body>
    </Card>
  );
};