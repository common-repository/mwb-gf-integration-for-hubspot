<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the header of feeds section.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Mwb_Gf_Integration_For_Hubspot
 * @subpackage Mwb_Gf_Integration_For_Hubspot/mwb-crm-fw/templates/meta-boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="mwb_gf_integration__feeds-wrap">
	<div class="mwb-gf_integration_logo-wrap">
		<div class="mwb-sf_gf__logo-zoho">
			<img src="<?php echo esc_url( MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/crm.png' ); ?>" alt="<?php esc_html_e( 'Hubspot', 'mwb-gf-integration-for-hubspot' ); ?>">
		</div>
		<div class="mwb-gf_integration_logo-contact">
			<img src="<?php echo esc_url( MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/gravity-form.png' ); ?>" alt="<?php esc_html_e( 'GF', 'mwb-gf-integration-for-hubspot' ); ?>">
		</div>
	</div>

