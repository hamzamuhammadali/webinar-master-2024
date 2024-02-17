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

$uid = wp_unique_id( $prefix = 'tyTicketHost-' );if ( $webinar_data->ty_ticket_host_option == "custom" ) {
        webinarignition_display( $webinar_data->ty_webinar_option_custom_host,  __(  "Your Name Here", "webinarignition") );
    } else {
        webinarignition_display( $webinar_data->webinar_host, __( "Host name", "webinarignition") );
}
