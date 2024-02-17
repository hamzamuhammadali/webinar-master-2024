<?php
/**
 * @var $webinar_data
 * @var $uid
 * @var $liveEventMonth
 * @var $liveEventDateDigit
 * @var $autoDate_format
 * @var $autoTime
 * @var $is_compact
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="eventDateContainer <?php echo $uid; ?>">
    <span class="eventDate">
        <?php echo $localized_date; ?>
    </span>
    <br>
    <span class="eventTime">
        <?php webinarignition_get_time_inline($webinar_data, true); ?>
    </span>
    <span class="eventTimezone">
        (<?php webinarignition_get_timezone_inline($webinar_data, true); ?>)
    </span>
</div>
