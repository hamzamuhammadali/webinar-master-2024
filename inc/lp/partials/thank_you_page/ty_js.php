<?php
/**
 * @var $lead
 * @var $leadId
 * @var $webinar_data
 */
defined( 'ABSPATH' ) || exit;

extract(webinarignition_get_ty_templates_vars($webinar_data));
?>
<script type="text/javascript">
    jQuery.expr.pseudos.parents = function (a, i, m) {
        return jQuery(a).parents(m[3]).length < 1;
    };


    (function($) {
        // AJAX FOR WP
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

        // TRACK +1 VIEW
        $getTrackingCookie = $.cookie('we-trk-ty-<?php echo $webinar_data->id; ?>');

        if ($getTrackingCookie != "tracked") {
            // No Cookie Set - Track View
            $.cookie('we-trk-ty-<?php echo $webinar_data->id; ?>', "tracked", {expires: 30});
            var data = {action: 'webinarignition_track_view', security:  '<?php echo wp_create_nonce("webinarignition_ajax_nonce"); ?>', id: "<?php echo $webinar_data->id; ?>", page: "ty"};
            $.post(ajaxurl, data, function (results) {
            });
        }
        // Track +1 Total
        var data = {action: 'webinarignition_track_view_total', security:  '<?php echo wp_create_nonce("webinarignition_ajax_nonce"); ?>', id: "<?php echo $webinar_data->id; ?>", page: "ty"};
        $.post(ajaxurl, data);


        // VIDEO FIXES:
        var wi_video_fix_w, wi_video_fix_h;
        if ($(window).width() < 825) {
            wi_video_fix_w = 290;
            wi_video_fix_h = 218;
        }
        else if ($(window).width() < 480) {
            //mobile size
            wi_video_fix_w = 278;
            wi_video_fix_h = 209;
        } else {
            wi_video_fix_w = 410;
            wi_video_fix_h = 231;
        }
        $('.ctaArea').find("embed, object").height(wi_video_fix_h).width(wi_video_fix_w);


        <?php
        if (webinarignition_is_auto($webinar_data)) {
            $expire = explode(' ', $lead->date_picked_and_live)[0];
            $time = explode(' ',$lead->date_picked_and_live)[1];
            $autoTZ = webinarignition_get_tzOffset($lead->lead_timezone);
        } else {
            $expire = $webinar_data->webinar_date;
            $webinarDate = strpos($expire, '-') ? explode("-", $expire) : explode("/", $expire);

            // Live webinar date is stored as mm-dd--yyyy, so format it to yyyy-mm-dd, so that we don't have to duplicate the logic below.
            $expire = "{$webinarDate[2]}-{$webinarDate[0]}-{$webinarDate[1]}";
            $time = date('H:i', strtotime($webinar_data->webinar_start_time));
        }

        // Get Date and Time
        $exDate = strpos($expire, '-') ? explode("-", $expire) : explode("/", $expire);
        $exTime = explode(":", $time);
        ?>

        var exYear = '<?=  $exDate[0] ?: '0' ?>';
        var exMonth = '<?= $exDate[1] ? $exDate[1] - 1 : '0' ?>';
        var exDay = '<?= $exDate[2] ?: '0'?>';
        var exHr = '<?= $exTime[0] ?: '0' ?>';
        var exMin = '<?php echo str_replace([' ', 'AM', 'PM'], '', $exTime[1]) ?: '0' ?>';
        var exSec = '0';
        var tzOffset = '<?= $autoTZ ?>';

        $('#defaultCountdown').wi_countdown({
            until: $.wi_countdown.UTCDate(tzOffset, exYear, exMonth, exDay, exHr, exMin, exSec),
            onExpiry: webinarignition_expired_cd,
            compact: true,
            alwaysExpire: true,
            compactLabels: ['<?php webinarignition_display($webinar_data->tycd_years, "y"); ?>', '<?php webinarignition_display($webinar_data->tycd_months, "m"); ?>', '<?php webinarignition_display($webinar_data->tycd_weeks, "w"); ?>', '<?php webinarignition_display($webinar_data->tycd_days, "d"); ?>'] // The compact texts for the counters
        });

        function webinarignition_expired_cd() {
            $(".ticketCDAreaBTN").text('<?php webinarignition_display($webinar_data->tycd_progress, __( "Webinar Is In Progress", "webinarignition") ); ?>');
            $("#defaultCountdown").hide();
            $("#webinarBTNNN").removeClass("disabled").removeClass("alert").addClass("success");

            setTimeout(function(){
                window.location.href = $(".ticketCDAreaBTN").attr('href');
            }, 1000 );
        }

        // Save Phone && Reveal Text
        $('#storePhone').on( 'click', function () {

            // Lead ID
            $ID = $("#leadID").val();
            // Phone NUmber
            $PHONE = $("#optPhone").val();

            // Post & Save & Reveal
            <?php if ( $webinar_data->webinar_date == "AUTO" ) { ?>
            var data = {
                action: 'webinarignition_store_phone_auto',
                security:  '<?php echo wp_create_nonce("webinarignition_ajax_nonce"); ?>',
                id: "<?php echo $leadId ?>",
                phone: "" + $PHONE + ""
            };
            <?php } else { ?>
            var data = {action: 'webinarignition_store_phone', security:  '<?php echo wp_create_nonce("webinarignition_ajax_nonce"); ?>', id: '<?php echo $leadId ?>', phone: "" + $PHONE + ""};
            <?php } ?>
            $.post(ajaxurl, data, function (results) {
                $("#phonePre").hide();
                $("#phoneReveal").show();
            });

            return false;
        });


    })(jQuery);

</script>
