<?php

if ( ! function_exists( 'woopress_license_hub_client' ) ) {
	function woopress_license_hub_client( array $client_data ) {
		$client = new ZIORWebDev\WooPressLicenseHubClient\Load( $client_data );

		return $client;
	}
}
