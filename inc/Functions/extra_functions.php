<?php

defined( 'ABSPATH' ) || exit;

// fnc fix permalink uri variable deliminator
// --------------------------------------------------------
function webinarignition_fixPerma( $postID = false, $url = false ) {
    $pml = $url;
    if ( ! $pml ) {
        $pml = $postID ? get_permalink( $postID ) : get_permalink();
    }
    $pml = ( ( strpos( $pml, '?' ) !== false ) ? "$pml&" : "$pml?" );

    return $pml;
}

// --------------------------------------------------------


function webinarignition_display( $var, $placeholder ) {
    // check if var is set
    if ( empty( $var ) ) {
        echo $placeholder;
    } else {
        echo stripcslashes( $var );
    }
}


function webinarignition_generate_key( $length = 32 ) {
    $str        = "";
    $characters = array_merge( range( 'A', 'Z' ), range( 'a', 'z' ), range( '0', '9' ) );
    $max        = count( $characters ) - 1;
    for ( $i = 0; $i < $length; $i ++ ) {
        $rand = mt_rand( 0, $max );
        $str  .= $characters[ $rand ];
    }

    return $str;
}


function webinarignition_live_notification_times( $start_date, $start_time ) {
    $newDate = strtotime( $start_date[2] . "-" . $start_date[0] . "-" . $start_date[1] );
    $newTime = strtotime( $start_time );

    $notification_times                       = array();
    $notification_times['live']['date']       = date( "m-d-Y", $newDate );
    $notification_times['live']['time']       = date( "H:i", $newTime );
    $notification_times['daybefore']['date']  = date( "m-d-Y", strtotime( '-1 day', $newDate ) );
    $notification_times['daybefore']['time']  = $notification_times['live']['time'];
    $notification_times['hourbefore']['date'] = $notification_times['live']['date'];
    $notification_times['hourbefore']['time'] = date( "H:i", strtotime( '-1 hour', $newTime ) );
    $notification_times['hourafter']['date']  = $notification_times['live']['date'];
    $notification_times['hourafter']['time']  = date( "H:i", strtotime( '+1 hour', $newTime ) );
    $notification_times['dayafter']['date']   = date( "m-d-Y", strtotime( '+1 day', $newDate ) );
    $notification_times['dayafter']['time']   = $notification_times['live']['time'];

    return $notification_times;
}

function webinarignition_prettifyNotificationTitle( $num ) {
    switch ( $num ) {
        case 1:
            return __( "Day Before Notification", "webinarignition");
            break;
        case 2:
            return __( "Hour Before Notification", "webinarignition");
            break;
        case 3:
            return __( "Live Notification", "webinarignition");
            break;
        case 4:
            return __( "Hour After Notification", "webinarignition");
            break;
        case 5:
            return __( "Day After Notification", "webinarignition");
            break;
    }


}


// export leads
add_action( 'admin_post_webinarignition_export_leads', 'webinarignition_export_leads' );
function webinarignition_export_leads() {
    require_once( WEBINARIGNITION_PATH . 'inc/ex/ex.php' );
}

add_action( 'wp_ajax_webinarignition_export_leads', 'webinarignition_export_leads' );

// extra stuff
function webinarignition_admin_scripts() {
    wp_enqueue_script( 'jquery-ui-sortable' );
}

add_action( 'admin_enqueue_scripts', 'webinarignition_admin_scripts' );


function webinarignition_check_admin() {

    if ( isset( $_SERVER['QUERY_STRING'] ) ) {

        if ( strstr( $_SERVER['QUERY_STRING'], 'preview-' ) && ! is_user_logged_in() ) {
            wp_redirect( home_url() );
            exit;
            header( "HTTP/1.1 403 Forbidden" );
            ob_get_clean();
            exit();
        }

    }


}

add_action( 'init', 'webinarignition_check_admin' );


/**
 * @param String $client (is the webinar id in the webinarignition_leads table
 *
 * @return String
 */
function webinarignition_getLid( $client ) {

    $input_cookie = filter_input_array( INPUT_COOKIE );

    $input_get = filter_input_array( INPUT_GET );
    if ( ! empty( $input_cookie[ 'we-trk-' . $client ] ) ) {
        return $input_cookie[ 'we-trk-' . $client ];
    } else if ( ! empty( $input_get['lid'] ) ) {
        return $input_get['lid'];
    } else {
        return '';
    }

}

function webinarignition_determinePaymentProvider( $webinar ) {

    if ( webinarignition_usingStripePaymentOption( $webinar ) ) {
        return 'stripe';
    } else if ( webinarignition_usingPaypalPaymentOption( $webinar ) ) {
        return 'paypal';
    } else if ( !empty(sanitize_text_field($webinar->paid_button_type)) ) {
        return sanitize_text_field($webinar->paid_button_type);
    }

    return 'unknown';
}

function webinarignition_usingStripePaymentOption( $results ) {
    if ( isset( $results->paid_button_type ) && $results->paid_button_type === 'stripe' ) {
        return true;
    } else if ( $results->paid_button_type === 'default' && $results->stripe_secret_key && $results->stripe_publishable_key ) {
        return true;
    }

    return false;
}

function webinarignition_usingPaypalPaymentOption( $results ) {
    return isset( $results->paid_button_type ) && ( $results->paid_button_type === 'paypal' || $results->paid_button_type === 'default' );
}

function webinarignition_should_use_videojs( $results ) {

    // don't use Videojs if it is a live webinar
    if ( ! isset( $results->webinar_date ) || $results->webinar_date !== "AUTO" ) {
        return false;
    }

    // don't use Videojs if iframe (3rd party e.g Youtube) is used
    if ( empty( $results->webinar_source_toggle ) || $results->webinar_source_toggle === 'iframe' ) {
        return false;
    }

    return true;
}

/**
 * @param $path
 * @param $webinar_data
 */
function webinarignition_inline_css_file( $path, $webinar_data ) {
    extract( webinarignition_get_global_templates_vars( $webinar_data ) );
    $to_include = [];

    if (!is_array($path)) {
        $to_include[] = $path;
    } else {
        $to_include = $path;
    }

    ob_start();
    foreach ($to_include as $path) {

        if (file_exists($path)) {
            include $path;
        }
    }
    $inline_css = ob_get_clean();

    $inline_css = str_replace('<style type="text/css">', '', $inline_css);
    $inline_css = str_replace('<style>', '', $inline_css);
    $inline_css = str_replace('</style>', '', $inline_css);
    $inline_css = trim($inline_css);

    return $inline_css;
}

/**
 * @param $path string | array
 *
 * @return string
 */
function webinarignition_inline_js_file( $path, $webinar_data ) {
    extract( webinarignition_get_global_templates_vars( $webinar_data ) );

    $to_include = [];

    if (!is_array($path)) {
        $to_include[] = $path;
    } else {
        $to_include = $path;
    }

    ob_start();
    foreach ($to_include as $path) {
        if (file_exists($path)) {
            include $path;
        }
    }

    $inline_js = ob_get_clean();
    $inline_js = str_replace('<script type="text/javascript">', '', $inline_js);
    $inline_js = str_replace('<script>', '', $inline_js);
    $inline_js = str_replace('</script>', '', $inline_js);
    $inline_js = trim($inline_js);

    return $inline_js;
}

function webinarignition_display_custom_js( $results, $propertyName ) {
    echo empty( $results->$propertyName ) ? '' : webinarignition_format_custom_js( $results->$propertyName );
}

function webinarignition_format_custom_js( $string ) {

    if ( empty( $string ) ) {
        return $string;
    }

    $possible_script_tags = array(
        '<script>',
        '</script>',
        '<script type="text/javascript">',
        '<script type="text/javascript" >',
        "<script type='text/javascript'>",
        "<script type='text/javascript' >"
    );

    foreach ( $possible_script_tags as $needle ) {
        if ( strpos( $string, $needle ) !== false ) {
            return $string;
        }
    }

    return '<script type="text/javascript">' . $string . '</script>';
}

function webinarignition_is_auto( $webinar ) {
    return isset( $webinar->webinar_date ) && ( $webinar->webinar_date === "AUTO" );
}


function webinarignition_parse_registration_page_data( $webinarId, $rawWebinarData ) {
    $w = $rawWebinarData;

    if ( ! empty( $w->paid_status ) ):
        $paidCode = $w->paid_status === 'paid' ? '&' . urlencode( $w->paid_code ) : '';
    else:
        $paidCode = '';
    endif;

    $webinarUrl = webinarignition_fixPerma();

    if ( ! empty( $w->webinar_url ) ) {
        $webinarUrl = webinarignition_fixPerma( false, $w->webinar_url );
    } elseif ( ! empty( $w->webinar_id ) ) {
        $webinarUrl = webinarignition_fixPerma( $w->webinar_id );
    }

    $thankYouPageUrl = webinarignition_fixPerma();

    if ( ! empty( $w->ty_url ) ) {
        $thankYouPageUrl = webinarignition_fixPerma( false, $w->ty_url );
    } elseif ( ! empty( $w->ty_id ) ) {
        $thankYouPageUrl = webinarignition_fixPerma( $w->ty_id );
    }

    $thankYouPageUrl = $thankYouPageUrl . 'confirmed' . $paidCode; // lead id (lid) will be appended after registration form has been submitted.

    $useCustomThankYouUrl = false;
    if ( $w->custom_ty_url_state === 'show' && ! empty( $w->custom_ty_url ) ) {
        $useCustomThankYouUrl = true;
        $thankYouPageUrl      = $w->custom_ty_url;
    }

    if ( webinarignition_is_auto( $w ) ) {
        $scheduleType = $w->lp_schedule_type;
        if ( ! empty( $scheduleType ) && ! in_array( $w->lp_schedule_type, array( 'fixed', 'delayed' ) ) ) {
            $scheduleType          = 'custom';
            $customScheduleMaxTime = webinarignition_get_cs_max_time( $w );
        }
    }

    $custom_thankyou_page_url = '';
    $custom_webinar_page_url = '';

    $is_webinar_protected = !empty($rawWebinarData->protected_webinar_id) && 'protected' === $rawWebinarData->protected_webinar_id;
    $webinarIdUrl = $is_webinar_protected ? $rawWebinarData->hash_id : $webinarId;

    if (!empty($rawWebinarData->custom_thankyou_page)) {
        $custom_thankyou_page = get_post($rawWebinarData->custom_thankyou_page);

        if (!empty($custom_thankyou_page)) {
            $custom_thankyou_page_url = get_permalink($custom_thankyou_page);
        }
    }

    if (!empty($rawWebinarData->custom_webinar_page)) {
        $custom_webinar_page = get_post($rawWebinarData->custom_webinar_page);

        if (!empty($custom_webinar_page)) {
            $custom_webinar_page_url = get_permalink($custom_webinar_page);
        }
    }

    $parsed = array(
        'webinarId'            => $webinarId,
        'webinarType'          => webinarignition_is_auto( $rawWebinarData ) ? 'evergreen' : 'live',
        'ajaxUrl'              => admin_url( 'admin-ajax.php' ),
        'webinarUrl'           => $webinarUrl,
        'custom_thankyou_page_url' => $custom_thankyou_page_url,
        'custom_webinar_page_url' => $custom_webinar_page_url,
        // This is the registration page url, to get the event/webinar page url, just append &live to it later (and the lid of course).
        'thankYouPageUrl'      => $thankYouPageUrl,
        'skipThankYouPage'     => isset( $rawWebinarData->skip_ty_page ) && $rawWebinarData->skip_ty_page === "yes",
        'useCustomThankYouUrl' => $useCustomThankYouUrl,
        'arUrl'                => ! empty( $w->ar_url ) ? $w->ar_url : 'none',
        'paidCode'             => ! empty( $rawWebinarData->paid_code ) ? $rawWebinarData->paid_code : '',
        'isPaidWebinar'        => isset( $w->paid_status ) && $w->paid_status === 'paid',
        'isSigningUpWithFB'    => false,
        'fbUserData'           => [],
        'paymentProvider'      => webinarignition_determinePaymentProvider( $rawWebinarData ),
        'scheduleType'         => ! empty( $scheduleType ) ? $scheduleType : '',
        'leadDeviceInfo'       => [],
        'userIp'               => $_SERVER['REMOTE_ADDR'],
	    'translations' => [
		    'ar_modal_head' => __( 'AR Data Submitted!', 'webinarignition' ),
		    'ar_modal_body' => __( 'If all went well, the data should be in your autoresponder list. Check your autoresponder list to confirm.', 'webinarignition' ),
		    'done'          => __( 'Done', 'webinarignition' )
	    ],
        // This is for values only used in live webinars.
        'live'                 => [
            'webinar_switch'   => ! empty( $rawWebinarData->webinar_switch ) ? $rawWebinarData->webinar_switch : '',
        ],

        // This is for values only used in evergreen webinars.
        'evergreen'            => [
            'schedules' => [
                'custom'  => [
                    'maxTime' => ! empty( $customScheduleMaxTime ) ? $customScheduleMaxTime : '',
                ],
                'fixed'   => [],
                'delayed' => [],
            ],
            'skip_instant_acces_confirm_page' =>  ( isset($rawWebinarData->skip_instant_acces_confirm_page) && $rawWebinarData->skip_instant_acces_confirm_page === 'yes' )
        ]
    );

    return $parsed;
}

// ARRAY TO OBJECT FUNCTION::
function webinarignition_array_to_object( $array ) {
    $obj = new stdClass;
    foreach ( $array as $k => $v ) {
        if ( is_array( $v ) ) {
            $obj->{$k} = $v; //RECURSION
        } else {
            $obj->{$k} = $v;
        }
    }

    return $obj;
}

// Timezone UTC To Abrv.
function webinarignition_utc_to_abrc( $utc ) {
    switch ( $utc ) {
        case "-12":
            return "Y";
        case "-11":
            return "SST";
        case "-10":
            return "CKT";
        case "-930":
            return "MART";
        case "-9":
            return "AKST";
        case "-8":
            return "PST";
        case "-7":
            return "MST";
        case "-6":
            return "CST";
        case "-5":
            return "EST";
        case "-430":
            return "VST";
        case "-330":
            return "NST";
        case "-3":
            return "SRT";
        case "-2":
            return "O";
        case "-1":
            return "EGT";
        case "0":
            return "GMT";
        case "+1":
            return "CET";
        case "+2":
            return "CAT";
        case "+3":
            return "EAT";
        case "+330":
            return "IST";
        case "+4":
            return "AST";
        case "+430":
            return "AFT";
        case "+5":
            return "PKT";
        case "+530":
            return "IST";
        case "+545":
            return "NPT";
        case "+6":
            return "BTT";
        case "+630":
            return "MMT";
        case "+7":
            return "ICT";
        case "+8":
            return "HKT";
        case "+845":
            return "ACWST";
        case "+9":
            return "JST";
        case "+930":
            return "ACST";
        case "+10":
            return "PGT";
        case "+1030":
            return "LHST";
        case "+11":
            return "VUT";
        case "+1130":
            return "NFT";
        case "+12":
            return "MHT";
        case "+1245":
            return "CHAST";
        case "+13":
            return "WST";
        case "+14":
            return "LINT";
            break;
    }
}


add_filter( 'cron_schedules', 'webinarignition_cron_add_five_minutes' );
function webinarignition_cron_add_five_minutes( $schedules ) {

    $schedules['five_minutes'] = [
        'interval' => 300,
        'display'  => 'Once Every Five Minutes'
    ];

    return $schedules;
}

add_action( 'webinarignition_cron_hook', 'webinarignition_cron_exec' );

function webinarignition_cron_exec() {
    include( WEBINARIGNITION_PATH . 'inc/schedule_notifications.php' );
}

if ( ! wp_next_scheduled( 'webinarignition_cron_hook' ) ) {
    wp_schedule_event( time(), 'five_minutes', 'webinarignition_cron_hook' );
}



function webinarignition_get_text_color_from_bg_color($hex_color) {
    $hexCode = ltrim($hex_color, '#');

    if (strlen($hexCode) == 3) {
        $hexCode_array = str_split($hexCode);
        $hexCode = $hexCode_array[0] . $hexCode_array[0] . $hexCode_array[1] . $hexCode_array[1] . $hexCode_array[2] . $hexCode_array[2];
    }
    $hex_color = '#' . $hexCode;

    $r = hexdec(substr($hex_color, 1, 2));
    $g = hexdec(substr($hex_color, 3, 2));
    $b = hexdec(substr($hex_color, 5, 2));
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    $text_color = ($yiq >= 198) ? 'black' : 'white';

    return $text_color;
}

function webinarignition_get_hover_color_from_bg_color($hex_color) {
    $hexCode = ltrim($hex_color, '#');

    if (strlen($hexCode) == 3) {
        $hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
    }

    $hoverCode = array_map('hexdec', str_split($hexCode, 2));

    $adjustPercent = -0.08;
    foreach ($hoverCode as & $color) {
        $adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
        $adjustAmount = ceil($adjustableLimit * $adjustPercent);

        $color = str_pad(dechex($color + $adjustAmount), 2, '0', STR_PAD_LEFT);
    }

    $hover_color = '#' . implode($hoverCode);

    return $hover_color;
}

add_action( 'webinarignition_delete_logs_db_cron_hook', 'webinarignition_delete_logs_db_cron_exec' );

function webinarignition_delete_logs_db_cron_exec() {
    
    if( get_option( 'webinarignition_auto_clean_log_db' ) ){
        WebinarIgnition_Logs::deleteOldLogs();
    }
        
}

if ( ! wp_next_scheduled( 'webinarignition_delete_logs_db_cron_hook' ) ) {
    wp_schedule_event( time(), 'daily', 'webinarignition_delete_logs_db_cron_hook' );
}

register_deactivation_hook( __FILE__, 'webinarignition_deactivate_delete_logs_cron' );

function webinarignition_deactivate_delete_logs_cron() {
    $timestamp = wp_next_scheduled( 'webinarignition_delete_logs_db_cron_hook' );
    wp_unschedule_event( $timestamp, 'webinarignition_delete_logs_db_cron_hook' );
}

if( !defined('WEBINAR_IGNITION_DISABLE_LOGIN_ON_REGISTER') || WEBINAR_IGNITION_DISABLE_LOGIN_ON_REGISTER !== true ) {

	/**
	 * Redefine "wp_new_user_notification" to send login details to user
	 *
	 * @param $user_id
	 * @param string $plaintext_pass
	 */
	if ( !function_exists('wp_new_user_notification') ) {
		function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {
			$user = new WP_User( $user_id );

			$user_login = stripslashes( $user->user_login );
			$user_email = stripslashes( $user->user_email );

			$message = sprintf( __( 'New user registration on your blog %s:' ), get_option( 'blogname' ) ) . "\r\n\r\n";
			$message .= sprintf( __( 'Username: %s' ), $user_login ) . "\r\n\r\n";
			$message .= sprintf( __( 'E-mail: %s' ), $user_email ) . "\r\n";

			@wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Registration' ), get_option( 'blogname' ) ), $message );

			if ( empty( $plaintext_pass ) ) {
				return;
			}

			$message = sprintf( __( 'Hi %s,' ), $user->display_name ) . "\r\n\r\n";
			$message .= sprintf( __( "Welcome to %s! \r\n\r\n Here's how to log in:" ), get_option( 'blogname' ) ) . "\r\n\r\n";
			$message .= wp_login_url() . "\r\n";
			$message .= sprintf( __( 'Username: %s' ), $user_login ) . "\r\n";
			$message .= sprintf( __( 'Password: %s' ), $plaintext_pass ) . "\r\n\r\n";
			$message .= sprintf( __( 'If you have any problems, please contact us at %s.' ), get_option( 'admin_email' ) ) . "\r\n\r\n";
			$message .= __( "Thank You," ) . "\r\n";
			$message .= sprintf( __( 'The %s Team' ), get_option( 'blogname' ) );

			wp_mail( $user_email, sprintf( __( '[%s] Your username and password' ), get_option( 'blogname' ) ), $message );
		}
	}

	/**
	 * Register/Login user when new lead has been created
	 *
	 * @param $webinar_data
	 * @param $lead_id
	 * @param $lead_meta
	 */
	function webinarignition_auto_login_cb( $webinar_id, $lead_id ) {

		if( wp_doing_cron() || wp_doing_ajax() || current_user_can('manage_options') ) {
			return; //bail here, skip cron, ajax, admin users
		}

		if( empty($webinar_id) || empty($lead_id) ) return; //bail here, missing required IDs

		$webinar_data = WebinarignitionManager::get_webinar_data($webinar_id);

		if( empty($webinar_data) ) return; //bail here, invalid webinar

		$lead = webinarignition_get_lead_info($lead_id, $webinar_data, false);

		if( empty($lead) ) return; //bail here, invalid lead

		$lead_data = WebinarignitionLeadsManager::get_lead_meta($lead->ID, 'wiRegForm', $webinar_data->webinar_date == 'AUTO' ? 'evergreen' : 'live');

		if( empty($lead_data) ) return; //bail here, invalid lead_data

		$names = [];
		$user_display_name = '';

		$wiRegForm_data = (array) maybe_unserialize($lead_data['meta_value']);
		$lead_meta = [];

		foreach ($wiRegForm_data as $field_name => $field) {
			$field_value = sanitize_text_field($field['value']);
			$lead_meta[$field_name] = $field_value;
		}

		if( isset($lead_meta['optName']) ) {

			if(trim($lead_meta['optName']) == '#firstlast#') {
				$names[]           = isset( $lead_meta['optFName'] ) ? trim( sanitize_text_field( $lead_meta['optFName'] ) ) : '';
				$names[]           = isset( $lead_meta['optLName'] ) ? trim( sanitize_text_field( $lead_meta['optLName'] ) ) : '';
				$user_display_name = implode( " ", $names );
			} else {
				$user_display_name = trim(sanitize_text_field($lead_meta['optName']));
				$names = explode(" ", $user_display_name, 2 );
			}
		}

		$user_email        = sanitize_text_field( $lead_meta['optEmail'] );

        //Do email verification
        global $wpdb;
        $table_db_name = $wpdb->prefix . "webinarignition_verification";        
        $code = $wpdb->get_row( "SELECT * FROM {$table_db_name} WHERE email = '{$user_email}'", ARRAY_A );
        if(!isset($_GET['code']) || $_GET['code'] != $code['code']) return; // bail here, invalid verification code

		$user              = get_user_by( 'email', $user_email );

		if ( ! $user && false == email_exists( $user_email ) ) {
			$random_password = wp_generate_password( $length = 12, $include_standard_special_chars = false );

			$user_data = [
				'user_login'    => $user_email,
				'user_email'    => $user_email,
				'user_pass'     => $random_password,
				'user_nicename' => $user_display_name,
				'display_name'  => $user_display_name
			];

			if( !empty($names) ) {
				$user_data['first_name'] = isset($names[0]) ? $names[0] : '';
				$user_data['last_name'] = isset($names[1]) ? $names[1] : '';
			}

			$user_id = wp_insert_user( $user_data );

			if ( ! is_wp_error( $user_id ) ) {
				$webinarignition_auto_login_password_email = absint(get_option( 'webinarignition_auto_login_password_email', 0 ));

				if( $webinarignition_auto_login_password_email !== 0 ) {
					wp_new_user_notification( $user_id, $random_password );
				}
			}
		} else {
			$user_id = $user->ID;

			$user_data = [
				'ID' => $user_id,
				'user_nicename' => $user_display_name,
				'display_name'  => $user_display_name,
			];

			if( !empty($names) ) {
				$user_data['first_name'] = isset($names[0]) ? $names[0] : $user->get('first_name');
				$user_data['last_name']  = isset($names[1]) ? $names[1] : '';
			}

			wp_update_user($user_data);
		}

		if ( $user_id !== get_current_user_id() ) {
			$user = get_user_by('id', $user_id);
            $roles = ( array ) $user->roles;            
            if ( $roles[0] == 'subscriber') {
                wp_set_current_user( $user_id ); // set the current wp user
                wp_set_auth_cookie( $user_id ); // start the cookie for the current registered user

                do_action('webinarignition_after_user_auto_log_in', $user_id, $webinar_id, $lead_id); //Do something after user auto log-in
            }
        }
	}

	add_action( 'webinarignition_auto_login', 'webinarignition_auto_login_cb', 10, 2 );
}