<?php
/**
 * Plugin information
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Backend\Plugin
 * @since 1.0.0
 */
namespace ZIORWebDev\WooPressLicenseHubClient\Backend\Plugin;

use ZIORWebDev\WooPressLicenseHubClient\Models\Plugin as Model_Plugin;
use ZIORWebDev\WooPressLicenseHubClient\Models\Activation as Model_Activation;
use ZIORWebDev\WooPressLicenseHubClient\Api\Fetch\Product\Information as API_Fetch_Product_Information;

/**
 * Controller_Plugin_Information Class
 *
 * Implement plugin information.
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Backend\Plugin
 * @since 1.0.0
 */
class Information {

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
	 * Setup class
	 *
	 * @param Model_Plugin     $plugin
	 * @param Model_Activation $activation
	 * @since 1.0.0
	 */
	public function __construct( Model_Plugin $plugin, Model_Activation $activation ) {
		$this->plugin     = $plugin;
		$this->activation = $activation;

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'add_fetch_data' ) );
	}

	/**
	 * Add fetch data from the server API to the plugin transient.
	 *
	 * @param object $transient
	 * @return object
	 * @since 1.0.0
	 */
	public function add_fetch_data( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$product = ( new API_Fetch_Product_Information( $this->plugin ) )->get_data();

		if ( isset( $product->error ) ) {
			return $transient;
		}

		$plugin              = new \stdClass();
		$plugin->id          = $this->plugin->get_slug();
		$plugin->slug        = $this->plugin->get_slug();
		$plugin->plugin      = $this->plugin->get_base();
		$plugin->new_version = $product->version;
		$plugin->url         = $product->homepage;
		$plugin->tested      = $product->tested;
		$plugin->icons       = array(
			'default' => $product->icon,
		);

		/**
		 * Fields for plugin info
		 */
		$plugin->version         = $product->version;
		$plugin->homepage        = $product->homepage;
		$plugin->name            = $product->name;
		$plugin->author          = $product->author;
		$plugin->requires        = $product->requires;
		$plugin->requires_php    = $product->requires_php;
		$plugin->rating          = null;
		$plugin->num_ratings     = null;
		$plugin->active_installs = null;
		$plugin->last_updated    = $product->last_updated;
		$plugin->added           = $product->added;
		$plugin->sections        = array(
			'description' => preg_replace( '/<h2(.*?)<\/h2>/si', '<h3"$1</h3>', $product->description ),
			'changelog'   => wpautop( $product->changelog ),
			'screenshots' => $product->screenshots,
		);

		$plugin->banners = array(
			'low'  => $product->banner_low,
			'high' => $product->banner_high,
		);

		$plugin->package = null;

		$transient->no_update[ $this->plugin->get_base() ] = $plugin;

		/**
		 * Check if there is higher version available.
		 */

		$is_higher_version = version_compare( $plugin->version, $this->plugin->get_version(), '>' );

		if ( ! $is_higher_version ) {
			return $transient;
		}

		if ( ! current_user_can( 'update_plugins' ) ) {
			return $transient;
		}

		/**
		 * Get the license activation data.
		 */
		$activation = $this->activation->get();

		/**
		 * Check if the license is activated. If not, show a notice.
		 */
		if ( ! isset( $activation['license_key'], $activation['activation_instance'] ) ) {
			$transient->response[ $this->plugin->get_base() ] = $plugin;

			return $transient;
		}

		$update_params = http_build_query(
			array(
				'license_key'         => $activation['license_key'],
				'activation_instance' => $activation['activation_instance'],
			)
		);

		$update_url = "{$product->package}?{$update_params}";

		$plugin->package       = $update_url;
		$plugin->download_link = $update_url;

		$transient->response[ $this->plugin->get_base() ] = $plugin;

		return $transient;
	}
}
