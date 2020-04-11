import React from 'react';
import { Card as BSCard, ListGroup } from 'react-bootstrap';

const Card = ({ title, children }) => {
	return (
		<BSCard className="mw-100 p-0">
			{title ? <BSCard.Header>{title}</BSCard.Header> : null}
			<ListGroup variant="flush">{children}</ListGroup>
		</BSCard>
	);
};

export default Card;
