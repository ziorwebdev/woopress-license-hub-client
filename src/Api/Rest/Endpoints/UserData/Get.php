<?php
/**
 * User data get API rest endpoint
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints\UserData
 * @since 1.0.0
 */
namespace ZIORWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints\UserData;

use ZIORWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints\Base;
use ZIORWebDev\WooPressLicenseHubClient\Models\Plugin as Model_Plugin;
use ZIORWebDev\WooPressLicenseHubClient\Models\UserData as Model_User_Data;
use ZIORWebDev\WooPressLicenseHubClient\Models\Activation as Model_Activation;

/**
 * API_Rest_User_Data_Get Class
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints\UserData
 * @since 1.0.0
 */
class Get extends Base {

	/**
	 * Define rest route path
	 *
	 * @var string
	 */
	protected $rest_route = 'user-data';

	/**
	 * Process rest request. Ej: /wp-json/ziorwebdev/WooPressLicenseHubClient/xxx/user-data
	 *
	 * @param \WP_REST_Request $request Request data.
	 * @param Model_Plugin     $model_plugin Model_Plugin instance.
	 * @param Model_Activation $model_activation Model_Activation instance.
	 * @param Model_User_Data  $model_user_data Model_User_Data instance.
	 * @return array
	 * @since 1.0.0
	 */
	public function callback( \WP_REST_Request $request, Model_Plugin $model_plugin, Model_Activation $model_activation, Model_User_Data $model_user_data ) {
		$user_data = $model_user_data->get();

		if ( false === $user_data ) {
			$response = array(
				'error'   => 1,
				'message' => esc_html__( 'User data could not be found.', 'woopress-license-hub-client' ),
			);

			return $this->handle_response( $response );
		}

		return $this->handle_response( $user_data );
	}

	/**
	 * Get rest method
	 *
	 * @return string POST
	 * @since 1.0.0
	 */
	public function get_rest_method() {
		return \WP_REST_Server::READABLE;
	}
}
