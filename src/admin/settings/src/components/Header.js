import React from 'react';
import { Card } from 'react-bootstrap';
import { title, version, settings } from 'plugin-data';

import { __ } from '@wordpress/i18n';
import { SocialIcons } from './';

const { assets } = settings;

const Header = () => {
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
					<h6 className="d-inline-block">{title}</h6>{' '}
					<small className="text-secondary font-italic">v{version}</small>
				</div>
			</Card.Header>
			<Card.Body className="pb-1">
				<Card.Text className="text-secondary font-italic text-justify">
					{__(
						'With this plugin, you can let the users login to your website with their Telegram and make it simple for them to get connected.'
					)}
				</Card.Text>
			</Card.Body>
			<Card.Body className="pb-1">
				<SocialIcons />
			</Card.Body>
		</Card>
	);
};

export default Header;
