<?php
/**
 * Example of how to integrate WooPress License Hub Client with your plugin.
 * Include this file in the root folder of your WordPress plugin and setup settings based on your product
 * Documentation: https://github.com/ziorwebdev/woopress-license-hub-client
 *
 * @package   ziorwebdev/woopress-license-hub-client
 * @link      https://github.com/ziorwebdev/woopress-license-hub-client
 */

if ( ! function_exists( 'your_prefix_license_hub_client_integration' ) ) {
	function _your_prefix_license_hub_client_integration() {
		global $_your_prefix_license_hub_client;

		if ( ! isset( $_your_prefix_license_hub_client ) ) {
			$_your_prefix_license_hub_client = woopress_license_hub_client(
				array(
					'api_url'           => 'https://your-site.com/wp-json/wc/woopress-license-hub-client/',
					'product_key'       => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
					'plugin_file'       => __FILE__,
					'plugin_name'       => 'Your Plugin Name',
					'license_url'       => 'admin.php?page=your-plugin-slug', // If you want to add the license page to the options page, set this to 'options-general.php?page=your-plugin-slug'
					'parent_menu_slug'  => 'you-parent-menu-slug', // If you want to add the license page to the options page, set this to 'options-general.php'
					'license_menu_slug' => 'your-plugin-slug',
				)
			);
		}

		return $_your_prefix_license_hub_client;
	}

	_your_prefix_license_hub_client_integration();
}
