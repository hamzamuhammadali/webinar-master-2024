<?php
defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_id
 * @var $webinar_data
 * @var $webinarignition_page
 * @var $pluginName
 * @var $leadId
 * @var $lead
 */

$webinar_data           = !empty( $webinar_data ) ? $webinar_data : get_query_var( 'webinar_data' );
$webinarignition_page   = !empty( $webinarignition_page ) ? $webinarignition_page : get_query_var( 'webinarignition_page' );
$webinar_type = $webinar_data->webinar_date == "AUTO" ? 'evergreen' : 'live';
$tracking_tags_settings = isset($webinar_data->tracking_tags) ? $webinar_data->tracking_tags : [];
$lead_id = !empty($leadinfo->ID) ? $leadinfo->ID : '';

$is_replay_page = $webinarignition_page == 'replay_custom' ||  $webinarignition_page == 'preview-replay' || $webinarignition_page == 'replay_page';

if ('evergreen' === $webinar_type) {
    if ( $webinar_data->auto_action === "time" && (! empty( $webinar_data->webinar_iframe_source ) || !empty( $webinar_data->auto_video_url ) ) ) {
        $cta_position_default = 'outer';

        $cta_position_allowed = 'outer';
        $cta_position_overlay_allowed = 'overlay';

        $additional_autoactions = [];

        $additional_autoactions_js = [];


        if (WebinarignitionPowerups::is_multiple_cta_enabled($webinar_data) && !empty($webinar_data->additional_autoactions)) {
            $additional_autoactions = maybe_unserialize($webinar_data->additional_autoactions);
        }

        if (
            !empty($webinar_data->auto_action_time)
            // && !empty($webinar_data->auto_action_time_end)
            && (
                !empty($webinar_data->auto_action_copy)
                || (!empty($webinar_data->auto_action_btn_copy) && !empty($webinar_data->auto_action_url))
            )
        ) {
            $webinar_main_auto_action = [
                'auto_action_time' => $webinar_data->auto_action_time,
                'auto_action_time_end' => '',
                'auto_action_copy' => '',
                'auto_action_btn_copy' => '',
                'auto_action_url' => '',
                'replay_order_color' => '#6BBA40',
            ];

            if (!empty($webinar_data->auto_action_time_end)) {
                $webinar_main_auto_action['auto_action_time_end'] = $webinar_data->auto_action_time_end;
            }

            if (!empty($webinar_data->replay_order_color)) {
                $webinar_main_auto_action['replay_order_color'] = $webinar_data->replay_order_color;
            }

            if (!empty($webinar_data->auto_action_copy)) {
                $webinar_main_auto_action['auto_action_copy'] = $webinar_data->auto_action_copy;
            }

            if (!empty($webinar_data->auto_action_btn_copy) && !empty($webinar_data->auto_action_url)) {
                $webinar_main_auto_action['auto_action_btn_copy'] = $webinar_data->auto_action_btn_copy;
                $webinar_main_auto_action['auto_action_url'] = $webinar_data->auto_action_url;
            }

            if (!empty($webinar_data->cta_position) ) {
                $cta_position_default = $webinar_data->cta_position;
            }

            if ($cta_position_default === $cta_position_allowed) {
                $webinar_main_auto_action['cta_position'] = 'outer';
            } else {
                $webinar_main_auto_action['cta_position'] = 'overlay';
            }

            if(is_array($additional_autoactions) && !empty($additional_autoactions)) {
	            $additional_autoactions = array_merge([$webinar_main_auto_action], $additional_autoactions);
            } else {
	            $additional_autoactions[] = $webinar_main_auto_action;
            }

        }

        ksort($additional_autoactions);

        foreach ($additional_autoactions as $index => $additional_autoaction) {
            $cta_position = $cta_position_default;

            if (!empty($additional_autoaction['cta_position'])) {
                $cta_position = $additional_autoaction['cta_position'];
            }

            if (!empty($additional_autoaction['auto_action_time'])) {
                $auto_action_time_array = explode(':', $additional_autoaction['auto_action_time']);
                $delay = 10;

                if (!empty($auto_action_time_array[0])) $delay = $delay + ($auto_action_time_array[0] * 60000);
                if (!empty($auto_action_time_array[1])) $delay = $delay + ($auto_action_time_array[1] * 1000);
                $start_delay = $delay;
                if ($start_delay > 10) $start_delay = $start_delay + 1000;

                if (empty($additional_autoaction['auto_action_time_end'])) {
                    $delay = 0;
                } else {
                    $auto_action_time_array = explode(':', $additional_autoaction['auto_action_time_end']);
                    $delay = 0;
                    if (!empty($auto_action_time_array[0])) $delay = $delay + ($auto_action_time_array[0] * 60000);
                    if (!empty($auto_action_time_array[1])) $delay = $delay + ($auto_action_time_array[1] * 1000);
                }

                $end_delay = $delay;
                if ($end_delay > 0) $end_delay = $end_delay + 1000;
                $cta_index = 'additional-' . $index;

                $additional_autoactions_js[] = [
                    'index' => $index,
                    'end_delay' => $end_delay,
                    'start_delay' => $start_delay,
                    'is_videojs' => webinarignition_should_use_videojs( $webinar_data ),
                ];
                ?>
            <?php }
        }
    }
}

?><script>
    var auto_video_length = <?php echo isset($webinar_data->auto_video_length) ? absint($webinar_data->auto_video_length) : 0; ?>;
    var globalOffset = 0;
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    <?php if ('evergreen' === $webinar_type && $webinar_data->auto_action === "time" && (! empty( $webinar_data->webinar_iframe_source )  || !empty( $webinar_data->auto_video_url ) )  ) { ?>
        var additional_autoactions = <?php echo json_encode($additional_autoactions_js, JSON_HEX_TAG); ?>;
    <?php } else { ?>
        var additional_autoactions = null;
    <?php } ?>
    <?php

    ob_start();
    // Prepare time tags

    if ( WebinarignitionPowerups::is_multiple_cta_enabled($webinar_data) && !empty($lead_id) && !empty($tracking_tags_settings) && !$is_replay_page) {
        $tracking_tags_timeouts = [];

        foreach ($tracking_tags_settings as $tracking_tag) {
            if (empty($tracking_tag['time']) || empty($tracking_tag['name'])) continue;
            $time = $tracking_tag['time'];
            $name = $tracking_tag['name'];
            $slug = empty($tracking_tag['slug']) ? '' : $tracking_tag['slug'];
            $pixel = empty($tracking_tag['pixel']) ? '' : $tracking_tag['pixel'];

            $timedActionArray = explode(':', $time);
            $minutes = $timedActionArray[0];

            if (!is_numeric($minutes))  $minutes = 0;
            else                        $minutes = (int) $minutes;

            $seconds = 0;

            if (!empty($timedActionArray[1])) {
                $seconds = $timedActionArray[1];

                if (!is_numeric($seconds))  $seconds = 0;
                else                        $seconds = (int) $seconds;
            }

            $timedAction = ($minutes * 60) + $seconds;

            if (empty($timedAction)) continue;

            $timedAction = $timedAction * 1000;

            $time_array = [
                'timeout' => $timedAction,
                'time' => $time,
                'name' => $name,
                'slug' => $slug,
            ];

            if (!empty($pixel)) $time_array['pixel'] = $pixel;

            $tracking_tags_timeouts[] = $time_array;
        }

        if (!empty($tracking_tags_timeouts)) {
            ?>var trackingTags = <?php echo json_encode($tracking_tags_timeouts, JSON_HEX_TAG); ?>;<?php
        } else {
            ?>var trackingTags = [];<?php
        }
    } else {
        ?>var trackingTags = [];<?php
    }

    $tracking_tag_script_output = ob_get_clean();

    echo $tracking_tag_script_output;

    echo PHP_EOL;
    echo PHP_EOL;
    ?>
(function ($) {
    <?php
    if ('evergreen' !== $webinar_type) { // live webinar
        $timeStampNow               = time();
        $webinarDateTime            = $webinar_data->webinar_date . ' ' . $webinar_data->webinar_start_time ;
        $date_picked                = DateTime::createFromFormat('m-d-Y H:i', $webinarDateTime, new DateTimeZone( $webinar_data->webinar_timezone ) );
        $too_late_lockout_minutes   = !empty( $webinar_data->too_late_lockout_minutes ) ? (int) $webinar_data->too_late_lockout_minutes * 60 : 3600;
        $date_picked_timestamp      = $date_picked->getTimestamp();
        $offset = $timeStampNow - $date_picked_timestamp;

        if (0 > $offset) $offset = 0;

        if (!empty($offset)) {
            ?>globalOffset = <?php echo $offset * 1000; ?>;<?php
        }
    }

    ?>

    $( document.body ).on( 'wi_player_play', function(e) {
        if (
            (additional_autoactions && additional_autoactions.length)
        ) {
            scheduleAdditionalAutoactions();
        }
        $('#vidOvl').show();
	    <?php if( isset($webinar_data->auto_action) && $webinar_data->auto_action !== 'time' ) { ?>
        //somehow div show stopped working immediately, so setting a bit of delay
        setTimeout(function() {
            $('#wi-cta-default-overlay').show(); //Show first CTA if defined
        }, 5);
	    <?php } ?>
    } );

    $(document).ready(function(){

<?php if ('evergreen' === $webinar_type) { ?>
    <?php if ( $webinar_data->auto_action === "time" && (! empty( $webinar_data->webinar_iframe_source ) || !empty( $webinar_data->auto_video_url ) ) ) { ?>
        <?php if ( ! webinarignition_should_use_videojs( $webinar_data ) ) { ?>
        if (
            (additional_autoactions && additional_autoactions.length)
        ) {
            scheduleAdditionalAutoactions();
        }
        <?php } ?>
    <?php } // if ( $webinar_data->auto_action === "time" && ! empty( $webinar_data->webinar_iframe_source ) ) ?>
<?php } // if ('evergreen' === $webinar_type) ?>

        if (trackingTags && trackingTags.length) {
            $('head').append('<div id="tracking_pixel_holder"></div>');

            trackingTags.forEach(function(item, index) {
                var timeout = item.timeout;
                var time = item.time;
                var name = item.name;
                var slug = item.slug;
                var pixel = item.pixel;

                if (timeout > globalOffset) {
                    setTimeout(function() {
                        $('#tracking_pixel_holder').empty();

                        if (pixel) {
                            var pixel_html = $(pixel);
                            $('#tracking_pixel_holder').html(pixel_html);
                        }

                        var data = {
                            action: 'webinarignition_tracking_tags',
                            time: time,
                            name: name,
                            slug: slug,
                            lead_id: '<?php echo $lead_id; ?>',
                            webinar_type: '<?php echo $webinar_type; ?>',
                            webinar_id: '<?php echo $webinar_id; ?>'
                        };

                        $.post(ajaxurl, data, function (results) { console.log(results); });
                    }, timeout);
                } else {
                    // console.log('Time Tag Outdated');
                }
            });
        }
    });

<?php if ('evergreen' === $webinar_type && $webinar_data->auto_action === "time" && (! empty( $webinar_data->webinar_iframe_source ) || !empty( $webinar_data->auto_video_url ) )) { ?>

    function scheduleAdditionalAutoactions() {
        if (additional_autoactions.length) {
            var myPlayer = null;
            var wiPlayer = $('#autoReplay').find('video').get(0);
            var wiVideoInterval = [];
            var endDelays = [];



            $.each(additional_autoactions, function(index, autoaction) {
                let start_delay = autoaction.start_delay - globalOffset;
                if (start_delay < 10) start_delay = 10;
                if (start_delay > 1010) start_delay = start_delay - 1000;
                let end_delay = autoaction.end_delay - globalOffset;
                if (autoaction.is_videojs) myPlayer = videojs('autoReplay');

                if(myPlayer) {

                    myPlayer.on('timeupdate', function() {
                        $( document.body ).trigger( 'wi_video_timeupdate', [ {
                            'index': autoaction.index,
                            'start': autoaction.start_delay,
                            'end': autoaction.end_delay
                        }, myPlayer.currentTime(), (myPlayer.paused() === true && myPlayer.ended() === false), myPlayer.duration() ] );
                        // console.log(autoaction.index, myPlayer.currentTime());
                    });
                } else if(wiPlayer) {
                    wiPlayer.ontimeupdate = function () {
                        $( document.body ).trigger( 'wi_video_timeupdate', [ {
                            'index': autoaction.index,
                            'start': autoaction.start_delay,
                            'end': autoaction.end_delay
                        }, wiPlayer.currentTime, (wiPlayer.paused === true && wiPlayer.ended === false), wiPlayer.duration() ] );
                        // console.log(autoaction.index, wiPlayer.currentTime);
                    };

                } else {

                    endDelays[autoaction.index] = autoaction.end_delay;

                    var wiIntervalTime = 1;
                    setTimeout( function() {

                        wiVideoInterval[autoaction.index] = setInterval(function() {
                            wiIntervalTime = (wiIntervalTime + 1);
                            $( document.body ).trigger( 'wi_video_timeupdate', [ {
                                'index': autoaction.index,
                                'start': autoaction.start_delay,
                                'end': autoaction.end_delay
                            }, wiIntervalTime, false, (auto_video_length * 60) ] );

                        }, 1000);

                    }, 5000 );
                }
            });

            if(myPlayer) {
                myPlayer.on('ended', function() {
                    $( document.body ).trigger( 'wi_video_ended');
                });
            } else if(wiPlayer) {
                wiPlayer.onended = function () {
                    $( document.body ).trigger( 'wi_video_ended');
                };

            } else {
                var stopTimer = (auto_video_length * 60) * 1000; <?php //Get video length in minutes set in webinar settings ?>

                if(endDelays.length > 0) {
                    endDelays.sort(function(a, b){return a-b});
                    stopTimer = endDelays[endDelays.length - 1]; <?php //Get the maximum of end/hide time ?>
                }

                stopTimer = (stopTimer + 5000); <?php //Add 5 seconds delay (manually set to allow video load) to stopTimer ?>

                setTimeout( function( ) {
                    if(wiVideoInterval.length > 0) {
                        $.each(wiVideoInterval, function(index, interval) {
                            clearInterval(interval);
                        });

                        $(document.body).trigger('wi_video_ended');
                    }
                }, stopTimer );
            }
        }
    }

<?php } ?>
})(jQuery);
</script>
