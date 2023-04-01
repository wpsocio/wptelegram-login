<?php
/**
 * Handles the plugin requirements.
 *
 * @link      https://wpsocio.com
 * @since     1.10.2
 *
 * @package WPTelegram
 * @subpackage WPTelegram\Login\includes
 */

namespace WPTelegram\Login\includes;

/**
 * Handles the plugin requirements.
 *
 * @package WPTelegram
 * @subpackage WPTelegram\Login\includes
 * @author   WP Socio
 */
class Requirements {

	/**
	 * Check if the requirements are satisfied.
	 *
	 * @since 1.10.2
	 *
	 * @return bool Whether the requirements are satisfied.
	 */
	public static function satisfied() {
		$env_details = self::get_env_details();

		return $env_details['satisfied'];
	}

	/**
	 * Get the environment details.
	 *
	 * @since 1.10.2
	 *
	 * @return array The environment details.
	 */
	public static function get_env_details() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugin_data = get_plugin_data( WPTELEGRAM_LOGIN_MAIN_FILE );

		$data = [
			'PHP' => [
				'version' => PHP_VERSION,
				'min'     => $plugin_data['RequiresPHP'],
			],
			'WP'  => [
				'version' => get_bloginfo( 'version' ),
				'min'     => $plugin_data['RequiresWP'],
			],
		];

		$satisfied = true;

		foreach ( $data as &$details ) {
			$details['satisfied'] = version_compare( $details['version'], $details['min'], '>=' );

			if ( $satisfied && ! $details['satisfied'] ) {
				$satisfied = false;
			}
		}

		return compact( 'data', 'satisfied' );
	}

	/**
	 * Display the requirements.
	 *
	 * @since 1.10.2
	 */
	public static function display_requirements() {
		$env_details = self::get_env_details();
		?>
		<tr class="plugin-update-tr">
			<td colspan="5" class="plugin-update colspanchange">
				<div class="update-message notice inline notice-error notice-alt" style="padding-block-end: 1rem;">
					<p>
						<?php esc_html_e( 'This plugin is not compatible with your website configuration.', 'wptelegram-login' ); ?>
					</p>
					<span><?php esc_html_e( 'Missing requirements', 'wptelegram-login' ); ?>&nbsp;👇</span>
					<ul style="list-style-type: disc; margin-inline-start: 2rem;">
						<?php
						foreach ( $env_details['data'] as $name => $requirement ) :
							if ( ! $requirement['satisfied'] ) :
								?>
								<li>
									<?php
									echo esc_html( $name );
									echo '&nbsp;&dash;&nbsp;';
									echo esc_html(
										sprintf(
										/* translators: %s: Version number */
											__( 'Current version: %s', 'wptelegram-login' ),
											$requirement['version']
										)
									);
									echo '&nbsp;&comma;&nbsp;';
									echo esc_html(
										sprintf(
										/* translators: %s: Version number */
											__( 'Minimum required version: %s', 'wptelegram-login' ),
											$requirement['min']
										)
									);
									?>
								</li>
								<?php
							endif;
						endforeach;
						?>
					</ul>
					<span>
						<?php esc_html_e( 'Please contact your hosting provider to ensure the above requirements are met.', 'wptelegram-login' ); ?>
					</span>
				</div>
			</td>
		</tr>
		<?php
	}
}
