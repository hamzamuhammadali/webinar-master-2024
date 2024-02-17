<?php defined( 'ABSPATH' ) || exit;

class WebinarignitionPowerupsShortcodes {
    private static function get_renamed_shortcodes() {
        return array(
            'old_shortcode_block' => 'new_shortcode_block',
            'ty_message_area' => 'ty_video_area',
        );
    }

    public static function get_available_shortcodes() {
        return array(
            'global_webinar_title' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'global',
                'cb' => 'webinarignition_get_webinar_title',
                'description' => 'global-lead-name',
            ),
            'global_webinar_giveaway' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'global',
                'cb' => 'webinarignition_get_webinar_giveaway_compact',
            ),
            'global_host_name' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'global',
                'cb' => 'webinarignition_get_host_name',
            ),
            'global_lead_name' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'global',
                'cb' => 'webinarignition_get_lead_name',
            ),
            'global_lead_email' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'global',
                'cb' => 'webinarignition_get_lead_email',
            ),
            'reg_banner' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'registration',
                'cb' => 'webinarignition_get_lp_banner_short',
                'description' => 'reg-banner',
            ),
            'reg_main_headline' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'registration',
                'cb' => 'webinarignition_get_lp_main_headline',
                'description' => 'reg-main-headline',
            ),
            'reg_video_area' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'registration',
                'cb' => 'webinarignition_get_video_area',
                'description' => 'reg-video-area',
            ),
            'reg_host_info' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'registration',
                'cb' => 'webinarignition_get_lp_host_info',
                'description' => 'reg-host-info',
            ),
            'reg_sales_headline' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'registration',
                'cb' => 'webinarignition_get_lp_sales_headline',
                'description' => 'reg-sales-headline',
            ),
            'reg_sales_copy' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'registration',
                'cb' => 'webinarignition_get_lp_sales_copy',
                'description' => 'reg-sales-copy',
            ),
            'reg_optin_headline' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'registration',
                'cb' => 'webinarignition_get_lp_optin_headline',
            ),
            'reg_optin_dates' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'registration',
                'cb' => 'webinarignition_get_lp_event_dates',
            ),
            'reg_optin_dates_compact' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'registration',
                'cb' => 'webinarignition_get_lp_event_dates_compact',
            ),
            'reg_date_time_inline' => array(
                'type' => array('live'),
                'page' => 'registration',
                'cb' => 'webinarignition_get_date_time_inline',
            ),
            'reg_date_inline' => array(
                'type' => array('live'),
                'page' => 'registration',
                'cb' => 'webinarignition_get_date_inline',
            ),
            'reg_time_inline' => array(
                'type' => array('live'),
                'page' => 'registration',
                'cb' => 'webinarignition_get_time_inline',
            ),
            'reg_timezone_inline' => array(
                'type' => array('live'),
                'page' => 'registration',
                'cb' => 'webinarignition_get_timezone_inline',
            ),
            'reg_optin_form' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'registration',
                'cb' => 'webinarignition_get_lp_optin_form',
                'description' => 'reg-optin-form',
            ),
            'reg_optin_form_compact' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'registration',
                'cb' => 'webinarignition_get_lp_optin_form_compact',
            ),
            'reg_optin_section' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'registration',
                'cb' => 'webinarignition_get_lp_optin_section',
            ),

            // Thankyou page
            'ty_headline' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_headline',
                'description' => 'ty-headline',
            ),
            'ty_video_area' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_message_area',
                'description' => 'ty-message-area',
            ),
            'ty_webinar_url' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_webinar_url',
                'description' => 'ty-webinar-url',
            ),
            'ty_webinar_url_inline' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_webinar_url_inline',
            ),
            'ty_calendar_reminder' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_calendar_reminder',
                'description' => 'ty-calendar-reminder',
            ),
            'ty_calendar_reminder_google_inline' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_calendar_reminder_google',
            ),
            'ty_calendar_reminder_outlook_inline' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_calendar_reminder_outlook',
            ),
            'ty_sms_reminder' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_sms_reminder',
                'description' => 'ty-sms-reminder',
            ),
            'ty_sms_reminder_compact' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_sms_reminder_compact',
            ),
            'ty_share_gift' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_share_gift',
                'description' => 'ty-share-gift',
            ),
            'ty_share_gift_compact' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_share_gift_compact',
            ),
            'ty_ticket_date' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_ticket_date',
                'description' => 'ty-ticket-date',
            ),
            'ty_date_time_inline' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_date_time_inline',
            ),
            'ty_date_inline' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_date_inline',
            ),
            'ty_time_inline' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_time_inline',
            ),
            'ty_timezone_inline' => array(
                'type' => array('live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_timezone_inline',
            ),
            'ty_ticket_webinar' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_ticket_webinar',
                'description' => 'ty-ticket-webinar',
            ),
            'ty_ticket_webinar_inline' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_ticket_webinar_inline',
            ),
            'ty_ticket_host' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_ticket_host',
                'description' => 'ty-ticket-host',
            ),
            'ty_ticket_host_inline' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_ticket_host_inline',
            ),
            'ty_countdown' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'thankyou',
                'cb' => 'webinarignition_get_ty_countdown',
                'description' => 'ty-countdown',
            ),
//            'ty_countdown_compact' => array(
//                'type' => array('evergreen', 'live'),
//                'page' => 'thankyou',
//                'cb' => 'webinarignition_get_ty_countdown_compact',
//            ),

            // Replay page
            'replay_main_headline' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'replay',
                'cb' => 'webinarignition_get_replay_main_headline',
                'description' => 'replay-main-headline',
            ),
            'replay_video' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'replay',
                'cb' => 'webinarignition_get_replay_video',
                'description' => 'replay-video',
            ),
            'replay_cta' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'replay',
                'cb' => 'webinarignition_get_replay_video_under_cta',
                'description' => 'replay-cta',
            ),
            'replay_info' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'replay',
                'cb' => 'webinarignition_get_replay_info',
                'description' => 'replay-info',
            ),
            'replay_giveaway' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'replay',
                'cb' => 'webinarignition_get_replay_giveaway',
                'description' => 'replay-giveaway',
            ),

            // Countdown page
            'countdown_main_headline' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'countdown',
                'cb' => 'webinarignition_get_countdown_main_headline',
                'description' => 'countdown-main-headline',
            ),
            'countdown_headline' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'countdown',
                'cb' => 'webinarignition_get_countdown_headline',
                'description' => 'countdown-headline',
            ),
            'countdown_counter' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'countdown',
                'cb' => 'webinarignition_get_countdown_counter',
                'description' => 'countdown-counter',
            ),
            'countdown_signup' => array(
                'type' => array('live'),
                'page' => 'countdown',
                'cb' => 'webinarignition_get_countdown_signup',
                'description' => 'countdown-signup',
            ),

            // Webinar page
            'webinar_video_cta' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'webinar',
                'cb' => 'webinarignition_get_webinar_video_cta_comb',
                'description' => 'webinar-video-cta-comb',
            ),
            'webinar_sidebar' => array(
                'type' => array( 'evergreen', 'live' ),
                'page' => 'webinar',
                'cb' => 'webinarignition_get_webinar_sidebar',
                'description' => 'sidebar',
            ),
            'webinar_video' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'webinar',
                'cb' => 'webinarignition_get_webinar_video_cta',
                'description' => 'webinar-video-cta',
            ),
            'webinar_cta' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'webinar',
                'cb' => 'webinarignition_get_webinar_video_under_cta',
                'description' => 'webinar-cta',
            ),
            'webinar_info' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'webinar',
                'cb' => 'webinarignition_get_webinar_info',
                'description' => 'webinar-info',
            ),
            'webinar_giveaway' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'webinar',
                'cb' => 'webinarignition_get_webinar_giveaway',
                'description' => 'webinar-giveaway',
            ),
            'webinar_giveaway_compact' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'webinar',
                'cb' => 'webinarignition_get_webinar_giveaway_compact',
            ),
            'webinar_qa' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'webinar',
                'cb' => 'webinarignition_get_webinar_qa',
                'description' => 'webinar-qa',
            ),
            'webinar_qa_compact' => array(
                'type' => array('evergreen', 'live'),
                'page' => 'webinar',
                'cb' => 'webinarignition_get_webinar_qa_compact',
                'description' => 'webinar-qa-compact',
            ),
        );
    }

    public static function get_available_templates() {
        return array(
            'custom_registration_page' => array(
                'title' => __( "Select Registration page template", 'webinarignition' ),
                'help' => __( "", 'webinarignition' ),
                'params' => '',
            ),
            'custom_thankyou_page' => array(
                'title' => __( "Select Thank You page template", 'webinarignition' ),
                'help' => __( "", 'webinarignition' ),
                'params' => 'thankyou&lid=[lead_id]',
            ),
            'custom_webinar_page' => array(
                'title' => __( "Select Webinar page template", 'webinarignition' ),
                'help' => __( "", 'webinarignition' ),
                'params' => 'webinar&lid=[lead_id]',
            ),
            'custom_countdown_page' => array(
                'title' => __( "Select Webinar Countdown page template", 'webinarignition' ),
                'help' => __( "", 'webinarignition' ),
                'params' => 'countdown&lid=[lead_id]',
            ),
            'custom_replay_page' => array(
                'title' => __( "Select Webinar Replay page template", 'webinarignition' ),
                'help' => __( "", 'webinarignition' ),
                'params' => 'replay&lid=[lead_id]',
            ),
//            'custom_closed_page' => array(
//                'title' => __( "Select Webinar Closed page template", 'webinarignition' ),
//                'help' => __( "", 'webinarignition' ),
//                'params' => 'webinar={{webinar_id}}&lid=[lead_id]',
//            ),
        );
    }

    public static function is_enabled($webinar_data) {
        return WebinarignitionPowerups::is_shortcodes_enabled($webinar_data);
    }

    public static function init() {
        add_shortcode('wi_webinar_block', array('WebinarignitionPowerupsShortcodes', 'shortcode'));
        add_shortcode('webinarignition_footer', array('WebinarignitionPowerupsShortcodes', 'webinarIgnition_footer_sc'));
    }

    public static function show_shortcode_description($block, $webinar_data, $display = true, $two_col = false) {
        if (!self::is_enabled($webinar_data)) {
            $html = '';
        } else {
            $renamed_shortcodes = self::get_renamed_shortcodes();

            if (!empty($renamed_shortcodes[$block])) {
                $block = $renamed_shortcodes[$block];
            }

            $template_array = self::get_available_shortcodes();
            $webinar_type = $webinar_data->webinar_date === 'AUTO' ? 'evergreen' : 'live';

            if (
                empty($template_array[$block]) ||
                !in_array($webinar_type, $template_array[$block]['type']) ||
                empty($template_array[$block]['description'])
            ) {
                $html = '';
            } else {
                if ($two_col) {
                    ob_start();
                    webinarignition_display_two_col_info(
                        __( "Available shortcode", 'webinarignition' ),
                        "",
                        self::get_shortcode_description($template_array[$block]['description'], $webinar_data)
                    );
                    $html = ob_get_clean();
                } else {
                    $html = wpautop(self::get_shortcode_description($template_array[$block]['description'], $webinar_data));
                }
            }

        }

        if (!$display) return $html;
        echo $html;
    }

    public static function get_shortcode_description($path, $webinar_data) {
        if (!file_exists(WEBINARIGNITION_PATH . "UI/shortcodes_descriptions/{$path}.php")) {
            return '';
        }



        global $webinarignition_shortcodes_is_list;
        $is_list = $webinarignition_shortcodes_is_list;

        ob_start(); 
        include WEBINARIGNITION_PATH . "UI/shortcodes_descriptions/{$path}.php";
        return ob_get_clean();
    }

    public static function webinarIgnition_footer_sc(){
        $webinarignition_footer_text = get_option( 'webinarignition_footer_text', '' );
        return $webinarignition_footer_text;
    }

    public static function shortcode($atts = array()) {
        global $webinarignition_shortcodes_options;
        global $webinarignition_shortcode_page;
        global $webinarignition_shortcode_scripts;

        if (empty($webinarignition_shortcodes_options)) {
            $webinarignition_shortcodes_options = array();
        }

        $params = shortcode_atts( array(
            'id' => '',
            'allowed_ids' => '',
            'block' => ''
        ), $atts );

        /**
         * @var int    $id
         * @var string $block
         */
        extract( $params );

        $renamed_shortcodes = self::get_renamed_shortcodes();

        if (!empty($renamed_shortcodes[$block])) {
            $block = $renamed_shortcodes[$block];
        }

        $available_shortcodes = self::get_available_shortcodes();

        if (empty($block) || empty($available_shortcodes[$block])) {
            return '';
        }

        $shortcode_settings = $available_shortcodes[$block];
        $id = get_query_var( 'webinar_id' );

        if (empty($id)) {
            $id = $atts["id"];
        }

        if (!empty($id)) {
            if (!empty($allowed_ids)) {
                $allowed = false;
                $allowed_ids_array = explode(',', $allowed_ids);

                foreach ($allowed_ids_array as $allowed_id) {
                    if ((int) $allowed_id === (int) $id) {
                        $allowed = true;
                    }
                }

                if (!$allowed) {
                    return '';
                }
            }
        }

        if (empty($id)) {
            return '';
        }

        if ( empty( $webinarignition_shortcodes_options[ $id ] ) ) {
            $results = WebinarignitionManager::get_webinar_data( $id );

            if ( empty( $results ) ) {
                return '';
            }

            $webinar_type = $results->webinar_date === 'AUTO' ? 'evergreen' : 'live';

            if ( ! in_array( $webinar_type, $shortcode_settings['type'] ) ) {
                return '';
            }

            $webinarignition_shortcodes_options[ $id ] = $results;
        } else {
            $results      = $webinarignition_shortcodes_options[ $id ];
            $webinar_type = $results->webinar_date === 'AUTO' ? 'evergreen' : 'live';

            if ( ! in_array( $webinar_type, $shortcode_settings['type'] ) ) {
                return '';
            }
        }

	    if ( ! self::is_enabled( $results ) && $webinar_type === 'evergreen') {
            return '';
        }

        if ( empty( $webinarignition_shortcode_page ) ) {
            $webinarignition_shortcode_page = $shortcode_settings['page'];
        }

        if ( empty( $webinarignition_shortcode_page ) ) {
            $webinarignition_shortcode_page = $shortcode_settings['page'];
        }

        if ( empty( $webinarignition_shortcode_scripts ) && false === strpos( $block, 'global' ) ) {
            $webinarignition_shortcode_scripts = $shortcode_settings['page'];

            self::enqueue_scripts( $id, $block );
        }

        return self::html($id, $block);
    }

    public static function html($webinarId, $block) {
        global $webinarignition_shortcodes_options;

        if (empty($webinarignition_shortcodes_options[$webinarId])) {
            return '';
        }

        $webinar_data = $webinarignition_shortcodes_options[$webinarId];
        $webinar_type = $webinar_data->webinar_date === 'AUTO' ? 'evergreen' : 'live';

        $template_cb_array = self::get_available_shortcodes();

        if (empty($template_cb_array[$block]) || !in_array($webinar_type, $template_cb_array[$block]['type'])) {
            return '';
        }

        self::set_page_params($webinarId, $template_cb_array[$block]['page']);

        $template_cb = $template_cb_array[$block]['cb'];

        //Switch to webinar language before getting shortcode HTML
	    WebinarignitionManager::set_locale($webinar_data);

	    $html = $template_cb($webinar_data);

	    //Restore back to previous language after getting shortcode HTML
	    WebinarignitionManager::restore_locale($webinar_data);

	    return $html;
    }

    public static function enqueue_scripts($webinarId, $block) {
        $template_cb_array = self::get_available_shortcodes();

        if ( empty( $template_cb_array[ $block ] ) ) {
            return '';
        }

        global $webinarignition_shortcodes_options;

        if ( empty( $webinarignition_shortcodes_options[ $webinarId ] ) ) {
            return '';
        }

        $page         = $template_cb_array[ $block ]['page'];
        $enqueue_cb   = 'enqueue_' . $page . '_page_script';
        $webinar_data = $webinarignition_shortcodes_options[ $webinarId ];

        if ( method_exists( 'WebinarignitionPowerupsShortcodes', $enqueue_cb ) ) {
            WebinarignitionPowerupsShortcodes::{$enqueue_cb}( $webinar_data );
        }
    }

    public static function enqueue_registration_page_script($webinar_data) {
        $webinarId = $webinar_data->id;
        //<head> css
        wp_enqueue_style( 'webinarignition_intlTelInput' );
        wp_enqueue_style( 'webinarignition_font-awesome' );

        if ( isset($webinar_data->custom_templates_styles) || 'off' !== $webinar_data->custom_templates_styles ) {
        	//Enqueue based style directly, so that in-line style can be inserted
	        wp_enqueue_style( 'webinarignition_main_template', WEBINARIGNITION_URL . 'inc/lp/css/main-template.css', [], WEBINARIGNITION_VERSION . '-' . time() );
	        wp_add_inline_style( 'webinarignition_main_template', wp_strip_all_tags( webinarignition_inline_css_file( WEBINARIGNITION_PATH . 'inc/lp/css/lp_css.php', $webinar_data ) ) ); //Used in shortcodes
        }

	    //Load wi admin CSS on frontend to display modal properly, when registration shortcode has been used on a custom page
        if( isset($_GET['artest']) && $_GET['artest'] == 1 ) {
	        wp_enqueue_style( 'webinarignition-admin', WEBINARIGNITION_URL . 'css/webinarignition-admin.css', [], '2.2.9' );
        }

        //<head> js
        wp_enqueue_script( 'webinarignition_js_moment' );
        wp_enqueue_script( 'webinarignition_js_utils' );

        //footer scripts
        wp_enqueue_script( 'webinarignition_cookie_js' );
        wp_enqueue_script( 'webinarignition_intlTelInput_js' );
        wp_enqueue_script( 'webinarignition_frontend_js' );
        wp_enqueue_script( 'webinarignition_tz_js' );
        wp_enqueue_script( 'webinarignition_luxon_js' );

        if( ! empty($webinar_data->custom_lp_css) ) {
            // $custom_css = esc_html($webinar_data->custom_lp_css);
            // wp_add_inline_style( 'webinarignition_main', $custom_css );
        }

        if( ! empty($webinar_data->custom_lp_js) ) {
            wp_add_inline_script( 'wi_js_utils', $webinar_data->custom_lp_js );
        }

        if( ! empty($webinar_data->stripe_publishable_key) ) {
            wp_enqueue_script( 'webinarignition_stripe_js', 'https://js.stripe.com/v2/', [], false, true );
            $setPublishableKey = 'Stripe.setPublishableKey("'. $webinar_data->stripe_publishable_key. '")';
            wp_add_inline_script( 'webinarignition_stripe_js', $setPublishableKey );
        }

        if( !empty( $webinar_data->paid_status ) && ( $webinar_data->paid_status == "paid" ) ) {
            $paid_js_code = "var paid_code = {code: $webinar_data->paid_code}";
            wp_add_inline_script( 'wi_js_utils', $paid_js_code );
        }

        global $wpdb;
        $webinar_post_id = $wpdb->get_var( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'webinarignitionx_meta_box_select' AND meta_value = {$webinarId} ORDER BY meta_id ASC;" );

        if (!empty($webinar_post_id)) {
            $webinar_data->webinar_id = $webinar_post_id;
            $webinar_data->ty_id = $webinar_post_id;
        }

        if (empty($webinarignition_shortcodes_options['webinarUrl'])) {

        }

        if (empty($webinarignition_shortcodes_options['thankYouPageUrl'])) {

        }

        $wi_parsed = webinarignition_parse_registration_page_data( $webinarId, $webinar_data );

        $isSigningUpWithFB = false;
        $fbUserData = [];
        $input_get = filter_input_array(INPUT_GET);

        if( !empty($webinar_data->fb_id) && !empty($webinar_data->fb_secret) && isset($input_get['code']) ) {
            include_once( "lp/fbaccess.php" );
            /**
             * @var $user_info;
             */
            $isSigningUpWithFB                      = true;
            $fbUserData['name']                     = $user_info['name'];
            $fbUserData['email']                    = $user_info['email'];
        }

        $wi_parsed['isSigningUpWithFB'] = $isSigningUpWithFB;
        $wi_parsed['fbUserData']        = $fbUserData;
        $window_webinarignition         = 'window.webinarignition = ' .  "'" . json_encode($wi_parsed, JSON_HEX_APOS) . "'";
        $window_security                = "window.wiRegJS.ajax_nonce = '".wp_create_nonce('webinarignition_ajax_nonce')."'";

	    wp_enqueue_script( 'webinarignition_registration_js', WEBINARIGNITION_URL . 'inc/lp/js/registration-page.js', [
		    'jquery',
		    'webinarignition_cookie_js',
		    'webinarignition_intlTelInput_js',
		    'webinarignition_frontend_js',
		    'webinarignition_tz_js',
		    'webinarignition_luxon_js'
	    ], WEBINARIGNITION_VERSION . '-' . time(), true );

	    wp_localize_script('webinarignition_registration_js', 'wiRegJS', [
	    	'ajax_nonce' => wp_create_nonce('webinarignition_ajax_nonce')
	    ]);

        wp_add_inline_script( 'webinarignition_registration_js', $window_webinarignition, 'before' );
//        wp_add_inline_script( 'webinarignition_registration_js', $window_security, 'before' );

        wp_enqueue_script( 'webinarignition_after_footer_js' );
    }

    public static function enqueue_thankyou_page_script($webinar_data) {
        $webinarId = $webinar_data->id;
        extract(webinarignition_get_ty_templates_vars($webinar_data));

        //<head> css
        wp_enqueue_style( 'webinarignition_intlTelInput' );
        wp_enqueue_style( 'webinarignition_font-awesome' );
        wp_enqueue_style( 'webinarignition_countdown_ty' );

        if (empty($webinar_data->custom_templates_styles) || 'off' !== $webinar_data->custom_templates_styles) {
            wp_enqueue_style( 'webinarignition_main_template' );
        }

	    $ty_css = '.topArea {';

        if( $webinar_data->lp_banner_bg_style === 'hide' ) {
	        $ty_css .= ' display: none;';
        }

        if( empty($webinar_data->lp_banner_bg_color) ) {
	        $ty_css .= ' background-color: #FFF;';
        } else {
	        $ty_css .= " background-color: {$webinar_data->lp_banner_bg_color};";
        }

        if( empty($webinar_data->lp_banner_bg_repeater) ) {
	        $ty_css .= ' border-top: 3px solid rgba(0,0,0,0.20); border-bottom: 3px solid rgba(0,0,0,0.20);';
        } else {
	        $ty_css .= ' background-image: url(' . $webinar_data->lp_banner_bg_repeater . ');';
        }

	    $ty_css .= '}';
	    $ty_css .= ' .mainWrapper{ background-color: #f1f1f1; }';

	    wp_enqueue_style( 'webinarignition_countdown_ty_inline');
	    wp_add_inline_style( 'webinarignition_countdown_ty_inline', $ty_css );

        if ( ! empty( $webinar_data->custom_ty_css ) )
            wp_add_inline_style( 'webinarignition_head_style', esc_html( $webinar_data->custom_ty_css ) );

        //<head> js
        wp_enqueue_script( 'webinarignition_js_moment');
        wp_enqueue_script( 'webinarignition_cookie_js' );
        wp_enqueue_script( 'webinarignition_intlTelInput_js' );
        wp_enqueue_script( 'webinarignition_frontend_js' );
        wp_enqueue_script( 'webinarignition_countdown_js');

        wp_enqueue_script( 'webinarignition_countdown_ty_inline');
	    wp_add_inline_script( 'webinarignition_countdown_ty_inline',
		    webinarignition_inline_js_file( WEBINARIGNITION_PATH . 'inc/lp/partials/countdown_page/cd-inline_head_js.php', $webinar_data )
	    );

        wp_enqueue_script( 'webinarignition_before_footer_js');

        if ( ! empty( $webinar_data->custom_ty_js ) )
            wp_add_inline_script( 'webinarignition_js_moment', $webinar_data->custom_ty_js );

        wp_enqueue_script( 'webinarignition_after_footer_js' );

        $after_footer_js = [
            WEBINARIGNITION_PATH . 'inc/lp/global_footer_inline_js.php',
            WEBINARIGNITION_PATH . 'inc/lp/partials/thank_you_page/ty_js.php',
        ];

        wp_add_inline_script( 'webinarignition_after_footer_js',
            webinarignition_inline_js_file( $after_footer_js, $webinar_data ),
            'before'
        );
    }

    public static function enqueue_replay_page_script($webinar_data) {
        extract(webinarignition_get_replay_templates_vars($webinar_data));

        wp_enqueue_style( 'webinarignition_font-awesome' );

        if (empty($webinar_data->custom_templates_styles) || 'off' !== $webinar_data->custom_templates_styles)
            wp_enqueue_style( 'webinarignition_main_template' );

        if ( webinarignition_should_use_videojs( $webinar_data ) )
            wp_enqueue_style( 'webinarignition_video_css' );

        wp_enqueue_style( 'webinarignition_head_style_after' );

        if ( ! empty( $webinar_data->custom_replay_css ) )
            wp_add_inline_style( 'webinarignition_head_style_after', esc_html( $webinar_data->custom_replay_css ) );


        /** ====================================
         *  HEAD JS
            ==================================== */
        wp_enqueue_script( 'webinarignition_js_countdown');
        wp_enqueue_script( 'webinarignition_cookie_js' );

        wp_add_inline_script( 'webinarignition_cookie_js',
            webinarignition_inline_js_file( WEBINARIGNITION_PATH . 'inc/lp/partials/replay_page/inline_head_js.php', $webinar_data )
        );

        if ( ! empty( $webinar_data->custom_replay_js ) )
            wp_add_inline_script( 'webinarignition_cookie_js', '(function ($) {' . $webinar_data->custom_replay_js . '})(jQuery);' );

        if ( webinarignition_should_use_videojs( $webinar_data ) )
            wp_enqueue_script( 'webinarignition_video_js' );

        /** ====================================
         *  FOOTER JS
            ==================================== */
        wp_enqueue_script( 'webinarignition_before_footer_js' );

        if ( $webinar_data->webinar_qa !== "hide" ) {
            wp_add_inline_script( 'webinarignition_before_footer_js',
                webinarignition_inline_js_file( [
                    WEBINARIGNITION_PATH . 'inc/lp/partials/fb_share_js.php',
                    WEBINARIGNITION_PATH . 'inc/lp/partials/tw_share_js.php',
                ], $webinar_data),
                'before'
            );
        }

        wp_enqueue_script( 'webinarignition_after_footer_js' );

        $after_footer_js = [WEBINARIGNITION_PATH . 'inc/lp/global_footer_inline_js.php'];

        if ( $webinar_data->webinar_date == "AUTO" )
            $after_footer_js[] = WEBINARIGNITION_PATH . 'inc/lp/webinar-auto-video-inline-js.php';

        $after_footer_js[] = WEBINARIGNITION_PATH . 'inc/lp/webinar-cta-inline-js.php';

        wp_add_inline_script( 'webinarignition_after_footer_js',
            webinarignition_inline_js_file( $after_footer_js, $webinar_data ),
            'before'
        );
        wp_enqueue_script( 'webinarignition_webinar_shared_js' );

        //TODO: Investigate why following localize script not wokring on "webinarignition_webinar_shared_js" handle,
	    //assignining it to "jquery" instead to make it work
        wp_localize_script( 'jquery', 'wiJsObj', array(
            'ajaxurl'       => admin_url( 'admin-ajax.php' ),
            'someWrong'     => __( 'Something went wrong', 'webinarignition' )
//                'nonce'         => wp_create_nonce( 'joints_nonce' ),
//                'wooNonce'      => wp_create_nonce( 'joints_woo_nonce' ),
        ) );
    }

    public static function enqueue_countdown_page_script($webinar_data) {
        extract(webinarignition_get_countdown_templates_vars($webinar_data));

        wp_enqueue_style( 'webinarignition_font-awesome' );

        if (empty($webinar_data->custom_templates_styles) || 'off' !== $webinar_data->custom_templates_styles) {
            wp_enqueue_style( 'webinarignition_countdown' );
            wp_enqueue_style( 'webinarignition_main_template' );
            wp_add_inline_style( 'webinarignition_main_template',
                webinarignition_inline_css_file( WEBINARIGNITION_PATH . 'inc/lp/css/webinar_css.php', $webinar_data )
            );
        }

        //<head> js
        wp_enqueue_script( 'webinarignition_cookie_js' );
        wp_enqueue_script( 'webinarignition_js_countdown');
        wp_add_inline_script( 'webinarignition_js_countdown',
            webinarignition_inline_js_file( WEBINARIGNITION_PATH . 'inc/lp/partials/countdown_page/cd-inline_head_js.php', $webinar_data )
        );

        // Footer JS
        wp_enqueue_script( 'webinarignition_after_footer_js' );
        wp_add_inline_script( 'webinarignition_after_footer_js',
            webinarignition_inline_js_file( WEBINARIGNITION_PATH . 'inc/lp/global_footer_inline_js.php', $webinar_data )
        );
    }

    public static function enqueue_webinar_page_script( $webinar_data ) {
        $template_vars = webinarignition_get_webinar_templates_vars( $webinar_data );
        extract( $template_vars );

        wp_enqueue_style( 'webinarignition_font-awesome' );

        if ( empty( $webinar_data->custom_templates_styles ) || 'off' !== $webinar_data->custom_templates_styles ) {
            wp_enqueue_style( 'webinarignition_main_template' );
        }

        if ( webinarignition_should_use_videojs( $webinar_data ) ) {
            wp_enqueue_style( 'webinarignition_video_css' );
        }

        wp_enqueue_style( 'webinarignition_head_style_after' );

        if ( ! empty( $webinar_data->custom_webinar_css ) ) {
            wp_add_inline_style( 'webinarignition_head_style_after', esc_html( $webinar_data->custom_webinar_css ) );
        }

        wp_enqueue_style( 'webinarignition_webinar_shared' );

        wp_enqueue_script( 'webinarignition_cookie_js' );

        if ( ! empty( $webinar_data->custom_webinar_js ) ){
            $custom_webinar_js = $webinar_data->custom_webinar_js;
            wp_add_inline_script( 'webinarignition_cookie_js', '(function ($) {' . $custom_webinar_js . '})(jQuery);' );
        }

        wp_enqueue_script( 'webinarignition_countdown_js');
        wp_enqueue_script( 'webinarignition_polling_js' );
        wp_enqueue_script( 'webinarignition_updater_js' );

        if ( webinarignition_should_use_videojs( $webinar_data ) ) {
            wp_enqueue_script( 'webinarignition_video_js' );
        }

	    $webinar_cta_by_position = WebinarignitionManager::get_webinar_cta_by_position( $webinar_data );

	    if ( ! empty( $webinar_cta_by_position['overlay'] ) ) {
		    wp_enqueue_script( 'webinarignition_webinar_cta_js' );
		    wp_enqueue_script( 'webinarignition_webinar_modern_js' );
	    }

        wp_enqueue_script( 'webinarignition_after_footer_js' );

        $after_footer_js = [
            WEBINARIGNITION_PATH . 'inc/lp/global_footer_inline_js.php',
            WEBINARIGNITION_PATH . 'inc/lp/partials/webinar_page/webinar_inline_js.php',
            WEBINARIGNITION_PATH . 'inc/lp/webinar-cta-inline-js.php'
        ];

        if ( $webinar_data->webinar_date == "AUTO" ) {
            $after_footer_js[] = WEBINARIGNITION_PATH . 'inc/lp/webinar-auto-video-inline-js.php';
        }

        wp_add_inline_script( 'webinarignition_after_footer_js',
            webinarignition_inline_js_file( $after_footer_js, $webinar_data ),
            'before'
        );

        wp_enqueue_script( 'webinarignition_webinar_shared_js' );

        wp_localize_script( 'webinarignition_webinar_shared_js', 'wiJsObj', array(
            'ajaxurl'       => admin_url( 'admin-ajax.php' ),
            'someWrong'     => __( 'Something went wrong', 'webinarignition' )
        ) );
    }

    private static function set_page_params($webinarId, $page) {
        global $webinarignition_shortcodes_options;

        $webinarignition_shortcodes_options[$webinarId]->thankyou = array();
        $webinarignition_shortcodes_options[$webinarId]->registration = array();
        $webinarignition_shortcodes_options[$webinarId]->webinar = array();

        $params_array = array(
            'assets' => $assets = WEBINARIGNITION_URL . "inc/lp/"
        );

        if ('thankyou' === $page) {
            $webinarignition_shortcodes_options[$webinarId]->thankyou = $params_array;
        } elseif ('registration' === $page) {
            $webinarignition_shortcodes_options[$webinarId]->registration = $params_array;
        }
    }

    public static function get_dummy_lead($webinar_data) {
        $webinar_type = $webinar_data->webinar_date === 'AUTO' ? 'evergreen' : 'live';
        $webinarId = $webinar_data->id;

        if ('evergreen' === $webinar_type) {
            $lead = array(
                'ID' => '11111',
                'app_id' => $webinarId,
                'name' => __( 'John Smith', 'webinarignition' ),
                'email' => __( 'john.smith@gmail.com', 'webinarignition' ),
                'phone' => '',
                'skype' => NULL,
                'created' => date('F j, Y', time() - (60 * 60 * 1)),
                'date_picked_and_live' => date('Y-m-d H:i', time() + (60 * 60 * 6)),
                'date_picked_and_live_check' => 'sent',
                'date_1_day_before' => date('Y-m-d H:i', time() + (60 * 60 * 6) - (60 * 60 * 24)),
                'date_1_day_before_check' => 'sent',
                'date_1_hour_before' => date('Y-m-d H:i', time() + (60 * 60 * 6) - (60 * 60 * 1)),
                'date_1_hour_before_check' => 'sent',
                'date_after_live' => date('Y-m-d H:i', time() + (60 * 60 * 7)),
                'date_after_live_check' => 'sent',
                'date_1_day_after' => date('Y-m-d H:i', time() + (60 * 60 * 31)),
                'date_1_day_after_check' => 'sent',
                'lead_timezone' => 'Asia/Beirut',
                'lead_status' => 'complete',
                'event' => 'Yes',
                'replay' => 'No',
                'trk1' => 'Optin',
                'trk2' => NULL,
                'trk3' => '127.0.0.1',
                'trk4' => NULL,
                'trk5' => NULL,
                'trk6' => NULL,
                'trk7' => NULL,
                'trk8' => 'no',
                'trk9' => NULL,
                'lead_browser_and_os' => NULL,
                'gdpr_data' => '',
                'hash_ID' => '',
            );
        } else {
            $lead = array(
                'ID' => '11111',
                'app_id' => $webinarId,
                'name' => __( 'John Smith', 'webinarignition' ),
                'email' => __( 'john.smith@gmail.com', 'webinarignition' ),
                'phone' => '',
                'skype' => NULL,
                'event' => 'No',
                'replay' => 'No',
                'trk1' => 'Optin',
                'trk2' => NULL,
                'trk3' => '127.0.0.1',
                'trk4' => NULL,
                'trk5' => NULL,
                'trk6' => NULL,
                'trk7' => NULL,
                'trk8' => NULL,
                'trk9' => NULL,
                'lead_browser_and_os' => NULL,
                'gdpr_data' => '',
                'hash_ID' => '',
                'created' => date('F j, Y', time() - (60 * 60 * 1)),
            );
        }

        $object = new stdClass();
        foreach ($lead as $key => $value) {
            $object->$key = $value;
        }

        return $object;
    }
}

WebinarignitionPowerupsShortcodes::init();
