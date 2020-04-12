import React from 'react';

const Code = ({ children }) => {
	return (
		<code
			style={{
				whiteSpace: 'pre-wrap',
				backgroundColor: '#fdfdfe',
			}}
		>
			{children}
		</code>
	);
};

export default Code;
