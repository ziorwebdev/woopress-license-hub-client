<?php
/**
 * Route interface
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints
 * @since 1.0.0
 */
namespace ZIORWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints;

use ZIORWebDev\WooPressLicenseHubClient\Models\Plugin as Model_Plugin;
use ZIORWebDev\WooPressLicenseHubClient\Models\UserData as Model_User_Data;
use ZIORWebDev\WooPressLicenseHubClient\Models\Activation as Model_Activation;
/**
 * Route Interface
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints
 * @since 1.0.0
 */

interface RouteInterface {

	/**
	 * Callback.
	 *
	 * @param \WP_REST_Request $request Request data.
	 * @param Model_Plugin     $model_plugin Model_Plugin instance.
	 * @param Model_Activation $model_activation Model_Activation instance.
	 * @param Model_User_Data  $model_user_data Model_User_Data instance.
	 * @return array
	 * @since 1.0.0
	 */
	public function callback( \WP_REST_Request $request, Model_Plugin $model_plugin, Model_Activation $model_activation, Model_User_Data $model_user_data );

	/**
	 * Get name.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_name();

	/**
	 * Get rest route.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_rest_route();

	/**
	 * Get rest path.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_rest_path();

	/**
	 * Get rest method.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_rest_method();

	/**
	 * Get rest args.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_rest_args();

	/**
	 * Get rest permission.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function get_rest_permission();

	/**
	 * Get rest url.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_rest_url();
}
