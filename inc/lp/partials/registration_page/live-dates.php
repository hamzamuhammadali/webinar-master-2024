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
<div class="eventDate <?php echo $uid; ?>">
    <div class="dateIcon">
        <div class="dateMonth">
            <?php echo $localized_month; ?>
        </div>
        <div class="dateDay">
            <?php echo $webinarDateObject->format('d'); echo (substr(get_locale(), 0, 2) == 'en') ? '': '.'; ?>
        </div>

            <div class="dateDayWeek">
                <?php echo $localized_week_day; ?>
            </div>
    </div>

    <?php
    if (!$is_compact) {
        ?>
        <div class="dateInfo">
            <div class="dateHeadline"><?php echo $localized_date; ?></div>
            <div class="dateSubHeadline">
                <?php
                if ( $webinar_data->lp_webinar_subheadline ) {
                    echo $webinar_data->lp_webinar_subheadline;
                } else {
                    echo __('At', 'webinarignition') . ' '. date( $time_format, strtotime($webinar_data->webinar_start_time));
                }
                ?>
            </div>
        </div>
        <?php
    }
    ?>


    <br clear="left"/>
</div>
