<?php
/**
 * The complete management for the HubSpot-GF plugin through out the site.
 *
 * @since      1.0.0
 * @package    Mwb_Gf_Integration_For_Hubspot
 * @subpackage Mwb_Gf_Integration_For_Hubspot/includes
 * @author     MakeWebBetter <https://makewebbetter.com>
 */

/**
 * The complete management for the ajax handlers.
 *
 * @since      1.0.0
 * @package    Mwb_Gf_Integration_For_Hubspot
 * @subpackage Mwb_Gf_Integration_For_Hubspot/includes
 * @author     MakeWebBetter <https://makewebbetter.com>
 */
class Mwb_Gf_Integration_For_Hubspot_Ajax_Handler {

	/**
	 * Current crm slug.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $crm_slug    The current crm slug.
	 */
	public $crm_slug;

	/**
	 * Current crm name.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @var       string   $crm_name    The current crm name.
	 */
	public $crm_name;

	/**
	 * Instance of the Mwb_Gf_Integration_Hubspot_Api_Base class.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      object   $crm_api_module   Instance of Mwb_Gf_Integration_Hubspot_Api_Base class.
	 */
	public $crm_api_module;

	/**
	 * Current CRM API class.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $crm_class   Name of the current CRM API class.
	 */
	public $crm_class;

	/**
	 * Connect manager class name
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $connect   Name of the Connect manager class.
	 */
	private $connect;

	/**
	 * Instance of Connect manager class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object    $connect_manager  Instance of the Connect manager class.
	 */
	private $connect_manager;

	/**
	 * Instance of the plugin main class.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      object   $core_class    Name of the plugin core class.
	 */
	public $core_class = 'Mwb_Gf_Integration_For_Hubspot';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// Initialise CRM name and slug.
		$this->crm_slug = $this->core_class::mwb_get_current_crm_property( 'slug' );
		$this->crm_name = $this->core_class::mwb_get_current_crm_property( 'name' );

		// Initialise CRM API class.
		$this->crm_class      = 'Mwb_Gf_Integration_' . $this->crm_name . '_Api';
		$this->crm_api_module = $this->crm_class::get_instance();

		// Initialise Connect manager class.
		$this->connect         = 'Mwb_Gf_Integration_Connect_' . $this->crm_name . '_Framework';
		$this->connect_manager = $this->connect::get_instance();

	}

	/**
	 * Ajax handler :: Handles all ajax callbacks.
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	public function mwb_gf_integration_ajax_callback() {

		/* Nonce verification */
		check_ajax_referer( 'mwb_gf_' . $this->crm_slug . '_nonce', 'nonce' );

		$event    = ! empty( $_POST['event'] ) ? sanitize_text_field( wp_unslash( $_POST['event'] ) ) : '';
		$response = $this->mwb_gf_integration_get_default_response();

		if ( ! empty( $event ) ) {
			$data = $this->$event( $_POST );
			if ( $data ) { // phpcs:ignore
				$response['status']  = true;
				$response['message'] = esc_html__( 'Success', 'mwb-gf-integration-for-hubspot' );

				$response = $this->maybe_add_data( $response, $data );
			}
		}

		wp_send_json( $response );

	}

	/**
	 * Get default response.
	 *
	 * @since     1.0.0
	 * @return    array
	 */
	public function mwb_gf_integration_get_default_response() {
		return array(
			'status'  => false,
			'message' => esc_html__( 'Something went wrong!!', 'mwb-gf-integration-for-hubspot' ),
		);
	}

	/**
	 * Merge additional data to response.
	 *
	 * @param     array $response   An array of response.
	 * @param     array $data       An array of data to merge in response.
	 * @since     1.0.0
	 * @return    array
	 */
	public function maybe_add_data( $response, $data ) {

		if ( is_array( $data ) ) {
			$response['data'] = $data;
		}

		return $response;
	}

	/**
	 * Get Content for app setup guide.
	 *
	 * @param  array $posted_data Array of posted data.
	 * @return array Response data.
	 */
	public function get_app_setup_guide_content( $posted_data = array() ) {

		$response = array(
			'success' => false,
			'message' => __( 'Something went wrong!! Please try reloading this page.', 'mwb-gf-integration-for-hubspot' ),
		);

		$custom_app = isset( $posted_data['custom'] ) ? $posted_data['custom'] : 'no';

		$params = array(
			'callback_url'    => admin_url(),
			'setup_guide_url' => 'https://docs.makewebbetter.com/integration-with-hubspot-setup-guide/',
			'allowed_html'    => array(
				'a'      => array(
					'href'   => array(),
					'title'  => array(),
					'target' => array(),
				),
				'strong' => array(),
			),
			'custom_app'      => $custom_app,
			'api_key_image'   => MWB_GF_INTEGRATION_FOR_HUBSPOT_URL . 'admin/images/api.png',
		);

		// Get template for setup guide.
		ob_start();
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/setup-guide.php';
		$output = ob_get_contents();
		ob_end_clean();

		if ( ! empty( $output ) ) {
			$response = array(
				'success' => true,
				'content' => $output,
				'title'   => __( 'How to get the API keys?', 'mwb-gf-integration-for-hubspot' ),
			);
		}
		return $response;
	}

	/**
	 * Check current if account has api enabled.
	 *
	 * @return array Response data.
	 */
	public function validate_api_detail() {

		$response = array(
			'success' => false,
			'code'    => 400,
			'message' => __( 'Something went wrong!! Please try again', 'mwb-gf-integration-for-hubspot' ),
		);

		$info     = false;
		$response = $this->crm_api_module->perform_auth_trial();
		$info     = array(
			'success'      => true,
			'msg'          => esc_html__( 'Validation successful !! Redirecting...', 'mwb-gf-integration-for-hubspot' ),
			'redirect_url' => admin_url( 'admin.php?page=mwb_' . $this->crm_slug . '_gf_page' ),
		);

		if ( isset( $response['code'] ) && 401 == $response['code'] ) { // phpcs:ignore
			if ( isset( $response['data'] ) ) {
				$info['success'] = false;
				$info['msg']     = ! empty( $response['data']['Message'] ) ? $response['data']['Message'] : 'API credentials are incorrect';
				$info['class']   = 'error';
				return $info;
			}
		}

		update_option( 'mwb-gf-' . $this->crm_slug . '-crm-active', true );
		return $info;
	}

	/**
	 * Save plugin general settings
	 *
	 * @return array Response array.
	 */
	public function mark_onboarding_complete() {
		update_option( 'mwb-gf-' . $this->crm_slug . '-authorised', '1' );
		return array( 'success' => true );
	}

	/**
	 * Referesh access tokens.
	 *
	 * @since     1.0.0
	 * @return    array
	 */
	public function refresh_crm_access_token() {

		$response        = array( 'success' => false );
		$response['msg'] = esc_html__( 'Something went wrong! Check your credentials and authorize again', 'mwb-gf-integration-for-hubspot' );
		$renew_result    = $this->crm_api_module->renew_access_token();

		if ( ! empty( $renew_result ) && true == $renew_result ) { // phpcs:ignore
			$expiry_time  = $this->crm_api_module->get_access_token_expiry();
			$access_token = $this->crm_api_module->get_access_token();
			if ( $expiry_time > time() ) {
				/* translators: %s: time */
				$duration      = ceil( ( $expiry_time - time() ) / 60 );
				$token_message = sprintf(
					/* translators: %14s: hours %24s: minutes */
					esc_html__( 'Access token will expire in %1$s hours %2$s minutes.', 'mwb-gf-integration-for-hubspot' ),
					floor( $duration / 60 ),
					( $duration % 60 )
				);
			} else {
				$token_message = esc_html__( 'Access token has expired.', 'mwb-gf-integration-for-hubspot' );
			}

			$response = array(
				'success'       => true,
				'msg'           => __( 'Success', 'mwb-gf-integration-for-hubspot' ),
				'token_message' => $token_message,
				'access_token'  => $access_token,
			);
		}
		return $response;
	}

	/**
	 * Revoke account access.
	 *
	 * @since     1.0.0
	 * @return    bool
	 */
	public function disconnect_account() {

		$options = array(
			'mwb-gf-' . $this->crm_slug . '-crm-connected',
			'mwb-gf-' . $this->crm_slug . '-owner-account',
			'mwb-gf-' . $this->crm_slug . '-authorised',
			'mwb-gf-' . $this->crm_slug . '-user-info',
		);

		if ( ! empty( $options ) && is_array( $options ) ) {

			foreach ( $options as $key => $option ) {
				if ( ! empty( $option ) ) {
					delete_option( $option );
				}
			}
		}

		return true;
	}

	/**
	 * Save plugin settings.
	 *
	 * @param    array $posted_data   Ajax post data.
	 * @since    1.0.0
	 * @return   array
	 */
	public function save_general_setting( $posted_data ) {

		$data = ! empty( $posted_data['data'] ) ? $posted_data['data'] : '';

		$form_data = array();
		parse_str( $data, $form_data );
		$form_data = ! empty( $form_data ) ? map_deep( wp_unslash( $form_data ), 'sanitize_text_field' ) : array();

		$result       = array();
		$setting_data = array();

		if ( empty( $form_data ) || ! is_array( $form_data ) ) {

			$result = array(
				'status'  => false,
				'message' => esc_html__( 'No data found', 'mwb-gf-integration-for-hubspot' ),
			);

		} else {
			if ( ! empty( $form_data['mwb_setting'] ) ) {
				foreach ( $form_data['mwb_setting'] as $data_key => $data_value ) {

					if ( 'email_notif' == $data_key ) { // phpcs:ignore

						if ( '' != $data_value && ! self::validate_email( $data_value ) ) { // phpcs:ignore

							return array(
								'status'  => false,
								'message' => esc_html__( 'Inavlid email', 'mwb-gf-integration-for-hubspot' ),
							);

						}
					}

					$setting_data[ $data_key ] = $data_value;
				}
			}

			update_option( 'mwb-' . $this->crm_slug . '-gf-setting', $setting_data );

			$result = array(
				'status'  => true,
				'message' => esc_html__( 'Settings saved successfully', 'mwb-gf-integration-for-hubspot' ),
			);
		}

		return $result;
	}

	/**
	 * Email validation.
	 *
	 * @param      string $email E-mail to validate.
	 * @since      1.0.0
	 * @return     bool
	 */
	public static function validate_email( $email = false ) {

		if ( function_exists( 'filter_var' ) ) {

			if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				return true;
			}
		} elseif ( function_exists( 'is_email' ) ) {

			if ( is_email( $email ) ) {
				return true;
			}
		} else {

			if ( preg_match( '/@.+\./', $email ) ) {
				return true;
			}
		}

		return false;

	}

	/**
	 * Get fields for a particular crm object
	 *
	 * @param   array $posted_data    Ajax request data.
	 * @return  array                 Array for fields.
	 * @since   1.0.0
	 */
	public function get_object_fields_for_mapping( $posted_data = array() ) {

		$response    = array( 'success' => false );
		$fields_data = array();
		$form_id     = ! empty( $posted_data['selected_form'] ) ? sanitize_text_field( wp_unslash( $posted_data['selected_form'] ) ) : '';
		$object      = ! empty( $posted_data['selected_object'] ) ? sanitize_text_field( wp_unslash( $posted_data['selected_object'] ) ) : '';
		$force       = ! empty( $posted_data['force'] ) ? sanitize_text_field( wp_unslash( $posted_data['force'] ) ) : false;
		$feed_id     = ! empty( $posted_data['post_id'] ) ? sanitize_text_field( wp_unslash( $posted_data['post_id'] ) ) : false;
		$fields_data = $this->crm_api_module->get_object_fields( $object, $force );

		$fields_data = $this->maybe_restrict_fields( $fields_data );

		$feed_data['crm_fields']      = $fields_data;
		$feed_data['selected_object'] = $object;
		$feed_data['selected_form']   = $form_id;

		$options = $this->get_field_mapping_options( $form_id );

		$feed_data['field_options'] = $options;

		return array(
			'html'   => $this->retrieved_html( $feed_id, $feed_data ),
			'fields' => $fields_data,
		);

	}

	/**
	 * Restrict fields from mapping.
	 *
	 * @param   array $fields  An array of fields data.
	 * @since   1.0.0
	 * @return  array
	 */
	public function maybe_restrict_fields( $fields = array() ) {
		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return;
		}

		$admin = 'Mwb_Gf_Integration_For_' . $this->crm_name . '_Admin';

		$restrict_fields = array(
			'mobilephone',
			'phone',
			'fax',
		);

		$result = $fields;

		if ( ! $admin::is_pro_available_and_active() ) {
			foreach ( $fields as $key => $field ) {

				if ( array_key_exists( $key, array_flip( $restrict_fields ) ) ) {
					unset( $fields[ $key ] );
				}

				if ( isset( $field['custom'] ) && true == $field['custom'] ) { // phpcs:ignore
					unset( $fields[ $key ] );
				}
			}
			$result = $fields;
		}

		return $result;

	}

	/**
	 * Get all mapping options for a crm field.
	 *
	 * @param    int $form_id    GF Form ID.
	 * @return   array           Array for field option.
	 * @since    1.0.0
	 */
	public function get_field_mapping_options( $form_id ) {
		$framework_class    = 'Mwb_Gf_Integration_Connect_' . $this->crm_name . '_Framework';
		$framework_instance = $framework_class::get_instance();
		$options            = $framework_instance->getMappingDataset( $form_id );
		return $options;
	}

	/**
	 * Ajax Callback :: Get module HTML.
	 *
	 * @param     int   $feed_id       Feed id.
	 * @param     array $posted_data   Posted data.
	 * @return    string               Response html.
	 * @since     1.0.0
	 */
	public function retrieved_html( $feed_id, $posted_data ) {

		$feed_class      = 'Mwb_Gf_Integration_' . $this->crm_name . '_Feed_Module';
		$feed_module     = $feed_class::get_instance();
		$selected_object = $posted_data['selected_object'];
		$primary_field   = $feed_module->fetch_feed_data( $feed_id, 'mwb-' . $this->crm_slug . '-gf-primary-field', '', $selected_object );
		$mapping_data    = $feed_module->fetch_feed_data( $feed_id, 'mwb-' . $this->crm_slug . '-gf-mapping-data', '', $selected_object );

		$params = array(
			'selected_object' => $selected_object,
			'crm_fields'      => $posted_data['crm_fields'],
			'field_options'   => $posted_data['field_options'],
			'mapping_data'    => $mapping_data,
			'primary_field'   => $primary_field,
		);

		$templates = array(
			'select-fields',
			'add-new-field',
			'nonce-field',
		);

		// Primary field only for contact, company & ticket object.
		if ( in_array( $selected_object,array( 'Contact', 'Company', 'Ticket' ) ) ) { // phpcs:ignore
			$params['primary_field'] = $primary_field;
			$templates[]             = 'primary-field';
		}

		$html = '';
		foreach ( $templates as $k => $v ) {
			$html .= $feed_module->do_ajax_render( $v, $params );
		}
		return $html;
	}

	/**
	 * Get CRM Objects.
	 *
	 * @param    array $posted_data    Array of ajax posted data.
	 * @since    1.0.0
	 * @return   array $module_data    data of specific module.
	 */
	public function get_crm_objects( $posted_data = array() ) {

		$objects  = array();
		$force    = ! empty( $posted_data['force'] ) ? sanitize_text_field( wp_unslash( $posted_data['force'] ) ) : false;
		$response = array(
			'success' => false,
			'data'    => esc_html__( 'Something went wrong, Refresh and try again.', 'mwb-gf-integration-for-hubspot' ),
		);

		$objects = $this->crm_api_module->get_crm_objects( $force );
		if ( ! empty( $objects ) ) {
			$response = array(
				'success' => true,
				'data'    => $objects,
			);
		}
		return $response;
	}

	/**
	 * Add new field in feed form.
	 *
	 * @param    array $posted_data   Posted data.
	 * @since    1.0.0
	 * @return   array                Response data.
	 */
	public function add_new_field( $posted_data ) {

		$response = array(
			'success' => false,
			'msg'     => esc_html__( 'Something went wrong, Refresh and try again.', 'mwb-gf-integration-for-hubspot' ),
		);

		$object      = ! empty( $posted_data['object'] ) ? sanitize_text_field( wp_unslash( $posted_data['object'] ) ) : '';
		$field       = ! empty( $posted_data['field'] ) ? sanitize_text_field( wp_unslash( $posted_data['field'] ) ) : '';
		$form_id     = ! empty( $posted_data['form'] ) ? sanitize_text_field( wp_unslash( $posted_data['form'] ) ) : '';
		$fields_data = $this->crm_api_module->get_object_fields( $object, false );

		if ( empty( $fields_data[ $field ] ) ) {
			return $response;
		}

		if ( empty( $fields_data[ $field ]['name'] ) ) {
			$fields_data[ $field ]['name'] = $field;
		}

		$field_options    = $this->get_field_mapping_options( $form_id );
		$template_manager = 'Mwb_Gf_Integration_' . $this->crm_name . '_Template_Manager';

		ob_start();
		$template_manager::get_field_section_html( $field_options, $fields_data[ $field ], array() );
		$output = ob_get_contents();
		ob_end_clean();
		$response = array(
			'success' => true,
			'html'    => $output,
		);
		return $response;
	}

	/**
	 * Create filter field in feed form.
	 *
	 * @param    array $posted_data   Posted data.
	 * @since    1.0.0
	 * @return   array                Response data.
	 */
	public function create_feed_filters( $posted_data ) {

		$response = array(
			'success' => false,
			'msg'     => esc_html__( 'Something went wrong, Refresh and try again.', 'mwb-gf-integration-for-hubspot' ),
		);

		$feed_id = ! empty( $posted_data['post_id'] ) ? sanitize_text_field( wp_unslash( $posted_data['post_id'] ) ) : false;
		$form_id = ! empty( $posted_data['selected_form'] ) ? sanitize_text_field( wp_unslash( $posted_data['selected_form'] ) ) : '';

		$form_fields   = $this->get_field_mapping_options( $form_id );
		$filter_fields = $this->get_field_filter_options();

		return array(
			'form'    => $form_fields,
			'filter'  => $filter_fields,
			'success' => true,
		);
	}

	/**
	 * Get all mapping options for a filter field.
	 *
	 * @return   array           Array for field option.
	 * @since    1.0.0
	 */
	public function get_field_filter_options() {
		$framework_class    = 'Mwb_Gf_Integration_Connect_' . $this->crm_name . '_Framework';
		$framework_instance = $framework_class::get_instance();
		$options            = $framework_instance->getFilterMappingDataset();
		return $options;
	}

	/**
	 * Toggle feed status.
	 *
	 * @param     array $data    An array of ajax posted data.
	 * @since     1.0.0
	 * @return    bool
	 */
	public function toggle_feed_status( $data = array() ) {

		$feed_id  = ! empty( $data['feed_id'] ) ? sanitize_text_field( wp_unslash( $data['feed_id'] ) ) : '';
		$status   = ! empty( $data['status'] ) ? sanitize_text_field( wp_unslash( $data['status'] ) ) : '';
		$response = $this->connect_manager->change_post_status( $feed_id, $status );
		return $response;
	}

	/**
	 * Trash feeds.
	 *
	 * @param     array $data    An array of ajax posted data.
	 * @since     1.0.0
	 * @return    bool
	 */
	public function trash_feeds_from_list( $data = array() ) {

		$feed_id = ! empty( $data['feed_id'] ) ? sanitize_text_field( wp_unslash( $data['feed_id'] ) ) : '';
		$trash   = wp_trash_post( $feed_id );

		if ( $trash ) {
			return true;
		}
		return false;
	}

	/**
	 * Clear sync log.
	 *
	 * @since      1.0.0
	 * @return     array          Response array.
	 */
	public function clear_sync_log() {
		$this->connect_manager->delete_sync_log();
		return array( 'success' => true );
	}

	/**
	 * Download logs.
	 *
	 * @param      array $data   An arraay of ajax posted data.
	 * @since      1.0.0
	 * @return     array         Response array.
	 */
	public function download_sync_log( $data = array() ) {

		global $wpdb;
		$response = array(
			'success' => false,
			'msg'     => esc_html__( 'Something went wrong, Refresh and try again.', 'mwb-gf-integration-for-hubspot' ),
		);

		$table_name     = $wpdb->prefix . 'mwb_' . $this->crm_slug . '_gf_log';
		$log_data_query = "SELECT * FROM {$table_name} ORDER BY `id` DESC"; // phpcs:ignore
		$log_data       = $wpdb->get_results( $log_data_query, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$path           = $this->connect_manager->create_log_folder( 'mwb-' . $this->crm_slug . '-gf-logs' );
		$log_dir        = $path . '/mwb-' . $this->crm_slug . '-gf-sync-log.log';

		if ( file_exists( $log_dir ) ) {
			unlink( $log_dir );
		}

		if ( ! empty( $log_data ) && is_array( $log_data ) ) {
			foreach ( $log_data as $key => $value ) {

				$value['hubspot_id'] = ! empty( $value['hubspot_id'] ) ? $value['hubspot_id'] : '-';

				$log  = 'FEED ID: ' . $value['feed_id'] . PHP_EOL;
				$log .= 'FEED : ' . $value['feed'] . PHP_EOL;
				$log .= 'HUBSPOT ID : ' . $value['hubspot_id'] . PHP_EOL;
				$log .= 'HUBSPOT OBJECT : ' . $value['hubspot_object'] . PHP_EOL;
				$log .= 'TIME : ' . gmdate( 'd-m-Y h:i A', esc_html( $value['time'] ) ) . PHP_EOL;
				$log .= 'REQUEST : ' . wp_json_encode( maybe_unserialize( $value['request'] ) ) . PHP_EOL;
				$log .= 'RESPONSE : ' . wp_json_encode( maybe_unserialize( $value['response'] ) ) . PHP_EOL;
				$log .= '------------------------------------' . PHP_EOL;
				file_put_contents( $log_dir, $log, FILE_APPEND ); // phpcs:ignore
			}

			$response = array(
				'success'  => true,
				'redirect' => admin_url( '?mwb_download=1' ),
			);
		} else {
			$response = array(
				'success' => false,
				'msg'     => esc_html__( 'No log data available', 'mwb-gf-integration-for-hubspot' ),
			);
		}

		return $response;
	}

	/**
	 * Enable datatable.
	 *
	 * @param     mixed $data    An array of ajax posted data.
	 * @since     1.0.0
	 * @return    void
	 */
	public function get_datatable_data_cb( $data = array() ) {

		$request = $_GET; // phpcs:ignore
		$offset  = $request['start'];
		$limit   = $request['length'];

		global $wpdb;
		$table_name     = $wpdb->prefix . 'mwb_' . $this->crm_slug . '_gf_log';
		$log_data_query = $wpdb->prepare( "SELECT * FROM {$table_name} ORDER BY `id` DESC LIMIT %d OFFSET %d ", $limit, $offset ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$log_data       = $wpdb->get_results( $log_data_query, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$count_query    = "SELECT COUNT(*) as `total_count` FROM {$table_name}"; // phpcs:ignore
		$count_data     = $wpdb->get_col( $count_query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$total_count    = $count_data[0];
		$data           = array();

		foreach ( $log_data as $key => $value ) {

			$data_href = $this->connect_manager->get_crm_link( $value['hubspot_id'], $value['feed_id'] );

			if ( ! empty( $data_href ) && '-' != $data_href ) { // phpcs:ignore
				$link = '<a href="' . $data_href . '" target="_blank">' . $value['hubspot_id'] . '</a>';
			} else {
				$link = $value['hubspot_id'];
			}

			$temp = array(
				'<span class="dashicons dashicons-plus-alt"></span>',
				$value['feed'],
				$value['feed_id'],
				$this->crm_api_module->get_objects_name( $value['hubspot_object'] ),
				$link,
				$value['event'],
				gmdate( 'd-m-Y h:i A', esc_html( $value['time'] ) ),
				wp_json_encode( maybe_unserialize( $value['request'] ) ),
				wp_json_encode( maybe_unserialize( $value['response'] ) ),
			);
			$data[] = $temp;
		}

		$json_data = array(
			'draw'            => intval( $request['draw'] ),
			'recordsTotal'    => $total_count,
			'recordsFiltered' => $total_count,
			'data'            => $data,
		);

		wp_send_json( $json_data );
	}

	/**
	 * Filter feeds by form
	 *
	 * @param      array $data     An array of ajax posted data.
	 * @since      1.0.0
	 * @return     mixed
	 */
	public function filter_feeds_by_form( $data ) {

		$form_id = isset( $data['form_id'] ) ? sanitize_text_field( wp_unslash( $data['form_id'] ) ) : '';
		$result  = array(
			'status' => false,
			'msg'    => esc_html__( 'Invalid form', 'mwb-gf-integration-for-hubspot' ),
		);

		$template_class   = 'Mwb_Gf_Integration_' . Mwb_Gf_Integration_For_Hubspot::mwb_get_current_crm_property( 'name' ) . '_Template_Manager';
		$template_manager = $template_class::get_instance();

		if ( ! empty( $form_id ) ) {

			if ( 'all' == $form_id ) { // phpcs:ignore
				$feeds = $this->connect_manager->get_available_crm_feeds();
			} else {
				$feeds = $this->connect_manager->get_feeds_by_form( $form_id );
			}

			$output = '';

			foreach ( $feeds as $feed ) {
				ob_start();
				$template_manager->get_filter_section_html( $feed );
				$output .= ob_get_contents();
				ob_end_clean();
			}

			$result = array(
				'status' => true,
				'feeds'  => $output,
			);
		}

		return $result;
	}
}
