<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinarId
 * @var $webinar_data
 * @var $data
 * @var $leadId
 * @var $instantTest
 * @var $autoDate_format
 * @var $autoTime
 * @var $liveEventMonth
 * @var $liveEventDateDigit
 */

if ( $webinar_data->ty_ticket_webinar_option == "custom" ) {
    ?><?php webinarignition_display( $webinar_data->ty_webinar_option_custom_title, __( "Webinar Event Title", "webinarignition")); ?><?php
} else {
    ?><?php webinarignition_display( $webinar_data->webinar_desc, __( "Webinar Event Title", "webinarignition") ); ?><?php
}
