<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $assets
 */
?>
<?php
$ty_CTA_BG = '#212121';
if( isset($webinar_data->ty_cta_bg_color) && !empty($webinar_data->ty_cta_bg_color) ) {
	$ty_CTA_BG = $webinar_data->ty_cta_bg_color;
}
?>
<style>
    .ctaArea.video {
        background-color: <?php echo $ty_CTA_BG; ?>;
    }
    <?php if($ty_CTA_BG == 'transparent'): ?>
    .ctaArea {
        border:none;
    }
    .ctaArea.video {
        padding:0;
    }
    <?php endif; ?>
</style>
<div class="ctaArea video" <?php echo $webinar_data->ty_cta_type == "html" ? ' style="background-color:#FFF;"' : '' ?>>
    <?php
    if ( has_shortcode( $webinar_data->lp_cta_video_code, 'video' ) && empty($GLOBALS['content_width'])) {
        $GLOBALS['content_width'] = 1225;//see /wp-includes/media.php::wp_video_shortcode();
    }

    if ( $webinar_data->ty_cta_type == "video" ) {
        if( isset($webinar_data->ty_cta_video_url) && !empty($webinar_data->ty_cta_video_url) ) {

            $is_preview = WebinarignitionManager::url_is_preview_page();
            wp_enqueue_style('webinarignition_video_css');
            wp_enqueue_script('webinarignition_video_js');
            ?>
            <script>
                jQuery(function() {
                    var wi_ctaVideoPlayer = videojs("wi_ctaVideoPlayer", {
                        fluid: true,
                        playsinline: true,
                        muted: true,
                        bigPlayButton: false,
                        controls: false,
                        controlBar: false
                    });

                    function wi_videojs_do_autoplay(player, muted, success_cb, error_cb) {

                        player.muted(muted);

                        var played_promise = player.play();

                        if ( played_promise !== undefined ) {

                            if( success_cb === null ) {
                                success_cb = function() {}
                            }

                            if( error_cb === null ) {
                                error_cb = function() {}
                            }

                            played_promise.then(success_cb).catch(error_cb);
                        }
                    }

                    //Immediate autoplay stopped working in chrome,
                    //Workaround: Play the video programatically, few seconds after player is ready,
                    // and detect if that fails then do autoplay in muted mode.
                    wi_ctaVideoPlayer.ready(function() {
                        setTimeout(function() {

                            wi_ctaVideoPlayer.fluid('true')

                            wi_videojs_do_autoplay(wi_ctaVideoPlayer, false, function() {
                                jQuery('#wi_ctaVideo > .wi_videoPlayerMute').show();
                            }, function(error) {
                                console.log(error);
                                wi_videojs_do_autoplay(wi_ctaVideoPlayer, true, function() {
                                    jQuery('#wi_ctaVideo > .wi_videoPlayerUnmute').show();
                                }, function(error) {
                                    console.log(error);
                                });
                            });

                        }, 500);
                    });

                    jQuery('#wi_ctaVideo > .wi_videoPlayerUnmute').click(function(e) {
                        e.preventDefault();
                        wi_ctaVideoPlayer.muted(false);
                        jQuery(this).hide();
                        jQuery('#wi_ctaVideo > .wi_videoPlayerMute').show();
                    });

                    jQuery('#wi_ctaVideo > .wi_videoPlayerMute').click(function(e) {
                        e.preventDefault();
                        wi_ctaVideoPlayer.muted(true);
                        jQuery(this).hide();
                        jQuery('#wi_ctaVideo > .wi_videoPlayerUnmute').show();
                    });
                });

            </script>
            <style>
                #wi_ctaVideo {
                    position:relative;
                    width: 100%;
                }

                #wi_ctaVideoPlayer {
                    width:100%;
                    height:100%;
                }

                #wi_ctaVideo > .wi_videoPlayerUnmute {
                    position: absolute;
                    width: 124px;
                    top: 50%;
                    margin-top: -22px;
                    left: 50%;
                    margin-left: -62px;
                    z-index: 9999;
                    display:none;
                }

                #wi_ctaVideo > .wi_videoPlayerMute {
                    background: no-repeat;
                    border: none;
                    width: 10%;
                    padding:0 2% 1% 2%;
                    position: absolute;
                    bottom: 0;
                    display: none;
                    -webkit-box-shadow: none;
                    box-shadow: none;
                    -webkit-transition: none;
                    -moz-transition: none;
                    transition: none;
                    z-index: 9999;
                    cursor: pointer;
                }
            </style>

            <div id="wi_ctaVideo">
                <button class="button addedArrow wiButton wiButton-primary wiButton-block wiButton-lg wi_videoPlayerUnmute"><?php echo apply_filters('wi_cta_video_unmute_text', esc_html__('Unmute', 'webinarignition')); ?></button>
                <video id="wi_ctaVideoPlayer" class="video-js vjs-default-skin wi_videoPlayer" disablePictureInPicture oncontextmenu="return false;">
                    <source src="<?php echo $webinar_data->ty_cta_video_url; ?>" type='video/mp4'/>
                </video>
                <button class="wi_videoPlayerMute"><img src="<?php echo $assets . 'images/mute.svg' ?>" /></button>
            </div>
            <?php
        } else {
            webinarignition_display(
                do_shortcode( $webinar_data->ty_cta_video_code ),
                '<img src="' . $assets . 'images/novideo.png" />'
            );
        }
    } else if ( $webinar_data->ty_cta_type == "html" || $webinar_data->ty_cta_type == "" ) {
        webinarignition_display(
            $webinar_data->ty_cta_html,
            __( '<h3>Looking Forward To Seeing You<br/> On The Webinar!</h3><p>An email is being sent to you with all the information. If you want more reminders for the event add the event date to your calendar...</p>', "webinarignition")
        );
    } else if ( $webinar_data->ty_cta_type == "image" ) {
        echo "<img src='";
        webinarignition_display( $webinar_data->ty_cta_image, $assets . 'images/noctaimage.png' );
        echo "' height='215' width='287' />";
    }
    ?>
</div>
