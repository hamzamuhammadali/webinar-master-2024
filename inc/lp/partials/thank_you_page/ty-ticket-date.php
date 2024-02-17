<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $data
 * @var $leadId
 * @var $instantTest
 * @var $autoDate_format
 * @var $autoTime
 * @var $liveEventMonth
 * @var $liveEventDateDigit
 */
?>

<div class="eventDate" <?php echo $instantTest; ?>>
    <div class="dateIcon">
        <div class="dateMonth">
        <?php echo webinarignition_get_localized_week_day($webinar_data, $lead); ?> 
        </div>
        <div class="dateDay">
                <?php  echo webinarignition_get_live_date_day($webinar_data, $lead);  echo (substr(get_locale(), 0, 2) == 'en') ? '': '.'; ?>
        </div>
        
        <div class="dateDayWeek">
        <?php echo webinarignition_get_locale_month($webinar_data, $lead); ?>
        </div>        
        
    </div>

    <div class="dateInfo">
        <div class="dateHeadline">
            <?php echo webinarignition_get_localized_date($webinar_data, $lead);?>
        </div>
        <div class="dateSubHeadline">
            <?php echo $webinar_data->lp_webinar_subheadline ?: __( 'At', "webinarignition"). ' ' . $autoTime ?>
        </div>
    </div>

    <br clear="left">
</div>
