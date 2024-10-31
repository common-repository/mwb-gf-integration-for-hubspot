<?php
/**
 * Base Api Class
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Mwb_Gf_Integration_For_Hubspot
 * @subpackage Mwb_Gf_Integration_For_Hubspot/mwb-crm-fw/api
 */

/**
 * Base Api Class.
 *
 * This class defines all code necessary api communication.
 *
 * @since      1.0.0
 * @package    Mwb_Gf_Integration_For_Hubspot
 * @subpackage Mwb_Gf_Integration_For_Hubspot/mwb-crm-fw/api
 */
class Mwb_Gf_Integration_Hubspot_Api extends Mwb_Gf_Integration_Hubspot_Api_Base {

	/**
	 * Crm prefix
	 *
	 * @var    string   Crm prefix
	 * @since  1.0.0
	 */
	public static $crm_prefix;

	/**
	 * HUbspot Client ID
	 *
	 * @var     string  Client ID
	 * @since   1.0.0
	 */
	public static $client_id;

	/**
	 * HUbspot Secret Key
	 *
	 * @var     string Secret key
	 * @since   1.0.0
	 */
	public static $client_secret;

	/**
	 * Hubspot redirect uri.
	 *
	 * @since    1.0.0
	 * @var      string Redirect uri.
	 */
	public static $redirect_uri;

	/**
	 * Hubspot scopes.
	 *
	 * @since    1.0.0
	 * @var      string Scopes
	 */
	public static $scopes;

	/**
	 * Hubspot own app.
	 *
	 * @since    1.0.0
	 * @var      string Own app
	 */
	public static $own_app;

	/**
	 * Hubspot Access token data.
	 *
	 * @var     string   Stores access token data.
	 * @since   1.0.0
	 */
	public static $access_token;

	/**
	 * Hubspot Refresh token data
	 *
	 * @var     string   Stores refresh token data.
	 * @since   1.0.0
	 */
	public static $refresh_token;

	/**
	 * Hubspot Portal ID
	 *
	 * @var     string  Portal ID.
	 * @since   1.0.0
	 */
	public static $portal_id;

	/**
	 * Hubspot timezone.
	 *
	 * @var     string  Timezone.
	 * @since   1.0.0
	 */
	public static $timezone;

	/**
	 * Access token expiry data
	 *
	 * @var     integer   Stores access token expiry data.
	 * @since   1.0.0
	 */
	public static $expiry;

	/**
	 * Current instance URL
	 *
	 * @var     string    Current instance url.
	 * @since   1.0.0
	 */
	public static $instance_url;

	/**
	 * Issued at data
	 *
	 * @var     string     Issued at data by hubspot
	 * @since   1.0.0
	 */
	public static $issued_at;

	/**
	 * Creates an instance of the class
	 *
	 * @var     object     An instance of the class
	 * @since   1.0.0
	 */
	protected static $_instance = null; // phpcs:ignore

	/**
	 * Instance of the class.
	 *
	 * @var     object  $instance  Instance of the class.
	 * @since   1.0.0
	 */
	protected static $instance = null;

	/**
	 * Main Mwb_Gf_Integration_Hubspot_Api Instance.
	 *
	 * Ensures only one instance of Mwb_Gf_Integration_Hubspot_Api is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @return Mwb_Gf_Integration_Hubspot_Api - Main instance.
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		self::initialize();
		return self::$instance;
	}

	/**
	 * Initialize properties.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $token_data Saved token data.
	 */
	private static function initialize( $token_data = array() ) {

		self::$crm_prefix = Mwb_Gf_Integration_For_Hubspot::mwb_get_current_crm_property( 'slug' );

		if ( empty( $token_data ) ) {
			$token_data = get_option( 'mwb-gf-' . self::$crm_prefix . '-token-data', array() );
		}

		self::$own_app = get_option( 'mwb-gf-' . self::$crm_prefix . '-own-app', false );

		if ( false != self::$own_app && 'yes' == self::$own_app ) { // phpcs:ignore

			self::$client_id     = get_option( 'mwb-gf-' . self::$crm_prefix . '-client-id', '' );
			self::$client_secret = get_option( 'mwb-gf-' . self::$crm_prefix . '-secret-id', '' );
			self::$scopes        = str_replace( ',', '', get_option( 'mwb-gf-' . self::$crm_prefix . '-scopes', '' ) );
			self::$redirect_uri  = rtrim( admin_url(), '/' );
		} else {
			self::$client_id     = '769fa3e6-79b1-412d-b69c-6b8242b2c62a';
			self::$client_secret = '2893dd41-017e-4208-962b-12f7495d16b0';
			self::$scopes        = 'oauth contacts';
		}
		self::$redirect_uri = rtrim( admin_url(), '/' );

		self::$access_token  = isset( $token_data['access_token'] ) ? $token_data['access_token'] : '';
		self::$refresh_token = isset( $token_data['refresh_token'] ) ? $token_data['refresh_token'] : '';
		self::$expiry        = isset( $token_data['expires_in'] ) ? $token_data['expires_in'] : '';

		$accountinfo = get_option( 'mwb-gf-' . self::$crm_prefix . '-user-info', array() );

		self::$portal_id = isset( $accountinfo['portalId'] ) ? $accountinfo['portalId'] : '';
		self::$timezone  = isset( $accountinfo['timezone'] ) ? $accountinfo['timezone'] : '';
	}

	/**
	 * Get access token.
	 *
	 * @since    1.0.0
	 * @return   string   Access token.
	 */
	public function get_access_token() {
		return ! empty( self::$access_token ) ? self::$access_token : false;
	}

	/**
	 * Get refresh token.
	 *
	 * @since     1.0.0
	 * @return    string    Refresh token.
	 */
	public function get_refresh_token() {
		return ! empty( self::$refresh_token ) ? self::$refresh_token : false;
	}

	/**
	 * Check if access token is valid.
	 *
	 * @since    1.0.0
	 * @return   boolean
	 */
	public function is_access_token_valid() {
		return ! empty( self::$expiry ) ? ( self::$expiry > time() ) : false;
	}

	/**
	 * Get token expiry.
	 *
	 * @since     1.0.0
	 * @return    string    Refresh token.
	 */
	public function get_access_token_expiry() {
		return ! empty( self::$expiry ) ? self::$expiry : false;
	}

	/**
	 * Get Portal ID.
	 *
	 * @since     1.0.0
	 * @return    string    Refresh token.
	 */
	public function get_portal_Id() {
		return ! empty( self::$portal_id ) ? self::$portal_id : false;
	}

	/**
	 * Get scopes.
	 *
	 * @since 1.0.0
	 * @return string.
	 */
	public function get_oauth_scopes() {
		return ! empty( self::$scopes ) ? urlencode( self::$scopes ) : false; //phpcs:ignore
	}

	/**
	 * Get portal timezone.
	 *
	 * @since     1.0.0
	 * @return    string    Refresh token.
	 */
	public function get_account_timezone() {
		return ! empty( self::$timezone ) ? self::$timezone : false;
	}

	/**
	 * Get refreshed token data from api.
	 *
	 * @since     1.0.0
	 * @return    boolean.
	 */
	public function renew_access_token() {

		$refresh_token = $this->get_refresh_token();

		if ( ! empty( $refresh_token ) ) {
			$response = $this->process_access_token( false, $refresh_token );
		}

		return ! empty( $response['code'] ) && 200 == $response['code'] ? true : false; // phpcs:ignore
	}

	/**
	 * Save New token data into db.
	 *
	 * @since   1.0.0
	 * @param   string $code    Unique code to generate token.
	 */
	public function save_access_token( $code ) {
		$this->process_access_token( $code );
	}

	/**
	 * Get Base Authorization url.
	 *
	 * @since    1.0.0
	 * @return   string   Base Authorization url.
	 */
	public function base_auth_url() {
		$url = 'https://api.hubapi.com/'; // phpcs:ignore
		return $url;
	}

	/**
	 * Get Authorization url.
	 *
	 * @since    1.0.0
	 * @return   string Authorization url.
	 */
	public function get_auth_code_url() {

		$nonce = wp_create_nonce( 'mwb_' . self::$crm_prefix . '_gf_state' );

		$query_args = array(
			'response_type'  => 'code',
			'state'          => urlencode( $nonce ), // phpcs:ignore
			'client_id'      => urlencode( self::$client_id ), // phpcs:ignore
			'redirect_uri'   => urlencode( self::$redirect_uri ), // phpcs:ignore
			'scope'          => $this->get_oauth_scopes(), // phpcs:ignore
			'optional_scope' => urlencode( 'automation files timeline content forms integration-sync e-commerce crm.import' ), // phpcs:ignore
		);

		$login_url = add_query_arg( $query_args, 'https://app.hubspot.com/oauth/authorize' );

		return $login_url;
	}

	/**
	 * Get refresh token data from api.
	 *
	 * @since   1.0.0
	 * @param   string $code            Unique code to generate token.
	 * @param   string $refresh_token   Unique code to renew token.
	 * @return  array
	 */
	public function process_access_token( $code = '', $refresh_token = '' ) {

		$endpoint = 'oauth/v1/token';

		$this->base_url = $this->base_auth_url();

		$params = array(
			'grant_type'    => 'authorization_code',
			'client_id'     => self::$client_id,
			'client_secret' => self::$client_secret,
			'redirect_uri'  => self::$redirect_uri,
			'code'          => $code,
		);

		if ( empty( $code ) ) {
			$params['refresh_token'] = $refresh_token;
			$params['grant_type']    = 'refresh_token';
			unset( $params['code'] );
		}

		$response = $this->post( $endpoint, $params, $this->get_token_auth_header() );

		if ( isset( $response['code'] ) && 200 == $response['code'] ) { // phpcs:ignore

			// Save token.
			$token_data = ! empty( $response['data'] ) ? $response['data'] : array();
			$token_data = $this->merge_refresh_token( $token_data );

			$token_data['expires_in'] = time() + $token_data['expires_in'];
			update_option( 'mwb-gf-' . self::$crm_prefix . '-token-data', $token_data );
			update_option( 'mwb-gf-' . self::$crm_prefix . '-crm-connected', true );
			self::initialize( $token_data );
		} else {
			// On failure add to log.
			delete_option( 'mwb-gf-' . self::$crm_prefix . '-token-data' );
			delete_option( 'mwb-gf-' . self::$crm_prefix . '-crm-connected' );
		}

		return $response;
	}

	/**
	 * Get connected portal info.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_userinfo() {
		$endpoint       = 'integrations/v1/me';
		$this->base_url = $this->base_auth_url();
		$headers        = $this->get_auth_header();
		$response       = $this->get( $endpoint, '', $headers );
		$userinfo       = array();

		if ( isset( $response['code'] ) && 200 == $response['code'] ) { // phpcs:ignore
			if ( isset( $response['data'] ) ) {
				$userinfo['portalId'] = $response['data']['portalId'];
				$userinfo['timezone'] = $response['data']['timeZone'];
			}
		}
		update_option( 'mwb-gf-' . self::$crm_prefix . '-user-info', $userinfo );
		return $userinfo;
	}


	/**
	 * Merge refresh token with new access token data.
	 *
	 * @since   1.0.0
	 * @param   array $new_token_data   Latest token data.
	 * @return  array                   Token data.
	 */
	public function merge_refresh_token( $new_token_data ) {

		$old_token_data = get_option( 'mwb-gf-' . self::$crm_prefix . '-token-data', array() );

		if ( empty( $old_token_data ) ) {
			return $new_token_data;
		}

		foreach ( $old_token_data as $key => $value ) {
			if ( isset( $new_token_data[ $key ] ) ) {
				$old_token_data[ $key ] = $new_token_data[ $key ];
			}
		}
		return $old_token_data;
	}

	/**
	 * Get authorization headers for getting token.
	 *
	 * @since   1.0.0
	 * @return  array   Headers.
	 */
	public function get_token_auth_header() {
		return array();
	}

	/**
	 * Get Request headers.
	 *
	 * @since    1.0.0
	 * @return   array   Headers.
	 */
	public function get_auth_header() {

		$headers = array(
			'Authorization' => 'Bearer ' . $this->get_access_token(),
			'content-type'  => 'application/json',
		);

		return $headers;
	}

	/**
	 * Create single record on CRM
	 *
	 * @param  string  $object      CRM object name.
	 * @param  array   $record_data Request data.
	 * @param  boolean $is_bulk     Is a bulk request.
	 * @param  array   $log_data    Data to create log.
	 * @param  bool    $manual_sync If synced manually.
	 *
	 * @since 1.0.0
	 *
	 * @return array Response data.
	 */
	public function create_or_update_record( $object, $record_data, $is_bulk = false, $log_data = array(), $manual_sync = false ) {

		$response_data = array(
			'success' => false,
			'msg'     => __( 'Something went wrong', 'mwb-gf-integration-for-hubspot' ),
		);

		$record_id = false;
		$is_update = false;
		$feed_id   = ! empty( $log_data['feed_id'] ) ? $log_data['feed_id'] : false;

		if ( $manual_sync && ! empty( $log_data['method'] ) ) {
			$event = $log_data['method'];
		} else {
			$event = __FUNCTION__;
		}

		// Check for the existing record based on selected primary field.
		$primary_field = '';
		if ( $feed_id ) {
			$duplicate_check_fields = get_post_meta( $feed_id, 'mwb-' . self::$crm_prefix . '-gf-primary-field', true );
			$primary_field          = ! empty( $duplicate_check_fields ) ? $duplicate_check_fields : false;
		}

		if ( $primary_field ) {
			$search_response = $this->check_for_existing_record( $object, $record_data, $primary_field );
			if ( $this->if_access_token_expired( $search_response ) ) {
				$search_response = $this->check_for_existing_record( $object, $record_data, $primary_field );
			}

			// Get record id from search query result.
			$record_id = $this->may_be_get_record_id_from_search( $search_response, $record_data, $primary_field );
		}

		if ( ! $record_id ) {

			$is_update = 'create';
			$response  = $this->create_record( $object, $record_data, $is_bulk, $log_data );
			if ( $this->if_access_token_expired( $response ) ) {
				$response = $this->create_record( $object, $record_data, $is_bulk, $log_data );
			}
			if ( $this->is_success( $response ) ) {
				$response_data['success']  = true;
				$response_data['msg']      = 'Create_Record';
				$response_data['response'] = $response;
				$response_data['id']       = $this->get_object_id_from_response( $response );
			} else {
				$response_data['success']  = false;
				$response_data['msg']      = esc_html__( 'Error posting to CRM', 'mwb-gf-integration-for-hubspot' );
				$response_data['response'] = $response;
			}
		} else {

			// Update an existing record based on record_id.
			$is_update = true;
			$response  = $this->update_record( $record_id, $object, $record_data, $is_bulk, $log_data );
			if ( $this->if_access_token_expired( $response ) ) {
				$response = $this->update_record( $record_id, $object, $record_data, $is_bulk, $log_data );
			}
			if ( $this->is_success( $response ) ) {

				// Insert record id and message to response.
				if ( isset( $response['message'] ) ) {
					$response['message'] = 'Updated';
				}
				if ( empty( $response['data'] ) ) {
					$response['data'] = array(
						'id' => $record_id,
					);
				}

				$response_data['success']  = true;
				$response_data['msg']      = 'Update_Record';
				$response_data['response'] = $response;
				$response_data['id']       = $record_id;
			}
		}

		// Insert log in db.
		$this->log_request_in_db( $event, $object, $record_data, $response, $log_data, $is_update );

		return $response_data;
	}

	/**
	 * Insert log data in db.
	 *
	 * @param     string  $event                Trigger event/ Feed .
	 * @param     string  $crm_object           CRM object.
	 * @param     array   $request              An array of request data.
	 * @param     array   $response             An array of response data.
	 * @param     array   $log_data             Data to log.
	 * @param     boolean $is_update            Is update request.
	 * @return    void
	 */
	public function log_request_in_db( $event, $crm_object, $request, $response, $log_data, $is_update = false ) {

		$feed    = ! empty( $log_data['feed_name'] ) ? $log_data['feed_name'] : false;
		$feed_id = ! empty( $log_data['feed_id'] ) ? $log_data['feed_id'] : false;
		$event   = ! empty( $event ) ? $event : false;

		$hubspot_object = $crm_object;
		$hubspot_id     = $this->get_object_id_from_response( $response, $hubspot_object );

		if ( '-' == $hubspot_object ) { // phpcs:ignore
			if ( ! empty( $log_data['id'] ) ) {
				$hubspot_object = $log_data['id'];
			}
		}

		$request  = serialize( $request ); //phpcs:ignore
		$response = serialize( $response ); //phpcs:ignore

		switch ( $is_update ) {
			case true === $is_update:
				$operation = 'Update';
				break;

			case 'search':
				$operation = 'Search';
				break;

			case 'create':
			default:
				$operation = 'Create';
				break;
		}

		$log_data = array(
			'event'    => $event,
			'feed_id'  => $feed_id,
			'feed'     => $feed,
			'request'  => $request,
			'response' => $response,
			Mwb_Gf_Integration_For_Hubspot::mwb_get_current_crm_property( 'slug' ) . '_id' => $hubspot_id,
			Mwb_Gf_Integration_For_Hubspot::mwb_get_current_crm_property( 'slug' ) . '_object' => $hubspot_object . ' - ' . $operation,
			'time'     => time(),
		);

		// Structure them!
		$this->insert_log_data( $log_data );
	}

	/**
	 * Retrieve object ID from crm response.
	 *
	 * @param     array $response     An array of response data from crm.
	 * @since     1.0.0
	 * @return    integer
	 */
	public function get_object_id_from_response( $response ) {

		$id = '-';
		if ( isset( $response['data']['vid'] ) && isset( $response['data']['portal-id'] ) ) {
			$id = ! empty( $response['data']['vid'] ) ? $response['data']['vid'] : $id;

		} elseif ( isset( $response['data']['companyId'] ) && isset( $response['data']['portalId'] ) ) {
			$id = ! empty( $response['data']['companyId'] ) ? $response['data']['companyId'] : $id;

		} elseif ( isset( $response['data']['engagement'] ) && isset( $response['data']['engagement']['id'] ) ) {
			$id = ! empty( $response['data']['engagement']['id'] ) ? $response['data']['engagement']['id'] : $id;

		} elseif ( isset( $response['data']['objectId'] ) && isset( $response['data']['portalId'] ) ) {
			$id = ! empty( $response['data']['objectId'] ) ? $response['data']['objectId'] : $id;

		} elseif ( isset( $response['data'] ) && isset( $response['data']['id'] ) ) {
			$id = ! empty( $response['data']['id'] ) ? $response['data']['id'] : $id;
		}

		return $id;
	}

	/**
	 * Insert data to db.
	 *
	 * @param      array $data    Data to log.
	 * @since      1.0.0
	 * @return     void
	 */
	public function insert_log_data( $data ) {

		$connect         = 'Mwb_Gf_Integration_Connect_' . self::$crm_prefix . '_Framework';
		$connect_manager = $connect::get_instance();

		if ( 'yes' != $connect_manager->get_settings_details( 'logs' ) ) { // phpcs:ignore
			return;
		}

		global $wpdb;
		$table = $wpdb->prefix . 'mwb_' . self::$crm_prefix . '_gf_log';
		$wpdb->insert( $table, $data ); // phpcs:ignore
	}

	/**
	 * Check for exsiting record in search query response.
	 *
	 * @param array  $response      Search query response.
	 * @param array  $record_data   Request data of searched record.
	 * @param string $primary_field Primary field name.
	 *
	 * @return string|bool          Id of existing record or false.
	 */
	public function may_be_get_record_id_from_search( $response, $record_data, $primary_field ) {

		$record_id     = false;
		$found_records = array();

		if ( isset( $response['code'] ) && 200 == $response['code'] && 'OK' == $response['message'] ) { // phpcs:ignore
			if ( ! empty( $response['data'] ) && ! empty( $response['data']['results'] ) ) {
				$found_records = $response['data']['results'];
			}
		}

		if ( count( $found_records ) > 0 ) {
			foreach ( $found_records as $key => $record ) {
				if ( $record['properties'][ $primary_field ] === $record_data['responses'][ $primary_field ] ) {
					$record_id = $record['id'];
					break;
				}
			}
		}

		return $record_id;
	}

	/**
	 * Check for existing record using parameterizedSearch.
	 *
	 * @param string $object        Target object name.
	 * @param array  $record_data   Record data.
	 * @param string $primary_field Primary field.
	 *
	 * @return array                Response data array.
	 */
	public function check_for_existing_record( $object, $record_data, $primary_field ) {

		if ( ! empty( $record_data['responses'] ) ) {
			$entries = $record_data['responses'];

			$module = $this->get_crm_module_name( $object );

			$this->base_url = $this->base_auth_url();
			$headers        = $this->get_auth_header();
			$endpoint       = 'crm/v3/objects/' . $module . '/search';
			$params         = array(
				'filterGroups' => array(
					array(
						'filters' => array(
							array(
								'propertyName' => $primary_field,
								'value'        => $entries[ $primary_field ],
								'operator'     => 'EQ',
							),
						),
					),
				),
			);

			$params   = wp_json_encode( $params );
			$response = $this->post( $endpoint, $params, $headers );

			if ( $this->if_access_token_expired( $response ) ) {
				$headers  = $this->get_auth_header();
				$response = $this->post( $endpoint, $params, $headers );
			}

			return $response;

		}
	}


	/**
	 * Check if resposne has success code.
	 *
	 * @param  array $response  Response data.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean true|false.
	 */
	public function is_success( $response ) {
		if ( ! empty( $response['code'] ) ) {
			return in_array( $response['code'], array( 200, 201, 204, 202 ) ); // phpcs:ignore
		}
		return false;
	}

	/**
	 * Create a new record.
	 *
	 * @param  string  $object     Object name.
	 * @param  array   $record_data Record data.
	 * @param  boolean $is_bulk    Is a bulk request.
	 * @param  array   $log_data   Data to create log.
	 * @return array               Response data.
	 */
	public function create_record( $object, $record_data, $is_bulk, $log_data ) {

		$this->base_url = $this->base_auth_url();

		if ( isset( $record_data['is_form'] ) && true == $record_data['is_form'] ) { // phpcs:ignore
			$this->base_url = 'https://api.hsforms.com/submissions/v3/integration/';
			$endpoint       = 'submit/' . $this->get_portal_Id() . '/' . substr( $object, 10 );
		} else {
			$endpoint = $this->get_object_endpoints( $object );
		}

		unset( $record_data['is_form'] );
		unset( $record_data['responses'] );

		$params   = wp_json_encode( $record_data );
		$headers  = $this->get_auth_header();
		$response = $this->post( $endpoint, $params, $headers );

		return $response;
	}

	/**
	 * Update an existing record.
	 *
	 * @param  string  $record_id   Record id to be updated.
	 * @param  string  $object      Object name.
	 * @param  array   $record_data Record data.
	 * @param  boolean $is_bulk     Is a bulk request.
	 * @param  array   $log_data    Data to create log.
	 * @return array                Response data.
	 */
	public function update_record( $record_id, $object, $record_data, $is_bulk, $log_data ) {

		$this->base_url = $this->base_auth_url();
		$endpoint       = $this->get_object_endpoints( $object, $record_id );
		$params         = wp_json_encode( $record_data );
		$headers        = $this->get_auth_header();

		switch ( $object ) {
			case 'Contact':
				$response = $this->post( $endpoint, $params, $headers );
				break;

			case 'Company':
			case 'Ticket':
				$response = $this->put( $endpoint, $params, $headers );
				break;

		}

		return $response;
	}

	/**
	 * Check if response has expired access token message.
	 *
	 * @param  array $response Api response.
	 * @return bool            Access token status.
	 */
	public function if_access_token_expired( $response ) {
		if ( isset( $response['code'] ) && 401 == $response['code'] && 'Unauthorized' == $response['message'] ) { // phpcs:ignore
			return $this->renew_access_token();
		}
		return false;
	}

	/**
	 * Get object endpoints.
	 *
	 * @param string $object     CRM Object.
	 * @param string $record_id  Record id of object.
	 * @since 1.0.0
	 * @return string
	 */
	public function get_object_endpoints( $object, $record_id = false ) {

		if ( empty( $object ) ) {
			return;
		}

		$endpoint = '';

		if ( ! $record_id ) {

			switch ( $object ) {
				case 'Contact':
					$endpoint = 'contacts/v1/contact';
					break;

				case 'Company':
					$endpoint = 'companies/v2/companies';
					break;

				case 'Ticket':
					$endpoint = 'crm-objects/v1/objects/tickets';
					break;

				case 'Task':
					$endpoint = 'engagements/v1/engagements';
					break;
			}
		} else {

			switch ( $object ) {
				case 'Contact':
					$endpoint = 'contacts/v1/contact/vid/' . $record_id . '/profile';
					break;

				case 'Company':
					$endpoint = 'companies/v2/companies/' . $record_id;
					break;

				case 'Ticket':
					$endpoint = 'crm-objects/v1/objects/tickets/' . $record_id;
					break;
			}
		}

		return $endpoint;

	}

	/**
	 * Get available object in crm.
	 *
	 * @param  boolean $force Fetch from api.
	 * @return array          Response data.
	 */
	public function get_crm_objects( $force = false ) {

		$objects = array();
		$objects = get_transient( 'mwb_gf_' . self::$crm_prefix . '_objects_data' );
		if ( ! $force && false !== ( $objects ) ) {
			$objects = get_transient( 'mwb_gf_' . self::$crm_prefix . '_objects_data' );
			return $objects;
		}

		$objects = array(
			'Contact' => 'Contact',
			'Company' => 'Company',
			'Task'    => 'Task',
			'Ticket'  => 'Ticket',
		);

		$forms = $this->get_crm_forms();
		if ( ! empty( $forms ) && is_array( $forms ) ) {
			$objects = array_merge( $objects, $forms );
			set_transient( 'mwb_gf_' . self::$crm_prefix . '_objects_data', $objects );
		}

		return $objects;
	}

	/**
	 * Get checkout forms.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_crm_forms() {

		$endpoint       = 'forms/v2/forms';
		$this->base_url = $this->base_auth_url();
		$headers        = $this->get_auth_header();
		$response       = $this->get( $endpoint, '', $headers );
		$forms          = array();

		if ( $this->if_access_token_expired( $response ) ) {
			$headers  = $this->get_auth_header();
			$response = $this->get( $endpoint, '', $headers );
		}

		if ( isset( $response['code'] ) && 200 == $response['code'] ) { // phpcs:ignore
			if ( isset( $response['data'] ) && ! empty( $response['data'] ) ) {
				if ( is_array( $response['data'] ) ) {
					foreach ( $response['data'] as $key => $form ) {
						$forms[ 'mwb_gf_hs_' . $form['guid'] ] = $form['name'];
					}
				}
			}
		}
		return $forms;
	}

	/**
	 * Get fields assosiated with an object.
	 *
	 * @param  string  $object Name of object.
	 * @param  boolean $force  Fetch from api.
	 * @return array           Response data.
	 */
	public function get_object_fields( $object, $force = false ) {

		$fields = array();

		switch ( $object ) {

			case false !== strpos( $object, 'mwb_gf_hs_' ):
				$form_id = substr( $object, 10 );
				$fields  = $this->get_crm_from_fields( $form_id );
				break;

			case 'Task':
				$fields = $this->get_json_fields( 'task_fields' );
				break;

			default:
				$module = $this->get_crm_module_name( $object );
				$fields = $this->get_module_fields( $module );

		}

		return $fields;
	}

	/**
	 * Get module name as per crm
	 *
	 * @param string $module   Name of the module.
	 * @since 1.0.0
	 * @return array
	 */
	public function get_crm_module_name( $module = false ) {

		if ( false == $module ) { // phpcs:ignore
			return;
		}

		switch ( $module ) {
			case 'Contact':
				$module = 'contacts';
				break;

			case 'Company':
				$module = 'companies';
				break;

			case 'Ticket':
				$module = 'tickets';
				break;
		}

		return $module;
	}

	/**
	 * Get modul fields.
	 *
	 * @param string $object  CRM object.
	 * @since 1.0.0
	 * @return array
	 */
	public function get_module_fields( $object = false ) {

		if ( false == $object ) { // phpcs:ignore
			return;
		}

		$fields_data = array();

		$endpoint       = 'properties/v2/' . $object . '/properties';
		$this->base_url = $this->base_auth_url();
		$headers        = $this->get_auth_header();
		$response       = $this->get( $endpoint, '', $headers );

		if ( $this->if_access_token_expired( $response ) ) {
			$headers  = $this->get_auth_header();
			$response = $this->get( $endpoint, '', $headers );
		}

		$field_arr = array();
		if ( isset( $response['code'] ) && 200 == $response['code'] ) { // phpcs:ignore
			if ( isset( $response['data'] ) && ! empty( $response['data'] ) && is_array( $response['data'] ) ) {
				foreach ( $response['data'] as $key => $field ) {
					if ( isset( $field['readOnlyValue'] ) && false === $field['readOnlyValue'] ) {
						$required = '';

						if ( in_array( $field['name'], array( 'email', 'name', 'dealname' ) ) ) { // phpcs:ignore
							$required = true;
						}

						if ( 'tickets' == $object && in_array( $field['name'], array( 'subject', 'content' ) ) ) { // phpcs:ignore
							$required = true;
						}

						$type = ! empty( $field['fieldType'] ) ? $field['fieldType'] : '';

						if ( ! empty( $field['type'] ) && in_array( $field['fieldType'], array('datetime') ) ) { //phpcs:ignore
							$type = $field['type'];
						}

						$field_arr = array(
							'name'     => ! empty( $field['name'] ) ? $field['name'] : '',
							'label'    => ! empty( $field['label'] ) ? $field['label'] : '',
							'type'     => ! empty( $type ) ? $type : '',
							'required' => ! empty( $required ) ? $required : '',
						);

						if ( ! empty( $field['options'] ) ) {
							$options = array();
							$example = array();

							foreach ( $field['options'] as $option ) {
								$options[] = array(
									'label' => $option['label'],
									'value' => $option['value'],
								);

								$example[] = $option['value'] . '=' . $option['label'];
							}

							if ( ! empty( $options ) ) {
								$field_arr['options'] = $options;
								$field_arr['example'] = $example;
							}
						}

						if ( ! ( ! empty( $field['hubspotDefined'] ) && true == $field['hubspotDefined'] ) ) { // phpcs:ignore
							$field_arr['custom'] = true;
						} else {
							$field_arr['custom'] = false;
						}
						$fields_data[ $field['name'] ] = $field_arr;
					}
				}
			}
		}
		return $fields_data;
	}

	/**
	 * Get hs fields.
	 *
	 * @param string $name  Name of JSON file.
	 * @since 1.0.0
	 * @return array
	 */
	public function get_json_fields( $name = false ) {

		$hs_fields = array();

		global $wp_filesystem;  // Define global object of WordPress filesystem.
		require_once ABSPATH . 'wp-admin/includes/file.php'; // Since we are using the filesystem outside wp-admin.
		WP_Filesystem(); // Initialise new file system object.

		$json_url = MWB_GF_INTEGRATION_FOR_HUBSPOT_DIRPATH . 'mwb-crm-fw/framework/jsons/' . $name . '.json';
		$response = $wp_filesystem->get_contents( $json_url );

		if ( ! empty( $response ) ) {
			$hs_fields = json_decode( $response, true );
		}

		return $hs_fields;
	}

	/**
	 * Get form fields from crm.
	 *
	 * @param  string $form_id   Form ID.
	 * @since  1.0.0
	 * @return array
	 */
	public function get_crm_from_fields( $form_id = false ) {

		if ( false == $form_id ) { // phpcs:ignore
			return;
		}

		$form_fields    = array();
		$endpoint       = 'forms/v2/forms/' . $form_id;
		$this->base_url = $this->base_auth_url();
		$headers        = $this->get_auth_header();
		$response       = $this->get( $endpoint, '', $headers );

		if ( $this->if_access_token_expired( $response ) ) {
			$headers  = $this->get_auth_header();
			$response = $this->get( $endpoint, '', $headers );
		}

		if ( isset( $response['code'] ) && 200 == $response['code'] ) { // phpcs:ignore
			if ( isset( $response['data'] ) && ! empty( $response['data']['formFieldGroups'] ) ) {

				foreach ( $response['data']['formFieldGroups'] as $groups ) {
					if ( ! empty( $groups['fields'] ) ) {
						foreach ( $groups['fields'] as $group ) {
							$form_fields[] = $group;

							if ( ! empty( $group['dependentFieldFilters'] ) ) {
								foreach ( $group['dependentFieldFilters'] as $dependent ) {
									if ( ! empty( $dependent['dependentFormField'] ) ) {
										$form_fields[] = $dependent['dependentFormField'];
									}
								}
							}
						}
					}
				}
			}
		}

		$fields_data = array();
		$form_data   = array();
		if ( ! empty( $form_fields ) && is_array( $form_fields ) ) {
			foreach ( $form_fields as $field ) {

				$fields_data = array(
					'label'    => ! empty( $field['label'] ) ? $field['label'] : '',
					'name'     => ! empty( $field['name'] ) ? $field['name'] : '',
					'type'     => ! empty( $field['fieldType'] ) ? $field['fieldType'] : '',
					'required' => ! empty( $field['required'] ) && true == $field['required'] ? true : false, // phpcs:ignore
				);

				if ( ! empty( $field['options'] ) ) {
					$options = array();
					$example = array();

					foreach ( $field['options'] as $option ) {
						$options[] = array(
							'label' => $option['label'],
							'value' => $option['value'],
						);

						$example[] = $option['value'] . '=' . $option['label'];
					}

					if ( ! empty( $options ) ) {
						$fields_data['options'] = $options;
						$fields_data['example'] = $example;
					}
				}

				if ( ! in_array( $field['name'], $this->get_json_fields( 'hubspot_fields' ) ) ) { // phpcs:ignore
					$fields_data['custom'] = true;
				} else {
					$fields_data['custom'] = false;
				}
				$form_data[ $field['name'] ] = $fields_data;

			}
		}

		// add subscription data.
		$subsdata = $this->get_subscription_data();
		if ( ! empty( $subsdata ) && is_array( $subsdata ) ) {
			$form_data = array_merge( $form_data, $subsdata );
		}

		// add additional data.
		$additional = $this->get_json_fields( 'form_fields' );
		if ( ! empty( $additional ) && is_array( $additional ) ) {
			$form_data = array_merge( $form_data, $additional );
		}

		return $form_data;

	}

	/**
	 * Get hs subscription.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_subscription_data() {

		$endpoint       = 'email/public/v1/subscriptions';
		$this->base_url = $this->base_auth_url();
		$headers        = $this->get_auth_header();
		$response       = $this->get( $endpoint, '', $headers );
		$subs_data      = array();

		if ( $this->if_access_token_expired( $response ) ) {
			$headers  = $this->get_auth_header();
			$response = $this->get( $endpoint, '', $headers );
		}

		if ( isset( $response['code'] ) && 200 == $response['code'] ) { // phpcs:ignore
			if ( isset( $response['data'] ) && ! empty( $response['data']['subscriptionDefinitions'] ) ) {
				foreach ( $response['data']['subscriptionDefinitions'] as $subs ) {
					$subs_data[ 'mwb_gf_hs_optin_' . $subs['id'] ] = array(
						'name'     => 'mwb_gf_hs_optin_' . $subs['id'],
						'label'    => $subs['name'],
						'type'     => 'Text',
						'required' => false,
						'custom'   => false,
					);
				}
			}
		}

		return $subs_data;
	}

	/**
	 * Check for mandatory fields and add an index to it also retricts phone fields.
	 *
	 * @param    array $object  Feed object.
	 * @since    1.0.0
	 * @return   array
	 */
	public function get_objects_name( $object = false ) {
		if ( empty( $object ) ) {
			return;
		}

		$objects = $this->get_crm_objects();

		if ( false !== strpos( $object, 'mwb_gf_hs_' ) ) {

			if ( false !== strpos( $object, ' - ' ) ) {
				$seperator = strpos( $object, ' - ' );
				$object    = trim( substr( $object, 0, $seperator ) );
			}

			foreach ( $objects as $key => $value ) {
				if ( $object == $key ) {
					$object = $value;
				}
			}
		}

		return $object;
	}
}
