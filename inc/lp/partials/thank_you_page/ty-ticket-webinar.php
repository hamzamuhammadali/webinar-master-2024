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

$uid = wp_unique_id( $prefix = 'tyTicketWebinar-' );
?>

<div id="<?php echo $uid ?>" class="tyTicketWebinar tyTicketWebinar-<?php echo $webinarId; ?> ticketSection ticketSectionNew">
    <!-- <i class="icon-desktop"></i> -->
    <?php
    if ( $webinar_data->ty_ticket_webinar_option == "custom" ) {
        ?>
        <div class="tyTicketInfoContainer tyTicketInfoContainerWebinar">
            <div class="tyTicketInfoCopy">
                <b><?php webinarignition_display( $webinar_data->ty_ticket_webinar, __( "Webinar", "webinarignition") ); ?></b>
                <div class="tyTicketInfoNewHeadline">
                    <?php webinarignition_display( $webinar_data->ty_webinar_option_custom_title, __( "Webinar Event Title" , "webinarignition") ); ?>
                </div>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="tyTicketInfoContainer tyTicketInfoContainerWebinar">
            <div class="tyTicketInfoCopy">
                <b><?php _e( 'Webinar:', "webinarignition" ); ?></b>
                <div class="tyTicketInfoNewHeadline">
                    <?php webinarignition_display( $webinar_data->webinar_desc, __( "Webinar Event Title" , "webinarignition") ); ?>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
</div>
