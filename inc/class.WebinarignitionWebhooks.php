<?php

defined( 'ABSPATH' ) || exit;

if( !class_exists('WebinarIgnitionWebhooks') ) {
	class WebinarIgnitionWebhooks {

		public function __construct() {
			//Webhooks
			add_action( 'webinarignition_lead_added', [ $this, 'webinarignition_user_register_cb' ], 10, 3 );
			add_action( 'webinarignition_lead_status_changed', [ $this, 'webinarignition_lead_status_changed_cb' ], 10, 3 );
			add_action( 'webinarignition_lead_purchased', [ $this, 'webinarignition_lead_purchased_cb' ], 10, 2 );
			add_action( 'wp_ajax_webinarignition_webhook_test_request', [ $this, 'webinarignition_webhook_test_request' ] );

			//Others
			add_action( 'admin_init', [ $this, 'webhook_crud' ] );
			add_action( 'admin_init', [ $this, 'update_webhook_trigger_register' ] );
			add_action( 'admin_notices', [ $this, 'display_webhook_saved_notice' ], 99 );
		}

		public function display_webhook_saved_notice() {

			$is_admin_page = $this->webinarignition_webhook_is_admin_page();
			$action        = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS );
			$webhook_id    = absint(filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT ));
			$saved         = absint(filter_input( INPUT_GET, 'saved', FILTER_SANITIZE_NUMBER_INT ));
			$deleted       = absint(filter_input( INPUT_GET, 'deleted', FILTER_SANITIZE_NUMBER_INT ));

			if( $is_admin_page && ($saved || $deleted) ) {

				$string = __('deleted');

				if( $action === 'edit' && $webhook_id > 0 && $saved ) {
					$string = __('saved');
				}
				?>
                <div class="notice notice-success">
                    <p><?php echo sprintf(__( 'Webhook has been %s successfully', 'webinarignition' ), $string); ?></p>
                </div>
				<?php
			}
		}

		private function webinarignition_webhook_get_all($trigger) {
			global $wpdb;
			$table = "{$wpdb->prefix}webinarignition_webhooks";
			$webhooks = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WI_WEBHOOKS WHERE WI_WEBHOOKS.trigger = %s AND WI_WEBHOOKS.is_active = %d", $trigger, 1 ), ARRAY_A );

			if ( is_null( $webhooks ) ) { //Silently handle null result
				$webhooks = [];
			}

			return $webhooks;
		}

		public static function webinarignition_webhook_get_data($webhook_id) {
			global $wpdb;
			$table = "{$wpdb->prefix}webinarignition_webhooks";
			$webhook_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WI_WEBHOOKS WHERE WI_WEBHOOKS.id = %d", $webhook_id), ARRAY_A);

			if( is_null($webhook_data) ) { //Silently handle null result
				$webhook_data = [];
			}

			return wp_parse_args($webhook_data, [
				'id' => 0,
				'name' => '',
				'trigger' => 'register',
				'integration' => 'default',
				'url' => '',
				'request_method' => 1,
				'request_format' => 0,
				'secret' => '',
				'is_active' => 0,
				'conditions' => '',
				'modified' => current_time('mysql')
			]);
		}

		public static function webinarignition_webhook_get_triggers() {
			return apply_filters('webinarignition_webhook_triggers', [
				'registered'       => __( 'Registered','webinarignition' ),
//				'ar_submit'      => __( 'AR Submit','webinarignition' ),
				'attended'         => __( 'Attended','webinarignition' ),
//				'watched_replay' => __( 'Watched Replay','webinarignition' ),
				'purchased'      => __( 'Purchased','webinarignition' )
			]);
		}

		public static function webinarignition_webhook_get_integrations() {
			return apply_filters('webinarignition_webhook_integrations', [
				'default'   => __( 'Default','webinarignition' ),
				'fluentcrm' => __( 'FluentCRM','webinarignition' ),
			]);
		}

		private static function convert_into_data_template($integration, $trigger, $data) {
			$data_templates = self::get_data_templates();

			if( !empty($integration) && $integration !== 'default' ) {

				if ( isset( $data_templates[ $integration ] ) && is_array( $data_templates[ $integration ] ) && ! empty( $data_templates[ $integration ] ) ) {
					$data_template = $data_templates[ $integration ];

					if ( ! empty( $trigger ) ) {
						if ( isset( $data_template[ $trigger ] ) && is_array( $data_template[ $trigger ] ) && ! empty( $data_template[ $trigger ] ) ) {
							foreach ( $data_template[ $trigger ] as $old_key => $new_key ) {
								if ( isset( $data[ $old_key ] ) ) {
									$data[ $new_key ] = $data[ $old_key ];
								} else {
									$data[ $new_key ] = '';
								}
								unset( $data[ $old_key ] );
							}
						}
					}
				}
			}

			return $data;
		}

		private static function get_data_templates() {
			$data_templates = apply_filters('webinarignition_webhook_get_data_templates', [
				'fluentcrm' => [
					'register' => [
						'attendee_email' => 'email',
						'attendee_name' => 'full_name'
					],
					'ar_submit' => [
						'attendee_email' => 'email',
						'attendee_name' => 'full_name'
					]
				]
			]);

			return $data_templates;
		}

		private static function get_demo_data($trigger) {
			$demo_data = [
				'register' => [
					"webinar_id" => "1",
					"webinar_name" => "Demo Webinar Title",
					"webinar_date" => "05-31-2022",
					/*"attendee_name" => "Jonh Doe",
					"attendee_email" => "john.doe@example.com",
					"attendee_user_id" => 1*/
				]
			];

			if( isset($demo_data[$trigger]) && is_array($demo_data[$trigger]) && !empty($demo_data[$trigger]) ) {
				return $demo_data[$trigger];
			}

			return [];
		}

		private static function get_default_data_templates($trigger) {
			$triggers = self::webinarignition_webhook_get_triggers();

			$default_data_template = [];

			foreach ($triggers as $trigger => $trigger_name) {
				$default_data_template[$trigger] = array_keys(self::get_demo_data($trigger));
			}

			return $default_data_template;
		}

		private function webinarignition_webhook_generate_signature( $payload, $webhook_id, $webhook_secret ) {
			$hash_algo = apply_filters( 'webinarignition_webhook_hash_algorithm', 'sha256', $payload, $webhook_id );

			if(is_array($payload)) {
				$payload = json_encode($payload);
			}

			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			return base64_encode( hash_hmac( $hash_algo, $payload, wp_specialchars_decode( $webhook_secret, ENT_QUOTES ), true ) );
		}

		public function webinarignition_user_register_cb($webinar_id, $lead_id, $lead_metadata) {

			$webhooks = $this->webinarignition_webhook_get_all('registered');

			if(!empty($webhooks)) {

				$payload = $this->create_payload($lead_id, $webinar_id);

				foreach ($webhooks as $webhook) {
					$this->webinarignition_webhook_deliver($webhook, $payload);
				}
			}
		}

		private function get_user_agent() {
			return isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : ''; // @codingStandardsIgnoreLine
		}

		private function webinarignition_webhook_deliver($webhook, $payload) {

			$webhook_id      = $webhook['id'];
			$webhook_trigger = $webhook['trigger'];
			$webhook_integration = $webhook['integration'];
			$webhook_url     = $webhook['url'];
			$webhook_request_method = absint($webhook['request_method']) === 1 ? 'POST' : 'GET';
			$webhook_request_format = absint($webhook['request_format']) === 1 ? 'FORM' : 'JSON';
			$webhook_secret  = $webhook['secret'];
			$webhook_conditions  = maybe_unserialize($webhook['conditions']);
//            Webinar_Ignition_Helper::debug_log($payload, true, true);
			if( !empty($webhook_conditions) && is_array($webhook_conditions) ) {
				$this->apply_webhook_conditions_on_payload($webhook_conditions, $payload);
			}
//            Webinar_Ignition_Helper::debug_log($payload);
			$content_type = 'application/x-www-form-urlencoded';

			$payload = apply_filters( 'webinarignition_webhook_payload', $payload, $webhook );

			if ( $webhook_request_format === 'JSON' ) {
				$content_type = 'application/json';
				$payload      = trim( wp_json_encode( $payload ) );
				if ( $webhook_request_method === 'GET' ) {
					$payload = [ 'payload' => $payload ];
				}
			}

			// Setup request args.
			$http_args = array(
				'method'      => $webhook_request_method,
				'timeout'     => MINUTE_IN_SECONDS,
				'redirection' => 0,
				'httpversion' => '1.0',
				'blocking'    => true,
				'user-agent'  => sprintf( 'WebinarIgnition/%s Hookshot (WordPress/%s)', WEBINARIGNITION_VERSION, $GLOBALS['wp_version'] ),
				'body'        => $payload,
				'headers'     => array(
					'Content-Type' => $content_type,
				),
				'cookies'     => array(),
			);

			//$http_args = apply_filters( 'webinarignition_webhook_http_args', $http_args, $arg, $webhook_id ); //TODO: Need to check why this giving error

			// Add custom headers.
			$delivery_id                                      = $this->webinarignition_webhook_get_new_delivery_id( $webhook_id );
			$http_args['headers']['X-WI-Webhook-Source']      = home_url( '/' ); // Since 2.6.0.
			$http_args['headers']['X-WI-Webhook-Trigger']     = $webhook_trigger;
			$http_args['headers']['X-WI-Webhook-Signature']   = $this->webinarignition_webhook_generate_signature( $http_args['body'], $webhook_id, $webhook_secret );
			$http_args['headers']['X-WI-Webhook-ID']          = $webhook_id;
			$http_args['headers']['X-WI-Webhook-Delivery-ID'] = $delivery_id;

			// Webhook away!
			$response = wp_safe_remote_request( $webhook_url, $http_args );

			do_action( 'webinarignition_webhook_response', $response, $webhook_url, $http_args );

		}

		private function apply_webhook_conditions_on_payload($webhook_conditions, &$payload) {
			if( !empty($webhook_conditions['field']) && is_array($webhook_conditions['field']) ) {

				$mapped_fields = [];
				foreach( $webhook_conditions['field'] as $index => $field ) {

                    //Rename "date_registered_on" and "time_registered_on" to "webinar_registration_date" and "webinar_registration_time" respectively
                    if( $field == 'date_registered_on') {
                        $field = 'webinar_registration_date';
                    } else if( $field == 'time_registered_on') {
                        $field = 'webinar_registration_time';
                    }

                    $pos = strpos('webinar_', $field);
                    if( $pos !== false ) {
                        $field = str_replace('webinar_', '', $field);
                    }

					if( !isset($payload[$field]) ) continue; //TODO: Need to test this, if payload[$field] is not set then its producing undefined index notice in PHP

					$operator = null;
					$operator_value = null;
					$new_field_name = null;
					$new_field_value = null;

					if( isset($webhook_conditions['operator'][$index]) ) {
						$operator = $webhook_conditions['operator'][$index];
					}

					if( isset($webhook_conditions['value'][$index]) ) {
						$operator_value = $webhook_conditions['value'][$index];
					}

					if( isset($webhook_conditions['field_name'][$index]) ) {
						$new_field_name = trim($webhook_conditions['field_name'][$index]);
					}

					if( isset($webhook_conditions['field_value'][$index]) ) {
						$new_field_value = $webhook_conditions['field_value'][$index];
					}

					if( $operator === 'map' ) {
						if( isset($payload[$field]) && !empty($new_field_name) && $field != $new_field_name ) {
							$payload[$new_field_name] = $payload[$field];
							$mapped_fields[] = $field;
						}
					} else {

						$is_condition_matched = false;

						if( $operator === 'equal' ) {
							$is_condition_matched = ( $payload[$field] == $operator_value );
						} else if( $operator === 'greater_than' ) {
							$is_condition_matched = ( absint($payload[$field]) >= absint($operator_value) );
						} else if( $operator === 'less_than' ) {
							$is_condition_matched = ( absint($payload[$field]) <= absint($operator_value) );
						} else if( $operator === 'not_equal' ) {
							$is_condition_matched = ( $payload[$field] != $operator_value );
						}

						//Check if string enclosed in square brackets
						$start_pos = strpos($new_field_value,'[', 0);
						$end_pos = strpos($new_field_value,']', 0);

						$string_length = strlen($new_field_value);

						if( $string_length > 2 ) {

							$string_length = $string_length - 1;
							if( $start_pos === 0 && $end_pos === $string_length ) {
								if( json_decode($new_field_value) !== null ) { //Detect if valid JSON array then use as it is
									$new_field_value = json_decode($new_field_value);
								} else { //Make an array from comma separated values
									$new_field_value = substr_replace( $new_field_value, '', $start_pos, 1 );
									$end_pos         = strpos( $new_field_value, ']', 0 );
									$new_field_value = substr_replace( $new_field_value, '', $end_pos, 1 );
									$new_field_value = explode( ',', stripslashes($new_field_value) );
								}
							}
						}

						if($is_condition_matched) {

							if( is_array($new_field_value) && array_key_exists($new_field_name, $payload) ) { //if array and key already exists, then merge it
								$new_field_value = array_merge( $payload[ $new_field_name ], $new_field_value );
							}

							$payload[$new_field_name] = $new_field_value;
						}
					}
				}

				//Remove newly mapped fields old data, to avoid duplication
				if( !empty($mapped_fields) ) {
					foreach ($mapped_fields as $mapped_field) {
						unset($payload[$mapped_field]);
					}
				}
			}
		}

		public function webinarignition_webhook_test_request() {

			$webhook_url = filter_input( INPUT_POST, 'webhook_url', FILTER_SANITIZE_SPECIAL_CHARS );
			$webhook_id = filter_input( INPUT_POST, 'webhook_id', FILTER_SANITIZE_SPECIAL_CHARS );
			$webhook_secret = filter_input( INPUT_POST, 'webhook_secret', FILTER_SANITIZE_SPECIAL_CHARS );
			$webhook_trigger = filter_input( INPUT_POST, 'webhook_trigger', FILTER_SANITIZE_SPECIAL_CHARS );
			$webhook_request_method = filter_input( INPUT_POST, 'webhook_request_method', FILTER_SANITIZE_SPECIAL_CHARS );
			$webhook_request_method = absint($webhook_request_method) === 1 ? 'POST' : 'GET';
			$webhook_request_format = filter_input( INPUT_POST, 'webhook_request_format', FILTER_SANITIZE_SPECIAL_CHARS );
			$webhook_request_format = absint($webhook_request_format) === 1 ? 'FORM' : 'JSON';

			$content_type = 'application/x-www-form-urlencoded';
			$webinar_data = WebinarignitionManager::get_webinar_data(1);
			print_r($webinar_data->ar_fields_order); exit;
			foreach ($webinar_data->ar_fields_order as $_field) {
				if ( empty( $webinar_data->$_field ) ) {
					continue;
				}
			}
			exit;
			$payload = [
				'webinar_id' => 1,
				'webinar_name' => "Test Webinar Name",
				'webinar_time' => "2022-05-04+18%3A28%3A20",
				/*'attendee_user_id' => 3,
				'attendee_name' => "Frank+Spencer",
				'attendee_email' => "frank%40example.com",*/
			];

			if( $webhook_request_format === 'JSON' ) {
				$content_type = 'application/json';
				$payload = trim(wp_json_encode( $payload ));
				if($webhook_request_method === 'GET') {
					$payload = [ 'payload' => $payload ];
				}
			}

			// Setup request args.
			$http_args = array(
				'method'      => $webhook_request_method,
				'timeout'     => MINUTE_IN_SECONDS,
				'redirection' => 0,
				'httpversion' => '1.0',
				'blocking'    => true,
				'user-agent'  => sprintf( 'WebinarIgnition/%s Hookshot (WordPress/%s)', WEBINARIGNITION_VERSION, $GLOBALS['wp_version'] ),
				'body'        => $payload,
				'headers'     => array(
					'Content-Type' => $content_type,
				),
				'cookies'     => array(),
			);

			//$http_args = apply_filters( 'webinarignition_webhook_http_args', $http_args, $arg, $webhook_id );

			// Add custom headers.
			$delivery_id                                      = $this->webinarignition_webhook_get_new_delivery_id($webhook_id);
			$http_args['headers']['X-WI-Webhook-Source']      = home_url( '/' ); // Since 2.6.0.
			$http_args['headers']['X-WI-Webhook-Trigger']     = $webhook_trigger;
			$http_args['headers']['X-WI-Webhook-Signature']   = $this->webinarignition_webhook_generate_signature( $http_args['body'], $webhook_id, $webhook_secret );
			$http_args['headers']['X-WI-Webhook-ID']          = $webhook_id;
			$http_args['headers']['X-WI-Webhook-Delivery-ID'] = $delivery_id;

			// Webhook away!
			$response = wp_safe_remote_request( $webhook_url, $http_args );
			var_dump($response); exit;
		}


		private function webinarignition_webhook_get_new_delivery_id($webhook_id) {
			// Since we no longer use comments to store delivery logs, we generate a unique hash instead based on current time and webhook ID.
			return wp_hash( $webhook_id . strtotime( 'now' ) );
		}

		private function webinarignition_webhook_is_admin_page() {
			$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS );
			$tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_SPECIAL_CHARS );
			return ($page === 'webinarignition_settings' && $tab === 'webhooks');
		}

		public function webhook_crud() {
			if( $this->webinarignition_webhook_is_admin_page() ) {
				if ( isset( $_POST['save'] ) && isset( $_POST['webhook_id'] ) ) { // WPCS: input var okay, CSRF ok.
					$this->save_webhook();
				} else {
					$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS );
					$webhook_id = absint(filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT ));

					if( $action === 'delete' && !empty($webhook_id) ) {
						if (wp_verify_nonce($_GET['wp_nonce'], 'delete_webhook_' . $webhook_id)) {
							global $wpdb;
							$table = "{$wpdb->prefix}webinarignition_webhooks";
							$deleted = $wpdb->delete($table, ['id' => $webhook_id], ['%d']);
							if($deleted) {
								wp_safe_redirect( add_query_arg( [
									'page'   => 'webinarignition_settings',
									'tab'    => 'webhooks',
									'deleted'  => 1
								], admin_url('admin.php') ) );
								exit;
							}
						}

					}
				}
			}
		}

		private function save_webhook() {
			check_admin_referer( 'webinarignition-webhook-save' );
			$webhook_id = absint($_POST['webhook_id']);
			$data = [];
			if ( ! empty( $_POST['webhook_name'] ) ) { // WPCS: input var okay, CSRF ok.
				$data['name'] = sanitize_text_field( wp_unslash( $_POST['webhook_name'] ) ); // WPCS: input var okay, CSRF ok.
			} else {
				$data['name'] = sprintf(
				/* translators: %s: date */
					__( 'Webhook created on %s', 'webinarignition' ),
					// @codingStandardsIgnoreStart
					strftime( _x( '%b %d, %Y @ %I:%M %p', 'Webhook created on date parsed by strftime', 'webinarignition' ) )
				// @codingStandardsIgnoreEnd
				);
			}

			//Cleanup webhook conditions post data
			if(is_array($_POST['wi_webhooks_condition'])) {
				foreach ($_POST['wi_webhooks_condition'] as &$condition) {
					if( is_array($condition) ) {
						if( isset($condition[0]) ) {
							unset( $condition[0] );
						}
					}

					$condition = array_values($condition);

					foreach ($condition as &$item) {
						$item = sanitize_text_field($item);
					}
				}
			}

			$data['trigger'] = isset($_POST['webhook_trigger']) ? sanitize_text_field( wp_unslash($_POST['webhook_trigger']) ) : '';
			$data['integration'] = isset($_POST['webhook_integration']) ? sanitize_text_field( wp_unslash($_POST['webhook_integration']) ) : 'default';
			$data['url']    = isset($_POST['webhook_url']) ? esc_url_raw( wp_unslash($_POST['webhook_url']) ) : '';
			$data['request_method'] = isset($_POST['webhook_request_method']) ? absint($_POST['webhook_request_method']) : 0;
			$data['request_format'] = isset($_POST['webhook_request_format']) ? absint($_POST['webhook_request_format']) : 0;
			$data['secret'] = isset($_POST['webhook_secret']) ? sanitize_text_field( wp_unslash($_POST['webhook_secret']) ) : '';
			$data['is_active'] = isset($_POST['webhook_status']) ? absint($_POST['webhook_status']) : 0;
			$data['conditions'] = ( isset($_POST['wi_webhooks_condition']) && !empty($_POST['wi_webhooks_condition']) ) ? serialize($_POST['wi_webhooks_condition']) : '';
			$data['modified'] = current_time('mysql');

			global $wpdb;
			$table = "{$wpdb->prefix}webinarignition_webhooks";
			if(empty($webhook_id)) {
				$wpdb->insert( $table, $data );
				$webhook_id = $wpdb->insert_id;
			} else {
				$wpdb->update( $table, $data, ['id' => $webhook_id] );
			}

			wp_safe_redirect( add_query_arg( [
				'page'   => 'webinarignition_settings',
				'tab'    => 'webhooks',
				'action' => 'edit',
				'id'     => $webhook_id,
				'saved'  => 1
			], admin_url('admin.php') ) );
			exit;
		}

		public function webinarignition_lead_status_changed_cb($lead_status, $lead_id, $webinar_id) {

			$webhooks = $this->webinarignition_webhook_get_all($lead_status);

			if( !empty($webhooks) ) {

				$payload = $this->create_payload($lead_id, $webinar_id);

				if( !empty($payload) ) {
					foreach ( $webhooks as $webhook ) {
						$this->webinarignition_webhook_deliver( $webhook, $payload );
					}
				}
			}
		}

		/**
		 * @param $lead_id
		 * @param $webinar_id
		 *
		 * @return array
		 */
		private function create_payload($lead_id, $webinar_id) {

			$payload = [];

			$webinar_data = WebinarignitionManager::get_webinar_data($webinar_id);

			if( !empty($webinar_data) ) {

				$payload = [
					'id' => $webinar_id,
					'title' => $webinar_data->webinar_desc,
					'url' => WebinarignitionManager::get_permalink($webinar_data, 'registration')
				];

				$webinar_data_fields = self::get_webinar_data_fields();
				foreach($webinar_data_fields as $webinar_data_field) {
					if( property_exists($webinar_data, $webinar_data_field) ) {
						$payload[$webinar_data_field] = $webinar_data->{$webinar_data_field};
					}
				}

				$leadInfo = null;
				if ( ! empty( $lead_id ) ) {
					$leadInfo = webinarignition_get_lead_info( $lead_id, $webinar_data, false );
				}

				if ( ! empty( $leadInfo ) ) { //Get name/email details from lead, if available

					$webinar_timezone = webinarignition_get_webinar_timezone($webinar_data, null, $leadInfo);
                    $webinar_date_time = webinarignition_get_webinar_datetime($webinar_data, $leadInfo);

                    $datetime_webinar = new DateTime($webinar_date_time, new DateTimeZone($webinar_timezone) );
                    $datetime_now = new DateTime("now", new DateTimeZone($webinar_timezone) );

                    $payload['date'] = $datetime_webinar->format('Y-m-d');
                    $payload['time'] = $datetime_webinar->format('H:i:s');
                    $payload['date_time'] = $datetime_webinar->format('c'); //format "YYYY-MM-DDThh:mm:ss.sTZD" JSON/JS standards
                    $payload['registration_date'] = $datetime_now->format('Y-m-d');
                    $payload['registration_time'] = $datetime_now->format('H:i:s');
                    $payload['registration_date_time'] = $datetime_now->format('c'); //format "YYYY-MM-DDThh:mm:ss.sTZD" JSON/JS standards
                    $payload['host'] = isset($webinar_data->webinar_host) ? $webinar_data->webinar_host : '';
                    $payload['timezone'] = $webinar_timezone;
                    $payload['lead_url'] = WebinarignitionManager::get_permalink($webinar_data, 'registration').'?confirmed&lid='.$leadInfo->hash_ID;

					$lead_meta = [];

					$lead_data = WebinarignitionLeadsManager::get_lead_meta( $leadInfo->ID, 'wiRegForm', $webinar_data->webinar_date == 'AUTO' ? 'evergreen' : 'live' );

					if ( ! empty( $lead_data ) ) {

						$wiRegForm_data = (array) maybe_unserialize( $lead_data['meta_value'] );
						$wiRegForm_data = WebinarignitionLeadsManager::fix_optName($wiRegForm_data);

						foreach ( $wiRegForm_data as $field_name => $field ) {
							$field_value              = sanitize_text_field( $field['value'] );
							$lead_meta[ $field_name ] = $field_value;
						}
					}

					$payload = array_merge( $payload, $lead_meta );
				}
			}

			return $payload;
		}

		public function webinarignition_lead_purchased_cb($lead_id, $webinar_id) {

			$webhooks = $this->webinarignition_webhook_get_all('purchased');

			if( !empty($webhooks) ) {
				$payload = $this->create_payload($lead_id, $webinar_id);

				if( !empty($payload) ) {
					foreach ( $webhooks as $webhook ) {
						$this->webinarignition_webhook_deliver( $webhook, $payload );
					}
				}
			}
		}

		/**
         * TODO: This function should be deleted in new version
         *
		 * Rename already saved webhook triggers - "register" to "registered", "attend" to "attended"
		 */
		public function update_webhook_trigger_register() {
			$transient_key = 'wi_update_webhook_trigger_register';

			if( get_transient($transient_key) === false ) {

				global $wpdb;

				$table   = "{$wpdb->prefix}webinarignition_webhooks";

				$wpdb->update( $table, [
					'trigger' => 'registered',
				], [
					'trigger' => 'register',
				], [
					'%s'
				] );

				$wpdb->update( $table, [
					'trigger' => 'attended',
				], [
					'trigger' => 'attend',
				], [
					'%s'
				] );

				set_transient($transient_key, 1, MONTH_IN_SECONDS);
			}
		}

		/**
		 * Fields to fetch data from $webinar_data object
		 *
		 * @return string[]
		 */
		public static function get_webinar_data_fields() {
			return [
//				'webinar_date',
//				'webinar_time',
//              'webinar_host',
//				'webinar_timezone',
//				'webinar_registration_date',
//				'webinar_registration_time',
//				'utm_source'
			];
		}
	}

	$webinarIgnitionWebhooks = new WebinarIgnitionWebhooks();
}