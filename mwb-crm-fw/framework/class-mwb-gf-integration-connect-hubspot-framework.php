<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Mwb_Gf_Integration_For_Hubspot
 * @subpackage Mwb_Gf_Integration_For_Hubspot/mwb-crm-fw/framework
 */

if ( ! class_exists( 'Mwb_Gf_Integration_Hubspot_Framework' ) ) {
	wp_die( 'Mwb_Gf_Integration_Hubspot_Framework does not exists.' );
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mwb_Gf_Integration_For_Hubspot
 * @subpackage Mwb_Gf_Integration_For_Hubspot/mwb-crm-fw/framework
 * @author     makewebbetter <webmaster@makewebbetter.com>
 */
class Mwb_Gf_Integration_Connect_Hubspot_Framework extends Mwb_Gf_Integration_Hubspot_Framework {

	/**
	 *  The instance of this class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $instance    The instance of this class.
	 */
	private static $instance;

	/**
	 * Main Mwb_Gf_Integration_Connect_Hubspot_Framework Instance.
	 *
	 * Ensures only one instance of Mwb_Gf_Integration_Connect_Hubspot_Framework is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Mwb_Gf_Integration_Connect_Hubspot_Framework - Main instance.
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get current mapping scenerio for current CRM connection.
	 *
	 * @param    mixed $form_id   GF Form ID.
	 * @since    1.0.0
	 * @return   array - Current CRM to GF mapping.
	 */
	public function getMappingDataset( $form_id = '' ) {

		if ( empty( $form_id ) ) {
			return;
		}

		$obj_type = array(
			'wpgf',
		);

		$formatted_dataset = array();
		foreach ( $obj_type as $key => $obj ) {
			$formatted_dataset[ $obj ] = $this->getMappingOptions( $form_id );
		}

		$formatted_dataset = $this->parse_labels( $formatted_dataset );
		return $formatted_dataset;
	}

	/**
	 * Get current mapping scenerio for current CRM connection.
	 *
	 * @param    string $id    GF form ID.
	 * @since    1.0.0
	 * @return   array         Current CRM to GF mapping.
	 */
	public function getMappingOptions( $id = false ) {
		return $this->get_gf_meta( $id );
	}

	/**
	 * Get available filter options.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public function getFilterMappingDataset() {
		return $this->get_avialable_form_filters();
	}

	/**
	 * Create log folder.
	 *
	 * @param     string $path    Name of log folder.
	 * @since     1.0.0
	 * @return    mixed
	 */
	public function create_log_folder( $path ) {

		$basepath = WP_CONTENT_DIR . '/uploads/';
		$fullpath = $basepath . $path;

		if ( ! empty( $fullpath ) ) {

			if ( ! is_dir( $fullpath ) ) {

				$folder = mkdir( $fullpath, 0755, true );

				if ( $folder ) {
					return $fullpath;
				}
			} else {
				return $fullpath;
			}
		}
		return false;
	}

	/**
	 * Get link to data sent over crm.
	 *
	 * @param      string $crm_id   An array of data synced over crm.
	 * @param      string $feed_id  Feed ID.
	 * @since      1.0.0
	 * @return     string
	 */
	public function get_crm_link( $crm_id = false, $feed_id ) {

		if ( false == $crm_id || empty( $feed_id ) ) { // phpcs:ignore
			return;
		}
		$link = '';

		$crm_class      = 'Mwb_Gf_Integration_' . $this->crm_name . '_Api';
		$crm_api_module = $crm_class::get_instance();
		$portal_id      = $crm_api_module->get_portal_Id();
		$module         = get_post_meta( $feed_id, 'mwb-' . $this->crm_slug . '-gf-object', true );

		if ( ! empty( $module ) && ! empty( $portal_id ) ) {

			switch ( $module ) {
				case 'Contact':
					$link = 'https://app.hubspot.com/contacts/' . $portal_id . '/contact/' . $crm_id . '/';
					break;

				case 'Ticket':
					$link = 'https://app.hubspot.com/contacts/' . $portal_id . '/ticket/' . $crm_id . '/';
					break;

				case 'Company':
					$link = 'https://app.hubspot.com/sales/' . $portal_id . '/company/' . $crm_id . '/';
					break;
			}
		}

		return $link;

	}

	/**
	 * Returns count of synced data.
	 *
	 * @since     1.0.0
	 * @return    integer
	 */
	public function get_synced_forms_count() {

		global $wpdb;
		$table_name  = $wpdb->prefix . 'mwb_' . $this->crm_slug . '_gf_log';
		$col_name    = $this->crm_slug . '_id';
		$count_query = "SELECT COUNT(*) as `total_count` FROM {$table_name} WHERE {$col_name} != '-'"; // phpcs:ignore
		$count_data  = $wpdb->get_col( $count_query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$total_count = isset( $count_data[0] ) ? $count_data[0] : '0';

		return $total_count;
	}

	/**
	 * Clear n days log.
	 *
	 * @param  int $timestamp  Delete timestamp.
	 * @since  1.0.0
	 * @return void
	 */
	public function clear_n_days_log( $timestamp ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'mwb_' . $this->crm_slug . '_gf_log';
		$query      = $wpdb->prepare( "DELETE FROM {$table_name} WHERE `time` < %d", $timestamp ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	// End of class.
}
