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

$uid = wp_unique_id( $prefix = 'tyHeadlineContainer-' );
?>
<div id="<?php echo $uid ?>" class="tyHeadlineContainer tyHeadlineContainer-<?php echo $webinarId; ?>">
    <div class="tyHeadlineCopy">
        <div class="optinHeadline1 wiOptinHeadline1">
            <?php webinarignition_display( $webinar_data->ty_ticket_headline, __( "Congrats - You Are All Signed Up!", "webinarignition") ); ?>
        </div>

        <div class="optinHeadline2 wiOptinHeadline2">
            <?php webinarignition_display( $webinar_data->ty_ticket_subheadline, __( "Below is all the information you need for the webinar...", "webinarignition")  ) ?>
        </div>
    </div>
</div>
