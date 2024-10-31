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
<div class="mwb_gf_integration_account-wrap">

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

	<!--============================================================================================
										Dashboard page start.
	================================================================================================-->

	<!-- Connection status start -->
	<div class="mwb_gf_integration_crm_connected">
		<ul>
			<li class="mwb-gf_intergation_conn-row">
				<div class="mwb-gf-integration__content-wrap">
					<div class="mwb-section__sub-heading__wrap">
						<h3 class="mwb-section__sub-heading">
							<?php echo sprintf( '%s %s', esc_html( $this->crm_name ), esc_html__( 'Connection Status', 'mwb-gf-integration-for-hubspot' ) ); ?>
						</h3>
						<div class="mwb-dashboard__header-text">
							<span class="is-connected" >
								<?php esc_html_e( 'Connected', 'mwb-gf-integration-for-hubspot' ); ?>
							</span>
						</div>
					</div>

					<div class="mwb-gf-integration__status-wrap">
						<div class="mwb-gf-integration__left-col">
							<div class="mwb-gf-integration-token-notice__wrap">
								<?php if ( ! empty( $params['portalId'] ) ) : ?>
									<p>
										<?php
										/* translators: %s: owner name */
										printf( esc_html__( 'Portal ID : %s', 'mwb-gf-integration-for-hubspot' ), esc_html( $params['portalId'] ) );
										?>
									</p>
								<?php endif; ?>
							</div>
							<div class="mwb-gf-integration-token-notice__wrap">
								<?php if ( ! empty( $params['timezone'] ) ) : ?>
									<p>
										<?php
										/* translators: %s: owner name */
										printf( esc_html__( 'Account TimeZone : %s', 'mwb-gf-integration-for-hubspot' ), esc_html( $params['timezone'] ) );
										?>
									</p>
								<?php endif; ?>
							</div>
							<div class="mwb-gf-integration-token-notice__wrap">
								<p id="mwb-gf-token-expiry-notice" >
									<?php if ( $params['expires_in'] > time() ) : ?>
										<?php
										$duration = ceil( ( $params['expires_in'] - time() ) / 60 );
										printf(
											/* translators: %s: time */
											esc_html__( 'Access token will expire in %1$s hours %2$s minutes.', 'mwb-gf-integration-for-hubspot' ),
											esc_html( floor( $duration / 60 ) ),
											esc_html( $duration % 60 )
										);
										?>
									<?php else : ?>
										<?php esc_html_e( 'Access token has expired.', 'mwb-gf-integration-for-hubspot' ); ?>
									<?php endif; ?>

								</p>
								<p class="mwb-gf-integration-token_refresh ">
									<img id ="mwb_gf_integration_refresh_token" src="<?php echo esc_url( MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/refresh.svg' ); ?>" title="<?php esc_html_e( 'Refresh Access Token', 'mwb-gf-integration-for-hubspot' ); ?>">
								</p>
							</div>
						</div>

						<div class="mwb-gf-integration__right-col">
							<a id="mwb_gf_integration_reauthorize" href="<?php echo esc_url( wp_nonce_url( admin_url( '?mwb-gf-integration-perform-reauth=1' ) ) ); ?>" class="mwb-btn mwb-btn--filled">
								<?php esc_html_e( 'Reauthorize', 'mwb-gf-integration-for-hubspot' ); ?>
							</a>
							<a id="mwb_gf_integration_disconnect" href="javascript:void(0)" class="mwb-btn mwb-btn--filled">
								<?php esc_html_e( 'Disconnect', 'mwb-gf-integration-for-hubspot' ); ?>
							</a>
						</div>
					</div>
				</div>
			</li>
		</ul>
	</div>
	<!-- Connection status end -->

	<!-- About list start -->
	<div class="mwb-dashboard__about">
		<div class="mwb-dashboard__about-list">
			<div class="mwb-content__list-item-text">
				<h2 class="mwb-section__heading"><?php esc_html_e( 'Synced Gravity Forms', 'mwb-gf-integration-for-hubspot' ); ?></h2>
				<div class="mwb-dashboard__about-number">
					<span><?php echo esc_html( ! empty( $params['count'] ) ? $params['count'] : '0' ); ?></span>
				</div>
				<div class="mwb-dashboard__about-number-desc">
					<p>

						<i><?php esc_html_e( 'Total number of Gravity Form submission data which are synced over HubSpot.', 'mwb-gf-integration-for-hubspot' ); ?>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=mwb_' . $this->crm_slug . '_gf_page&tab=logs' ) ); ?>" target="_blank"><?php esc_html_e( 'View log', 'mwb-gf-integration-for-hubspot' ); ?></a></i>
					</p>
				</div>
			</div>
			<div class="mwb-content__list-item-image">
				<img src="<?php echo esc_url( MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/deals.svg' ); ?>" alt="<?php esc_html_e( 'Synced Gravity Forms', 'mwb-gf-integration-for-hubspot' ); ?>">
			</div>
		</div>

		<?php do_action( 'mwb_' . $this->crm_slug . '_gf_about_list' ); ?>

	</div>
	<!-- About list end -->

	<!-- Support section start -->
	<div class="mwb-content-wrap">
		<ul class="mwb-about__list">
			<li class="mwb-about__list-item">
				<div class="mwb-about__list-item-text">
					<p><?php esc_html_e( 'Need any help ? Check our documentation.', 'mwb-gf-integration-for-hubspot' ); ?></p>
				</div>
				<div class="mwb-about__list-item-btn">
					<a href="<?php echo esc_url( ! empty( $params['links']['doc'] ) ? $params['links']['doc'] : '' ); ?>" class="mwb-btn mwb-btn--filled"><?php esc_html_e( 'Documentation', 'mwb-gf-integration-for-hubspot' ); ?></a>
				</div>
			</li>
			<li class="mwb-about__list-item">
				<div class="mwb-about__list-item-text">
					<p><?php esc_html_e( 'Facing any issue ? Open a support ticket.', 'mwb-gf-integration-for-hubspot' ); ?></p>
				</div>
				<div class="mwb-about__list-item-btn">
					<a href="<?php echo esc_url( ! empty( $params['links']['ticket'] ) ? $params['links']['ticket'] : '' ); ?>" class="mwb-btn mwb-btn--filled"><?php esc_html_e( 'Support', 'mwb-gf-integration-for-hubspot' ); ?></a>
				</div>
			</li>
			<li class="mwb-about__list-item">
				<div class="mwb-about__list-item-text">
					<p><?php esc_html_e( 'Need personalized solution, contact us !', 'mwb-gf-integration-for-hubspot' ); ?></p>
				</div>
				<div class="mwb-about__list-item-btn">
					<a href="<?php echo esc_url( ! empty( $params['links']['contact'] ) ? $params['links']['contact'] : '' ); ?>" class="mwb-btn mwb-btn--filled"><?php esc_html_e( 'Connect', 'mwb-gf-integration-for-hubspot' ); ?></a>
				</div>
			</li>
		</ul>	
	</div>
	<!-- Support section end -->

</div>

