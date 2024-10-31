<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the crm logs listing aspects of the plugin.
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
<div class="mwb-gf-integration__logs-wrap" id="mwb-<?php echo esc_attr( $this->crm_slug ); ?>-gf-logs" ajax_url="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>">
	<div class="mwb-gf_integration_logo-wrap">
		<div class="mwb-gf_integration_logo-crm">
			<img src="<?php echo esc_url( MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/crm.png' ); ?>" alt="<?php esc_html_e( 'HubSpot', 'mwb-gf-integration-for-hubspot' ); ?>">
		</div>
		<div class="mwb-gf_integration_logo-contact">
			<img src="<?php echo esc_url( MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/gravity-form.png' ); ?>" alt="<?php esc_html_e( 'GF', 'mwb-gf-integration-for-hubspot' ); ?>">
		</div>
		<?php if ( $params['log_enable'] ) : ?>
				<ul class="mwb-logs__settings-list">
					<li class="mwb-logs__settings-list-item">
						<a id="mwb-<?php echo esc_attr( $this->crm_slug ); ?>-gf-clear-log" href="javascript:void(0)" class="mwb-logs__setting-link">
							<?php esc_html_e( 'Clear Log', 'mwb-gf-integration-for-hubspot' ); ?>	
						</a>
					</li>
					<li class="mwb-logs__settings-list-item">
						<a id="mwb-<?php echo esc_attr( $this->crm_slug ); ?>-gf-download-log" href="javascript:void(0)"  class="mwb-logs__setting-link">
							<?php esc_html_e( 'Download', 'mwb-gf-integration-for-hubspot' ); ?>	
						</a>
					</li>
				</ul>
		<?php endif; ?>
	</div>
	<div>
		<div>
			<?php if ( $params['log_enable'] ) : ?>
			<div class="mwb-gf-integration__logs-table-wrap">
				<table id="mwb-<?php echo esc_attr( $this->crm_slug ); ?>-gf-table" class="display mwb-gf-integration__logs-table dt-responsive nowrap" style="width: 100%;">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Expand', 'mwb-gf-integration-for-hubspot' ); ?></th>
							<th><?php esc_html_e( 'Feed', 'mwb-gf-integration-for-hubspot' ); ?></th>
							<th><?php esc_html_e( 'Feed ID', 'mwb-gf-integration-for-hubspot' ); ?></th>
							<th>
								<?php
								/* translators: %s: CRM name. */
								printf( esc_html__( '%s Object', 'mwb-gf-integration-for-hubspot' ), esc_html( $this->crm_name ) );
								?>
							</th>
							<th>
								<?php
								/* translators: %s: CRM name. */
								printf( esc_html__( '%s ID', 'mwb-gf-integration-for-hubspot' ), esc_html( $this->crm_name ) );
								?>
							</th>
							<th><?php esc_html_e( 'Event', 'mwb-gf-integration-for-hubspot' ); ?></th>
							<th><?php esc_html_e( 'Timestamp', 'mwb-gf-integration-for-hubspot' ); ?></th>
							<th class=""><?php esc_html_e( 'Request', 'mwb-gf-integration-for-hubspot' ); ?></th>
							<th class=""><?php esc_html_e( 'Response', 'mwb-gf-integration-for-hubspot' ); ?></th>
						</tr>
					</thead>
				</table>
			</div>
			<?php else : ?>
				<div class="mwb-content-wrap">
					<?php esc_html_e( 'Please enable the logs from ', 'mwb-gf-integration-for-hubspot' ); ?>
					<a href="<?php echo esc_url( 'admin.php?page=mwb_' . $this->crm_slug . '_gf_page&tab=settings' ); ?>" target="_blank">
						<?php esc_html_e( 'Settings tab', 'mwb-gf-integration-for-hubspot' ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
