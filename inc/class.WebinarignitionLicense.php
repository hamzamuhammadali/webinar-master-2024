<?php

/**
 * Class WebinarignitionLicense
 * ($username == 'dks') && ($license_key == 'seropt4n0zxdfkv')
 */

class WebinarignitionLicense {

	private static $license_check_timeout = 6 * 60 * 60;
	private static $activation_url = 'https://webinarignition.com/wp-content/plugins/wikeygen/inc/response.php';
	private static $free_trial_limit = 500;

	private static $en_first_key  = '4kLBvWTGPK+B2KLn5+kCp3WPjhvGUGmHkbjh/ZY8k/0=';
	private static $en_second_key = 'Kv7crTCwa8oSosIGnXDeTchHyJZdxT7jIgbxpcv2zYQFE6g8sxW4zA+ViFNRc3Oadv00vzA+P5LjsRLiPwXgIQ==';

	public static function get_activation_url() {
		return self::$activation_url;
	}

	/**
	 * Check whether branding is enable and valid to avail 500 free registrations.
	 *
	 * @return bool true or false based on valid/invalid
	 */
	public static function is_branding_enable_and_valid() {

		$statusesCheck = self::get_license_level();

		if( !$statusesCheck->is_registered ) {
			return false;
		}

		$branding_enable = get_option('webinarignition_show_footer_branding', false);

		if (!$branding_enable) {
			return false;
		}

		$fg_color = get_option('webinarignition_footer_text_color', false);
		$bg_color = get_option('webinarignition_branding_background_color', false);

		if ($bg_color == $fg_color) {
			return false;
		}

		$branding_text = get_option('webinarignition_branding_copy', '');
		$required_text = 'webinar powered by webinarignition';

		if (!$branding_text || 0 > strpos($required_text, strtolower($branding_text))) {
			return false;
		}

		return true;
	}

	public static function get_status_check($type = 'OBJECT') {
		global $wpdb;

		$table_name   = $wpdb->prefix . "webinarignition_wi";
		$table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'");

		if (!$table_exists) {
			return false;
		}

		$table_db_name     = $wpdb->prefix . "webinarignition_wi";
		$statusCheck       = $wpdb->get_row("SELECT * FROM $table_db_name LIMIT 1", $type);
		$activate_freemius = get_option('webinarignition_activate_freemius');

		if (!empty($statusCheck)) {
			if (!empty($activate_freemius)) {
				$statusCheck->activate_freemius = true;
			}
		} else {
			if (!empty($activate_freemius)) {
				delete_option('webinarignition_activate_freemius');
			}
		}

		return $statusCheck;
	}

	public static function get_license_level() {
		$is_dev = self::is_dev();

		$license = array(
			'switch' => 'free',
			'slug' => 'free',
			'licensor' => false,
			'is_free' => true,
			'is_premium' => false,
			'is_dev' => $is_dev,
			'is_registered' => false,
			'title' => __('Free', 'webinarignition'),
			'member_area' => 'https://webinarignition.com/?p=4870&ppt=da853e6d074bedd3d8eb0f8a6d11674e',
		);

		global $wpdb;
		$activate_freemius = get_option('webinarignition_activate_freemius');

		$table_db_name = $wpdb->prefix . "webinarignition_wi";
		$statusesCheck = $wpdb->get_results("SELECT * FROM {$table_db_name} ORDER BY ID DESC", ARRAY_A);
		$current_level = 'free';

		if (!empty($statusesCheck)) {
			$statusCheck = $statusesCheck[0];
			unset($statusesCheck[0]);

			if (!empty($statusesCheck)) {
				foreach ($statusesCheck as $i => $item) {
					self::remove_webinarignition_license($item['ID']);
					unset($statusesCheck[$i]);
				}
			}

			$switch  = !empty($statusCheck['switch']) ? $statusCheck['switch'] : 'free';
			$keyused = !empty($statusCheck['keyused']) ? $statusCheck['keyused'] : '';

			if (
				strpos($statusCheck['switch'], 'WebinarExpress - Full Access') !== false ||
				strpos($statusCheck['switch'], 'WebinarIgnition - Enterprise Level   Premium Content') !== false ||
				strpos($statusCheck['switch'], 'WebinarIgnition - Enterprise Level (Personal Unlimited)') !== false
			) {
				$switch = 'enterprise';
			} elseif (
				strpos($statusCheck['switch'], 'WebinarIgnition - Pro Level (3 Site Install)') !== false ||
				(int) $statusCheck['switch'] === 1
			) {
				$switch = 'pro';
			} else {
				$switch_array = explode(' - ', $switch);

				if (count($switch_array) > 1) {
					$switch = $switch_array[0];
				}
			}

			$license = array(
				'switch' => self::get_switch_by_slug($switch),
				'slug' => $switch,
				'is_fs' => false,
				'is_free' => false,
				'is_registered' => false,
				'is_premium' => true,
				'keyused' => $keyused,
				'title' => self::get_title_by_slug($switch),
				'is_dev' => $is_dev,
				'member_area' => 'https://webinarignition.com/?p=4870&ppt=da853e6d074bedd3d8eb0f8a6d11674e',
				'statusesCheck' => $statusesCheck,
			);

			if (empty($activate_freemius)) {
				$license['show_freemius_btn'] = true;
			}

			$current_level = $license['switch'];

			if (self::is_dev()) {
				$license['show_dis_license'] = true;
			}
		}

		if (function_exists('webinarignition_fs')) {
			// Check if registered
			$is_registered = webinarignition_fs()->is_registered() && webinarignition_fs()->is_tracking_allowed();
			$license['is_registered'] = $is_registered;

			if (!$is_registered) {
				$reconnect_url = webinarignition_fs()->get_activation_url(array(
					'nonce'     => wp_create_nonce(webinarignition_fs()->get_unique_affix() . '_reconnect'),
					'fs_action' => (webinarignition_fs()->get_unique_affix() . '_reconnect'),
				));
			} else {
				$account_url = webinarignition_fs()->get_account_url();
			}

			$upgrade_url = webinarignition_fs()->get_upgrade_url();
			$trial_url   = webinarignition_fs()->get_trial_url();

			$plan_name       = webinarignition_fs()->get_plan_name();
			$is_free_plan    = webinarignition_fs()->is_free_plan();
			$is_paying       = webinarignition_fs()->is_paying();
			$is_trial        = webinarignition_fs()->is_trial();
			$is_paid_trial   = webinarignition_fs()->is_paid_trial();
			$freemius_switch = self::get_switch_by_slug($plan_name, $is_trial);

			/**
			 * Current user already bought some plan
			 * we need to make sure that WI.com license is cleared
			 */
			if ('free' !== $freemius_switch) {
				$license = array(
					'switch' => $freemius_switch,
					'is_fs' => true,
					'is_free' => $is_free_plan,
					'is_paying' => $is_paying,
					'title' => self::get_title_by_slug($plan_name, $is_trial), // Human readable
					'name' => $plan_name, // slug
					'is_dev' => $is_dev,
					'is_trial' => $is_trial,
					'is_paid_trial' => $is_paid_trial,
					'is_registered' => $is_registered,
				);

				if ($is_dev && 'free' === $license['switch']) $license['show_enab_license'] = true;
				if (!empty($reconnect_url)) $license['reconnect_url'] = $reconnect_url;
				if (!empty($account_url)) $license['account_url'] = $account_url;
				if (!empty($upgrade_url)) $license['upgrade_url'] = $upgrade_url;
				if (!empty($trial_url) && !$is_trial) $license['trial_url'] = $trial_url;

				// Clean
				if ($activate_freemius) delete_option('webinarignition_activate_freemius');
				if (!empty($statusCheck)) self::remove_webinarignition_license();
			} else { // no plan for this user so we use WI.com license
				if ($is_dev && 'free' === $license['switch']) $license['show_enab_license'] = true;
				if (!empty($upgrade_url)) $license['upgrade_url'] = $upgrade_url;
				if (!empty($trial_url) && !$is_paying && !$is_trial) $license['trial_url'] = $trial_url;
				if (!empty($is_trial)) {
					$license['is_trial'] = $upgrade_url;
					$license['title'] = self::get_title_by_slug('ultimate_powerup', $is_trial);
				}
				if (!empty($reconnect_url)) $license['reconnect_url'] = $reconnect_url;
				if (!empty($account_url)) $license['account_url'] = $account_url;
			}
		}

		$object = new stdClass();
		foreach ($license as $key => $value) {
			$object->$key = $value;
		}

        if( !isset($object->name) ) {
            $object->name = null;
        }

        return $object;
	}

	public static function webinarignition_activate($license_key, $resp = null) {
		global $wpdb;
		$table_db_name = $wpdb->prefix . "webinarignition_wi";

		if (empty($license_key)) {
			self::remove_webinarignition_license();
		} else {
			if (!empty($resp->level)) {
				$wpdb->insert($table_db_name, array('switch' => $resp->level, 'keyused' => $license_key));
			} else {
				$wpdb->insert($table_db_name, array('switch' => "pro", 'keyused' => $license_key));
			}
		}
	}

	public static function remove_webinarignition_license($ID = null) {
		global $wpdb;
		$table_db_name = $wpdb->prefix . "webinarignition_wi";

		if (empty($ID)) {
			$wpdb->query("DELETE FROM {$table_db_name}");
		} else {
			$wpdb->query("DELETE FROM {$table_db_name} WHERE ID = {$ID}");
		}

		return true;
	}

	public static function add_webinarignition_license($level = 'basic') {
		global $wpdb;
		$table_db_name = $wpdb->prefix . "webinarignition_wi";
		$wpdb->insert($table_db_name, array('switch' => $level, 'keyused' => 'seropt4n0zxdfkv'));

		return true;
	}

	public static function is_webinar_available($webinar_id, $webinar_data = false) {

		$statusCheck = WebinarignitionLicense::get_license_level();

		if( 'enterprise_powerup' !== $statusCheck->switch && !in_array($statusCheck->name, ['ultimate_powerup_tier2a','ultimate_powerup_tier3a']) ) {
			if( self::is_limit_reached() ) {
				return array(
					'available' => false
				);
			}
		}

		return array(
			'available' => true
		);
	}

	#--------------------------------------------------------------------------------
	#region Free Limitation
	#--------------------------------------------------------------------------------

	public static function free_limitation() {
		$is_ajax = defined('DOING_AJAX') && DOING_AJAX;

		if (!$is_ajax) {

			$is_limit_set = get_transient('webinarignition_limit_set');

			if (!$is_limit_set || true) {

				$limit_set = self::set_limitation();

				if ($limit_set) {
					set_transient('webinarignition_limit_set', 1, self::$license_check_timeout);
				}
			}

			self::reset_limit_counter();
		}
	}

	/**
	 * Limit counter for free/paid registrations.
	 *
	 * @return int Number of free paid registrations.
	 */
	public static function get_limit_counter() {

		global $wpdb;

		$user_count = get_option('webinarignition_limit_counter');

		if (!empty($user_count) && !is_numeric($user_count)) {
			$user_count = self::decrypt_data($user_count);
		}

		if (empty($user_count) && !empty(get_option('webinarignition_free_limit_count'))) {
			$user_count = get_option('webinarignition_free_limit_count');
			if (!empty($user_count) && !is_numeric($user_count)) {
				$user_count = self::decrypt_data($user_count);
			}
			update_option('webinarignition_limit_counter', self::encrypt_data($user_count) );
			delete_option('webinarignition_free_limit_count');
		}

		$user_count = (int) $user_count;
		return $user_count;
	}

	public static function is_limit_reached() {

		$statusCheck = WebinarignitionLicense::get_license_level();

		if( 'enterprise_powerup' == $statusCheck->switch ) {
			return true;
		}

		$is_trial = !empty($statusCheck->is_trial);

		$user_count             = self::get_limit_counter();
		$limit_settings_decoded = self::get_limitation_settings();
		$free_limit             = $is_trial ? self::$free_trial_limit : (int) $limit_settings_decoded['limit_users'];

		if ($free_limit > $user_count) {
			return false;
		}

		return true;
	}

	public static function encrypt_data($data) {

		$first_key  = base64_decode(self::$en_first_key);
		$second_key = base64_decode(self::$en_second_key);

		$method    = "AES-256-CBC";
		$iv_length = openssl_cipher_iv_length($method);
		$iv        = openssl_random_pseudo_bytes($iv_length);

		$first_encrypted  = openssl_encrypt($data, $method, $first_key, OPENSSL_RAW_DATA, $iv);
		$second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);

		$output = base64_encode($iv . $second_encrypted . $first_encrypted);

		return $output;
	}

	public static function decrypt_data($data) {

		if (empty($data) || is_numeric($data)) {
			return $data;
		}

		$first_key  = base64_decode(self::$en_first_key);
		$second_key = base64_decode(self::$en_second_key);
		$mix        = base64_decode($data);

		$method    = "AES-256-CBC";
		$iv_length = openssl_cipher_iv_length($method);

		$iv               = substr($mix, 0, $iv_length);
		$second_encrypted = substr($mix, $iv_length, 64);
		$first_encrypted  = substr($mix, $iv_length + 64);

		$data                 = openssl_decrypt($first_encrypted, $method, $first_key, OPENSSL_RAW_DATA, $iv);
		$second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);

		if (hash_equals($second_encrypted, $second_encrypted_new)) {
			return $data;
		}

		return false;
	}

	public static function get_limitation_settings() {

		global $wpdb;

		$limit_settings = get_option('wi_backbone');

		if (empty($limit_settings)) {
			self::set_limitation();
			$limit_settings = get_option('wi_backbone');
		}

		$limit_settings_decoded = maybe_unserialize($limit_settings);

		if (empty($limit_settings_decoded['limit_users']) || empty($limit_settings_decoded['limit_message']) || empty($limit_settings_decoded['limit_days'])) {
			self::set_limitation();

			$limit_settings         = get_option('wi_backbone');
			$limit_settings_decoded = maybe_unserialize($limit_settings);
		}

		if (!empty($limit_settings_decoded['limit_users']) && !is_numeric($limit_settings_decoded['limit_users'])) {
			$limit_settings_decoded['limit_users'] = self::decrypt_data($limit_settings_decoded['limit_users']);
		}

		return $limit_settings_decoded;
	}

	public static function increment_limit_counter() {

		$statusCheck = WebinarignitionLicense::get_license_level();

		if( 'ultimate_powerup' == $statusCheck->switch ) {
			return;
		}

		global $wpdb;

		$user_count = get_option('webinarignition_limit_counter');

		if (!empty($user_count) && !is_numeric($user_count)) {
			$user_count = self::decrypt_data($user_count);
		}

		if (empty($user_count) && !empty(get_option('webinarignition_free_limit_count'))) {

			$user_count = get_option('webinarignition_free_limit_count');

			if (!empty($user_count) && !is_numeric($user_count)) {
				$user_count = self::decrypt_data($user_count);
			}

			delete_option('webinarignition_free_limit_count');
		}

		$limit_count_timeout = get_option('webinarignition_limit_timeout');

		$limit_settings_decoded = self::get_limitation_settings();
		$limit_days = $limit_settings_decoded['limit_days'];

		if (empty($user_count) || empty($limit_count_timeout)) {

			$timeout      = time() + 60 * 60 * 24 * $limit_days;
			update_option('webinarignition_limit_timeout', $timeout);
		}

		$user_count = intval( $user_count ) + 1;

		update_option('webinarignition_limit_counter', self::encrypt_data($user_count));
		update_option(strrev('webinarignition_limit_counter'), self::encrypt_data($user_count) );

		return true;
	}

	public static function reset_limit_counter() {

		$limit_count_timeout = (int) get_option('webinarignition_limit_timeout');

		$statusesCheck = WebinarignitionLicense::get_license_level();

		if( isset( $statusesCheck->is_trial ) && $statusesCheck->is_trial ) {
			if( !get_option('webinarignition_trial_count_reset') ) {
				self::remove_limit_counter();
			}
		}

		if (!$limit_count_timeout) return true;
		if (time() > (int) $limit_count_timeout) self::remove_limit_counter();

		return true;
	}

	public static function remove_limit_counter() {

		delete_option('webinarignition_limit_counter');
		delete_option(strrev('webinarignition_limit_counter'));
		delete_option('webinarignition_limit_timeout');

		$statusesCheck = WebinarignitionLicense::get_license_level();

		if( isset( $statusesCheck->is_trial ) && $statusesCheck->is_trial ) {
			if( !get_option('webinarignition_trial_count_reset') ) {
				update_option('webinarignition_trial_count_reset', true );
			}
		} else {
			delete_option('webinarignition_trial_count_reset');
		}

		$admin_users = get_users( array( 'role__in' => array( 'super_admin', 'administrator' ) ) );

		foreach( $admin_users as $user ) {
			delete_user_meta( $user->ID, 'notice-webinarignition-free');
		}

		return true;
	}

	public static function set_limitation() {

		$free_limitation     = get_option('wi_backbone');
		$existing_limitation = get_option('webinarignition_free_limitation');
		$statusCheck         = WebinarignitionLicense::get_license_level();

		if ('enterprise_powerup' == $statusCheck->switch || in_array($statusCheck->name, ['ultimate_powerup_tier2a','ultimate_powerup_tier3a'])) {
			delete_option('wi_backbone');
		}

		if (false) {
			$response = wp_remote_get("https://www.klick-tipp.com/api/split/1dg2z7uaz1fzkz58a6?ip=" . $_SERVER["REMOTE_ADDR"] . "&cookie=" . (isset($_COOKIE["KTSTC50Z59362"]) ? $_COOKIE["KTSTC50Z59362"] : -1) . "");

			if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
				$settings_array = explode(' ', '50 10 30');
			} else {
				$body = sanitize_text_field(wp_remote_retrieve_body($response));

				$settings_array = explode(' ', $body);
			}
		} else {
			$settings_array = explode(' ', '50 10 30');
		}

		if( $statusCheck->is_registered ) {

			if (isset($statusCheck->is_trial) && $statusCheck->is_trial) {
				$settings_array = explode(' ', '250 10 30');
			} elseif ( self::is_branding_enable_and_valid()) {
				$settings_array = explode(' ', '100 10 30');
			} elseif ('free' == $statusCheck->switch) {

				if (!empty($statusCheck->account_url)) {
					$settings_array = explode(' ', '25 10 30');
				} else {
					$settings_array = explode(' ', '5 10 30');
				}
			} elseif ('basic' == $statusCheck->switch) {
				$settings_array = explode(' ', '100 10 30');
			} elseif ('pro' == $statusCheck->switch) {
				$settings_array = explode(' ', '200 10 30');
                if ('ultimate_powerup_tier1a' == $statusCheck->name) {
                    $settings_array = explode(' ', '250 10 30');
                }
			}

		} else {
			$settings_array = explode(' ', '5 10 30');
		}

		$free_limitation = array(
			'limit_users' => self::encrypt_data($settings_array[0]),
			'limit_message' => $settings_array[1],
			'limit_days' => $settings_array[2],
		);

		update_option('wi_backbone', $free_limitation);
	}

	public static function get_free_limitation_message() {

		$statusCheck = WebinarignitionLicense::get_license_level();

		if (!empty($statusCheck) && $statusCheck->switch !== 'free' && $statusCheck->switch !== 'basic') {
			return '';
		}

		$settings   = self::get_limitation_settings();
		$is_trial   = !empty($statusCheck->is_trial);
		$user_limit = $is_trial ? self::$free_trial_limit : $settings['limit_users'];
		$days_limit = $settings['limit_days'];
		$limit_message = $settings['limit_message'];
		$user_count = self::get_limit_counter();
		$user_count_timeout = get_option('webinarignition_limit_timeout', 0);
		$timeout_label = '';
		$optin_button_text = __('Opt-in for More Free Registrations, License Options', 'webinarignition');

		if (!empty($user_count_timeout)) {
			$user_count_timeout_left = $user_count_timeout - time();

			if ($user_count_timeout_left > 86400) {
				$timeout_left = ceil($user_count_timeout_left / 86400);
				$timeout_label = $timeout_left . ' ' . __(' days', 'webinarignition');
			} else {
				$timeout_left = ceil($user_count_timeout_left / 3600);
				$timeout_label = $timeout_left . ' ' . __(' hours', 'webinarignition');
			}
		}

		$user_left = (int) $user_limit - (int) $user_count;

		$string = '<p>';
		$string .= __('In Free version: You can create unlimited Live webinars with unlimited registrations, but with old webinar room design.', 'webinarignition') . '<br>';
		$string .= ' ' . __('In Ultimate version: You have multiple evergreen Call-to-Actions to boost your sales', 'webinarignition') . '<br>';
		$string .= ' ' . __('In Ultimate version: Shortcodes to layout and design your live and evergreen webinar individually.', 'webinarignition');
		$string .= '</p>';

		$string .= '<p style="color: #f6b401;">';
		if (!empty($statusCheck->is_registered)) {
			$string .=  ' ';
			$string .=  sprintf(__('In Free version: Evergreen webinars have limited registrations up to %s per %s days.', 'webinarignition'), $user_limit, $days_limit);
			$string .=  '<br>';
			$string .= ' ' . __('In Ultimate version: You have unlimited evergreen registrations.', 'webinarignition');
			$string .=  '<br>';
		} else {
			$string .= sprintf(__('Please click "%s" button at the top right corner, click "Allow & Continue" button on next page to get:', 'webinarignition'), $optin_button_text) . '<br>';
			$string .= ' &bull; ' . __('Option to try all features for FREE on both Live & Evergreen webinars', 'webinarignition') . '<br>';
			$string .= ' &bull; ' . __('Multiple Call-to-Actions to boost your sales by showing relevant content on your webinar room video overlay or sidebar', 'webinarignition') . '<br>';
			$string .= ' &bull; ' . __('Customize the design of your webinar pages separately by using shortcodes', 'webinarignition') . '<br>';
			$string .= ' &bull; ' . __('Send data to your CRM, or other third-party services using Webhooks', 'webinarignition') . '<br>';
			$string .= ' &bull; ' . __('New webinar room design with flexible sidebar', 'webinarignition') . '<br>';
			$string .= ' &bull; ' . sprintf(__('Checkout our <a href="%s" target="_blank">homepage</a> to see all existing and upcoming features', 'webinarignition'), 'https://webinarignition.com/') . '<br>';
		}
		$string .= '</p>';

		$string .= '<p>';
		if (0 > $user_left || 0 === $user_left) {
			$string .= __('Webinar registration for Evergreen webinars stopped!', 'webinarignition');

			if (!empty($timeout_label)) {
				$string .=  sprintf(__('Wait %s without new registrations.', 'webinarignition'), $timeout_label);
			}
		} else {
			if (!empty($user_count)) {
				$string .=  sprintf(__('You already have %d registrations for Evergreen webinars out of %d.', 'webinarignition'), absint($user_count), absint($user_limit));
			}

			if (!empty($timeout_label)) {
				$string .= ' ';
				$string .= sprintf(__('Limit will be reset in %s.', 'webinarignition'), $timeout_label);
			}
		}
		$string .= '</p>';

		return $string;
	}

	#endregion

	#--------------------------------------------------------------------------------
	#region Helpers
	#--------------------------------------------------------------------------------

	private static function get_switch_by_slug($slug, $is_trial = false) {
		if ($is_trial) {
			$slug = 'free';
		}

		switch ($slug) {
			case 'enterprise_powerup':
			case 'ultimate_powerup':
				return 'enterprise_powerup';
            case 'ultimate_powerup_tier1a':
            case 'ultimate_powerup_tier2a':
            case 'ultimate_powerup_tier3a':
			case 'enterprise':
			case 'pro':
			case 'premium':
			case 'onetimepro':
				return 'pro';
			case 'basic':
			case 'onetime_basic':
				return 'basic';
			default:
				return 'free';
		}
	}

	private static function get_title_by_slug($slug, $is_trial = false) {
		switch ($slug) {
			case 'enterprise_powerup':
			case 'ultimate_powerup':
				if ($is_trial) {
					return __('Ultimate', 'webinarignition') . ' (' . __('14 days trial', 'webinarignition') . ')';
				}
				return __('Ultimate', 'webinarignition');
			case 'ultimate_powerup_tier1a':
			case 'ultimate_powerup_tier2a':
			case 'ultimate_powerup_tier3a':
                return ucwords(strtolower(webinarignition_fs()->get_plan_title()));
			case 'enterprise':
			case 'premium':
			case 'pro':
			case 'onetimepro':
				return __('Enterprise', 'webinarignition');
			case 'basic':
			case 'onetime_basic':
				return __('Basic', 'webinarignition');
			default:
				return __('Free', 'webinarignition');
		}
	}

	public static function is_dev() {
		return defined('WEBINARIGNITIN_DEV_MODE') && WEBINARIGNITIN_DEV_MODE;
	}

	#endregion
}
