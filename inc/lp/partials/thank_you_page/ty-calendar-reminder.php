<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data;
 * @var $leadId;
 */
$is_preview = get_query_var('webinarignition_preview');
?>

<?php $wi_calendarOption = ! empty( $webinar_data->ty_add_to_calendar_option ) ? $webinar_data->ty_add_to_calendar_option : 'enable'; ?>

<?php if ( $wi_calendarOption === 'enable' ): ?>
    <div class="ticketSection ticketCalendarArea wiTicketSection">
        <div class="optinHeadline12 wiOptinHeadline1"><?php webinarignition_display(
                $webinar_data->ty_calendar_headline,
                __( "Add To Your Calendar", "webinarignition")
            ); ?></div>

        <!-- AUTO CODE BLOCK AREA -->
        <?php if ( $webinar_data->webinar_date == "AUTO" ) { ?>
            <?php
            if ($is_preview) {
                ?>
                <!-- AUTO DATE -->
                <a href="#" class="small button wiButton wiButton-info wiButton-block"
                   target="_blank">
                    <i class="icon-google-plus"></i> <?php webinarignition_display(
                        $webinar_data->ty_calendar_google,
                        __( "Google Calendar", "webinarignition")
                    ); ?>
                </a>
                <a href="#" class="small button wiButton wiButton-info wiButton-block" target="_blank">
                    <i class="icon-calendar"></i> <?php webinarignition_display( $webinar_data->ty_calendar_ical, __('iCal / Outlook', 'webinarignition') ); ?>
                </a>
                <?php
            } else {
                $thankyou_URL = WebinarignitionManager::get_permalink($webinar_data,'thank_you');
	            $googleCalendarURL = add_query_arg(['googlecalendarA' => '', 'lid' => $leadId], $thankyou_URL);
	            $iCalendarURL = add_query_arg(['icsA' => '', 'lid' => $leadId], $thankyou_URL);
                ?>
                <!-- AUTO DATE -->
                <a href="<?php echo $googleCalendarURL; ?>" class="small button wiButton wiButton-info wiButton-block"
                   target="_blank">
                    <i class="icon-google-plus"></i> <?php webinarignition_display(
                        $webinar_data->ty_calendar_google,
                        __( "Google Calendar", "webinarignition")
                    ); ?>
                </a>
                <a href="<?php echo $iCalendarURL; ?>" class="small button wiButton wiButton-info wiButton-block" target="_blank">
                    <i class="icon-calendar"></i> <?php webinarignition_display( $webinar_data->ty_calendar_ical, __('iCal / Outlook', 'webinarignition') ); ?>
                </a>
                <?php
            }
            ?>
        <?php } else { ?>
            <?php
            if ($is_preview) {
                ?>
                <a href="#" class="small button wiButton wiButton-info wiButton-block">
                    <i class="icon-google-plus"></i> <?php webinarignition_display(
                        $webinar_data->ty_calendar_google,
                       __( "Google Calendar", "webinarignition")
                    ); ?>
                </a>
                <a href="#" class="small button wiButton wiButton-info wiButton-block">
                    <i class="icon-calendar"></i> <?php webinarignition_display( $webinar_data->ty_calendar_ical, __('iCal / Outlook', 'webinarignition') ); ?>
                </a>
                <?php
            } else {
                ?>
                <a href="?googlecalendar&lid=<?php echo $leadId; ?>" class="small button wiButton wiButton-info wiButton-block" target="_blank">
                    <i class="icon-google-plus"></i> <?php webinarignition_display(
                        $webinar_data->ty_calendar_google,
                        __( "Google Calendar", "webinarignition")
                    ); ?>
                </a>
                <a href="?ics&lid=<?php echo $leadId; ?>" class="small button wiButton wiButton-info wiButton-block" target="_blank">
                    <i class="icon-calendar"></i> <?php webinarignition_display( $webinar_data->ty_calendar_ical, __('iCal / Outlook', 'webinarignition') ); ?>
                </a>
                <?php
            }
            ?>
        <?php } ?>
        <!-- END OF AUTO CODE BLOCK -->

    </div>
<?php endif; ?>
