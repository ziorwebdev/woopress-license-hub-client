<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap about-wrap full-width-layout woopress-license-hub-client-wrap">
	<form method="post">
		<h3><?php echo esc_html( $plugin_name ); ?> License</h3>
		<?php settings_fields( sanitize_key( $plugin_slug . '-woopress-license-hub-client-create' ) ); ?>
		<table class="widefat striped">
			<thead>
				<tr>
					<th colspan="2"><b><?php echo esc_html__( 'License', 'woopress-license-hub-client' ); ?></b></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Email', 'woopress-license-hub-client' ); ?></th>
					<td>
						<input type="email" name="<?php echo esc_html( $plugin_slug ); ?>[license_email]" placeholder="<?php echo esc_html__( 'Enter your order email.', 'woopress-license-hub-client' ); ?>" value="<?php echo esc_html( $user_data['license_email'] ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Key', 'woopress-license-hub-client' ); ?></th>
					<td>
						<input type="password" name="<?php echo esc_html( $plugin_slug ); ?>[license_key]" placeholder="<?php echo esc_html__( 'Enter your license key.', 'woopress-license-hub-client' ); ?>" value="<?php echo esc_attr( $user_data['license_key'] ); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button( esc_html__( 'Save', 'woopress-license-hub-client' ), 'primary' ); ?>	
	</form>	
	<form method="post">		
		<?php settings_fields( sanitize_key( $plugin_slug . '-woopress-license-hub-client-delete' ) ); ?>
		<table class="widefat striped" cellspacing="0">
			<thead>
				<tr>
					<th colspan="2"><b><?php echo esc_html__( 'Status', 'woopress-license-hub-client' ); ?></b></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( ! empty( $activation['license_created'] ) ) : ?>
					<tr>
						<td><?php echo esc_html__( 'Created', 'woopress-license-hub-client' ); ?></td>
						<td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $activation['license_created'] ) ) ); ?></td>
					</tr>
					<tr>
						<td><?php echo esc_html__( 'Limit', 'woopress-license-hub-client' ); ?></td>
						<td><?php echo $activation['license_limit'] ? esc_attr( $activation['license_limit'] ) : esc_html__( 'Unlimited', 'woopress-license-hub-client' ); ?></td>
					</tr>
					<tr>
						<td><?php echo esc_html__( 'Activations', 'woopress-license-hub-client' ); ?></td>
						<td><?php echo esc_attr( $activation['activation_count'] ); ?></td>
					</tr>
					<tr>
						<td><?php echo esc_html__( 'Updates', 'woopress-license-hub-client' ); ?></td>
						<td><?php echo '0000-00-00 00:00:00' !== $activation['license_expiration'] && $activation['license_updates'] ? sprintf( esc_html__( 'Expires on %s', 'woopress-license-hub-client' ), esc_html( $activation['license_expiration'] ) ) : esc_html__( 'Unlimited', 'woopress-license-hub-client' ); ?></td>
					</tr>
					<tr>
						<td><?php echo esc_html__( 'Support', 'woopress-license-hub-client' ); ?></td>
						<td><?php echo '0000-00-00 00:00:00' !== $activation['license_expiration'] && $activation['license_support'] ? sprintf( esc_html__( 'Expires on %s', 'woopress-license-hub-client' ), esc_html( $activation['license_expiration'] ) ) : esc_html__( 'Unlimited', 'woopress-license-hub-client' ); ?></td>
					</tr>
					<tr>
						<td><?php echo esc_html__( 'Expiration', 'woopress-license-hub-client' ); ?></td>
						<td><?php echo '0000-00-00 00:00:00' !== $activation['license_expiration'] ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $activation['license_expiration'] ) ) ) : esc_html__( 'Unlimited', 'woopress-license-hub-client' ); ?></td>
					</tr>
				<?php endif; ?>
				<tr>
					<td>
						<b><?php echo esc_html__( 'Notice', 'woopress-license-hub-client' ); ?></b>
					</td>
					<td>
						<span class="description">
							<?php if ( ! empty( $activation['message'] ) ) : ?>
								<?php echo esc_html( $activation['message'] ); ?>
							<?php endif; ?>
							<?php if ( ! empty( $activation['license_key'] ) ) : ?>
								<?php echo esc_html__( 'Thanks for register your license!', 'woopress-license-hub-client' ); ?>
							<?php endif; ?>
							<?php if ( empty( $activation['message'] ) && empty( $activation['license_key'] ) ) : ?>
								<?php echo esc_html__( 'Before you can receive plugin updates, you must first authenticate your license.', 'woopress-license-hub-client' ); ?>
							<?php endif; ?>

						</span>
					</td>
				</tr>
			</tbody>
		</table>
		<?php if ( $activation ) : ?>
			<?php if ( empty( $activation_delete_url ) ) : ?>
				<?php submit_button( esc_html__( 'Delete', 'woopress-license-hub-client' ), 'secondary' ); ?>
			<?php else : ?>
				<p class="submit" style="font-size: 14px;">
				<?php
					printf(
						wp_kses(
							__( 'Do you want to delete license activation? Please contact support <a href="%s" target="_blank">here</a>.', 'woopress-license-hub-client' ),
							array(
								'a' => array(
									'href'   => array(),
									'target' => array(),
								),
							)
						),
						esc_url( $activation_delete_url )
					);
				?>
				</p>
				<?php endif; ?>
		<?php endif; ?>
		<style>
			.woopress-license-hub-client-wrap td input {
				width: 100%
			}
		</style>
	</form>
</div>