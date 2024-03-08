<?php
defined( 'ABSPATH' ) || exit;

// TODO - Separate Backend and Frontend callbacks

// ADD NEW LEAD
add_action( 'wp_ajax_nopriv_webinarignition_add_lead', 'webinarignition_add_lead_callback' );
add_action( 'wp_ajax_webinarignition_add_lead', 'webinarignition_add_lead_callback' );
function webinarignition_add_lead_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input       = filter_input_array( INPUT_POST );

	$post_input['name']         = isset( $post_input['name'] )      ? sanitize_text_field( $post_input['name'] ) : '';
	$post_input['firstName']    = isset( $post_input['firstName'] ) ? sanitize_text_field( $post_input['firstName'] ) : '';
	$post_input['email']        = isset( $post_input['email'] )     ? sanitize_email( $post_input['email'] ) : '';
	$post_input['phone']        = isset( $post_input['phone'] )     ? sanitize_text_field( $post_input['phone'] ) : '';
	$post_input['source']       = isset( $post_input['source'] )    ? sanitize_text_field( $post_input['source'] ) : 'Optin';
	$post_input['gdpr_data']    = isset( $post_input['gdpr_data'] ) ? sanitize_text_field( $post_input['gdpr_data'] ) : '';
	$post_input['ip']           = isset( $post_input['ip'] )        ? sanitize_text_field( $post_input['ip'] ) : '';
	$post_input['id']           = isset( $post_input['id'] )        ? sanitize_text_field( $post_input['id'] ) : '';
	$post_input['id']           = ( empty( $post_input['id'] ) && !empty( $post_input['campaignID'] ) ) ? sanitize_text_field( $post_input['campaignID'] ) : $post_input['id'];

	$webinar_data               = WebinarignitionManager::get_webinar_data($post_input['id']);

	if ( !empty( $webinar_data->webinar_lang )  ) {
		$applang = $webinar_data->webinar_lang;
		switch_to_locale( $applang );
		unload_textdomain( 'webinarignition' );
		load_textdomain( 'webinarignition', WEBINARIGNITION_PATH . 'languages/webinarignition-' . $applang . '.mo' );
	}

	if( !empty($webinar_data->time_format ) && ( $webinar_data->time_format == '12hour' || $webinar_data->time_format == '24hour'  ) ){ //old formats
		$webinar_data->time_format = get_option( "time_format", 'H:i' );
	}
	$time_format                = $webinar_data->time_format;
	$is_lead_protected          = !empty($webinar_data->protected_lead_id) && 'protected' === $webinar_data->protected_lead_id;

	global $wpdb;
	$is_ajax = false;

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		$is_ajax = true;
	}

	$table_db_name = $wpdb->prefix . "webinarignition_leads";

	if ($is_lead_protected) {
		$sql           = "SELECT hash_ID AS ID FROM {$table_db_name} WHERE email = %s AND app_id = %d";
	} else {
		$sql           = "SELECT ID FROM {$table_db_name} WHERE email = %s AND app_id = %d";
	}

	$safe_sql      = $wpdb->prepare( $sql, $post_input['email'], $post_input['id'] );
	$lead          = $wpdb->get_row( $safe_sql );

	if ( $lead ) {
		if ( $is_ajax !== false ) {
			echo $lead->ID;
			exit();
		} else {
			return $lead->ID;
		}
	}

	$wpdb->insert( $table_db_name, [
		'app_id'    => $post_input['id'],
		'name'      => $post_input['name'],
		'email'     => $post_input['email'],
		'phone'     => $post_input['phone'],
		'trk1'      => $post_input['source'],
		'trk3'      => $post_input['ip'],
		'event'     => 'No',
		'replay'    => 'No',
		'created'   => date( 'F j, Y' ),
		'gdpr_data' => $post_input['gdpr_data'],

	] );

	$out = $wpdb->insert_id;

	$hash_ID = sha1($post_input['id'] . $post_input['email'] . $out);

	$wpdb->update($table_db_name, ['hash_ID' => $hash_ID], ['ID' => $out]);

	$wiRegForm_data = !empty($post_input["wiRegForm"]) ? $post_input["wiRegForm"] : [];

	$lead_meta = [];

	foreach ($wiRegForm_data as $field_name => $field) {
		$field_label = rtrim(sanitize_text_field($field['label']), '*');
		$field_value = sanitize_text_field($field['value']);

		$lead_meta[$field_name] = [
			'label' => $field_label,
			'value' => $field_value
		];
	}

	if ( !empty($lead_meta) ) {
		$lead_meta = WebinarignitionLeadsManager::fix_optName($lead_meta);
		WebinarignitionLeadsManager::update_lead_meta($out, 'wiRegForm', serialize($lead_meta), 'live');
		WebinarignitionLeadsManager::update_lead_meta($out, 'wiRegForm_' . $post_input['id'], serialize($lead_meta), 'live');

		/**
		 * Action Hook: webinarignition_live_lead_added
		 *
		 * @param int $webinar_id Webinar ID for which the lead was added
		 * @param int $lead_id Lead ID which was added
		 * @param array $lead_metadata Associated lead metadata
		 */
		$webhook_lead_data = [];
		foreach ($lead_meta as $lead_meta_key => $lead_meta_value) {
			if( is_array($lead_meta_value) ) {
				$webhook_lead_data[$lead_meta_key] = $lead_meta_value['value'];
			}
		}
		do_action('webinarignition_lead_added', absint($post_input['id']), $out, $webhook_lead_data);
		do_action('webinarignition_live_lead_added', absint($post_input['id']), $out, $webhook_lead_data);
		do_action( 'webinarignition_lead_status_changed', 'attended', $out, absint($post_input['id']) );
//	    do_action( 'webinarignition_lead_status_changed', 'watched_replay', $out, absint($post_input['id']) );
	}

	do_action( 'webinarignition_lead_created', $out, $table_db_name );

	Webinar_Ignition_Helper::debug_log('webinarignition_lead_created1');
	$lead_details_string = "Name: {$post_input['name']}\nEmail: {$post_input['email']}\n";
	if ( isset( $post_input['phone'] ) && $post_input['phone'] != 'undefined' ) {
		$lead_details_string .= "Phone: {$post_input['phone']}";
	}

	// registration email has been disabled in notification settings
	if ( $webinar_data->email_signup === 'off' ) {
		WebinarIgnition_Logs::add( __( "New Lead Added", "webinarignition")."\n$lead_details_string\n\n".__( "Not sending registration email (DISABLED)", "webinarignition"), $post_input['id'], WebinarIgnition_Logs::LIVE_EMAIL );

		if ($is_lead_protected) {
			echo $hash_ID;
		} else {
			echo $out;
		}
		die();
	}

	WebinarIgnition_Logs::add( __( "New Lead Added", "webinarignition")."\n$$lead_details_string\n\n".__( "Firing registration email", "webinarignition"), $post_input['id'], WebinarIgnition_Logs::LIVE_EMAIL );

	if( !empty( $webinar_data->templates_version ) || ( !empty( $webinar_data->use_new_email_signup_template )  && ( $webinar_data->use_new_email_signup_template == 'yes' ) ) ){
		//use new templates
		$webinar_data->emailheading     = $webinar_data->email_signup_heading;
		$webinar_data->emailpreview     = $webinar_data->email_signup_preview;
		$webinar_data->bodyContent      = $webinar_data->email_signup_body;
		$webinar_data->footerContent    = ( property_exists($webinar_data, 'show_or_hide_local_email_signup_footer') && $webinar_data->show_or_hide_local_email_signup_footer == 'show' ) ? $webinar_data->local_email_signup_footer : '';

		$email      = new WI_Emails();
		$emailBody  = $email->build_email( $webinar_data );
	} else {
		//this is an old webinar, created before this version
		$emailHead = WebinarignitionEmailManager::get_email_head();
		$emailBody = $emailHead;
		$emailBody .= $webinar_data->email_signup_body;
		$emailBody .= '</html>';
	}

	$emailBody = str_replace( "{LEAD_NAME}", ( ! empty( $post_input['firstName'] ) ? sanitize_text_field( $post_input['firstName'] ) : $post_input['name'] ), $emailBody );
	$emailBody = str_replace( "{FIRSTNAME}", ( ! empty( $post_input['firstName'] ) ? sanitize_text_field( $post_input['firstName'] ) : $post_input['name'] ), $emailBody );

	$localized_date = webinarignition_get_localized_date( $webinar_data );

	$timeonly  = ( empty($webinar_data->display_tz ) || ( !empty($webinar_data->display_tz) && ($webinar_data->display_tz == 'yes') ) ) ? false : true;
	// Replace
	$emailBody = str_replace( "{DATE}", $localized_date . " @ " . webinarignition_get_time_tz( $webinar_data->webinar_start_time, $time_format, $webinar_data->webinar_timezone, false, $timeonly ), $emailBody );

	$emailBody = WebinarignitionManager::replace_email_body_placeholders($webinar_data, $out, $emailBody);

	$email_signup_sbj = str_replace( "{TITLE}", $webinar_data->webinar_desc, $webinar_data->email_signup_sbj );

	$headers = array('Content-Type: text/html; charset=UTF-8');

	webinarignition_test_smtp_options();

	try {
		if ( ! wp_mail( $post_input['email'], $email_signup_sbj, $emailBody, $headers) ) {
			WebinarIgnition_Logs::add( __( "Registration email could not be sent to", "webinarignition"). " {$post_input['email']}", WebinarIgnition_Logs::LIVE_EMAIL );
		} else {
			WebinarIgnition_Logs::add( __( "Registration email has been sent.", "webinarignition"), $post_input['id'], WebinarIgnition_Logs::LIVE_EMAIL );
		}
	} catch (Exception $e) {
		WebinarIgnition_Logs::add( __( "Registration email could not be sent to", "webinarignition"). " {$post_input['email']}", WebinarIgnition_Logs::LIVE_EMAIL );
	}

	if ( ! empty( $webinar_data->get_registration_notices_state ) && ( $webinar_data->get_registration_notices_state === 'show' ) && ( ! empty( $webinar_data->registration_notice_email ) ) && filter_var( $webinar_data->registration_notice_email, FILTER_VALIDATE_EMAIL ) ) {

		$subj         = __( "New Registration For", "webinarignition") . " ".$webinar_data->webinar_desc . " " . __( "By", "webinarignition") . " ". $post_input['name'];
		$attendeeName = $post_input['name'];

		$emailBody = $emailHead;

		if( !empty($lead_meta) ) {
			foreach ($lead_meta as $lead_field_key => $lead_field_data) {
				if( $lead_field_key === 'optName' && $lead_field_data['value'] === '#firstlast#' ) continue; //Skip firstlast tag

				$emailBody .= "<br><br>{$lead_field_data['label']}: {$lead_field_data['value']}";
			}
		}

		$emailBody .= '</html>';
		try {
			wp_mail( $webinar_data->registration_notice_email, $subj, $emailBody, $headers);
		} catch (Exception $e) {

		}

	}

	if ( !empty( $webinar_data->webinar_lang ) ) { restore_previous_locale(); }

	if ( $is_ajax !== false ) {
		if ($is_lead_protected) {
			echo $hash_ID;
		} else {
			echo $out;
		}
		die();
	}

	if ($is_lead_protected) {
		return $hash_ID;
	} else {
		return $out;
	}
}


// ADD NEW EVERGREEN (auto) LEAD
add_action( 'wp_ajax_nopriv_webinarignition_get_lead_auto', 'webinarignition_get_lead_auto_callback' );
add_action( 'wp_ajax_webinarignition_get_lead_auto', 'webinarignition_get_lead_auto_callback' );
function webinarignition_get_lead_auto_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$input_get = filter_input_array( INPUT_GET );

	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition_leads_evergreen";
	$lead          = $wpdb->get_row( $wpdb->prepare( "SELECT `app_id`, `name`, `email`, `phone`, `date_picked_and_live`, `lead_timezone` FROM $table_db_name WHERE ID = %d", $input_get['lid'] ), OBJECT );

	//when $webinar_data->protected_lead_id
	if( empty( $lead ) ){
		$lead          = $wpdb->get_row( $wpdb->prepare( "SELECT `app_id`, `name`, `email`, `phone`, `date_picked_and_live`, `lead_timezone` FROM $table_db_name WHERE hash_ID = %s", $input_get['lid'] ), OBJECT );
	}

	if( is_object($lead) ){

		if ( ! isset( $lead->lname ) && strrpos( $lead->name, " " ) ) {
			$lead->lname = explode( " ", $lead->name, 2 );
			$lead->name  = $lead->lname[0];
			$lead->lname = $lead->lname[1];
		}
		//    $lead->webinar_date = date('m-d-Y', strtotime($lead->date_picked_and_live));
		$webinar                           = WebinarignitionManager::get_webinar_data($lead->app_id);
		$arCustomDateFormat                = isset( $webinar->ar_custom_date_format ) ? $webinar->ar_custom_date_format : 'not-set';
		$webinarignition_webinar_timestamp = strtotime( $lead->date_picked_and_live );
		$arWebinarDate                     = webinarignition_format_date_for_ar_service( $arCustomDateFormat, $webinarignition_webinar_timestamp );
		$lead->webinar_date                = $arWebinarDate;
		$lead->webinar_time                = date( 'g:i A', strtotime( $lead->date_picked_and_live ) );

		$lead->lead_timezone = $lead->lead_timezone . " (UTC" . webinarignition_get_timezone_offset_by_name( $lead->lead_timezone ) . ")";

		echo json_encode( $lead );
		exit;

	}

	$object          = new stdClass();
	$object->message = 'lead not found';
	echo json_encode( $object );
	exit;

}

add_action( 'wp_ajax_nopriv_webinarignition_add_lead_auto', 'webinarignition_add_lead_auto_callback' );
add_action( 'wp_ajax_webinarignition_add_lead_auto', 'webinarignition_add_lead_auto_callback' );
function webinarignition_add_lead_auto_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );

	$post_input['name']         = isset( $post_input['name'] )        ? sanitize_text_field( $post_input['name'] ) : null;
	$post_input['email']        = isset( $post_input['email'] )       ? sanitize_email( $post_input['email'] ) : null;
	$post_input['phone']        = isset( $post_input['phone'] )       ? sanitize_text_field( $post_input['phone'] ) : null;
	$post_input['id']     		  = isset( $post_input['id'] )          ? sanitize_text_field( $post_input['id'] ) : null;
	$post_input['timezone']     = isset( $post_input['timezone'] )    ? sanitize_text_field( $post_input['timezone'] ) : null;
	$post_input['date']         = isset( $post_input['date'] )        ? sanitize_text_field( $post_input['date'] ) : null;
	$post_input['time']         = isset( $post_input['time'] )        ? sanitize_text_field( $post_input['time'] ) : null;
	$post_input['gdpr_data']    = isset( $post_input['gdpr_data'] )   ? sanitize_text_field( $post_input['gdpr_data'] ) : null;

	$webinar_data   = WebinarignitionManager::get_webinar_data($post_input['id']);

	//Delete existing lead
	if( !empty($webinar_data) ) {
		$delete_lead_id = webinarignition_existing_lead_id($post_input['id'], $post_input['email']);

		if( !empty($delete_lead_id) ) {
			webinarignition_delete_lead_by_id($delete_lead_id);
		}
	}

	if ( $applang = $webinar_data->webinar_lang ) {
		switch_to_locale( $applang );
		unload_textdomain( 'webinarignition' );
		load_textdomain( 'webinarignition', WEBINARIGNITION_PATH . 'languages/webinarignition-' . $applang . '.mo' );
	}

	if( !empty($webinar_data->time_format ) && ( $webinar_data->time_format == '12hour' || $webinar_data->time_format == '24hour'  ) ){ //old formats
		$webinar_data->time_format = get_option( "time_format", 'H:i' );
	}
	$time_format    = $webinar_data->time_format;
	$date_format    = !empty($webinar_data->date_format ) ? $webinar_data->date_format  : 'l, F j, Y';

	if( ! empty( $post_input['timezone'] )  ){
		$lead_timezone = new DateTimeZone( $post_input['timezone'] );
	} else {
		$lead_timezone = get_option('timezone_string');
	}

	// Get info
	$webinarLength   = $webinar_data->auto_video_length;
	$setCheckInstant = "";
	$instant         = "no";

	if ( $post_input['date'] == "instant_access" ) {
		$current_time = new DateTime( 'now', $lead_timezone );
		$todaysDate   = $current_time->format( "Y-m-d" );
		$todaysTime   = $current_time->format( "H:i" );

		// They choose to watch replay
		$time               = date( 'H:i', strtotime( $todaysTime . "+0 hours" ) );
		$post_input['date'] = $todaysDate;
		$post_input['time'] = $time;

		$instant         = "yes";
	}

	$is_ty_page_skipped = false;
	if( $instant === 'yes' ) {
		$is_ty_page_skipped = ( isset($webinar_data->skip_instant_acces_confirm_page) && $webinar_data->skip_instant_acces_confirm_page === 'yes' );
	}

	// Get & Set Dates For Emails...
	$dpl = $post_input['date'] . " " . $post_input['time'];
	$fmt = 'Y-m-d H:i';

	$date_picked_and_live = date( $fmt, strtotime( $dpl ) );
	$date_1_day_before    = date( $fmt, strtotime( $dpl . " -1 days" ) );
	$date_1_hour_before   = date( $fmt, strtotime( $dpl . " -1 hours" ) );
	$date_after_live      = date( $fmt, strtotime( $dpl . " +$webinarLength minutes" ) );
	$date_1_day_after     = date( $fmt, strtotime( $dpl . " +1 days" ) );

	$wiRegForm_data = !empty($post_input["wiRegForm"]) ? $post_input["wiRegForm"] : [];

	$lead_meta = [];

	foreach ($wiRegForm_data as $field_name => $field) {
		$field_label = rtrim(sanitize_text_field($field['label']), '*');
		$field_value = sanitize_text_field($field['value']);

		$lead_meta[$field_name] = [
			'label' => $field_label,
			'value' => $field_value
		];
	}

	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition_leads_evergreen";

	$wpdb->insert( $table_db_name,
		[
			'app_id'                     => $post_input['id'],
			'name'                       => $post_input['name'],
			'email'                      => $post_input['email'],
			'phone'                      => ! empty( $post_input['phone'] ) ? $post_input['phone'] : '',
			'lead_timezone'              => ! empty( $post_input['timezone'] ) ? $post_input['timezone'] : '',
			'trk1'                       => 'Optin',
			'trk3'                       => ! empty( $post_input['ip'] ) ? $post_input['ip'] : '',
			'trk8'                       => $instant,
			'event'                      => ( $instant === 'yes' && $is_ty_page_skipped ) ? 'Yes' : 'No', //Set attended "Yes" for instant leads only when user get redirected to webinar page directly, skipping ty page setting
			'replay'                     => ( $instant === 'yes' && $is_ty_page_skipped ) ? 'Yes' : 'No',
			'created'                    => date( 'F j, Y' ),
			'date_picked_and_live'       => $date_picked_and_live,
			'date_1_day_before'          => $date_1_day_before,
			'date_1_hour_before'         => $date_1_hour_before,
			'date_after_live'            => $date_after_live,
			'date_1_day_after'           => $date_1_day_after,
			'date_picked_and_live_check' => $setCheckInstant,
			'date_1_day_before_check'    => $setCheckInstant,
			'date_1_hour_before_check'   => $setCheckInstant,
			'date_after_live_check'      => $setCheckInstant,
			//'lead_browser_and_os'           => !empty($post_input['lead_browser_and_os']) ? $post_input['lead_browser_and_os'] : '',
			'gdpr_data'                  => ! empty( $post_input['gdpr_data'] ) ? $post_input['gdpr_data'] : ''
		]
	);

	$out      = $wpdb->insert_id;

	$hash_ID = sha1($post_input['id'] . $post_input['email'] . $out);

	$wpdb->update($table_db_name, ['hash_ID' => $hash_ID], ['ID' => $out]);

	if (!empty($lead_meta)) {
		$lead_meta = WebinarignitionLeadsManager::fix_optName($lead_meta);
		WebinarignitionLeadsManager::update_lead_meta($out, 'wiRegForm', serialize($lead_meta), 'evergreen');
		WebinarignitionLeadsManager::update_lead_meta($out, 'wiRegForm_' . $post_input['id'], serialize($lead_meta), 'evergreen');

		/**
		 * Action Hook: webinarignition_lead_added
		 *
		 * @param int $webinar_id Webinar ID for which the lead was added
		 * @param int $lead_id Lead ID which was added
		 * @param array $lead_metadata Associated lead metadata
		 */
		$webhook_lead_data = [];
		foreach ($lead_meta as $lead_meta_key => $lead_meta_value) {
			if( is_array($lead_meta_value) ) {
				$webhook_lead_data[$lead_meta_key] = $lead_meta_value['value'];
			}
		}
		do_action('webinarignition_lead_added', absint($post_input['id']), $out, $webhook_lead_data);
		do_action('webinarignition_live_lead_added', absint($post_input['id']), $out, $webhook_lead_data);

		if( $instant === 'yes' ) { //Trigger status change hooks
			do_action( 'webinarignition_lead_status_changed', 'attended', $out, absint($post_input['id']) );
//		        do_action( 'webinarignition_lead_status_changed', 'watched_replay', $out, absint($post_input['id']) );
		}
	}

	$cookieID = $out;
	do_action( 'webinarignition_lead_created', $out, $table_db_name );
	$lead_id = $out;

	$is_lead_protected = !empty($webinar_data->protected_lead_id) && 'protected' === $webinar_data->protected_lead_id;
	if ($is_lead_protected) {
		$lead_id = $hash_ID;
	}

	Webinar_Ignition_Helper::debug_log('webinarignition_lead_created2');
	echo $lead_id;

	$lead_details_string = "Name: {$post_input['name']}\nEmail: {$post_input['email']}\n";

	if ( ! empty( $post_input['phone'] ) ) {
		$lead_details_string .= "Phone: {$post_input['phone']}";
	}

	$send_signup_user_notification  = isset($webinar_data->email_signup) && $webinar_data->email_signup !== 'off';
	$send_signup_admin_notification = isset($webinar_data->get_registration_notices_state) && $webinar_data->get_registration_notices_state === 'show';

	WebinarIgnition_Logs::add( __( "New Lead Added", "webinarignition"), $post_input['id'], WebinarIgnition_Logs::AUTO_EMAIL );
	WebinarIgnition_Logs::add( $lead_details_string, $post_input['id'], WebinarIgnition_Logs::AUTO_EMAIL );

	/*
	|-------------------------------------------------------------------------------------------
	|  EMAIL SENDING`
	|-------------------------------------------------------------------------------------------
	*/

	//Send sign-up email to user
	if( !$send_signup_user_notification || ( $instant === 'yes' && $is_ty_page_skipped ) ) {
		WebinarIgnition_Logs::add( __( 'Not sending user sign-up email', "webinarignition"), $post_input['id'], WebinarIgnition_Logs::AUTO_EMAIL );

	} else {

		WebinarIgnition_Logs::add( __( 'Sending user sign-up email', 'webinarignition'), $post_input['id'], WebinarIgnition_Logs::AUTO_EMAIL );

		if( isset( $webinar_data->templates_version ) && !empty( $webinar_data->templates_version ) || ( isset( $webinar_data->use_new_email_signup_template ) && $webinar_data->use_new_email_signup_template == 'yes' ) ) {
			//use new templates
			$webinar_data->emailheading     = $webinar_data->email_signup_heading;
			$webinar_data->emailpreview     = $webinar_data->email_signup_preview;
			$webinar_data->bodyContent      = $webinar_data->email_signup_body;
			$webinar_data->footerContent    = ( property_exists($webinar_data, 'show_or_hide_local_email_signup_footer') && $webinar_data->show_or_hide_local_email_signup_footer == 'show' ) ? $webinar_data->local_email_signup_footer : '';

			$email      = new WI_Emails();
			$emailBody  = $email->build_email( $webinar_data );
		} else {
			$emailHead = WebinarignitionEmailManager::get_email_head();
			$emailBody = $emailHead;
			$emailBody .= $webinar_data->email_signup_body;
		}

		$email_signup_sbj = str_replace( "{TITLE}", $webinar_data->webinar_desc, $webinar_data->email_signup_sbj );

		$name = '';
		if( isset($post_input['name']) && !empty($post_input['name']) ) {
			$name = sanitize_text_field($post_input['name']);
		}

		if( isset($post_input['firstName']) && !empty($post_input['firstName']) ) {
			$name = sanitize_text_field($post_input['firstName']);
		}

		$emailBody = str_replace( "{LEAD_NAME}", $name, $emailBody );
		$emailBody = str_replace( "{FIRSTNAME}", $name, $emailBody );

		if ( !isset( $webinar_data->webinar_permalink ) ) {
			$webinar_data->webinar_permalink = WebinarignitionManager::get_permalink($post_input['id'], 'webinar');
		}

		$translated_date = '';
		if( isset($post_input['date']) && !empty($post_input['date']) ) {
			$translated_date = webinarignition_get_translated_date( sanitize_text_field($post_input['date']),  'Y-m-d', $date_format );
		}

		// Replace
		if ( $instant === 'yes' ) {
			if ( $webinar_data->auto_translate_instant == "" ) {
				$emailBody = str_replace( "{DATE}", "Watch Replay", $emailBody );
			} else {
				$emailBody = str_replace( "{DATE}", $webinar_data->auto_translate_instant, $emailBody );
			}
		} else {
			$timeonly  = ( empty($webinar_data->display_tz ) || ( !empty($webinar_data->display_tz) && ($webinar_data->display_tz == 'yes') ) ) ? false : true;
			$emailBody = str_replace( "{DATE}", $translated_date . " @ " . webinarignition_get_time_tz( $post_input['time'], $time_format, $post_input['timezone'], false, $timeonly  ), $emailBody );
		}

		$headers = array('Content-Type: text/html; charset=UTF-8');

		webinarignition_test_smtp_options();

		$watch_type = 'live';
		$additional_email_query_params = 'event=OI3shBXlqsw';
		$additional_email_query_params .= "&watch_type={$watch_type}";

		$emailBody = WebinarignitionManager::replace_email_body_placeholders($webinar_data, $out, $emailBody, $additional_email_query_params);

		try {
			if ( ! wp_mail( $post_input['email'], $email_signup_sbj, $emailBody, $headers ) ) {
				WebinarIgnition_Logs::add( __( "Registration email could not be sent to", "webinarignition")." {$post_input['email']}", $post_input['id'], WebinarIgnition_Logs::AUTO_EMAIL );
			} else {
				WebinarIgnition_Logs::add( __( "Registration email has been sent.", "webinarignition"), $post_input['id'], WebinarIgnition_Logs::AUTO_EMAIL );
			}
		}  catch ( Exception $e ) {
			WebinarIgnition_Logs::add( __( "Registration email could not be sent to", "webinarignition")." {$post_input['email']}", $post_input['id'], WebinarIgnition_Logs::AUTO_EMAIL );
		}
	}

	if ( !empty( $webinar_data->webinar_lang ) ) {
		restore_previous_locale();
	}

	//Send new user sign-up notification email to admin
	if ( $send_signup_admin_notification && ( isset($webinar_data->registration_notice_email) && !empty($webinar_data->registration_notice_email) && filter_var( $webinar_data->registration_notice_email, FILTER_VALIDATE_EMAIL ) ) ) {

		WebinarIgnition_Logs::add( __( 'Sending new user sign-up notification email to admin', "webinarignition"), $post_input['id'], WebinarIgnition_Logs::AUTO_EMAIL );

		$headers   = array('Content-Type: text/html; charset=UTF-8');
		$subj      = __( "New Registration For", "webinarignition") . ' '. $webinar_data->webinar_desc . ' '. __( "By", "webinarignition") . ' ' . $post_input['name'];
		$emailHead = WebinarignitionEmailManager::get_email_head();
		$emailBody = $emailHead;

		if( !empty($lead_meta) ) {
			foreach ($lead_meta as $lead_field_key => $lead_field_data) {
				if( $lead_field_key === 'optName' && $lead_field_data['value'] === '#firstlast#' ) continue; //Skip firstlast tag

				$emailBody .= "<br><br>{$lead_field_data['label']}: {$lead_field_data['value']}";
			}
		}

		$emailBody .= '</html>';

		try {
			wp_mail( $webinar_data->registration_notice_email, $subj, $emailBody, $headers );
		} catch (Exception $e) {

		}
	} else {
		WebinarIgnition_Logs::add( __( 'Not sending new user sign-up notification email to admin', "webinarignition"), $post_input['id'], WebinarIgnition_Logs::AUTO_EMAIL );
	}

	die();
}


// ADD NEW LEAD
add_action( 'wp_ajax_nopriv_webinarignition_add_lead_auto_reg', 'webinarignition_add_lead_auto_reg_callback' );
add_action( 'wp_ajax_webinarignition_add_lead_auto_reg', 'webinarignition_add_lead_auto_reg_callback' );
function webinarignition_add_lead_auto_reg_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );

	$post_input['name']         = isset( $post_input['name'] )      ? sanitize_text_field( $post_input['name'] ) : null;
	$post_input['firstName']    = isset( $post_input['firstName'] ) ? sanitize_text_field( $post_input['firstName'] ) : null;
	$post_input['email']        = isset( $post_input['email'] )     ? sanitize_email( $post_input['email'] ) : null;
	$post_input['phone']        = isset( $post_input['phone'] )     ? sanitize_text_field( $post_input['phone'] ) : null;
	$post_input['id']           = isset( $post_input['id'] )        ? sanitize_text_field( $post_input['id'] ) : null;
	$post_input['source']       = isset( $post_input['source'] )    ? sanitize_text_field( $post_input['source'] ) : null;
	$post_input['ip']           = isset( $post_input['ip'] )        ? sanitize_text_field( $post_input['ip'] ) : null;


	$webinar_data       = WebinarignitionManager::get_webinar_data($post_input['id']);

	if ( $applang = $webinar_data->webinar_lang ) {
		switch_to_locale( $applang );
		unload_textdomain( 'webinarignition' );
		load_textdomain( 'webinarignition', WEBINARIGNITION_PATH . 'languages/webinarignition-' . $applang . '.mo' );
	}

	if( !empty($webinar_data->time_format ) && ( $webinar_data->time_format == '12hour' || $webinar_data->time_format == '24hour'  ) ){ //old formats
		$webinar_data->time_format = get_option( "time_format", 'H:i' );
	}
	$time_format        = $webinar_data->time_format;
	$date_format        = !empty($webinar_data->date_format ) ? $webinar_data->date_format  : 'l, F j, Y';
	$is_lead_protected  = !empty($webinar_data->protected_lead_id) && 'protected' === $webinar_data->protected_lead_id;

	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition_leads";
	// Check if lead with such email exists in database

	if ($is_lead_protected) {
		$lead = $wpdb->get_row( $wpdb->prepare( "SELECT hash_ID AS ID FROM $table_db_name WHERE email = %s AND app_id = %d", $post_input['email'], $post_input['id'] ) );
	} else {
		$lead = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $table_db_name WHERE email = %s AND app_id = %d", $post_input['email'], $post_input['id'] ) );
	}

	if ( $lead ) {
		echo $lead->ID;
		die();
	}

	$wpdb->insert( $table_db_name, [
		'app_id'  => $post_input['id'],
		'name'    => $post_input['name'],
		'email'   => $post_input['email'],
		'trk1'    => ! empty( $post_input['source'] ) ? $post_input['source'] : 'Optin',
		'trk3'    => $post_input['ip'],
		'event'   => 'No',
		'replay'  => 'No',
		'created' => date( 'F j, Y' ) //TODO: This should be in mysql datetime format

	] );

	$out = $wpdb->insert_id;

	$hash_ID = sha1($post_input['id'] . $post_input['email'] . $out);

	$wpdb->update($table_db_name, ['hash_ID' => $hash_ID], ['ID' => $out]);

	$lead_meta = $post_input;
	$lead_meta['hash_ID'] = $hash_ID;
	$webinar_type = strtolower(trim($webinar_data->webinar_date)) != 'auto' ? 'evergreen' : 'live';
	/**
	 * Action Hook: webinarignition_lead_added
	 *
	 * @param int $webinar_id Webinar ID for which the lead was added
	 * @param int $lead_id Lead ID which was added
	 * @param array $lead_metadata Associated lead metadata
	 */
	$webhook_lead_data = [];
	foreach ($lead_meta as $lead_meta_key => $lead_meta_value) {
		if( is_array($lead_meta_value) ) {
			$webhook_lead_data[$lead_meta_key] = $lead_meta_value['value'];
		}
	}
	do_action('webinarignition_lead_added', absint($post_input['id']), $out, $webhook_lead_data);
	do_action('webinarignition_live_lead_added', absint($post_input['id']), $out, $webhook_lead_data);

	do_action( 'webinarignition_lead_created', $out, $table_db_name );
	Webinar_Ignition_Helper::debug_log('webinarignition_lead_created3');
	$lead_details_string = "Name: {$post_input['name']}\nEmail: {$post_input['email']}\n";
	WebinarIgnition_Logs::add( __( "New Lead Added", "webinarignition")."\n$lead_details_string\n\n".__( "Firing registration email", "webinarignition"), $post_input['id'], WebinarIgnition_Logs::LIVE_EMAIL );

	// ADD TO MAILING LIST
	$emailBody = $webinar_data->email_signup_body;
	$emailBody = str_replace( "{LEAD_NAME}", ( ! empty( $post_input['firstName'] ) ? $post_input['firstName'] : $post_input['name'] ), $emailBody );
	$emailBody = str_replace( "{FIRSTNAME}", ( ! empty( $post_input['firstName'] ) ? $post_input['firstName'] : $post_input['name'] ), $emailBody );

	// NB: date format for Live webinars always saved in DB as m-d-Y
	$translated_date = webinarignition_get_translated_date( $webinar_data->webinar_date, 'm-d-Y', $date_format );

	$timeonly  = ( empty($webinar_data->display_tz ) || ( !empty($webinar_data->display_tz) && ($webinar_data->display_tz == 'yes') ) ) ? false : true;
	// Replace
	$emailBody = str_replace( "{DATE}", $translated_date . " @ " . webinarignition_get_time_tz( $webinar_data->webinar_start_time, $time_format, $webinar_data->webinar_timezone, false, $timeonly  ), $emailBody );

	$emailBody = WebinarignitionManager::replace_email_body_placeholders($webinar_data, $out, $emailBody);

	$email_signup_sbj = str_replace( "{TITLE}", $webinar_data->webinar_desc, $webinar_data->email_signup_sbj );
	$headers          = array('Content-Type: text/html; charset=UTF-8');

	webinarignition_test_smtp_options();

	try {
		if ( ! wp_mail( $post_input['email'], $email_signup_sbj, $emailBody, $headers ) ) {
			WebinarIgnition_Logs::add( __( "Registration email could not be sent to", "webinarignition")." {$post_input['email']}", $post_input['id'], WebinarIgnition_Logs::AUTO_EMAIL );
		} else {
			WebinarIgnition_Logs::add( __( "Registration email has been sent.", "webinarignition"), $post_input['id'], WebinarIgnition_Logs::AUTO_EMAIL );
		}
	} catch (Exception $e) {
		WebinarIgnition_Logs::add( __( "Registration email could not be sent to", "webinarignition")." {$post_input['email']}", $post_input['id'], WebinarIgnition_Logs::AUTO_EMAIL );
	}

	if ( ( $webinar_data->get_registration_notices_state === 'show' ) && ( ! empty( $webinar_data->registration_notice_email ) ) && filter_var( $webinar_data->registration_notice_email, FILTER_VALIDATE_EMAIL ) ) {

		$subj         = "New Registration For Webinar " . $webinar_data->webinar_desc;
		$attendeeName = $post_input['name'];

		$emailBody = $attendeeName . ' (' . $post_input['email'] . ') '.__( "has just registered for your webinar", "webinarignition"). ' '. $webinar_data->webinar_desc;

		try {
			wp_mail( $webinar_data->registration_notice_email, $subj, $emailBody, $headers );
		} catch (Exception $e) {

		}

	}

	if ( !empty( $webinar_data->webinar_lang ) ) { restore_previous_locale(); }

	if ($is_lead_protected) {
		echo $hash_ID;
	} else {
		echo $out;
	}

	die();
}

/**
 * TODO: This function might not be in used, need to check further before removing it.
 *
 * @param $ID
 * @param $NAME
 * @param $EMAIL
 * @param $IP
 */
function webinarignition_add_lead_fb( $ID, $NAME, $EMAIL, $IP ) {

	$webinar_data   = WebinarignitionManager::get_webinar_data($ID);
	if ( $applang = $webinar_data->webinar_lang ) {
		switch_to_locale( $applang );
		unload_textdomain( 'webinarignition' );
		load_textdomain( 'webinarignition', WEBINARIGNITION_PATH . 'languages/webinarignition-' . $applang . '.mo' );
	}

	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition_leads";

	$ID    = sanitize_text_field( $ID );
	$NAME  = sanitize_text_field( $NAME );
	$EMAIL = sanitize_email( $EMAIL );

	$wpdb->insert( $table_db_name, array(
		'app_id'  => $ID,
		'name'    => $NAME,
		'email'   => $EMAIL,
		'trk1'    => 'FB',
		'trk3'    => $IP,
		'created' => date( 'F j, Y' )
	) );

	$getLEADID = $wpdb->insert_id;

	$hash_ID = sha1($ID . $EMAIL . $getLEADID);

	$wpdb->update($table_db_name, ['hash_ID' => $hash_ID], ['ID' => $getLEADID]);

	echo $getLEADID;
	$lead_details_string = "Name: {$NAME}\nEmail: {$EMAIL}\n";
	WebinarIgnition_Logs::add( __( "New Lead Added", "webinarignition")."\n$lead_details_string\n\n".__( "Firing registration email", "webinarignition"), $ID, WebinarIgnition_Logs::LIVE_EMAIL );


	if( !empty($webinar_data->time_format ) && ( $webinar_data->time_format == '12hour' || $webinar_data->time_format == '24hour'  ) ){ //old formats
		$webinar_data->time_format = get_option( "time_format", 'H:i' );
	}
	$time_format    = $webinar_data->time_format;
	$date_format    = !empty($webinar_data->date_format ) ? $webinar_data->date_format  : ( ($webinar_data->webinar_date == "AUTO") ? 'l, F j, Y' : get_option( "date_format") );

	$emailBody = $webinar_data->email_signup_body;

	// NB: date format for Live webinars always saved in DB as m-d-Y
	$translated_date = webinarignition_get_translated_date( $webinar_data->webinar_date, 'm-d-Y', $date_format );

	$timeonly = ( empty($webinar_data->display_tz ) || ( !empty($webinar_data->display_tz) && ($webinar_data->display_tz == 'yes') ) ) ? false : true;
	// Replace
	$emailBody = str_replace( "{DATE}", $translated_date . " @ " . webinarignition_get_time_tz( $webinar_data->webinar_start_time, $time_format, $webinar_data->webinar_timezone, false, $timeonly ), $emailBody );

	$emailBody = WebinarignitionManager::replace_email_body_placeholders($webinar_data, $getLEADID, $emailBody);

	$email_signup_sbj = str_replace( "{TITLE}", $webinar_data->webinar_desc, $webinar_data->email_signup_sbj );
	$headers = array('Content-Type: text/html; charset=UTF-8');

	webinarignition_test_smtp_options();

	try {
		if ( ! wp_mail( $EMAIL, $email_signup_sbj, $emailBody, $headers ) ) {
			WebinarIgnition_Logs::add( __( "Registration email could not be sent to", "webinarignition")." {$EMAIL}", $ID, WebinarIgnition_Logs::LIVE_EMAIL );
			exit;
		} else {
			WebinarIgnition_Logs::add( __( "Registration email has been sent.", "webinarignition"), $ID, WebinarIgnition_Logs::LIVE_EMAIL );
		}
	} catch (Exception $e) {
		WebinarIgnition_Logs::add(  __( "Registration email could not be sent to", "webinarignition")." {$EMAIL}", $ID, WebinarIgnition_Logs::LIVE_EMAIL );
		exit;
	}


	if ( ( $webinar_data->get_registration_notices_state === 'show' ) && ( ! empty( $webinar_data->registration_notice_email ) ) && filter_var( $webinar_data->registration_notice_email, FILTER_VALIDATE_EMAIL ) ) {

		$subj = __( "New Registration For Webinar", "webinarignition")." " . $webinar_data->webinar_desc;

		$emailBody = $NAME . ' '.__( "has just registered for your webinar", "webinarignition").' ' . $webinar_data->webinar_desc;

		try {
			wp_mail( $webinar_data->registration_notice_email, $subj, $emailBody, $headers );
		} catch (Exception $e) {

		}

	}

	if ( !empty( $webinar_data->webinar_lang ) ) { restore_previous_locale(); }

}

function webinarignition_get_fb_id( $ID, $EMAIL ) {
	// Get ID for the FB Lead
	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition_leads";
	$findstat      = $wpdb->get_row( "SELECT * FROM $table_db_name WHERE app_id = '$ID' AND email = '$EMAIL' ", OBJECT );

	return $findstat->ID;
}

// Track View - LANDING PAGE
add_action( 'wp_ajax_nopriv_webinarignition_track_lp_view', 'webinarignition_track_lp_view_callback' );
add_action( 'wp_ajax_webinarignition_track_lp_view', 'webinarignition_track_lp_view_callback' );
function webinarignition_track_lp_view_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );

	$ID = sanitize_text_field( $post_input['id'] );

	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition";
	$findstat      = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM '$table_db_name' WHERE 'id' = %d", $ID ), OBJECT );

	$wpdb->update( $table_db_name, array(
		'total_views' => $findstat->total_views + 1
	), array( 'id' => $ID )
	);
}

// ADD NEW QUESTION
add_action( 'wp_ajax_nopriv_webinarignition_submit_question', 'webinarignition_submit_question_callback' );
add_action( 'wp_ajax_webinarignition_submit_question', 'webinarignition_submit_question_callback' );
function webinarignition_submit_question_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );

	$timezone_string = get_option('timezone_string');

	if( !empty( $timezone_string ) ){
		date_default_timezone_set($timezone_string);
	}

	$created =  date("Y-m-d h:i:sa");

	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition_questions";

	$post_input['name']         = isset( $post_input['name'] )        ? sanitize_text_field( $post_input['name'] ) : null;
	$post_input['email']        = isset( $post_input['email'] )       ? sanitize_email( $post_input['email'] ) : null;
	$post_input['id']           = isset( $post_input['id'] )          ? sanitize_text_field( $post_input['id'] ) : null;
	$post_input['question']     = isset( $post_input['question'] )    ? sanitize_text_field( $post_input['question'] ) : null;
	$post_input['lead']         = isset( $post_input['lead'] )        ? sanitize_text_field( $post_input['lead'] ) : null;
	$post_input['webinar_type']         = isset( $post_input['webinar_type'] )    ? sanitize_text_field( $post_input['webinar_type'] ) : null;
	$post_input['webinarTime']         = isset( $post_input['webinarTime'] )    ? sanitize_text_field( $post_input['webinarTime'] ) : null;
	$post_input['is_first_question']    = ( $post_input['is_first_question'] == true )  ? true : false;

	$data = [
		'app_id' => $post_input['id'],
		'name' => $post_input['name'],
		'email' => $post_input['email'],
		'question' => $post_input['question'],
		'type' => 'question',
		'status' => 'live',
		'created' => current_time( 'mysql' ),
		'webinarTime' => $post_input['webinarTime'],
	];

	$id = WebinarignitionQA::create_question($data);

	$data['webinar_type'] = $post_input['webinar_type'];
	$data['is_first_question'] = $post_input['is_first_question'];

	do_action( 'webinarignition_question_asked', $data);

	wp_send_json( $id );
}


add_action( 'webinarignition_question_asked', 'webinarignition_send_after_question_live_support_request');

function webinarignition_send_after_question_live_support_request( $supportData ) {

	$webinar_data                   = WebinarignitionManager::get_webinar_data( $supportData['app_id'] );

	if ( $applang = $webinar_data->webinar_lang ) {
		switch_to_locale( $applang );
		unload_textdomain( 'webinarignition' );
		load_textdomain( 'webinarignition', WEBINARIGNITION_PATH . 'languages/webinarignition-' . $applang . '.mo' );
	}

	if( $webinar_data->webinar_date == 'AUTO' || !WebinarignitionPowerups::is_multiple_support_enabled($webinar_data) ){
		return;
	}

	$send_question_notification     = false;

	if( isset( $webinar_data->enable_first_question_notification ) && ($webinar_data->enable_first_question_notification == 'yes') && ( $webinar_data->first_question_notification_sent == 'no' ) ){
		$send_question_notification  = true;
	}

	if( $send_question_notification  && isset( $webinar_data->support_staff_count ) && ( !empty( $webinar_data->support_staff_count ) ) ){

		for( $x=1; $x<= $webinar_data->support_staff_count; $x++ ){

			$member_email                   = "member_email_" . $x;

			if( property_exists($webinar_data, $member_email )  ){

				$qstn_notification_email_body   = $webinar_data->qstn_notification_email_body;
				$emailSubj                      = $webinar_data->qstn_notification_email_sbj;

				$member                         = get_user_by( 'email',  $webinar_data->{"member_email_".$x});

				if( is_object( $member ) ){

					$email_data                     = new stdClass();

					$_wi_support_token              =   get_user_meta( $member->ID, '_wi_support_token', true );
					$support_link                   =   $webinar_data->webinar_permalink . '?console&_wi_support_token='.$_wi_support_token . '#/questions';

					$replacement                    = [ $member->first_name, $supportData['name'], $webinar_data->webinar_desc, $support_link ];
					$replace                        = [ '{support}', '{attendee}', '{webinarTitle}', '{link}' ];
					$email_data->bodyContent        = str_replace($replace, $replacement, $qstn_notification_email_body);
					$email_data->footerContent      = ( !empty( $webinar_data->show_or_hide_local_qstn_answer_email_footer )  &&  ( $webinar_data->show_or_hide_local_qstn_answer_email_footer == 'show' )  ) ?  $webinar_data->qstn_answer_email_footer : '';

					$email_data->email_subject      = __( 'Questions From Your Webinar', 'webinarignition' );
					$email_data->emailheading       = __( 'Questions From Your Webinar', 'webinarignition' );
					$email_data->emailpreview       = __( 'Questions From Your Webinar', 'webinarignition' );

					$email                          = new WI_Emails();
					$emailBody                      = $email->build_email( $email_data );

					$headers                = array('Content-Type: text/html; charset=UTF-8');

					try {
						if ( ! wp_mail( $member->user_email, $emailSubj, $emailBody, $headers) ) {

							WebinarIgnition_Logs::add( __( "Support request email could not be sent to", "webinarignition") ." {$member->email}", WebinarIgnition_Logs::LIVE_EMAIL );

						} else {

							if( property_exists($webinar_data, 'first_question_notification_sent') &&  ( $webinar_data->first_question_notification_sent == 'no' ) ){

								$webinar_data->first_question_notification_sent = 'yes';
								update_option( 'webinarignition_campaign_' . $supportData['app_id'], $webinar_data );
								WebinarIgnition_Logs::add( __( "Support request has been sent.", "webinarignition"), $supportData['app_id'], WebinarIgnition_Logs::LIVE_EMAIL );
							}

						}
					} catch (Exception $e) {
						WebinarIgnition_Logs::add( __( "Support request email could not be sent to", "webinarignition"). " {$member->user_email}", WebinarIgnition_Logs::LIVE_EMAIL );
					}

				}

			}

		}

	}

	if( $send_question_notification && isset($webinar_data->send_host_questions_notifications) && ($webinar_data->send_host_questions_notifications == 'yes')  && isset( $webinar_data->host_questions_notifications_email ) ){
		if (filter_var($webinar_data->host_questions_notifications_email, FILTER_VALIDATE_EMAIL)) {
			$qstn_notification_email_body   = $webinar_data->qstn_notification_email_body;
			$emailSubj                      = $webinar_data->qstn_notification_email_sbj;
			$support_link                   = $webinar_data->webinar_permalink . '/?console#/questions';

			$replacement                    = [ $webinar_data->webinar_host, $supportData['name'], $webinar_data->webinar_desc, $support_link ];
			$replace                        = [ '{support}', '{attendee}', '{webinarTitle}', '{link}' ];

			$email_data->bodyContent        = str_replace($replace, $replacement, $qstn_notification_email_body);
			$email_data->footerContent      = ( !empty( $webinar_data->show_or_hide_local_qstn_answer_email_footer )  &&  ( $webinar_data->show_or_hide_local_qstn_answer_email_footer == 'show' )  ) ?  $webinar_data->qstn_answer_email_footer : '';
			//$email_data->email_subject      = $webinar_data->qstn_notification_email_sbj;
			$email_data->email_subject      = __( 'Questions From Your Webinar', 'webinarignition' );
			$email_data->emailheading       = __( 'Questions From Your Webinar', 'webinarignition' );
			$email_data->emailpreview       = __( 'Questions From Your Webinar', 'webinarignition' );

			$wi_emails                      = new WI_Emails();
			$emailBody                      = $email->build_email( $email_data );

			$headers                = array('Content-Type: text/html; charset=UTF-8');

			try {
				if ( ! wp_mail( $webinar_data->host_questions_notifications_email, $emailSubj, $emailBody, $headers) ) {

					WebinarIgnition_Logs::add( __( "Support request email to webinar host could not be sent", "webinarignition"), WebinarIgnition_Logs::LIVE_EMAIL );

				}
			} catch (Exception $e) {
				WebinarIgnition_Logs::add( __( "Support request email to webinar host could not be sent.", "webinarignition"), WebinarIgnition_Logs::LIVE_EMAIL );
			}
		}
	}

	if ( !empty( $webinar_data->webinar_lang ) ) { restore_previous_locale(); }

}

add_action( 'webinarignition_question_asked', 'webinarignition_send_after_question_auto_support_request');

function webinarignition_send_after_question_auto_support_request( $supportData ) {

	$webinar_data                   = WebinarignitionManager::get_webinar_data( $supportData['app_id'] );

	if ( $applang = $webinar_data->webinar_lang ) {
		switch_to_locale( $applang );
		unload_textdomain( 'webinarignition' );
		load_textdomain( 'webinarignition', WEBINARIGNITION_PATH . 'languages/webinarignition-' . $applang . '.mo' );
	}

	if ( !WebinarignitionPowerups::is_multiple_support_enabled($webinar_data) || ($webinar_data->webinar_date == 'AUTO' && ! $supportData['is_first_question'] ) ) {
		return;
	}

	$send_question_notification     = false;

	if( isset( $webinar_data->enable_first_question_notification ) && ($webinar_data->enable_first_question_notification == 'yes') ){
		$send_question_notification  = true;
	}

	if( $send_question_notification  && isset( $webinar_data->support_staff_count ) && ( !empty( $webinar_data->support_staff_count ) ) ){

		for( $x=1; $x<= $webinar_data->support_staff_count; $x++ ){

			$member_email                   = "member_email_" . $x;

			if( property_exists($webinar_data, $member_email )  ){

				$qstn_notification_email_body   = $webinar_data->qstn_notification_email_body;
				$emailSubj                      = $webinar_data->qstn_notification_email_sbj;

				$member                         = get_user_by( 'email',  $webinar_data->{"member_email_".$x});

				if( is_object( $member ) ){

					$_wi_support_token      =   get_user_meta( $member->ID, '_wi_support_token', true );
					$support_link           =   $webinar_data->webinar_permalink . '?console&_wi_support_token='.$_wi_support_token . '#/questions';

					$replacement            = [ $member->first_name, $supportData['name'], $webinar_data->webinar_desc, $support_link ];
					$replace                = [ '{support}', '{attendee}', '{webinarTitle}', '{link}' ];

					$email_data                     = new stdClass();
					$email_data->bodyContent        = str_replace($replace, $replacement, $qstn_notification_email_body);
					$email_data->footerContent      = ( !empty( $webinar_data->show_or_hide_local_qstn_answer_email_footer )  &&  ( $webinar_data->show_or_hide_local_qstn_answer_email_footer == 'show' )  ) ?  $webinar_data->qstn_answer_email_footer : '';
					$email_data->email_subject      = $webinar_data->qstn_notification_email_sbj;
					$email                          = new WI_Emails();
					$emailBody                      = $email->build_email( $email_data );

					$headers                = array('Content-Type: text/html; charset=UTF-8');

					try {
						if ( ! wp_mail( $member->user_email, $emailSubj, $emailBody, $headers) ) {

							WebinarIgnition_Logs::add( __( "Support request email could not be sent to", "webinarignition")." {$member->email}", WebinarIgnition_Logs::LIVE_EMAIL );

						} else {

							if( property_exists($webinar_data, 'first_question_notification_sent') &&  ( $webinar_data->first_question_notification_sent == 'no' ) ){

								$webinar_data->first_question_notification_sent = 'yes';
								update_option( 'webinarignition_campaign_' . $supportData['app_id'], $webinar_data );
								WebinarIgnition_Logs::add( __( "Support request has been sent.", "webinarignition"), $supportData['app_id'], WebinarIgnition_Logs::LIVE_EMAIL );
							}

						}
					} catch (Exception $e) {
						WebinarIgnition_Logs::add( __( "Support request email could not be sent to", "webinarignition")." {$member->user_email}", WebinarIgnition_Logs::LIVE_EMAIL );
					}

				}
			}
		}
	}

	if( $send_question_notification && isset($webinar_data->send_host_questions_notifications) && ($webinar_data->send_host_questions_notifications == 'yes')  && isset( $webinar_data->host_questions_notifications_email ) ){

		if (filter_var($webinar_data->host_questions_notifications_email, FILTER_VALIDATE_EMAIL)) {

			$qstn_notification_email_body   = $webinar_data->qstn_notification_email_body;
			$emailSubj                      = $webinar_data->qstn_notification_email_sbj;
			$support_link                   = $webinar_data->webinar_permalink . '/?console#/questions';

			$replacement            = [ $webinar_data->webinar_host, $supportData['name'], $webinar_data->webinar_desc, $support_link ];
			$replace                = [ '{support}', '{attendee}', '{webinarTitle}', '{link}' ];

			$email_data                     = new stdClass();
			$email_data->bodyContent        = str_replace($replace, $replacement, $qstn_notification_email_body);
			$email_data->footerContent      = ( !empty( $webinar_data->show_or_hide_local_qstn_answer_email_footer )  &&  ( $webinar_data->show_or_hide_local_qstn_answer_email_footer == 'show' )  ) ?  $webinar_data->qstn_answer_email_footer : '';
			$email_data->email_subject      = $webinar_data->qstn_notification_email_sbj;
			$email                          = new WI_Emails();
			$emailBody                      = $email->build_email( $email_data );

			$headers                = array('Content-Type: text/html; charset=UTF-8');

			try {
				if ( ! wp_mail( $webinar_data->host_questions_notifications_email, $emailSubj, $emailBody, $headers) ) {

					WebinarIgnition_Logs::add( __( "Support request email to webinar host could not be sent", "webinarignition"), WebinarIgnition_Logs::LIVE_EMAIL );

				}
			} catch (Exception $e) {
				WebinarIgnition_Logs::add( __( "Support request email to webinar host could not be sent.", "webinarignition"), WebinarIgnition_Logs::LIVE_EMAIL );
			}
		}
	}

	if ( !empty( $webinar_data->webinar_lang ) ) { restore_previous_locale(); }

}

add_action( 'wp_ajax_webinarignition_delete_question', 'webinarignition_delete_question_callback' );
function webinarignition_delete_question_callback() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	global $wpdb;
	$ID            = sanitize_text_field( $post_input['id'] );
	$data = [
		'ID' => $ID,
		'status' => 'deleted',
	];

	$result        = WebinarignitionQA::create_question($data);

	if( $result ){
		WebinarignitionQA::delete_answers($ID);
		$message = __( 'Question successfully deleted', "webinarignition");
		wp_send_json_success( [ 'success' => true, 'message' => $message ] );
	}

}

add_action( 'wp_ajax_webinarignition_delete_lead', 'webinarignition_delete_lead_callback' );
function webinarignition_delete_lead_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition_leads";
	$table_meta_db_name = $wpdb->prefix . 'webinarignition_leadmeta';
	$ID            = sanitize_text_field( $post_input['id'] );

	if( $wpdb->delete( $table_db_name, ['ID'=>$ID]) ){
		$message = 'lead ' . $ID . ' deleted';
		$wpdb->query( "DELETE FROM $table_meta_db_name WHERE lead_id = '$ID'" );
		wp_send_json_success( ['success' => true, 'message' => $message] );
	}
}

add_action( 'wp_ajax_webinarignition_delete_lead_auto', 'webinarignition_delete_lead_auto_callback' );
function webinarignition_delete_lead_auto_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition_leads_evergreen";
	$table_meta_db_name = $wpdb->prefix . 'webinarignition_lead_evergreenmeta';
	$ID            = sanitize_text_field( $post_input['id'] );
	$wpdb->query( "DELETE FROM $table_db_name WHERE id = '$ID'" );
	$wpdb->query( "DELETE FROM $table_meta_db_name WHERE lead_id = '$ID'" );
}

add_action( 'wp_ajax_webinarignition_reset_stats', 'webinarignition_reset_stats_callback' );
function webinarignition_reset_stats_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition";
	$ID            = sanitize_text_field( $post_input['id'] );

	$wpdb->update( $table_db_name, array(
		'total_lp'     => '0%%0',
		'total_ty'     => '0%%0',
		'total_live'   => '0%%0',
		'total_replay' => '0%%0'
	), array( 'id' => $ID )
	);
}

// COUNTDOWN - EXPIRE -- UPDATE TO LIVE
add_action( 'wp_ajax_nopriv_webinarignition_update_to_live', 'webinarignition_update_to_live_callback' );
add_action( 'wp_ajax_webinarignition_update_to_live', 'webinarignition_update_to_live_callback' );
function webinarignition_update_to_live_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	$ID         = sanitize_text_field( $post_input['id'] );
	$results = WebinarignitionManager::get_webinar_data($ID);
	// update status
	$results->webinar_switch = "live";
	// save
	update_option( 'webinarignition_campaign_' . $ID, $results );
}


add_action( 'wp_ajax_nopriv_webinarignition_get_master_switch_status', 'webinarignition_get_master_switch_status_callback' );
add_action( 'wp_ajax_webinarignition_get_master_switch_status', 'webinarignition_get_master_switch_status_callback' );

function webinarignition_get_master_switch_status_callback() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input         = filter_input_array( INPUT_POST );
	$ID                 = sanitize_text_field( $post_input['id'] );
	$webinar_data       = WebinarignitionManager::get_webinar_data($ID);

	wp_send_json(['webinar_switch_status'  => $webinar_data->webinar_switch]);
}

// TRACK VIEW
add_action( 'wp_ajax_nopriv_webinarignition_track_view', 'webinarignition_track_view_callback' );
add_action( 'wp_ajax_webinarignition_track_view', 'webinarignition_track_view_callback' );
function webinarignition_track_view_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	// Campaign ID
	$ID   = sanitize_text_field( $post_input['id'] );
	$PAGE = sanitize_text_field( $post_input['page'] );

	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition";
	$findstat      = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM '$table_db_name' WHERE 'id' = %d", $ID ), OBJECT );

	if ( $PAGE == "lp" ) {
		// LANDING PAGE
		$getData   = $findstat->total_lp;
		$getData   = explode( "%%", $getData );
		$getUnique = $getData[0] + 1;
		$getTotal  = $getData[1];
		$wpdb->update( $table_db_name, array(
			'total_lp' => $getUnique . "%%" . $getTotal
		), array( 'id' => $ID )
		);
	} else if ( $PAGE == "ty" ) {
		// THANK YOU PAGE
		$getData   = $findstat->total_ty;
		$getData   = explode( "%%", $getData );
		$getUnique = $getData[0] + 1;
		$getTotal  = $getData[1];
		$wpdb->update( $table_db_name, array(
			'total_ty' => $getUnique . "%%" . $getTotal
		), array( 'id' => $ID )
		);
	} else if ( $PAGE == "live" ) {
		// LIVE
		$getData   = $findstat->total_live;
		$getData   = explode( "%%", $getData );
		$getUnique = $getData[0] + 1;
		$getTotal  = $getData[1];
		$wpdb->update( $table_db_name, array(
			'total_live' => $getUnique . "%%" . $getTotal
		), array( 'id' => $ID )
		);
	} else if ( $PAGE == "replay" ) {
		// REPLAY
		$getData   = $findstat->total_replay;
		$getData   = explode( "%%", $getData );
		$getUnique = $getData[0] + 1;
		$getTotal  = $getData[1];
		$wpdb->update( $table_db_name, array(
			'total_replay' => $getUnique . "%%" . $getTotal
		), array( 'id' => $ID )
		);
	}
}

// TRACK VIEW
add_action( 'wp_ajax_nopriv_webinarignition_track_view_total', 'webinarignition_track_view_total_callback' );
add_action( 'wp_ajax_webinarignition_track_view_total', 'webinarignition_track_view_total_callback' );
function webinarignition_track_view_total_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	// Campaign ID
	$ID   = sanitize_text_field( $post_input['id'] );
	$PAGE = sanitize_text_field( $post_input['page'] );

	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition";
	$findstat      = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM '$table_db_name' WHERE 'id' = %d", $ID ), OBJECT );

	if ( $PAGE == "lp" ) {
		// LANDING PAGE
		$getData   = $findstat->total_lp;
		$getData   = explode( "%%", $getData );
		$getUnique = $getData[0];
		$getTotal  = $getData[1] + 1;
		$wpdb->update( $table_db_name, array(
			'total_lp' => $getUnique . "%%" . $getTotal
		), array( 'id' => $ID )
		);
	} else if ( $PAGE == "ty" ) {
		// THANK YOU PAGE
		$getData   = $findstat->total_ty;
		$getData   = explode( "%%", $getData );
		$getUnique = $getData[0];
		$getTotal  = $getData[1] + 1;
		$wpdb->update( $table_db_name, array(
			'total_ty' => $getUnique . "%%" . $getTotal
		), array( 'id' => $ID )
		);
	} else if ( $PAGE == "live" ) {
		// LIVE
		$getData   = $findstat->total_live;
		$getData   = explode( "%%", $getData );
		$getUnique = $getData[0];
		$getTotal  = $getData[1] + 1;
		$wpdb->update( $table_db_name, array(
			'total_live' => $getUnique . "%%" . $getTotal
		), array( 'id' => $ID )
		);
	} else if ( $PAGE == "replay" ) {
		// REPLAY
		$getData   = $findstat->total_replay;
		$getData   = explode( "%%", $getData );
		$getUnique = $getData[0];
		$getTotal  = $getData[1] + 1;
		$wpdb->update( $table_db_name, array(
			'total_replay' => $getUnique . "%%" . $getTotal
		), array( 'id' => $ID )
		);
	}
}

// TRACK LIVE ATTEND
add_action( 'wp_ajax_nopriv_webinarignition_update_view_status', 'webinarignition_update_view_status_callback' );
add_action( 'wp_ajax_webinarignition_update_view_status', 'webinarignition_update_view_status_callback' );
function webinarignition_update_view_status_callback() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );

	$post_input   = filter_input_array( INPUT_POST );

	$lead_id      = sanitize_text_field( $post_input['lead_id'] );
	$webinar_data = WebinarignitionManager::get_webinar_data( absint( $post_input['id']) );

	$webinar_started = (webinarignition_should_use_videojs( $webinar_data ) && isset($_COOKIE["videoResumeTime-{$lead_id}"])) || !webinarignition_should_use_videojs( $webinar_data );
	$updated = false;
	if ( !empty($lead_id) && !empty($webinar_data) && $webinar_started ) {
		$updated = webinarignition_update_webinar_lead_status($webinar_data->webinar_date, $lead_id);
	}

	wp_send_json_success( [ 'message' => __( "Data updated successfully", "webinarignition" ) ] );
}

// GET QA -- NAME AND EMAIL
add_action( 'wp_ajax_nopriv_webinarignition_get_qa_name_email', 'webinarignition_get_qa_name_email_callback' );
add_action( 'wp_ajax_webinarignition_get_qa_name_email', 'webinarignition_get_qa_name_email_callback' );
function webinarignition_get_qa_name_email_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	// Get Variables
	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition_leads";
	$cookieStatus  = sanitize_text_field( $post_input['cookie'] );
	$IP            = sanitize_text_field( $post_input['ip'] );

	if ( empty( $cookieStatus ) ) {
		// No Cookie Found -- Try IP
		$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_db_name WHERE trk3 = %s", $IP ), OBJECT );

		if ( empty( $data ) ) {
			// No IP Found - Do Nothing...

		} else {
			// IP Found - GET NAME / EMAIL
			echo $data->name . "//" . $data->email . "//" . $data->ID;
		}
	} else {
		// Cookie Was Found - Get Info
		$data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_db_name WHERE id = %d", $cookieStatus ), OBJECT );
		if ( is_object( $data ) ):
			echo $data->name . "//" . $data->email . "//" . $data->ID;
		endif;

	}

	die();
}

// GET QA -- NAME AND EMAIL AUTO
add_action( 'wp_ajax_nopriv_webinarignition_get_qa_name_email2', 'webinarignition_get_qa_name_email2_callback' );
add_action( 'wp_ajax_webinarignition_get_qa_name_email2', 'webinarignition_get_qa_name_email2_callback' );
function webinarignition_get_qa_name_email2_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	// Get Variables
	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition_leads_evergreen";
	$cookieStatus  = sanitize_text_field( $post_input['cookie'] );

	if ( ! empty( $cookieStatus ) ):
		$data = $wpdb->get_row( "SELECT * FROM $table_db_name WHERE id = '$cookieStatus' ", OBJECT );
	endif;

	if ( is_object( $data ) ):
		echo $data->name . "//" . $data->email . "//" . $data->ID;
	endif;


	die();
}

// RESET STATS
//add_action('wp_ajax_nopriv_webinarignition_update_master_switch', 'webinarignition_update_master_switch_callback');
add_action( 'wp_ajax_webinarignition_update_master_switch', 'webinarignition_update_master_switch_callback' );
function webinarignition_update_master_switch_callback() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	$ID         = sanitize_text_field( $post_input['id'] );
	$status     = sanitize_text_field( $post_input['status'] );

	// Return Option Object:
	$results = WebinarignitionManager::get_webinar_data($ID);
	$results->webinar_switch = $status;

	update_option( 'webinarignition_campaign_' . $ID, $results );
}

// SAVE AIR MESSAGE
add_action( 'wp_ajax_nopriv_webinarignition_save_air', 'webinarignition_save_air_callback' );
add_action( 'wp_ajax_webinarignition_save_air', 'webinarignition_save_air_callback' );
function webinarignition_save_air_callback() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	$ID         = sanitize_text_field( $post_input['id'] );

	// Return Option Object:
	$results = WebinarignitionManager::get_webinar_data($ID);
	$results->air_toggle    = sanitize_text_field( $post_input['toggle'] );
	$results->air_btn_copy  = sanitize_text_field( $post_input['btncopy'] );
	$results->air_btn_url   = sanitize_text_field( $post_input['btnurl'] );
	$results->air_btn_color = sanitize_text_field( $post_input['btncolor'] );
	$results->air_html      = $post_input['html'];

	update_option( 'webinarignition_campaign_' . $ID, $results );
}

add_action( 'wp_ajax_nopriv_webinarignition_track_order', 'webinarignition_track_order_callback' );
add_action( 'wp_ajax_webinarignition_track_order', 'webinarignition_track_order_callback' );
function webinarignition_track_order_callback() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	global $wpdb;
	$post_input           = filter_input_array( INPUT_POST );
	$post_input['lead']   = isset( $post_input['lead'] )  ? sanitize_text_field( $post_input['lead'] ) : null;

	if ( empty( $post_input['id'] ) || empty( $post_input['lead'] ) ) {
		wp_send_json( 'invalid webinar or lead id' );
	}

	$webinarData = WebinarignitionManager::get_webinar_data($post_input['id']);
	if ( empty( $webinarData ) ) {
		wp_send_json( 'webinar not found: ' . $post_input['id'] );
	}

	$table_db_name = webinarignition_is_auto( $webinarData ) ? $wpdb->prefix . "webinarignition_leads_evergreen" : $wpdb->prefix . "webinarignition_leads";

	$updated = $wpdb->update( $table_db_name, array( 'trk2' => 'Yes' ), array( 'id' => $post_input['lead'] ) );

	if( !empty($updated) ) {
		do_action( 'webinarignition_lead_purchased', $post_input['lead'], $post_input['id'] );
	}

	wp_send_json( 'tracked lead' );
}

// Store New / Add Phone Number webinarignition_store_phone
add_action( 'wp_ajax_nopriv_webinarignition_store_phone', 'webinarignition_store_phone_callback' );
add_action( 'wp_ajax_webinarignition_store_phone', 'webinarignition_store_phone_callback' );
function webinarignition_store_phone_callback() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	// Get Variables
	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition_leads";

	$ID    = sanitize_text_field( $post_input['id'] );
	$PHONE = sanitize_text_field( $post_input['phone'] );

	$sql = $wpdb->prepare( "SELECT * FROM '$table_db_name' WHERE 'id' = %d", $ID );
	$lead = $wpdb->get_row($sql, OBJECT);

	if (empty($lead)) {
		$sql = $wpdb->prepare( "SELECT * FROM '$table_db_name' WHERE 'hash_ID' = %d", $ID );
		$lead = $wpdb->get_row($sql, OBJECT);
	}

	if (!empty($lead)) {
		$ID = $lead->ID;
	}

	// Set Phone Number
	$wpdb->update( $table_db_name, array(
		'phone' => $PHONE
	), array( 'id' => $ID )
	);
}

// Store New / Add Phone Number webinarignition_store_phone
add_action( 'wp_ajax_nopriv_webinarignition_store_phone_auto', 'webinarignition_store_phone_auto_callback' );
add_action( 'wp_ajax_webinarignition_store_phone_auto', 'webinarignition_store_phone_auto_callback' );
function webinarignition_store_phone_auto_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	// Get Variables
	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition_leads_evergreen";

	$ID    = sanitize_text_field( $post_input['id'] );
	$PHONE = sanitize_text_field( $post_input['phone'] );

	$sql = $wpdb->prepare( "SELECT * FROM '$table_db_name' WHERE 'id' = %d", $ID );
	$lead = $wpdb->get_row($sql, OBJECT);

	if (empty($lead)) {
		$sql = $wpdb->prepare( "SELECT * FROM '$table_db_name' WHERE 'hash_ID' = %d", $ID );
		$lead = $wpdb->get_row($sql, OBJECT);
	}

	if (!empty($lead)) {
		$ID = $lead->ID;
	}

	// Set Phone Number
	$wpdb->update( $table_db_name, array(
		'phone' => $PHONE
	), array( 'id' => $ID )
	);
}

// Get Timezone & Local Time For Users
add_action( 'wp_ajax_nopriv_webinarignition_get_local_tz', 'webinarignition_get_local_tz_callback' );
add_action( 'wp_ajax_webinarignition_get_local_tz', 'webinarignition_get_local_tz_callback' );
function webinarignition_get_local_tz_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	// Get Olson Time ::
	$timezone = sanitize_text_field( $post_input['tz'] );

	date_default_timezone_set( $timezone );
	$dtz           = new DateTimeZone( $timezone );
	$time_in_sofia = new DateTime( 'now', $dtz );
	$offset        = $dtz->getOffset( $time_in_sofia ) / 3600;

	echo "<i class='icon-globe' style='margin-right: 10px;' ></i> <b>UTC</b> :: " . ( $offset < 0 ? $offset : "+" . $offset ) . "<i class='icon-time' style='margin-left: 10px; margin-right:10px;' ></i>  <b>".__( "Local Time", "webinarignition")."</b> :: " . date( 'g:i A' );
	die();
}

// Get Timezone & Local Time For Users
add_action( 'wp_ajax_nopriv_webinarignition_get_local_tz_set', 'webinarignition_get_local_tz_set_callback' );
add_action( 'wp_ajax_webinarignition_get_local_tz_set', 'webinarignition_get_local_tz_set_callback' );
function webinarignition_get_local_tz_set_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	// Get Olson Time ::
	$timezone = sanitize_text_field( $post_input['tz'] );
	date_default_timezone_set( $timezone );
	$dtz           = new DateTimeZone( $timezone );
	$time_in_sofia = new DateTime( 'now', $dtz );
	$offset        = $dtz->getOffset( $time_in_sofia ) / 3600;

	$set = ( $offset < 0 ? $offset : "+" . $offset );
	// ReFormat UTC - GMT and half'rs
	if ( $set == "+0" ) {
		$set = "0";
	} else if ( $set == "-9.5" ) {
		$set = "-930";
	} else if ( $set == "-4.5" ) {
		$set = "-430";
	} else if ( $set == "+5.5" ) {
		$set = "+530";
	} else if ( $set == "+5.75" ) {
		$set = "+545";
	} else if ( $set == "+6.5" ) {
		$set = "+630";
	} else if ( $set == "+9.5" ) {
		$set = "+930";
	}
	echo $set;
	die();
}

// UNLOCK
add_action( 'wp_ajax_nopriv_webinarignition_unlock', 'webinarignition_unlock_callback' );
add_action( 'wp_ajax_webinarignition_unlock', 'webinarignition_unlock_callback' );
function webinarignition_unlock_callback() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input  = filter_input_array( INPUT_POST );
	$username    = sanitize_text_field( $post_input["username"] );
	$license_key = sanitize_text_field( $post_input["key"] );

	if ( ( $username == 'dks' ) && ( $license_key == 'seropt4n0zxdfkv' ) ) {
		WebinarignitionLicense::webinarignition_activate( $license_key );

		$return = array(
			'message' => __( "Activation Successful", "webinarignition"),
			'status'  => 1,
			'success' => true
		);

		wp_send_json_success( $return );

	}

	$dk_activation_url = WebinarignitionLicense::get_activation_url() . "?username={$username}&key={$license_key}";

	$resp = wp_remote_get( $dk_activation_url, array(
		'user-agent' => 'WI',
		'timeout'    => 60
	) );


	$resp = json_decode( $resp['body'] );

	if ( is_object( $resp ) && ( $resp->result == 'KeyFound' ) ):

		WebinarignitionLicense::webinarignition_activate( $license_key, $resp );

		$return = array(
			'message' => __( "Activation Successful", "webinarignition"),
			'status'  => 1,
			'success' => true
		);

		wp_send_json_success( $return );

	else:

		$return = array(
			'message' => $resp->result,
			'status'  => 1,
			'success' => false
		);

		wp_send_json( $return );


	endif;

}

// Reh csv upload
// Add CSV Lead
add_action( 'wp_ajax_nopriv_reh_wi_handle_csv_upload', 'reh_wi_handle_csv_upload_callback' );
add_action( 'wp_ajax_reh_wi_handle_csv_upload', 'reh_wi_handle_csv_upload_callback' );
if( !function_exists('reh_wi_handle_csv_upload_callback') ){
	function reh_wi_handle_csv_upload_callback() {
		// wp_send_json( [ 'All Data ' => $_POST ] );
		check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
		// $post_input = filter_input_array( INPUT_POST );
		global $wpdb;
		$app_id = (int) sanitize_text_field( $_POST["id"] );

		$table_db_name = $wpdb->prefix . "webinarignition_leads";

		$csv_array = [];
		if( isset($_FILES['csv_file']) ){

			if( isset($_FILES['csv_file']['tmp_name']) ){

				$whole_csv_file = $_FILES['csv_file']['tmp_name'];

				$file_info = pathinfo($whole_csv_file);

				if ( isset( $_FILES['csv_file']['type'] ) && $_FILES['csv_file']['type'] !== 'text/csv') {
					wp_send_json_error('Invalid file format. Only CSV files are allowed.');
				}

				$upload_dir = wp_upload_dir();
				$target_dir = $upload_dir['basedir'] . '/csv-files/';

				if (!is_dir($target_dir)) {
					wp_mkdir_p($target_dir);
				}

				$target_filename = uniqid('csv_', true) . '.txt';
				$target_path     = $target_dir . $target_filename;
				$webinar_data    = WebinarignitionManager::get_webinar_data($app_id);

                $time_format    = $webinar_data->time_format;
                $date_format    = !empty($webinar_data->date_format ) ? $webinar_data->date_format  : 'l, F j, Y';

				if (move_uploaded_file($whole_csv_file, $target_path)) {
					// wp_send_json_success($target_path);
					$csv_data = file_get_contents($target_path);
					$lines = explode("\n", $csv_data);
					$csv_array = array();
					$current_new_user_id = false;

					foreach ($lines as $line) {
						$row = str_getcsv($line);

						$csv_array[] = $row;
						// $csv_array[] = [ 'Email' => $row[2] , 'Data' => $row];
						$name  = trim( $row[0] );
						$email = trim( $row[1] );
						$phone = trim( $row[2] );

						if( ( empty( str_replace( ' ' , '' , $name ) ) && empty( str_replace( ' ' , '' , $email ) ) ) || ( 'name' == strtolower(str_replace( ' ' , '' , $name )) ) ){
							continue;
						}

						$lead = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $table_db_name WHERE email = %s AND app_id = %d", $email, $app_id ) );
						if ( $lead ) {
							$current_new_user_id = $lead->ID;
						} else {

							$wpdb->insert( $table_db_name, array(
								'app_id'  => sanitize_text_field( $app_id ),
								'name'    => sanitize_text_field( $name ),
								'email'   => sanitize_email( $email ),
								'phone'   => sanitize_text_field( $phone ),
								'trk1'    => "import",
								'trk3'    => "-",
								'event'   => 'No',
								'replay'  => 'No',
								'created' => date( 'F j, Y' )
							) );

							$new_lead_id = $wpdb->insert_id;
                            $hash_ID     = sha1($app_id . $email . $new_lead_id);

                            $wpdb->update($table_db_name, ['hash_ID' => $hash_ID], ['ID' => $new_lead_id]);

                            if( !empty( $webinar_data->templates_version ) || ( !empty( $webinar_data->use_new_email_signup_template )  && ( $webinar_data->use_new_email_signup_template == 'yes' ) ) ){
                                //use new templates
                                $webinar_data->emailheading     = $webinar_data->email_signup_heading;
                                $webinar_data->emailpreview     = $webinar_data->email_signup_preview;
                                $webinar_data->bodyContent      = $webinar_data->email_signup_body;
                                $webinar_data->footerContent    = ( property_exists($webinar_data, 'show_or_hide_local_email_signup_footer') && $webinar_data->show_or_hide_local_email_signup_footer == 'show' ) ? $webinar_data->local_email_signup_footer : '';

                                $wi_emails  = new WI_Emails();
                                $emailBody  = $wi_emails->build_email( $webinar_data );
                            } else {
                                //this is an old webinar, created before this version
                                $emailHead = WebinarignitionEmailManager::get_email_head();
                                $emailBody = $emailHead;
                                $emailBody .= $webinar_data->email_signup_body;
                                $emailBody .= '</html>';
                            }

                            $emailBody = str_replace( "{LEAD_NAME}", ( ! empty( $name ) ? sanitize_text_field( $name ) : '' ), $emailBody );
                            $emailBody = str_replace( "{FIRSTNAME}", ( ! empty( $name ) ? sanitize_text_field( $name ) : '' ), $emailBody );

                            $localized_date = webinarignition_get_localized_date( $webinar_data );

							$timeonly  = ( empty($webinar_data->display_tz ) || ( !empty($webinar_data->display_tz) && ($webinar_data->display_tz == 'yes') ) ) ? false : true;
							// Replace
                            $emailBody = str_replace( "{DATE}", $localized_date . " @ " . webinarignition_get_time_tz( $webinar_data->webinar_start_time, $time_format, $webinar_data->webinar_timezone, false, $timeonly ), $emailBody );

							$emailBody = WebinarignitionManager::replace_email_body_placeholders($webinar_data, $new_lead_id, $emailBody);

							$email_signup_sbj = str_replace( "{TITLE}", $webinar_data->webinar_desc, $webinar_data->email_signup_sbj );
							$headers          = array('Content-Type: text/html; charset=UTF-8');

                            webinarignition_test_smtp_options();

                            try {
                                if ( ! wp_mail( $email, $email_signup_sbj, $emailBody, $headers) ) {
                                    WebinarIgnition_Logs::add( __( "Registration email could not be sent to", "webinarignition"). " {$email}", WebinarIgnition_Logs::LIVE_EMAIL );
                                } else {
                                    WebinarIgnition_Logs::add( __( "Registration email has been sent.", "webinarignition"), $new_lead_id, WebinarIgnition_Logs::LIVE_EMAIL );
                                }
                            } catch (Exception $e) {
                                WebinarIgnition_Logs::add( __( "Registration email could not be sent to", "webinarignition"). " {$email}", WebinarIgnition_Logs::LIVE_EMAIL );
                            }
						}
					}

				} else {
					wp_send_json_error('Failed to save the CSV file.');
				}

				if (file_exists($target_path)) {
					unlink($target_path);
				}

				wp_send_json( [ 'status' => true , 'data' => $csv_array ] );
			}
		}

		wp_send_json( [ 'status' => false ] );

	}
}

// Add CSV Lead
add_action( 'wp_ajax_nopriv_webinarignition_import_csv_leads', 'webinarignition_import_csv_leads_callback' );
add_action( 'wp_ajax_webinarignition_import_csv_leads', 'webinarignition_import_csv_leads_callback' );
function webinarignition_import_csv_leads_callback() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	global $wpdb;
	$app_id = (int) sanitize_text_field( $post_input["id"] );
    $webinar_data = WebinarignitionManager::get_webinar_data($app_id);

    $time_format    = $webinar_data->time_format;
    $date_format    = !empty($webinar_data->date_format ) ? $webinar_data->date_format  : 'l, F j, Y';

	$lines = explode(PHP_EOL, $post_input["csv"]);
	$leads = [];
	foreach ($lines as $line) {
		$leads[] = str_getcsv($line);
	}

	$table_db_name = $wpdb->prefix . "webinarignition_leads";

	foreach ( $leads as $key => $lead ) {

		$name  = trim( $lead[0] );
		$email = trim( $lead[1] );
		$phone = trim( $lead[2] );

		if( strtolower($email) == 'email' ) {
		    continue;
        }

		$lead = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $table_db_name WHERE email = %s AND app_id = %d", $email, $app_id ) );

		if ( $lead ) {
			echo $lead->ID;
		} else {
			$wpdb->insert( $table_db_name, array(
				'app_id'  => sanitize_text_field( $app_id ),
				'name'    => sanitize_text_field( $name ),
				'email'   => sanitize_email( $email ),
				'phone'   => sanitize_text_field( $phone ),
				'trk1'    => "import",
				'trk3'    => "-",
				'event'   => 'No',
				'replay'  => 'No',
				'created' => date( 'F j, Y' )
			) );

            $new_lead_id = $wpdb->insert_id;
            $hash_ID     = sha1($app_id . $email . $new_lead_id);

            $wpdb->update($table_db_name, ['hash_ID' => $hash_ID], ['ID' => $new_lead_id]);

            if( !empty( $webinar_data->templates_version ) || ( !empty( $webinar_data->use_new_email_signup_template )  && ( $webinar_data->use_new_email_signup_template == 'yes' ) ) ){
                //use new templates
                $webinar_data->emailheading     = $webinar_data->email_signup_heading;
                $webinar_data->emailpreview     = $webinar_data->email_signup_preview;
                $webinar_data->bodyContent      = $webinar_data->email_signup_body;
                $webinar_data->footerContent    = ( property_exists($webinar_data, 'show_or_hide_local_email_signup_footer') && $webinar_data->show_or_hide_local_email_signup_footer == 'show' ) ? $webinar_data->local_email_signup_footer : '';

                $wi_emails  = new WI_Emails();
                $emailBody  = $wi_emails->build_email( $webinar_data );
            } else {
                //this is an old webinar, created before this version
                $emailHead = WebinarignitionEmailManager::get_email_head();
                $emailBody = $emailHead;
                $emailBody .= $webinar_data->email_signup_body;
                $emailBody .= '</html>';
            }

            $emailBody = str_replace( "{LEAD_NAME}", ( ! empty( $name ) ? sanitize_text_field( $name ) : '' ), $emailBody );
            $emailBody = str_replace( "{FIRSTNAME}", ( ! empty( $name ) ? sanitize_text_field( $name ) : '' ), $emailBody );

            $localized_date = webinarignition_get_localized_date( $webinar_data );

            $timeonly  = ( empty($webinar_data->display_tz ) || ( !empty($webinar_data->display_tz) && ($webinar_data->display_tz == 'yes') ) ) ? false : true;
            // Replace
            $emailBody = str_replace( "{DATE}", $localized_date . " @ " . webinarignition_get_time_tz( $webinar_data->webinar_start_time, $time_format, $webinar_data->webinar_timezone, false, $timeonly ), $emailBody );

            $emailBody = WebinarignitionManager::replace_email_body_placeholders($webinar_data, $new_lead_id, $emailBody);

            $email_signup_sbj = str_replace( "{TITLE}", $webinar_data->webinar_desc, $webinar_data->email_signup_sbj );

            $headers = array('Content-Type: text/html; charset=UTF-8');

            webinarignition_test_smtp_options();

            try {
                if ( ! wp_mail( $email, $email_signup_sbj, $emailBody, $headers) ) {
                    WebinarIgnition_Logs::add( __( "Registration email could not be sent to", "webinarignition"). " {$email}", WebinarIgnition_Logs::LIVE_EMAIL );
                } else {
                    WebinarIgnition_Logs::add( __( "Registration email has been sent.", "webinarignition"), $new_lead_id, WebinarIgnition_Logs::LIVE_EMAIL );
                }
            } catch (Exception $e) {
                WebinarIgnition_Logs::add( __( "Registration email could not be sent to", "webinarignition"). " {$email}", WebinarIgnition_Logs::LIVE_EMAIL );
            }
		}
	}
	die();
}

add_action( 'wp_ajax_nopriv_wi_show_logs_get', 'webinarignition_ajax_show_logs' );
add_action( 'wp_ajax_wi_show_logs_get', "webinarignition_ajax_show_logs" );
function webinarignition_ajax_show_logs() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );
	$webinar = WebinarignitionManager::get_webinar_data(sanitize_text_field( $post_input['campaign_id'] ));

	$log_types = array( WebinarIgnition_Logs::LIVE_EMAIL, WebinarIgnition_Logs::LIVE_SMS );
	if ( $webinar->webinar_date == 'AUTO' ) {
		$log_types                 = array( WebinarIgnition_Logs::AUTO_EMAIL, WebinarIgnition_Logs::AUTO_SMS );
		$webinar->webinar_timezone = false;
	}

	webinarignition_show_logs( $webinar->id, $log_types, sanitize_text_field( $post_input['page'] ), $webinar->timezone );
	die();
}

add_action( 'wp_ajax_nopriv_wi_delete_logs', 'webinarignition_ajax_delete_logs' );
add_action( 'wp_ajax_wi_delete_logs', "webinarignition_ajax_delete_logs" );
function webinarignition_ajax_delete_logs() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input = filter_input_array( INPUT_POST );

	$logs = WebinarIgnition_Logs::deleteCampaignLogs( sanitize_text_field( $post_input['campaign_id'] ) );

	return $logs;
	die();
}

function webinarignition_show_logs( $id, $log_types, $page, $timezone = false ) {
	$logs = WebinarIgnition_Logs::getLogs( $id, $log_types, $page, $timezone );
	?>
    <table>
        <tr>
            <th>Date</th>
            <th>Message</th>
        </tr>
		<?php foreach ( $logs as $log ) { ?>
            <tr>
                <td><?php echo $log->date; ?></td>
                <td><?php echo nl2br( $log->message ); ?></td>
            </tr>
		<?php } ?>
    </table>
	<?php WebinarIgnition_Logs::pagination( $id ); ?>
	<?php
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		die();
	}
}

add_action( 'wp_ajax_nopriv_webinarignition_broadcast_msg_poll_callback', 'webinarignition_broadcast_msg_poll_callback' );
add_action( 'wp_ajax_webinarignition_broadcast_msg_poll_callback', 'webinarignition_broadcast_msg_poll_callback' );
function webinarignition_broadcast_msg_poll_callback() {

	// check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	// $input_get = filter_input_array( INPUT_GET );
	$input_get = $_GET;
	$ID        = sanitize_text_field( $input_get['id'] );
	$IP        = sanitize_text_field( $input_get['ip'] );
	$LEAD_ID   =  sanitize_text_field( $input_get['lead_id'] );

	// Count User As Online -- User Tracking...
	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition_users_online";
	$lookUpIP      = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM '$table_db_name' WHERE 'app_id' = %d AND 'ip' = %s AND 'lead_id' = %d", $ID, $IP, $LEAD_ID ), OBJECT );

	if ( empty( $lookUpIP ) ) {
		// Not Found -- Add Users
		$wpdb->insert( $table_db_name, array( 'app_id' => $ID, 'ip' => $IP, 'lead_id' => $LEAD_ID, 'dt' => date( "Y-m-d H:i:s" ) ) );
	} else {
		// Found -- Update Time
		$wpdb->update( $table_db_name, array( 'dt' => date( "Y-m-d H:i:s" ) ), array( 'id' => $lookUpIP->ID ) );
	}
	// Purge All Who Havent been updated in 5 minutes...
	// $currentTime = date("Y-m-d H:i:s");
	// $currentTime = strtotime($currentTime);
	// $minus5Minutes = date("Y-m-d H:i:s", strtotime('-5 minutes', $currentTime));
	// $wpdb->query("DELETE FROM $table_db_name WHERE dt < '$minus5Minutes' ");
	// Return Option Object:
	$results = WebinarignitionManager::get_webinar_data($ID);

	// Check If Message is ON, if not, do nothing...
	if ( ! property_exists( $results, 'air_toggle' ) || $results->air_toggle == "" || $results->air_toggle == "off" ) {
		// Air Message Not On
		wp_send_json( [ "air_toggle" => "OFF" ] );
	} else {
		// Air Message On, show Message::
		$showHTML = $results->air_html;
		$showHTML = str_replace( "<!DOCTYPE html><html><head></head><body>", "", $showHTML );
		$showHTML = str_replace( "</body></html>", "", $showHTML );
		$showHTML = stripcslashes( wpautop($showHTML) );
		$showHTML .= '<div id="orderBTNArea">';

		if ( $results->air_btn_url == "" ) {

		} else {
			ob_start();
			$bg_color = empty( $results->air_btn_color ) ? '#6BBA40' : $results->air_btn_color;
			?>
            <a
                    href="<?php echo $results->air_btn_url ?>"
                    target="_blank"
                    class="large radius button success addedArrow replayOrder wiButton wiButton-lg wiButton-block"
                    style="background-color:<?php echo $bg_color ?>;color:#fff;border: 1px solid rgba(0,0,0,0.20);"
            >
				<?php echo $results->air_btn_copy; ?>
            </a>

			<?php
			$showHTML .= ob_get_clean();
		}
		$showHTML .= '</div>';

		$hash = wp_hash($showHTML );
		if( class_exists('advancediFrame') ){
			$advance_iframe_sc = $showHTML .get_cta_aiframe_sc($ID, '3', '');
			$showHTML = apply_filters( 'ai_handle_temp_pages', $advance_iframe_sc );
		}
		wp_send_json( [ "air_toggle" => "ON", "response" => do_shortcode($showHTML), "hash" => $hash ] );
	}
	die();
}

add_action( 'wp_ajax_nopriv_webinarignition_delete_smtp_updated_status', 'webinarignition_delete_smtp_updated_status' );
add_action( 'wp_ajax_webinarignition_delete_smtp_updated_status', "webinarignition_delete_smtp_updated_status" );
function webinarignition_delete_smtp_updated_status() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$option_deleted = delete_option( 'webinarignition_upgraded_smtp');
	wp_send_json( ['result' => $option_deleted] );
}


add_action( 'admin_notices', 'webinarignition_smtp_credentials_failed_notice' );
function webinarignition_smtp_credentials_failed_notice() {
	$webinarignition_smtp_credentials_failed     = get_option( 'webinarignition_smtp_credentials_failed' );

	if( $webinarignition_smtp_credentials_failed == 1 ) { ?>

        <div id="webinarignition-smtp-failed-notice" class="notice notice-warning is-dismissible">
            <p><?php _e( 'Your WebinarIgnition SMTP settings failed in the last attempt to use them. Webinarignition will not try using them from now on.', 'webinarignition' ); ?></p>
        </div>

        <script>
            jQuery(document).on( 'click', '#webinarignition-smtp-failed-notice .notice-dismiss', function() {
                jQuery.ajax({
                    url: '/wp-admin/admin-ajax.php',
                    data: {
                        action: 'webinarignition_delete_smtp_failed_notice',
                        security: '<?php echo wp_create_nonce("webinarignition_ajax_nonce"); ?>'
                    }
                });
            });
        </script>
		<?php
	}
}


add_action( 'wp_ajax_nopriv_webinarignition_delete_smtp_failed_notice', 'webinarignition_delete_smtp_failed_notice' );
add_action( 'wp_ajax_webinarignition_delete_smtp_failed_notice', "webinarignition_delete_smtp_failed_notice" );
function webinarignition_delete_smtp_failed_notice() {
	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$option_deleted = delete_option( 'webinarignition_smtp_credentials_failed');
	wp_send_json( ['result' => $option_deleted] );
}

add_action( 'wp_ajax_nopriv_webinarignition_get_support_users', 'webinarignition_get_support_users' );
add_action( 'wp_ajax_webinarignition_get_support_users', 'webinarignition_get_support_users' );
function webinarignition_get_support_users() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );

	$users = get_users();

	wp_send_json_success( $users );
}

add_action( 'wp_ajax_nopriv_webinarignition_check_if_q_and_a_enabled', 'webinarignition_check_if_q_and_a_enabled' );
add_action( 'wp_ajax_webinarignition_check_if_q_and_a_enabled', 'webinarignition_check_if_q_and_a_enabled' );


function webinarignition_check_if_q_and_a_enabled() {

	// check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );

	// $post_input         = filter_input_array( INPUT_POST );
	$post_input         = $_POST;
	$webinar_data       = WebinarignitionManager::get_webinar_data($post_input['webinar_id']);

	if( isset( $webinar_data->enable_qa ) && ( $webinar_data->enable_qa != 'yes' ) ){

		return wp_send_json_success( [ 'enable_qa' => 'no' ] );

	}

	wp_send_json_success( [ 'enable_qa' => 'yes' ] );

}

add_action( 'wp_ajax_nopriv_webinarignition_set_q_a_status', 'webinarignition_set_q_a_status' );
add_action( 'wp_ajax_webinarignition_set_q_a_status', 'webinarignition_set_q_a_status' );


function webinarignition_set_q_a_status() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );

	$post_input         = filter_input_array( INPUT_POST );
	$webinar_data       = WebinarignitionManager::get_webinar_data($post_input['webinarId']);

	if( $post_input['status'] == 'hide'  ){

		$webinar_data->enable_qa = 'no';

		update_option('webinarignition_campaign_' . $post_input['webinarId'], $webinar_data);
		wp_send_json_success( [ 'webinar_qa' =>  '1849', 'status' => $webinar_data->enable_qa  ] );

	} else {


		$webinar_data->enable_qa = 'yes';

		update_option('webinarignition_campaign_' . $post_input['webinarId'], $webinar_data);

		wp_send_json_success( [ 'webinar_qa' => '1853', 'status' => $webinar_data->enable_qa  ] );

	}

}



add_action( 'wp_ajax_nopriv_webinarignition_answer_attendee_question', 'webinarignition_answer_attendee_question' );
add_action( 'wp_ajax_webinarignition_answer_attendee_question', 'webinarignition_answer_attendee_question' );


function webinarignition_answer_attendee_question() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );

	$post_input         = filter_input_array( INPUT_POST );
	$webinar_data       = WebinarignitionManager::get_webinar_data($post_input['webinarId']);
	$attendeeEmail      = filter_var($post_input['attendeeEmail'], FILTER_SANITIZE_EMAIL);
	$emailAnswer        = $post_input['answer'] ;
	$attendeeQuestion   = $post_input['attendeeQuestion'];

	$subject            = sanitize_text_field( $post_input['subject'] );

	$answerText         = $post_input['answerText'] ;

	global $wpdb;
	$table_db_name = $wpdb->prefix . "webinarignition_questions";
	$ID            = sanitize_text_field( $post_input['questionId'] );

	$supportId     = sanitize_text_field( $post_input['supportId'] );
	$supportName   = sanitize_text_field( $post_input['supportName'] );

	$result = $wpdb->update( $table_db_name, [ 'status' => 'done', 'attr2' => $supportId, 'attr3' => $supportName, 'attr4' => '', 'attr5' => '', 'answer' => $emailAnswer, 'answer_text' => $answerText ], ['id' => $ID] );

	$parent = WebinarignitionQA::get_question($ID);

	if (!empty($parent)) {
		unset($parent['ID']);

		$parent['type'] = 'answer';
		$parent['status'] = 'answer';
		$parent['created'] = current_time( 'mysql' );
		$parent['parent_id'] = $ID;

		$answer_id = WebinarignitionQA::create_question($parent);
	}

	if (empty($post_input["emailQAEnabled"]) || 'off' !== $post_input["emailQAEnabled"]) {
		$email_data                     = new stdClass();
		$email_data->bodyContent        = $emailAnswer;
		$email_data->email_subject      = $subject;
		$email_data->footerContent      =  ( !empty( $webinar_data->show_or_hide_local_qstn_answer_email_footer ) && ( $webinar_data->show_or_hide_local_qstn_answer_email_footer == 'show' ) ) ? $webinar_data->qstn_answer_email_footer : '';

		if( !empty( $webinar_data->show_or_hide_local_qstn_answer_email_footer ) && ( $webinar_data->show_or_hide_local_qstn_answer_email_footer == 'show' )  ){
			$email_data->footerContent          = str_replace( "{YEAR}", date( "Y" ), $email_data->footerContent );
		}

		$email_data->emailheading       = $subject;
		$email_data->emailpreview       = $subject;

		$email                          = new WI_Emails();
		$emailBody                      = $email->build_email( $email_data );
		$headers            = ['Content-Type: text/html; charset=UTF-8'];

		if ( ! wp_mail( $attendeeEmail, $subject, $emailBody, $headers) ) {
			WebinarIgnition_Logs::add( __( "Support answer email could not be sent to", "webinarignition"). " {$attendeeEmail}", WebinarIgnition_Logs::LIVE_EMAIL );
		}
	}

	wp_send_json_success( );

}


add_action( 'wp_ajax_nopriv_webinarignition_hold_or_release_console_question', 'webinarignition_hold_or_release_console_question' );
add_action( 'wp_ajax_webinarignition_hold_or_release_console_question', 'webinarignition_hold_or_release_console_question' );


function webinarignition_hold_or_release_console_question() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );

	$post_input         = filter_input_array( INPUT_POST );

	global $wpdb;
	$table_db_name      = $wpdb->prefix . "webinarignition_questions";
	$questionId         = sanitize_text_field( $post_input['questionId'] );
	$supportName        = sanitize_text_field( $post_input['supportName'] );
	$webinarId          = sanitize_text_field( $post_input['webinarId'] );
	$supportId          = sanitize_text_field( $post_input['supportId'] );

	//release other questions first
	$questions = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM '$table_db_name' WHERE 'attr2' = %d", $supportId ), ARRAY_A );
	foreach ($questions as $question) {

		if( $question['attr4'] == 'hold' ){

			$wpdb->update( $table_db_name, [ 'attr2' => '', 'attr3' => '','attr4' => '', 'attr5' =>  ''  ], ['ID' => $question['ID']] );

		}

	}

	if ( $post_input['hold'] == 'true'  ){
		$wpdb->update( $table_db_name, [ 'attr2' => $supportId, 'attr3' => $supportName, 'attr4' => 'hold', 'attr5' =>  $supportName ], ['id' => $questionId] );
	} else {
		$wpdb->update( $table_db_name, ['attr2' => '', 'attr3' => '',  'attr4' => '', 'attr5' =>  ''  ], ['id' => $questionId] );
	}

	wp_send_json_success( );
}

add_action( 'wp_ajax_nopriv_webinarignition_release_unanswered_questions', 'webinarignition_release_unanswered_questions' );
add_action( 'wp_ajax_webinarignition_release_unanswered_questions', 'webinarignition_release_unanswered_questions' );


function webinarignition_release_unanswered_questions() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input         = filter_input_array( INPUT_POST );

	global $wpdb;
	$table_db_name      = $wpdb->prefix . "webinarignition_questions";
	$webinarId          = sanitize_text_field( $post_input['webinarId'] );
	$supportId          = sanitize_text_field( $post_input['supportId'] );

	$questions = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM '$table_db_name' WHERE 'app_id' = %d AND 'attr2' = %d", $webinarId, $supportId ), ARRAY_A );

	foreach ($questions as $question) {

		if( $question->attr4 == 'hold' ){

			$wpdb->update( $table_db_name, [ 'attr2' => '', 'attr3' => '','attr4' => '', 'attr5' =>  ''  ], ['ID' => $question->ID] );

		}

	}

	wp_send_json_success( );
}


add_action( 'wp_ajax_nopriv_webinarignition_get_answer_template', 'webinarignition_get_answer_template' );
add_action( 'wp_ajax_webinarignition_get_answer_template', 'webinarignition_get_answer_template' );


function webinarignition_get_answer_template() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input         = filter_input_array( INPUT_POST );

	$webinar_data       = WebinarignitionManager::get_webinar_data($post_input['webinarId']);
	$emailBody          = $webinar_data->qstn_answer_email_body;

	$return = array(
		'template' => $emailBody
	);

	wp_send_json_success( $return );

}


add_action( 'wp_ajax_webinarignition_send_test_email', 'webinarignition_send_test_email_callback' );
function webinarignition_send_test_email_callback() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );

	$post_input                   = filter_input_array( INPUT_POST );
	$email_data                   = new stdClass();
	$email_data->email_subject    = $post_input['subject'];
	$email_data->showLocalFooter  = $post_input['showLocalFooter'];
	$email_data->emailheading     = $post_input['emailheadingval'];
	$email_data->emailpreview     = $post_input['emailpreviewval'];
	$email_data->bodyContent      = $post_input['bodyContent'];
	$email_data->footerContent    = $post_input['footerContent'];
	$email_data->webinarid        = $post_input['webinarid'];

	$email_data->templates_version      = isset( $post_input['templates_version'] ) ? $post_input['templates_version']  : '';
	$email_data->use_new_template       = isset( $post_input['use_new_template'] )  ? $post_input['use_new_template']   : '';

	if( ( $email_data->use_new_template == 'yes' ) ||  !empty( $email_data->templates_version ) ){

		$email      = new WI_Emails();
		$emailBody  = $email->build_email( $email_data );

	} else {

		$emailHead = WebinarignitionEmailManager::get_email_head();
		$emailBody = $emailHead;
		$emailBody .= $email_data->bodyContent;

	}

	$webinar_data = WebinarignitionManager::get_webinar_data($post_input['webinarid']);


	$headers    = array('Content-Type: text/html; charset=UTF-8');
	$response   = [];

	if ( ! wp_mail( $post_input['email'], $post_input['subject'], $emailBody, $headers ) ) {

		$response['status']  = 0;
		$response['message'] = __( 'Sorry; email could not be sent.', "webinarignition");

	} else {

		$response['status']  = 1;
		$response['message'] = __( 'Email was successfully sent.', "webinarignition");

	}

	echo json_encode( $response );


	die;

}


add_action( 'wp_ajax_nopriv_webinarignition_update_webinar_status', 'webinarignition_update_webinar_status' );
add_action( 'wp_ajax_webinarignition_update_webinar_status', 'webinarignition_update_webinar_status' );

function webinarignition_update_webinar_status() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input                     = filter_input_array( INPUT_POST );
	$webinar_data                   = WebinarignitionManager::get_webinar_data($post_input['webinarId']);
	$webinar_data->webinar_switch   = $post_input['webinar_switch'];
	update_option( 'webinarignition_campaign_' . $post_input['webinarId'], $webinar_data );

	wp_send_json_success();

}

add_action( 'wp_ajax_nopriv_webinarignition_ajax_get_localized_time', 'webinarignition_ajax_get_localized_time' );
add_action( 'wp_ajax_webinarignition_ajax_get_localized_time', 'webinarignition_ajax_get_localized_time' );

function webinarignition_ajax_get_localized_time() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input     = filter_input_array( INPUT_POST );
	$time           =  webinarignition_get_localized_time( $post_input['time'] );

	echo $time;
	die;
}
add_action( 'wp_ajax_nopriv_webinarignition_ajax_get_date_format', 'webinarignition_ajax_get_date_format' );
add_action( 'wp_ajax_webinarignition_ajax_get_date_format', 'webinarignition_ajax_get_date_format' );
function webinarignition_ajax_get_date_format() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input     = filter_input_array( INPUT_POST );

	switch_to_locale(  $post_input['locale'] );

	$format = __( $post_input['format'] );
	echo date_i18n( $format );

	restore_previous_locale();

	wp_die( );
}

add_action( 'wp_ajax_nopriv_webinarignition_ajax_get_date_in_chosen_language', 'webinarignition_ajax_get_date_in_chosen_language' );
add_action( 'wp_ajax_webinarignition_ajax_get_date_in_chosen_language', 'webinarignition_ajax_get_date_in_chosen_language' );
/**
 * Retrieves the date in localized format, based on the format and language provided.
 */
function webinarignition_ajax_get_date_in_chosen_language() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input     = filter_input_array( INPUT_POST );
	$selected_lng   = $post_input['locale'];

	require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );

	$available_languages    = webinarignition_get_available_languages();
	$wp_available_languages = get_available_languages();

	if ( get_locale() !== $selected_lng && in_array($selected_lng, $available_languages) && ! in_array( $selected_lng, $wp_available_languages, true ) ) {

		$downloaded = wp_download_language_pack( $selected_lng );

		if($downloaded) {
			wp_send_json_success( 'downloaded' );
		}

	} else {

		$response = [];

		$switched_locale = switch_to_locale( $selected_lng );

//            if($switched_locale) {

		$date_format                       = __( 'F j, Y' );
		$response['date_in_chosen_locale'] = date_i18n( $date_format );
		$response['date_in_chosen_day_D_locale'] = date_i18n( 'D' );
		$response['date_in_chosen_day_l_locale'] = date_i18n( 'l' );
		$response['monthsFull']            = WiDateHelpers::get_locale_months();
		$response['weekdaysFull']          = WiDateHelpers::get_locale_days();
		$response['weekdaysShort']         = WiDateHelpers::get_locale_weekday_abbrev();
		$response['js_date_format']        = webinarignition_convert_php_to_js_date_format( $date_format );
		$response['php_date_format']       = $date_format;

		$time_format                       = __( 'g:i a' );
		$response['php_time_format']       = $time_format;
		$response['time_in_chosen_locale'] = date_i18n( $time_format );
		$response['js_time_format']        = webinarignition_convert_wp_to_js_time_format( $time_format );
		$response['preview_text']          = __( 'Preview:' );
		$response['custom_text']          = __( 'Custom:' );

		restore_previous_locale();

		wp_send_json_success( $response );
//            }
	}
}


add_action( 'wp_ajax_nopriv_webinarignition_ajax_convert_php_to_js_date_format', 'webinarignition_ajax_convert_php_to_js_date_format' );
add_action( 'wp_ajax_webinarignition_ajax_convert_php_to_js_date_format', 'webinarignition_ajax_convert_php_to_js_date_format' );
function webinarignition_ajax_convert_php_to_js_date_format() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input     = filter_input_array( INPUT_POST );

	$response       = [];
	$response['date_format']  = webinarignition_convert_php_to_js_date_format( $post_input['date_format']  );

	wp_send_json_success( $response );

}

add_action( 'wp_ajax_nopriv_webinarignition_ajax_convert_wp_to_js_time_format', 'webinarignition_ajax_convert_wp_to_js_time_format' );
add_action( 'wp_ajax_webinarignition_ajax_convert_wp_to_js_time_format', 'webinarignition_ajax_convert_wp_to_js_time_format' );
function webinarignition_ajax_convert_wp_to_js_time_format() {

	check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
	$post_input                 = filter_input_array( INPUT_POST );

	$response                   = [];
	$response['time_format']    = webinarignition_convert_wp_to_js_time_format( $post_input['time_format']  );

	wp_send_json_success( $response );

}

//TODO: Need to check how not to duplicate this function, after reviewing the whole plugin structure
if( !function_exists('webinarignition_get_available_languages') ) {
	function webinarignition_get_available_languages() {


		$webinarignition_languages = get_available_languages( WEBINARIGNITION_PATH . '/languages/' );
		$loco_translate_languages  = get_available_languages( WP_CONTENT_DIR . '/languages/loco/plugins/' );
		$system_languages          = get_available_languages( WP_CONTENT_DIR . '/languages/plugins/' );
		$all_languages             = array_merge( $loco_translate_languages, $system_languages, $webinarignition_languages );
		$available_languages       = [];

		for ( $i = 0; $i < count( $all_languages ); $i ++ ) {
			if ( ( strpos( $all_languages[ $i ], 'webinarignition' ) !== false ) || ( strpos( $all_languages[ $i ], 'webinar-ignition' ) !== false ) ) {
				$available_languages[] = $all_languages[ $i ];
			}
		}

		for ( $i = 0; $i < count( $available_languages ); $i ++ ) {
			if ( ( strpos( $available_languages[ $i ], 'webinarignition-' ) !== false ) ) {
				$available_languages[ $i ] = substr( $available_languages[ $i ], 16 );
			}

			if ( ( strpos( $available_languages[ $i ], 'webinar-ignition-' ) !== false ) ) {
				$available_languages[ $i ] = substr( $available_languages[ $i ], 17 );
			}

		}

		return array_unique( $available_languages );

	}
}

function webinarignition_get_lead_table($webinar_type) {
	global $wpdb;

	$table = "{$wpdb->prefix}webinarignition_leads";
	$webinar_type = trim(strtolower($webinar_type));

	if( $webinar_type === 'auto' ) {
		$table = "{$table}_evergreen";
	}

	return $table;
}

function webinarignition_update_webinar_lead_status($webinar_type, $lead_id) {
	global $wpdb;

	$table_name = webinarignition_get_lead_table($webinar_type);
	$id_column = 'ID';
	if( !is_numeric($lead_id) ) {
		$id_column = 'hash_ID';
	}

	$data = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$table_name} L WHERE L.{$id_column} = %s", $lead_id), OBJECT );

	if ( ! empty( $data ) ) {

		$attended = trim(strtolower($data->event));
		$watched_replay = trim(strtolower($data->replay));
		$status_column_value = 'Yes';

		if( $attended !== 'yes' ) {
			$status_column = 'event';
		} else if( $watched_replay !== 'yes' ) {
			$status_column = 'replay';
		} else {
			$status_column = false;
		}

		if( $status_column !== false ) {

			$lead_status = 'attended'; //Give more logical names to lead status
			if( $status_column === 'replay' ) {
				$lead_status = 'watched_replay';
			}

			$updated = $wpdb->update( $table_name, array( $status_column => $status_column_value ), array( $id_column => $lead_id ) );
			do_action( 'webinarignition_lead_updated', $data->ID );
			do_action( 'webinarignition_lead_status_changed', $lead_status, $lead_id, $data->app_id );

			return !empty($updated);
		}
	}

	return false;
}

/**
 * Check if current logged in user has existing un-attempted lead for the given webinar ID
 *
 * Returns 0 if no lead found, numeric lead ID otherwise
 *
 * @param $webinar_id
 * @param $webinar_type
 *
 * @return int
 */
function webinarignition_existing_lead_id($webinar_id, $user_email, $webinar_type='auto') {

	$webinar_id = absint($webinar_id);

	if( !is_user_logged_in() || empty($webinar_id) || empty($webinar_type) || empty($user_email) ) return 0; //Return 0

	global $wpdb;

	if($webinar_type === 'auto') {
		$table_lead      = $wpdb->prefix . "webinarignition_leads_evergreen";
	} else {
		$table_lead      = $wpdb->prefix . 'webinarignition_leads';
	}

	$lead_id = $wpdb->get_var($wpdb->prepare("SELECT L.ID FROM {$table_lead} L WHERE L.app_id = %d AND L.email = %s", $webinar_id, $user_email));

	$lead_id = absint($lead_id);

	return $lead_id;
}

/**
 * Delete lead by ID and webinar type
 *
 * @param $lead_id
 * @param $webinar_type
 */
function webinarignition_delete_lead_by_id($lead_id, $webinar_type = 'auto') {
	global $wpdb;

	if($webinar_type === 'auto') {
		$table_lead      = $wpdb->prefix . "webinarignition_leads_evergreen";
		$table_lead_meta = $wpdb->prefix . 'webinarignition_lead_evergreenmeta';
	} else {
		$table_lead      = $wpdb->prefix . 'webinarignition_leads';
		$table_lead_meta = $wpdb->prefix . 'webinarignition_leadmeta';
	}

	$lead_id = absint( $lead_id );

	$lead_deleted = $wpdb->delete( $table_lead, ['ID' => $lead_id], ['%d']);

	if( $lead_deleted ) {
		$wpdb->delete( $table_lead_meta, ['lead_id'=> $lead_id], ['%d']);
	}
}

/**
 *
 * @param int $webinar_id
 * @param int $lead_id
 * @param string $status
 */
function webinarignition_mark_lead_status($webinar_data, $lead, $status) {

	if ( ! empty( $webinar_data ) && !empty($lead) ) {

		if( $status === 'attending' ) {

			$webinar_timezone = webinarignition_get_webinar_timezone($webinar_data, null, $lead);
			$webinar_timezone = Webinar_Ignition_Helper::getValidTimezoneId($webinar_timezone);
			$lead_live_datetime = isset($lead->date_picked_and_live) && !empty($lead->date_picked_and_live) ? $lead->date_picked_and_live : date('Y-m-d H:i:s');

			$datetime_now = new DateTime('now', new DateTimeZone($webinar_timezone) );

			//Create a new datetime object with today's date for comparison with max time slot, and assign webinar timezone
			$datetime_compare = new DateTime( date('Y-m-d H:i:s', strtotime($lead_live_datetime) ), new DateTimeZone($webinar_timezone) );

			//Convert current datetime from webinar timezone to UTC for comparison, and to avoid daylight saving differences
			$datetime_now->setTimezone(new DateTimeZone('UTC'));

			//Convert compare datetime from webinar timezone to UTC
			$datetime_compare->setTimezone(new DateTimeZone('UTC'));

			//If current time is less than lead time, then consider lead is not yet started/available
			if( $datetime_now->getTimestamp() < $datetime_compare->getTimestamp() ) {
				return false;
			}
		}

		$is_auto = webinarignition_is_auto( $webinar_data );

		global $wpdb;

		$leads_table = "{$wpdb->prefix}webinarignition_leads";
		if ( $is_auto ) {
			$leads_table .= "_evergreen";
		}

		$wpdb->update( $leads_table,
			[
				'lead_status' => $status
			], [
				'ID'     => $lead->ID,
				'app_id' => $webinar_data->id
			]
		);

		do_action( 'webinarignition_lead_status_changed', $status, $lead->ID, $webinar_data->id);

		return true;

	}

	return false;
}

function webinarignition_mark_lead_watched() {

	check_admin_referer('webinarignition_mark_lead_status','nonce');

	if( !wp_doing_ajax() ) {
		return;
	}

	$response_type = 'error';

	if( isset($_POST['webinar_id']) && isset($_POST['lead_id']) ) {

		if( isset($_POST['is_preview_page']) && true === $_POST['is_preview_page'] ) {
			$response_type = 'success'; //Return success always for preview page
		} else {
			$webinar_id = absint( $_POST['webinar_id'] );
			$lead_id    = absint( $_POST['lead_id'] );

			$webinar_data = WebinarignitionManager::get_webinar_data( $webinar_id );
			$lead         = webinarignition_get_lead_info( $lead_id, $webinar_data, false );

			if ( $lead->lead_status !== 'watched' ) {
				if ( webinarignition_mark_lead_status( $webinar_data, $lead, 'watched' ) ) {
					$response_type = 'success';
				}
			}
		}
	}

	call_user_func("wp_send_json_{$response_type}");
}

add_action('wp_ajax_nopriv_webinarignition_lead_mark_watched','webinarignition_mark_lead_watched');
add_action('wp_ajax_webinarignition_lead_mark_watched','webinarignition_mark_lead_watched');

function webinarignition_mark_lead_attended() {

	check_admin_referer('webinarignition_mark_lead_status','nonce');

	if( !wp_doing_ajax() ) {
		return;
	}

	$response_type = 'error';

	if( isset($_POST['webinar_id']) && isset($_POST['lead_id']) ) {

		if( isset($_POST['is_preview_page']) && true === $_POST['is_preview_page'] ) {
			$response_type = 'success'; //Return success always for preview page
		} else {
			$webinar_id = absint( $_POST['webinar_id'] );
			$lead_id    = absint( $_POST['lead_id'] );

			$webinar_data = WebinarignitionManager::get_webinar_data( $webinar_id );
			$lead         = webinarignition_get_lead_info( $lead_id, $webinar_data, false );

			if ( $lead->lead_status !== 'attended' ) {
				if( empty( $lead->lead_status ) ) {
					if ( webinarignition_mark_lead_status( $webinar_data, $lead, 'attended' ) ) {
						$response_type = 'success';
					}
				}
			}
		}
	}

	call_user_func("wp_send_json_{$response_type}");
}

add_action('wp_ajax_nopriv_webinarignition_lead_mark_attended','webinarignition_mark_lead_attended');
add_action('wp_ajax_webinarignition_lead_mark_attended','webinarignition_mark_lead_attended');

function webinarignition_mark_lead_attending() {

	check_admin_referer('webinarignition_mark_lead_status','nonce');

	if( !wp_doing_ajax() ) {
		return;
	}

	$response_type = 'error';

	if( isset($_POST['webinar_id']) && isset($_POST['lead_id']) ) {

		$webinar_id = absint( $_POST['webinar_id'] );
		$lead_id    = absint( $_POST['lead_id'] );

		$webinar_data = WebinarignitionManager::get_webinar_data($webinar_id);
		$lead = webinarignition_get_lead_info($lead_id, $webinar_data, false);

		if( isset( $lead->lead_status ) && $lead->lead_status !== 'watched' ) {
			if ( webinarignition_mark_lead_status( $webinar_data, $lead, 'attending' ) ) {
				$response_type = 'success';
			}
		}
	}

	call_user_func("wp_send_json_{$response_type}");
}

add_action('wp_ajax_nopriv_webinarignition_lead_mark_attending','webinarignition_mark_lead_attending');
add_action('wp_ajax_webinarignition_lead_mark_attending','webinarignition_mark_lead_attending');

function webinarignition_mark_lead_complete() {

	check_admin_referer('webinarignition_mark_lead_status','nonce');

	if( !wp_doing_ajax() ) {
		return;
	}

	$response_type = 'error';

	if( isset($_POST['webinar_id']) && isset($_POST['lead_id']) ) {

		$webinar_id = absint( $_POST['webinar_id'] );
		$lead_id    = absint( $_POST['lead_id'] );

		$webinar_data = WebinarignitionManager::get_webinar_data($webinar_id);
		$lead = webinarignition_get_lead_info($lead_id, $webinar_data, false);

		if( $lead->lead_status !== 'watched' ) {
			if ( webinarignition_mark_lead_status( $webinar_data, $lead, 'complete' ) ) {
				$response_type = 'success';
			}
		}
	}

	call_user_func("wp_send_json_{$response_type}");
}

add_action('wp_ajax_nopriv_webinarignition_lead_mark_complete','webinarignition_mark_lead_complete');
add_action('wp_ajax_webinarignition_lead_mark_complete','webinarignition_mark_lead_complete');