<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the feeds listing aspects of the plugin.
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

<div id="mwb-feeds" class="mwb-gf-integration__feedlist-wrap">

	<div class="mwb-gf_integration_logo-wrap">
		<div class="mwb-gf_integration_logo-crm">
			<img src="<?php echo esc_url( MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/crm.png' ); ?>" alt="<?php esc_html_e( 'HubSpot', 'mwb-gf-integration-for-hubspot' ); ?>">
		</div>
		<div class="mwb-gf_integration_logo-contact">
			<img src="<?php echo esc_url( MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/gravity-form.png' ); ?>" alt="<?php esc_html_e( 'GF', 'mwb-gf-integration-for-hubspot' ); ?>">
		</div>
		<div class="mwb-gf-integration__filterfeed">
			<Select class="filter-feeds-by-form" name="filter-feeds-by-form" >
				<option value="-1"><?php esc_html_e( 'Select GF form', 'mwb-gf-integration-for-hubspot' ); ?></option>
				<option value="all"><?php esc_html_e( 'All Feeds', 'mwb-gf-integration-for-hubspot' ); ?></option>
				<?php if ( ! empty( $params['wpgf'] ) && is_array( $params['wpgf'] ) ) : ?>
					<?php foreach ( $params['wpgf'] as $gf_post => $val ) : ?>
						<option value="<?php echo esc_attr( $val['id'] ); ?>"><?php echo esc_html( $val['title'] ); ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</Select>
		</div>
	</div>

	<ul class="mwb-gf-integration__feed-list">
		<?php
		$feed_module = $params['feed_class']::get_instance();
		$api_module  = $params['api_class']::get_instance();

		foreach ( $params['feeds'] as $key => $feed ) :


			$feed_title     = $feed->post_title;
			$_status        = get_post_status( $feed->ID );
			$active         = ( 'publish' === $feed->post_status ) ? 'yes' : 'no';
			$edit_link      = get_edit_post_link( $feed->ID );
			$gf_from_id     = $feed_module->fetch_feed_data( $feed->ID, 'mwb-' . $this->crm_slug . '-gf-form', '-' );
			$crm_object     = $api_module->get_objects_name( $feed_module->fetch_feed_data( $feed->ID, 'mwb-' . $this->crm_slug . '-gf-object', '-' ) );
			$primary_field  = $feed_module->fetch_feed_data( $feed->ID, 'mwb-' . $this->crm_slug . '-gf-primary-field', '-' );
			$filter_applied = $feed_module->fetch_feed_data( $feed->ID, 'mwb-' . $this->crm_slug . '-gf-enable-filters', '-' );

			$gf_form = GFAPI::get_form( $gf_from_id );

			?>
			<li class="mwb-gf-integration__feed-row">
				<div class="mwb-gf-integration__left-col">
					<h3 class="mwb-about__list-item-heading">
						<?php echo esc_html( $feed_title ); ?>
					</h3>
					<div class="mwb-feed-status__wrap">
						<p class="mwb-feed-status-text_<?php echo esc_attr( $feed->ID ); ?>" ><strong><?php echo 'publish' == $_status ? esc_html__( 'Active', 'mwb-gf-integration-for-hubspot' ) : esc_html__( 'Sandbox', 'mwb-gf-integration-for-hubspot' ); // phpcs:ignore ?></strong></p>
						<p><input type="checkbox" class="mwb-feed-status" value="publish" <?php checked( 'publish', $_status ); ?> feed-id=<?php echo esc_attr( $feed->ID ); ?> ></p>
					</div>
					<p>
						<span class="mwb-about__list-item-sub-heading"><?php esc_html_e( 'Form : ', 'mwb-gf-integration-for-hubspot' ); ?></span>
						<span><?php echo esc_html( $gf_form['title'] ); ?></span>   
					</p>
					<p>
						<span class="mwb-about__list-item-sub-heading"><?php esc_html_e( 'Object : ', 'mwb-gf-integration-for-hubspot' ); ?></span>
						<span><?php echo esc_html( $crm_object ); ?></span> 
					</p>
					<p> 
						<span class="mwb-about__list-item-sub-heading"><?php esc_html_e( 'Primary Key : ', 'mwb-gf-integration-for-hubspot' ); ?></span>
						<span><?php echo esc_html( $primary_field ); ?></span> 
					</p>
					<p>
						<span class="mwb-about__list-item-sub-heading"><?php esc_html_e( 'Conditions : ', 'mwb-gf-integration-for-hubspot' ); ?></span>
						<span><?php echo 'yes' != $filter_applied ? esc_html__( '-', 'mwb-gf-integration-for-hubspot' ) : esc_html__( 'Applied', 'mwb-gf-integration-for-hubspot' ); // phpcs:ignore ?></span> 
					</p>
				</div>
				<div class="mwb-gf-integration__right-col">
					<a href="<?php echo esc_url( $edit_link ); ?>">
						<img src="<?php echo esc_url( MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/edit.svg' ); ?>" alt="<?php esc_html_e( 'Edit feed', 'mwb-gf-integration-for-hubspot' ); ?>">
					</a>
					<div class="mwb-gf-integration__right-col1">
						<a href="javascript:void(0)" class="mwb_gf_integration_trash_feed" feed-id="<?php echo esc_html( $feed->ID ); ?>">
							<img src="<?php echo esc_url( MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/trash.svg' ); ?>" alt="<?php esc_html_e( 'Trash feed', 'mwb-gf-integration-for-hubspot' ); ?>">
						</a>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="mwb-about__list-item mwb-about__list-add">            
		<div class="mwb-about__list-item-btn">
			<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=mwb_' . $this->crm_slug . '_gf' ) ); ?>" class="mwb-btn mwb-btn--filled">
				<?php esc_html_e( 'Add Feeds', 'mwb-gf-integration-for-hubspot' ); ?>
			</a>
		</div>
	</div>

	<?php do_action( 'mwb_' . $this->crm_slug . '_gf_display_dependent_feeds' ); ?>
</div>
