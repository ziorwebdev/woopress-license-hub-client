<?php
/**
 * Table class
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Backend\Plugin
 * @since 1.0.0
 */
namespace ZIORWebDev\WooPressLicenseHubClient\Backend\Plugin;

use ZIORWebDev\WooPressLicenseHubClient\Models\Plugin as Model_Plugin;
use ZIORWebDev\WooPressLicenseHubClient\Models\Activation as Model_Activation;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Controller_Plugin_Table Class
 *
 * Implement plugin version notification in plugins table.
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Backend\Plugin
 * @since 1.0.0
 */
class Table {

	/**
	 * Instantiated Model_Plugin in the constructor.
	 *
	 * @var Model_Plugin
	 */
	private $plugin;

	/**
	 * Instantiated Model_Activation in the constructor.
	 *
	 * @var Model_Activation
	 */
	private $activation;

	/**
	 * Setup class.
	 *
	 * @param Model_Plugin     $plugin
	 * @param Model_Activation $activation
	 * @since 1.0.0
	 */
	public function __construct( Model_Plugin $plugin, Model_Activation $activation ) {
		$this->plugin     = $plugin;
		$this->activation = $activation;

		add_filter( 'plugins_api', array( $this, 'add_fetch_data' ), 10, 3 );
		// add_action( 'in_plugin_update_message-' . $this->plugin->get_base(), array( $this, 'add_update_notification' ), 10, 2 );
		add_action( 'after_plugin_row_' . $this->plugin->get_base(), array( $this, 'add_row_notification' ), 100, 2 );
	}

	/**
	 * Include fetched data in transient to the plugin in the plugins table.
	 *
	 * @param object $return
	 * @param string $action
	 * @param object $args
	 * @return object
	 * @since 1.0.0
	 */
	public function add_fetch_data( $return, $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $return;
		}

		if ( $args->slug != $this->plugin->get_slug() ) {
			return $return;
		}

		$transient = get_site_transient( 'update_plugins' );

		if ( empty( $transient->no_update[ $this->plugin->get_base() ] ) ) {
			return $return;
		}

		$plugin = $transient->no_update[ $this->plugin->get_base() ];

		if ( ! $plugin ) {
			return $return;
		}

		if ( isset( $plugin->sections['screenshots'] ) && is_array( $plugin->sections['screenshots'] ) ) {
			$plugin->sections['screenshots'] = $this->add_screenshots( $plugin->sections['screenshots'] );
		}

		return $plugin;
	}

	/**
	 * Add product screenshots to the plugin in the View details modal of the plugins table.
	 *
	 * @param array $screenshots
	 * @return string
	 * @since 1.0.0
	 */
	public function add_screenshots( array $screenshots = array() ) {
		if ( empty( $screenshots ) ) {
			return '';
		}

		$html = '<ol>';

		foreach ( $screenshots as $image ) {
			$src     = esc_url( $image->src );
			$caption = esc_attr( $image->caption );

			$html .= sprintf(
				'<li><a href="%1$s"><img src="%1$s" alt="%2$s"></a></li>',
				$src,
				$caption
			);
		}

		$html .= '</ol>';

		return $html;
	}


	/**
	 * Add error notification to the active plugin row.
	 *
	 * @param string $plugin_file
	 * @param array  $plugin_data
	 * @return void
	 * @since 1.0.0
	 */
	public function add_row_notification( $plugin_file, $plugin_data ) {
		// Check if current user has the required capability and we are not in the network admin
		if ( is_network_admin() || ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		$plugin_base = $this->plugin->get_base();

		// Check if the plugin is active
		$is_active = is_plugin_active( $plugin_base ) ? 'active' : '';

		if ( 'valid' !== $this->activation->status() ) {
			// Add notification for non-activated license
			echo '<tr class="plugin-update-tr installer-plugin-update-tr ' . esc_attr( $is_active ) . '" style="position:relative;top:-1px;">
				<td colspan="4" class="plugin-update colspanchange">
					<div class="update-message notice notice-error notice-alt inline">
						<p>' .
						'<b>' . esc_html__( 'Updates are disabled because the license is not activated.', 'woopress-license-hub-client' ) . '</b> ' .
						sprintf(
							esc_html__( 'Please visit the %1$s to activate your license or %2$s one from our website.', 'woopress-license-hub-client' ),
							sprintf(
								'<a href="%s">%s</a>',
								esc_url( $this->plugin->get_menu_license_url() ),
								esc_html__( 'license', 'woopress-license-hub-client' )
							),
							sprintf(
								'<a href="%s" target="_blank">%s</a>',
								esc_url( $this->plugin->get_url() ),
								esc_html__( 'purchase', 'woopress-license-hub-client' )
							)
						) . '</p>
					</div>
				</td>
			</tr>';

			return;
		}

		if ( 'expired' === $this->activation->status() ) {
			// Add notification for expired license
			echo '<tr class="plugin-update-tr installer-plugin-update-tr ' . esc_attr( $is_active ) . '" style="position:relative;top:-1px;">
				<td colspan="4" class="plugin-update colspanchange">
					<div class="update-message notice notice-error notice-alt inline">
						<p>' .
						'<b>' . esc_html__( 'Your plugin license has expired.', 'woopress-license-hub-client' ) . '</b> ' .
						sprintf(
							esc_html__( 'Please visit your %1$s to renew your license or %2$s a new one from our website.', 'woopress-license-hub-client' ),
							sprintf(
								'<a href="%s" target="_blank">%s</a>',
								esc_url( $this->plugin->get_license_key_url() ),
								esc_html__( 'account', 'woopress-license-hub-client' )
							),
							sprintf(
								'<a href="%s" target="_blank">%s</a>',
								esc_url( $this->plugin->get_url() ),
								esc_html__( 'purchase', 'woopress-license-hub-client' )
							)
						) . '</p>
					</div>
				</td>
			</tr>';
		}
	}
}
