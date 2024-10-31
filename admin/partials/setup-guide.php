<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Mwb_Gf_Integration_For_Hubspot
 * @subpackage Mwb_Gf_Integration_For_Hubspot/admin/partials
 */

?>
<div class="mwb-crm-setup-content-wrap">
	<div class="mwb-crm-setup-list-wrap">
		<ul class="mwb-crm-setup-list">
			<?php if ( 'yes' == $custom_app ) : // phpcs:ignore ?>
				<li>
					<a href="https://app.hubspot.com/signup/developers/"><?php esc_html_e( 'Login', 'mwb-gf-integration-for-hubspot' ); ?></a>
					<?php esc_html_e( ' to your developer account.', 'mwb-gf-integration-for-hubspot' ); ?>
				</li>
				<li>
					<?php esc_html_e( 'Click on Manage apps to manage your existing app or to create a new one.', 'mwb-gf-integration-for-hubspot' ); ?>
				</li>
				<li>
					<?php esc_html_e( 'Click on Create app. It will open a new tab.', 'mwb-gf-integration-for-hubspot' ); ?>
				</li>
				<li>
					<?php esc_html_e( 'Fill up mandatory informations like "Public App Name" on the app info tab', 'mwb-gf-integration-for-hubspot' ); ?>
				</li>
				<li>
					<?php esc_html_e( 'New app will be created and its its credentials will be displayed on auth info tab.', 'mwb-gf-integration-for-hubspot' ); ?>
				</li>
				<li>
					<?php
					echo wp_kses(
						sprintf(
						/* translators: Feed object name */
							__( 'Enter  <strong> %s </strong> as Redirect URL.', 'mwb-gf-integration-for-hubspot' ),
							$params['callback_url']
						),
						$params['allowed_html']
					);
					?>
				</li>
				<li>
					<?php esc_html_e( 'Copy "Client ID" and "Client secret" from there and enter it in Authentication form.', 'mwb-gf-integration-for-hubspot' ); ?>
				</li>
			<?php else : ?>
				<li>
					<?php esc_html_e( 'Click on authorize button.', 'mwb-gf-integration-for-hubspot' ); ?>
				</li>
				<li>
					<?php esc_html_e( 'It will redirect you to HubSpot login panel.', 'mwb-gf-integration-for-hubspot' ); ?>
				</li>
				<li>
					<?php esc_html_e( 'Select the HubSpot portal you want to connet with.', 'mwb-gf-integration-for-hubspot' ); ?>
				</li>
				<li>
					<?php esc_html_e( 'After successful login, it will redirect you to consent page, where it will ask your permissions to access the data.', 'mwb-gf-integration-for-hubspot' ); ?>
				</li>
				<li>
					<?php esc_html_e( 'Click on allow, it should redirect back to your plugin admin page and your connection part is done.', 'mwb-gf-integration-for-hubspot' ); ?>
				</li>
			<?php endif; ?>
			<li>
			<?php
			echo wp_kses(
				sprintf(
				/* translators: Feed object name */
					__( 'Still facing issue! Please check detailed app setup <a href="%s" target="_blank"  >documentation</a>.', 'mwb-gf-integration-for-hubspot' ),
					$params['setup_guide_url']
				),
				$params['allowed_html']
			);
			?>
			</li>
		</ul>
		<?php if ( 'yes' == $custom_app ) : // phpcs:ignore ?>
			<img style="width: 100%; border: 2px solid;" src="<?php echo esc_url( $params['api_key_image'] ); ?>">
		<?php endif; ?>
	</div>
</div>
