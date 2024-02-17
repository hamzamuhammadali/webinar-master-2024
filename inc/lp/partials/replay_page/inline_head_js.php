<?php
defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $input_get
 * @var $leadinfo
 * @var $webinar_id
 */
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        <?php
        if ( $webinar_data->webinar_date == "AUTO" ) {

            $autoTZ_org = trim($leadinfo->lead_timezone);

            if( empty($autoTZ_org) ) {
                $autoTZ_org = $webinar_data->webinar_timezone;
                if( empty($autoTZ_org) ) {
                    $autoTZ_org = wp_timezone_string();
                }
            }

            // Get Number Of Days
	        $addDays = 3;
            $webinar_auto_replay = trim($webinar_data->auto_replay);
            if( $webinar_auto_replay != '' ) {
	            $addDays = absint($webinar_auto_replay);
            }

            // Get Start Date (live date)
	        $liveDate = current_time('mysql');
            if( isset($leadinfo->date_picked_and_live) && empty($leadinfo->date_picked_and_live) ) {
	            $liveDate = $leadinfo->date_picked_and_live;
            }

            $liveDateTS = strtotime($liveDate);

            $expire   = date( 'Y-m-d', strtotime( "+{$addDays} day", $liveDateTS));
            $dtz      = new DateTimeZone( $autoTZ_org );

            $time_in_sofia = new DateTime( 'now', $dtz );
            $autoTZ        = $dtz->getOffset( $time_in_sofia ) / 3600;
            $autoTZ        = ( $autoTZ < 0 ? $autoTZ : "+" . $autoTZ );
        } else {
            // Get Date
            $expire = $webinar_data->replay_cd_date;
        }

        if (!empty($expire)) {
            // Check Format ie - OR /
            if ( strpos( $expire, '-' ) ) {
                $exDate = explode( "-", $expire );
            } else {
                $exDate = explode( "/", $expire );
            }
        }

        if (!empty($exDate) && 3 === count($exDate)) {
            // $exDate = explode("-", $expire);
            if ( $webinar_data->webinar_date == "AUTO" ) {
                $exYear = $exDate[0];
                $exMonth = $exDate[1];
                $exDay = $exDate[2];
                ?>
                austDay = jQuery.wi_countdown.UTCDate(<?php echo $autoTZ; ?>, <?php echo $exYear ? $exYear : '0'; ?>, <?php echo $exMonth ? $exMonth : '0'; ?> -1, <?php echo $exDay ? $exDay : '0'; ?>);

                <?php
            } else {
                $exYear = $exDate[2];
                $exMonth = $exDate[0];
                $exDay = $exDate[1];
                // Get Time
                $expire_time = $webinar_data->replay_cd_time;
                if ( $expire_time == "" ) {
                    $expire_time_hour   = "00";
                    $expire_time_minute = "00";
                } else {
                    $expire_time        = explode( ":", $expire_time );
                    $expire_time_hour   = $expire_time[0];
                    $expire_time_minute = $expire_time[1];
                }
                $tz = new DateTimeZone( $webinar_data->webinar_timezone );
                ?>
                austDay = jQuery.wi_countdown.UTCDate(<?php echo $tz->getOffset( new DateTime() ) / 3600; ?>, <?php echo $exYear ? $exYear : '0'; ?>, <?php echo $exMonth ? $exMonth : '0'; ?> -1, <?php echo $exDay ? $exDay : '0'; ?>, <?php echo $expire_time_hour; ?>, <?php echo $expire_time_minute; ?>, 00);
                <?php
            }
        }
        ?>

        <?php if (!empty($exDate) && 3 === count($exDate) && !empty($webinar_data->replay_optional) && $webinar_data->replay_optional != "hide") : ?>
            <?php if ( ! isset( $webinar_data->auto_replay ) || $webinar_data->auto_replay != 0  ) : ?>
            jQuery('#cdExpire').wi_countdown({
                until: austDay,
                onExpiry: webinarignition_closeWebinar,
                alwaysExpire: true,
                    labels: [ '<?php _e( 'Years', "webinarignition" ); ?>', '<?php webinarignition_display($webinar_data->cd_months, __( "Months", 'webinarignition') ); ?>', '<?php webinarignition_display($webinar_data->cd_weeks, __( "Weeks", 'webinarignition') ); ?>', '<?php webinarignition_display($webinar_data->cd_days, __( "Days", 'webinarignition') ); ?>', '<?php webinarignition_display($webinar_data->cd_hours, "Hours" ); ?>', '<?php webinarignition_display($webinar_data->cd_minutes, __( "Minutes", 'webinarignition') ); ?>', '<?php webinarignition_display($webinar_data->cd_seconds, __( "Seconds", 'webinarignition') ); ?>'],
                    labels1: [ '<?php _e( 'Year', "webinarignition" ); ?>' , '<?php webinarignition_display($webinar_data->cd_months, __( "Month", 'webinarignition') ); ?>', '<?php webinarignition_display($webinar_data->cd_weeks, __( "Week", 'webinarignition') ); ?>', '<?php webinarignition_display($webinar_data->cd_days, __( "Day", 'webinarignition') ); ?>', '<?php webinarignition_display($webinar_data->cd_hours, "Hour" ); ?>', '<?php webinarignition_display($webinar_data->cd_minutes, __( "Minute", 'webinarignition')  ); ?>', '<?php webinarignition_display($webinar_data->cd_seconds, __( "Second", 'webinarignition') ); ?>']
            });
            <?php endif; ?>
        <?php endif; ?>


        // AJAX FOR WP
        var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';

        // TRACK +1 VIEW
        <?php $input_cookie = filter_input_array( INPUT_COOKIE ); ?>

        <?php if(is_array($input_cookie) && count( $input_cookie )):  ?>
            <?php if( ! empty( $input_cookie[ 'we-trk-replay' . $webinar_id ] )):  ?>
                $getTrackingCookie = "<?php echo $input_cookie[ 'we-trk-replay' . $webinar_id ]; ?>";

                if ($getTrackingCookie != "tracked") {
                    // No Cookie Set - Track View
                    $.cookie('we-trk-replay-<?php echo $webinar_id; ?>', "tracked", {expires: 30});
                    var data = {
                        action: 'webinarignition_track_view',
                        security: '<?php echo wp_create_nonce( "webinarignition_ajax_nonce" ); ?>',
                        id: "<?php echo $webinar_id; ?>",
                        page: "replay"
                    };
                    jQuery.post(ajaxurl, data, function (results) {
                    });
                } else {
                    // Already tracked...
                }
            <?php endif; ?>
        <?php endif; ?>


        // Track +1 Total
        var data = {
            action: 'webinarignition_track_view_total',
            security: '<?php echo wp_create_nonce( "webinarignition_ajax_nonce" ); ?>',
            id: "<?php echo $webinar_id; ?>",
            page: "replay"
        };
        jQuery.post(ajaxurl, data, function (results) {
        });

        // VIDEO FIXES:
        jQuery(".ctaArea").find("embed, object").height(518).width(920);

        // ASK QUESTION
        jQuery('#askQuestion').on('click', function () {
            function validateEmail(email) {
                var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(email);
            }

            if (!validateEmail($Email)) {
                jQuery('#optEmail').addClass("errorField");
                return false;
            }

            $question = jQuery("#question").val();

            if ($question == "") {
                // No Question
                jQuery("#question").addClass("errorField");
            } else {

                // Submit Question...
                jQuery("#question").removeClass("errorField");

                // AJAX Question
                var data = {
                    action: 'webinarignition_submit_question',
                    security: '<?php echo wp_create_nonce( "webinarignition_ajax_nonce" ); ?>',
                    id: "<?php echo $webinar_id; ?>",
                    question: "" + $question + ""
                };
                jQuery.post(ajaxurl, data, function (results) {

                    jQuery("#askQArea").hide();
                    jQuery("#askQThankyou").show();

                    setTimeout(function () {
                        jQuery("#askQArea").show();
                        jQuery("#askQThankyou").hide();
                        jQuery("#question").val("");
                    }, 15000);

                });

            }

            return false;
        });

    });

    // Close WEBINAR
    function webinarignition_closeWebinar() {
        // hide webinar
        var closeWebinarIntervalID = setInterval(function () {

            // wrap in timeout otherwise audio still plays
            jQuery(".webinarBlock").hide();
            jQuery(".webinarExtraBlock").hide();
            jQuery(".webinarBlockRight").hide();
            if (window.myPlayer) {
                window.myPlayer.dispose();
                clearInterval(closeWebinarIntervalID);
            }

            // Replace any video area here..
            jQuery(".ctaArea").html("closed");

            // show closed area
            jQuery("#closed").show();
        }, 10);

    }

    // CTA Timed action for Iframe (videos not handled by video.js)
    <?php if ( $webinar_data->webinar_date === "AUTO" ) : ?>
        <?php
            $webinar_source_toggle = !empty($webinar_data->webinar_source_toggle) ? $webinar_data->webinar_source_toggle : '';
            $is_webinar_iframe_source = !empty($webinar_data->webinar_iframe_source) ? $webinar_data->webinar_iframe_source : false;
        if (
                $webinar_data->auto_action === "time" &&
                ! empty( $is_webinar_iframe_source ) &&
                'iframe' === $webinar_source_toggle

        ) {
            if (empty($webinar_data->auto_action_time)) {
                $delay = 10;
            } else {
                $auto_action_time_array = explode(':', $webinar_data->auto_action_time);
                $delay = 10;

                if (!empty($auto_action_time_array[0])) {
                    $delay = $delay + ($auto_action_time_array[0] * 60000);
                }

                if (!empty($auto_action_time_array[1])) {
                    $delay = $delay + ($auto_action_time_array[1] * 1000);
                }
            }

        $start_delay = $delay;

        if (empty($webinar_data->auto_action_time_end)) {
            $delay = 0;
        } else {
            $auto_action_time_array = explode(':', $webinar_data->auto_action_time_end);
            $delay = 0;

            if (!empty($auto_action_time_array[0])) {
                $delay = $delay + ($auto_action_time_array[0] * 60000);
            }

            if (!empty($auto_action_time_array[1])) {
                $delay = $delay + ($auto_action_time_array[1] * 1000);
            }
        }

        $end_delay = $delay;
        ?>
        setTimeout(timedAction, <?php echo $start_delay; ?>);
        <?php
        if ($end_delay > $start_delay) {
        ?>
        setTimeout(timedEndAction, <?php echo $end_delay; ?>);
        <?php
        }

        if (WebinarignitionPowerups::is_multiple_cta_enabled($webinar_data)) {
        if (!empty($webinar_data->additional_autoactions)) {
            $additional_autoactions = maybe_unserialize($webinar_data->additional_autoactions);
        } else {
            $additional_autoactions = array();
        }

        foreach ($additional_autoactions as $index => $additional_autoaction) {
        if (!empty($additional_autoaction['auto_action_time'])) {
        $auto_action_time_array = explode(':', $additional_autoaction['auto_action_time']);
        $delay = 10;

        if (!empty($auto_action_time_array[0])) {
            $delay = $delay + ($auto_action_time_array[0] * 60000);
        }

        if (!empty($auto_action_time_array[1])) {
            $delay = $delay + ($auto_action_time_array[1] * 1000);
        }

        $start_delay = $delay;

        if (empty($additional_autoaction['auto_action_time_end'])) {
            $delay = 0;
        } else {
            $auto_action_time_array = explode(':', $additional_autoaction['auto_action_time_end']);
            $delay = 0;

            if (!empty($auto_action_time_array[0])) {
                $delay = $delay + ($auto_action_time_array[0] * 60000);
            }

            if (!empty($auto_action_time_array[1])) {
                $delay = $delay + ($auto_action_time_array[1] * 1000);
            }
        }

        $end_delay = $delay;
        $cta_index = 'additional-' . $index;
        ?>

    setTimeout(function() {
        var orderBTN = jQuery("#orderBTN");
        orderBTN.hide();
        var orderBTNCopyHtml = jQuery('#orderBTNCopy_<?php echo $index ?>').html();
        var orderBTNAreaHtml = jQuery('#orderBTNArea_<?php echo $index ?>').html();

        setTimeout(function() {
            jQuery('#orderBTNCopy').html(orderBTNCopyHtml);
            jQuery('#orderBTNArea').html(orderBTNAreaHtml);
            var pre_hurrytimer = jQuery('#orderBTNCopy').find('.pre-hurrytimer-campaign');

            if (pre_hurrytimer.length) {
                pre_hurrytimer.each(function() {
                    jQuery( this ).addClass('hurrytimer-campaign');
                });
            }

            orderBTN.show();
            orderBTN.data('cta-index', 'additional-<?php  echo $index; ?>');
        }, 1000);
    }, <?php echo $start_delay; ?>);
    <?php
    if ($end_delay > $start_delay) {
    ?>
    setTimeout(function() {
        var orderBTN = jQuery("#orderBTN");
        var ctaIndex = orderBTN.data('cta-index');

        if ('additional-<?php echo $index; ?>'  == ctaIndex) {
            orderBTN.hide();
        }
    }, <?php echo $end_delay; ?>);

    <?php
    }
    }
    }
    }

        ?>
    function timedAction() {
        var orderBTN = jQuery("#orderBTN");
        orderBTN.show();

        var pre_hurrytimer = jQuery('#orderBTNCopy').find('.pre-hurrytimer-campaign');

        if (pre_hurrytimer.length) {
            pre_hurrytimer.each(function() {
                jQuery( this ).addClass('hurrytimer-campaign');
            });
        }

        orderBTN.data('cta-index', 'default');
    }

    function timedEndAction() {
        var orderBTN = jQuery("#orderBTN");
        var ctaIndex = orderBTN.data('cta-index');

        if ('default' == ctaIndex) {
            orderBTN.hide();
        }
    }
    <?php
    }
    ?>



    <?php else : ?>
        // Only For Live Webinars
        <?php if ( $webinar_data->replay_order_time == "" ) : ?>
            // NO TIME SET - SHOW BUTTON
            jQuery("#orderBTN").show();
            jQuery('.webinarVideoCTA').addClass('webinarVideoCTAActive');
        <?php else : ?>
            // TIME IS SET ::
            setTimeout('timedAction()', <?php echo ( $webinar_data->replay_order_time == "" ) ? '50' : $webinar_data->replay_order_time . "000"; ?>);

            function timedAction() {
                jQuery("#orderBTN").show();
                jQuery('.webinarVideoCTA').addClass('webinarVideoCTAActive');
            }
        <?php endif; ?>
    <?php endif; ?>

    <?php
    $is_auto_login_enabled = get_option( 'webinarignition_registration_auto_login', 1 ) == 1;

    if( ($is_auto_login_enabled && is_user_logged_in() || !$is_auto_login_enabled) && get_query_var( 'webinarignition_page' ) != 'preview-replay'):  ?>
        // Track Event Attending
        $checkCookie = jQuery.cookie('we-trk-<?php echo $webinar_id; ?>');
        // Post & Track
        <?php if (!empty($input_get['lid'])): ?>
        jQuery.post('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
            action: 'webinarignition_update_view_status',
            security: '<?php echo wp_create_nonce( "webinarignition_ajax_nonce" ); ?>',
            id: "<?php echo $webinar_id; ?>",
            lead_id: "<?php echo $input_get['lid']; ?>"
        });
        <?php endif; ?>

    <?php endif; ?>

</script>