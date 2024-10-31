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
class Mwb_Gf_Integration_Hubspot_Request_Module {

	/**
	 *  The instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string    $_instance    The instance of this class.
	 */
	private static $_instance; // phpcs:ignore

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
	 *  The instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string    $feed id to record  The feed to sync.
	 */
	public static $feed_id;

	/**
	 * Instance of the plugin main class.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      object   $core_class    Name of the plugin core class.
	 */
	public $core_class = 'Mwb_Gf_Integration_For_Hubspot';

	/**
	 * Main Mwb_Gf_Integration_Hubspot_Request_Module Instance.
	 *
	 * Ensures only one instance of Mwb_Gf_Integration_Hubspot_Request_Module is loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Mwb_Gf_Integration_Hubspot_Request_Module - Main instance.
	 */
	public static function get_instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

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
		$crm_class            = 'Mwb_Gf_Integration_' . $this->crm_name . '_Api';
		$this->crm_api_module = $crm_class::get_instance();
	}

	/**
	 * Returns the requested values for required form index.
	 *
	 * @param     string $key      The form field meta key.
	 * @param     array  $data     An array of form entries data.
	 * @since     1.0.0
	 * @return    string           The post meta values.
	 */
	public function get_prop_value( $key = false, $data = array() ) {

		if ( empty( $key ) || ! is_array( $data ) ) {
			return;
		}

		foreach ( $data as $field => $value ) {
			if ( $key == $field ) { // @codingStandardsIgnoreLine
				return $value;
			}
		}
	}

	/**
	 * Returns the feed data we require.
	 *
	 * @param     int    $feed_id      The object id for post type feed.
	 * @param     string $meta_key     The object meta key for post type feed.
	 * @return    array|bool The current data for required object.
	 * @since 1.0.0
	 */
	public function get_feed( $feed_id = false, $meta_key = 'mapping_data' ) {
		if ( false == $feed_id ) { // phpcs:ignore
			return;
		}

		if ( 'mapping_data' == $meta_key ) { // phpcs:ignore
			$meta_key = 'mwb-' . $this->crm_slug . '-gf-mapping-data';
		}

		$mapping = get_post_meta( $feed_id, $meta_key, true );

		if ( empty( $mapping ) ) {
			$mapping = false;
		}

		return $mapping;
	}

	/**
	 * Returns the mapping step we require.
	 *
	 * @param string $crm_obj  The CRM module.
	 * @param string $feed_id  Feed ID.
	 * @param string $entries  Current form entries.
	 *
	 * @since  1.0.0
	 * @return array The current mapping step required.
	 */
	public function get_crm_request( $crm_obj = false, $feed_id = false, $entries = array() ) {
		if ( false == $crm_obj || false == $feed_id || ! is_array( $entries ) ) { // phpcs:ignore
			return;
		}

		$feed            = $this->get_feed( $feed_id ); // Get feed mapping data.
		$connect         = 'Mwb_Gf_Integration_Connect_' . $this->crm_name . '_Framework';
		$connect_manager = $connect::get_instance();

		if ( empty( $feed ) ) {
			return false;
		}

		// Process Feeds.
		$response = array();

		foreach ( $feed as $k => $mapping ) {

			$field_type = ! empty( $mapping['field_type'] ) ? $mapping['field_type'] : 'standard_field';

			switch ( $field_type ) {

				case 'standard_field':
					$field_format = ! empty( $mapping['field_value'] ) ? $mapping['field_value'] : '';
					$meta_key     = substr( $field_format, 10 );
					$field_value  = $this->get_prop_value( $meta_key, $entries );
					break;

				case 'custom_value':
					$field_key = ! empty( $mapping['custom_value'] ) ? $mapping['custom_value'] : '';

					preg_match_all( '/{(.*?)}/', $field_key, $dynamic_strings );

					if ( ! empty( $dynamic_strings[1] ) ) {
						$dynamic_values = $dynamic_strings[1];

						foreach ( $dynamic_values as $key => $value ) {
							$field_format = substr( $value, 10 );
							$field_value  = $this->get_prop_value( $field_format, $entries );

							$substr = '{' . $value . '}';

							$field_key   = str_replace( $substr, $field_value, $field_key );
							$field_value = $field_key;
						}
					}
					break;
			}

			$response[ $k ] = ! empty( $field_value ) ? $field_value : '';
		}

		// Restructure request as per hubspot.
		$crm_fields = $this->get_feed( $feed_id, 'mwb-' . $this->crm_slug . '-gf-crm-fields' );
		$crm_fields = json_decode( $crm_fields, true );

		$request = array();

		if ( ! empty( $response ) && is_array( $response ) ) {

			if ( false !== strpos( $crm_obj, 'mwb_gf_hs_' ) ) {
				$request = $this->get_structured_form_request( $response, $crm_fields );

				$request['is_form']   = true;
				$request['responses'] = $response;
			} else {

				$request = $this->get_structured_object_request( $crm_obj, $response, $crm_fields );

				$request['is_form']   = false;
				$request['responses'] = $response;
			}
		}

		$request = apply_filters( 'mwb_' . $this->crm_slug . '_gf_request_data', $request, $crm_obj, $feed_id, $entries );

		return $request;

	}

	/**
	 * Get structured form request.
	 *
	 * @param array $response_data   Feed maaping data.
	 * @param array $obj_fields      Fields of an object.
	 * @since 1.0.0
	 * @return array
	 */
	public function get_structured_form_request( $response_data = array(), $obj_fields = array() ) {

		if ( empty( $response_data ) || ! is_array( $response_data ) || empty( $obj_fields ) || ! is_array( $obj_fields ) ) {
			return;
		}

		$context_arr = array(
			'mwb_gf_hs_url'         => 'pageUri',
			'mwb_gf_hs_title'       => 'pageName',
			'mwb_gf_hs_webinar_key' => 'goToWebinarWebinarKey',
			'mwb_gf_hs_camp_id'     => 'sfdcCampaignId',
			'mwb_gf_hs_ip'          => 'ipAddress',
		);

		$form_request = array();
		$consent_arr  = array();

		// Now restructure data.
		foreach ( $response_data as $key => $value ) {
			if ( ! empty( $context_arr[ $key ] ) ) {

				if ( 'mwb_gf_hs_url' == $key && ! empty( $value ) ) { // phpcs:ignore
					$value = strtok( $value, '?' );
				}

				$form_request['context'][ $context_arr[ $key ] ] = $value;

			} elseif ( 'mwb_gf_hs_consent' == $key ) { // phpcs:ignore
				$form_request['legalConsentOptions'] = array(
					'consent' => array(
						'consentToProcess' => ! empty( $value ),
						'text'             => 'I agree to allow to store and process my personal data.',
					),
				);

			} elseif ( false !== strpos( $key, 'optin_' ) ) {
				$val           = ! empty( $value ) ? true : false;
				$consent_arr[] = array(
					'value'              => $val,
					'text'               => $value,
					'subscriptionTypeId' => substr( $key, 17 ),
				);
			} else {
				$type  = ! empty( $obj_fields[ $key ]['type'] ) ? $obj_fields[ $key ]['type'] : '';
				$value = $this->maybe_verify_values( $value, $type );

				$form_request['fields'][] = array(
					'name'  => $key,
					'value' => $value,
				);
			}
		}

		if ( ! empty( $consent_arr ) && isset( $form_request['legalConsentOptions'] ) ) {
			$form_request['legalConsentOptions']['consent']['communications'] = $consent_arr;
		}

		return $form_request;
	}

	/**
	 * Get structured object request.
	 *
	 * @param string $object          CRM object.
	 * @param array  $response_data   Feed maaping data.
	 * @param array  $obj_fields      Fields of an object.
	 * @since 1.0.0
	 * @return array
	 */
	public function get_structured_object_request( $object = false, $response_data = array(), $obj_fields = array() ) {

		if ( empty( $object ) || empty( $response_data ) || ! is_array( $response_data ) || empty( $obj_fields ) || ! is_array( $obj_fields ) ) {
			return;
		}

		$obj_request = array();

		if ( 'Task' == $object ) { // phpcs:ignore

			if ( isset( $response_data['description'] ) ) {
				$obj_request['engagement'] = array(
					'type' => 'TASK',
				);
			}

			if ( ! empty( $response_data['timestamp'] ) ) {
				$timestamp = strtotime( $response_data['timestamp'] );

				if ( ! empty( $timestamp ) ) {
					$offset    = get_option( 'gmt_offset' ) * 3600;
					$timestamp = $timestamp - $offset;
					$timestamp = $timestamp . '000';
				}

				$obj_request['engagement']['timestamp'] = $timestamp;
			}

			$metadata = array();
			foreach ( $response_data as $key => $value ) {
				if ( in_array( $key, array( 'forObjectType', 'subject', 'status', 'description' ) ) ) { // phpcs:ignore

					if ( ! empty( $value ) ) {
						if ( 'description' == $key ) { // phpcs:ignore
							$metadata['body'] = $value;
						} else {
							$metadata[ $key ] = $value;
						}
					}
				}
			}
			$obj_request['metadata'] = $metadata;
		} else {

			$index = 'property';

			if ( in_array( $object, array( 'Company', 'Ticket' ) ) ) { // phpcs:ignore
				$index = 'name';
			}

			// Now restructure data.
			foreach ( $response_data as $key => $value ) {

				if ( 'Ticket' == $object && 'hs_pipeline_stage' == $key ) { // phpcs:ignore
					continue;
				}

				$type  = ! empty( $obj_fields[ $key ]['type'] ) ? $obj_fields[ $key ]['type'] : '';
				$value = $this->maybe_verify_values( $value, $type );

				$obj_request[] = array(
					$index  => $key,
					'value' => $value,
				);
			}

			if ( 'Ticket' == $object ) { // phpcs:ignore
				$obj_request[] = array(
					$index  => 'hs_pipeline_stage',
					'value' => 1,
				);
			}

			if ( 'Ticket' != $object ) { // phpcs:ignore
				$obj_request = array(
					'properties' => $obj_request,
				);
			}
		}
		return $obj_request;
	}

	/**
	 * Verify value
	 *
	 * @param mixed  $value  Entry value.
	 * @param string $type   Type of value.
	 * @since 1.0.0
	 * @return mixed
	 */
	public function maybe_verify_values( $value, $type ) {

		switch ( $type ) {

			case 'number':
				$value = filter_var( $value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
				break;

			case 'file':
				if ( is_string( $value ) ) {
					$files_temp = json_decode( $value, true );

					if ( ! empty( $files_temp ) ) {
						$value = $files_temp;
					}
				}
				break;

			case 'date':
				$date = new DateTime( $value );
				$date->modify( 'midnight' );
				$value = $date->getTimestamp() * 1000;
				break;

			case 'datetime':
				$value = strtotime( $value ) . '000';
				break;

			case 'booleancheckbox':
				$value = ! empty( $value ) ? 'true' : 'false';
				break;

			default:
				if ( is_array( $value ) ) {
					$value = implode( ';', $value );
				}
		}

		return $value;
	}

	/**
	 * Replace the occurence within the string only once.
	 *
	 * @param string $from    The sub-string before replace.
	 * @param string $to      The sub-string after replace.
	 * @param string $content The string from which we operate.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function str_replace_first( $from, $to, $content ) {
		$from = '/' . preg_quote( $from, '/' ) . '/';

		return preg_replace( $from, $to, $content, 1 );
	}

	/**
	 * Retrive form data
	 *
	 * @param [type] $entry  Current submitted Form Entry Data.
	 * @param [type] $form   Current submitted Form.
	 * @since     1.0.0
	 * @return    array
	 */
	public function retrieve_form_data( $entry, $form ) {

		foreach ( $form['fields'] as $field_obj ) {
			if ( ! empty( $field_obj->inputs ) && is_array( $field_obj->inputs ) && 'time' != $field_obj->type ) {   // @codingStandardsIgnoreLine
				foreach ( $field_obj->inputs as $field_options ) {

					$field_values[ $field_options['id'] ] = $entry[ $field_options['id'] ];
				}
			} else {

				$field_values[ $field_obj->id ] = $entry[ $field_obj->id ];
			}
		}

		$form_data = array(
			'id'     => $entry['form_id'],
			'name'   => $form['title'],
			'fields' => $form['fields'],
			'values' => $field_values,
		);

		return $form_data;
	}

	/**
	 * Get all feeds of a respective form id.
	 *
	 * @param     int $form_id    Form id.
	 * @since     1.0.0
	 * @return    array
	 */
	public function get_feeds_by_form_id( $form_id = '' ) {

		if ( empty( $form_id ) ) {
			return;
		}

		// Get all feeds.
		$active_feeds = get_posts(
			array(
				'numberposts' => -1,
				'fields'      => 'ids', // return only ids.
				'post_type'   => 'mwb_' . $this->crm_slug . '_gf',
				'post_staus'  => 'publish',
				'order'       => 'DESC',
				'meta_query'  => array( // phpcs:ignore
					array(
						'relation' => 'AND',
						array(
							'key'     => 'mwb-' . $this->crm_slug . '-gf-form',
							'compare' => 'EXISTS',
						),
						array(
							'key'     => 'mwb-' . $this->crm_slug . '-gf-form',
							'value'   => $form_id,
							'compare' => '==',
						),
					),
				),
			)
		);

		return $active_feeds;
	}

	/**
	 * Check if filter exists in feed.
	 *
	 * @param     int $feed_id    Feed ID.
	 * @since     1.0.0
	 * @return    bool|array
	 */
	public function maybe_check_filter( $feed_id = '' ) {

		if ( empty( $feed_id ) ) {
			return;
		}
		if ( 'yes' == get_post_meta( $feed_id, 'mwb-' . $this->crm_slug . '-gf-enable-filters', true ) ) { // phpcs:ignore
			$meta = get_post_meta( $feed_id, 'mwb-' . $this->crm_slug . '-gf-condtion-field', true );

			if ( ! empty( $meta ) && is_array( $meta ) && count( $meta ) > 0 ) {
				return $meta;
			}
		}

		return false;
	}

	/**
	 * Validate form values with conditions.
	 *
	 * @param    string $option_type    Filter conditon type.
	 * @param    string $feed_value     Value to compare with entry value.
	 * @param    string $form_value     Entry value .
	 *
	 * @since    1.0.0
	 * @return   bool
	 */
	public function is_value_allowed( $option_type = false, $feed_value, $form_value = false ) {

		if ( false == $option_type ) { // phpcs:ignore
			return;
		}

		$time   = current_time( 'timestamp' ); // phpcs:ignore
		$result = false;
		if ( false != $form_value ) { // phpcs:ignore

			switch ( $option_type ) {

				case 'exact_match':
					if ( $feed_value === $form_value ) { // phpcs:ignore
						$result = true;
					}
					break;

				case 'no_exact_match':
					if ( $feed_value !== $form_value ) { // phpcs:ignore
						$result = true;
					}
					break;

				case 'contains':
					if ( false !== strpos( $form_value, $feed_value ) ) {
						$result = true;
					}
					break;

				case 'not_contains':
					if ( false === strpos( $form_value, $feed_value ) ) {
						$result = true;
					}
					break;

				case 'exist':
					if ( false !== strpos( $feed_value, $form_value ) ) {
						$result = true;
					}
					break;

				case 'not_exist':
					if ( false === strpos( $feed_value, $form_value ) ) {
						$result = true;
					}
					break;

				case 'starts':
					if ( 0 === strpos( $form_value, $feed_value ) ) {
						$result = true;
					}
					break;

				case 'not_starts':
					if ( 0 !== strpos( $form_value, $feed_value ) ) {
						$result = true;
					}
					break;

				case 'ends':
					if ( strlen( $form_value ) == strpos( $form_value, $feed_value ) + strlen( $feed_value ) ) { // phpcs:ignore
						$result = true;
					}
					break;

				case 'not_ends':
					if ( strlen( $form_value ) != strpos( $form_value, $feed_value ) + strlen( $feed_value ) ) { // phpcs:ignore
						$result = true;
					}
					break;

				case 'less_than':
					if ( (float) $form_value < (float) $feed_value ) {
						$result = true;
					}
					break;

				case 'greater_than':
					if ( (float) $form_value > (float) $feed_value ) {
						$result = true;
					}
					break;

				case 'less_than_date':
					if ( strtotime( $form_value, $time ) < strtotime( $feed_value, $time ) ) {
						$result = true;
					}
					break;

				case 'greater_than_date':
					if ( strtotime( $form_value, $time ) > strtotime( $feed_value, $time ) ) {
						$result = true;
					}
					break;

				case 'equal_date':
					if ( strtotime( $form_value, $time ) == strtotime( $feed_value, $time ) ) { // phpcs:ignore
						$result = true;
					}
					break;

				case 'empty':
					if ( empty( $form_value ) ) {
						$result = true;
					}
					break;

				case 'not_empty':
					if ( ! empty( $form_value ) ) {
						$result = true;
					}
					break;

				default:
					$result = false;
					break;
			}
		}
		return $result;
	}
	// End of class.
}