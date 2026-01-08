<?php
/**
 * User data delete API rest endpoint
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
 * API_Rest_User_Data_Delete Class
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints\UserData
 * @since 1.0.0
 */
class Delete extends Base {

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
		$status = $model_user_data->delete();

		if ( ! $status ) {
			$response = array(
				'error'   => 1,
				'message' => esc_html__( 'User data could not be deleted.', 'woopress-license-hub-client' ),
			);

			return $this->handle_response( $response );
		}

		return $this->handle_response( true );
	}

	/**
	 * Get rest method
	 *
	 * @return string DELETE
	 * @since 1.0.0
	 */
	public function get_rest_method() {
		return \WP_REST_Server::DELETABLE;
	}
}
