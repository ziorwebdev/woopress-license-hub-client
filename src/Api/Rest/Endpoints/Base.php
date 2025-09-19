<?php
/**
 * Base API rest endpoint
 *
 * @package ZiorWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints
 * @since 1.0.0
 */
namespace ZiorWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints;

use ZiorWebDev\WooPressLicenseHubClient\Api\Rest\RoutesLibrary;
use ZiorWebDev\WooPressLicenseHubClient\Models\Plugin as Model_Plugin;
use ZiorWebDev\WooPressLicenseHubClient\Models\UserData as Model_User_Data;
use ZiorWebDev\WooPressLicenseHubClient\Models\Activation as Model_Activation;

/**
 * Abstract Base Class
 *
 * Implemented by rest routes classes.
 *
 * @package ZiorWebDev\WooPressLicenseHubClient\Api\Rest\Endpoints
 * @since  1.0.0
 */
abstract class Base implements RouteInterface {

	/**
	 * Routes library.
	 *
	 * @var RoutesLibrary
	 */
	protected $routes_library;

	/**
	 * Rest route.
	 *
	 * @var string
	 */
	protected $rest_route;

	/**
	 * Setup class with routes library.
	 *
	 * @param array $client_data Client data.
	 * @param RoutesLibrary $routes_library Routes library.
	 * @since 1.0.0
	 */
	public function __construct( array $client_data, RoutesLibrary $routes_library ) {
		$this->routes_library = $routes_library;

		add_action(
			'rest_api_init',
			function () use ( $client_data ) {

				register_rest_route(
					$this->routes_library->get_rest_namespace(),
					$this->get_rest_route(),
					array(
						'args'                => $this->get_rest_args(),
						'methods'             => $this->get_rest_method(),
						'callback'            => function ( $request ) use ( $client_data ) {

							$model_plugin     = new Model_Plugin( $client_data );
							$model_activation = new Model_Activation( $model_plugin );
							$model_user_data = new Model_User_Data( $model_plugin );

							return $this->callback(
								$request,
								$model_plugin,
								$model_activation,
								$model_user_data
							);
						},
						'permission_callback' => array( $this, 'get_rest_permission' ),
					)
				);
			}
		);

		$routes_library->register( $this );
	}

	/**
	 * Get rest route name.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_name() {
		$rest_route = $this->get_rest_route();
		$method     = strtolower( static::get_rest_method() );

		return "$rest_route/$method";
	}

	/**
	 * Get rest route name.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_rest_route() {
		return $this->rest_route;
	}

	/**
	 * Get rest route path.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_rest_path() {
		$rest_namespace = $this->routes_library->get_rest_namespace();
		$rest_route     = $this->get_rest_route();

		return "{$rest_namespace}/{$rest_route}";
	}

	/**
	 * Get rest route args.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_rest_args() {
		return array();
	}

	/**
	 * Get rest route permission.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function get_rest_permission() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get rest route url.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_rest_url() {
		$blog_id   = get_current_blog_id();
		$rest_path = $this->get_rest_path();

		return get_rest_url( $blog_id, $rest_path );
	}

	/**
	 * Get error.
	 *
	 * @param int $code Error code.
	 * @param string $message Error message.
	 * @return array
	 * @since 1.0.0
	 */
	private static function get_error( $code, $message ) {
		return array(
			'code'    => $code,
			'message' => $message,
		);
	}

	/**
	 * Handle response.
	 *
	 * @param array $response Response.
	 * @return array
	 * @since 1.0.0
	 */
	public static function handle_response( $response ) {
		$response = (array) $response;

		if ( isset( $response['code'], $response['message'] ) ) {
			return rest_ensure_response(
				self::get_error(
					$response['code'],
					$response['message']
				)
			);
		}

		return $response;
	}
}
