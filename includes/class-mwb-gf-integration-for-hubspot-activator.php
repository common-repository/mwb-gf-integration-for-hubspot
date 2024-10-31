<?php
/**
 * Fired during plugin activation
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Mwb_Gf_Integration_For_Hubspot
 * @subpackage Mwb_Gf_Integration_For_Hubspot/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Mwb_Gf_Integration_For_Hubspot
 * @subpackage Mwb_Gf_Integration_For_Hubspot/includes
 * @author     MakeWebBetter <https://makewebbetter.com>
 */
class Mwb_Gf_Integration_For_Hubspot_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// Create database table for logs.
		self::mwb_gf_integration_create_log_table();
		// Schedule event to clear log.
		self::mwb_gf_integration_scheduled_event();
	}

	/**
	 * Create logs table in DB on plugin activation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function mwb_gf_integration_create_log_table() {

		global $wpdb;
		$crm_slug   = Mwb_Gf_Integration_For_Hubspot::mwb_get_current_crm_property( 'slug' );
		$table_name = $wpdb->prefix . 'mwb_' . $crm_slug . '_gf_log';

		$query  = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
		$result = $wpdb->get_var( $query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( empty( $result ) || $result != $table_name ) { // phpcs:ignore

			try {
				global $wpdb;
				$charset_collate = $wpdb->get_charset_collate();

				$sql = "CREATE TABLE IF NOT EXISTS $table_name (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`feed` varchar(255) NOT NULL,
					`feed_id` int(11) NOT NULL,
					`hubspot_object` varchar(255) NOT NULL,
					`hubspot_id` varchar(255) NOT NULL,
					`event` varchar(255) NOT NULL,
					`request` text NOT NULL,
					`response` text NOT NULL,
					`time` int(11) NOT NULL,
					PRIMARY KEY (`id`)
					) $charset_collate;";

				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );

			} catch ( \Throwable $th ) {
				wp_die( esc_html( $th->getMessage() ) );
			}
		}
	}

	/**
	 * Schedule clear log event.
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	public static function mwb_gf_integration_scheduled_event() {
		$crm_slug = Mwb_Gf_Integration_For_Hubspot::mwb_get_current_crm_property( 'slug' );

		if ( ! wp_next_scheduled( 'mwb_' . $crm_slug . '_gf_clear_log' ) ) {
			wp_schedule_event( time(), 'daily', 'mwb_' . $crm_slug . '_gf_clear_log' );
		}
	}
}
