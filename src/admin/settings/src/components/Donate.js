import React from 'react';

const Donate = () => (
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
		<input type="hidden" name="cmd" value="_s-xclick" />
		<input type="hidden" name="hosted_button_id" value="9CK738NLFADA8" />
		<input
			type="image"
			src="https://www.paypalobjects.com/en_GB/i/btn/btn_donateCC_LG.gif"
			border="0"
			name="submit"
			title="PayPal - The safer, easier way to pay online!"
			alt="Donate with PayPal button"
		/>
		<img alt="" border="0" src="https://www.paypal.com/en_IN/i/scr/pixel.gif" width="1" height="1" />
	</form>
);

export default Donate;
