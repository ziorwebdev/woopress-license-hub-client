<?php
/**
 * Activation create API rest endpoint
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints\Activation
 * @since 1.0.0
 */
namespace ZIORWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints\Activation;

use ZIORWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints\Base;
use ZIORWebDev\WooPressLicenseHubClient\Api\Fetch\Activation\Create as API_Fetch_Activation_Create;

use ZIORWebDev\WooPressLicenseHubClient\Models\Plugin as Model_Plugin;
use ZIORWebDev\WooPressLicenseHubClient\Models\UserData as Model_User_Data;
use ZIORWebDev\WooPressLicenseHubClient\Models\Activation as Model_Activation;

/**
 * API_Rest_Activation_License_Create Class
 *
 * @package ZIORWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints\Activation
 * @since 1.0.0
 */
class Create extends Base {

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
		$body = json_decode( $request->get_body() );

		if ( empty( $body->license_email ) ) {
			$response = array(
				'error'   => 1,
				'message' => esc_html__( 'license_email not setted.', 'woopress-license-hub-client' ),
			);
			return $this->handle_response( $response );
		}

		if ( empty( $body->license_key ) ) {
			$response = array(
				'error'   => 1,
				'message' => esc_html__( 'license_key not setted.', 'woopress-license-hub-client' ),
			);
			return $this->handle_response( $response );
		}

		$activation = ( new API_Fetch_Activation_Create( $model_plugin ) )->get_data(
			array_merge(
				(array) $body,
				array(
					'activation_site' => $model_plugin->get_activation_site(),
				)
			)
		);

		if ( isset( $activation->error ) ) {
			$response = array(
				'error'   => isset( $activation->error ) ? $activation->error : null,
				'message' => isset( $activation->message ) ? $activation->message : null,
			);

			$model_activation->delete();
			return $this->handle_response( $activation );
		}

		$model_activation->create( (array) $activation );

		return $this->handle_response( $activation );
	}

	/**
	 * Get rest method
	 *
	 * @return string POST
	 * @since 1.0.0
	 */
	public function get_rest_method() {
		return \WP_REST_Server::CREATABLE;
	}
}
