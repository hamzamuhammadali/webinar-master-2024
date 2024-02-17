<?php
/**
 * @var $webinar_data
 * @var $uid
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="eventDate fixed-type <?php echo $uid; ?>">
    <?php
    $dateTime     = webinarignition_make_delayed_date( $webinar_data );
    $tz_abbr      = $dateTime->format( 'T' );
    $date_format  = !empty($webinar_data->date_format ) ? $webinar_data->date_format  :  'l, F j, Y';
    $delayed_date = webinarignition_get_translated_date( $dateTime->format( 'm-d-Y' ), 'm-d-Y', $date_format  );
    ?>
    <img src="<?php echo WEBINARIGNITION_URL . 'images/date_crystal.png'; ?>"/>
    <span style="font-size: 18px"><?php echo $delayed_date; ?></span>
    <img src="<?php echo WEBINARIGNITION_URL . 'images/clock.png'; ?>"/>
    <span style="font-size: 18px">
        <?php
        echo $webinar_data->auto_time_delayed, ' ';
        if ( $webinar_data->delayed_timezone_type !== 'user_specific' ) {
            echo $tz_abbr;
        } else {
            ?>
            <div class="user_specific_timezone_name" style="display: inline-block;font-size: 10px">
                <?php
                if ( $webinar_data->auto_timezone_user_specific_name ) {
                    echo $webinar_data->auto_timezone_user_specific_name;
                } else {
                    ?>
                    <?php _e( 'YOUR<br/>TIMEZONE', "webinarignition" ); ?>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>
    </span>

    <input type="hidden" id="webinar_start_date" value="<?php echo $dateTime->format( 'Y-m-d' ); ?>"/>
    <input type="hidden" id="webinar_start_time" value="<?php echo $webinar_data->auto_time_delayed; ?>"/>
    <input type="hidden" id="timezone_user"
           value="<?php if ( $webinar_data->delayed_timezone_type !== 'user_specific' ) {
               echo $webinar_data->auto_timezone_delayed;
           } ?>">
    <input type="hidden" id="today_date" value="<?php echo date( 'Y-m-d' ); ?>">
    <br clear="left"/>
</div>
