<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://makewebbetter.com
 * @since             1.0.0
 * @package           Mwb_Gf_Integration_For_Hubspot
 *
 * @wordpress-plugin
 * Plugin Name:       MWB GF Integration for Hubspot
 * Plugin URI:        https://wordpress.org/plugins/mwb-gf-integration-for-hubspot
 * Description:       Automate lead generation and data collection by syncing Gravity Forms data over HubSpot with this MWB GF Integration for HubSpot plugin.
 * Version:           1.0.0
 * Author:            MakeWebBetter
 * Author URI:        https://makewebbetter.com/?utm_source=MWB-GFhubspotfree-backend&utm_medium=MWB-Site-backend&utm_campaign=MWB-backend
 *
 * Requires at least: 4.0
 * Tested up to:      5.8.1
 *
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       mwb-gf-integration-for-hubspot
 * Domain Path:       /languages
 */

use function Crontrol\Event\add;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$crm_slug = 'hubspot';

if ( ! mwb_hs_gf_is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
	add_action( 'admin_init', 'mwb_hs_gf_plugin_deactivate' );
	return;
}

register_activation_hook( __FILE__, 'activate_mwb_gf_integration_for_hubspot' );
register_deactivation_hook( __FILE__, 'deactivate_mwb_gf_integration_for_hubspot' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mwb-gf-integration-for-hubspot.php';

mwb_hs_gf_define_constants();

run_mwb_gf_integration_for_hubspot();


/**
 * Deactivate the plugin
 *
 * @return void
 */
function mwb_hs_gf_plugin_deactivate() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
	add_action( 'admin_notices', 'mwb_hs_gf_error_notice' );
}
/**
 * Integration plugin dependent on Gravity forms notice
 *
 * @return void
 */
function mwb_hs_gf_error_notice() {

	$dependent = esc_html( 'Gravity Forms' );
	$plugin    = esc_html( 'MWB GF Integration for HubSpot' );
	?>

	<div class="error notice is-dismissible">
		<p>
			<?php
			printf(
				/* translators: %1$s: Dependent plugin, %2$s: The plugin. */
				esc_html__( ' %1$s is not activated, Please activate %1$s first to activate %2$s', 'mwb-gf-integration-for-hubspot' ),
				'<strong>' . esc_html( $dependent ) . '</strong>',
				'<strong>' . esc_html( $plugin ) . '</strong>'
			);
			?>
		</p>
	</div>
	<?php

	// To hide Plugin activated notice.
	unset( $_GET['activate'] ); // phpcs:ignore
}

/**
 * Check if a particular plugin is active or not.
 *
 * @param string $slug Slug of the plugin to check if active or not.
 * @return boolean
 */
function mwb_hs_gf_is_plugin_active( $slug = '' ) {

	if ( empty( $slug ) ) {
		return;
	}
	$active_plugins = get_option( 'active_plugins', array() );
	if ( is_multisite() ) {
		$active_plugins = array_merge( $active_plugins, get_option( 'active_sitewide_plugins', array() ) );
	}

	return in_array( $slug, $active_plugins, true ) || array_key_exists( $slug, $active_plugins );

}

/**
 * Define Plugin Constants.
 *
 * @return void
 */
function mwb_hs_gf_define_constants() {

	/**
	 * Currently plugin version.
	 * Start at version 1.0.0 and use SemVer - https://semver.org
	 * Rename this for your plugin and update it as you release new versions.
	 */
	define( 'MWB_GF_INTEGRATION_FOR_HUBSPOT_VERSION', '1.0.0' );

	define( 'MWB_GF_INTEGRATION_FOR_HUBSPOT_DIRPATH', plugin_dir_path( __FILE__ ) );
	define( 'MWB_GF_INTEGRATION_FOR_HUBSPOT_URL', plugin_dir_url( __FILE__ ) );
	define( 'MWB_GF_INTEGRATION_FOR_HUBSPOT_PLUGIN_NAME', 'MWB GF Integration for HubSpot' );

}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mwb-gf-integration-for-hubspot-activator.php
 */
function activate_mwb_gf_integration_for_hubspot() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mwb-gf-integration-for-hubspot-activator.php';
	Mwb_Gf_Integration_For_Hubspot_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mwb-gf-integration-for-hubspot-deactivator.php
 */
function deactivate_mwb_gf_integration_for_hubspot() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mwb-gf-integration-for-hubspot-deactivator.php';
	Mwb_Gf_Integration_For_Hubspot_Deactivator::deactivate();
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mwb_gf_integration_for_hubspot() {

	$plugin = new Mwb_Gf_Integration_For_Hubspot();
	$plugin->run();
}

add_filter( 'plugin_row_meta', 'mwb_hs_gf_important_links', 10, 2 );

/**
 * Add custom links.
 *
 * @param   string $links   Link to index file of plugin.
 * @param   string $file    Index file of plugin.
 * @since   1.0.0
 * @return  array
 */
function mwb_hs_gf_important_links( $links, $file ) {
	if ( strpos( $file, 'mwb-gf-integration-for-hubspot.php' ) !== false ) {

		$row_meta = array(
			'demo'    => '<a href="' . esc_url( 'https://demo.makewebbetter.com/get-personal-demo/mwb-gf-integration-for-hubspot/?utm_source=MWB-GFhubspotfree-backend&utm_medium=MWB-Site-backend&utm_campaign=MWB-backend' ) . '" target="_blank"><img src="' . MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/Demo.svg" style="width: 15px;padding-right: 5px;"></i>' . esc_html__( 'Demo', 'mwb-gf-integration-for-hubspot' ) . '</a>',
			'doc'     => '<a href="' . esc_url( 'https://docs.makewebbetter.com/mwb-gf-integration-for-hubspot/?utm_source=MWB-GFhubspotfree-backend&utm_medium=MWB-Site-backend&utm_campaign=MWB-backend' ) . '" target="_blank"><img src="' . MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/Documentation.svg" style="width: 15px;padding-right: 5px;"></i>' . esc_html__( 'Documentation', 'mwb-gf-integration-for-hubspot' ) . '</a>',
			'support' => '<a href="' . esc_url( 'https://support.makewebbetter.com/wordpress-plugins-knowledge-base/category/mwb-gf-integration-for-hubspot/?utm_source=MWB-GFhubspotfree-backend&utm_medium=MWB-Site-backend&utm_campaign=MWB-backend' ) . '" target="_blank"><img src="' . MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/Support.svg" style="width: 15px;padding-right: 5px;"></i>' . esc_html__( 'Support', 'mwb-gf-integration-for-hubspot' ) . '</a>',
		);

		return array_merge( $links, $row_meta );
	}

	return (array) $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'hubspot_gf_settings_link' );

/**
 * Add settings link callback.
 *
 * @since 1.0.0
 * @param string $links link to the admin area of the plugin.
 */
function hubspot_gf_settings_link( $links ) {

	global $crm_slug;

	$connected = get_option( 'mwb-gf-' . $crm_slug . '-crm-connected' );

	if ( $connected ) {
		$href = admin_url( 'admin.php?page=mwb_' . $crm_slug . '_gf_page&tab=settings' );
	} else {
		$href = admin_url( 'admin.php?page=mwb_' . $crm_slug . '_gf_page' );
	}

	$plugin_links = array(
		'<a href="' . $href . '">' . esc_html__( 'Settings', 'mwb-gf-integration-for-hubspot' ) . '</a>',
	);
	return array_merge( $plugin_links, $links );
}
