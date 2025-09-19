<?php
/**
 * Activation create API fetch
 *
 * @package ZiorWebDev\WooPressLicenseHubClient\Api\Fetch\Activation
 * @since 1.0.0
 */
namespace ZiorWebDev\WooPressLicenseHubClient\Api\Fetch\Activation;

use ZiorWebDev\WooPressLicenseHubClient\Api\Fetch\Base;

/**
 * API_Fetch_Activation_Create Class
 *
 * @package ZiorWebDev\WooPressLicenseHubClient\Api\Fetch\Activation
 * @since 1.0.0
 */
class Create extends Base {

	/**
	 * Get rest route path
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_rest_path() {
		return 'activation';
	}

	/**
	 * Get rest method
	 *
	 * @return string POST
	 * @since 1.0.0
	 */
	public static function get_rest_method() {
		return 'POST';
	}
}
