import React from 'react';
import { __, sprintf } from '../i18n';
import { Card } from 'react-bootstrap';

export default () => {

  const { location: { host } } = window;

  return (
    <Card border="dark" className="mw-100 p-0">
      <Card.Header className="text-white bg-dark">{__('INSTRUCTIONS!')}</Card.Header>
      <Card.Body>
        <ol>
          <li dangerouslySetInnerHTML={{ __html: sprintf( __( 'Create a Bot by sending %1$s command to %2$s.' ), '<b><code>/newbot</code></b>', '<a href="https://t.me/BotFather"  target="_blank">@BotFather</a>' ) }}></li>
          <li>{sprintf( __( 'After completing the steps %s will provide you the Bot Token.' ), '@BotFather' )}</li>
          <li dangerouslySetInnerHTML={{ __html: __( 'Copy the token and paste into the Bot Token field below.' ) + ' ' + sprintf( __( 'For ease, use %s' ), '<a href="https://desktop.telegram.org" target="_blank">Telegram Desktop</a>' ) }}></li>
          <li dangerouslySetInnerHTML={{ __html: sprintf( __( 'Send %1$s command to %2$s, select your bot and then send %3$s' ), '<b><code>/setdomain</code></b>', '<a href="https://t.me/BotFather"  target="_blank">@BotFather</a>', '<b><code>' + host + '</code></b>' ) }}></li>
          <li>{__( 'Test your bot token below and fill in the bot username if not filled automatically.' )}</li>
          <li>{sprintf( __( 'Hit %s below' ), __( 'Save Changes' ) )}</li>
          <li>{__( 'That\'s it. You are ready to rock :)' )}</li>
        </ol>
      </Card.Body>
    </Card>
  );
}