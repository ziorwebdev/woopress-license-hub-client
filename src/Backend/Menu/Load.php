<?php
/**
 * Load menu
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Backend\Menu
 * @since 1.0.0
 */
namespace ZIORWebDev\WooPressLicenseHubClient\Backend\Menu;

use ZIORWebDev\WooPressLicenseHubClient\Models\Plugin as Model_Plugin;
use ZIORWebDev\WooPressLicenseHubClient\Models\Activation as Model_Activation;

/**
 * Controller_Menu Class
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Backend\Menu
 * @since 1.0.0
 */
class Load {

	/**
	 * Plugin model
	 *
	 * @var Model_Plugin
	 */
	protected $plugin;

	/**
	 * Activation model
	 *
	 * @var Model_Activation
	 */
	protected $activation;

	/**
	 * User data model
	 *
	 * @var Model_User_Data
	 */
	protected $user_data;

	/**
	 * Constructor
	 *
	 * @param Model_Plugin     $model_plugin Plugin model
	 * @param Model_Activation $model_activation Activation model
	 * @since 1.0.0
	 */
	public function __construct( Model_Plugin $model_plugin, Model_Activation $model_activation ) {
		$this->plugin     = $model_plugin;
		$this->activation = $model_activation;

		// TODO: Implement alert.
		// add_action( 'admin_footer', array( $this, 'add_menu_alert' ) );
	}

	/**
	 * Add menu alert
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_menu_alert() {
		global $_parent_pages;

		$parent_menu_slug = $this->plugin->get_parent_menu_slug();

		if ( ! $parent_menu_slug ) {
			return;
		}

		if ( ! isset( $_parent_pages[ $parent_menu_slug ] ) ) {
			return;
		}

		if ( 'valid' === $this->activation->status() ) {
			return;
		}
	}
}
