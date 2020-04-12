import React from 'react';
import { Card, ListGroup } from 'react-bootstrap';
import { title } from 'plugin-data';

import { __, sprintf } from '../i18n';
import { Code } from './';

const WidgetInfoCard = () => {
	return (
		<Card border="dark" className="mw-100 p-0 text-center">
			<Card.Header as="h6" className="text-center">
				{__('Widget Info')}
			</Card.Header>
			<ListGroup variant="flush">
				<ListGroup.Item className="text-justify">
					{sprintf(
						__('Goto %1$s and click/drag %2$s and place it where you want it to be.'),
						__('Appearance') + ' > ' + __('Widgets'),
						title
					)}
				</ListGroup.Item>
				<ListGroup.Item className="text-justify">
					{__('Alternately, you can use the below shortcode or the block available in block editor.')}
				</ListGroup.Item>
				<ListGroup.Item className="font-weight-bold text-secondary">
					{__('Inside page or post content:')}
				</ListGroup.Item>
				<ListGroup.Item variant="light" className="text-monospace text-left">
					<Code>{'[wptelegram-login button_style="large" show_user_photo="1" corner_radius="15"]'}</Code>
				</ListGroup.Item>
				<ListGroup.Item className="font-weight-bold text-secondary">
					{__('Inside the theme templates')}
				</ListGroup.Item>
				<ListGroup.Item variant="light" className="text-monospace text-left">
					<Code>
						{"<?php\nif ( function_exists( 'wptelegram_login' ) ) {\n    wptelegram_login();\n}\n?>"}
					</Code>
					<br />
					<span className="font-weight-bold text-secondary">{__('or')}</span>
					<br />
					<Code>
						{
							'<?php\necho do_shortCode( \'[wptelegram-login button_style="small" show_user_photo="0" show_if_user_is="logged_in"]\' );\n?>'
						}
					</Code>
				</ListGroup.Item>
			</ListGroup>
			<Card.Footer>
				<span role="img" aria-label="Smile">
					🙂
				</span>
			</Card.Footer>
		</Card>
	);
};

export default WidgetInfoCard;
