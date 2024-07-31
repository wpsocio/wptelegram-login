<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpsocio.com
 * @since      1.0.0
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\admin
 */

namespace WPTelegram\Login\admin;

use WPTelegram\Login\includes\BaseClass;
use WP_User;
use WP_REST_Request;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WPTelegram\Login
 * @subpackage WPTelegram\Login\admin
 * @author     WP Socio
 */
class Admin extends BaseClass {

	/**
	 * Registers custom category for blocks.
	 *
	 * @since 1.6.3
	 *
	 * @param array $categories The block categories.
	 * @return array
	 */
	public function register_block_category( $categories ) {
		$slugs = wp_list_pluck( $categories, 'slug' );
		$slug  = 'wptelegram';
		if ( in_array( $slug, $slugs, true ) ) {
			return $categories;
		}

		$categories[] = [
			'slug'  => $slug,
			'title' => __( 'WP Telegram', 'wptelegram-login' ),
			'icon'  => null,
		];

		return $categories;
	}

	/**
	 * Register WP REST API routes.
	 *
	 * @since 1.5.0
	 */
	public function register_rest_routes() {
		$controller = new \WPTelegram\Login\includes\restApi\SettingsController();
		$controller->register_routes();
	}

	/**
	 * Register user fields in WP REST API.
	 *
	 * @since 1.8.4
	 */
	public function register_user_fields() {
		$meta_key = WPTELEGRAM_USER_ID_META_KEY;
		register_rest_field(
			'user',
			$meta_key,
			[
				'get_callback'    => function ( $object ) use ( $meta_key ) {
					return current_user_can( 'list_users' ) ? get_user_meta( $object['id'], $meta_key, true ) : '';
				},
				'update_callback' => function ( $value, $object ) use ( $meta_key ) {
					update_user_meta( $object->ID, $meta_key, $value );
				},
				'schema'          => [
					'type'        => 'string',
					'arg_options' => [
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => function ( $chat_id ) {
							return self::is_valid_chat_id( $chat_id );
						},
					],
				],
			]
		);

		$meta_key = WPTELEGRAM_USERNAME_META_KEY;
		register_rest_field(
			'user',
			$meta_key,
			[
				'get_callback' => function ( $object ) use ( $meta_key ) {
					return current_user_can( 'list_users' ) ? get_user_meta( $object['id'], $meta_key, true ) : '';
				},
				'schema'       => [
					'type'        => 'string',
					'arg_options' => [
						'sanitize_callback' => 'sanitize_text_field',
					],
				],
			]
		);
	}

	/**
	 * Add custom params to WP REST user collection.
	 *
	 * @since 1.8.4
	 *
	 * @param array $query_params JSON Schema-formatted collection parameters.
	 */
	public function rest_user_collection_params( $query_params ) {
		$query_params['telegram_users_only'] = [
			'description' => __( 'Limit result set to users who have their Telegram accounts connected.' ),
			'type'        => 'boolean',
		];
		return $query_params;
	}

	/**
	 * Modifies WP REST user query if needed.
	 *
	 * @since 1.8.4
	 *
	 * @param array           $prepared_args Array of arguments for WP_User_Query.
	 * @param WP_REST_Request $request       The current request.
	 */
	public function modify_rest_user_query( $prepared_args, WP_REST_Request $request ) {
		$telegram_users_only = $request->get_param( 'telegram_users_only' );

		if ( ! $telegram_users_only ) {
			return $prepared_args;
		}
		$meta_query = [
			'relation' => 'AND',
			[
				'key'     => WPTELEGRAM_USER_ID_META_KEY,
				'compare' => 'EXISTS',
			],
			[
				'key'     => WPTELEGRAM_USER_ID_META_KEY,
				'compare' => '!=',
				'value'   => '',
			],
		];
		// If there is already a meta query.
		if ( ! empty( $prepared_args['meta_query'] ) ) {
			$meta_query[] = $prepared_args['meta_query'];
		}

		$prepared_args['meta_query'] = $meta_query; // phpcs:ignore

		return $prepared_args;
	}

	/**
	 * Register the admin menu.
	 *
	 * @since 1.5.0
	 */
	public function add_plugin_admin_menu() {
		add_submenu_page(
			'wptelegram',
			esc_html( $this->plugin()->title() ),
			esc_html__( 'Telegram Login', 'wptelegram-login' ),
			'manage_options',
			$this->plugin()->name(),
			[ $this, 'display_plugin_admin_page' ]
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since 1.5.0
	 */
	public function display_plugin_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( $this->plugin()->doing_upgrade() ) {
			return printf(
				'<h1>%1$s %2$s</h1>',
				esc_html__( 'Plugin data has been upgraded.', 'wptelegram-login' ),
				esc_html__( 'Please reload the page.', 'wptelegram-login' )
			);
		}
		?>
			<div id="wptelegram-login-settings"></div>
		<?php
	}

	/**
	 * Create our widget.
	 *
	 * @since    1.0.0
	 */
	public function register_widgets() {
		register_widget( '\WPTelegram\Login\shared\widgets\Primary' );
	}

	/**
	 * Add Telegram user field to WooCommerce My Account page.
	 *
	 * @since 1.10.2
	 *
	 * @return void
	 */
	public static function wc_add_telegram_fields() {
		$bot_username = WPTG_Login()->options()->get( 'bot_username' );
		if ( empty( $bot_username ) ) {
			return;
		}
		$field_name  = WPTELEGRAM_USER_ID_META_KEY;
		$telegram_id = get_the_author_meta( $field_name, get_current_user_id() );
		?>
		<fieldset style="margin-top:1rem;margin-bottom:1rem;">
			<legend><?php esc_html_e( 'Telegram Info', 'wptelegram-login' ); ?></legend>
			<p class="description"><?php esc_html_e( 'Here you can connect this account to Telegram.', 'wptelegram-login' ); ?></p>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
				<label for="<?php echo esc_attr( $field_name ); ?>"><?php esc_html_e( 'Telegram Chat ID', 'wptelegram-login' ); ?></label>
				<input type="text" name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $telegram_id ); ?>" class="input-text" />
			</p>
			<?php self::render_instructions(); ?>
		</fieldset>
		<?php
	}

	/**
	 * Adds User Telegram ID field to user profile.
	 *
	 * @since    1.8.2
	 * @param WP_User $user Currently-listed user.
	 * @return void
	 */
	public static function wp_add_telegram_fields( $user ) {
		$bot_username = WPTG_Login()->options()->get( 'bot_username' );
		if ( empty( $bot_username ) ) {
			return;
		}
		$field_name      = WPTELEGRAM_USER_ID_META_KEY;
		$telegram_id     = get_the_author_meta( $field_name, $user->ID );
		$is_current_user = get_current_user_id() === $user->ID;
		?>
		<h2><?php esc_html_e( 'Telegram Info', 'wptelegram-login' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Here you can connect this account to Telegram.', 'wptelegram-login' ); ?></p>
		<table class="form-table">
			<tr>
				<th>
					<label for="<?php echo esc_attr( $field_name ); ?>"><?php esc_html_e( 'Telegram Chat ID', 'wptelegram-login' ); ?></label>
				</th>
				<td>
					<input type="text" name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $telegram_id ); ?>" class="regular-text" />
					<?php self::render_instructions( $is_current_user ); ?>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Render the instructions for Telegram integration.
	 *
	 * @since 1.10.2
	 *
	 * @param bool $is_current_user Whether the instructions are for the current user or for an admin.
	 */
	public static function render_instructions( $is_current_user = true ) {
		$bot_username = WPTG_Login()->options()->get( 'bot_username' );
		?>
		<p style="color:#f10e0e;"><b><?php esc_html_e( 'INSTRUCTIONS!', 'wptelegram-login' ); ?></b></p>
		<ul style="list-style-type: disc;padding-inline-start:1rem;">
			<li>
				<?php
					printf(
						/* translators: %s is bot username */
						$is_current_user // phpcs:ignore
						? __( 'Get your Chat ID from %s and enter it above.', 'wptelegram-login' ) // phpcs:ignore
						/* translators: %s is bot username */
						: __( 'Ask the user to get the Chat ID from %s and enter it above.', 'wptelegram-login' ), // phpcs:ignore
						'<a href="https://t.me/MyChatInfoBot" target="_blank" rel="noreferrer noopener">@MyChatInfoBot</a>' // phpcs:ignore
					);
				?>
			</li>
			<li>
				<?php
					printf(
						$is_current_user // phpcs:ignore
						/* translators: %s is bot username */
						? __( 'Start a conversation with %s to receive notifications.', 'wptelegram-login' ) // phpcs:ignore
						/* translators: %s is bot username */
						: __( 'Ask the user to start a conversation with %s to receive notifications.', 'wptelegram-login' ), // phpcs:ignore
						sprintf( '<a href="https://t.me/%1$s"  target="_blank" rel="noreferrer noopener">@%1$s</a>', esc_html( $bot_username ) )
					);
				?>
			</li>
		</ul>
		<?php
	}

	/**
	 * Validate the profile fields.
	 *
	 * @since   1.8.2
	 * @param   \WP_Error $errors WP_Error object (passed by reference).
	 */
	public function validate_user_profile_fields( &$errors ) {

		if ( isset( $_POST[ WPTELEGRAM_USER_ID_META_KEY ] ) ) { // phpcs:ignore

			// phpcs:ignore WordPress.Security.NonceVerification
			$chat_id = sanitize_text_field( wp_unslash( $_POST[ WPTELEGRAM_USER_ID_META_KEY ] ) );

			if ( $chat_id && ! self::is_valid_chat_id( $chat_id ) ) {

				$errors->add( 'invalid_chat_id', __( 'Error:', 'wptelegram-login' ) . ' ' . __( 'Please Enter a valid Chat ID', 'wptelegram-login' ) );
			}
		}
	}

	/**
	 * Update user fields.
	 *
	 * @since   1.8.2
	 * @param integer $user_id The user ID.
	 * @return void
	 */
	public function update_user_profile_fields( $user_id ) {
		// phpcs:ignore WordPress.Security.NonceVerification
		if ( current_user_can( 'edit_user', $user_id ) && isset( $_POST[ WPTELEGRAM_USER_ID_META_KEY ] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification
			$chat_id = sanitize_text_field( wp_unslash( $_POST[ WPTELEGRAM_USER_ID_META_KEY ] ) );

			if ( empty( $chat_id ) ) {
				delete_user_meta( $user_id, WPTELEGRAM_USER_ID_META_KEY );
			} elseif ( self::is_valid_chat_id( $chat_id ) ) {
				update_user_meta( $user_id, WPTELEGRAM_USER_ID_META_KEY, $chat_id );
			}
		}
	}

	/**
	 * Whether a given chat ID is valid.
	 *
	 * @param integer $chat_id The Telegram chat ID.
	 * @return bool
	 */
	public static function is_valid_chat_id( $chat_id ) {
		return (bool) preg_match( '/^\-?[1-9][0-9]{6,51}$/', $chat_id );
	}

	/**
	 * Register the column to be displayed in user list table.
	 *
	 * @since    1.0.0
	 * @param  array $columns The table columns.
	 * @return array
	 */
	public function register_custom_user_column( $columns ) {
		$columns[ WPTELEGRAM_USER_ID_META_KEY ] = __( 'Telegram User ID', 'wptelegram-login' );
		return $columns;
	}

	/**
	 * Register the output for User Telegram ID.
	 *
	 * @since    1.0.0
	 * @param string $output      Custom column output. Default empty.
	 * @param string $column_name Column name.
	 * @param int    $user_id     ID of the currently-listed user.
	 * @return string|null
	 */
	public function register_custom_user_column_view( $output, $column_name, $user_id ) {

		if ( WPTELEGRAM_USER_ID_META_KEY === $column_name ) {

			$user = get_user_by( 'id', $user_id );

			if ( $user && $user instanceof WP_User ) {

				return $user->{WPTELEGRAM_USER_ID_META_KEY};
			}
		}
		return $output;
	}
}
