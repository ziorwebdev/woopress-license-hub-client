<?php
/**
 * Activation status trait
 *
 * @package ZiorWebDev\WooPressLicenseHubClient\Traits
 * @since 1.0.0
 */
namespace ZiorWebDev\WooPressLicenseHubClient\Traits;

/**
 * Activation status trait
 *
 * @package ZiorWebDev\WooPressLicenseHubClient\Traits
 * @since 1.0.0
 */
trait ActivationStatus {

	/**
	 * Get the activation status
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function status() {
		$activation = $this->get();

		return $this->get_status( $activation );
	}

	/**
	 * Get the activation status
	 *
	 * @param array $activation
	 * @return string
	 * @since 1.0.0
	 */
	public function get_status( $activation ) {
		if ( isset( $activation['error'], $activation['message'] ) ) {
			return 'error';
		}

		if ( ! isset( $activation['license_key'], $activation['activation_instance'], $activation['license_expiration'] ) ) {
			return 'none';
		}

		if ( ! $this->is_expired_updates( $activation ) ) {
			return 'valid';
		}

		return 'expired';
	}

	/**
	 * Get the activation status
	 *
	 * @param array $activation
	 * @return string
	 * @since 1.0.0
	 */
	public function is_expired( $activation ) {
		if ( $activation['license_expiration'] === '0000-00-00 00:00:00' ) {
			return false;
		}

		return strtotime( current_time( 'mysql' ) ) > strtotime( $activation['license_expiration'] );
	}

	/**
	 * Get the activation status
	 *
	 * @param array $activation
	 * @return string
	 * @since 1.0.0
	 */
	public function is_expired_updates( $activation ) {
		if ( ! $activation['license_updates'] ) {
			return false;
		}

		if ( ! $this->is_expired( $activation ) ) {
			return false;
		}

		return true;
	}
}
