<?php


class WebinarignitionManager {
    public static function is_webinarignition() {
        $input_get = filter_input_array( INPUT_GET );

        global $post;

        if (empty($post)) return false;

        if (!empty($input_get["webinar"])) { // Maybe shortcode page
            $webinar_id = urlencode( $input_get["webinar"] );
            $is_webinar_id_protected = WebinarignitionManager::is_webinar_id_protected($webinar_id);

            if (!empty($is_webinar_id_protected)) $webinar_id = $is_webinar_id_protected;
            $webinar_data = WebinarignitionManager::get_webinar_data($webinar_id);

            if (
                !empty($webinar_data)
                && WebinarignitionPowerupsShortcodes::is_enabled($webinar_data)
            ) {
                return $webinar_data;
            }
        } else { // Maybe wi page
	        $webinar_id = absint( get_post_meta( $post->ID, 'webinarignitionx_meta_box_select', true ) ); //Check if webinar page

            if (empty($webinar_id)) return false;

            $protected_webinar_id = '';
            $is_webinar_id_protected = WebinarignitionManager::is_webinar_id_protected($webinar_id);

            if (!empty($protected_webinar_id)) $webinar_id = $is_webinar_id_protected;

            // Return Option Object:
            $webinar_data = WebinarignitionManager::get_webinar_data($webinar_id);

            if ( !empty($webinar_data) ) return $webinar_data;
        }

        return false;
    }

    public static function get_webinar_data($id) {

        if( defined('WI_WEBINAR_DATA_POST') && WI_WEBINAR_DATA_POST ) {
            return self::get_webinar_post_data($id);
        }

        $webinar_data = get_option( 'webinarignition_campaign_' . $id );
        // $hash_to_id = get_option('webinarignition_map_campaign_hash_to_id', []);
        $id_to_hash = get_option('webinarignition_map_campaign_id_to_hash', []);

        if (!empty($id_to_hash[$id])) {
            $webinar_data->hash_id = $id_to_hash[$id];
        }

        return $webinar_data;
    }


    public static function get_webinar_post_data($campaign_id) {

        $webinar_data = $index_keys = array();

        $webinar = self::get_webinar_record_by_id( $campaign_id, 'object' );

        $webinar_id = get_option('wi_webinar_post_id_' . $campaign_id );

        if( 'new' == $webinar->camtype ) {
           $index_keys = self::get_live_webinar_index_keys();
        } else {
            $index_keys = self::get_auto_webinar_index_keys();
        }

        foreach( $index_keys as $index_key ) {
            $meta_key = 'wi_' . $index_key;
            $webinar_data[$index_key] = get_post_meta( $webinar_id, $meta_key, true );
        }

        $id_to_hash = get_option('webinarignition_map_campaign_id_to_hash', []);

        if (!empty($id_to_hash[$campaign_id])) {
            $webinar_data['hash_id'] = $id_to_hash[$campaign_id];
        }

        return webinarignition_array_to_object( $webinar_data );
    }

    public static function get_webinar_post_id($webinar_id) {
        global $wpdb;
        $table = "{$wpdb->prefix}webinarignition";
        $query = $wpdb->prepare("SELECT postID FROM {$table} W WHERE W.ID=%d", [$webinar_id]);
        $webinar_post_id = $wpdb->get_var($query);
        return absint($webinar_post_id);
    }

    public static function get_webinar_record_by_id($webinar_id, $type = 'a_array') {
        global $wpdb;
        $table = "{$wpdb->prefix}webinarignition";
        $query = $wpdb->prepare("SELECT * FROM {$table} W WHERE W.ID=%d", [$webinar_id]);

        if( 'object' == $type ) {
            $webinar = $wpdb->get_row($query);
        } else {
            $webinar = $wpdb->get_row($query, ARRAY_A);
        }

        return $webinar;
    }

	/**
     * Get currently set locale
     *
	 * @return string
	 */
    public static function get_current_locale() {
	    $current_locale = determine_locale();

	    if(is_user_logged_in()) {
		    $current_locale = get_user_locale();
	    }

	    return $current_locale;
    }

	/**
	 * Set locale based on webinar
     * @param $webinar_data
	 */
    public static function set_locale($webinar_data) {
	    $text_domain = 'webinarignition';
	    $mopath = WEBINARIGNITION_PATH . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR;

	    $current_locale = self::get_current_locale();

	    if ( isset($webinar_data->webinar_lang) && !empty( $webinar_data->webinar_lang ) && $current_locale !== $webinar_data->webinar_lang ) {
	        $switched = switch_to_locale($webinar_data->webinar_lang);
	        if($switched) {
		        $mo_file = $mopath . $text_domain . '-' . $webinar_data->webinar_lang . '.mo';
				load_textdomain( $text_domain, $mo_file );
	        }
	    }
    }

	/**
	 * Restore webinar locale if changed previously
	 * @param $webinar_data
	 */
	public static function restore_locale($webinar_data) {
	    $current_locale = self::get_current_locale();

	    if ( isset($webinar_data->webinar_lang) && !empty( $webinar_data->webinar_lang ) && $current_locale === $webinar_data->webinar_lang ) {
	        restore_previous_locale();
	    }
    }

	/**
     * Pass webinar hash_ID and get numeric ID
     *
	 * @param $id
	 *
	 * @return false|mixed
	 */
    public static function is_webinar_id_protected($id) {
        $hash_to_id = get_option('webinarignition_map_campaign_hash_to_id', []);
        // $id_to_hash = get_option('webinarignition_map_campaign_id_to_hash', []);

        if (!empty($hash_to_id[$id])) {
            return $hash_to_id[$id];
        }

        return false;
    }

    public static function is_webinar_public($webinar_data) {
        $webinar_access = !empty($webinar_data->protected_webinar_id) ? $webinar_data->protected_webinar_id : 'public';

        return $webinar_access === 'public';
    }

    public static function maybe_redirect_to_custom_page($webinar_data, $lid, $page) {
        $custom_id = false;

        if ($page === 'countdown') {
            if ( !empty($webinar_data->custom_countdown_page) && !empty(get_post($webinar_data->custom_countdown_page))) {
                $custom_id = $webinar_data->custom_countdown_page;
            }
        } elseif ($page === 'webinar') {
            if ( !empty($webinar_data->custom_webinar_page) && !empty(get_post($webinar_data->custom_webinar_page))) {
                $custom_id = $webinar_data->custom_webinar_page;
            }
        } elseif ($page === 'closed') {
            if ( !empty($webinar_data->custom_closed_page) && !empty(get_post($webinar_data->custom_closed_page))) {
                $custom_id = $webinar_data->custom_closed_page;
            }
        } elseif ($page === 'replay') {
            if ( !empty($webinar_data->custom_replay_page) && !empty(get_post($webinar_data->custom_replay_page))) {
                $custom_id = $webinar_data->custom_replay_page;
            }
        } elseif ($page === 'thankyou') {
            if ( !empty($webinar_data->custom_thankyou_page) && !empty(get_post($webinar_data->custom_thankyou_page))) {
                $custom_id = $webinar_data->custom_thankyou_page;
            }
        }

        if (!empty($custom_id)) {
            $webinar_url = get_permalink($custom_id);
            if($webinar_url) {

                $webinar_url = add_query_arg(['lid' => $lid], $webinar_url);

	            wp_redirect( $webinar_url );
            }
        }
    }

    public static function get_live_webinar_index_keys(){
        $dataArray = array(
            "id",
            "webinar_lang",
            'settings_language',
            "webinar_desc",
            "webinar_host",
            "webinar_date",
            "webinar_start_time",
            "webinar_end_time",
            "time_format",
            "webinar_timezone",
            "lp_metashare_title",
            "lp_metashare_desc",
            "lp_main_headline",
            "lp_webinar_subheadline",
            "cd_headline",
            "email_signup_sbj",
            "email_signup_body",
            "email_notiff_date_1",
            "email_notiff_time_1",
            "email_notiff_status_1",
            "email_notiff_sbj_1",
            "email_notiff_body_1",
            "email_notiff_date_2",
            "email_notiff_time_2",
            "email_notiff_status_2",
            "email_notiff_sbj_2",
            "email_signup_heading",
            "email_signup_preview",
            "email_notiff_1_heading",
            "email_notiff_1_preview",
            "email_notiff_2_heading",
            "email_notiff_2_preview",
            "email_notiff_3_heading",
            "email_notiff_3_preview",
            "email_notiff_4_heading",
            "email_notiff_4_preview",
            "email_notiff_5_heading",
            "email_notiff_5_preview",
            "email_notiff_body_2",
            "email_notiff_date_3",
            "email_notiff_time_3",
            "email_notiff_status_3",
            "email_notiff_sbj_3",
            "email_notiff_body_3",
            "email_notiff_date_4",
            "email_notiff_time_4",
            "email_notiff_status_4",
            "email_notiff_sbj_4",
            "email_notiff_body_4",
            "email_notiff_date_5",
            "email_notiff_time_5",
            "email_notiff_status_5",
            "email_notiff_sbj_5",
            "email_notiff_body_5",
            "email_twilio_date",
            "email_twilio_time",
            "email_twilio_status",
            "email_twilio",
            "twilio_msg",
            "lp_banner_bg_style",
            "webinar_banner_bg_style",
            'ar_fields_order',
            'ar_required_fields',
            'ar_name'                        ,
            'ar_email'                       ,
            'lp_optin_name'                  ,
            'lp_optin_email'                 ,
            'ar_hidden'                      ,
            'fb_id'                          ,
            'fb_secret'                      ,
            'ty_share_image'                 ,
            'ar_url'                         ,
            'ar_method'                      ,
            'lp_background_color'            ,
            'lp_background_image'            ,
            'lp_cta_bg_color'                ,
            'lp_cta_type'                    ,
            'lp_cta_video_url'               ,
            'lp_cta_video_code'              ,
            'lp_sales_headline'              ,
            'lp_sales_headline_color'        ,
            'lp_sales_copy'                  ,
            'lp_optin_headline'              ,
            'lp_webinar_host_block'          ,
            'lp_host_image'                  ,
            'lp_host_info'                   ,
            'paid_status'                    ,
            'ar_code'                        ,
            'lp_fb_button'                   ,
            'ar_custom_date_format'          ,
            'lp_optin_button'                ,
            'lp_optin_btn_color'             ,
            'lp_optin_spam'                  ,
            'lp_optin_closed'                ,
            'custom_ty_url_state'            ,
            'ty_ticket_headline'             ,
            'ty_ticket_subheadline'          ,
            'ty_cta_bg_color'                ,
            'ty_cta_type'                    ,
            'ty_cta_html'                    ,
            'ty_webinar_headline'            ,
            'ty_webinar_subheadline'         ,
            'ty_webinar_url'                 ,
            'ty_share_toggle'                ,
            'ty_step2_headline'              ,
            'ty_fb_share'                    ,
            'ty_tw_share'                    ,
            'ty_share_intro'                 ,
            'ty_share_reveal'                ,
            'webinar_switch'                 ,
            'total_cd'                       ,
            'cd_button_show'                 ,
            'cd_button_copy'                 ,
            'cd_button_color'                ,
            'cd_button'                      ,
            'cd_button_url'                  ,
            'cd_headline2'                   ,
            'cd_months'                      ,
            'cd_weeks'                       ,
            'cd_days'                        ,
            'cd_hours'                       ,
            'cd_minutes'                     ,
            'cd_seconds'                     ,
            'webinar_info_block'             ,
            'webinar_info_block_title'       ,
            'webinar_info_block_host'        ,
            'webinar_info_block_eventtitle'  ,
            'webinar_info_block_desc'        ,
            'privacy_status'                 ,
            'webinar_live_video'             ,
            'webinar_live_bgcolor'           ,
            'webinar_banner_bg_color'        ,
            'webinar_banner_bg_repeater'     ,
            'webinar_banner_image'           ,
            'webinar_background_color'       ,
            'webinar_background_image'       ,
            'webinar_qa_title'               ,
            'webinar_qa'                     ,
            'webinar_qa_name_placeholder'    ,
            'webinar_qa_email_placeholder'   ,
            'webinar_qa_desc_placeholder'    ,
            'webinar_qa_button'              ,
            'webinar_qa_button_color'        ,
            'webinar_qa_thankyou'            ,
            'webinar_qa_custom'              ,
            'webinar_speaker'                ,
            'webinar_speaker_color'          ,
            'social_share_links'             ,
            'webinar_invite'                 ,
            'webinar_invite_color'           ,
            'webinar_fb_share'               ,
            'webinar_tw_share'               ,
            'webinar_ld_share'               ,
            'webinar_callin'                 ,
            'webinar_callin_copy'            ,
            'webinar_callin_color'           ,
            'webinar_callin_number'          ,
            'webinar_callin_color2'          ,
            'webinar_live'                   ,
            'webinar_live_color'             ,
            'webinar_giveaway_toggle'        ,
            'webinar_giveaway_title'         ,
            'webinar_giveaway'               ,
            'lp_banner_bg_color'             ,
            'lp_banner_bg_repeater'          ,
            'lp_banner_image'                ,
            'lp_cta_image'                   ,
            'paid_headline'                  ,
            'paid_button_type'               ,
            'paid_button_custom'             ,
            'payment_form'                   ,
            'paypal_paid_btn_copy'           ,
            'paid_btn_color'                 ,
            'stripe_secret_key'              ,
            'stripe_publishable_key'         ,
            'stripe_charge'                  ,
            'stripe_charge_description'      ,
            'stripe_paid_btn_copy'           ,
            'paid_pay_url'                   ,
            'lp_fb_copy'                     ,
            'lp_fb_or'                       ,
            'lp_optin_btn_image'             ,
            'lp_optin_btn'                   ,
            'custom_ty_url'                  ,
            'ty_cta_video_url'               ,
            'ty_cta_video_code'              ,
            'ty_cta_image'                   ,
            'ty_werbinar_custom_url'         ,
            'ty_ticket_webinar_option'       ,
            'ty_ticket_webinar'              ,
            'ty_webinar_option_custom_title' ,
            'ty_ticket_host_option'          ,
            'ty_ticket_host'                 ,
            'ty_webinar_option_custom_host'  ,
            'ty_ticket_date_option'          ,
            'ty_ticket_date'                 ,
            'ty_webinar_option_custom_date'  ,
            'ty_ticket_time_option'          ,
            'ty_ticket_time'                 ,
            'ty_webinar_option_custom_time'  ,
            'tycd_countdown'                 ,
            'tycd_progress'                  ,
            'tycd_years'                     ,
            'tycd_months'                    ,
            'tycd_weeks'                     ,
            'tycd_days'                      ,
            'ty_add_to_calendar_option'      ,
            'ty_calendar_headline'           ,
            'ty_calendar_google'             ,
            'ty_calendar_ical'               ,
            'skip_ty_page'                   ,
            'txt_area'                       ,
            'txt_headline'                   ,
            'txt_placeholder'                ,
            'txt_btn'                        ,
            'txt_reveal'                     ,
            'replay_video'                   ,
            'replay_optional'                ,
            'replay_cd_date'                 ,
            'replay_cd_time'                 ,
            'replay_cd_headline'             ,
            'replay_timed_style'             ,
            'replay_order_copy'              ,
            'replay_order_url'               ,
            'replay_order_html'              ,
            'replay_order_time'              ,
            'replay_closed'                  ,
            'footer_copy'                    ,
            'footer_branding'                => 'hide',
            'custom_lp_js'                   ,
            'custom_lp_css'                  ,
            'meta_site_title_ty'             ,
            'meta_desc_ty'                   ,
            'custom_ty_js'                   ,
            'custom_ty_css'                  ,
            'meta_site_title_webinar'        ,
            'meta_desc_webinar'              ,
            'custom_webinar_js'              ,
            'custom_webinar_css'             ,
            'meta_site_title_replay'         ,
            'meta_desc_replay'               ,
            'custom_replay_js'               ,
            'custom_replay_css'              ,
            'footer_code'                    ,
            'footer_code_ty'                 ,
            'live_stats'                     ,
            'wp_head_footer'                 ,
            'email_signup'                   ,
            'email_notiff_1'                 ,
            'email_notiff_2'                 ,
            'email_notiff_3'                 ,
            'email_notiff_4'                 ,
            'email_notiff_5'                 ,
            'twilio_id'                      ,
            'twilio_token'                   ,
            'twilio_number'                  ,
            'webinar_live_overlay'           ,
            'replay_order_color'             ,
            'air_toggle'                     ,
            'protected_webinar_id'           ,
            'protected_lead_id'              ,
            'protected_webinar_redirection'  ,
            'limit_lead_visit'               ,
            'limit_lead_timer'               ,
            'webinar_status'                 ,
            'cta_position'                   ,
            'console_q_notifications'        ,
            'qstn_notification_email_sbj'    ,
            'enable_first_question_notification'            ,
            'enable_after_webinar_question_notification'    ,
            'first_question_notification_sent'              ,
            'after_webinar_question_notification_sent'      ,
            'qstn_notification_email_body'    ,
            'templates_version'               ,
            'date_format'                     ,
            'time_format'                     ,
            'settings_language'               ,
            'display_tz'

        );

        return $dataArray;
    }

    public static function get_auto_webinar_index_keys(){
        $dataArray = array(
            "id"                               ,
            "webinar_lang"                     ,
            'settings_language'                ,
            "webinar_desc"                     ,
            "webinar_host"                     ,
            "webinar_date"                     ,
            "lp_metashare_title"               ,
            "lp_metashare_desc"                ,
            "lp_main_headline"                 ,
            "cd_headline"                      ,
            "email_signup_sbj"                 ,
            "email_signup_body"                ,
            "email_notiff_sbj_1"               ,
            "email_notiff_body_1"             ,
            "email_notiff_sbj_2"               ,
            "email_signup_heading"             ,
            "email_signup_preview"             ,
            "email_notiff_1_heading"           ,
            "email_notiff_1_preview"           ,
            "email_notiff_2_heading"           ,
            "email_notiff_2_preview"           ,
            "email_notiff_3_heading"           ,
            "email_notiff_3_preview"           ,
            "email_notiff_4_heading"           ,
            "email_notiff_4_preview"           ,
            "email_notiff_5_heading"           ,
            "email_notiff_5_preview"           ,
            "email_notiff_body_2"              ,
            "email_notiff_sbj_3"               ,
            "email_notiff_body_3"              ,
            "email_notiff_sbj_4"               ,
            "email_notiff_body_4"              ,
            "email_notiff_sbj_5"               ,
            "email_notiff_body_5"              ,
            "twilio_msg"                       ,
            "email_twilio"                     ,
            "lp_banner_bg_style"               ,
            "webinar_banner_bg_style"          ,
            "auto_saturday"                    ,
            "auto_sunday"                      ,
            "auto_thursday"                    ,
            "auto_monday"                      ,
            "auto_friday"                      ,
            "auto_tuesday"                     ,
            "auto_wednesday"                   ,
            "auto_time_1"                      ,
            "auto_time_2"                      ,
            "auto_time_3"                      ,
            "auto_video_length"                ,
            "auto_translate_local"             ,
            'ar_fields_order'                  ,
            'ar_required_fields'               ,
            'ar_name'                          ,
            'ar_email'                         ,
            'lp_optin_name'                    ,
            'lp_optin_email'                   ,
            'lp_schedule_type'                 ,
            'auto_today'                       ,
            'auto_day_offset'                  ,
            'auto_day_limit'                   ,
            'auto_blacklisted_dates'           ,
            'auto_timezone_type'               ,
            'lp_background_color'              ,
            'lp_background_image'              ,
            'ty_share_image'                   ,
            'lp_cta_bg_color'                  ,
            'lp_cta_type'                      ,
            'lp_cta_video_url'                 ,
            'lp_cta_video_code'                ,
            'lp_sales_headline'                ,
            'lp_sales_headline_color'          ,
            'lp_sales_copy'                    ,
            'lp_optin_headline'                ,
            'lp_webinar_host_block'            ,
            'lp_host_image'                    ,
            'lp_host_info'                     ,
            'paid_status'                      ,
            'ar_code'                          ,
            'ar_custom_date_format'            ,
            'lp_optin_button'                  ,
            'lp_optin_btn_color'               ,
            'lp_optin_spam'                    ,
            'lp_optin_closed'                  ,
            'custom_ty_url_state'              ,
            'ty_ticket_headline'               ,
            'ty_ticket_subheadline'            ,
            'ty_cta_bg_color'                ,
            'ty_cta_type'                      ,
            'ty_cta_html'                      ,
            'ty_webinar_headline'              ,
            'ty_webinar_subheadline'           ,
            'ty_webinar_url'                   ,
            'ty_share_toggle'                  ,
            'ty_step2_headline'                ,
            'ty_fb_share'                      ,
            'ty_tw_share'                      ,
            'ty_share_intro'                   ,
            'ty_share_reveal'                  ,
            'webinar_switch'                   ,
            'total_cd'                         ,
            'cd_button_show'                   ,
            'cd_button_copy'                   ,
            'cd_button_color'                  ,
            'cd_button'                        ,
            'cd_button_url'                    ,
            'cd_headline2'                     ,
            'cd_months'                        ,
            'cd_weeks'                         ,
            'cd_days'                          ,
            'cd_hours'                         ,
            'cd_minutes'                       ,
            'cd_seconds'                       ,
            'webinar_info_block'               ,
            'webinar_info_block_title'         ,
            'webinar_info_block_host'          ,
            'webinar_info_block_eventtitle'    ,
            'webinar_info_block_desc'          ,
            'privacy_status'                   ,
            'webinar_live_video'               ,
            'webinar_live_overlay'             ,
            'webinar_live_bgcolor'             ,
            'webinar_banner_bg_color'          ,
            'webinar_banner_bg_repeater'       ,
            'webinar_banner_image'             ,
            'webinar_background_color'         ,
            'webinar_background_image'         ,
            'webinar_qa_title'                 ,
            'webinar_qa'                       ,
            'webinar_qa_name_placeholder'      ,
            'webinar_qa_email_placeholder'     ,
            'webinar_qa_desc_placeholder'      ,
            'webinar_qa_button'                ,
            'webinar_qa_button_color'          ,
            'webinar_qa_thankyou'              ,
            'webinar_qa_custom'                ,
            'webinar_speaker'                  ,
            'webinar_speaker_color'            ,
            'social_share_links'               ,
            'webinar_invite'                   ,
            'webinar_invite_color'             ,
            'webinar_fb_share'                 ,
            'webinar_tw_share'                 ,
            'webinar_ld_share'                 ,
            'webinar_callin'                   ,
            'webinar_callin_copy'              ,
            'webinar_callin_color'             ,
            'webinar_callin_number'            ,
            'webinar_callin_color2'            ,
            'webinar_live'                     ,
            'webinar_live_color'               ,
            'webinar_giveaway_toggle'          ,
            'webinar_giveaway_title'           ,
            'webinar_giveaway'                 ,
            'lp_banner_bg_color'               ,
            'lp_banner_bg_repeater'            ,
            'lp_banner_image'                  ,
            'lp_cta_image'                     ,
            'paid_headline'                    ,
            'paid_button_type'                 ,
            'paid_button_custom'               ,
            'payment_form'                     ,
            'paid_btn_copy'                    ,
            'paid_btn_color'                   ,
            'stripe_secret_key'                ,
            'stripe_publishable_key'           ,
            'stripe_charge'                    ,
            'stripe_charge_description'        ,
            'paid_pay_url'                     ,
            'lp_fb_copy'                       ,
            'lp_fb_or'                         ,
            'lp_optin_btn_image'               ,
            'lp_optin_btn'                     ,
            'custom_ty_url'                    ,
            'ty_cta_video_url'                 ,
            'ty_cta_video_code'                ,
            'ty_cta_image'                     ,
            'ty_werbinar_custom_url'           ,
            'ty_ticket_webinar_option'         ,
            'ty_ticket_webinar'                ,
            'ty_webinar_option_custom_title'   ,
            'ty_ticket_host_option'            ,
            'ty_ticket_host'                   ,
            'ty_webinar_option_custom_host'    ,
            'ty_ticket_date_option'            ,
            'ty_ticket_date'                   ,
            'ty_webinar_option_custom_date'    ,
            'ty_ticket_time_option'            ,
            'ty_ticket_time'                   ,
            'ty_webinar_option_custom_time'    ,
            'tycd_countdown'                   ,
            'tycd_progress'                    ,
            'tycd_years'                       ,
            'tycd_months'                      ,
            'tycd_weeks'                       ,
            'tycd_days'                        ,
            'ty_add_to_calendar_option'        ,
            'ty_calendar_headline'             ,
            'ty_calendar_google'               ,
            'ty_calendar_ical'                 ,
            'skip_ty_page'                     ,
            'txt_area'                         ,
            'skip_instant_acces_confirm_page'  ,
            'txt_headline'                     ,
            'txt_placeholder'                  ,
            'txt_btn'                          ,
            'txt_reveal'                       ,
            'replay_video'                     ,
            'replay_optional'                  ,
            'replay_cd_date'                   ,
            'replay_cd_time'                   ,
            'replay_cd_headline'               ,
            'replay_timed_style'               ,
            'replay_order_copy'                ,
            'replay_order_url'                 ,
            'replay_order_html'                ,
            'replay_order_time'                ,
            'replay_closed'                    ,
            'footer_copy'                      ,
            'footer_branding'                  ,
            'custom_lp_js'                     ,
            'custom_lp_css'                    ,
            'meta_site_title_ty'               ,
            'meta_desc_ty'                     ,
            'custom_ty_js'                     ,
            'custom_ty_css'                    ,
            'meta_site_title_webinar'          ,
            'meta_desc_webinar'                ,
            'custom_webinar_js'                ,
            'custom_webinar_css'               ,
            'meta_site_title_replay'           ,
            'meta_desc_replay'                 ,
            'custom_replay_js'                 ,
            'custom_replay_css'                ,
            'footer_code'                      ,
            'footer_code_ty'                   ,
            'live_stats'                       ,
            'wp_head_footer'                   ,
            'email_signup'                     ,
            'email_notiff_1'                   ,
            'email_notiff_2'                   ,
            'email_notiff_3'                   ,
            'email_notiff_4'                   ,
            'email_notiff_5'                   ,
            'twilio_id'                        ,
            'twilio_token'                     ,
            'twilio_number'                    ,
            'webinar_source_toggle'            ,
            'auto_video_url'                   ,
            'auto_video_load'                  ,
            'webinar_show_videojs_controls'    ,
            'webinar_iframe_source'            ,
            'auto_action'                      ,
            'auto_action_time'                 ,
            'auto_action_copy'                 ,
            'auto_action_btn_copy'             ,
            'auto_action_url'                  ,
            'replay_order_color'               ,
            'auto_redirect'                    ,
            'auto_redirect_url'                ,
            'auto_redirect_delay'              ,
            'auto_timezone_custom'             ,
            'auto_time_fixed'                  ,
            'auto_timezone_fixed'              ,
            'delayed_day_offset'               ,
            'auto_time_delayed'                ,
            'delayed_timezone_type'            ,
            'auto_timezone_user_specific_name' ,
            'auto_timezone_delayed'            ,
            'delayed_blacklisted_dates'        ,
            'auto_translate_instant'           ,
            'auto_translate_headline1'         ,
            'auto_translate_subheadline1'      ,
            'auto_translate_headline2'         ,
            'auto_translate_subheadline2'      ,
            'lp_webinar_subheadline'           ,
            'fb_id'                            ,
            'fb_secret'                        ,
            'auto_video_url2'                  ,
            'auto_date_fixed'                  ,
            'auto_replay'                      ,
            'protected_webinar_id'           ,
            'protected_lead_id'              ,
            'protected_webinar_redirection'  ,
            'limit_lead_visit'               ,
            'limit_lead_timer'               ,
            'webinar_status'                 ,
            'cta_position'                   ,
            'console_q_notifications'        ,
            'qstn_notification_email_sbj'    ,
            'enable_first_question_notification'            ,
            'enable_after_webinar_question_notification'    ,
            'first_question_notification_sent'              ,
            'after_webinar_question_notification_sent'      ,
            'qstn_notification_email_body'    ,
            'templates_version'             ,
            'date_format'                     ,
            'time_format'                     ,
            'auto_weekdays_1'                 ,
            'auto_weekdays_2'                 ,
            'auto_weekdays_3'                 ,
            'display_tz'                      ,

        );
        return $dataArray;
    }

    public static function clean_webinar_hash() {
        $hash_to_id = get_option('webinarignition_map_campaign_hash_to_id', []);
        $id_to_hash = get_option('webinarignition_map_campaign_id_to_hash', []);

        global $wpdb;
        $getVersion    = "webinarignition";
        $table_db_name = $wpdb->prefix . $getVersion;
        $webinars = $wpdb->get_results("SELECT * FROM {$table_db_name}", ARRAY_A);

        $hash_to_id_new = [];
        $id_to_hash_new = [];

        foreach ($webinars as $webinar) {
            $ID_new = $webinar['ID'];

            if (!empty($id_to_hash[$ID_new])) {
                $hash_new = $id_to_hash[$ID_new];

                $hash_to_id_new[$hash_new] = $ID_new;
                $id_to_hash_new[$ID_new] = $hash_new;
            }
        }

        update_option('webinarignition_map_campaign_hash_to_id', $hash_to_id_new);
        update_option('webinarignition_map_campaign_id_to_hash', $id_to_hash_new);
    }

    public static function get_host_presenters_url($id) {

        $webinar_data = get_option( 'webinarignition_campaign_' . $id );

        if ( $webinar_data && empty($webinar_data->host_presenters_url)) {

            $host_presenters_url = sha1(serialize($webinar_data) . time() . 'host_presenters_url');
            $webinar_data->host_presenters_url = $host_presenters_url;

            update_option('webinarignition_campaign_' . $id, $webinar_data);

            return $webinar_data->host_presenters_url;
        }

        return '';

    }

    public static function get_support_stuff_url($id) {
        $webinar_data = get_option( 'webinarignition_campaign_' . $id );

        if (empty($webinar_data->support_stuff_url)) {
            $support_stuff_url = sha1(serialize($webinar_data) . time() . 'support_stuff_url');
            $webinar_data->support_stuff_url = $support_stuff_url;

            update_option('webinarignition_campaign_' . $id, $webinar_data);
        }

        return $webinar_data->support_stuff_url;
    }

    /**
     * DEPRECATED WILL REMOVE
     *
     * @param $webinar_data
     * @param $lead_id
     * @param $email_body
     * @param string $additional_params
     *
     * @return string|string[]
     */
    public static function replace_email_body_placeholders($webinar_data, $lead_id, $email_body, $additional_params = '', $args = []) {
        return WebinarignitionEmailManager::replace_email_body_placeholders( $webinar_data, $lead_id, $email_body, $additional_params, $args );
    }

    public static function get_webinar_page_template($webinar_data) {
        if (!WebinarignitionPowerups::is_modern_template_enabled($webinar_data)) return 'classic';

        return !empty($webinar_data->webinar_template) ? $webinar_data->webinar_template : 'classic';
    }

    public static function get_webinar_cta_by_position($webinar_data) {
        $webinar_type = $webinar_data->webinar_date == "AUTO" ? 'evergreen' : 'live';

        if ('evergreen' !== $webinar_type) {
            return false;
        }

        $is_time = $webinar_data->auto_action === "time";

        $return_data = [
            'is_time' => $webinar_data->auto_action === "time",
            'outer' => [],
            'overlay' => [],
        ];

        $additional_autoactions = [];

        if ($is_time && WebinarignitionPowerups::is_multiple_cta_enabled($webinar_data)) {
            if (!empty($webinar_data->additional_autoactions)) {
                $additional_autoactions = maybe_unserialize($webinar_data->additional_autoactions);
            }
        }

        $cta_position_default = 'outer';

        if (
            !empty($webinar_data->auto_action_time) && (
                !empty($webinar_data->auto_action_copy)
                || (!empty($webinar_data->auto_action_btn_copy) && !empty($webinar_data->auto_action_url))
            )
        ) {
            $webinar_main_auto_action = [
                'is_main' => true,
                'auto_action_time' => $webinar_data->auto_action_time,
                'auto_action_time_end' => '',
                'auto_action_copy' => '',
                'auto_action_btn_copy' => '',
                'auto_action_url' => '',
                'auto_action_title' => __('Click here', 'webinarignition'),
                'replay_order_color' => '#6BBA40',
            ];

            if (!empty($webinar_data->auto_action_time_end)) {
                $webinar_main_auto_action['auto_action_time_end'] = $webinar_data->auto_action_time_end;
            }

            if (!empty($webinar_data->replay_order_color)) {
                $webinar_main_auto_action['replay_order_color'] = $webinar_data->replay_order_color;
            }

            if (!empty($webinar_data->auto_action_copy)) {
                $webinar_main_auto_action['auto_action_copy'] = $webinar_data->auto_action_copy;
            }

            if (!empty($webinar_data->auto_action_max_width)) {
                $webinar_main_auto_action['auto_action_max_width'] = $webinar_data->auto_action_max_width;
            }

            if (!empty($webinar_data->auto_action_btn_copy)) {
                $webinar_main_auto_action['auto_action_title'] = $webinar_data->auto_action_btn_copy;

                if (!empty($webinar_data->auto_action_url)) {
                    $webinar_main_auto_action['auto_action_btn_copy'] = $webinar_data->auto_action_btn_copy;
                    $webinar_main_auto_action['auto_action_url'] = $webinar_data->auto_action_url;
                }
            }

            if (!empty($webinar_data->cta_position) ) {
                $cta_position_default = $webinar_data->cta_position;
            }

            if ($cta_position_default === 'outer') {
                $webinar_main_auto_action['cta_position'] = 'outer';
                $additional_autoactions[0] = $webinar_main_auto_action;
            } else {
                $webinar_main_auto_action['cta_position'] = 'overlay';
                $additional_autoactions[0] = $webinar_main_auto_action;
            }
        }

        ksort($additional_autoactions);

        foreach ($additional_autoactions as $index => $additional_autoaction) {
            $cta_position = $cta_position_default;

            if (!empty($additional_autoaction['cta_position'])) {
                $cta_position = $additional_autoaction['cta_position'];
            }

            $return_data[$cta_position][$index] = $additional_autoaction;
        }

        return $return_data;
    }

    /**
     * @param $app_id
     * @param $data
     * @param string $host_presenters_url
     * @param string $support_stuff_url
     *
     * @return false
     */
    public static function register_support($app_id, $data, $host_presenters_url = '', $support_stuff_url = '') {
        if (empty($host_presenters_url) && empty($support_stuff_url)) return false;

        $webinar_data = get_option( 'webinarignition_campaign_' . $app_id );
        if (empty($webinar_data)) return false;

        if (!empty($host_presenters_url)) {
            $enabled = 'enable_multiple_hosts';
            $count = 'host_member_count';
            $prefix = 'host_member_';
            $meta_prefix = '_wi_host_';
            $role = 'webinarignition_host';
        } else {
            $enabled = 'enable_support';
            $count = 'support_staff_count';
            $prefix = 'member_';
            $meta_prefix = '_wi_support_';
            $role = 'webinarignition_support';
        }

        if (isset($webinar_data->{$enabled}) && ($webinar_data->{$enabled} == 'yes')) {
            $exists = false;

            if (!empty($webinar_data->{$count})) {
                for ( $x = 1; $x <= $webinar_data->{$count}; $x++ ){
                    $email_str       = $prefix . "email_" . $x;

                    if( property_exists($webinar_data, $email_str) && $webinar_data->{$email_str} === $data['email'] ) {
                        $exists = true;
                        break;
                    }
                }
            }

            if (!$exists) {
                $x = $webinar_data->{$count};
                $x++;
                $webinar_data->{$count} = $x;

                foreach ($data as $field => $value) $webinar_data->{$prefix . $field . "_" . $x} = $value;

                update_option('webinarignition_campaign_' . $app_id, $webinar_data);
            }

            $member = get_user_by( 'email',  $data['email']);

            if (empty($member)) {
                $member_email           = $data['email'];
                $member_first_name      = $data['first_name'];
                $member_last_name       = $data['last_name'];

                $password       = wp_generate_password( absint( 15 ), true, false );
                $display_name   = trim( $member_first_name . ' ' . $member_last_name );

                $user_id        = wp_insert_user( [
                    'user_login'    => $member_email,
                    'user_email'    => sanitize_email( $member_email ),
                    'user_pass'     => $password,
                    'display_name'  => $display_name,
                    'first_name'    => $member_first_name,
                    'last_name'     => $member_last_name,
                    'role'          => $role
                ] );
            } else {
                $user_id = $member->ID;
            }

            $_wi_support_token = get_user_meta($user_id, $meta_prefix . 'token', true);
            $_wi_support_status = get_user_meta($user_id, $meta_prefix . 'status', true);

            if (empty($_wi_support_token)) {
                $_wi_support_token = md5( $user_id . time() . uniqid( '', true ) );
                update_user_meta( $user_id, $meta_prefix . 'token', $_wi_support_token );
            }

            if (empty($_wi_support_status)) update_user_meta( $user_id, $meta_prefix . 'status', 'pending' );

            $_app_id_support_token = get_user_meta($user_id, $meta_prefix . 'token_' . $app_id, true);
            $_app_id_support_status = get_user_meta($user_id, $meta_prefix . 'status_' . $app_id, true);

            if (empty($_app_id_support_token)) {
                $_app_id_support_token = md5( $app_id . $user_id . time() . uniqid( '', true ) );
                update_user_meta( $user_id, $meta_prefix . 'token_' . $app_id, $_app_id_support_token );
            }

            if (empty($_app_id_support_status)) update_user_meta( $user_id, $meta_prefix . 'status_' . $app_id, 'pending' );

            return $_wi_support_token;

            if (!empty($host_presenters_url)) {
                return $_app_id_support_token;
            } else {
                return $_wi_support_token;
            }
        } else {
            return false;
        }
    }

    public static function generate_support_token( $user_id, $app_id, $meta_prefix, $status = 'pending' ) {
        $_wi_support_token = get_user_meta($user_id, $meta_prefix . 'token', true);
        $_wi_support_status = get_user_meta($user_id, $meta_prefix . 'status', true);
        $_app_id_support_token = get_user_meta($user_id, $meta_prefix . 'token_' . $app_id, true);
        $_app_id_support_status = get_user_meta($user_id, $meta_prefix . 'status_' . $app_id, true);

        if (empty($_wi_support_token)) {
            $_wi_support_token = md5( $user_id . time() . uniqid( '', true ) );
            update_user_meta( $user_id, $meta_prefix . 'token', $_wi_support_token );
        }

        if (empty($_wi_support_status) || $status !== $_wi_support_status) {
            update_user_meta( $user_id, $meta_prefix . 'status', $status );
        }

        if (empty($_app_id_support_token)) {
            $_app_id_support_token = md5( $app_id . $user_id . time() . uniqid( '', true ) );
            update_user_meta( $user_id, $meta_prefix . 'token_' . $app_id, $_app_id_support_token );
        }

        if (empty($_app_id_support_status) || $status !== $_app_id_support_status) {
            update_user_meta( $user_id, $meta_prefix . 'status_' . $app_id, $status );
        }
    }

    public static function is_support_enabled($webinar_data, $type = 'support') {
        if (!WebinarignitionPowerups::is_multiple_support_enabled($webinar_data)) return false;

        if ( $type === 'support' ) {
            return !empty($webinar_data->enable_support)
                   && 'yes' === $webinar_data->enable_support
                   && !empty($webinar_data->console_q_notifications)
                   && 'yes' === $webinar_data->console_q_notifications;
        } elseif ( $type === 'host' ) {
            return !empty($webinar_data->enable_multiple_hosts)
                    && 'yes' === $webinar_data->enable_multiple_hosts;
        }

        return false;
    }

    public static function extra_user_profile_fields( $user ) {
        return;
        $id = $user->ID;

        $host_token = esc_attr( get_the_author_meta( '_wi_host_token', $user->ID ) );
        $support_token = esc_attr( get_the_author_meta( '_wi_support_token', $user->ID ) );

        if (empty($host_token) && empty($support_token)) {
            return;
        }
        ?>
        <h3><?php _e("Webinarignition Profile", "webinarignition"); ?></h3>

        <table class="form-table">

        </table>
        <?php
    }

    public static function is_auto_webinar( $webinar_data ) {
		return ( isset($webinar_data->webinar_date) && strtolower($webinar_data->webinar_date) === 'auto' ) ;
	}

    public static function is_paid_webinar( $webinar_data ) {
        $paid_status = isset($webinar_data->paid_status) ? strtolower(trim($webinar_data->paid_status)) : null;
        $paid_code   = isset($webinar_data->paid_code) ? trim($webinar_data->paid_code) : null;
        if(isset($webinar_data->paid_status))

		return ( $paid_status === 'paid' && !empty($paid_code) );
	}

    public static function get_paid_webinar_type( $webinar_data ) {
        $paid_type = isset($webinar_data->paid_button_type) ? strtolower(trim($webinar_data->paid_button_type)) : null;

        if(empty($paid_type)) {
	        $paid_type = false;
        }

        return $paid_type;
	}

	public static function get_webinar_page_id($webinar_data, $page_type) {
		$page_id = 0;

		if( $page_type === 'webinar' ) {
			$page_id = isset($webinar_data->custom_webinar_page) ? absint($webinar_data->custom_webinar_page) : 0;
		} else if( $page_type === 'registration' ) {
			$page_id = isset($webinar_data->custom_registration_page) ? (array) $webinar_data->custom_registration_page : 0;
		} else if ($page_type === 'countdown') {
			$page_id = isset($webinar_data->custom_countdown_page) ? absint($webinar_data->custom_countdown_page) : 0;
		} elseif ($page_type === 'closed') {
			$page_id = isset($webinar_data->custom_closed_page) ? absint($webinar_data->custom_closed_page) : 0;
		} elseif ($page_type === 'replay') {
			$page_id = isset($webinar_data->custom_replay_page) ? absint($webinar_data->custom_replay_page) : 0;
		} elseif ($page_type === 'thank_you' || $page_type === 'thankyou') {
			$page_id = isset($webinar_data->custom_thankyou_page) ? absint($webinar_data->custom_thankyou_page) : 0;
		}

        if( is_array( $page_id ) ) {
            return $page_id;
        }

		if( empty($page_id) || ( get_post_type($page_id) === false ) ) {
			$page_id    = self::get_webinar_post_id($webinar_data->id);
		}

		return $page_id;
	}

	public static function get_permalink($webinar_data, $page_type) {
		$page_id = self::get_webinar_page_id($webinar_data, $page_type);

        if( 'registration' == $page_type ) {
            $default = isset( $webinar_data->default_registration_page ) ? $webinar_data->default_registration_page : 0;

	        if( is_array($page_id) ) {
		        if( !in_array( $default, $page_id ) ) {
			        $default = reset( $page_id );
		        }
	        }

	        if( !empty( $default ) ) {
		        return get_the_permalink($default);
	        }

	        $default = WebinarignitionManager::get_webinar_post_id( $webinar_data->id );

	        if( !empty( $default ) ) {
                return get_the_permalink($default);
            }
        }

		return get_the_permalink($page_id);
	}

	public static function get_user_from_wc_order_id() {

        $input_get = filter_input_array( INPUT_GET );

		$user_email = ( isset($input_get['sremail']) && !empty($input_get['sremail']) ) ? trim(sanitize_text_field($input_get['sremail'])) : null;

		$order_id = self::url_has_valid_wc_order_id();
		$user = null;

		if( !empty($order_id) ) { //Consider it as paid webinar redirect
			$order = wc_get_order( $order_id );
            $order_user_id = $order->get_user_id();

            if( $order->has_shipping_address() ) {
                $user_first_name = $order->get_shipping_first_name();
                $user_last_name  = $order->get_shipping_last_name();
            } else {
                $user_first_name = $order->get_billing_first_name();
                $user_last_name  = $order->get_billing_last_name();
            }

            $user_full_name  = "{$user_first_name} {$user_last_name}";
            if ( empty( $user_email ) ) {
                $user_email = $order->get_billing_email();
            }

            $user = json_decode(json_encode([
                'ID' => $order_user_id,
                'display_name' => "{$user_full_name}",
                'first_name' => "{$user_first_name}",
                'last_name'  => "{$user_last_name}",
                'user_email' => "{$user_email}",
            ]));

		}

		return $user;
	}

	public static function url_has_valid_paid_code($webinar_data) {
		$webinar_paid_code = trim($webinar_data->paid_code);

        return ( !empty( $webinar_paid_code ) && isset($_GET[$webinar_paid_code]) );
	}

	public static function url_is_confirmed_set() {
		return isset($_GET['confirmed']);
	}

	public static function url_has_valid_lead_id() {
		$input_get = filter_input_array( INPUT_GET );

		if( isset($input_get['lid']) ) {
			$lead_id = trim($input_get['lid']);
			if( !empty($lead_id) ) {
			    return $lead_id;
            }
        }

		return false;
	}

	/**
     * Check if page/post is in preview mode for any third-party page builder
     *
	 * @return bool
	 */
	private static function is_builder_preview() {

        global $post;

        if( empty( $post ) || !isset( $post->ID ) ) {
            return false;
        }

        $has_editor_access = current_user_can( 'edit_published_pages' );
        //Elementor
		if ( class_exists('\Elementor\Plugin') && \Elementor\Plugin::$instance->preview->is_preview_mode($post->ID) && $has_editor_access ) {
			return true;
		}

		//OptimizePress
		if( class_exists('OPBuilder\Providers\BuilderBootstrap') && $has_editor_access ) { //Check if OptimizePress3 is active and user has editor access

			if( class_exists('OPBuilder\Providers\BuilderBootstrap') && $has_editor_access ) { //Check if OptimizePress3 is active and user has editor access
				if( function_exists('op3_is_admin') && function_exists('is_op3_page') ) {
					if ( is_op3_page( $post->ID ) && (op3_is_admin() || is_preview()) ) {
						return true;
					}
				}
			}
		}

		//Fusion/Avada
		$is_avada_builder_preview = ( function_exists( 'fusion_is_preview_frame' ) && fusion_is_preview_frame() ) || ( function_exists( 'fusion_is_builder_frame' ) && fusion_is_builder_frame() );
		if( $is_avada_builder_preview ) {
			return true;
		}

		return false;
	}

	public static function url_is_preview_page() {
		$input_get = filter_input_array( INPUT_GET );

		//Check if current user has editor access for this particular post/page
		$has_editor_access = current_user_can( 'edit_published_pages' );

		return (
			    $has_editor_access &&
		        (
		                is_preview() ||
		                self::is_builder_preview() ||
                        isset($input_get['preview_auto_thankyou']) ||
                        isset($input_get['preview-webinar']) ||
                        isset($input_get['preview-replay']) ||
                        isset($input_get['preview-countdown']) ||
                        ( isset($input_get['lid']) && $input_get['lid'] === '[lead_id]' )
                )
        );
	}

	public static function url_has_valid_wc_order_id() {

        if ( isset( $_GET['order'] ) ) {
			$order_id = absint( $_GET['order'] );
			if ( !empty( $order_id ) && class_exists( 'WooCommerce' ) ) {
				$order = wc_get_order( $order_id );

				if ( !empty( $order ) ) {
					return $order_id;
				}
			}
		} else if ( isset( $_GET['order_id'] ) ) {
			$order_id = absint( $_GET['order_id'] );
			if ( !empty( $order_id ) && class_exists( 'WooCommerce' ) ) {
				$order = wc_get_order( $order_id );

				if ( !empty( $order ) ) {
					return $order_id;
				}
			}
		}

        return false;
	}

	public static function url_is_calendar_page() {
        global $post;

        if($post) {
            $input_get = filter_input_array( INPUT_GET );
	        return (
                isset( $input_get['googlecalendarA'] ) ||
                isset( $input_get['icsA'] )  ||
                isset( $input_get['googlecalendar'] )  ||
                isset( $input_get['ics'] )
            );
        }

		return false;
	}

	/**
     * Return auto redirect URL only when enabled and has valid URL, false otherwise
     *
	 * @param $webinar_data
	 */
	public static function get_auto_redirect_url($webinar_data) {

		if(
            isset($webinar_data->auto_redirect) &&
            $webinar_data->auto_redirect === 'redirect' &&
            isset($webinar_data->auto_redirect_url) &&
            wp_http_validate_url($webinar_data->auto_redirect_url, FILTER_VALIDATE_URL)
        ) {
			return $webinar_data->auto_redirect_url;
		}

		return false;
	}
}
