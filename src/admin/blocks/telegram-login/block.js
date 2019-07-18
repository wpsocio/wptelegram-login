//  Import CSS.
import './editor.scss';

const el =  wp.element.createElement;
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.editor;
const {
	PanelBody,
	RadioControl,
	ToggleControl,
	TextControl,
	SelectControl,
} = wp.components;

const { blocks: {assets: { login_image_url, login_avatar_url }, select_opts} } = wptelegram_login;

const getFinalOutput = (attributes, className = null) => {
	const {
		button_style,
		show_user_photo,
		corner_radius
	} = attributes;

	const button_width = 'small' === button_style ? '100px' : 'medium' === button_style ? '150px' : null;
	const avatar_width = 'small' === button_style ? '20px' : 'medium' === button_style ? '30px' : null;

	const avatar = 'on' === show_user_photo ? <img src={login_avatar_url} style={{ width: avatar_width }} /> : null;

	return (
		<div className={className}>
			<img
				src={login_image_url}
				style={{ borderRadius: corner_radius + 'px', width: button_width }}
			/>
			{avatar}
		</div>
	);
};

registerBlockType( 'wptelegram/login', {
	title: __( 'WP Telegram Login' ),
	icon: 'smartphone',
	category: 'widgets',
	attributes: {
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
	},
	edit({ attributes, setAttributes, className }) {

		const {
			button_style,
			show_user_photo,
			corner_radius,
			show_if_user_is,
		} = attributes;

		const controls = [
			<InspectorControls>
				<PanelBody title={__( 'Button Settings' )}>
					<RadioControl
						label={__( 'Button Style' )}
						selected={button_style}
						onChange={newStyle => setAttributes({ button_style: newStyle })}
						options={[
							{ label: 'Large', value: 'large' },
							{ label: 'Medium', value: 'medium' },
							{ label: 'Small', value: 'small' }
						]}
					/>
					<ToggleControl
						label={__( 'Show User Photo' )}
						checked={'on' === show_user_photo}
						onChange={() => setAttributes({ show_user_photo: 'on' === show_user_photo ? 'off' : 'on' })}
					/>
					<TextControl
						label={__( 'Corner Radius' )}
						value={corner_radius}
						onChange={newRadius => setAttributes({ corner_radius: newRadius })}
						type="number"
						min="0"
						max="20"
					/>
					<SelectControl
						label={ __( 'Show if user is' ) }
						value={ show_if_user_is }
						onChange={ value => setAttributes({ show_if_user_is: value }) }
						options={ select_opts.show_if_user_is }
					/>
				</PanelBody>
			</InspectorControls>
		];

		return [
			controls,
			getFinalOutput(attributes, className)
		];
	},

	save(props) {

		return getFinalOutput(props.attributes);
	}
} );
