# WooPress License Hub Client

The **WooPress License Hub Client** allows you to integrate license key validation into your WordPress plugins or themes. It communicates with the **WooPress License Hub** server to validate licenses when your product is installed on a customer’s site.

---

## Installation

Install via [Composer](https://getcomposer.org/):

```bash
composer require ziorwebdev/woopress-license-hub-client:dev-main
```

---

## Usage

After installing the package, include the following code inside your **WordPress plugin main file** (e.g., `your-plugin.php`):

```php
if ( ! function_exists( 'your_prefix_license_hub_client_integration' ) ) {
	function _your_prefix_license_hub_client_integration() {
		global $_your_prefix_license_hub_client;

		if ( ! isset( $_your_prefix_license_hub_client ) ) {
			$_your_prefix_license_hub_client = woopress_license_hub_client(
				array(
					'api_url'           => 'https://your-site.com/wp-json/wc/woopress-license-hub/',
					'product_key'       => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
					'plugin_file'       => __FILE__,
					'plugin_name'       => 'Your Plugin Name',
					'license_url'       => 'admin.php?page=your-plugin-slug',
					'parent_menu_slug'  => 'your-parent-menu-slug',
					'license_menu_slug' => 'your-plugin-slug',
				)
			);
		}

		return $_your_prefix_license_hub_client;
	}

	_your_prefix_license_hub_client_integration();
}
```

---

## Configuration

The `woopress_license_hub_client()` function accepts an **array of arguments** to configure your plugin’s license integration:

| Key                  | Required | Description |
|-----------------------|----------|-------------|
| **`api_url`**         | ✅ Yes   | The full REST API URL of your License Hub server. Example: `https://your-site.com/wp-json/wc/woopress-license-hub-client/` |
| **`product_key`**     | ✅ Yes   | The unique product key registered in your License Hub server. Replace `xxxxxxxxxxxxxxxxxxxxxxxxxxxxx` with your actual product key. |
| **`plugin_file`**     | ✅ Yes   | Typically `__FILE__`. Used by WordPress to identify the plugin file where the client is running. |
| **`plugin_name`**     | ✅ Yes   | The display name of your plugin (e.g., `"My Awesome Plugin"`). This name will appear in the License page. |
| **`license_url`**     | ⚡ Optional | The admin page URL where the license form should appear. Example: `admin.php?page=your-plugin-slug`. If you prefer adding it to **Settings → Your Plugin**, set this to: `options-general.php?page=your-plugin-slug`. |
| **`parent_menu_slug`** | ⚡ Optional | Defines the parent menu in the WordPress admin. For a top-level menu, set your own slug (e.g., `your-parent-menu-slug`). If you want it under **Settings**, use `options-general.php`. |
| **`license_menu_slug`** | ✅ Yes   | The slug for your plugin’s license menu. Typically the same as your plugin slug (e.g., `your-plugin-slug`). |

---

## Example Setup

If your plugin is named **"My Plugin"** and your plugin slug is `my-plugin`, a typical configuration looks like this:

```php
$_your_prefix_license_hub_client = woopress_license_hub_client(
	array(
		'api_url'           => 'https://licenses.mysite.com/wp-json/wc/woopress-license-hub/',
		'product_key'       => 'myplugin_1234567890abcdef',
		'plugin_file'       => __FILE__,
		'plugin_name'       => 'My Plugin',
		'license_url'       => 'admin.php?page=my-plugin',
		'parent_menu_slug'  => 'options-general.php',
		'license_menu_slug' => 'my-plugin',
	)
);
```

This will create a **License page** under **Settings → My Plugin** in your WordPress admin.

---

## Notes

- Always replace `your_prefix` with a unique prefix to avoid conflicts with other plugins.  
- Make sure the `product_key` matches the product registered on your License Hub server.  
- If you’re developing multiple plugins, each should have a **unique product key**.  
