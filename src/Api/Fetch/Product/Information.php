<?php
/**
 * Product information API fetch
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Api\Fetch\Product
 * @since 1.0.0
 */
namespace ZIORWebDev\WooPressLicenseHubClient\Api\Fetch\Product;

use ZIORWebDev\WooPressLicenseHubClient\Api\Fetch\Base;

/**
 * API_Fetch_Product_Information Class
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Api\Fetch\Product
 * @since 1.0.0
 */
class Information extends Base {

	/**
	 * Get rest route path
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_rest_path() {
		return 'product/information';
	}

	/**
	 * Get rest method
	 *
	 * @return string GET
	 * @since 1.0.0
	 */
	public static function get_rest_method() {
		return 'GET';
	}
}
