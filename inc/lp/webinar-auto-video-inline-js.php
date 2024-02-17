<?php
/**
 * @var $webinar_data
 * @var $input_get
 * @var $leadinfo
 * @var $webinar_id
 */

defined( 'ABSPATH' ) || exit;

$lead_id = !empty($leadinfo->ID) ? $leadinfo->ID : '';
$webinar_type = $webinar_data->webinar_date == "AUTO" ? 'evergreen' : 'live';
?>
<script type="text/javascript">
    var globalOffset = 0;
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

    (function ($) {
	    <?php
	    $is_auto_login_enabled = get_option( 'webinarignition_registration_auto_login', 1 ) == 1;
	    if( ($is_auto_login_enabled && is_user_logged_in() || !$is_auto_login_enabled)):
	    ?>

        var is_preview_page = <?php echo json_encode( WebinarignitionManager::url_is_preview_page() ); ?>;

        $( document ).on( 'wi_player_play', function(e) {
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'webinarignition_lead_mark_attended',
                    nonce: '<?php echo wp_create_nonce('webinarignition_mark_lead_status'); ?>',
                    webinar_id: '<?php echo $webinar_data->id; ?>',
                    lead_id: '<?php echo $lead_id; ?>',
                    is_preview_page: is_preview_page
                },
                success: function(response) {
                }
            });
        });

        $( document ).on( 'wi_video_ended', function(e) {

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'webinarignition_lead_mark_watched',
                    nonce: '<?php echo wp_create_nonce('webinarignition_mark_lead_status'); ?>',
                    webinar_id: '<?php echo $webinar_data->id; ?>',
                    lead_id: '<?php echo $lead_id; ?>',
                    is_preview_page: is_preview_page
                },
                success: function(response) {
                    if( response.success || is_preview_page ) {
	                    <?php
                        $auto_redirect_url = WebinarignitionManager::get_auto_redirect_url($webinar_data);
                        if ( $auto_redirect_url ):
                        ?>
                            window.onbeforeunload = undefined; //Unbind beforeunload, to avoid confirmation alert and further ajax calls

                            <?php if( isset($webinar_data->auto_redirect_delay) && absint($webinar_data->auto_redirect_delay) > 0 ): ?>
                            setTimeout(function() {
                                window.location.href = '<?php echo $webinar_data->auto_redirect_url; ?>';
                            }, "<?php echo ( absint($webinar_data->auto_redirect_delay) * 1000 ); ?>");
                            <?php else: ?>
                            window.location.href = '<?php echo $webinar_data->auto_redirect_url; ?>';
                            <?php endif; ?>

                        <?php else: ?>
                            <?php if ( !WebinarignitionManager::url_is_preview_page() ): ?>
                            window.location.reload();
                            <?php endif; ?>
                        <?php endif; ?>
                    }
                }
            });
        });
	    <?php endif ?>

        <?php
        if ( ! webinarignition_should_use_videojs( $webinar_data ) ) {
            if ($webinar_data->auto_redirect == "redirect" && ! empty( $webinar_data->auto_video_length )) {
                ?>
                var webinarLength = <?php echo (int) $webinar_data->auto_video_length * 60 * 1000 ?>;

                setTimeout(function () {
                    // $( document.body ).trigger( 'wi_video_ended');
                }, webinarLength);
                <?php
            }
        } else {
            ?>
            if (!document.getElementById('autoReplay')) return;


            var no_autoplay_block = $('#no-autoplay-block').css({"top":"0", "left":"0"}).show();
            var myPlayerOptions = {
                "preload": "auto",
                width: "920",
                height: "500",
                fluid: true,
                playsinline: true,
                // muted: true,
                autoplay: true,
                bigPlayButton: false,
                languages: {
                  en: {
                        "Video Player": "<?php esc_html_e( 'Video Player', "webinarignition" ); ?>",
                        "Play": "<?php esc_html_e( 'Play', "webinarignition" ); ?>",
                        "Pause": "<?php esc_html_e( 'Pause', "webinarignition" ); ?>",
                        "Replay": "<?php esc_html_e( 'Replay', "webinarignition" ); ?>",
                        "Current Time": "<?php esc_html_e( 'Current Time', "webinarignition" ); ?>",
                        "Duration": "<?php esc_html_e( 'Duration', "webinarignition" ); ?>",
                        "Remaining Time": "<?php esc_html_e( 'Remaining Time', "webinarignition" ); ?>",
                        "LIVE": "<?php esc_html_e( 'LIVE', "webinarignition" ); ?>",
                        "Seek to live, currently behind live": "<?php esc_html_e( 'Seek to live, currently behind live', "webinarignition" ); ?>",
                        "Seek to live, currently playing live": "<?php esc_html_e( 'Seek to live, currently playing live', "webinarignition" ); ?>",
                        "Loaded": "<?php esc_html_e( 'Loaded', "webinarignition" ); ?>",
                        "Progress": "<?php esc_html_e( 'Progress', "webinarignition" ); ?>",
                        "Fullscreen": "<?php esc_html_e( 'Fullscreen', "webinarignition" ); ?>",
                        "Non-Fullscreen": "<?php esc_html_e( 'Exit Fullscreen', "webinarignition" ); ?>",
                        "Mute": "<?php esc_html_e( 'Mute', "webinarignition" ); ?>",
                        "Unmute": "<?php esc_html_e( 'Unmute', "webinarignition" ); ?>",
                        "Playback Rate": "<?php esc_html_e( 'Playback Rate', "webinarignition" ); ?>",
                        "Subtitles": "<?php esc_html_e( 'Subtitles', "webinarignition" ); ?>",
                        "subtitles off": "<?php esc_html_e( 'subtitles off', "webinarignition" ); ?>",
                        "Volume Level": "<?php esc_html_e( 'Volume Level', "webinarignition" ); ?>",
                        "You aborted the media playback": "<?php esc_html_e( 'You aborted the media playback', "webinarignition" ); ?>",
                        "A network error caused the media download to fail part-way.": "<?php esc_html_e( 'A network error caused the media download to fail part-way.', "webinarignition" ); ?>",
                        "The media could not be loaded, either because the server or network failed or because the format is not supported.": "<?php esc_html_e( 'The media could not be loaded, either because the server or network failed or because the format is not supported.', "webinarignition" ); ?>",
                        "The media playback was aborted due to a corruption problem or because the media used features your browser did not support.": "<?php esc_html_e( 'The media playback was aborted due to a corruption problem or because the media used features your browser did not support.', "webinarignition" ); ?>",
                        "No compatible source was found for this media.": "<?php esc_html_e( 'No compatible source was found for this media.', "webinarignition" ); ?>",
                        "The media is encrypted and we do not have the keys to decrypt it.": "<?php esc_html_e( 'The media is encrypted and we do not have the keys to decrypt it.', "webinarignition" ); ?>",
                        "Play Video": "<?php esc_html_e( 'Play Video', "webinarignition" ); ?>",
                        "Close": "<?php esc_html_e( 'Close', "webinarignition" ); ?>",
                        "Close Modal Dialog": "<?php esc_html_e( 'Close Modal Dialog', "webinarignition" ); ?>",
                        "Modal Window": "<?php esc_html_e( 'Modal Window', "webinarignition" ); ?>",
                        "This is a modal window": "<?php esc_html_e( 'This is a modal window', "webinarignition" ); ?>",
                        "This modal can be closed by pressing the Escape key or activating the close button.": "<?php esc_html_e( 'This modal can be closed by pressing the Escape key or activating the close button.', "webinarignition" ); ?>",
                        "Reset": "<?php esc_html_e( 'Reset', "webinarignition" ); ?>",
                        "restore all settings to the default values": "<?php esc_html_e( 'restore all settings to the default values', "webinarignition" ); ?>",
                        "Done": "<?php esc_html_e( 'Done', "webinarignition" ); ?>",
                        "Beginning of dialog window. Escape will cancel and close the window.": "<?php esc_html_e( 'Beginning of dialog window. Escape will cancel and close the window.', "webinarignition" ); ?>",
                        "End of dialog window.": "<?php esc_html_e( 'End of dialog window.', "webinarignition" ); ?>",
                        "{1} is loading.": "<?php esc_html_e( '{1} is loading.', "webinarignition" ); ?>",
                        "Exit Picture-in-Picture": "<?php esc_html_e( 'Exit Picture-in-Picture', "webinarignition" ); ?>",
                        "Picture-in-Picture": "<?php esc_html_e( 'Picture-in-Picture', "webinarignition" ); ?>"
                  }
                }                
            };

            // videojs video playback initialised?
            var initdone = false;

            // video playback offset
            var offset              = <?php echo ( !empty($individual_offset) ? $individual_offset : 0 ) ?>;

            <?php
            if ( !current_user_can( 'manage_options' ) ) {
                ?>
                var lid                 = '<?php echo ( !empty($_GET['lid']) ? $_GET['lid'] : '' ) ?>';
                var videoResumeTime     = $.cookie('videoResumeTime-' + lid);
                // console.log(videoResumeTime);
                <?php
            }
            ?>

            offset                  = (typeof(videoResumeTime) !== 'undefined') ? videoResumeTime : offset;
            globalOffset            = Math.ceil(offset * 1000);

            // initialise Videojs
            var myPlayer = videojs('autoReplay', myPlayerOptions);

            myPlayer.on('loadedmetadata', function () {
                var playButton = document.getElementById('mobile-play-button');
                if (!playButton) {
                    return;
                }

                playButton.addEventListener('click', function () {
                    <?php if ( !current_user_can( 'manage_options' ) ): ?>
                        myPlayer.currentTime(videoResumeTime);
                    <?php endif;  ?>

                    if (myPlayer.duration() > offset) {
                        myPlayer.muted(false);
                        myPlayer.play();
                        no_autoplay_block.hide();
                    }
                });

                var unmuteButton = document.getElementById('unmute-button');
                if (!unmuteButton) {
                    return;
                }

                unmuteButton.addEventListener('click', function () {
                    myPlayer.muted(false);
                    document.getElementById('muted-autoplay-block').style.display = 'none';
                });

               document.getElementById('video-loading-block').style.display = 'none';

               myPlayer.ready(function() {
                   no_autoplay_block.appendTo('#autoReplay');
                   //append default overlay CTA into videoJS container, to fix over-sizing
                   if( $('.webinarVideoCTA.webinarVideoCTAActive').parent().hasClass('webinarVideoCtaCombined') ) {
                       $('.webinarVideoCTA.webinarVideoCTAActive').appendTo('#autoReplay');
                   }

                    <?php if ( !current_user_can( 'manage_options' ) ): ?>
                        myPlayer.currentTime(videoResumeTime);
                    <?php endif;  ?>

                    if (myPlayer.duration() > offset) {
                        myPlayer.play().then(function () {
                                console.log('autoplay with sound started!');
                                $( document.body ).trigger( 'wi_player_play' );
                                no_autoplay_block.hide();
                                // unmuteButton.click();
                            })
                            .catch(function (error) {
                                console.log('autoplay with sound could not start: ', error);
                                no_autoplay_block.show();
                                return;
                        });

                        <?php if( !WebinarignitionManager::url_is_preview_page() ): ?>
                        myPlayer.on('pause', function () {
                            $( document.body ).trigger( 'wi_player_pause',myPlayer );
                        });
                        <?php endif; ?>

                        myPlayer.on('play', function () {
                            $( document.body ).trigger( 'wi_player_play' );
                        });

                        myPlayer.on('timeupdate', function () {
                            var videoResumeTime = myPlayer.currentTime();
                            var newResumeTime = Math.ceil(videoResumeTime);
                            var oldResumeTime = Math.ceil(globalOffset / 1000);
                            var differenceTime = Math.abs(newResumeTime - oldResumeTime);

                            globalOffset = Math.ceil(videoResumeTime * 1000);

                            if (1 < differenceTime) {
                                $( document.body ).trigger( 'wi_player_rewind', [ newResumeTime, oldResumeTime, differenceTime ] );
                            }

                            <?php if ( !current_user_can( 'manage_options' ) ): ?>
                                $.cookie('videoResumeTime-' + lid, videoResumeTime);
                            <?php endif;  ?>

                            var num = myPlayer.currentTime();
                            num = Math.floor(num);
                            var minutes = Math.floor(num / 60);
                            jQuery("#autoVideoTime").val(minutes);
                            jQuery(".autoWebinarLoading").fadeOut("fast");
                        });

                        myPlayer.on('ended', function () {
                            $( document.body ).trigger( 'wi_video_ended');
                        });
                    } else {
                        myPlayer.on('play', function () {
                            $( document.body ).trigger( 'wi_player_play' );
                        });
                    }
                });
            });
        <?php
        }
    ?>
    })(jQuery);
</script>
