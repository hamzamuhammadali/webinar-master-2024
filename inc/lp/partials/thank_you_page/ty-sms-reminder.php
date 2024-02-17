<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data;
 * @var $leadID;
 * @var $lead;
 */
?>

<?php
if (!empty($webinar_data->txt_area) && $webinar_data->txt_area === "on") {
    ?>
    <div class="phoneReminder ticketSection wiTicketSection">
        <div id="phonePre">
            <div class="phoneReminderHeadline">
                <!-- <i class="icon-mobile-phone icon-4x ticketIcon"></i> -->
                <div class="optinHeadline1 wiOptinHeadline1"><?php webinarignition_display(
                        $webinar_data->txt_headline,
                        __( "Get A SMS Reminder", "webinarignition")
                    ); ?></div>
                <?php
                if (!empty($webinar_data->txt_subheadline)) {
                    ?><div class="optinHeadline2 wiOptinHeadline2" ><?php webinarignition_display(
                        isset( $webinar_data->txt_subheadline ) ? $webinar_data->txt_subheadline : "",
                        __( "Text Message 1 Hour Before Event...", "webinarignition")
                    ); ?></div><?php
                }
                ?>
            </div>

            <?php include(WEBINARIGNITION_PATH . "inc/lp/partials/thank_you_page/partials/sms-form.php"); ?>
        </div>

        <?php include(WEBINARIGNITION_PATH . "inc/lp/partials/thank_you_page/partials/sms-reveal.php"); ?>
    </div>
    <?php
}
?>
