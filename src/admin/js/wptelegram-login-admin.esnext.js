'use strict';

(function($) {
	const app1 = {};

	app1.configure = () => {
		app1.settings_block = $('#wptelegram-login-settings');
	};

	app1.init = () => {
		app1.configure();
		app1.remove_junk();
	};

	app1.remove_junk = () => {
		if (app1.settings_block.length) {
			app1.settings_block.siblings().remove();
		}
	};

	$(app1.init);
})(jQuery);
