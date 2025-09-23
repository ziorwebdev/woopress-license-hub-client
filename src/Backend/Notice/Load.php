<?php
/**
 * Load notice
 *
 * @package ZiorWebDev\WooPressLicenseHubClient\Backend\Notice
 * @since 1.0.0
 */
namespace ZiorWebDev\WooPressLicenseHubClient\Backend\Notice;

use ZiorWebDev\WooPressLicenseHubClient\Models\Plugin as Model_Plugin;
use ZiorWebDev\WooPressLicenseHubClient\Models\Activation as Model_Activation;
use ZiorWebDev\WooPressLicenseHubClient\Models\UserData as Model_User_Data;

/**
 * Controller_Notice Class
 *
 * @package ZiorWebDev\WooPressLicenseHubClient\Backend\Notice
 * @since 1.0.0
 */
class Load {

	/**
	 * Plugin model
	 *
	 * @var Model_Plugin
	 */
	protected $plugin;
	
	/**
	 * Activation model
	 *
	 * @var Model_Activation
	 */
	protected $activation;
	
	/**
	 * User data model
	 *
	 * @var Model_User_Data
	 */
	protected $user_data;

	/**
	 * Constructor
	 *
	 * @param Model_Plugin $model_plugin Plugin model
	 * @param Model_Activation $model_activation Activation model
	 * @param Model_User_Data $model_user_data User data model
	 * @since 1.0.0
	 */
	public function __construct( Model_Plugin $model_plugin, Model_Activation $model_activation, Model_User_Data $model_user_data = null ) {
		$this->plugin     = $model_plugin;
		$this->activation = $model_activation;
		$this->user_data  = $model_user_data;

		add_action( 'admin_notices', array( $this, 'add_license_activate' ) );
		add_action( 'admin_notices', array( $this, 'add_license_expired' ) );
		add_action( 'admin_notices', array( $this, 'add_license_error' ) );
	}

	/**
	 * Add license activate
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_license_activate() {
		if ( 'none' !== $this->activation->status() ) {
			return;
		}

		$license_url = $this->plugin->get_menu_license_url();
		if ( ! $license_url ) {
			return;
		}

		// Text strings
		$title   = sprintf(
			esc_html__( 'Please activate your %s license key.', 'woopress-license-hub-client' ),
			esc_html( $this->plugin->get_name() )
		);

		$message = esc_html__(
			'Please complete the license activation process to receive automatic updates and enable all premium features.',
			'woopress-license-hub-client'
		);

		// Buttons
		$activate_btn = sprintf(
			'<a href="%s" class="button-primary">%s</a>',
			esc_url( $license_url ),
			esc_html__( 'Activate', 'woopress-license-hub-client' )
		);

		$purchase_btn = sprintf(
			'<a href="%s" target="_blank" class="button-secondary">%s</a>',
			esc_url( $this->plugin->get_url() ),
			esc_html__( 'Purchase', 'woopress-license-hub-client' )
		);

		$license_key_btn = $this->plugin->get_license_key_url()
			? sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( $this->plugin->get_license_key_url() ),
				esc_html__( 'Get license key', 'woopress-license-hub-client' )
			)
			: '';

		$support_btn = $this->plugin->get_support_url()
			? sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( $this->plugin->get_support_url() ),
				esc_html__( 'Get support', 'woopress-license-hub-client' )
			)
			: '';

		// Final HTML
		echo sprintf(
			'<div class="notice notice-error">
				<div class="notice-container" style="padding:10px 0; display:flex; align-items:center;">
					<div class="notice-content" style="margin-left:15px;">
						<p><strong>%1$s</strong><br/>%2$s</p>
						<p style="display:flex; align-items:center; gap:15px;">%3$s %4$s %5$s %6$s</p>
					</div>
				</div>
			</div>',
			$title,
			$message,
			$activate_btn,
			$purchase_btn,
			$license_key_btn,
			$support_btn
		);
	}

	/**
	 * Add license expired
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_license_expired() {
		// Don't show if already dismissed within 7 days.
		$dismissed_until = (int) get_user_meta( get_current_user_id(), '_license_expired_dismissed_until', true );
		if ( $dismissed_until && $dismissed_until > time() ) {
			return;
		}

		if ( 'expired' !== $this->activation->status() ) {
			return;
		}

		if ( ! $this->plugin->get_menu_license_url() ) {
			return;
		}

		$activation = $this->activation->get();
		$user       = wp_get_current_user();

		// Message formatting
		$title   = sprintf(
			esc_html__( 'Your %s license has expired.', 'woopress-license-hub-client' ),
			esc_html( $this->plugin->get_name() )
		);

		$message = sprintf(
			esc_html__( 'Hello %1$s, your license has expired. You can still access premium features for 7 more days (until %2$s). Renew now to avoid losing them.', 'woopress-license-hub-client' ),
			esc_html( $user->display_name ),
			! empty( $activation['license_expiration'] ) ? esc_html( $activation['license_expiration'] ) : esc_html__( 'unknown date', 'woopress-license-hub-client' )
		);

		$renew_btn = sprintf(
			'<a href="%s" class="button-secondary">%s</a>',
			esc_url( $this->plugin->get_license_key_url() ),
			esc_html__( 'Renew License', 'woopress-license-hub-client' )
		);

		$support_btn = '';
		if ( $this->plugin->get_support_url() ) {
			$support_btn = sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( $this->plugin->get_support_url() ),
				esc_html__( 'Get Support', 'woopress-license-hub-client' )
			);
		}

		// Final HTML output
		echo sprintf(
			'<div class="notice notice-error is-dismissible license-expired-notice" data-notice="license_expired">
				<p><strong>%1$s</strong><br/>%2$s</p>
				<p style="display:flex;align-items:center;gap:15px;">%3$s %4$s</p>
			</div>',
			$title,
			$message,
			$renew_btn,
			$support_btn
		);

		// TODO: Make the notice dismissible
		// Enqueue dismiss script
		// add_action( 'admin_footer', array( $this, 'enqueue_license_notice_script' ) );
	}

	/**
	 * Display license API error messages with detailed explanation about premium features
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_license_error() {
		if ( 'error' !== $this->activation->status() ) {
			return;
		}

		$activation = $this->activation->get();

		if ( empty( $activation['message'] ) ) {
			return;
		}

		$plugin_name     = $this->plugin->get_name();
		$message         = $activation['message'];
		$license_url     = $this->plugin->get_menu_license_url();
		$license_key_url = $this->plugin->get_license_key_url();

		// Title + message
		$title = sprintf(
			esc_html__( '%s license activation error!', 'woopress-license-hub-client' ),
			esc_html( $plugin_name )
		);

		$error_message = esc_html( $message );

		// Important warning
		$warning = sprintf(
			'<p><strong>%1$s</strong> %2$s</p>',
			esc_html__( 'Important:', 'woopress-license-hub-client' ),
			sprintf(
				esc_html__( '%s will be deactivated because your license is not active. Please resolve this issue to restore full functionality.', 'woopress-license-hub-client' ),
				esc_html( $plugin_name )
			)
		);

		// Buttons
		$activate_btn = $license_url
			? sprintf(
				'<a href="%s" class="button-primary">%s</a>',
				esc_url( $license_url ),
				esc_html__( 'Activate', 'woopress-license-hub-client' )
			)
			: '';

		$renew_btn = $license_key_url
			? sprintf(
				'<a href="%s" target="_blank" class="button-secondary">%s</a>',
				esc_url( $license_key_url ),
				esc_html__( 'Renew', 'woopress-license-hub-client' )
			)
			: '';

		$support_btn = $this->plugin->get_support_url()
			? sprintf(
				'<a href="%s" target="_blank">%s</a>',
				esc_url( $this->plugin->get_support_url() ),
				esc_html__( 'Get support', 'woopress-license-hub-client' )
			)
			: '';

		// Final output
		echo sprintf(
			'<div class="notice notice-error">
				<div class="notice-container" style="padding:10px 0; display:flex; align-items:center;">
					<div class="notice-content" style="margin-left:15px;">
						<p><strong>%1$s</strong><br/>%2$s</p>
						%3$s
						<p style="display:flex; align-items:center; gap:15px;">%4$s %5$s %6$s</p>
					</div>
				</div>
			</div>',
			$title,
			$error_message,
			$warning,
			$activate_btn,
			$renew_btn,
			$support_btn
		);
	}
}
