import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RadioControl, ToggleControl, TextControl, SelectControl } from '@wordpress/components';
//  Import CSS.
import './editor.scss';

const {
	blocks: {
		assets: { login_image_url, login_avatar_url },
		select_opts,
	},
} = window.wptelegram_login;

const getFinalOutput = (attributes, className = null) => {
	const { button_style, show_user_photo, corner_radius } = attributes;

	let button_width = null;
	if ('small' === button_style) {
		button_width = '100px';
	} else if ('medium' === button_style) {
		button_width = '150px';
	}

	let avatar_width = null;
	if ('small' === button_style) {
		avatar_width = '20px';
	} else if ('medium' === button_style) {
		avatar_width = '30px';
	}

	/* eslint-disable-next-line jsx-a11y/alt-text */
	const avatar = 'on' === show_user_photo ? <img src={login_avatar_url} style={{ width: avatar_width }} /> : null;

	return (
		<div className={className} key="output">
			{/* eslint-disable-next-line jsx-a11y/alt-text */}
			<img src={login_image_url} style={{ borderRadius: corner_radius + 'px', width: button_width }} />
			{avatar}
		</div>
	);
};

const blockAttributes = {
	button_style: {
		type: 'string',
		default: 'large',
	},
	show_user_photo: {
		type: 'string',
		default: 'on',
	},
	corner_radius: {
		type: 'string',
		default: '20',
	},
	show_if_user_is: {
		type: 'string',
		default: '0',
	},
};

registerBlockType('wptelegram/login', {
	title: __('WP Telegram Login'),
	icon: 'smartphone',
	category: 'wptelegram',
	attributes: blockAttributes,
	edit({ attributes, setAttributes, className }) {
		const { button_style, show_user_photo, corner_radius, show_if_user_is } = attributes;

		const controls = [
			<InspectorControls key="controls">
				<PanelBody title={__('Button Settings')}>
					<RadioControl
						label={__('Button Style')}
						selected={button_style}
						onChange={(newStyle) => setAttributes({ button_style: newStyle })}
						options={[
							{ label: 'Large', value: 'large' },
							{ label: 'Medium', value: 'medium' },
							{ label: 'Small', value: 'small' },
						]}
					/>
					<ToggleControl
						label={__('Show User Photo')}
						checked={'on' === show_user_photo}
						onChange={() =>
							setAttributes({
								show_user_photo: 'on' === show_user_photo ? 'off' : 'on',
							})
						}
					/>
					<TextControl
						label={__('Corner Radius')}
						value={corner_radius}
						onChange={(newRadius) => setAttributes({ corner_radius: newRadius })}
						type="number"
						min="0"
						max="20"
					/>
					<SelectControl
						label={__('Show if user is')}
						value={show_if_user_is}
						onChange={(value) => setAttributes({ show_if_user_is: value })}
						options={select_opts.show_if_user_is}
					/>
				</PanelBody>
			</InspectorControls>,
		];

		return [controls, getFinalOutput(attributes, className)];
	},

	save(props) {
		return getFinalOutput(props.attributes);
	},
	deprecated: [
		{
			attributes: blockAttributes,
			save(props) {
				return getFinalOutput(props.attributes);
			},
		},
	],
});
