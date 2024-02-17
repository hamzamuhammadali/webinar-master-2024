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

$uid = wp_unique_id( $prefix = 'tyTicketHost-' );
?>

<div id="<?php echo $uid ?>" class="tyTicketHost tyTicketHost-<?php echo $webinarId; ?> ticketSection ticketSectionNew">
    <!-- <i class="icon-bullhorn"></i>  -->
    <?php if ( $webinar_data->ty_ticket_host_option == "custom" ) {
        ?>
        <div class="tyTicketInfoContainer tyTicketInfoContainerHost">
            <div class="tyTicketInfoCopy">
                <b><?php webinarignition_display( $webinar_data->ty_ticket_host, __( "Host", "webinarignition") ); ?></b>
                <div class="tyTicketInfoNewHeadline">
                    <?php webinarignition_display( $webinar_data->ty_webinar_option_custom_host, __( "Your Name Here", "webinarignition") ); ?>
                </div>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="tyTicketInfoContainer tyTicketInfoContainerHost">
            <div class="tyTicketInfoCopy">
                <b><?php _e( 'Host', "webinarignition" ); ?>:</b>
                <div class="tyTicketInfoNewHeadline">
                    <?php webinarignition_display( $webinar_data->webinar_host, __( "Host name", "webinarignition") ); ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
