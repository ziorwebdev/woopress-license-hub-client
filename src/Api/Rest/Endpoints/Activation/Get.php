<?php
/**
 * Activation get API rest endpoint
 *
 * @package ZiorWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints\Activation
 * @since 1.0.0
 */
namespace ZiorWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints\Activation;

use ZiorWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints\Base;
use ZiorWebDev\WooPressLicenseHubClient\Models\Plugin as Model_Plugin;
use ZiorWebDev\WooPressLicenseHubClient\Models\UserData as Model_User_Data;
use ZiorWebDev\WooPressLicenseHubClient\Models\Activation as Model_Activation;


/**
 * API_Rest_Activation_License_Get Class
 *
 * @package ZiorWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints\Activation
 * @since 1.0.0
 */
class Get extends Base {

	/**
	 * Define rest route path
	 *
	 * @var string
	 */
	protected $rest_route = 'activation';

	/**
	 * Process rest request. Ej: /wp-json/ziorwebdev/WooPressLicenseHubClient/xxx/activation
	 *
	 * @param \WP_REST_Request $request Request data.
	 * @param Model_Plugin     $model_plugin Model_Plugin instance.
	 * @param Model_Activation $model_activation Model_Activation instance.
	 * @param Model_User_Data  $model_user_data Model_User_Data instance.
	 * @return array
	 * @since 1.0.0
	 */
	public function callback( \WP_REST_Request $request, Model_Plugin $model_plugin, Model_Activation $model_activation, Model_User_Data $model_user_data ) {
		$activation = $model_activation->get();

		if ( empty( $activation ) ) {
			$response = array(
				'error'   => true,
				'message' => esc_html__( 'Unknown error. Please delete and try again.', 'woopress-license-hub-client' ),
			);

			return $this->handle_response( $response );
		}

		return $this->handle_response( $activation );
	}

	/**
	 * Get rest method
	 *
	 * @return string GET
	 * @since 1.0.0
	 */
	public function get_rest_method() {
		return \WP_REST_Server::READABLE;
	}
}
