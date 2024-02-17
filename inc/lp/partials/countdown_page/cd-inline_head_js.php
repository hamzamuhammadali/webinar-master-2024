<?php
defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $input_get
 * @var $leadinfo
 * @var $webinar_id
 * @var $is_preview
 */


if ($is_preview) {
    if ( $webinar_data->webinar_date == "AUTO" ) {

    } else {
        $webinar_data->webinar_switch = 'countdown';
    }
}
?><script type="text/javascript">
    jQuery(document).ready(function ( $ ) {
        var austDay = new Date();<?php
        // Get Date
        if ( $webinar_data->webinar_date == "AUTO" ) {
            $livedate = explode(" ", $leadinfo->date_picked_and_live);
            $expire = $livedate[0];
            // Check Format ie - OR /
            if ( strpos($expire, '-') ) {
                $exDate = explode("-", $expire);
            } else {
                $exDate = explode("/", $expire);
            }

            // $exDate = explode("-", $expire);
            $exYear = $exDate[0];
            $exMonth = (int)$exDate[1];
            $exDay = $exDate[2];
        } else {
            $expire = $webinar_data->webinar_date;
            // Check Format ie - OR /
            if ( strpos($expire, '-') ) {
                $exDate = explode("-", $expire);
            } else {
                $exDate = explode("/", $expire);
            }

            // $exDate = explode("-", $expire);
            $exYear = $exDate[2];
            $exMonth = (int)$exDate[0];
            $exDay = $exDate[1];
        }

        // Get Time
        if ( $webinar_data->webinar_date == "AUTO" ) {
            $time = $livedate[1];
        } else {
            $time = $webinar_data->webinar_start_time;
        }

        $time = date('H:i', strtotime($time));
        $exTime = explode(":", $time);
        $exHr = $exTime[0];
        $exMin = $exTime[1];
        $exSec = "00";


        if (empty($leadinfo->lead_timezone)) {
            $timezone_to_create = 'Asia/Beirut';
        } else {
            $timezone_to_create = $leadinfo->lead_timezone;
        }
        ?>

        <?php if( $webinar_data->webinar_date == "AUTO" ){ $tz = new DateTimeZone($timezone_to_create); ?>

        austDay = $.wi_countdown.UTCDate(<?php echo $tz->getOffset(new DateTime()) / 3600; ?>, <?php echo $exYear ? $exYear : '0'; ?>, <?php echo $exMonth ? $exMonth : '0'; ?>-1, <?php echo $exDay ? $exDay : '0'; ?>, <?php echo $exHr ? $exHr : '0'; ?>, <?php echo $exMin ? $exMin : '0'; ?>, <?php echo $exSec ? $exSec : '0'; ?>);
        <?php } else {
        $tz = new DateTimeZone($webinar_data->webinar_timezone);
        ?>
        austDay = $.wi_countdown.UTCDate(<?php echo $tz->getOffset(new DateTime())/3600; ?>, <?php echo $exYear ? $exYear : '0'; ?>, <?php echo $exMonth ? $exMonth : '0'; ?>-1, <?php echo $exDay ? $exDay : '0'; ?>, <?php echo $exHr ? $exHr : '0'; ?>, <?php echo $exMin ? $exMin : '0'; ?>, <?php echo $exSec ? $exSec : '0'; ?>);
        <?php } ?>

	    <?php if($is_preview): ?>
        austDay = +43200;
	    <?php endif; ?>

        $('#defaultCountdown').empty();

        $('#defaultCountdown').wi_countdown({
            until: austDay,
            onExpiry: webinarignition_expired_cd,
            alwaysExpire: true,
            labels: [ '<?php _e( 'Years', "webinarignition" ); ?>', '<?php webinarignition_display($webinar_data->cd_months, __( "Months", 'webinarignition') ); ?>', '<?php webinarignition_display($webinar_data->cd_weeks, __( "Weeks", 'webinarignition') ); ?>', '<?php webinarignition_display($webinar_data->cd_days, __( "Days", 'webinarignition') ); ?>', '<?php webinarignition_display($webinar_data->cd_hours, __( "Hours", 'webinarignition')  ); ?>', '<?php webinarignition_display($webinar_data->cd_minutes, __( "Minutes", 'webinarignition') ); ?>', '<?php webinarignition_display($webinar_data->cd_seconds, __( "Seconds", 'webinarignition') ); ?>'],
            labels1: [ '<?php _e( 'Year', "webinarignition" ); ?>' , '<?php webinarignition_display($webinar_data->cd_months, __( "Month", 'webinarignition') ); ?>', '<?php webinarignition_display($webinar_data->cd_weeks, __( "Week", 'webinarignition') ); ?>', '<?php webinarignition_display($webinar_data->cd_days, __( "Day", 'webinarignition') ); ?>', '<?php webinarignition_display($webinar_data->cd_hours, __( "Hour", 'webinarignition')  ); ?>', '<?php webinarignition_display($webinar_data->cd_minutes, __( "Minute", 'webinarignition')  ); ?>', '<?php webinarignition_display($webinar_data->cd_seconds, __( "Second", 'webinarignition') ); ?>']
        });

        function webinarignition_expired_cd() {

            <?php
	        $webinar_url = WebinarignitionManager::get_permalink($webinar_data,'webinar');
	        $webinar_url = add_query_arg('live', '', $webinar_url);

	        $leadId = sanitize_text_field($input_get['lid']);

	        $webinar_url = add_query_arg('lid', $leadId, $webinar_url);
	        if ( WebinarignitionManager::is_paid_webinar($webinar_data) ) {
		        $webinar_url = add_query_arg(md5( $webinar_data->paid_code ), '', $webinar_url);
	        }

	        $webinar_url = add_query_arg('watch_type', 'live', $webinar_url);
            ?>

            <?php if ( $webinar_data->webinar_date == "AUTO" ) {
                ?>
            window.location.href = "<?php echo add_query_arg('live', '', $webinar_url); ?>";
            <?php } else { ?>
            // reset link to show live webinar
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
                action: 'webinarignition_update_to_live',
                security:       '<?php echo wp_create_nonce("webinarignition_ajax_nonce"); ?>',
                id: "<?php echo $webinar_id; ?>"
            };
            $.post(ajaxurl, data, function (results) {
                <?php if( 'paid' == $webinar_data->paid_status ): ?>
                window.location.href = "<?php echo $webinar_data->paid_webinar_url; ?>";
                <?php else: ?>
                window.location.href = "<?php echo $webinar_url; ?>";
                <?php endif; ?>
            });
            <?php } ?>

        }



        <?php if ( $webinar_data->webinar_date != "AUTO" ) { ?>

                function checkMasterSwitchStatus(){

                            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
                            var data = {
                                action  : 'webinarignition_get_master_switch_status',
                                security: '<?php echo wp_create_nonce("webinarignition_ajax_nonce"); ?>',
                                id      : "<?php echo $webinar_id; ?>"
                            };

                            $.post(ajaxurl, data, function (results) {

                                if( results["webinar_switch_status"] != "countdown" ){
                                    webinarignition_expired_cd();
                                }

                            });
                }


                setInterval( checkMasterSwitchStatus, 3000);

        <?php } ?>

    });
</script>
