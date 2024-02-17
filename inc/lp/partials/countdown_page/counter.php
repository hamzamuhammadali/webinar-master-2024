<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $leadinfo
 */
?>

<div class="countdownArea">
    <center>
        <div id="defaultCountdown">
            <span class="countdown_row countdown_show3">
                <span class="countdown_section">
                    <span class="countdown_amount">11</span>
                    <br><?php _e( 'Hours', "webinarignition" ); ?>
                </span>
                <span class="countdown_section">
                    <span class="countdown_amount">57</span>
                    <br><?php _e( 'Minutes', "webinarignition" ); ?>
                </span>
                <span class="countdown_section">
                    <span class="countdown_amount">54</span>
                    <br><?php _e( 'Seconds', "webinarignition" ); ?>
                </span>
            </span>
        </div>
    </center>
    <br clear="left"/>
</div>



<br clear="left"/>

<?php if ($webinar_data->webinar_date == "AUTO") {
    
        // Display Date ::
        // Get Date
        $date_format        = !empty($webinar_data->date_format ) ? $webinar_data->date_format  : ( ($webinar_data->webinar_date == "AUTO") ? 'l, F j, Y' : get_option( "date_format") );
        if( !empty($webinar_data->time_format ) && ( $webinar_data->time_format == '12hour' || $webinar_data->time_format == '24hour'  ) ){ //old formats
            $webinar_data->time_format = get_option( "time_format", 'H:i' );
        }          
        $time_format        = $webinar_data->time_format;

        if( !empty($leadinfo) ) {
	        $autoDate_info = explode( " ", $leadinfo->date_picked_and_live );
	        $autoDate      = $autoDate_info[0];

	        $localized_date = webinarignition_get_localized_date( $webinar_data, $leadinfo );
	        $timeonly        = ( empty( $webinar_data->display_tz ) || ( ! empty( $webinar_data->display_tz ) && ( $webinar_data->display_tz == 'yes' ) ) ) ? false : true;
	        $autoTime        = webinarignition_get_time_tz( $autoDate_info[1], $time_format, false, false, $timeonly );

	        $autoDate2 = $localized_date . " - " . $autoTime;

	        echo "<div class='cd_auto_date' >" . $autoDate2 . "</div>";
        }
        
} ?>
