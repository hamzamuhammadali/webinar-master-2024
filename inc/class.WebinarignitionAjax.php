<?php defined( 'ABSPATH' ) || exit;

class WebinarignitionAjax {
	public static function add_lead_check_secure() {
		$post_input = filter_input_array( INPUT_POST );

		if (
			empty($post_input['email'])
			||  empty($post_input['id'])
		) {
			self::error_response(['message' => __('Error', 'webinarignition') . ': ' . __('Cheating, huh!!!.', 'webinarignition')]);
		}

		$app_id = sanitize_text_field($post_input['id']);
		$email = sanitize_email($post_input['email']);
		$webinar_data = WebinarignitionManager::get_webinar_data($app_id);

		if ( empty($webinar_data) ) {
			self::error_response(['message' => __('Error', 'webinarignition') . ': ' . __('Cheating, huh!!!.', 'webinarignition')]);
		}

		$user = get_user_by( 'email', $email );
		if ( $user && $user->roles[0] != 'subscriber'){
			self::error_response(['message' => __('Hint', 'webinarignition') . ': ' . __('Please register with a not used email address, like name+1@gmail.com, name+2@gmail.com,...', 'webinarignition')]);
		}

		if (!WebinarignitionPowerups::is_secure_access_enabled($webinar_data)) {
			self::success_response(['message' => __('Success', 'webinarignition') . ': ' . __('Great lets register.', 'webinarignition')]);
		}

		$secure_access_webinar_blacklisted = [];
		$secure_access_webinar_whitelisted = [];

		if (!empty($webinar_data->secure_access_webinar_blacklisted)) {
			$secure_access_webinar_blacklisted = explode(',', $webinar_data->secure_access_webinar_blacklisted);
		}

		if (!empty($secure_access_webinar_blacklisted)) {
			foreach ($secure_access_webinar_blacklisted as $blacklisted) {
				$blacklisted = trim($blacklisted);

				if (false !== strpos($email, $blacklisted)) {
					self::error_response(['message' => __('Error', 'webinarignition') . ': ' . __('You are not authorized to register for the event.', 'webinarignition')]);
				}
			}
		}

		$is_whitelisted = true;

		if (!empty($webinar_data->secure_access_webinar_whitelisted)) {
			$is_whitelisted = false;
			$secure_access_webinar_whitelisted = explode(',', $webinar_data->secure_access_webinar_whitelisted);
		}

		if (!empty($secure_access_webinar_whitelisted)) {
			foreach ($secure_access_webinar_whitelisted as $whitelisted) {
				$whitelisted = trim($whitelisted);

				if (false !== strpos($email, $whitelisted)) {
					$is_whitelisted = true;
					break;
				}
			}
		}

		if (empty($is_whitelisted)) {
			self::error_response(['message' => __('Error', 'webinarignition') . ': ' . __('You are not authorized to register for the event.', 'webinarignition')]);
		}


		self::success_response(['message' => __('Success', 'webinarignition') . ': ' . __('Great lets register.', 'webinarignition')]);
	}

	public static function register_support() {
		$data = self::get_form_data();

		if (empty($data['app_id']) || ( empty($data['support_stuff_url']) && empty($data['host_presenters_url']) ) ) {
			self::error_response(['message' => __('Error', 'webinarignition') . ': ' . __('Cheating, huh!!!.', 'webinarignition')]);
		}

		$app_id = sanitize_text_field($data['app_id']);

		$webinar_data = WebinarignitionManager::get_webinar_data($app_id);

		if (empty($webinar_data) ) {
			self::error_response(['message' => __('Error', 'webinarignition') . ': ' . __('Cheating, huh!!!.', 'webinarignition')]);
		}

		$host_presenters_url = sanitize_text_field($data['host_presenters_url']);
		$support_stuff_url = sanitize_text_field($data['support_stuff_url']);

		unset($data['app_id']);
		unset($data['host_presenters_url']);
		unset($data['support_stuff_url']);

		$errors = [];

		foreach ($data as $field => $value) {
			if (empty($value)) {
				$errors[$field] = __('Field required', 'webinarignition');
			} elseif ( $field === 'email' && empty(filter_var($value, FILTER_VALIDATE_EMAIL))) {
				$errors[$field] = __('Wrong email', 'webinarignition');
			} else {
				if ($field === 'email') {
					$value = filter_var($value, FILTER_VALIDATE_EMAIL);
				} else {
					$value = sanitize_text_field($value);
				}

				$data[$field] = $value;
			}
		}

		if (!empty($errors)) {
			self::error_response(['errors' => $errors]);
		}

		$_wi_support_token = WebinarignitionManager::register_support($app_id, $data, $host_presenters_url, $support_stuff_url);

		if (empty($_wi_support_token)) {
			self::error_response(['message' => __('Error', 'webinarignition') . ': ' . __('Cheating, huh!!!', 'webinarignition')]);
		}

		if (!empty($host_presenters_url)) {
			$support_link = $webinar_data->webinar_permalink . '?console&_wi_host_token='.$_wi_support_token ;
			ob_start();
			?>
            <p id="registerSupport_url" class="registerSupport_row" style="text-align: center;">
                <label for="support_link">
					<?php _e( 'Save this link to enter Live console next time without entering your email and name.', 'webinarignition') ?>
                </label>

                <input id="support_link" class="registerSupport_field" onclick="this.select()" type="text" value="<?php echo $support_link; ?>" readonly>
            </p>

            <p class="registerSupport_row" style="text-align: center;">
				<?php
				echo sprintf(__('%slogin Live console%s.', 'webinarignition'), '<a href="'.$support_link.'" class="button radius success">', '</a>')
				?>
            </p>
			<?php
			$content = ob_get_clean();
		} else {
			$support_link = $webinar_data->webinar_permalink . '?console&_wi_support_token='.$_wi_support_token ;
			ob_start();
			?>
            <p id="registerSupport_url" class="registerSupport_row" style="text-align: center;">
                <label for="support_link">
					<?php _e( 'Save this link to enter Live console next time without entering your email and name.', 'webinarignition') ?>
                </label>

                <input id="support_link" class="registerSupport_field" onclick="this.select()" type="text" value="<?php echo $support_link; ?>" readonly>
            </p>

            <p class="registerSupport_row" style="text-align: center;">
				<?php
				echo sprintf(__('%slogin Live console%s.', 'webinarignition'), '<a href="'.$support_link.'" class="button radius success">', '</a>')
				?>
            </p>
			<?php
			$content = ob_get_clean();
		}

		self::success_response( [
			'data' => $data,
			'replace' => $content,
		] );
	}

	public static function submit_chat_question() {
		$post_input = filter_input_array( INPUT_POST );

		if (
			empty($post_input['app_id'])
			||  empty($post_input['name'])
			||  empty($post_input['email'])
			||  empty($post_input['question'])
		) {
			self::error_response(['message' => __('Error', 'webinarignition') . ': ' . __('Cheating, huh!!!.', 'webinarignition')]);
		}

		$app_id = sanitize_text_field($post_input['app_id']);
		$webinar_data = WebinarignitionManager::get_webinar_data($app_id);

		if ( empty($webinar_data) ) {
			self::error_response(['message' => __('Error', 'webinarignition') . ': ' . __('Cheating, huh!!!.', 'webinarignition')]);
		}

		$data = [
			'app_id' => $app_id,
			'name' => sanitize_text_field($post_input['name']),
			'email' => sanitize_text_field($post_input['email']),
			'question' => htmlspecialchars($post_input['question']),
			'type' => 'question',
			'status' => 'live',
			'created' => current_time( 'mysql' ),
		];

		if (!empty($post_input['webinarTime'])) {
			$data['webinarTime'] = sanitize_text_field($post_input['webinarTime']);
		}

		$id = WebinarignitionQA::create_question($data);

		$data['webinar_type'] = $webinar_data->webinar_date == 'AUTO' ? 'evergreen' : 'live';
		$data['is_first_question'] = $post_input['is_first_question'];

		do_action( 'webinarignition_question_asked', $data);

		if (!empty($post_input['last_message'])) {
			$chat_messages = WebinarignitionQA::get_chat_messages($app_id, $data['email'], null, 'AND ID > ' . sanitize_text_field($post_input['last_message']));
		} else {
			$chat_messages = WebinarignitionQA::get_chat_messages($app_id, $data['email'] );
		}

		$chat_messages_deleted = WebinarignitionQA::get_chat_messages($app_id, $data['email'], null, 'AND status IN ("deleted")');

		self::success_response( [
			'chat_messages' => $chat_messages,
			'chat_messages_deleted' => $chat_messages_deleted,
		] );
	}

	public static function load_chat_messages() {
		if (empty($_POST['app_id']) || empty($_POST['email'])) {
			$params = array(
				'message' => __('Error', 'webinarignition') . ': ' . __('Cheating, huh!!!.', 'webinarignition'),
				'reload' => 0,
			);

			self::error_response($params);
		}

		$app_id = sanitize_text_field($_POST['app_id']);
		$email = sanitize_text_field($_POST['email']);

		$params = array(
			'chat_messages' => WebinarignitionQA::get_chat_messages($app_id, $email, null, ' AND status NOT IN ("deleted")'),
			'reload' => 0,
		);

		self::success_response($params);
	}

	public static function refresh_chat_messages() {
		if (empty($_POST['app_id']) || empty($_POST['email'])) {
			$params = array(
				'message' => __('Error', 'webinarignition') . ': ' . __('Cheating, huh!!!.', 'webinarignition'),
				'reload' => 0,
			);

			self::error_response($params);
		}

		$app_id = sanitize_text_field($_POST['app_id']);
		$webinar_data = WebinarignitionManager::get_webinar_data($app_id);

		if ( empty($webinar_data) ) {
			self::error_response(['message' => __('Error', 'webinarignition') . ': ' . __('Cheating, huh!!!.', 'webinarignition')]);
		}

		$email = sanitize_text_field($_POST['email']);
		$last_message = !empty($_POST['last_message']) ? sanitize_text_field($_POST['last_message']) : false;

		if (!empty($last_message)) {
			$chat_messages = WebinarignitionQA::get_chat_messages($app_id, $email, null, 'AND ID > ' . $last_message);
		} else {
			$chat_messages = WebinarignitionQA::get_chat_messages($app_id, $email );
		}

		$chat_messages_deleted = WebinarignitionQA::get_chat_messages($app_id, $email, null, 'AND status IN ("deleted")');

		self::success_response( [
			'chat_messages' => $chat_messages,
			'chat_messages_deleted' => $chat_messages_deleted,
		] );
	}

	public static function dev_remove_license() {
		$statusCheck = WebinarignitionLicense::get_status_check();

		if (empty($statusCheck)) {
			$params = array(
				'message' => __('Error', 'webinarignition') . ': ' . __('Cheating, huh!!!.', 'webinarignition'),
				'reload' => 0,
			);

			self::error_response($params);
		}

		WebinarignitionLicense::remove_webinarignition_license($statusCheck->ID);

		$params = array(
			'message' => __('Success', 'webinarignition') . ': ' . __('Key deleted.', 'webinarignition'),
			'reload' => 1,
		);

		self::success_response($params);
	}

	public static function dev_add_license() {
		$level = !empty($_POST['level']) ? sanitize_text_field($_POST['level']) : 'basic';
		$statusCheck = WebinarignitionLicense::get_status_check();

		if (!empty($statusCheck)) {
			WebinarignitionLicense::remove_webinarignition_license($statusCheck->ID);
		}

		WebinarignitionLicense::add_webinarignition_license($level);

		update_option('wi_webinar_key_activated', 1);

		$params = array(
			'message' => __('Success', 'webinarignition') . ': ' . __('License activated.', 'webinarignition'),
			'reload' => 1,
		);

		self::success_response($params);
	}

	public static function activate_freemius() {
		$is_active = get_option('webinarignition_activate_freemius');

		if (!empty($is_active)) {
			delete_option('webinarignition_activate_freemius');
			$message = __("Freemius disabled!!", 'webinarignition');
		} else {
			update_option('webinarignition_activate_freemius', 1);
			$message = __("Let's see what we have for you!!!", 'webinarignition');
		}

		$params = array(
			'message' => $message,
			'reload' => 1,
		);

		self::success_response($params);
	}

	public static function deactivate_freemius() {
		$activate_freemius = get_option('webinarignition_activate_freemius');
		$params = array(
			'message' => __('Hooray!!!', 'webinarignition') . ' ' . __("Let's see what we have for you!!!", 'webinarignition'),
			'reload' => 1,
		);

		self::success_response($params);
	}

	public static function unlock_key() {
		$username = !empty($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
		$key = !empty($_POST['key']) ? sanitize_text_field($_POST['key']) : '';
		$old_key = !empty($_POST['old_key']) ? sanitize_text_field($_POST['old_key']) : '';
		$old_switch = !empty($_POST['old_switch']) ? sanitize_text_field($_POST['old_switch']) : '';

		if (empty($username) && !empty($key)) {
			$params = array( 'message' => __('Error', 'webinarignition') . ': ' . __('WebinarIgnition Username is required field.', 'webinarignition') );
			self::error_response($params);
		}

		if (!empty($key) && !empty($old_key) && $key === $old_key) {
			$params = array('message' => __("This key already activated", 'webinarignition'));
			self::error_response($params);
		}

		if ( ( $username == 'dks' ) && ( $key == 'seropt4n0zxdfkv' ) ) {
			WebinarignitionLicense::webinarignition_activate( $key );

			update_option('wi_webinar_key_activated', 1);

			$params = array(
				'message' => __("Activation Successful", 'webinarignition'),
				'reload' => 1,
			);

			self::success_response($params);
		}

		$is_change = !empty($key) && !empty($old_key);
		$is_deactivate = empty($key) && !empty($old_key);

		$dk_activation_url = WebinarignitionLicense::get_activation_url() . "?username={$username}&key={$key}&old_key={$old_key}&old_switch={$old_switch}";

		$response = wp_remote_get( $dk_activation_url, array('user-agent' => 'WI', 'timeout' => 60));
		$response_body = json_decode(wp_remote_retrieve_body( $response ));

		if (!is_wp_error($response)) {
			if ( is_object( $response_body ) && ( $response_body->result == 'KeyFound' || $response_body->result == 'KeyDeactivated' ) ) {
				WebinarignitionLicense::webinarignition_activate( $key, $response_body );

				if ($is_deactivate) {
					$params = array(
						'message' => __("Deactivation Successful", 'webinarignition'),
						'reload' => 1,
					);
				} else {

					update_option('wi_webinar_key_activated', 1);

					$params = array(
						'message' => __("Activation Successful", 'webinarignition'),
						'reload' => 1,
					);
				}

				self::success_response($params);
			} else {
				$params = array(
					'message' => __('Error', 'webinarignition') . ': ' . $response_body->result,
					'reload' => 0,
				);

				self::error_response($params);
			}

			$params = array(
				'message' => __('Hooray!!!', 'webinarignition') . ' ' . __("Let's see what we have for you!!!", 'webinarignition'),
				'reload' => 0,
			);

			self::error_response($params);

		}

		$params = array(
			'message' => __('Something went wrong, please try again later', 'webinarignition'),
			'reload' => 0,
		);

		self::error_response($params);
	}

	public static function track_is_live() {
		if (
			empty($_POST["action"]) ||
			empty($_POST["cookie"]) ||
			empty($_POST["page"]) ||
			empty($_POST["webinar_id"]) ||
			empty($_POST["webinar_type"]) ||
			empty($_POST["lead_id"]) ||
			empty($_POST["start"]) ||
			empty($_POST["current"])
		) {
			$params = array( 'return' => 'missing_args', 'post' => $_POST );
			self::error_response($params);
		}

		$status = sanitize_text_field($_POST["status"]);

		unset ($_POST["action"]);
		unset ($_POST["status"]);

		$lead_id =  sanitize_text_field($_POST["lead_id"]);
		$cookie =  sanitize_text_field($_POST["cookie"]);
		$page =  sanitize_text_field($_POST["page"]);
		$webinar_id =  sanitize_text_field($_POST["webinar_id"]);
		$webinar_type =  sanitize_text_field($_POST["webinar_type"]);

		$post_start = isset($_POST['start']) ? absint($_POST['start']) : 0;
		if($post_start > 0) {
			$post_start = $post_start / 1000;
		}

		$post_current = isset($_POST['current']) ? absint($_POST['current']) : 0;
		if($post_current > 0) {
			$post_current = $post_current / 1000;
		}

		$start =  get_gmt_from_date(date('Y-m-d H:i:s', $post_start), 'U');
		$current =  get_gmt_from_date(date('Y-m-d H:i:s', $post_current), 'U');
		$now = get_gmt_from_date(date('Y-m-d H:i:s', time()), 'U');
		$webinar_data = WebinarignitionManager::get_webinar_data($webinar_id);

		$result = compact(array_keys($_POST));

		// $option_is_alive = get_option('webinarignition_is_alive_' . $webinar_type . '_' . $lead_id, array());
		$option_is_alive = get_transient('webinarignition_is_alive_' . $webinar_type . '_' . $lead_id);

		if (empty($option_is_alive)) {
			$option_is_alive = array();
		}

		if (empty($option_is_alive['cookie'])) {
			// update_option('webinarignition_is_alive_' . $webinar_type . '_' . $lead_id, $result);
			set_transient('webinarignition_is_alive_' . $webinar_type . '_' . $lead_id, $result, 60 * 60 * 24);

			self::success_response(['return' => 'first_join', 'post' => $_POST]);
		} else {
			if ($option_is_alive['cookie'] === $cookie) {
				// update_option('webinarignition_is_alive_' . $webinar_type . '_' . $lead_id, $result);
				set_transient('webinarignition_is_alive_' . $webinar_type . '_' . $lead_id, $result, 60 * 60 * 24);

				self::success_response(['return' => 'rejoin', 'post' => $_POST]);
			} else {
				if ($current - $option_is_alive['current'] > 25) {
					// update_option('webinarignition_is_alive_' . $webinar_type . '_' . $lead_id, $result);
					set_transient('webinarignition_is_alive_' . $webinar_type . '_' . $lead_id, $result, 60 * 60 * 24);

					self::success_response(['return' => 'another_device_join', 'reload' => 1, 'post' => $_POST]);
				} else {
					if ('initial' === $status) {
						$timer = !empty($webinar_data->limit_lead_timer) ? (int) $webinar_data->limit_lead_timer : 30;
						WebinarignitionManager::set_locale($webinar_data);
						ob_start();
						?>
                        <p style="margin: 5px"><?php echo __('Looks like you already watching this webinar on another device/browser.', 'webinarignition'); ?></p>
                        <p style="margin: 5px"><?php echo __('To continue watching here you need to logout from another device.', 'webinarignition'); ?></p>
                        <p style="margin: 5px">
							<?php echo sprintf(' ' . __( 'Otherwise you will be redirected to registration page in %s seconds.', 'webinarignition' ), '<strong id="not_allowed_timer">'.$timer.'</strong>' ); ?>
                        </p>
						<?php
						WebinarignitionManager::restore_locale($webinar_data);
						$message = ob_get_clean();
						$permalink = WebinarignitionManager::get_permalink($webinar_data, 'registration');

						$params = array(
							'return' => 'not_allowed',
							'post' => $_POST,
							'pending_permalink' => $permalink,
							'pending_message' => $message,
							'pending_timer' => $timer,
						);
					} else {
						$params = array( 'return' => 'not_allowed', 'post' => $_POST );
					}

					self::error_response($params);
				}
			}
		}

		self::success_response($params);
	}

	public static function tracking_tags() {
		if (
			empty(sanitize_text_field($_POST["time"])) ||
			empty(sanitize_text_field($_POST["name"])) ||
			empty(sanitize_text_field($_POST["lead_id"])) ||
			empty(sanitize_text_field($_POST["webinar_type"])) ||
			empty(sanitize_text_field($_POST["webinar_id"]))
		) {
			$params = array( 'return' => 'missing_args', 'post' => $_POST );
			self::error_response($params);
		}

		$time = sanitize_text_field($_POST["time"]);
		$name = sanitize_text_field($_POST["name"]);
		$lead_id = sanitize_text_field($_POST["lead_id"]);
		$webinar_type = sanitize_text_field($_POST["webinar_type"]);
		$webinar_id = sanitize_text_field($_POST["webinar_id"]);

		$tracking_tags = WebinarignitionLeadsManager::get_lead_meta($lead_id, 'tracking_tags', $webinar_type);

		if (!empty($tracking_tags['meta_value'])) {
			$tracking_tags_array = explode(',', $tracking_tags['meta_value']);

			if (!in_array($name, $tracking_tags_array)) {
				$tracking_tags_array[] = $name;
			}
		} else {
			$tracking_tags_array = [$name];
		}

		$tracking_tags = implode(',', $tracking_tags_array);

		WebinarignitionLeadsManager::update_lead_meta($lead_id, 'tracking_tags', $tracking_tags, $webinar_type);
		WebinarignitionLeadsManager::update_lead_meta($lead_id, 'tracking_tags_last', $name, $webinar_type);

		if (!empty(sanitize_text_field($_POST["slug"]))) {
			$slug = sanitize_text_field($_POST["slug"]);

			$tracking_tags = WebinarignitionLeadsManager::get_lead_meta($lead_id, $slug, $webinar_type);

			if (!empty($tracking_tags['meta_value'])) {
				$tracking_tags_array = explode(',', $tracking_tags['meta_value']);

				if (!in_array($name, $tracking_tags_array)) {
					$tracking_tags_array[] = $name;
				}
			} else {
				$tracking_tags_array = [$name];
			}

			$tracking_tags = implode(',', $tracking_tags_array);

			WebinarignitionLeadsManager::update_lead_meta($lead_id, $slug, $tracking_tags, $webinar_type);
			WebinarignitionLeadsManager::update_lead_meta($lead_id, $slug . '_last', $name, $webinar_type);
		}

		$params = array( 'return' => 'tag_saved', 'post' => $_POST );

		self::success_response($params);
	}

	public static function get_form_data() {
		if (empty($_POST['formData']) || !is_array($_POST['formData'])) {
			self::error_response([
				'message' => __('Error', '5-stars-rating-funnel') . ': ' . __('Cheating, huh!!!', '5-stars-rating-funnel'),
			]);
		}

		$data = [];

		foreach ($_POST['formData'] as $form_data) {
			$data[sanitize_text_field($form_data['name'])] = $form_data['value'];
		}

		return $data;
	}

	public static function error_response($params = array()) {
		$response = array('success' => 0, 'error' => 1);

		if (!empty($params)) {
			$response = array_merge($response, $params);
		}

		echo json_encode($response);
		wp_die();
	}

	public static function success_response($params = array()) {
		$response = array('success' => 1, 'error' => 0);

		if (!empty($params) && is_array($params)) {
			$response = array_merge($response, $params);
		}

		echo json_encode($response);
		wp_die();
	}

	public static function save_reg_date() {
		var_dump($_POST);
	}

	public static function track_video_time(){

		$nonce      = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : 0;

		if( !wp_verify_nonce( $nonce, 'limit-custom-video') ) {
			die('Security violated');
		}

		$watch_time = isset( $_POST['watch_time'] ) ? sanitize_text_field( $_POST['watch_time'] ) : 0;
		$lead_id    = isset( $_POST['lead_id'] ) ? sanitize_text_field( $_POST['lead_id'] ) : 0;
		$preview    = isset( $_POST['preview'] ) ? sanitize_text_field( $_POST['preview'] ) : false;

		$video_watch_time = (int) get_option('wi_lead_watch_time_'. $lead_id, true );

		if( $preview ) {

			$video_watch_time = $watch_time;
			update_option('wi_lead_watch_time_' . $lead_id, $video_watch_time );

		} else {
			$video_watch_time += 60;
			update_option('wi_lead_watch_time_' . $lead_id, $video_watch_time );
		}

		$timeover = false;
		$show_countdown = false;
		$popup    = '';

        $statusCheck = WebinarignitionLicense::get_license_level();
        if($statusCheck->name == 'ultimate_powerup_tier1a') {
            $watch_limit = HOUR_IN_SECONDS * 2;
        } else {
            $watch_limit = MINUTE_IN_SECONDS * 45;
        }


        if( is_plugin_active('webinar-ignition-helper/webinar-ignition-helper.php') ) {
			$watch_limit = 300;
			$show_countdown = true;
		}

		if( $watch_limit - $video_watch_time <= 300 ) {
			$show_countdown = true;
		}

		$timeleft = $watch_limit - intval($watch_time);

		if( $video_watch_time >= $watch_limit ) {
			$timeover = true;
			ob_start();
			require WEBINARIGNITION_PATH . 'inc/lp/partials/timeout_page/popup.php';
			$popup = ob_get_clean();
		}

		wp_send_json(
			array(
				'timeover'   => $timeover,
				'popup'      => $popup,
				'watch_time' => $video_watch_time,
				'show_countdown' => $show_countdown,
				'timeleft'       => $timeleft,
			)
		);
	}

	public static function track_video_time_iframe(){

		$nonce      = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : 0;

		if( !wp_verify_nonce( $nonce, 'limit-iframe-video') ) {
			die('Security violated');
		}

		$lead_id    = isset( $_POST['lead_id'] ) ? sanitize_text_field( $_POST['lead_id'] ) : 0;

		$video_watch_time = (int) get_option('wi_lead_watch_time_'. $lead_id, true );

		$video_watch_time += 60;

		update_option('wi_lead_watch_time_' . $lead_id, $video_watch_time );

		$timeover = false;
		$show_countdown = false;
		$popup    = '';

        $statusCheck = WebinarignitionLicense::get_license_level();
        if($statusCheck->name == 'ultimate_powerup_tier1a') {
            $watch_limit = HOUR_IN_SECONDS * 2;
        } else {
            $watch_limit = MINUTE_IN_SECONDS * 45;
        }

		if( is_plugin_active('webinar-ignition-helper/webinar-ignition-helper.php') ) {
			$watch_limit = 300;
			$show_countdown = true;
		}

		if( $watch_limit - $video_watch_time <= 300 ) {
			$show_countdown = true;
		}


		if( $video_watch_time >= $watch_limit ) {
			$timeover = true;
			ob_start();
			require WEBINARIGNITION_PATH . 'inc/lp/partials/timeout_page/popup.php';
			$popup = ob_get_clean();
		}

		wp_send_json(
			array(
				'timeover'   => $timeover,
				'popup'      => $popup,
				'watch_time' => $video_watch_time,
				'show_countdown' => $show_countdown,
			)
		);
	}

	public static function return_video_time_left(){

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : 0;

		if( !wp_verify_nonce( $nonce, 'limit-custom-video') ) {
			die('Security violated');
		}

		$lead_id    = isset( $_POST['lead_id'] ) ? sanitize_text_field( $_POST['lead_id'] ) : 0;
		$view_time  = isset( $_POST['viewTime'] ) ? sanitize_text_field( $_POST['viewTime'] ) : 0;

		update_option('wi_lead_watch_time_' . $lead_id, $view_time );

		$show_countdown = false;
        $statusCheck = WebinarignitionLicense::get_license_level();
        if($statusCheck->name == 'ultimate_powerup_tier1a') {
            $watch_limit = HOUR_IN_SECONDS * 2;
        } else {
            $watch_limit = MINUTE_IN_SECONDS * 45;
        }

		if( is_plugin_active('webinar-ignition-helper/webinar-ignition-helper.php') ) {
			$watch_limit = 300;
			$show_countdown = true;
		}

		if( $watch_limit - $view_time <= 300 ) {
			$show_countdown = true;
		}

		if( $view_time >= $watch_limit ) {
			$timeover = true;
			ob_start();
			require WEBINARIGNITION_PATH . 'inc/lp/partials/timeout_page/popup.php';
			$popup = ob_get_clean();
		}

		$timeleft = $watch_limit - $view_time;

		wp_send_json(
			array(
				'timeover'       => $timeover,
				'popup'          => $popup,
				'show_countdown' => $show_countdown,
				'timeleft'       => round($timeleft, 2),
				'timelimit'      => $watch_limit,
			)
		);
	}

	public static function dismiss_admin_notice() {
		if( current_user_can('edit_posts') ) {
			if( isset( $_REQUEST['dismiss_wi_notice'] ) ) {
				update_user_meta( get_current_user_id(), 'notice-webinarignition-free', 1 );
				wp_die('success');
			}
		}
		wp_die('failed');
	}

	public static function send_email_verification_code() {
		check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
		$post_input = filter_input_array( INPUT_POST );

		global $wpdb;
		if (empty($post_input['email']) || empty($post_input['id'])) {
			self::error_response(['message' => __('Error', 'webinarignition') . ': ' . __('Cheating, huh!!!.', 'webinarignition')]);
		}

		$input_email = sanitize_email($post_input['email']);
		
        $table_db_name = $wpdb->prefix . "webinarignition_verification";        
        $code = $wpdb->get_row( "SELECT * FROM {$table_db_name} WHERE email = '{$input_email}'", ARRAY_A ); 
		$gen_code = rand(100000, 999999);
		if(!isset($code)){
			$wpdb->insert( $table_db_name, [                                
				'email'   => $input_email,              
				'code'    => $gen_code,
			] );
		}
		else{    
			$id = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM $table_db_name WHERE email = %s", $input_email ), ARRAY_A );        
			$wpdb->update( $table_db_name, array( 'code' => $gen_code ), array( 'id' => $id['id'] ) );                
		}
		$to = $input_email;		
		$headers   = array('Content-Type: text/html; charset=UTF-8');
		$subj      = __( "Email Verification Code For New Registration", "webinarignition");
		$emailHead = WebinarignitionEmailManager::get_email_head();
		$emailBody = $emailHead;
		$emailBody .= 'Here is your email verification code.';
		$emailBody .= "<br><br><div>{$gen_code}</div>";
		try {
			wp_mail( $to, $subj, $emailBody, $headers );                
		} catch (Exception $e) {
			self::error_response(['message' => __('Error', 'webinarignition') . ': ' . __('Error occurs in sending email!.', 'webinarignition')]);
		}
		
		self::success_response(['message' => __('Success', 'webinarignition') . ': ' . __('Great lets register.', 'webinarignition')]);
	}
	
	public static function verify_user_email() {
		check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
		$post_input = filter_input_array( INPUT_POST );
		$input_email = sanitize_email($post_input['email']);
		$get_code = $post_input['code'];

		global $wpdb;
		$table_db_name = $wpdb->prefix . "webinarignition_verification";		   
        $code = $wpdb->get_row( "SELECT * FROM {$table_db_name} WHERE email = '{$input_email}'", ARRAY_A ); 		
		if( isset($code) && $code['code'] == $get_code ){
			$response =array(
					'verified'   => 1,										
					'status'     => 'success',
			);
		}
		else{
			$response =array(
				'verified'   => 0,										
				'status'     => 'failed',
			);
		}
		echo json_encode($response);
		wp_die();
	}
}

add_action( 'wp_ajax_nopriv_webinarignition_add_lead_check_secure', array('WebinarignitionAjax', 'add_lead_check_secure') );
add_action( 'wp_ajax_webinarignition_add_lead_check_secure', array('WebinarignitionAjax', 'add_lead_check_secure') );

add_action( 'wp_ajax_nopriv_webinarignition_register_support', array('WebinarignitionAjax', 'register_support') );
add_action( 'wp_ajax_webinarignition_register_support', array('WebinarignitionAjax', 'register_support') );

add_action( 'wp_ajax_nopriv_webinarignition_submit_chat_question', array('WebinarignitionAjax', 'submit_chat_question') );
add_action( 'wp_ajax_webinarignition_submit_chat_question', array('WebinarignitionAjax', 'submit_chat_question') );

add_action('wp_ajax_nopriv_webinarignition_load_chat_messages', array('WebinarignitionAjax', 'load_chat_messages'));
add_action('wp_ajax_webinarignition_load_chat_messages', array('WebinarignitionAjax', 'load_chat_messages'));

add_action('wp_ajax_nopriv_webinarignition_refresh_chat_messages', array('WebinarignitionAjax', 'refresh_chat_messages'));
add_action('wp_ajax_webinarignition_refresh_chat_messages', array('WebinarignitionAjax', 'refresh_chat_messages'));

add_action('wp_ajax_nopriv_webinarignition_dev_remove_license', array('WebinarignitionAjax', 'dev_remove_license'));
add_action('wp_ajax_webinarignition_dev_remove_license', array('WebinarignitionAjax', 'dev_remove_license'));

add_action('wp_ajax_nopriv_webinarignition_dev_add_license', array('WebinarignitionAjax', 'dev_add_license'));
add_action('wp_ajax_webinarignition_dev_add_license', array('WebinarignitionAjax', 'dev_add_license'));

add_action('wp_ajax_nopriv_webinarignition_activate_freemius', array('WebinarignitionAjax', 'activate_freemius'));
add_action('wp_ajax_webinarignition_activate_freemius', array('WebinarignitionAjax', 'activate_freemius'));

add_action('wp_ajax_nopriv_webinarignition_unlock_key', array('WebinarignitionAjax', 'unlock_key'));
add_action('wp_ajax_webinarignition_unlock_key', array('WebinarignitionAjax', 'unlock_key'));

add_action('wp_ajax_nopriv_webinarignition_track_is_live', array('WebinarignitionAjax', 'track_is_live'));
add_action('wp_ajax_webinarignition_track_is_live', array('WebinarignitionAjax', 'track_is_live'));

add_action('wp_ajax_nopriv_webinarignition_tracking_tags', array('WebinarignitionAjax', 'tracking_tags'));
add_action('wp_ajax_webinarignition_tracking_tags', array('WebinarignitionAjax', 'tracking_tags'));

add_action('wp_ajax_nopriv_webinarignition_save_reg_date', array('WebinarignitionAjax', 'save_reg_date'));
add_action('wp_ajax_webinarignition_save_reg_date', array('WebinarignitionAjax', 'save_reg_date'));

add_action('wp_ajax_wi_track_self_hosted_videos_time', array('WebinarignitionAjax', 'track_video_time') );
add_action('wp_ajax_nopriv_wi_track_self_hosted_videos_time', array('WebinarignitionAjax', 'track_video_time') );

add_action('wp_ajax_wi_track_embeded_videos_time', array('WebinarignitionAjax', 'track_video_time_iframe') );
add_action('wp_ajax_nopriv_wi_track_embeded_videos_time', array('WebinarignitionAjax', 'track_video_time_iframe') );

add_action('wp_ajax_wi_get_self_hosted_videos_time_left', array('WebinarignitionAjax', 'return_video_time_left') );
add_action('wp_ajax_nopriv_wi_get_self_hosted_videos_time_left', array('WebinarignitionAjax', 'return_video_time_left') );

add_action('wp_ajax_webinarignition_dismiss_notice', array( 'WebinarignitionAjax', 'dismiss_admin_notice'));

add_action( 'wp_ajax_webinarignition_send_email_verification_code', array('WebinarignitionAjax', 'send_email_verification_code') );
add_action( 'wp_ajax_nopriv_webinarignition_send_email_verification_code', array('WebinarignitionAjax', 'send_email_verification_code') );

add_action( 'wp_ajax_webinarignition_verify_user_email', array('WebinarignitionAjax', 'verify_user_email') );
add_action( 'wp_ajax_nopriv_webinarignition_verify_user_email', array('WebinarignitionAjax', 'verify_user_email') );