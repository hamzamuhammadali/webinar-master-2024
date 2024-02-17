<?php
defined( 'ABSPATH' ) || exit;

add_action('wp_ajax_webinarignition_get_webinar', 'webinarignition_get_webinar');

function webinarignition_get_webinar() {

    check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
    $post_input     = filter_input_array(INPUT_POST);
    $webinar = WebinarignitionManager::get_webinar_data(sanitize_text_field( $post_input['id'] ));

    if (isset($webinar->air_html)) {
        $webinar->air_html = stripcslashes($webinar->air_html);
    }

    if (isset($webinar->air_btn_copy)) {
        $webinar->air_btn_copy = stripcslashes($webinar->air_btn_copy);
    }

    if (isset($webinar->air_btn_url)) {
        $webinar->air_btn_url = stripcslashes($webinar->air_btn_url);
    }

    wp_send_json_success([
        'webinar' => $webinar,
    ]);
}


add_action('wp_ajax_webinarignition_save_on_air_settings', 'webinarignition_save_on_air_settings');
function webinarignition_save_on_air_settings() {

    check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
    $post_input     = filter_input_array(INPUT_POST);

    if ( !isset($post_input['id']) || !is_numeric($post_input['id'])) {
        wp_send_json_error( ['error' => 'Invalid Webinar ID'] );
    }

    $optionName = 'webinarignition_campaign_' . sanitize_text_field( $post_input['id'] );
    $option = WebinarignitionManager::get_webinar_data(sanitize_text_field( $post_input['id'] ));

    $onairStatus = sanitize_text_field( $post_input['onair_status'] );
    if (!in_array($onairStatus, array('on', 'off'))) {
        wp_send_json_error(array( __( 'on air message could not be toggled', "webinarignition") ));
    }

    $option->air_toggle = $onairStatus;

    if (isset($post_input['air_html'])) {
        $option->air_html = $post_input['air_html'];
    }

    $option->air_btn_copy   = sanitize_text_field( $post_input['air_btn_copy'] );

    $option->air_btn_url    = sanitize_text_field( $post_input['air_btn_url'] );

    $option->air_btn_color  = sanitize_text_field( $post_input['air_btn_color'] );

    update_option($optionName, $option);

    wp_send_json_success();
}


add_action('wp_ajax_webinarignition_toggle_on_air_message', 'webinarignition_toggle_on_air_message');
function webinarignition_toggle_on_air_message() {

    check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );
    $post_input     = filter_input_array(INPUT_POST);
    if ( !isset($post_input['id']) || !is_numeric($post_input['id'])) {
        wp_send_json_error( ['error' => __( 'Invalid Webinar ID', "webinarignition") ] );
    }

    $optionName = 'webinarignition_campaign_' . sanitize_text_field( $post_input['id'] );
    $option = WebinarignitionManager::get_webinar_data(sanitize_text_field( $post_input['id'] ));

    $onairStatus = sanitize_text_field( $post_input['onair_status'] );
    if (!in_array($onairStatus, array('on', 'off'))) {
        wp_send_json_error(array( __( 'on air message could not be toggled', "webinarignition") ));
    }

    $option->air_toggle = $onairStatus;
    update_option($optionName, $option);

    wp_send_json_success();
}

add_action('wp_ajax_webinarignition_get_leads', 'webinarignition_get_leads');

function webinarignition_get_leads() {

    check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );

    global $wpdb;

    $post_input                 = filter_input_array(INPUT_POST);
    $post_input['id']           = isset( $post_input['id'] )  ? sanitize_text_field( $post_input['id'] ) : null;
    $post_input['webinar_type'] = isset( $post_input['webinar_type'] )  ? sanitize_text_field( $post_input['webinar_type'] ) : null;
    $post_input['search_for']   = isset( $post_input['search_for'] )  ? sanitize_text_field( $post_input['search_for'] ) : null;
    $post_input['limit']        = isset( $post_input['limit'] )  ? sanitize_text_field( $post_input['limit'] ) : null;
    $post_input['offset']       = isset( $post_input['offset'] )  ? sanitize_text_field( $post_input['offset'] ) : null;

    if ( !isset($post_input['id']) || !is_numeric($post_input['id'])) {
        wp_send_json_error( ['error' => __( 'Invalid Webinar ID', "webinarignition") ] );
    }

    if ( !isset($post_input['webinar_type']) || !in_array($post_input['webinar_type'], ['evergreen', 'live'])) {
        wp_send_json_error( ['error' => __( 'Invalid Webinar Type', "webinarignition") ] );
    }

    $table_db_name = $post_input['webinar_type'] === 'evergreen' ? $wpdb->prefix . "webinarignition_leads_evergreen" : $wpdb->prefix . "webinarignition_leads";

    $sql = "
      SELECT *
      FROM {$table_db_name}
      WHERE app_id = %d ";

    if ( !empty($post_input['search_for']) ) {
        $sql .= " AND ( `name` LIKE %s OR `email` LIKE  %s )";
    }

    $sql .= "
      LIMIT %d
      OFFSET %d
    ";

    if ( !empty($post_input['search_for']) ) {
        $preparedSql = $wpdb->prepare( $sql,
            $post_input['id'],
            '%%' . $wpdb->esc_like( $post_input['search_for'] ) . '%%',
            '%%' . $wpdb->esc_like( $post_input['search_for'] ) . '%%',
            $post_input['limit'],
            $post_input['offset']
        );

        $totalQueryLeads = $wpdb->get_var(
            $wpdb->prepare(
                "
                  SELECT COUNT(*)
                  FROM {$table_db_name}
                  WHERE app_id = %d
                  AND `email` LIKE %s
                ",
                $post_input['id'],
                '%%' . $wpdb->esc_like( $post_input['search_for'] ) . '%%'
            )
        );
    } else {
        $preparedSql = $wpdb->prepare( $sql,
            $post_input['id'],
            $post_input['limit'],
            $post_input['offset']
        );
    }

    $leads = $wpdb->get_results($preparedSql, OBJECT);

    $totalLeads = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table_db_name} WHERE `app_id` = %d", $post_input['id']));
    $totalQueryLeads = isset($totalQueryLeads) ? $totalQueryLeads : $totalLeads;
    $totalAttendedEvent = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table_db_name} WHERE `app_id` = %d AND `event` = %s ", $post_input['id'], 'Yes'));
    $totalAttendedReplay = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table_db_name} WHERE `app_id` = %d AND `replay` = %s ", $post_input['id'], 'Yes'));
    $totalOrdered = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table_db_name} WHERE `app_id` = %d AND `trk2` = %s ", $post_input['id'], 'Yes'));
    $data = array(
        'leads'                     => $leads,
        'total_leads'               => $totalLeads,
        'total_query_leads'         => $totalQueryLeads,
        'total_attended_event'      => $totalAttendedEvent,
        'total_attended_replay'     => $totalAttendedReplay,
        'total_ordered'             => $totalOrdered,
        'number_of_pages'           => ceil($totalQueryLeads / $post_input['limit']),
    );

    wp_send_json_success($data);
}


add_action('wp_ajax_webinarignition_get_questions', 'webinarignition_get_questions');

function webinarignition_get_questions() {

    // check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );

    global $wpdb;

    // $post_input                 = filter_input_array(INPUT_POST);
    $post_input                 = $_POST;

    $post_input['id']           = isset( $post_input['id'] )        ? sanitize_text_field( $post_input['id'] ) : null;
    $post_input['search_for']   = isset( $post_input['search_for'] )        ? sanitize_text_field( $post_input['search_for'] ) : null;
    $post_input['limit']        = isset( $post_input['limit'] )        ? sanitize_text_field( $post_input['limit'] ) : null;
    $post_input['offset']       = isset( $post_input['offset'] )        ? sanitize_text_field( $post_input['offset'] ) : null;

    if ( !isset($post_input['id']) || !is_numeric($post_input['id'])) {
        wp_send_json_error( ['error' => 'Invalid Webinar ID'] );
    }

    $webinar_id = sanitize_text_field($post_input['id']);

    $webinar_data = WebinarignitionManager::get_webinar_data($webinar_id);

    $table_db_name = $wpdb->prefix . 'webinarignition_questions';

    //check if table exists
    $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_db_name ) );

  	if ( $wpdb->get_var( $query ) !== $table_db_name ) {
  		$table_db_name = $wpdb->prefix . 'webinarignition_questions_new';
  	}

    $totalQuestions = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table_db_name} WHERE `app_id` = %d", $post_input['id']));

    $sql = "
      SELECT *
      FROM {$table_db_name}
      WHERE app_id = %d ";

    if ( !empty($post_input['search_for']) ) {
        $sql .= " AND `email` LIKE %s ";
    }

    $sql .= "
      LIMIT %d
      OFFSET %d
    ";

    if ( !empty($post_input['search_for']) ) {
        $preparedSql = $wpdb->prepare( $sql,
            $post_input['id'],
            '%%' . $wpdb->esc_like( $post_input['search_for'] ) . '%%',
            $post_input['limit'],
            $post_input['offset']
        );

        $totalQueryQuestions = $wpdb->get_var(
            $wpdb->prepare(
                "
                  SELECT COUNT(*)
                  FROM {$table_db_name}
                  WHERE app_id = %d
                  AND `email` LIKE %s
                ",
                $post_input['id'],
                '%%' . $wpdb->esc_like( $post_input['search_for'] ) . '%%'
            )
        );
    } else {
        $preparedSql = $wpdb->prepare( $sql,
            $post_input['id'],
            $post_input['limit'],
            $post_input['offset']
        );
    }

    $questions              = $wpdb->get_results($preparedSql, OBJECT_K);
    $questions              = is_array($questions) ? array_reverse($questions) : $questions;
    $active_questions       = '';
    $current_user_id        = get_current_user_id();

    foreach ( $questions as $questionsActive ) {

                if( $questionsActive->status == 'live' ){

                    $deleteQuestionHtml =  empty( $post_input['is_support'] ) ? "<div class='questionBlockIcons qbi-remove' qaID='" . $questionsActive->ID . "'><i class='icon-remove icon-large' data-toggle='tooltip' data-placement='top' title='Delete question'></i></div>" : '';
                    $answerAttendeeHtml =  ( empty( $questionsActive->attr4 ) || $questionsActive->attr2 == $current_user_id ) ? "<div class='questionBlockIcons qbi-reply'><a class='answerAttendee' data-toggle='tooltip' data-placement='top' title='".__( "Respond to attendee question", "webinarignition")."'data-questionid=" . $questionsActive->ID . " data-attendee-email=" . $questionsActive->email . " data-attendee-name=" . $questionsActive->name . "><i class='icon-comments icon-large'></i></a></div>" : '';
                    $message            =  ( $questionsActive->attr4 == 'hold'  && !empty( $questionsActive->attr5 ) &&  $questionsActive->attr2 == $current_user_id ) ? __(  "You're answering this question...", "webinarignition") : $questionsActive->attr5 . ' '. __( 'is answering this question...', "webinarignition");
                    $questionOnHoldHtml = ( $questionsActive->attr4 == 'hold'  && !empty( $questionsActive->attr5 ) ) ? '<span class="questionOnHold green bold"> ' . $message. '</span>' : '';

                    $active_questions .=  "<!-- QUESTION BLOCK -->
                                        <div class='questionBlockWrapper questionBlockWrapperActive' qa_lead='" . $questionsActive->ID . "' id='QA-BLOCK-" . $questionsActive->ID . "' >
                                                <div class='questionBlockQuestion'>
                                                <span class='questionTimestamp'> " . $questionsActive->created  . " </span>
                                                    <p style='padding: 10px; background-color: #eee; width: 100%;border-radius: 7px;'>
                                                        <span class='questionBlockText' >". $questionsActive->question ."</span>
                                                        <span class='questionBlockAuthor' >
                                                            ".$questionsActive->name." - 
                                                            <span data-toggle='tooltip' data-placement='top' title='Search leads table' class='radius secondary label qa-lead-search'>".$questionsActive->email."
                                                            </span>
                                                        </span>
                                                    </p>
                                                            ".$questionOnHoldHtml."   
                                                </div>
                                                <div class='questionActions'>
                                                            ".$deleteQuestionHtml."
                                                             ".$answerAttendeeHtml."   
                                                        <br clear='left' />
                                                </div>
                                                <br clear='all' />
                                        </div>
                    <!-- END OF QUESTION BLOCK -->";

                }
    }


    $answered_questions       = '';

    $questionsDone = [];

    foreach ( $questions as $question ) {
        if( $question->status == 'done' ){
            $questionsDone[] = $question;
        }
    }

    foreach ( $questionsDone as $question ) {
        $is_support = !empty( $post_input['is_support'] );
        $questionDone = $question;

        ob_start();
        include WEBINARIGNITION_PATH . 'inc/lp/console/partials/answeredQuestion.php';
        $answered_questions .= ob_get_clean();
    }


    $totalQuestions         = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table_db_name} WHERE `app_id` = %d", $post_input['id']));
    $totalQueryQuestions    = isset($totalQueryQuestions) ? $totalQueryQuestions : $totalQuestions;
    $totalActiveQuestions   = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table_db_name} WHERE `app_id` = %d AND `status` = %s ", $post_input['id'], 'live'));
    $totalDoneQuestions     = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table_db_name} WHERE `app_id` = %d AND `status` = %s ", $post_input['id'], 'done'));

    $data = array(
        'active_questions'              => $active_questions,
        'answered_questions'            => $answered_questions,
        'total_questions'               => $totalQuestions,
        'total_query_questions'         => $totalQueryQuestions,
        'total_active_questions'        => $totalActiveQuestions,
        'total_done_questions'          => $totalDoneQuestions,
        'number_of_pages'               => ceil($totalQueryQuestions / $post_input['limit']),
    );

    wp_send_json_success($data);
}

add_action('wp_ajax_webinarignition_get_users_online', 'webinarignition_get_users_online');

function webinarignition_get_users_online() {

    // check_ajax_referer( 'webinarignition_ajax_nonce', 'security' );

    // $post_input                 = filter_input_array(INPUT_POST);
    $post_input                 = $_POST;
    $post_input['webinar_id']   = isset( $post_input['webinar_id'] )  ? sanitize_text_field( $post_input['webinar_id'] ) : null;
    $webinar_id = sanitize_text_field($post_input['webinar_id']);
    $webinar_type = sanitize_text_field($post_input['webinar_type']);

    global $wpdb;
    $table_db_name = $wpdb->prefix . "webinarignition_users_online";
    // Purge All Who Havent been updated in 1 minute...
    $currentTime = date("Y-m-d H:i:s");
    $currentTime = strtotime($currentTime);
    $minus5Minutes = date("Y-m-d H:i:s", strtotime('-10 seconds', $currentTime));
    $wpdb->query("DELETE FROM $table_db_name WHERE dt < '$minus5Minutes' ");
    $wpdb->query("DELETE FROM $table_db_name WHERE lead_id is null OR lead_id = 0 OR lead_id = '0' ");
    // Count All
    $attendees = $wpdb->get_results("SELECT lead_id FROM $table_db_name WHERE app_id = {$post_input['webinar_id']}", ARRAY_A);
    $count = count($attendees);

    if ($webinar_type == "live") {
        $table_db_name      = $wpdb->prefix . "webinarignition_leads";
        $leads              = $wpdb->get_results("SELECT ID, email FROM $table_db_name WHERE app_id = '$webinar_id' ", ARRAY_A);
    } else {
        $table_db_name      = $wpdb->prefix . "webinarignition_leads_evergreen";
        $leads              = $wpdb->get_results("SELECT ID, email FROM $table_db_name WHERE app_id = '$webinar_id' ", ARRAY_A);
    }

    wp_send_json(json_encode([
        'count' => $count,
        'visitors' => $attendees,
        'webinar_id' => $webinar_id,
        'webinar_type' => $webinar_type,
        'leads' => $leads,
    ]));
}

