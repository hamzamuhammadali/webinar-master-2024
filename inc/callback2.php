<?php  use Twilio\Rest\Client;

defined( 'ABSPATH' ) || exit;

// Edit Campaign // Save
add_action('wp_ajax_webinarignition_test_sms', 'webinarignition_test_sms');

add_action('wp_ajax_webinarignition_process_stripe_charge', 'webinarignition_process_stripe_charge');
add_action('wp_ajax_nopriv_webinarignition_process_stripe_charge', 'webinarignition_process_stripe_charge');

function webinarignition_process_stripe_charge() {
            check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
            $post_input                 = filter_input_array(INPUT_POST);

            $post_input['campaign_id']            = isset( $post_input['campaign_id'] )     ? sanitize_text_field( $post_input['campaign_id'] ) : null;
            $post_input['token']                  = isset( $post_input['token'] )        ? sanitize_text_field( $post_input['token'] ) : null;
            $post_input['stripe_receipt_email']   = isset( $post_input['stripe_receipt_email'] )    ? sanitize_email( $post_input['stripe_receipt_email'] ) : null;

            $webinar_data = WebinarignitionManager::get_webinar_data($post_input['campaign_id']);
            $stripe_secret_key          = $webinar_data->stripe_secret_key;
            $stripe_charge              = $webinar_data->stripe_charge;
            $stripe_charge_description  = $webinar_data->stripe_charge_description;
            $stripe_currency            = ! empty( $webinar_data->stripe_currency ) ? $webinar_data->stripe_currency : 'usd';
            
            if(empty($webinar_data)) {
               die();
            }

            // require 'stripe-php/init.php';

            // Set your secret key: remember to change this to your live secret key in production
            // See your keys here https://dashboard.stripe.com/account/apikeys
            \Stripe\Stripe::setApiKey($stripe_secret_key);

            $token = $post_input['token'];

            $customers = \Stripe\Customer::all( ["limit" => 1, "email" => $post_input['stripe_receipt_email'] ] );

            if( empty( $customers['data'] ) ) {

                // Create a Customer
                $customer = \Stripe\Customer::create(array(
                    "email"     => $post_input['stripe_receipt_email'],
                    "source"    => $token,
                ));

                $customerID = $customer->id;

            } else {
                $customerID = $customers['data'][0]['id'];
            }

            // Create the charge on Stripe's servers - this will charge the user's card
            try {

              $charge = \Stripe\Charge::create(array(
                "amount"        => $stripe_charge, // amount in cents, again
                "currency"      => $stripe_currency,
                "description"   => $stripe_charge_description,
                "customer"      => $customerID
                ));

            } catch(\Stripe\Error\Card $e) {
              // The card has been declined
                die(json_encode(array('status' => 0,'error' => $e->getMessage(), 'token' => $token)));
            } catch (Exception $e) {
                wp_send_json(array('status' => 0, 'error' => $e->getMessage()));
            }

            die(json_encode(array('status' => 1, 'token'=> $token, 'charge'=>$charge)));

}


function webinarignition_test_sms() {

            check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );

            $post_input     = filter_input_array(INPUT_POST);

            $webinar_data = WebinarignitionManager::get_webinar_data(sanitize_text_field( $post_input['campaign_id'] ));
            if (empty($webinar_data))
                die();
            $AccountSid     = $webinar_data->twilio_id;
            $AuthToken      = $webinar_data->twilio_token;

            try {

                $client = new Client($AccountSid, $AuthToken);
                $client->messages->create(
                    sanitize_text_field( $post_input['phone_number'] ),
                    array(
                        'from' => $webinar_data->twilio_number,
                        'body' => __( 'You received this message to test WebinarIgnition SMS integration.', "webinarignition")
                    )
                );

                echo json_encode(array('status' => 1));
            } catch (Exception $e) {
                echo json_encode(array('status' => -1, 'errors' => $e->getMessage()));
            }
            die();
}



if(!function_exists('webinarignition_build_time')) { 
    function webinarignition_build_time($date, $time)
    {
        // ReArrange Date To Fit Format
        if (strpos($date, '-')) {
            $exDate = explode("-", $date); 
        } else {
            $exDate = explode("/", $date);
        }

        $exYear  = isset($exDate[2]) ? $exDate[2] : 0;
        $exMonth = isset($exDate[0]) ? $exDate[0] : 0;
        $exDay   = isset($exDate[1]) ? $exDate[1] : 0;

        $newDate = $exYear . "-" . $exMonth . "-" . $exDay . " " . $time;

        return $newDate;
    }
}

// Create Campaign
add_action( 'wp_ajax_webinarignition_create', 'webinarignition_create_callback' );
function webinarignition_create_callback() {
    check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
    if (!current_user_can( 'edit_posts' ) ){
        wp_die( __( 'You do not have permissions to access this page.' ) );
    }
    $post_input     = filter_input_array(INPUT_POST);

    // WP DB Include
    global $wpdb;
    global $wp_locale;
    $table_db_name  = $wpdb->prefix . "webinarignition";
    $clone          = sanitize_text_field($_POST['cloneapp']);

    $importcode     = sanitize_text_field($_POST['importcode']);
    // Save DB Info - Name & Created Date
    $wpdb->insert( $table_db_name, array(
        'appname'      => sanitize_text_field($_POST['appname']),
        'camtype'      => $clone,
        'total_lp'     => '0%%0',
        'total_ty'     => '0%%0',
        'total_live'   => '0%%0',
        'total_replay' => '0%%0',
        'created'      => date( 'F j, Y' )
    ) );

    // Return The ID Of Campaign Created
    $campaignID = $wpdb->insert_id;

    do_action('webinarignition_campaign_created', $campaignID );

    // CREATE A CORRASPONDING POST ::
    $my_post = array(
        'post_title'   => sanitize_text_field($_POST['appname']),
        'post_type'    => 'page',
        'post_content' => __( 'NOT USED -- REPLACED WITH WEBINAR IGNITION CAMPAIGN CONTENT', "webinarignition"),
        'post_status'  => 'publish'
    );

    // Insert the post into the database
    $getPostID = wp_insert_post( $my_post );

    // Add postID to db:
    $wpdb->update( $table_db_name, array( 'postID' => $getPostID ), array( 'id' => $campaignID ) );

    // Set Meta Info so it links this page with the bonus page::
    update_post_meta( $getPostID, 'webinarignitionx_meta_box_select', esc_attr( $campaignID ) );

    $statusCheck  = WebinarignitionLicense::get_license_level();
    $current_locale_temp = determine_locale();

	$is_ultimate_activated = false;
	if( !empty($statusCheck) && (isset($statusCheck->switch) || isset($statusCheck->is_trial)) ) {
		$is_ultimate_activated = !empty($statusCheck->is_trial) || $statusCheck->switch === 'enterprise_powerup'; //If enterprise_powerup, consider it as ultimate
	}

	$show_all_live_languages = true;
    $show_all_eg_languages   = true;

	$applang = isset($post_input["applang"]) ? sanitize_text_field($post_input["applang"]) : null;
	if( empty($applang) || ( ( !$show_all_live_languages && $clone === 'new' ) || ( !$show_all_eg_languages && $clone === 'auto' ) ) ) {
		$applang = 'en_US';
	}

	$settings_language = isset($post_input['settings_language']) ? sanitize_text_field($post_input['settings_language']) : null;

	if( empty($settings_language) ) {
		$settings_language = 'no';
	}

	if( $settings_language === 'no' && ( ( !$show_all_live_languages && $clone === 'new' ) || ( !$show_all_eg_languages && $clone === 'auto' ) ) ) {
		$settings_language = 'yes';
	}

	$post_input['settings_language'] = $settings_language;

    switch_to_locale( $applang );
    unload_textdomain( 'webinarignition' );
    load_textdomain( 'webinarignition', WEBINARIGNITION_PATH . 'languages/webinarignition-' . $applang . '.mo' );

    echo $campaignID;

    // MODEL :: CORE DATA
    add_option( 'webinarignition_campaign_' . $campaignID, "" );

    $maintitle = sanitize_text_field($_POST['appname']);
    
    if ( $clone == "auto" ) {
        $_POST['webinar_date'] = 'AUTO';
    }
    
    $live_date = sanitize_text_field($_POST['webinar_date']);
    
    if ( $clone == "new" ) {
        
        $webinarDateObject          = DateTime::createFromFormat( 'm-d-Y', $live_date);
        if( is_object($webinarDateObject) ){
            $webinarTimestamp       = $webinarDateObject->getTimestamp();    
            $localized_date         = date_i18n( $post_input['date_format_custom'], $webinarTimestamp);
            $localized_month        = $wp_locale->get_month( $webinarDateObject->format('m') );
            $localized_week_day     = $wp_locale->get_weekday( $webinarDateObject->format('w') );
        }

        // NB: Date is stored in DB in format m-d-Y but displayed according to user's chosen option, get_option("date_format").
       if ( ! empty( $live_date ) ):

           $live_date_array        = explode('-', $live_date ); //['m', 'd', 'Y']

       endif;

        if ( $setTime = sanitize_text_field($_POST['webinar_start_time']) ):
            $getTime = date( "h:i:s A", strtotime( $setTime ) );
            $getTime  = explode( " ", $getTime );
            $getHour  = explode( ":", $getTime[0] );
            $getHour2 = $getHour[0];
            // Check for 0 in front of time..
            if ( $getHour2[0] == "0" ) {
                $getHour2 = str_replace( "0", "", $getHour2 );
            }
        endif;

    }

    $timezone = "-5";
    if ( $_POST["webinar_timezone"] != "" ) {
        $timezone = sanitize_text_field($_POST['webinar_timezone']);
    }

    $host = __( 'Your Name', 'webinarignition' );
    if ( $_POST["webinar_host"] != "" ) {
        $host = sanitize_text_field($_POST['webinar_host']);
    }

    $desc = __( 'How We Crush It With Webinars', 'webinarignition' );
    if ( $_POST["webinar_desc"] != "" ) {
        $desc = sanitize_text_field($_POST['webinar_desc']);
    }
    if ( $_POST["cloneapp"] == 1 ) {
        $desc = sanitize_text_field($_POST['appname']);
    }

    $emailSetup = '';

    $emailSetup .= '<p>';

    $emailSetup  .= !empty($statusCheck->account_url) ?  __( 'Hi', 'webinarignition' ) : 'Hi';
    $emailSetup  .= ' {FIRSTNAME}.</p><p>%%INTRO%%</p><p>';

    $emailSetup  .= !empty($statusCheck->account_url) ?  __( 'Date: Join us live on', 'webinarignition' ) : 'Date: Join us live on';
    $emailSetup  .= ' {DATE}</p><p>';

    $emailSetup  .= !empty($statusCheck->account_url) ?  __( 'Webinar Topic', 'webinarignition' ) : 'Webinar Topic';
    $emailSetup  .= ': {TITLE}</p><p>';

    $emailSetup  .= !empty($statusCheck->account_url) ?  __( 'Hosts', 'webinarignition' ) : 'Hosts';
    $emailSetup  .= ': {HOST}</p><p><strong>';

    $emailSetup  .= !empty($statusCheck->account_url) ?  __( 'How To Join The Webinar', 'webinarignition' ) : 'How To Join The Webinar';
    $emailSetup  .= '</strong></p><p>';

    $emailSetup  .= !empty($statusCheck->account_url) ?  __( 'Click the following link to join.', 'webinarignition' ) : 'Click the following link to join.';
    $emailSetup  .= '</p><p></p><p style="text-align:center;">{LINK}</p><p></p>';


    $systemRequirements     = '<p style="font-size: 14px;">';
    $systemRequirements     .= !empty($statusCheck->account_url) ?  __( 'You will be connected to video via your browser using your computer, tablet, or mobile phone\'s microphone and speakers. A headset is recommended.', 'webinarignition' ) : 'You will be connected to video via your browser using your computer, tablet, or mobile phone\'s microphone and speakers. A headset is recommended.';
    $systemRequirements     .= '</p><p style="font-size: 14px;"><strong>';

    $systemRequirements     .= !empty($statusCheck->account_url) ?  __( 'Webinar Requirements', 'webinarignition' ) : 'Webinar Requirements';
    $systemRequirements     .= '</strong></p><p style="font-size: 14px;">';

    $systemRequirements     .= !empty($statusCheck->account_url) ?  __( 'A recent browser version of Mozilla Firefox, Google Chrome, Apple Safari, Microsoft Edge or Opera.', 'webinarignition' ) : 'A recent browser version of Mozilla Firefox, Google Chrome, Apple Safari, Microsoft Edge or Opera.';
    $systemRequirements     .= '</p><p style="font-size: 14px;">';

    $systemRequirements     .= !empty($statusCheck->account_url) ?  __( 'You can join the webinar on mobile, tablet or desktop.', 'webinarignition' ) : 'You can join the webinar on mobile, tablet or desktop.';
    $systemRequirements     .= '</p>';

    $lp_main_headline           = !empty($statusCheck->account_url) ?  __( 'Introducing This Exclusive Webinar From', 'webinarignition' ) : 'Introducing This Exclusive Webinar From';
    $cd_headline                = !empty($statusCheck->account_url) ?  __( 'You Are Viewing A Webinar That Is Not Yet Live - <b>We Go Live Soon!</b>', 'webinarignition' ) : 'You Are Viewing A Webinar That Is Not Yet Live - <b>We Go Live Soon!</b>';
    $webinar_starts_soon        = !empty($statusCheck->account_url) ?  __( 'Webinar Starts Very Soon', 'webinarignition' ) : 'Webinar Starts Very Soon';
    $email_signup_sbj           = !empty($statusCheck->account_url) ?  __( '[Reminder] Your Webinar ::', 'webinarignition' ) : '[Reminder] Your Webinar ::';
    $email_signup_intro         = !empty($statusCheck->account_url) ?  __( 'Here is the webinar information you\'ve just signed up for...', 'webinarignition' ) : 'Here is the webinar information you\'ve just signed up for...';
    $email_notiff_sbj_1         = !empty($statusCheck->account_url) ?  __( 'WEBINAR REMINDER :: Goes Live Tomorrow ::', 'webinarignition' ) : 'WEBINAR REMINDER :: Goes Live Tomorrow ::';
    $email_notiff_body_1        = !empty($statusCheck->account_url) ?  __( 'This is a reminder that the webinar you signed up for is tomorrow...', 'webinarignition' ) : 'This is a reminder that the webinar you signed up for is tomorrow...';
    $email_notiff_sbj_2         = !empty($statusCheck->account_url) ?  __( 'WEBINAR REMINDER :: Goes Live In 1 Hour ::', 'webinarignition' ) : 'WEBINAR REMINDER :: Goes Live In 1 Hour ::';
    $email_signup_heading       = !empty($statusCheck->account_url) ?  __( "Information On The Webinar", 'webinarignition' ) : "Information On The Webinar";
    $email_signup_preview       = !empty($statusCheck->account_url) ?  __( "Here's info on the webinar you've signed up for...", 'webinarignition' ) : "Here's info on the webinar you've signed up for...";
    $email_notiff_1_heading     = !empty($statusCheck->account_url) ?  __( "Information On Tomorrow's Webinar", 'webinarignition' ) : "Information On Tomorrow's Webinar";
    $email_notiff_1_preview     = !empty($statusCheck->account_url) ?  __( "Here's info on tomorrow's webinar...", 'webinarignition' ) : "Here's info on tomorrow's webinar...";
    $email_notiff_2_heading     = !empty($statusCheck->account_url) ?  __( 'Information On Your Webinar', 'webinarignition' ) : 'Information On Your Webinar';
    $email_notiff_2_preview     = !empty($statusCheck->account_url) ?  __( "Here's info on today's webinar...", 'webinarignition' ) : "Here's info on today's webinar...";
    $email_notiff_3_heading     = !empty($statusCheck->account_url) ?  __( 'Information On Your Webinar', 'webinarignition' ) : 'Information On Your Webinar';
    $email_notiff_3_preview     = !empty($statusCheck->account_url) ?  __( "The webinar is live...", 'webinarignition' ) : "The webinar is live...";
    $email_notiff_4_heading     = !empty($statusCheck->account_url) ?  __( 'Replay is live!', 'webinarignition' ) : 'Replay is live!';
    $email_notiff_4_preview     = !empty($statusCheck->account_url) ?  __( "The webinar replay is live...", 'webinarignition' ) : "The webinar replay is live...";
    $email_notiff_5_heading     = !empty($statusCheck->account_url) ?  __( 'Webinar replay is going down soon!', 'webinarignition' ) : 'Webinar replay is going down soon!';
    $email_notiff_5_preview     = !empty($statusCheck->account_url) ?  __( "The webinar replay is going down soon...", 'webinarignition' ) : "The webinar replay is going down soon...";
    $email_notiff_body_2        = !empty($statusCheck->account_url) ?  __( 'The webinar is live in 1 hour!', 'webinarignition' ) : 'The webinar is live in 1 hour!';
    $email_notiff_sbj_3         = !empty($statusCheck->account_url) ?  __( 'We Are Live', 'webinarignition' ) : 'We Are Live';
    $email_notiff_body_3        = !empty($statusCheck->account_url) ?  __( 'We are live, on air and ready to go!', 'webinarignition' ) : 'We are live, on air and ready to go!';
    $email_notiff_sbj_4         = !empty($statusCheck->account_url) ?  __( 'Replay is live!', 'webinarignition' ) : 'Replay is live!';
    $email_notiff_body_4        = !empty($statusCheck->account_url) ?  __( 'We just posted the replay video for the webinar tonight...', 'webinarignition' ) : 'We just posted the replay video for the webinar tonight...';
    $email_notiff_sbj_5         = !empty($statusCheck->account_url) ?  __( 'WEBINAR REPLAY COMING DOWN SOON ::', 'webinarignition' ) : 'WEBINAR REPLAY COMING DOWN SOON ::';
    $email_notiff_body_5        = !empty($statusCheck->account_url) ?  __( 'Did you get a chance to check out the webinar replay? It\'s coming down very soon!', 'webinarignition' ) : 'Did you get a chance to check out the webinar replay? It\'s coming down very soon!';
    $twilio_msg                 = !empty($statusCheck->account_url) ?  __( 'The webinar is starting soon! Jump On Live:', 'webinarignition' ) : 'The webinar is starting soon! Jump On Live:';

    $email_signup_body              = $emailSetup . $systemRequirements;

    // Save Campaign Setup
    if ( $clone == "new" ) {
        $notification_times = webinarignition_live_notification_times( $live_date_array, $setTime );

        // Data For New Webinar
        $dataArray = array(
            "id"                             => (string) $campaignID,
            "webinar_lang"                   => $applang,
            'settings_language'              => $post_input['settings_language'],
            "webinar_desc"                   => $desc,
            "webinar_host"                   => $host,
            "webinar_date"                   => $live_date, //'m-d-Y'
            "webinar_start_time"             => $notification_times['live']['time'],
            "webinar_end_time"               => $notification_times['live']['time'],
            "time_format"                    => get_option( 'time_format' ),
            "webinar_timezone"               => $timezone,
            "lp_metashare_title"             => $maintitle,
            "lp_metashare_desc"              => $desc,
            "lp_main_headline"               => '<h4 class="subheader">' .$lp_main_headline . ' ' . $host . '</h4><h2 style="margin-top: -10px;" id="150">' . $desc . '</h2>',
            "lp_webinar_subheadline"         => "",
            "cd_headline"                    => '<h4 class="subheader">'.$cd_headline.'</h4><h2 style="margin-top: -10px; margin-bottom: 30px;">'.$webinar_starts_soon.'</h2>',
            "email_signup_sbj"               => $email_signup_sbj . " $desc",
            "email_signup_body"              => str_replace( "%%INTRO%%", $email_signup_intro, $email_signup_body ),
            "email_notiff_date_1"            => $notification_times['daybefore']['date'],
            "email_notiff_time_1"            => $notification_times['daybefore']['time'],
            "email_notiff_status_1"          => "queued",
            "email_notiff_sbj_1"             => $email_notiff_sbj_1 . " $desc",
            "email_notiff_body_1"            => str_replace( "%%INTRO%%", $email_notiff_body_1, $emailSetup ),
            "email_notiff_date_2"            => $notification_times['hourbefore']['date'],
            "email_notiff_time_2"            => $notification_times['hourbefore']['time'],
            "email_notiff_status_2"          => "queued",
            "email_notiff_sbj_2"             => $email_notiff_sbj_2 . " $desc",
            "email_signup_heading"           => $email_signup_heading,
            "email_signup_preview"           => $email_signup_preview,
            "email_notiff_1_heading"         => $email_notiff_1_heading,
            "email_notiff_1_preview"         => $email_notiff_1_preview,
            "email_notiff_2_heading"         => $email_notiff_2_heading,
            "email_notiff_2_preview"         => $email_notiff_2_preview,
            "email_notiff_3_heading"         => $email_notiff_3_heading,
            "email_notiff_3_preview"         => $email_notiff_3_preview,
            "email_notiff_4_heading"         => $email_notiff_4_heading,
            "email_notiff_4_preview"         => $email_notiff_4_preview,
            "email_notiff_5_heading"         => $email_notiff_5_heading,
            "email_notiff_5_preview"         => $email_notiff_5_preview,
            "email_notiff_body_2"            => str_replace( "%%INTRO%%", $email_notiff_body_2, $emailSetup ) . $systemRequirements,
            "email_notiff_date_3"            => $notification_times['live']['date'],
            "email_notiff_time_3"            => $notification_times['live']['time'],
            "email_notiff_status_3"          => "queued",
            "email_notiff_sbj_3"             => $email_notiff_sbj_3,
            "email_notiff_body_3"            => str_replace( "%%INTRO%%", $email_notiff_body_3, $emailSetup ) . $systemRequirements,
            "email_notiff_date_4"            => $notification_times['hourafter']['date'],
            "email_notiff_time_4"            => $notification_times['hourafter']['time'],
            "email_notiff_status_4"          => "queued",
            "email_notiff_sbj_4"             => $email_notiff_sbj_4,
            "email_notiff_body_4"            => str_replace( "%%INTRO%%", $email_notiff_body_4 , $emailSetup ),
            "email_notiff_date_5"            => $notification_times['dayafter']['date'],
            "email_notiff_time_5"            => $notification_times['dayafter']['time'],
            "email_notiff_status_5"          => "queued",
            "email_notiff_sbj_5"             => $email_notiff_sbj_5. " $desc",
            "email_notiff_body_5"            => str_replace( "%%INTRO%%", $email_notiff_body_5, $emailSetup ),
            "email_twilio_date"              => $notification_times['live']['date'],
            "email_twilio_time"              => $notification_times['hourbefore']['time'],
            "email_twilio_status"            => "queued",
            "email_twilio"                   => "off",
            "twilio_msg"                     => $twilio_msg . " {LINK}",
            "lp_banner_bg_style"             => "hide",
            "webinar_banner_bg_style"        => "hide",
            'ar_fields_order'                => array( 'ar_name', 'ar_email' ),
            'ar_required_fields'             => array( 'ar_name', 'ar_email' ),
            'ar_name'                        => '',
            'ar_email'                       => '',
            'lp_optin_name'                  => __( "Enter your first name:", "webinarignition" ),
            'lp_optin_email'                 => __( "Enter your email:", "webinarignition" ),
            'ar_hidden'                      => '',
            'fb_id'                          => '',
            'fb_secret'                      => '',
            'ty_share_image'                 => '',
            'ar_url'                         => '',
            'ar_method'                      => '',
            'lp_background_color'            => '',
            'lp_background_image'            => '',
            'lp_cta_bg_color'                => 'transparent',
            'lp_cta_type'                    => '',
            'lp_cta_video_url'               => '',
            'lp_cta_video_code'              => '',
            'lp_sales_headline'              => '',
            'lp_sales_headline_color'        => '',
            'lp_sales_copy'                  => '',
            'lp_optin_headline'              => '',
            'lp_webinar_host_block'          => '',
            'lp_host_image'                  => '',
            'lp_host_info'                   => '',
            'paid_status'                    => '',
            'ar_code'                        => '',
            'lp_fb_button'                   => '',
            'ar_custom_date_format'          => '',
            'lp_optin_button'                => '',
            'lp_optin_btn_color'             => '',
            'lp_optin_spam'                  => '',
            'lp_optin_closed'                => '',
            'custom_ty_url_state'            => '',
            'ty_ticket_headline'             => '',
            'ty_ticket_subheadline'          => '',
            'ty_cta_bg_color'                => 'transparent',
            'ty_cta_type'                    => '',
            'ty_cta_html'                    => '',
            'ty_webinar_headline'            => '',
            'ty_webinar_subheadline'         => '',
            'ty_webinar_url'                 => '',
            'ty_share_toggle'                => '',
            'ty_step2_headline'              => '',
            'ty_fb_share'                    => '',
            'ty_tw_share'                    => '',
            'ty_share_intro'                 => '',
            'ty_share_reveal'                => '',
            'webinar_switch'                 => 'countdown',
            'total_cd'                       => '',
            'cd_button_show'                 => '',
            'cd_button_copy'                 => '',
            'cd_button_color'                => '',
            'cd_button'                      => '',
            'cd_button_url'                  => '',
            'cd_headline2'                   => '',
            'cd_months'                      => '',
            'cd_weeks'                       => '',
            'cd_days'                        => '',
            'cd_hours'                       => '',
            'cd_minutes'                     => '',
            'cd_seconds'                     => '',
            'webinar_info_block'             => '',
            'webinar_info_block_title'       => '',
            'webinar_info_block_host'        => '',
            'webinar_info_block_eventtitle'  => '',
            'webinar_info_block_desc'        => '',
            'privacy_status'                 => '',
            'webinar_live_video'             => '',
            'webinar_live_bgcolor'           => '',
            'webinar_banner_bg_color'        => '',
            'webinar_banner_bg_repeater'     => '',
            'webinar_banner_image'           => '',
            'webinar_background_color'       => '',
            'webinar_background_image'       => '',
            'webinar_qa_title'               => '',
            'webinar_qa'                     => '',
            'webinar_qa_name_placeholder'    => '',
            'webinar_qa_email_placeholder'   => '',
            'webinar_qa_desc_placeholder'    => '',
            'webinar_qa_button'              => '',
            'webinar_qa_button_color'        => '',
            'webinar_qa_thankyou'            => '',
            'webinar_qa_custom'              => '',
            'webinar_speaker'                => '',
            'webinar_speaker_color'          => '',
            'social_share_links'             => '',
            'webinar_invite'                 => '',
            'webinar_invite_color'           => '',
            'webinar_fb_share'               => '',
            'webinar_tw_share'               => '',
            'webinar_ld_share'               => '',
            'webinar_callin'                 => '',
            'webinar_callin_copy'            => '',
            'webinar_callin_color'           => '',
            'webinar_callin_number'          => '',
            'webinar_callin_color2'          => '',
            'webinar_live'                   => '',
            'webinar_live_color'             => '',
            'webinar_giveaway_toggle'        => '',
            'webinar_giveaway_title'         => '',
            'webinar_giveaway'               => '',
            'lp_banner_bg_color'             => '',
            'lp_banner_bg_repeater'          => '',
            'lp_banner_image'                => '',
            'lp_cta_image'                   => '',
            'paid_headline'                  => '',
            'paid_button_type'               => '',
            'paid_button_custom'             => '',
            'payment_form'                   => '',
            'paypal_paid_btn_copy'           => '',
            'paid_btn_color'                 => '',
            'stripe_secret_key'              => '',
            'stripe_publishable_key'         => '',
            'stripe_charge'                  => '',
            'stripe_charge_description'      => '',
            'stripe_paid_btn_copy'           => '',
            'paid_pay_url'                   => '',
            'lp_fb_copy'                     => '',
            'lp_fb_or'                       => '',
            'lp_optin_btn_image'             => '',
            'lp_optin_btn'                   => '',
            'custom_ty_url'                  => '',
            'ty_cta_video_url'               => '',
            'ty_cta_video_code'              => '',
            'ty_cta_image'                   => '',
            'ty_werbinar_custom_url'         => '',
            'ty_ticket_webinar_option'       => '',
            'ty_ticket_webinar'              => '',
            'ty_webinar_option_custom_title' => '',
            'ty_ticket_host_option'          => '',
            'ty_ticket_host'                 => '',
            'ty_webinar_option_custom_host'  => '',
            'ty_ticket_date_option'          => '',
            'ty_ticket_date'                 => '',
            'ty_webinar_option_custom_date'  => '',
            'ty_ticket_time_option'          => '',
            'ty_ticket_time'                 => '',
            'ty_webinar_option_custom_time'  => '',
            'tycd_countdown'                 => '',
            'tycd_progress'                  => '',
            'tycd_years'                     => '',
            'tycd_months'                    => '',
            'tycd_weeks'                     => '',
            'tycd_days'                      => '',
            'ty_add_to_calendar_option'      => '',
            'ty_calendar_headline'           => '',
            'ty_calendar_google'             => '',
            'ty_calendar_ical'               => '',
            'skip_ty_page'                   => '',
            'txt_area'                       => 'off',
            'txt_headline'                   => '',
            'txt_placeholder'                => '',
            'txt_btn'                        => '',
            'txt_reveal'                     => '',
            'replay_video'                   => '',
            'replay_optional'                => '',
            'replay_cd_date'                 => '',
            'replay_cd_time'                 => '',
            'replay_cd_headline'             => '',
            'replay_timed_style'             => '',
            'replay_order_copy'              => '',
            'replay_order_url'               => '',
            'replay_order_html'              => '',
            'replay_order_time'              => '',
            'replay_closed'                  => '',
            'footer_copy'                    => '',
            'footer_branding'                => 'hide',
            'custom_lp_js'                   => '',
            'custom_lp_css'                  => '',
            'meta_site_title_ty'             => '',
            'meta_desc_ty'                   => '',
            'custom_ty_js'                   => '',
            'custom_ty_css'                  => '',
            'meta_site_title_webinar'        => '',
            'meta_desc_webinar'              => '',
            'custom_webinar_js'              => '',
            'custom_webinar_css'             => '',
            'meta_site_title_replay'         => '',
            'meta_desc_replay'               => '',
            'custom_replay_js'               => '',
            'custom_replay_css'              => '',
            'footer_code'                    => '',
            'footer_code_ty'                 => '',
            'live_stats'                     => '',
            'wp_head_footer'                 => '',
            'email_signup'                   => '',
            'email_notiff_1'                 => '',
            'email_notiff_2'                 => '',
            'email_notiff_3'                 => '',
            'email_notiff_4'                 => '',
            'email_notiff_5'                 => '',
            'twilio_id'                      => '',
            'twilio_token'                   => '',
            'twilio_number'                  => '',
            'webinar_live_overlay'           => '1',
            'replay_order_color'             => '',
            'air_toggle'                     => '',
            'protected_webinar_id'           => 'protected',
            'protected_lead_id'              => 'protected',
            'protected_webinar_redirection'  => '',
            'limit_lead_visit'               => 'enabled',
            'limit_lead_timer'               => '30',
            'webinar_status'                 => 'draft',
            'cta_position'                   => 'overlay',
            'console_q_notifications'        => 'no',
            'qstn_notification_email_sbj'    => __( "You have new support questions for webinar ", "webinarignition" ) . $desc,
            'enable_first_question_notification'            => 'no',
            'enable_after_webinar_question_notification'    => 'no',
            'first_question_notification_sent'              => 'no',
            'after_webinar_question_notification_sent'      => 'no',
            'qstn_notification_email_body'    => __( "Hi", "webinarignition" ) . " {support}, {attendee} " . __( "has asked a question in the", "webinarignition" ) . " {webinarTitle} " . __( "webinar and needs an answer. Click", "webinarignition" ) . " {link} ". __( "to answer this question now.", "webinarignition" ),
            'templates_version'               => 2,
            'date_format'                     => $post_input['date_format_custom'],
            'time_format'                     => $post_input['time_format'],
            'settings_language'               => $post_input['settings_language'],
            'display_tz'                      => 'no'

        );

        $obj = webinarignition_array_to_object( $dataArray );

        // no clone - new
        update_option( 'webinarignition_campaign_' . $campaignID, $obj );
    } else if ( $clone == "import" ) {
        // importing campaign -- update Name & Permalink
        $importcode               = trim( $importcode );
        $webinar                  = maybe_unserialize( base64_decode( $importcode ) );
        $webinar->webinarURLName2   = sanitize_text_field( $_POST['appname'] );
        $webinar->webinar_permalink = get_permalink($getPostID);
        $webinar->id = (string) $campaignID;
        update_option( 'webinarignition_campaign_' . $campaignID, $webinar );
    } else if ( $clone == "auto" ) {
        // Data For New Webinar

        $webinar_starts_soon                    = !empty($statusCheck->account_url) ?  __( 'Webinar Starts Very Soon', 'webinarignition' ) : 'Webinar Starts Very Soon';
        $email_signup_sbj                       = !empty($statusCheck->account_url) ?  __( '[Reminder] Your Webinar Information', 'webinarignition' ) : '[Reminder] Your Webinar Information';

        $dataArray = array(
            "id"                               => (string) $campaignID,
            "webinar_lang"                      => $applang,
            'settings_language'                => $post_input['settings_language'],
            "webinar_desc"                     => $desc,
            "webinar_host"                     => $host,
            "webinar_date"                     => "AUTO",
            "lp_metashare_title"               => $maintitle,
            "lp_metashare_desc"                => $desc,
            "lp_main_headline"                 => '<h4 class="subheader">'.$lp_main_headline. ' '. $host . '</h4><h2 style="margin-top: -10px;" id="229">' . $desc . '</h2>',
            "cd_headline"                      => '<h4 class="subheader">'.$cd_headline.'</h4><h2 style="margin-top: -10px; margin-bottom: 30px;">'.$webinar_starts_soon.'</h2>',
            "email_signup_sbj"                 => $email_signup_sbj,
            "email_signup_body"                => str_replace( "%%INTRO%%", $email_signup_intro, $email_signup_body ),
            "email_notiff_sbj_1"               => $email_notiff_sbj_1 . " $desc",
            "email_notiff_body_1"              => str_replace( "%%INTRO%%", $email_notiff_body_1, $emailSetup ),
            "email_notiff_sbj_2"               => $email_notiff_sbj_2 . " $desc",
            "email_signup_heading"             => $email_signup_heading,
            "email_signup_preview"             => $email_signup_preview,
            "email_notiff_1_heading"           => $email_notiff_1_heading,
            "email_notiff_1_preview"           => $email_notiff_1_preview,
            "email_notiff_2_heading"           => $email_notiff_2_heading,
            "email_notiff_2_preview"           => $email_notiff_2_preview,
            "email_notiff_3_heading"           => $email_notiff_3_heading,
            "email_notiff_3_preview"           => $email_notiff_3_preview,
            "email_notiff_4_heading"           => $email_notiff_4_heading,
            "email_notiff_4_preview"           => $email_notiff_4_preview,
            "email_notiff_5_heading"           => $email_notiff_5_heading,
            "email_notiff_5_preview"           => $email_notiff_5_preview,
            "email_notiff_body_2"              => str_replace( "%%INTRO%%", $email_notiff_body_2, $emailSetup ). $systemRequirements,
            "email_notiff_sbj_3"               => $email_notiff_sbj_3,
            "email_notiff_body_3"              => str_replace( "%%INTRO%%", $email_notiff_body_3, $emailSetup ). $systemRequirements,
            "email_notiff_sbj_4"               => $email_notiff_sbj_4,
            "email_notiff_body_4"              => str_replace( "%%INTRO%%", $email_notiff_body_4, $emailSetup ),
            "email_notiff_sbj_5"               => $email_notiff_sbj_5. " $desc",
            "email_notiff_body_5"              => str_replace( "%%INTRO%%", $email_notiff_body_5, $emailSetup ),
            "twilio_msg"                       => $twilio_msg. " {LINK}",
            "email_twilio"                     => "off",
            "lp_banner_bg_style"               => "hide",
            "webinar_banner_bg_style"          => "hide",
            "auto_saturday"                    => "yes",
            "auto_sunday"                      => "yes",
            "auto_thursday"                    => "yes",
            "auto_monday"                      => "yes",
            "auto_friday"                      => "yes",
            "auto_tuesday"                     => "yes",
            "auto_wednesday"                   => "yes",
            "auto_time_1"                      => "16:00",
            "auto_time_2"                      => "18:00",
            "auto_time_3"                      => "20:00",
            "auto_video_length"                => "60",
            "auto_translate_local"             => "Local Time",
            'ar_fields_order'                  => array( 'ar_name', 'ar_email' ),
            'ar_required_fields'               => array( 'ar_name', 'ar_email' ),
            'ar_name'                          => '',
            'ar_email'                         => '',
            'lp_optin_name'                    => __( "Enter your first name:", "webinarignition" ),
            'lp_optin_email'                   => __( "Enter your email:", "webinarignition" ),
            'lp_schedule_type'                 => 'customized',
            'auto_today'                       => 'yes',
            'auto_day_offset'                  => 0,
            'auto_day_limit'                   => 7,
            'auto_blacklisted_dates'           => '',
            'auto_timezone_type'               => 'user_specific',
            'lp_background_color'              => '',
            'lp_background_image'              => '',
            'ty_share_image'                   => '',
            'lp_cta_bg_color'                  => 'transparent',
            'lp_cta_type'                      => '',
            'lp_cta_video_url'                 => '',
            'lp_cta_video_code'                => '',
            'lp_sales_headline'                => '',
            'lp_sales_headline_color'          => '',
            'lp_sales_copy'                    => '',
            'lp_optin_headline'                => '',
            'lp_webinar_host_block'            => '',
            'lp_host_image'                    => '',
            'lp_host_info'                     => '',
            'paid_status'                      => '',
            'ar_code'                          => '',
            'ar_custom_date_format'            => '',
            'lp_optin_button'                  => '',
            'lp_optin_btn_color'               => '',
            'lp_optin_spam'                    => '',
            'lp_optin_closed'                  => '',
            'custom_ty_url_state'              => '',
            'ty_ticket_headline'               => '',
            'ty_ticket_subheadline'            => '',
            'ty_cta_bg_color'                => 'transparent',
            'ty_cta_type'                      => '',
            'ty_cta_html'                      => '',
            'ty_webinar_headline'              => '',
            'ty_webinar_subheadline'           => '',
            'ty_webinar_url'                   => '',
            'ty_share_toggle'                  => '',
            'ty_step2_headline'                => '',
            'ty_fb_share'                      => '',
            'ty_tw_share'                      => '',
            'ty_share_intro'                   => '',
            'ty_share_reveal'                  => '',
            'webinar_switch'                   => 'countdown',
            'total_cd'                         => '',
            'cd_button_show'                   => '',
            'cd_button_copy'                   => '',
            'cd_button_color'                  => '',
            'cd_button'                        => '',
            'cd_button_url'                    => '',
            'cd_headline2'                     => '',
            'cd_months'                        => '',
            'cd_weeks'                         => '',
            'cd_days'                          => '',
            'cd_hours'                         => '',
            'cd_minutes'                       => '',
            'cd_seconds'                       => '',
            'webinar_info_block'               => '',
            'webinar_info_block_title'         => '',
            'webinar_info_block_host'          => '',
            'webinar_info_block_eventtitle'    => '',
            'webinar_info_block_desc'          => '',
            'privacy_status'                   => '',
            'webinar_live_video'               => '',
            'webinar_live_overlay'             => '1',
            'webinar_live_bgcolor'             => '',
            'webinar_banner_bg_color'          => '',
            'webinar_banner_bg_repeater'       => '',
            'webinar_banner_image'             => '',
            'webinar_background_color'         => '',
            'webinar_background_image'         => '',
            'webinar_qa_title'                 => '',
            'webinar_qa'                       => '',
            'webinar_qa_name_placeholder'      => '',
            'webinar_qa_email_placeholder'     => '',
            'webinar_qa_desc_placeholder'      => '',
            'webinar_qa_button'                => '',
            'webinar_qa_button_color'          => '',
            'webinar_qa_thankyou'              => '',
            'webinar_qa_custom'                => '',
            'webinar_speaker'                  => '',
            'webinar_speaker_color'            => '',
            'social_share_links'               => '',
            'webinar_invite'                   => '',
            'webinar_invite_color'             => '',
            'webinar_fb_share'                 => '',
            'webinar_tw_share'                 => '',
            'webinar_ld_share'                 => '',
            'webinar_callin'                   => '',
            'webinar_callin_copy'              => '',
            'webinar_callin_color'             => '',
            'webinar_callin_number'            => '',
            'webinar_callin_color2'            => '',
            'webinar_live'                     => '',
            'webinar_live_color'               => '',
            'webinar_giveaway_toggle'          => '',
            'webinar_giveaway_title'           => '',
            'webinar_giveaway'                 => '',
            'lp_banner_bg_color'               => '',
            'lp_banner_bg_repeater'            => '',
            'lp_banner_image'                  => '',
            'lp_cta_image'                     => '',
            'paid_headline'                    => '',
            'paid_button_type'                 => '',
            'paid_button_custom'               => '',
            'payment_form'                     => '',
            'paid_btn_copy'                    => '',
            'paid_btn_color'                   => '',
            'stripe_secret_key'                => '',
            'stripe_publishable_key'           => '',
            'stripe_charge'                    => '',
            'stripe_charge_description'        => '',
            'paid_pay_url'                     => '',
            'lp_fb_copy'                       => '',
            'lp_fb_or'                         => '',
            'lp_optin_btn_image'               => '',
            'lp_optin_btn'                     => '',
            'custom_ty_url'                    => '',
            'ty_cta_video_url'                 => '',
            'ty_cta_video_code'                => '',
            'ty_cta_image'                     => '',
            'ty_werbinar_custom_url'           => '',
            'ty_ticket_webinar_option'         => '',
            'ty_ticket_webinar'                => '',
            'ty_webinar_option_custom_title'   => '',
            'ty_ticket_host_option'            => '',
            'ty_ticket_host'                   => '',
            'ty_webinar_option_custom_host'    => '',
            'ty_ticket_date_option'            => '',
            'ty_ticket_date'                   => '',
            'ty_webinar_option_custom_date'    => '',
            'ty_ticket_time_option'            => '',
            'ty_ticket_time'                   => '',
            'ty_webinar_option_custom_time'    => '',
            'tycd_countdown'                   => '',
            'tycd_progress'                    => '',
            'tycd_years'                       => '',
            'tycd_months'                      => '',
            'tycd_weeks'                       => '',
            'tycd_days'                        => '',
            'ty_add_to_calendar_option'        => '',
            'ty_calendar_headline'             => '',
            'ty_calendar_google'               => '',
            'ty_calendar_ical'                 => '',
            'skip_ty_page'                     => '',
            'txt_area'                         => 'off',
            'skip_instant_acces_confirm_page'  => 'yes',
            'txt_headline'                     => '',
            'txt_placeholder'                  => '',
            'txt_btn'                          => '',
            'txt_reveal'                       => '',
            'replay_video'                     => '',
            'replay_optional'                  => '',
            'replay_cd_date'                   => '',
            'replay_cd_time'                   => '',
            'replay_cd_headline'               => '',
            'replay_timed_style'               => '',
            'replay_order_copy'                => '',
            'replay_order_url'                 => '',
            'replay_order_html'                => '',
            'replay_order_time'                => '',
            'replay_closed'                    => '',
            'footer_copy'                      => '',
            'footer_branding'                  => '',
            'custom_lp_js'                     => '',
            'custom_lp_css'                    => '',
            'meta_site_title_ty'               => '',
            'meta_desc_ty'                     => '',
            'custom_ty_js'                     => '',
            'custom_ty_css'                    => '',
            'meta_site_title_webinar'          => '',
            'meta_desc_webinar'                => '',
            'custom_webinar_js'                => '',
            'custom_webinar_css'               => '',
            'meta_site_title_replay'           => '',
            'meta_desc_replay'                 => '',
            'custom_replay_js'                 => '',
            'custom_replay_css'                => '',
            'footer_code'                      => '',
            'footer_code_ty'                   => '',
            'live_stats'                       => '',
            'wp_head_footer'                   => '',
            'email_signup'                     => '',
            'email_notiff_1'                   => '',
            'email_notiff_2'                   => '',
            'email_notiff_3'                   => '',
            'email_notiff_4'                   => '',
            'email_notiff_5'                   => '',
            'twilio_id'                        => '',
            'twilio_token'                     => '',
            'twilio_number'                    => '',
            'webinar_source_toggle'            => '',
            'auto_video_url'                   => '',
            'auto_video_load'                  => '',
            'webinar_show_videojs_controls'    => '',
            'webinar_iframe_source'            => '',
            'auto_action'                      => '',
            'auto_action_time'                 => '',
            'auto_action_copy'                 => '',
            'auto_action_btn_copy'             => '',
            'auto_action_url'                  => '',
            'replay_order_color'               => '',
            'auto_redirect'                    => '',
            'auto_redirect_url'                => '',
            'auto_redirect_delay'              => 0,
            'auto_timezone_custom'             => '',
            'auto_time_fixed'                  => '',
            'auto_timezone_fixed'              => '',
            'delayed_day_offset'               => '',
            'auto_time_delayed'                => '',
            'delayed_timezone_type'            => '',
            'auto_timezone_user_specific_name' => '',
            'auto_timezone_delayed'            => '',
            'delayed_blacklisted_dates'        => '',
            'auto_translate_instant'           => '',
            'auto_translate_headline1'         => '',
            'auto_translate_subheadline1'      => '',
            'auto_translate_headline2'         => '',
            'auto_translate_subheadline2'      => '',
            'lp_webinar_subheadline'           => '',
            'fb_id'                            => '',
            'fb_secret'                        => '',
            'auto_video_url2'                  => '',
            'auto_date_fixed'                  => '',
            'auto_replay'                      => '',
            'protected_webinar_id'           => 'protected',
            'protected_lead_id'              => 'protected',
            'protected_webinar_redirection'  => '',
            'limit_lead_visit'               => 'enabled',
            'limit_lead_timer'               => '30',
            'webinar_status'                 => 'draft',
            'cta_position'                   => 'overlay',
            'console_q_notifications'        => 'no',
            'qstn_notification_email_sbj'    => __( "You have new support questions for webinar ", "webinarignition" ) . $desc,
            'enable_first_question_notification'            => 'no',
            'enable_after_webinar_question_notification'    => 'no',
            'first_question_notification_sent'              => 'no',
            'after_webinar_question_notification_sent'      => 'no',
            'qstn_notification_email_body'    => __( "Hi", "webinarignition" ) . " {support}, {attendee} " . __( "has asked a question in the", "webinarignition" ) . " {webinarTitle} " . __( "webinar and needs an answer. Click", "webinarignition" ) . " {link} ". __( "to answer this question now.", "webinarignition" ),
            'templates_version'             => 2,
            'date_format'                     => $post_input['date_format_custom'],
            'time_format'                     => $post_input['time_format'],
            'auto_weekdays_1'                 => ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'],
            'auto_weekdays_2'                 => ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'],
            'auto_weekdays_3'                 => ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'],
            'display_tz'                      => 'no'

        );
        $obj       = webinarignition_array_to_object( $dataArray );

        $obj->wi_show_day = ( isset($post_input['wi_show_day']) && !empty($post_input['wi_show_day']) ) ? 1 : 0;
        $obj->day_string  = ( isset($post_input['day_string']) && !empty($post_input['day_string']) ) ? sanitize_text_field( $post_input['day_string'] ) : 'D';

        // save
        update_option( 'webinarignition_campaign_' . $campaignID, $obj );
    } else {
        // get option from parent campaign
        $cloneParent = WebinarignitionManager::get_webinar_data($clone);

        $cloneParent->id                = (string) $campaignID;
        $cloneParent->webinarURLName2    = sanitize_text_field( $_POST['appname'] );
        $cloneParent->webinar_desc       = sanitize_text_field( $_POST['appname'] );
        $cloneParent->lp_metashare_title = sanitize_text_field( $_POST['appname'] );
        $cloneParent->lp_metashare_desc  = sanitize_text_field( $_POST['appname'] );

        $cloneParent->lp_main_headline = "<h4 class='subheader'>" . $lp_main_headline  ." ". $cloneParent->webinar_host . "</h4><h2 style='margin-top: -10px;'>" . $desc . "</h2>";

        update_option( 'webinarignition_campaign_' . $campaignID, $cloneParent );
    }

    $sql = "SELECT * FROM {$wpdb->options} WHERE option_name LIKE 'webinarignition_campaign_{$campaignID}'";
    $webinar = $wpdb->get_row($sql, ARRAY_A);

    $webinar_settings_string = $webinar['option_id'] . $webinar['option_value'];
    $webinar_hashed_id = sha1($webinar_settings_string);

    $map = get_option('webinarignition_map_campaign_hash_to_id', []);
    $map_rev = get_option('webinarignition_map_campaign_id_to_hash', []);

    $map[$webinar_hashed_id] = $campaignID;
    $map_rev[$campaignID] = $webinar_hashed_id;

    update_option('webinarignition_map_campaign_hash_to_id', $map);
    update_option('webinarignition_map_campaign_id_to_hash', $map_rev);

    switch_to_locale( $current_locale_temp );

    // *****************************************************************************

    die();
}

// Edit Campaign
add_action( 'wp_ajax_webinarignition_edit', 'webinarignition_edit_callback' );
function webinarignition_edit_callback() {
    if (!current_user_can('edit_posts')) {return;}

    check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
    $post_input     = filter_input_array(INPUT_POST);

    // NB: Date is stored in DB in format m-d-Y but displayed according to user's chosen option
    $live_date = sanitize_text_field($post_input['webinar_date']);
    if ( ! empty( $live_date ) && ("AUTO" != $live_date)  ):

        $live_date                          = $post_input['webinar_date_submit'];
        $post_input['webinar_date']         = $post_input['webinar_date_submit'];
        $live_date_array                    = explode('-', $live_date ); //['m', 'd', 'Y']

        $post_input['email_notiff_date_1']  = $post_input['email_notiff_date_1_submit'];
        $post_input['email_notiff_date_2']  = $post_input['email_notiff_date_2_submit'];
        $post_input['email_notiff_date_3']  = $post_input['email_notiff_date_3_submit'];
        $post_input['email_notiff_date_4']  = $post_input['email_notiff_date_4_submit'];
        $post_input['email_notiff_date_5']  = $post_input['email_notiff_date_5_submit'];

        $post_input['email_notiff_time_1']  = $post_input['email_notiff_time_1_submit'];
        $post_input['email_notiff_time_2']  = $post_input['email_notiff_time_2_submit'];
        $post_input['email_notiff_time_3']  = $post_input['email_notiff_time_3_submit'];
        $post_input['email_notiff_time_4']  = $post_input['email_notiff_time_4_submit'];
        $post_input['email_notiff_time_5']  = $post_input['email_notiff_time_5_submit'];
        $post_input['webinar_start_time']   = $post_input['webinar_start_time_submit'];

        $post_input['email_twilio_date']    = $post_input['email_twilio_date_submit'];
        $post_input['email_twilio_time']    = $post_input['email_twilio_time_submit'];

        $post_input['replay_cd_date']       = $post_input['replay_cd_date_submit'];


    endif;


    if ( ("AUTO" == $live_date) && ( $post_input['lp_schedule_type'] == 'fixed'  )  ):
        $auto_date_fixed_date_object        = DateTime::createFromFormat( 'm-d-Y',  $post_input['auto_date_fixed_submit'] );
        $post_input['auto_date_fixed']      = $auto_date_fixed_date_object->format('Y-m-d');
    endif;



    // Get ID & Post Data Array
    $id             = sanitize_text_field( $post_input['id'] );
    $data           = $post_input;

    //get old webinar data for comparison
    $old_webinar_data = WebinarignitionManager::get_webinar_data($id);

    $additional_autoactions = array();
    $auto_action_time_array = array();
    $auto_action_time_end_array = array();

    foreach ($post_input as $key => $value) {
        if (is_string($key) && false !== strpos($key, 'additional-autoaction__')) {
            $additional_autoaction = explode('__', $key);

            if (3 === count($additional_autoaction) && !empty($additional_autoaction[2])) {
                $index = $additional_autoaction[2];
                $field = $additional_autoaction[1];

                if ($field === 'auto_action_time_min') {

                } elseif ($field === 'auto_action_time_sec') {

                } elseif ($field === 'auto_action_time') {
                    $additional_autoactions[$index][$field] = stripslashes($value);
                } elseif ($field === 'auto_action_time_end') {
                    $additional_autoactions[$index][$field] = stripslashes($value);
                } else {
                    $additional_autoactions[$index][$field] = stripslashes($value);
                }

                unset($post_input[$key]);
                unset($data[$key]);
            } else {
                unset($post_input[$key]);
                unset($data[$key]);
            }
        }

        if ($key === 'auto_action_time_min') {
            $auto_action_time_array['min'] = (int)$value;
            unset($post_input[$key]);
            unset($data[$key]);
        }

        if ($key === 'auto_action_time_sec') {
            $auto_action_time_array['sec'] = (int)$value;
            unset($post_input[$key]);
            unset($data[$key]);
        }

        if ($key === 'auto_action_time_end_min') {
            $auto_action_time_end_array['min'] = (int)$value;
            unset($post_input[$key]);
            unset($data[$key]);
        }

        if ($key === 'auto_action_time_end_sec') {
            $auto_action_time_end_array['sec'] = (int)$value;
            unset($post_input[$key]);
            unset($data[$key]);
        }
    }

    if (!empty($auto_action_time_array)) {
        $auto_action_time = '';

        if (!empty($auto_action_time_array['min'])) {
            $auto_action_time .= $auto_action_time_array['min'];
        } else {
            $auto_action_time .= '0';
        }

        if (!empty($auto_action_time_array['sec'])) {
            $sec = (int)$auto_action_time_array['sec'];

            if ($sec < 10) {
                $sec = '0' . $sec;
            } elseif ($sec > 60) {
                $sec = '60';
            }

            $auto_action_time .= ':' . $sec;
        } else {
            $auto_action_time .= ':00';
        }

    } else {
        $auto_action_time = !empty($data['auto_action_time']) ? $data['auto_action_time'] : '';
    }

    if (!empty($auto_action_time_end_array)) {
        $auto_action_time_end = '';

        if (!empty($auto_action_time_end_array['min'])) {
            $auto_action_time_end .= $auto_action_time_end_array['min'];
        } else {
            $auto_action_time_end .= '0';
        }

        if (!empty($auto_action_time_end_array['sec'])) {
            $sec = (int)$auto_action_time_end_array['sec'];

            if ($sec < 10) {
                $sec = '0' . $sec;
            } elseif ($sec > 60) {
                $sec = '60';
            }

            $auto_action_time_end .= ':' . $sec;
        } else {
            $auto_action_time_end .= ':00';
        }

    } else {
        $auto_action_time_end = !empty($data['auto_action_time_end']) ? $data['auto_action_time_end'] : '';
    }

    if (empty($additional_autoactions) && isset($post_input['additional_autoactions_serialise'])) {
        $data['additional_autoactions'] = $post_input['additional_autoactions_serialise'];
    } else {
        $data['additional_autoactions'] = serialize($additional_autoactions);
    }

    $data['auto_action_time'] = $auto_action_time;
    $data['auto_action_time_end'] = $auto_action_time_end;

    // fix issue where default webinar length and iframe video length settings override each other.
    if ( isset($post_input['webinar_source_toggle']) && $post_input['webinar_source_toggle'] === 'default')  {
        if ( !empty($post_input['auto_video_length_default']) && is_numeric($post_input['auto_video_length_default']) ) {
            $data['auto_video_length'] = $post_input['auto_video_length_default'];
        }
    }

    // change Youtube urls (iframe only) from http to https
    $youtubeUrlsToCheck = array(
        'webinar_live_video', // live webinar video
        'replay_video', // live replay
        'webinar_iframe_source', // evergreen iframe (same for replay)
    );

    foreach ($youtubeUrlsToCheck as $formFieldName) {
        if (!empty($data[$formFieldName])) {
            $wi_iframe = $data[$formFieldName];
            if (strpos($wi_iframe, 'youtube') || strpos($wi_iframe, 'youtu.be')) {
                $wi_iframe = str_replace('http://', 'https://', $wi_iframe);
                $data[$formFieldName] = $wi_iframe;
            }
        }
    }

    if(isset($post_input['webinar_source_toggle']) && $post_input['webinar_source_toggle'] == 'iframe') {
        $data['webinar_live_overlay'] = $post_input['webinar_live_overlay1'];
        unset($data['webinar_live_overlay1']);
    }

    foreach ($data as $key => $value) {
        $data[$key] = !is_array($value) ? stripslashes($value) : $value;
    }

    // Convert Array To Object
    $object = webinarignition_array_to_object($data);

    if (strpos($object->webinar_date, '-')) {
        $fullDate = explode("-", $object->webinar_date);
    } else {
        $fullDate = explode("/", $object->webinar_date);
    }

    $webinar_data = WebinarignitionManager::get_webinar_data($id);

    if( !empty( $webinar_data->webinar_start_time ) && ( strtotime(webinarignition_build_time($webinar_data->webinar_date, $webinar_data->webinar_start_time)) != strtotime(webinarignition_build_time($object->webinar_date  , $object->webinar_start_time)) )  ){
        //Webinar Date has changed
        //Update email notification dates & times
        $notification_times = webinarignition_live_notification_times( $fullDate, $object->webinar_start_time );

        $object->email_notiff_date_1 = $notification_times[ 'daybefore' ][ 'date' ];
        $object->email_notiff_time_1 = $notification_times[ 'daybefore' ][ 'time' ];

        $object->email_notiff_date_2 = $notification_times[ 'hourbefore' ][ 'date' ];
        $object->email_notiff_time_2 = $notification_times[ 'hourbefore' ][ 'time' ];

        $object->email_notiff_date_3 = $notification_times[ 'live' ][ 'date' ];
        $object->email_notiff_time_3 = $notification_times[ 'live' ][ 'time' ];

        $object->email_notiff_date_4 = $notification_times[ 'hourafter' ][ 'date' ];
        $object->email_notiff_time_4 = $notification_times[ 'hourafter' ][ 'time' ];

        $object->email_notiff_date_5 = $notification_times[ 'dayafter' ][ 'date' ];
        $object->email_notiff_time_5 = $notification_times[ 'dayafter' ][ 'time' ];

        $object->email_twilio_date = $notification_times[ 'live' ][ 'date' ];
        $object->email_twilio_time = $notification_times[ 'hourbefore' ][ 'time' ];
    }

    //just in case date or time formats have backslashes, as in 'j \d\e F \d\e Y'
    $object->date_format = $post_input['date_format_custom'];
    $object->time_format = $post_input['time_format'];

    if ( "AUTO" == $live_date ) {
	    $object->wi_show_day = ( isset($post_input['wi_show_day']) && !empty($post_input['wi_show_day']) ) ? 1 : 0;
	    $object->day_string  = ( isset($post_input['day_string']) && !empty($post_input['day_string']) ) ? sanitize_text_field( $post_input['day_string'] ) : 'D';
	}

    //Keep air CTA settings intact on webinar save
	$air_cta_fields = ['air_toggle','air_btn_copy','air_btn_url','air_btn_color','air_html'];

    foreach( $air_cta_fields as $air_cta_field ) {

    	if( !isset($webinar_data->{$air_cta_field}) || empty($webinar_data->{$air_cta_field}) ) continue;

    	if( $air_cta_field === 'air_html' ) {
		    $object->{$air_cta_field} = sanitize_post($webinar_data->{$air_cta_field});
	    } else {
		    $object->{$air_cta_field} = sanitize_text_field( $webinar_data->{$air_cta_field} );
	    }
    }

	//Keep webinar settings language intact after saving AR fields
    $object->settings_language = $old_webinar_data->settings_language;

    // Update Option Field:
    update_option('webinarignition_campaign_' . $id, $object);

    // Resave & Redo URL
    $webinarName        = $object->webinarURLName2;

    // Get Current Name From DB
    global $wpdb;
    $table_db_name  = $wpdb->prefix . "webinarignition";
    $webinars       = $wpdb->get_results("SELECT * FROM $table_db_name WHERE id = '$id'", OBJECT);

    if( count($webinars) ):

        $webinar = $webinars[0];

        if ($webinar->appname != $webinarName):

            $wpdb->update($table_db_name, array(
                    'appname' => $webinarName
                ), array('id' => $id)
            );
            // ReName permalink URL
            $my_post                = [];
            $my_post['ID']          = $webinar->postID;
            $my_post['post_name']   = $webinarName;
            wp_update_post($my_post);

        endif;

    endif;

    do_action('webinar_saved', $webinar_data, $old_webinar_data);
}

add_action('webinar_saved', 'webinarignition_save_support_staff', 10, 2 );

function webinarignition_save_support_staff ( $webinar_data, $old_webinar_data ) {

    if( isset($webinar_data->enable_support) && ($webinar_data->enable_support == 'yes') && isset($webinar_data->support_staff_count) && ( !empty( $webinar_data->support_staff_count ) ) ){

        for( $x=1; $x<= $webinar_data->support_staff_count; $x++){

            $member_email_str       = "member_email_".$x;
            $member_first_name_str  = "member_first_name_".$x;
            $member_last_name_str   = "member_last_name_".$x;

            if( property_exists($webinar_data, $member_email_str) &&  property_exists($webinar_data, $member_first_name_str) && property_exists($webinar_data, $member_last_name_str)) {

                    $member_email           = $webinar_data->{"member_email_".$x};
                    $member                 = get_user_by( 'email',  $member_email);
                    $member_first_name      = $webinar_data->{"member_first_name_".$x};
                    $member_last_name       = $webinar_data->{"member_last_name_".$x};

                    if( empty( $member ) ) {

                            $password       = wp_generate_password( absint( 15 ), true, false );
                            $display_name   = $member_first_name . ' ' . $member_last_name;

                            $user_id        = wp_insert_user( [
                            'user_login'    => $member_email,
                            'user_email'    => sanitize_email( $member_email ),
                            'user_pass'     => $password,
                            'display_name'  => $display_name,
                            'first_name'    => $member_first_name,
                            'last_name'     => $member_last_name,
                            'role'          => 'subscriber'
                            ] );

                            $str = $user_id . time() . uniqid( '', true );

                            $_wi_support_token = md5( $str );

                            update_user_meta( $user_id, '_wi_support_token', $_wi_support_token );

                    }

            }

        }

    }
}

add_action( 'init', 'webinarignition_add_additional_host_role' );

function webinarignition_add_additional_host_role() {

    add_role(
            'webinarignition_host',
            'WebinarIgnition Host',
            [
              'manage_options' => true,
              'edit_posts' => true,
              'edit_others_posts' => true,
              'edit_published_posts' => true,
              'publish_posts' => true,
              'edit_pages' => true,
              'edit_others_pages' => true,
              'edit_published_pages' => true,
              'publish_pages' => true,
              'edit_others_pages' => true
    ]);

    add_role( 'webinarignition_support', 'WebinarIgnition Support', get_role( 'subscriber' )->capabilities );

}

add_filter('login_redirect', 'webinarignition_redirect_webinarignition_host', 10, 3 );

function webinarignition_redirect_webinarignition_host( $redirect_to, $request, $user ) {

    if ( isset( $user->roles ) && is_array( $user->roles ) && in_array( 'webinarignition_host', $user->roles ) ) {
            $redirect_to =    get_admin_url() . '?page=webinarignition-dashboard';
    }

    return $redirect_to;

}

add_action('webinar_saved', 'webinarignition_save_additional_hosts', 20, 2 );

function webinarignition_save_additional_hosts ( $webinar_data, $old_webinar_data ) {

    if( isset($webinar_data->enable_multiple_hosts) && ($webinar_data->enable_multiple_hosts == 'yes') && isset($webinar_data->host_member_count) && ( !empty( $webinar_data->host_member_count ) ) ){

        for( $x=1; $x<= $webinar_data->host_member_count; $x++ ){

            $host_member_email_str       = "host_member_email_".$x;
            $host_member_first_name_str  = "host_member_first_name_".$x;
            $host_member_last_name_str   = "host_member_last_name_".$x;

            if( property_exists($webinar_data, $host_member_email_str) &&  property_exists($webinar_data, $host_member_first_name_str) && property_exists($webinar_data, $host_member_last_name_str)) {

                    $host_member_email           = $webinar_data->{"host_member_email_".$x};

                    if( filter_var($host_member_email, FILTER_VALIDATE_EMAIL) ){

                        $member                      = get_user_by( 'email',  $host_member_email);
                        $host_member_first_name      = $webinar_data->{"host_member_first_name_".$x};
                        $host_member_last_name       = $webinar_data->{"host_member_last_name_".$x};

                        if( empty( $member ) ) {

                                $password           = wp_generate_password( absint( 15 ), true, false );
                                $display_name       = $host_member_first_name . ' ' . $host_member_last_name;

                                $user_id        = wp_insert_user( [
                                    'user_login'    => $host_member_email,
                                    'user_email'    => sanitize_email( $host_member_email ),
                                    'user_pass'     => $password,
                                    'display_name'  => $display_name,
                                    'first_name'    => $host_member_first_name,
                                    'last_name'     => $host_member_last_name,
                                    'role'          => 'webinarignition_host'
                                ] );

                                if( isset($webinar_data->send_user_notification) &&  !empty($webinar_data->send_user_notification)){
                                    wp_new_user_notification( $user_id, null, 'both' );
                                }

                        }

                    }

            }

        }

    }
}

add_action('webinar_saved', 'webinarignition_send_after_live_webinar_questions', 10, 2 );

function webinarignition_send_after_live_webinar_questions ( $webinar_data, $webinar_old_data ) {

    if( $webinar_data->webinar_date == 'AUTO' ){
            return;
    }

    if( !empty($webinar_data->console_q_notifications) && ($webinar_data->console_q_notifications == 'yes') && !empty($webinar_data->enable_after_webinar_question_notification) && ($webinar_data->enable_after_webinar_question_notification == 'yes') && ( $webinar_data->webinar_switch == 'closed') && ( $webinar_data->webinar_switch != $webinar_old_data->webinar_switch )  ){

        if (filter_var($webinar_data->host_questions_notifications_email, FILTER_VALIDATE_EMAIL)) {

            global $wpdb;
            $table_db_name  = $wpdb->prefix . "webinarignition_questions";

            $results        = $wpdb->get_results("SELECT * FROM $table_db_name WHERE app_id = '$webinar_data->id' ", OBJECT);

            if(empty($results)) {

              $table_db_name  = $wpdb->prefix . "webinarignition_questions_new"; //for older installations that stored questions in this table
              $results        = $wpdb->get_results("SELECT * FROM $table_db_name WHERE app_id = '$webinar_data->id' ", OBJECT);

            }

            $upload_dir = wp_upload_dir();
            $wi_dirname = $upload_dir['basedir'] . '/webinarignition';
            if(!file_exists($wi_dirname)) {wp_mkdir_p($wi_dirname);}

            $filename   = $wi_dirname . '/webinar_'.$webinar_data->id.'_questions.csv';
            $f          = fopen( $filename, 'w');

            foreach ($results as $results) {

               $question    = [];
               $question[]  = $results->name;
               $question[]  = $results->email;
               $question[]  = str_replace(',', ' -', $results->created);
               $question[]  = $results->status;
               $question[]  = $results->question;

               fputcsv($f, $question);

           }

            fclose($f);

            $email_data                     = new stdClass();
            $csv_link                       = $upload_dir["baseurl"] . '/webinarignition/webinar_'.$webinar_data->id.'_questions.csv';

            $host_email                     = ! empty($webinar_data->host_questions_notifications_email) ? $webinar_data->host_questions_notifications_email : get_option( 'webinarignition_smtp_email' );
            $email_data->email_subject      = __( 'Questions From Your Webinar', 'webinarignition' );

            $headers                        = array('Content-Type: text/html; charset=UTF-8');
            $email_data->bodyContent        = '<p>'.__( 'Here is a link to the questions your webinar attendees asked in your recent webinar:', 'webinarignition' );
            $email_data->bodyContent       .= '<a href="'.$csv_link.'">'.__( 'Webinar Questions', 'webinarignition' ).'</a></p>';
            $email_data->bodyContent       .= '<p>'.__( 'The file is also attached to this email for your convenience.', 'webinarignition' ).'</p>';

            $attachments                    = array( $wi_dirname . '/webinar_'.$webinar_data->id.'_questions.csv' );

            $email_data->footerContent      = ( !empty( $webinar_data->show_or_hide_local_qstn_answer_email_footer )  &&  ( $webinar_data->show_or_hide_local_qstn_answer_email_footer == 'show' )  ) ?  $webinar_data->qstn_answer_email_footer : '';
            $email_data->emailheading       = __( 'Questions From Your Webinar', 'webinarignition' );
            $email_data->emailpreview       = __( 'Questions From Your Webinar', 'webinarignition' );

            $email                          = new WI_Emails();
            $emailBody                      = $email->build_email( $email_data );

            wp_mail( $host_email, $email_data->email_subject, $emailBody, $headers, $attachments );

        }

    }

}

add_action( 'wp_ajax_nopriv_webinarignition_after_auto_webinar', 'webinarignition_after_auto_webinar_callback' );
add_action( 'wp_ajax_webinarignition_after_auto_webinar', 'webinarignition_after_auto_webinar_callback' );
function webinarignition_after_auto_webinar_callback() {

    check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
    $post_input = filter_input_array( INPUT_POST );

    $webinar_id                         = sanitize_text_field( $post_input['webinar_id'] );
    $email                              = sanitize_email( $post_input['email'] );
    $attendee_name                      = sanitize_text_field( $post_input['name'] );
    $lead_id                            = sanitize_text_field( $post_input['lead'] );
    $webinar_data                       = WebinarignitionManager::get_webinar_data( $webinar_id );
    $send_question_notification         = false;
    $host_notification_email            = filter_var($webinar_data->host_questions_notifications_email, FILTER_VALIDATE_EMAIL);

    if( isset( $webinar_data->console_q_notifications ) && ($webinar_data->console_q_notifications == 'yes') && isset( $webinar_data->enable_after_webinar_question_notification )  && ( $webinar_data->enable_after_webinar_question_notification == 'yes')  ){

            global $wpdb;
            $table_db_name  = $wpdb->prefix . "webinarignition_questions";
            $results        = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM '$table_db_name' WHERE 'app_id' = %d AND 'email' = %s", $webinar_id, $email ), OBJECT);

            if( !empty( $results ) ){

                    $upload_dir = wp_upload_dir();
                    $wi_dirname = $upload_dir['basedir'] . '/webinarignition';
                    if(!file_exists($wi_dirname)) { wp_mkdir_p($wi_dirname);}

                    $filename   = $wi_dirname . '/webinar_'.$webinar_data->id.'_questions_'.$lead_id.'.csv';
                    $f          = fopen( $filename, 'w');

                    foreach ($results as $results) {

                       $question    = [];
                       $question[]  = $results->name;
                       $question[]  = $results->email;
                       $question[]  = str_replace(',', ' -', $results->created);
                       $question[]  = $results->status;
                       $question[]  = $results->question;

                       fputcsv($f, $question);

                   }

                    fclose($f);

                    $email_data                 = new stdClass();

                    $csv_link                   = $upload_dir["baseurl"] . '/webinarignition/webinar_'.$webinar_data->id.'_questions_'.$lead_id.'.csv';

                    $email_data->bodyContent    = '';

                    $email_data->email_subject  = __( 'Questions From Your Webinar', 'webinarignition' );
                    $headers                    = array('Content-Type: text/html; charset=UTF-8');
                    $email_data->bodyContent    = "<p>".$attendee_name. __( ' has just finished watching your webinar ', 'webinarignition' ) .$webinar_data->webinar_desc.".</p><p>". __( 'Here is a link to the questions they asked in your recent webinar: ', 'webinarignition' );
                    $email_data->bodyContent    .= '<a href="'.$csv_link.'">'.__( 'Webinar Questions', 'webinarignition' ).'</a></p>';
                    $email_data->bodyContent    .= '<p>'.__( 'The file is also attached to this email for your convenience.', 'webinarignition' ).'</p>';

                    $email_data->footerContent  = ( !empty( $webinar_data->show_or_hide_local_qstn_answer_email_footer )  &&  ( $webinar_data->show_or_hide_local_qstn_answer_email_footer == 'show' )  ) ?  $webinar_data->qstn_answer_email_footer : '';
                    $email_data->emailheading   = __( 'Questions From Your Webinar', 'webinarignition' );
                    $email_data->emailpreview   = __( 'Questions From Your Webinar', 'webinarignition' );

                    $attachments    = array( $wi_dirname . '/webinar_'.$webinar_data->id.'_questions_'.$lead_id.'.csv' );

                    $email          = new WI_Emails();
                    $emailBody      = $email->build_email( $email_data );

                    wp_mail( $webinar_data->host_questions_notifications_email, $email_data->email_subject, $emailBody, $headers, $attachments );

            }

    }

}


// Delete Campaign
add_action( 'wp_ajax_webinarignition_delete_campaign', 'webinarignition_delete_campaign_callback' );
function webinarignition_delete_campaign_callback() {
    check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
    $post_input = filter_input_array( INPUT_POST );
    global $wpdb;
    $ID            = ( int ) sanitize_text_field( $post_input['id'] );
    $getVersion    = "webinarignition";
    $table_db_name = $wpdb->prefix . $getVersion;

    // Also Delete Corrasponding Page Post
    $results = $wpdb->get_row( "SELECT * FROM $table_db_name WHERE id = '$ID'", OBJECT );
    wp_delete_post( $results->postID, true );
    $wpdb->query( "DELETE FROM $table_db_name WHERE id = $ID" );

    delete_option('webinarignition_campaign_' . $ID);
    delete_option('wi_webinar_post_id_' . $ID);

    $hash_to_id = get_option('webinarignition_map_campaign_hash_to_id', []);
    $id_to_hash = get_option('webinarignition_map_campaign_id_to_hash', []);

    if (!empty($id_to_hash[$ID])) {
        $hash = $id_to_hash[$ID];
        unset($id_to_hash[$ID]);

        if (!empty($hash_to_id[$hash])) {
            unset($hash_to_id[$hash]);
        }
    }

    update_option('webinarignition_map_campaign_hash_to_id', $hash_to_id);
    update_option('webinarignition_map_campaign_id_to_hash', $id_to_hash);

    //Remove old webinar warning, if there are no old webinars exists in DB
    $date_before = '2022-03-25';
    $has_old_webinars = WebinarIgnition::instance()->has_webinars_before_date($date_before);
    if( !$has_old_webinars ) {
        set_transient('wi_has_old_webinars', 0);
    }
}
