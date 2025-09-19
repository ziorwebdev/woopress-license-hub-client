<?php
/**
 * Load class
 *
 * @package ZiorWebDev\WooPressLicenseHubClient
 * @since 1.0.0
 */
namespace ZiorWebDev\WooPressLicenseHubClient;

/**
 * Include the Composer autoload file if you're not using Composer in your package.
 *
 */

/**
 * Models
 */
use ZiorWebDev\WooPressLicenseHubClient\Models\Plugin as Model_Plugin;
use ZiorWebDev\WooPressLicenseHubClient\Models\UserData as Model_User_Data;
use ZiorWebDev\WooPressLicenseHubClient\Models\Activation as Model_Activation;
/**
 * API
 */
use ZiorWebDev\WooPressLicenseHubClient\Api\Rest\RoutesLibrary as API_Rest_Routes_Library;
/**
 * Controllers
 */
use ZiorWebDev\WooPressLicenseHubClient\Backend\Plugin\Information as Controller_Plugin_Information;
use ZiorWebDev\WooPressLicenseHubClient\Backend\Plugin\Update as Controller_Plugin_Update;
use ZiorWebDev\WooPressLicenseHubClient\Backend\Plugin\Table as Controller_Plugin_Table;
use ZiorWebDev\WooPressLicenseHubClient\Backend\Page\Load as Controller_Page;
use ZiorWebDev\WooPressLicenseHubClient\Backend\Notice\Load as Controller_Notice;
use ZiorWebDev\WooPressLicenseHubClient\Backend\Menu\Load as Controller_Menu;
use ZiorWebDev\WooPressLicenseHubClient\Backend\Cron\VerifyLicense as Controller_Verify_License;
/**
 * Class Load
 *
 * @package ZiorWebDev\WooPressLicenseHubClient\Load
 * @since 1.0.0
 */
final class Load {

	/**
	 * Client data initialized in the constructor.
	 *
	 * @var array
	 */
	public $client_data;

	/**
	 * Registered rest routes in the constructor of API_Rest_Routes_Library.
	 *
	 * @var API_Rest_Routes_Library
	 */
	public $routes;

	/**
	 * Instantiated Model_Plugin in the constructor.
	 *
	 * @var Model_Plugin
	 */
	public $plugin;

	/**
	 * Instantiated Model_Activation in the constructor.
	 *
	 * @var Model_Activation
	 */
	public $activation;

	/**
	 * Instantiated Model_User_Data in the constructor.
	 *
	 * @var Model_User_Data
	 */
	public $user_data;

	/**
	 * Setup client instance based on client data.
	 *
	 * @param array $client_data Client data.
	 * @since 1.0.0
	 */
	public function __construct( array $client_data ) {

		$this->client_data = $client_data;

		/**
		 * Get plugin file path
		 */
		if ( ! isset( $this->client_data['plugin_file'] ) ) {
			trigger_error( esc_html__( 'Please include a valid plugin_file.', 'woopress-license-hub-client' ), E_USER_NOTICE );
		}

		add_action(
			'init',
			function () {
				/**
				* Rest API support
				*/
				$this->routes = new API_Rest_Routes_Library( $this->client_data );

				/**
				* Load plugin models for all contexts (needed for Cron)
				*/
				$this->plugin = new Model_Plugin( $this->client_data );

				if ( ! $this->plugin->is_valid() ) {
					trigger_error( sprintf( esc_html__( '%s is not a valid plugin file.', 'woopress-license-hub-client' ), esc_html( $this->plugin->get_file() ) ), E_USER_NOTICE );
				}

				$this->activation = new Model_Activation( $this->plugin );
				$this->user_data  = new Model_User_Data( $this->plugin );

				// License verification works in both admin and frontend for Cron
				new Controller_Verify_License( $this->plugin, $this->user_data, $this->activation );

				/**
				* Don't load admin-specific controllers outside admin panel
				*/
				if ( ! is_admin() ) {
					return;
				}

				/**
				* Load admin-specific controllers
				*/
				new Controller_Plugin_Information( $this->plugin, $this->activation, $this->user_data );
				new Controller_Plugin_Update( $this->plugin, $this->activation, $this->user_data );
				new Controller_Plugin_Table( $this->plugin, $this->activation, $this->user_data );
				new Controller_Page( $this->plugin, $this->activation, $this->user_data );
				new Controller_Notice( $this->plugin, $this->activation, $this->user_data );
				new Controller_Menu( $this->plugin, $this->activation );
			}
		);
	}
}
