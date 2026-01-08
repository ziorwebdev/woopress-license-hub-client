<?php
/**
 * Load page
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Backend\Page
 * @since 1.0.0
 */
namespace ZIORWebDev\WooPressLicenseHubClient\Backend\Page;

use ZIORWebDev\WooPressLicenseHubClient\Models\Plugin as Model_Plugin;
use ZIORWebDev\WooPressLicenseHubClient\Models\Activation as Model_Activation;
use ZIORWebDev\WooPressLicenseHubClient\Models\UserData as Model_User_Data;
use ZIORWebDev\WooPressLicenseHubClient\Api\Fetch\Activation\Create as API_Fetch_Activation_Create;
use ZIORWebDev\WooPressLicenseHubClient\Api\Fetch\Activation\Delete as API_Fetch_Activation_Delete;

/**
 * Controller_Page Class
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Backend\Page
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
	 * @param Model_User_Data  $model_user_data User data model
	 * @since 1.0.0
	 */
	public function __construct( Model_Plugin $model_plugin, Model_Activation $model_activation, ?Model_User_Data $model_user_data = null ) {
		$this->plugin     = $model_plugin;
		$this->activation = $model_activation;
		$this->user_data  = $model_user_data;

		/**
		 * Don't load plugin menu if parent_menu_slug is set to false
		 */
		if ( false === $this->plugin->get_parent_menu_slug() ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'create_activation' ) );
		add_action( 'admin_init', array( $this, 'delete_activation' ) );
		add_action( 'admin_menu', array( $this, 'add_menu' ), 999 );
	}

	/**
	 * Add menu
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_menu() {
		global $_parent_pages;

		$parent_menu_slug  = $this->plugin->get_parent_menu_slug();
		$menu_slug_license = $this->plugin->get_license_menu_slug();

		// Early return if license menu is not set
		if ( ! $menu_slug_license ) {
			return;
		}

		$plugin_name = $this->plugin->get_name();

		// Add parent menu if needed
		$needs_parent_menu = (
			! isset( $_parent_pages[ $parent_menu_slug ] ) &&
			$plugin_name &&
			$parent_menu_slug !== 'options-general.php'
		);

		if ( $needs_parent_menu ) {
			add_menu_page(
				$plugin_name,
				$plugin_name,
				'edit_posts',
				$parent_menu_slug,
				'__return_null',
				'dashicons-cloud-upload'
			);
		}

		// Skip if submenu already exists
		if ( isset( $_parent_pages[ $menu_slug_license ] ) ) {
			return;
		}

		// Submenu title: defaults to "License", except under "Settings"
		$submenu_title = $parent_menu_slug === 'options-general.php'
			? esc_html__( $plugin_name, 'woopress-license-hub-client' )
			: esc_html__( 'License', 'woopress-license-hub-client' );

		add_submenu_page(
			$parent_menu_slug,
			$submenu_title,
			$submenu_title,
			'manage_options',
			$menu_slug_license,
			function () use ( $plugin_name ) {
				$plugin_slug           = $this->plugin->get_slug();
				$activation            = $this->activation->get();
				$user_data             = $this->user_data->get();
				$activation_delete_url = $this->plugin->get_activation_delete_url();
				$plugin_name           = $plugin_name;
				include __DIR__ . '/view/license.php';
			},
			99
		);
	}

	/**
	 * Create activation
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function create_activation() {
		$plugin_slug = $this->plugin->get_slug();

		/**
		 * Validate current page
		 */
		if ( ! isset( $_REQUEST['option_page'] ) || $_REQUEST['option_page'] !== $plugin_slug . '-woopress-license-hub-client-create' ) {
			return;
		}

		/**
		 * Validate license
		 */
		if ( ! isset( $_REQUEST[ $plugin_slug ] ) ) {
			return;
		}

		/**
		 * Validate nonce
		 */
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_REQUEST['_wpnonce'] ), $plugin_slug . '-woopress-license-hub-client-create-options' ) ) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		$license = wp_unslash( $_REQUEST[ $plugin_slug ] ); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$this->user_data->create( $license );

		$activation = ( new API_Fetch_Activation_Create( $this->plugin ) )->get_data(
			array_merge(
				(array) $license,
				array(
					'activation_site' => $this->plugin->get_activation_site(),
				)
			)
		);

		if ( $activation ) {
			$this->activation->create( (array) $activation );
		}

		wp_clean_plugins_cache();
	}

	/**
	 * Delete activation
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function delete_activation() {
		$plugin_slug = $this->plugin->get_slug();

		/**
		 * Validate current page
		 */
		if ( ! isset( $_REQUEST['option_page'] ) || $_REQUEST['option_page'] !== $plugin_slug . '-woopress-license-hub-client-delete' ) {
			return;
		}

		/**
		 * Validate nonce
		 */
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_REQUEST['_wpnonce'] ), $plugin_slug . '-woopress-license-hub-client-delete-options' ) ) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}

		$this->user_data->delete();

		$activation = $this->activation->get();

		$delete = ( new API_Fetch_Activation_Delete( $this->plugin ) )->get_data(
			array(
				'license_key'         => isset( $activation['license_key'] ) ? $activation['license_key'] : null,
				'activation_instance' => isset( $activation['activation_instance'] ) ? $activation['activation_instance'] : null,
			)
		);

		if ( $delete ) {
			$this->activation->delete();
		}
	}
}
