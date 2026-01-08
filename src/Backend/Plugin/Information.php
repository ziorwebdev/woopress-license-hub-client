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

		add_filter( 'plugins_api', array( $this, 'add_fetch_data' ), 10, 3 );
		add_action( 'upgrader_process_complete', array( $this, 'clear_plugin_info_cache' ), 10, 2 );
	}

	/**
	 * Clears cached plugin API info after a plugin is updated.
	 *
	 * @param object $upgrader_object The upgrader object.
	 * @param array  $options         Array of update options.
	 */
	public function clear_plugin_info_cache( $upgrader_object, $options ) {
		// Return early if not a plugin update or no plugins provided.
		if ( empty( $options['plugins'] ) || empty( $options['type'] ) || 'plugin' !== $options['type'] ) {
			return;
		}

		foreach ( $options['plugins'] as $plugin_file ) {
			$slug = dirname( $plugin_file );
			delete_transient( 'plugin_info_' . $slug );
		}
	}

	/**
	 * Add fetch data from the server API to the plugin transient.
	 *
	 * @param object $transient
	 * @return object
	 * @since 1.0.0
	 */
	public function add_fetch_data( $result, $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $result;
		}

		if ( empty( $args->slug ) || $args->slug !== $this->plugin->get_slug() ) {
			return $result;
		}

		// Create a transient key unique to this plugin
		$transient_key = 'plugin_info_' . $this->plugin->get_slug();

		// Try to get cached data
		$cached = get_transient( $transient_key );
		if ( $cached ) {
			return $cached;
		}

		// Fetch fresh data if cache is empty
		$product = ( new API_Fetch_Product_Information( $this->plugin ) )->get_data();

		if ( empty( $product ) ) {
			return $result;
		}

		$plugin_data = (object) array(
			'name'         => $product->name,
			'slug'         => $this->plugin->get_slug(),
			'version'      => $product->version,
			'author'       => $product->author,
			'homepage'     => $product->homepage,
			'requires'     => $product->requires,
			'tested'       => $product->tested,
			'last_updated' => $product->last_updated,
			'sections'     => array(
				'description' => $product->description,
				'changelog'   => wpautop( $product->changelog ),
				'screenshots' => $product->screenshots,
			),
			'banners'      => array(
				'low'  => $product->banner_low,
				'high' => $product->banner_high,
			),
		);

		// Save it to transient for 12 hours
		set_transient( $transient_key, $plugin_data, 12 * HOUR_IN_SECONDS );

		return $plugin_data;
	}
}
