<?php
/**
 * Base model
 *
 * @package ZiorWebDev\WooPressLicenseHubClient\Models
 * @since 1.0.0
 */
namespace ZiorWebDev\WooPressLicenseHubClient\Models;

use ZiorWebDev\WooPressLicenseHubClient\Models\Plugin as Model_Plugin;

/**
 * Abstract Base Class
 *
 * Implemented by classes using the same CRUD(s) pattern.
 *
 * @since  1.0.0
 */
abstract class Base {

	/**
	 * Plugin model
	 *
	 * @var Model_Plugin
	 */
	protected $plugin;

	/**
	 * Default attributes of the model.
	 *
	 * @var array
	 */
	protected $defaults = array();

	/**
	 * Setup class
	 *
	 * @param Model_Plugin $plugin Model_Plugin instance.
	 */
	public function __construct( Model_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Get database model suffix.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	abstract protected function get_db_suffix();

	/**
	 * Get model default attributes.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_defaults() {
		return $this->defaults;
	}

	/**
	 * Get valid data parameter.
	 *
	 * @param string $param
	 * @param int $length
	 * @return string
	 * @since 1.0.0
	 */
	protected function get_valid_data_param( $param, $length = 1000 ) {
		if ( ! is_string( $param ) ) {
			return $param;
		}

		if ( strlen( $param ) > $length ) {
			$param = substr( $param, 0, $length );
		}

		
		return $param ? trim( $param ) : '';
	}

	/**
	 * Get valid data.
	 *
	 * @param array $array
	 * @return array
	 * @since 1.0.0
	 */
	protected function get_valid_data( array $array ) {
		$valid_data = array();

		foreach ( $this->get_defaults() as $key => $value ) {
			if ( array_key_exists( $key, $array ) ) {
				$valid_data[ $key ] = $this->get_valid_data_param( $array[ $key ] );
			} else {
				$valid_data[ $key ] = $value;
			}
		}

		return $valid_data;
	}

	/**
	 * Get database key.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	protected function get_db_key() {
		if ( ! $this->plugin->is_valid() ) {
			return;
		}

		$plugin_slug = $this->plugin->get_slug();
		$db_suffix   = $this->get_db_suffix();

		return sanitize_key( "woopress-license-hub-client_{$plugin_slug}_{$db_suffix}" );
	}

	/**
	 * Create data.
	 *
	 * @param array $data
	 * @return array
	 * @since 1.0.0
	 */
	abstract public function create( array $data );

	/**
	 * Update data.
	 *
	 * @param array $data
	 * @return array
	 * @since 1.0.0
	 */
	abstract public function update( array $data );

	/**
	 * Get data.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get() {
		if ( ! $this->plugin->is_valid() ) {
			return $this->get_defaults();
		}

		$data      = array();
		$site_data = null;

		if ( is_multisite() && is_network_admin() ) {
			$sites = get_sites(
				array(
					'fields' => 'ids',
				)
			);

			foreach ( $sites as $site_id ) {
				if ( function_exists( 'switch_to_blog' ) && function_exists( 'restore_current_blog' ) ) {
					switch_to_blog( $site_id );
					$site_data = get_option( $this->get_db_key(), array() );
					restore_current_blog();
					if ( isset( $site_data['license_key'], $site_data['activation_instance'] ) ) {
						$data = $site_data;
						break; // Found valid license data
					}
				}
			}
		} else {
			$data = get_option( $this->get_db_key(), array() );
		}

		if ( ! is_array( $data ) || empty( $data ) ) {
			return $this->get_defaults();
		}

		$valid_data = $this->get_valid_data( $data );

		return $valid_data;
	}

	/**
	 * Delete data.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function delete() {
		if ( ! $this->plugin->is_valid() ) {
			return;
		}

		return delete_option( $this->get_db_key() );
	}

	/**
	 * Save data.
	 *
	 * @param array $data
	 * @return array
	 * @since 1.0.0
	 */
	public function save( array $data ) {
		if ( ! $this->plugin->is_valid() ) {
			return;
		}

		$valid_data = $this->get_valid_data( $data );
		$status     = update_option( $this->get_db_key(), $valid_data );

		if ( $status ) {
			return $valid_data;
		}

		return false;
	}
}
