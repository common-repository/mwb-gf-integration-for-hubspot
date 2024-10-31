<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com
 * @since      1.0.0
 *
 * @package    Mwb_Gf_Integration_For_Hubspot
 * @subpackage Mwb_Gf_Integration_For_Hubspot/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mwb_Gf_Integration_For_Hubspot
 * @subpackage Mwb_Gf_Integration_For_Hubspot/admin
 * @author     MakeWebBetter <https://makewebbetter.com>
 */
class Mwb_Gf_Integration_For_Hubspot_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The Slug of the CRM.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $crm_slug    The Slug of the CRM.
	 */
	private $crm_slug;

	/**
	 * The Name of the CRM.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $crm_name    The Name of the CRM.
	 */
	private $crm_name;

	/**
	 * Instance of the plugin main class.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string   $core_class    Name of the plugin core class.
	 */
	public $core_class = 'Mwb_Gf_Integration_For_Hubspot';

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name       The name of this plugin.
	 * @param    string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// Initialize CRM name and slug.
		$this->crm_slug = $this->core_class::mwb_get_current_crm_property( 'slug' );
		$this->crm_name = $this->core_class::mwb_get_current_crm_property( 'name' );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mwb_Gf_Integration_For_Hubspot_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mwb_Gf_Integration_For_Hubspot_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mwb-gf-integration-for-hubspot-admin.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name . '-select2', plugin_dir_url( dirname( __FILE__ ) ) . 'packages/select2/select2.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-tooltip', plugin_dir_url( dirname( __FILE__ ) ) . 'packages/jq-tiptip/tooltip.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-datatable-css', plugin_dir_url( dirname( __FILE__ ) ) . 'packages/datatables/media/css/jquery.dataTables.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-animate', plugin_dir_url( dirname( __FILE__ ) ) . 'packages/animate/animate.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mwb_Gf_Integration_For_Hubspot_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mwb_Gf_Integration_For_Hubspot_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mwb-gf-integration-for-hubspot-admin.js', array( 'jquery' ), $this->version, false );

		if ( $this->is_valid_screen() ) {

			wp_enqueue_script( $this->plugin_name . '-select2', plugin_dir_url( dirname( __FILE__ ) ) . 'packages/select2/select2.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name . '-swal2', plugin_dir_url( dirname( __FILE__ ) ) . 'packages/sweet-alert2/sweet-alert2.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name . '-tooltip', plugin_dir_url( dirname( __FILE__ ) ) . 'packages/jq-tiptip/jquery.tipTip.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name . '-datatable-js', plugin_dir_url( dirname( __FILE__ ) ) . 'packages/datatables/media/js/jquery.dataTables.min.js', array(), $this->version, false );
			wp_enqueue_script( $this->plugin_name . '-datatable-responsive-js', plugin_dir_url( dirname( __FILE__ ) ) . 'packages/datatables.net-responsive/js/dataTables.responsive.min.js', array(), $this->version, false );

			$ajax_data = array(
				'crm'           => $this->crm_slug,
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'ajaxNonce'     => wp_create_nonce( 'mwb_gf_' . $this->crm_slug . '_nonce' ),
				'ajaxAction'    => 'mwb_gf_' . $this->crm_slug . '_ajax_request',
				'feedBackLink'  => admin_url( 'admin.php?page=mwb_' . $this->crm_slug . '_gf_page&tab=feeds' ),
				'feedBackText'  => esc_html__( 'Back to feeds', 'mwb-gf-integration-for-hubspot' ),
				'isPage'        => isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '', // phpcs:ignore
				'criticalError' => esc_html__( 'Internal server error', 'mwb-gf-integration-for-hubspot' ),
				'trashIcon'     => MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/trash.svg',
				'api_key_image' => MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/api.png',
			);

			wp_localize_script( $this->plugin_name, 'mwb_gf_integration_ajax_data', $ajax_data );
		}
	}

	/**
	 * Check for the screens provided by the plugin.
	 *
	 * @since     1.0.0
	 * @return    bool
	 */
	public function is_valid_screen() {

		$result = false;

		$valid_screens = array(
			'mwb_' . $this->crm_slug . '_gf_page',
			'mwb_' . $this->crm_slug . '_gf',
			'plugins',
		);

		$screen = get_current_screen();

		if ( ! empty( $screen->id ) ) {

			$page = trim( $screen->id );

			foreach ( $valid_screens as $screen ) {
				if ( false !== strpos( $page, $screen ) ) { // phpcs:ignore
					$result = true;
				}
			}
		}

		return $result;
	}

	/**
	 * Add Hubspot submenu to Contact menu.
	 *
	 * @param array $menu_items Submenu Items.
	 * @return array
	 */
	public function mwb_hs_gf_submenu( $menu_items ) {

		$menu_items[] = array(
			'name'       => 'mwb_' . $this->crm_slug . '_gf_page',
			'label'      => 'Hubspot',
			'callback'   => array( $this, 'mwb_hs_gf_submenu_cb' ),
			'permission' => 'edit_posts',
		);
		return $menu_items;

	}

	/**
	 * Hubspot sub-menu callback function.
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	public function mwb_hs_gf_submenu_cb() {
		require_once MWB_GF_INTEGRATION_FOR_HUBSPOT_DIRPATH . 'admin/partials/mwb-gf-integration-for-hubspot-admin-display.php';
	}

	/**
	 * Get plugin name and version.
	 *
	 * @since    1.0.0
	 * @return   array
	 */
	public function add_plugin_headings() {

		$headings = array(
			'name'    => esc_html__( 'MWB GF Integration for HubSpot', 'mwb-gf-integration-for-hubspot' ),
			'version' => MWB_GF_INTEGRATION_FOR_HUBSPOT_VERSION,
		);

		return apply_filters( 'mwb_' . $this->crm_slug . '_gf_plugin_headings', $headings );
	}

	/**
	 * Function to run at admin intitialization.
	 *
	 * @since     1.0.0
	 * @return    bool
	 */
	public function mwb_gf_integration_admin_init_process() {

		if ( 'hubspot' != $this->crm_slug ) { // phpcs:ignore
			return;
		}

		if ( ! empty( $_GET['mwb-gf-integration-perform-auth'] ) ) {
			if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) ) ) {

				$method  = ! empty( $_GET['method'] ) ? sanitize_text_field( wp_unslash( $_GET['method'] ) ) : false;
				$own_app = 'true' == $method ? 'yes' : 'no'; // phpcs:ignore

				if ( 'yes' == $own_app ) { // phpcs:ignore
					$client_id = ! empty( $_GET['client_id'] ) ? sanitize_text_field( wp_unslash( $_GET['client_id'] ) ) : false;
					$secret_id = ! empty( $_GET['secret_id'] ) ? sanitize_text_field( wp_unslash( $_GET['secret_id'] ) ) : false;
					$scopes    = ! empty( $_GET['scopes'] ) ? sanitize_text_field( wp_unslash( $_GET['scopes'] ) ) : false;

					if ( ! $client_id || ! $secret_id || ! $scopes ) {
						return false;
					}

					update_option( 'mwb-gf-' . $this->crm_slug . '-client-id', $client_id );
					update_option( 'mwb-gf-' . $this->crm_slug . '-secret-id', $secret_id );
					update_option( 'mwb-gf-' . $this->crm_slug . '-scopes', $scopes );
				}
				update_option( 'mwb-gf-' . $this->crm_slug . '-own-app', $own_app );

				$crm_class      = 'Mwb_Gf_Integration_' . $this->crm_name . '_Api';
				$crm_api_module = $crm_class::get_instance();
				$auth_url       = $crm_api_module->get_auth_code_url();

				wp_redirect( $auth_url ); // phpcs:ignore

			}
		} elseif ( ! empty( $_GET['code'] ) ) {

			$crm_class = 'Mwb_Gf_Integration_' . $this->crm_name . '_Api';

			if ( ! isset( $_GET['state'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['state'] ) ), 'mwb_' . $this->crm_slug . '_gf_state' ) ) {
				wp_die( 'The state is not correct from HubSpot Server. Try again.' );
			}

			$auth_code      = ! empty( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : false;
			$crm_api_module = $crm_class::get_instance();
			$crm_api_module->save_access_token( $auth_code );
			$crm_api_module->get_userinfo();
			wp_redirect( admin_url( 'admin.php?page=mwb_' . $this->crm_slug . '_gf_page&mwb_auth=1' ) ); // phpcs:ignore
			exit;

		} elseif ( ! empty( $_GET['mwb-gf-perform-refresh'] ) ) { // Perform refresh token.

			$crm_class      = 'Mwb_Gf_Integration_' . $this->crm_name . '_Api';
			$crm_api_module = $crm_class::get_instance();
			$crm_api_module->renew_access_token();
			wp_redirect( admin_url( 'admin.php?page=mwb_' . $this->crm_slug . '_gf_page' ) ); // phpcs:ignore
			exit;

		} elseif ( ! empty( $_GET['mwb-gf-integration-perform-reauth'] ) ) { // Perform reauthorization.
			if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) ) ) {
				$crm_class      = 'Mwb_Gf_Integration_' . $this->crm_name . '_Api';
				$crm_api_module = $crm_class::get_instance();
				$auth_url       = $crm_api_module->get_auth_code_url();
				if ( ! $auth_url ) {
					return;
				}
				wp_redirect( $auth_url ); // phpcs:ignore
				exit;
			}
		}

		/* Download log file */
		if ( ! empty( $_GET['mwb_download'] ) ) { // Perform log file download.
			$filename = WP_CONTENT_DIR . '/uploads/mwb-' . $this->crm_slug . '-gf-logs/mwb-' . $this->crm_slug . '-gf-sync-log.log';
			header( 'Content-type: text/plain' );
			header( 'Content-Disposition: attachment; filename="' . basename( $filename ) . '"' );
			readfile( $filename ); // phpcs:ignore
			exit;
		}

	}

	/**
	 * Tooltip icon and tooltip data.
	 *
	 * @param     string $tip Tip to display.
	 * @since     1.0.0
	 * @return    void
	 */
	public static function mwb_gf_integration_tooltip( $tip ) {
		$crm_slug = Mwb_Gf_Integration_For_Hubspot::mwb_get_current_crm_property( 'slug' );
		?>
			<i class="mwb_<?php echo esc_attr( $crm_slug ); ?>_gf_tips" data-tip="<?php echo esc_html( $tip ); ?>"><span class="dashicons dashicons-editor-help"></span></i> 
		<?php

	}

	/**
	 * Returns if pro plugin is active or not.
	 *
	 * @since      1.0.0
	 * @return     bool
	 */
	public static function pro_dependency_check() {

		// Check if pro plugin exists.
		if ( mwb_hs_gf_is_plugin_active( 'gf-integration-for-hubspot/gf-integration-for-hubspot.php' ) ) {

			if ( class_exists( 'Gf_Integration_For_Hubspot_Admin' ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get all valid screens to add scripts and templates.
	 *
	 * @param     array $valid_screens An array of plugin scrrens.
	 * @since     1.0.0
	 * @return    array
	 */
	public function mwb_gf_integration_add_frontend_screens( $valid_screens = array() ) {

		if ( is_array( $valid_screens ) ) {

			// Push your screen here.
			array_push( $valid_screens, 'forms_page_mwb_' . $this->crm_slug . '_gf_page' );
		}

		return $valid_screens;
	}


	/**
	 * Get all valid slugs to add deactivate popup.
	 *
	 * @param     array $valid_screens An array of plugin scrrens.
	 * @since     1.0.0
	 * @return    array
	 */
	public function mwb_gf_integration_add_deactivation_screens( $valid_screens = array() ) {

		if ( is_array( $valid_screens ) ) {

			// Push your screen here.
			array_push( $valid_screens, 'mwb-gf-integration-for-' . $this->crm_slug );
		}

		return $valid_screens;
	}

	/**
	 * Check if pro plugin active and trail not expired.
	 *
	 * @since    1.0.0
	 * @return   bool
	 */
	public static function is_pro_available_and_active() {
		$result   = true;
		$crm_name = Mwb_Gf_Integration_For_Hubspot::mwb_get_current_crm_property( 'name' );
		$pro_main = 'Gf_Integration_For_' . $crm_name;
		if ( self::pro_dependency_check() ) {

			$license    = $pro_main::$mwb_gf_pro_license_cb;
			$ini_days   = $pro_main::$mwb_gf_pro_ini_license_cb;
			$days_count = $pro_main::$ini_days();

			if ( ! $pro_main::$license() && 0 > $days_count ) {
				$result = false;
			}
		} elseif ( false === self::pro_dependency_check() ) {
			$result = false;
		}
		return $result;
	}


}
