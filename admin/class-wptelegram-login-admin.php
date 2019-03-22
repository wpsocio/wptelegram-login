<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://t.me/manzoorwanijk
 * @since      1.0.0
 *
 * @package    WPTelegram_Login
 * @subpackage WPTelegram_Login/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WPTelegram_Login
 * @subpackage WPTelegram_Login/admin
 * @author     Manzoor Wani <@manzoorwanijk>
 */
class WPTelegram_Login_Admin {

	/**
	 * Title of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $title    Title of the plugin
	 */
	protected $title;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The suffix to be used for JS and CSS files
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $suffix    The suffix to be used for JS and CSS files
	 */
	private $suffix;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.0
	 * @param 	string    $title		Title of the plugin
	 * @param	string    $plugin_name	The name of the plugin.
	 * @param	string    $version		The version of this plugin.
	 */
	public function __construct( $title, $plugin_name, $version ) {
		$this->title		= $title;
		$this->plugin_name	= $plugin_name;
		$this->version		= $version;

		// Use minified libraries if SCRIPT_DEBUG is turned off
		$this->suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, WPTELEGRAM_LOGIN_URL . '/admin/css/wptelegram-login-admin' . $this->suffix . '.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, WPTELEGRAM_LOGIN_URL . '/admin/js/wptelegram-login-admin' . $this->suffix . '.js', array( 'jquery' ), $this->version, false );

		// script localization
		$translation_array = array(
			'could_not_connect'    => __( 'Could not connect', 'wptelegram-login' ),
			'empty_bot_token'      => __( 'Bot Token is empty', 'wptelegram-login' ),
			'empty_bot_username'   => __( 'Bot username is empty', 'wptelegram-login' ),
			'error'                => __( 'Error:', 'wptelegram-login' ),
			'login_image_url'      => WPTELEGRAM_LOGIN_URL . '/admin/icons/telegram-login.svg',
			'login_avatar_url'     => WPTELEGRAM_LOGIN_URL . '/admin/icons/telegram-login-avatar.svg',
			'show_if_user_is_opts' => self::show_if_user_is_options_cb( true ),
		);
		wp_localize_script(
			$this->plugin_name,
			'wptelegram_login_I18n',
			$translation_array
		);
	}

 	/**
	 * Enqueue assets for the Gutenberg block
	 *
	 * @since    x.y.z
	 */
	public function enqueue_block_editor_assets() {

		wp_enqueue_script(
			$this->plugin_name . '-block',
			WPTELEGRAM_LOGIN_URL . '/admin/blocks/blocks-build' . $this->suffix . '.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
			$this->version,
			true
		);

		wp_enqueue_style(
			$this->plugin_name . '-block',
			WPTELEGRAM_LOGIN_URL . '/admin/blocks/blocks-build' . $this->suffix . '.css',
			array( 'wp-edit-blocks' ),
			$this->version
		);
	}

	/**
	 * Create our widget
	 *
	 * @since    1.0.0
	 */
	public function register_widgets() {
		register_widget( 'WPTelegram_Login_Widget_Primary' );
	}

	/**
	 * Build Options page
	 *
	 * @since    1.0.0
	 */
	public function create_options_page() {

		$box = array(
			'id'           => $this->plugin_name,
			'title'        => esc_html( $this->title ),
			'object_types' => array( 'options-page' ),
			'option_key'   => $this->plugin_name,
			'icon_url'     => WPTELEGRAM_LOGIN_URL . '/admin/icons/icon-16x16-white.svg',
			'capability'   => 'manage_options',
			'message_cb'   => array( $this, 'custom_settings_messages' ),
		);

		if ( defined( 'WPTELEGRAM_LOADED' ) && WPTELEGRAM_LOADED ) {
			$box['menu_title'] = esc_html__( 'Telegram Login', 'wptelegram-login' );
			$box['parent_slug'] = 'wptelegram';
		}
		$cmb2 = new_cmb2_box( $box );

		$fields = array(
			array(
				'name'       => __( 'Telegram Options', 'wptelegram-login' ),
				'type'       => 'title',
				'id'         => 'tg_guide_title',
				'before_row' => array( $this, 'render_header' ),
				'after'      => array( __CLASS__, 'get_telegram_guide' ),
			),
			array(
				'name'            => __( 'Bot Token', 'wptelegram-login' ),
				'desc'            => self::get_button_html( 'bot_token' ) . '<br>' . __( 'Please read the instructions above', 'wptelegram-login' ),
				'id'              => 'bot_token',
				'type'            => 'text_medium',
				'sanitization_cb' => array( $this, 'sanitize_values' ),
				'after_field'     => array( __CLASS__, 'render_after_field' ),
				'attributes'      => array(
					'required' => 'required',
				),
			),
			array(
				'name'				=> __( 'Bot Username', 'wptelegram-login' ),
				'desc'				=> sprintf( __( 'Telegram Bot username (without %s).', 'wptelegram-login' ), '<code>@</code>' ),
				'id'              => 'bot_username',
				'after'           => sprintf( __( 'Use %s above to set automatically.', 'wptelegram-login' ), '<b>' . __( 'Test Token', 'wptelegram-login' ) . '</b>' ),
				'type'            => 'text_medium',
				'sanitization_cb' => array( $this, 'sanitize_values' ),
				'before_field'    => '<code>@</code>',
				'after_field'     => array( __CLASS__, 'render_after_field' ),
				'attributes'      => array(
					'required' => 'required',
				),
			),
			array(
				'name'	=> __( 'Login Options', 'wptelegram-login' ),
				'type'	=> 'title',
				'id'	=> 'login_options',
			),
			array(
				'name'			=> __( 'Sign up', 'wptelegram-login' ),
				'desc'			=> __( 'Disable Sign up', 'wptelegram-login' ),
				'after'			=> '<br>' . __( 'If checked, only the existing users who have connected their Telegram will be able to login.', 'wptelegram-login' ),
				'id'			=> 'disable_signup',
				'type'			=> 'checkbox',
			),
			array(
				'name'			=> __( 'User Role', 'wptelegram-login' ),
				'desc'			=> __( 'The default role to assign for the new users', 'wptelegram-login' ),
				'id'			=> 'user_role',
				'type'			=> 'select',
				'default'		=> get_option( 'default_role' ),
				'options_cb'	=> array( __CLASS__, 'user_role_options_cb' ),
	            'attributes'	=> array(
	                'data-the-conditional-id'		=> 'disable_signup',
	                'data-the-conditional-value'	=> 'off',
	            ),
			),
			array( // fake field for redirect_url
				'name'			=> '',
				'id'			=> 'redirect_url',
				'type'			=> 'text_url',
				'protocols'		=> array( 'http', 'https' ),
				'render_row_cb'	=> '__return_empty_string', // fake callback
			),
			array(
				'name'		=> __( 'Redirect to', 'wptelegram-login' ),
				'id'		=> 'redirect_to',
				'type'		=> 'radio',
				'options'	=> array(
					'default'		=> __( 'Default', 'wptelegram-login' ),
					'homepage'		=> __( 'Homepage', 'wptelegram-login' ),
					'current_page'	=> __( 'Current page', 'wptelegram-login' ),
					'custom_url'	=> __( 'Custom URL', 'wptelegram-login' ),
				),
				'default'	=> 'default',
				'after'		=> '<input type="url" class="regular-text" name="redirect_url" id="redirect_url" value="' . WPTG_Login()->options()->get('redirect_url') . '" placeholder="' . __( 'Custom URL', 'wptelegram-login' ) . '"><p class="cmb2-metabox-description">' . __( 'Redirect location after login', 'wptelegram-login' ) . '</p>',
			),
			array(
				'name'				=> __( 'Avatar URL Meta Key', 'wptelegram-login' ),
				'desc'				=> __( 'The user meta key to be used to save Telegram photo URL.', 'wptelegram-login' ),
				'id'				=> 'avatar_meta_key',
				'type'				=> 'text_medium',
				'default'			=> 'wptg_login_avatar',
				'sanitization_cb'	=> array( $this, 'sanitize_values' ),
			),
			array(
				'name'	=> __( 'Button Options', 'wptelegram-login' ),
				'type'	=> 'title',
				'id'	=> 'login_btn_options',
			),
			array(
				'name'		=> __( 'Button Style', 'wptelegram-login' ),
				'id'		=> 'button_style',
				'type'		=> 'radio',
				'options'	=> array(
					'large'		=> __( 'Large', 'wptelegram-login' ),
					'medium'	=> __( 'Medium', 'wptelegram-login' ),
					'small'		=> __( 'Small', 'wptelegram-login' ),
				),
				'default'	=> 'large',
			),
			array(
				'name' => __( 'Show User Photo', 'wptelegram-login' ),
				'desc' => __( 'Display Telegram user profile photo beside button', 'wptelegram-login' ),
				'id'   => 'show_user_photo',
				'type' => 'checkbox',
			),
			array(
				'name'				=> __( 'Corner Radius', 'wptelegram-login' ),
				'desc'				=> __( 'Leave empty for default', 'wptelegram-login' ),
				'id'				=> 'corner_radius',
				'type'				=> 'text_small',
				'attributes'		=> array(
					'type'		=> 'number',
					'pattern'	=> '\d*',
				),
			),
			array(
				'name'			=> __( 'Show if user is', 'wptelegram-login' ),
				'desc'			=> __( 'Who can see the login button', 'wptelegram-login' ),
				'id'			=> 'show_if_user_is',
				'type'			=> 'select',
				'default'		=> 'logged_out',
				'options_cb'	=> array( __CLASS__, 'show_if_user_is_options_cb' ),
			),
			array(
				'name'			=> __( 'Hide on default login', 'wptelegram-login' ),
				'desc'			=> __( 'Hide the button on default WordPress login/register page', 'wptelegram-login' ),
				'id'			=> 'hide_on_default',
				'type'			=> 'checkbox',
			),
			array(
				'name'	=> __( 'Error Message', 'wptelegram-login' ),
				'type'	=> 'title',
				'id'	=> 'login_error_message',
			),
			array(
				'name' => __( 'Show error message', 'wptelegram-login' ),
				'desc' => __( 'Display an error message if Telegram is blocked by user\'s ISP', 'wptelegram-login' ),
				'id'   => 'show_message_on_error',
				'type' => 'checkbox',
			),
			array(
				'name'			=> __( 'Error message text', 'wptelegram-login' ),
				'desc'			=> __( 'Leave empty for default', 'wptelegram-login' ),
				'id'			=> 'custom_error_message',
				'type'			=> 'text',
	            'attributes'	=> array(
	                'data-the-conditional-id'		=> 'show_message_on_error',
	                'data-the-conditional-value'	=> 'on',
	            ),
			),
			array(
				'name'	=> __( 'Widget Info', 'wptelegram-login' ),
				'type'	=> 'title',
				'id'	=> 'tg_shortcode_title',
				'after_row' => array( $this, 'render_shortcode_guide' ),
			),
		);
		foreach ( $fields as $field ) {
			$cmb2->add_field( $field );
		}
	}

	public static function user_role_options_cb(){
		
		$data = array();

		foreach ( get_editable_roles() as $role_name => $role_info ) {
			$data[ $role_name ] = translate_user_role( $role_info['name'] );
		}
		return $data;
	}

	public static function show_if_user_is_options_cb( $for_block_editor = false ){
		
		$data = array(
			'0'          => __( 'Any', 'wptelegram-login' ),
			'logged_out' => __( 'Logged out', 'wptelegram-login' ),
			'logged_in'  => __( 'Logged in', 'wptelegram-login' ),
		);

		$data = array_merge( $data, self::user_role_options_cb() );

		if ( $for_block_editor ) {
			$_data = array();
			foreach ( $data as $key => $label ) {
				$_data[] = array(
					'value' => $key,
					'label' => $label,
				);
			}

			return $_data;
		}

		return $data;
	}

	/**
	 * Handles sanitization for the fields
	 *
	 * @param  mixed      $value      The unsanitized value from the form.
	 * @param  array      $field_args Array of field arguments.
	 * @param  CMB2_Field $field      The field object
	 *
	 * @return mixed                  Sanitized value to be stored.
	 */
	public function sanitize_values( $value, $field_args, $field ){
		
		$status = '';
		$value = sanitize_text_field( $value );
		switch ( $field->id() ) {
			case 'bot_token':
				if ( empty( $value ) ) {
					$status = 'empty';
				} elseif ( ! preg_match( '/\A\d{9}:[\w-]{35}\Z/', $value ) ) {
					$status = 'invalid';
				}
				break;
			case 'bot_username':
				if ( empty( $value ) ) {
					$status = 'empty';
				} elseif ( ! preg_match( '/\A[a-z]\w{3,30}[^\W_]\Z/i', $value ) ) {
					$status = 'invalid';
				}
				break;
			case 'avatar_meta_key':
				$value = sanitize_key( $value );
				break;
		}

		if ( ! empty( $status ) ) {
			$value = $field->value();
			$transient = 'wptelegram_login_cmb2_invalid_fields';
			$invalid_fields = get_transient( $transient );
			/**
			 * avoid E_WARNING in latest PHP versions
			 * for inserting elements into string or boolean as array
			 */
			if ( empty( $invalid_fields ) ) {
				$invalid_fields = array();
			}
			$invalid_fields[ $field->id() ] = $status;
			set_transient( $transient, $invalid_fields, 30 );
		}
		return $value;
	}

	/**
	 * Callback to define the optionss-saved message.
	 *
	 * @param CMB2  $cmb The CMB2 object.
	 * @param array $args {
	 *     An array of message arguments
	 *
	 *     @type bool   $is_options_page Whether current page is this options page.
	 *     @type bool   $should_notify   Whether options were saved and we should be notified.
	 *     @type bool   $is_updated      Whether options were updated with save (or stayed the same).
	 *     @type string $setting         For add_settings_error(), Slug title of the setting to which
	 *                                   this error applies.
	 *     @type string $code            For add_settings_error(), Slug-name to identify the error.
	 *                                   Used as part of 'id' attribute in HTML output.
	 *     @type string $message         For add_settings_error(), The formatted message text to display
	 *                                   to the user (will be shown inside styled `<div>` and `<p>` tags).
	 *                                   Will be 'Settings updated.' if $is_updated is true, else 'Nothing to update.'
	 *     @type string $type            For add_settings_error(), Message type, controls HTML class.
	 *                                   Accepts 'error', 'updated', '', 'notice-warning', etc.
	 *                                   Will be 'updated' if $is_updated is true, else 'notice-warning'.
	 * }
	 */
	public function custom_settings_messages( $cmb, $args ){
		if ( ! empty( $args['should_notify'] ) ) {

			if ( $args['is_updated'] ) {

				// Modify the updated message.
				$args['message'] = esc_html__( 'Settings updated', 'wptelegram-login' );
			}

			$transient = 'wptelegram_login_cmb2_invalid_fields';
			$invalid_fields = get_transient( $transient );
			
			if ( ! empty( $invalid_fields ) ) {
				$args['type'] = 'error';
				foreach ( (array) $invalid_fields as $field => $status ) {
					$field_name = $cmb->get_field(
						array(
							'id' => $field,
							'cmb_id' => $cmb->prop( 'id' ),
						)
					)->args( 'name' );

					if ( 'empty' == $status ) {
						$args['message'] = sprintf( esc_html__( '%s is empty', 'wptelegram-login' ), $field_name );
					} else {
						$args['message'] = sprintf( esc_html__( 'Invalid %s', 'wptelegram-login' ), $field_name );
					}
					
					add_settings_error( $args['setting'], $args['code'], $args['message'], $args['type'] );
				}
			} else {
				add_settings_error( $args['setting'], $args['code'], $args['message'], $args['type'] );
			}
			delete_transient( $transient );
		}
	}
	
	/**
	 * Render the instructions related to shortcode
	 * @param  object $field_args Current field args
	 * @param  object $field      Current field object
	 */
	public function render_shortcode_guide( $field_args, $field ){
		?>
		<div class="cmb-row">
			<p><?php printf( __( 'Goto %1$s and click/drag %2$s and place it where you want it to be.', 'wptelegram-login' ), '<b>' . __( 'Appearance', 'wptelegram-login' ) . ' &gt; <a href="' . admin_url( 'widgets.php' ) . '">' . __( 'Widgets', 'wptelegram-login' ) . '</a></b>', '<b>' . $this->title . '</b>' ); ?></p>
			<p><?php echo __( 'Alternately, you can use the below shortcode or the block available in block editor.', 'wptelegram-login' ); ?></p>
			<h4><?php echo __( 'Inside page or post content:', 'wptelegram-login' ); ?></h4>
			<p><code><?php echo esc_html( '[wptelegram-login button_style="large" show_user_photo="on" corner_radius="15"]' ); ?></code></p>
			<h4><?php _e( 'Inside the theme templates', 'wptelegram-login' ); ?></h4>
			<?php $pre = "<?php\nif ( function_exists( 'wptelegram_login' ) ) {\n    wptelegram_login();\n}\n?>"; ?>
			<p><pre><?php echo esc_html( $pre ); ?></pre></p>
			<h5><?php _e( 'or', 'wptelegram-login' ); ?></h5>
			<?php $code = '<?php echo do_shortcode( \'[wptelegram-login button_style="small" show_user_photo="off" show_if_user_is="logged_in"]\' ); ?>'; ?>
			<p><code><?php echo esc_html( $code ); ?></code></p>
		</div>
		<?php
	}

	/**
	 * Render the settings page header
	 * @param  object $field_args Current field args
	 * @param  object $field      Current field object
	 */
	public function render_header( $field_args, $field ) {

		$plugin_url = WPTELEGRAM_LOGIN_URL;
		$text_domain = 'wptelegram-login';

		include_once WPTELEGRAM_LOGIN_DIR . '/admin/partials/wptelegram-login-admin-header.php';
		?>
		<div class="cmb-row wptelegram-header-desc">
			<p><?php echo __( 'With this plugin, you can let the users login to your website with their Telegram and make it simple for them to get connected.', 'wptelegram-login' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Output the Telegram Instructions
	 * @param  object $field_args Current field args
	 * @param  object $field      Current field object
	 */
	public static function get_telegram_guide( $field_args, $field ) {
		$parts = parse_url( site_url() );
		$domain = $parts['host'];
		?>
		<p style="color:#f10e0e;"><b><?php echo __( 'INSTRUCTIONS!','wptelegram-login'); ?></b></p>
		 <ol style="list-style-type: decimal;">
		 	<li><?php echo sprintf( __( 'Create a Bot by sending %1$s command to %2$s.', 'wptelegram-login' ), '<code>/newbot</code>', '<a href="https://t.me/BotFather"  target="_blank">@BotFather</a>' );
            ?></li>
		 	<li><?php echo sprintf( __( 'After completing the steps %s will provide you the Bot Token.', 'wptelegram-login' ), '@BotFather' );?></li>
		 	<li><?php esc_html_e( 'Copy the token and paste into the Bot Token field below.', 'wptelegram-login' );?>&nbsp;<?php printf( __( 'For ease, use %s', 'wptelegram-login' ), '<a href="' . esc_url( 'https://web.telegram.org' ) . '" target="_blank">Telegram Web</a>' ); ?></li>
		 	<li><?php echo sprintf( __( 'Send %1$s command to %2$s, select your bot and then send %3$s', 'wptelegram-login' ), '<code>/setdomain</code>', '<a href="https://t.me/BotFather"  target="_blank">@BotFather</a>', '<b><code>' . $domain . '</code></b>' );
            ?></li>
		 	<li><?php esc_html_e( 'Test your bot token below and fill in the bot username if not filled automatically.', 'wptelegram-login' );?>
		 	</li>
		 	<li><?php echo sprintf( __( 'Hit %s below', 'wptelegram-login' ), '<b>' . __( 'Save Changes', 'wptelegram-login' ) . '</b>' );?></li>
		 	<li><?php esc_html_e( 'That\'s it. You are ready to rock :)', 'wptelegram-login' );?></li>
		 </ol>
		 <?php
	}
	
	/**
	 * Output a the after field html
	 * @param  object $field_args Current field args
	 * @param  object $field      Current field object
	 */
	public static function render_after_field( $field_args, $field ){
		$id = $field->id(); ?>
		<br>
		<?php if ( 'bot_token' == $id ) : ?>

			<p><span id="bot_token-info" class="info"></span></p>
			<p><span id="bot_token-err" class="hidden wptelegram-err info">&nbsp;<?php esc_html_e('Invalid Bot Token', 'wptelegram-login' ); ?></span></p>

		<?php elseif ( 'bot_username' == $id ) : ?>
			<p><span id="bot_username-err" class="hidden wptelegram-err info">&nbsp;<?php esc_html_e('Invalid Bot Username', 'wptelegram-login' ); ?></span></p>
		<?php endif; ?>
	<?php
	}

	public static function get_button_html( $id ){
		$text = '';
		switch ( $id ) {
			case 'bot_token':
				$text = __( 'Test Token', 'wptelegram-login' );
				break;
		}
		$html = '<button type="button" id="button-' . $id . '" class="button-secondary" data-id="' . $id . '">' . $text . '</button>';
		return $html;
	}

	/**
	 * Register the column to be displayed in user list table
	 *
	 * @since    1.0.0
	 * 
	 * @param  array $columns The table columns
	 * 
	 * @return array
	 */
	public function register_custom_user_column( $columns ) {
		$columns['telegram_chat_id'] = __( 'Telegram User ID', 'wptelegram-login' );
		return $columns;
	}

	/**
	 * Register the output for User Telegram ID
	 *
	 * @since    1.0.0
	 * 
	 * @param string $output      Custom column output. Default empty.
	 * @param string $column_name Column name.
	 * @param int    $user_id     ID of the currently-listed user.
	 * 
	 * @return string|null
	 */
	public function register_custom_user_column_view( $output, $column_name, $user_id ) {
		
		if ( $column_name == 'telegram_chat_id' ) {

			$user = get_user_by( 'id', $user_id );

			if ( $user && $user instanceof WP_User ) {
				
				return $user->wptelegram_login_user_id;
			}
		}
		return $output;
	}
}