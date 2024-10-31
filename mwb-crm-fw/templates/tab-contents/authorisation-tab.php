<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the accounts creation page.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Mwb_Gf_Integration_For_Hubspot
 * @subpackage Mwb_Gf_Integration_For_Hubspot/mwb-crm-fw/templates/tab-contents
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<?php if ( '1' !== get_option( 'mwb-gf-' . $this->crm_slug . '-crm-connected', false ) ) : ?>

	<section class="mwb-intro">
		<div class="mwb-content-wrap">
			<div class="mwb-intro__header">
				<h2 class="mwb-section__heading">
					<?php echo sprintf( 'Getting started with GF and %s', esc_html( $this->crm_name ) ); ?>
				</h2>
			</div>
			<div class="mwb-intro__body mwb-intro__content">
				<p>
				<?php
				echo sprintf(
					/* translators: %1$s:crm name, %2$s:crm name, $2$s:crm name, $4$s: crm modules */
					esc_html__( 'With this GF %1$s Integration you can easily sync all your GF Form Submissions data over %2$s. It will create %3$s over %4$s, based on your GF Form Feed data.', 'mwb-gf-integration-for-hubspot' ),
					esc_html( $this->crm_name ),
					esc_html( $this->crm_name ),
					esc_html( 'Contacts, Companies, Tickets, Tasks and Forms' ),
					esc_html( $this->crm_name )
				);
				?>
				</p>
				<ul class="mwb-intro__list">
					<li class="mwb-intro__list-item">
						<?php
						echo sprintf(
							/* translators: %s:crm name */
							esc_html__( 'Connect your %s account with GF.', 'mwb-gf-integration-for-hubspot' ),
							esc_html( $this->crm_name )
						);
						?>
					</li>
					<li class="mwb-intro__list-item">
						<?php
						echo sprintf(
							/* translators: %s:crm name */
							esc_html__( 'Sync your data over %s.', 'mwb-gf-integration-for-hubspot' ),
							esc_html( $this->crm_name )
						);
						?>
					</li>
				</ul>
				<div class="mwb-intro__button">
					<a href="javascript:void(0)" class="mwb-btn mwb-btn--filled" id="mwb-showauth-form">
						<?php esc_html_e( 'Connect your Account.', 'mwb-gf-integration-for-hubspot' ); ?>
					</a>
				</div>
			</div> 
		</div>
	</section>


	<!--============================================================================================
											Authorization form start.
	================================================================================================-->

	<div class="mwb_gf_integration_account-wrap <?php echo esc_html( false === get_option( 'mwb-gf-' . $this->crm_slug . '-crm-connected', false ) ? 'row-hide' : '' ); ?>" id="mwb-gf-auth_wrap">
		<!-- Logo section start -->
		<div class="mwb-gf_integration_logo-wrap">
			<div class="mwb-gf_integration_logo-crm">
				<img src="<?php echo esc_url( MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/crm.png' ); ?>" alt="<?php esc_html_e( 'HubSpot', 'mwb-gf-integration-for-hubspot' ); ?>">
			</div>
			<div class="mwb-gf_integration_logo-contact">
				<img src="<?php echo esc_url( MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/gravity-form.png' ); ?>" alt="<?php esc_html_e( 'GF', 'mwb-gf-integration-for-hubspot' ); ?>">
			</div>
		</div>
		<!-- Logo section end -->

		<!-- Login form start -->
		<form method="post" id="mwb_gf_integration_account_form">

			<div class="mwb_gf_integration_table_wrapper">
				<div class="mwb_gf_integration_account_setup">
					<h2>
						<?php esc_html_e( 'Enter your credentials here', 'mwb-gf-integration-for-hubspot' ); ?>
					</h2>
				</div>

				<table class="mwb_gf_integration_table">
					<tbody>
						<div class="mwb-auth-notice-wrap row-hide">
							<p class="mwb-auth-notice-text">
								<?php esc_html_e( 'Connection has been successful ! Validating .....', 'mwb-gf-integration-for-hubspot' ); ?>
							</p>
						</div>

						<!-- Own app start -->
						<tr>
							<th>
								<label>
									<?php esc_html_e( 'Use own app', 'mwb-gf-integration-for-hubspot' ); ?>
								</label>

								<td>
									<input type="checkbox" name="mwb_account[own_app]" id="mwb-<?php echo esc_attr( $this->crm_slug ); ?>-gf-own-app" value="yes" <?php checked( 'yes', $params['own_app'] ); ?> >
								</td>
							</th>
						</tr>
						<!-- Own app end -->

						<!-- client Id start  -->
						<tr class="mwb-api-fields">
							<th>							
								<label><?php esc_html_e( 'Client ID', 'mwb-gf-integration-for-hubspot' ); ?></label>
							</th>

							<td>
								<?php
								$client_id = ! empty( $params['client_id'] ) ? sanitize_text_field( wp_unslash( $params['client_id'] ) ) : '';
								?>
								<div class="mwb-gf-integration__secure-field">
									<input type="password"  name="mwb_account[client_id]" id="mwb-<?php echo esc_attr( $this->crm_slug ); ?>-gf-client-id" value="<?php echo esc_html( $client_id ); ?>" required placeholder="<?php esc_html_e( 'Enter Client ID', 'mwb-gf-integration-for-hubspot' ); ?>">
									<div class="mwb-gf-integration__trailing-icon">
										<span class="dashicons dashicons-visibility mwb-toggle-view"></span>
									</div>
								</div>
							</td>
						</tr>
						<!-- Client id end -->

						<!-- client secret start  -->
						<tr class="mwb-api-fields">
							<th>							
								<label><?php esc_html_e( 'Client Secret', 'mwb-gf-integration-for-hubspot' ); ?></label>
							</th>

							<td>
								<?php
								$client_secret = ! empty( $params['client_secret'] ) ? sanitize_text_field( wp_unslash( $params['client_secret'] ) ) : '';
								?>
								<div class="mwb-gf-integration__secure-field">
									<input type="password"  name="mwb_account[client_secret]" id="mwb-<?php echo esc_attr( $this->crm_slug ); ?>-gf-client-secret" value="<?php echo esc_html( $client_secret ); ?>" required placeholder="<?php esc_html_e( 'Enter Client Secret', 'mwb-gf-integration-for-hubspot' ); ?>">
									<div class="mwb-gf-integration__trailing-icon">
										<span class="dashicons dashicons-visibility mwb-toggle-view"></span>
									</div>
								</div>
							</td>
						</tr>
						<!-- Client secret end -->

						<!-- client secret start  -->
						<tr class="mwb-api-fields">
							<th>							
								<label><?php esc_html_e( 'Scopes', 'mwb-gf-integration-for-hubspot' ); ?></label>
							</th>

							<td>
								<?php
								$scopes = ! empty( $params['scopes'] ) ? sanitize_text_field( wp_unslash( $params['scopes'] ) ) : '';
								?>
								<textarea name="mwb_account[client_secret]" id="mwb-<?php echo esc_attr( $this->crm_slug ); ?>-gf-scopes" required ><?php echo esc_html( $scopes ); ?></textarea>
								<p class="mwb-description">
									<?php esc_html_e( 'Copy Scopes from your HubSpot app and paste here seperated by commas. For eg: {oauth, contacts, tickets} or {crm.objects.contacts.read, crm.objects.contacts.write}', 'mwb-gf-integration-for-hubspot' ); ?>
								</p>
							</td>
						</tr>
						<!-- Client secret end -->

						<!-- Redirect uri start -->
						<tr class="mwb-api-fields">
							<th>
								<label><?php esc_html_e( 'Redirect URI', 'mwb-gf-integration-for-hubspot' ); ?></label>
							</th>

							<td>
								<input type="url" name="mwb_account[redirect_uri]" id="mwb-<?php echo esc_attr( $this->crm_slug ); ?>-gf-redirect-uri" value="<?php echo esc_html( rtrim( admin_url(), '/' ) ); ?>" readonly >
								<p class="mwb-description">
									<?php esc_html_e( 'Web-Protocol must be HTTPS in order to successfully authorize with HubSpot', 'mwb-gf-integration-for-hubspot' ); ?>
								</p>
							</td>
						</tr>
						<!-- redirect uri end -->

						<!-- Save & connect account start -->
						<tr>
							<th>
							</th>
							<td>
								<a href="<?php echo esc_url( wp_nonce_url( admin_url( '?mwb-gf-integration-perform-auth=1' ) ) ); ?>" class="mwb-btn mwb-btn--filled mwb_gf_integration_submit_account" id="mwb-<?php echo esc_attr( $this->crm_slug ); ?>-gf-authorize-button" ><?php esc_html_e( 'Authorize', 'mwb-gf-integration-for-hubspot' ); ?></a>
							</td>
						</tr>
						<!-- Save & connect account end -->
					</tbody>
				</table>
			</div>
		</form>
		<!-- Login form end -->

		<!-- Info section start -->
		<div class="mwb-intro__bottom-text-wrap">
			<p>
				<?php esc_html_e( 'Don’t have an account yet . ', 'mwb-gf-integration-for-hubspot' ); ?>
				<a href="https://www.hubspot.com/" target="_blank" class="mwb-btn__bottom-text">
					<?php esc_html_e( 'Create A Free Account', 'mwb-gf-integration-for-hubspot' ); ?>
				</a>
			</p>
			<p class="mwb-api-fields">
				<?php esc_html_e( 'Get Your Api Key here.', 'mwb-gf-integration-for-hubspot' ); ?>
				<a href="https://app.hubspot.com/signup/developers/" target="_blank" class="mwb-btn__bottom-text"><?php esc_html_e( 'Get Api Keys', 'mwb-gf-integration-for-hubspot' ); ?></a>
			</p>
			<p >
				<?php esc_html_e( 'Check app setup guide . ', 'mwb-gf-integration-for-hubspot' ); ?>
				<a href="javascript:void(0)" class="mwb-btn__bottom-text trigger-setup-guide"><?php esc_html_e( 'Show Me How', 'mwb-gf-integration-for-hubspot' ); ?></a>
			</p>
		</div>
		<!-- Info section end -->
	</div>

<?php else : ?>

	<!-- Successfull connection start -->
	<section class="mwb-sync">
		<div class="mwb-content-wrap">
			<div class="mwb-sync__header">
				<h2 class="mwb-section__heading">
					<?php
					echo sprintf(
						/* translators: %s:crm name */
						esc_html__( 'Congrats! You’ve successfully set up the MWB GF Integration with %s Plugin.', 'mwb-gf-integration-for-hubspot' ),
						esc_html( $this->crm_name )
					);
					?>
				</h2>
			</div>
			<div class="mwb-sync__body mwb-sync__content-wrap">            
				<div class="mwb-sync__image">    
					<img src="<?php echo esc_url( MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/congo.jpg' ); ?>" >
				</div>       
				<div class="mwb-sync__content">            
					<p> 
						<?php
						echo sprintf(
							/* translators: %s:crm name */
							esc_html__( 'Now you can go to the dashboard and check connection data. You can create your feeds, edit them in the feeds tab. If you do not see your data over %s, you can check the logs for any possible error.', 'mwb-gf-integration-for-hubspot' ),
							esc_html( $this->crm_name )
						);
						?>
					</p>
					<div class="mwb-sync__button">
						<a href="javascript:void(0)" class="mwb-btn mwb-btn--filled mwb-onboarding-complete">
							<?php esc_html_e( 'View Dashboard', 'mwb-gf-integration-for-hubspot' ); ?>
						</a>
					</div>
				</div>             
			</div>       
		</div>
	</section>
	<!-- Successfull connection end -->

<?php endif; ?>
