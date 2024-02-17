<?php defined( 'ABSPATH' ) || exit; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- META INFO -->
    <title><?php
        if ($webinar_data->meta_site_title_ty == "") {
            webinarignition_display($webinar_data->lp_metashare_title, __( "Amazing Webinar", "webinarignition") );
        } else {
            echo $webinar_data->meta_site_title_ty;
        }
        ?></title>
    <meta name="description" content="<?php
    if ($webinar_data->meta_desc_ty == "") {
        webinarignition_display($webinar_data->lp_metashare_desc, __( "Join this amazing webinar, and discover industry trade secrets!", "webinarignition") );
    } else {
        echo $webinar_data->meta_desc_ty;
    }
    ?>">

    <?php
    if ($webinar_data->ty_share_image != "") { ?>
    <meta property="og:image" content="<?php webinarignition_display($webinar_data->ty_share_image, ""); ?>"/><?php } ?>

    <?php wp_head(); ?>

    <?php include("css/ty_css.php"); ?>

    <!-- CUSTOM JS -->
    <?php webinarignition_display_custom_js($webinar_data, 'custom_ty_js'); ?>

    <!-- CUSTOM CSS -->
    <style type="text/css">
        <?php webinarignition_display($webinar_data->custom_ty_css, ""); ?>
    </style>



</head>
<body class="thankyou_cp_preview" id="webinarignition">

<!-- TOP AREA -->
<div class="topArea">
    <div class="bannerTop container">
        <?php
        if ($webinar_data->lp_banner_image !== "") {
            echo "<img src='$webinar_data->lp_banner_image' />";
        }
        ?>
    </div>
</div>

<!-- Main Area -->
<div class="mainWrapper">
    <!-- HEADLINE AREAA -->
    <div class="headlineArea">
        <div class="wiContainer container">
            <div class="tyHeadlineIcon">
                <i class="icon-check-sign icon-4x" style="color: #6a9f37;"></i>
            </div>

            <div class="tyHeadlineCopy">
                <div
                    class="optinHeadline1 wiOptinHeadline1"><?php webinarignition_display(
                        $webinar_data->ty_ticket_headline,
                        __( "Congrats - You Are All Signed Up!", "webinarignition")
                    ); ?></div>
                <div
                    class="optinHeadline2 wiOptinHeadline2"><?php webinarignition_display(
                        $webinar_data->ty_ticket_subheadline,
                        __( "Below is all the information you need for the webinar...", "webinarignition") 
                    ) ?></div>
            </div>

            <br clear="left"/>

        </div>
         <!-- /.headlineArea .container-->
    </div>
    <!-- /.headlineArea -->

    <!-- MAIN AREA -->
    <div class="cpWrapperWrapper">
    <div class="wiContainer container">
    <div class="row">
        <div class="cpWrapper">
            <div class="cpLeftSide col-md-6">
                <div class="ticketWrapper">
                    <div class="eventDate">


            <div class="dateIcon">
                <div class="dateMonth">MONTH</div>
                <div class="dateDay">DAY</div>
            </div>

            <div class="dateInfo">
                <div class="dateHeadline"><?php _e( 'Date Chosen Will Be Here', "webinarignition" ); ?></div>
                <div class="dateSubHeadline"><?php _e( '@ Time Chosen local time ', "webinarignition" ); ?></div>
            </div>

                        <br clear="left">
                    </div>

                    <div class="ticketInfo">

                        <div class="ticketInfoNew">

                            <div class="ticketSection ticketSectionNew">
                                <!-- <i class="icon-desktop"></i> -->
                                <?php if ($webinar_data->ty_ticket_webinar_option == "custom") {
                                    ?>
                                    <div class="ticketInfoIcon">
                                        <i class="icon-desktop icon-3x"></i>
                                    </div>
                                    <div class="ticketInfoCopy">
                                        <b><?php webinarignition_display($webinar_data->ty_ticket_webinar, __( "Webinar", "webinarignition")); ?></b>

                                        <div
                                            class="ticketInfoNewHeadline"><?php webinarignition_display(
                                                $webinar_data->ty_webinar_option_custom_title,
                                                __( "Webinar Event Title", "webinarignition")
                                            ); ?></div>
                                    </div>
                                    <br clear="left"/>
                                <?php
                                } else {
                                    ?>
                                    <div class="ticketInfoIcon">
                                        <i class="icon-desktop icon-3x"></i>
                                    </div>
                                    <div class="ticketInfoCopy">
                                        <b><?php _e( 'Webinar:', "webinarignition" ); ?></b>

                                        <div
                                            class="ticketInfoNewHeadline"><?php webinarignition_display(
                                                $webinar_data->webinar_desc,
                                                __( "Webinar Event Title", "webinarignition")
                                            ); ?></div>
                                    </div>
                                    <br clear="left"/>
                                <?php } ?>
                            </div>

                <div class="ticketSection ticketSectionNew">
                    <!-- <i class="icon-bullhorn"></i>  -->
                    <?php if ($webinar_data->ty_ticket_host_option == "custom") {
                        ?>
                        <div class="ticketInfoIcon2">
                            <i class="icon-microphone icon-3x"></i>
                        </div>
                        <div class="ticketInfoCopy2">
                            <b><?php webinarignition_display($webinar_data->ty_ticket_host, "Host"); ?></b>

                            <div
                                class="ticketInfoNewHeadline"><?php webinarignition_display( $webinar_data->ty_webinar_option_custom_host, __( "Your Name Here" , "webinarignition") ); ?></div>
                        </div>
                        <br clear="left"/>
                    <?php
                    } else {
                        ?>
                        <div class="ticketInfoIcon2">
                            <i class="icon-microphone icon-3x"></i>
                        </div>
                        <div class="ticketInfoCopy2">
                            <b><?php _e( 'Host', "webinarignition" ); ?>:</b>

                            <div
                                class="ticketInfoNewHeadline"><?php webinarignition_display($webinar_data->webinar_host, __( "Host name", "webinarignition") ); ?></div>
                        </div>
                        <br clear="left"/>
                    <?php } ?>
                </div>

                <div class="ticketCDArea ticketSection ticketSectionNew">

                    <a href="<?php echo webinarignition_fixPerma($data->postID)."live"; ?>"
                       class="ticketCDAreaBTN button alert radius disabled addedArrow" id="webinarBTNNN">
                        <?php _e( 'Example Countdown button', "webinarignition" ); ?>
                    </a>

                </div>


            </div>


            <div class="webinarURLArea">

                <div class="webinarURLHeadline">
                    <i class="icon-bookmark" style="margin-right: 10px; color: #878787;"></i>
                    <?php
                    webinarignition_display(
                        $webinar_data->ty_webinar_headline, __( 'Here Is Your Webinar Event URL...', "webinarignition")
                    );
                    ?>
                </div>

                <div class="webinarURLHeadline2">
                    <?php
                    webinarignition_display(
                        $webinar_data->ty_webinar_subheadline, __( 'Save and bookmark this URL so you can get access to the live webinar and webinar replay...', "webinarignition")
                    );
                    ?>
                </div>
            </div>

        </div>

    </div>


</div>

<div class="cpRightSide col-md-6">
    <!-- VIDEO / CTA BLOCK AREA HERE -->
    <div class="ctaArea" <?php
    if ($webinar_data->ty_cta_type == "html") {
        echo 'style="background-color:#FFF;"';
    }
    ?>>

        <div class="preview"
             style="padding:10px; margin:10px; font-size: 14px; font-weight: bold; background-color:#C65355; color:#FFF;">
            <?php _e( 'THIS IS JUST A PREVIEW - The Real Thank You Page Depends On User Submited Dates - Do a Fake Optin For Real The Experience', "webinarignition" ); ?>
        </div>

        <?php
        if ($webinar_data->ty_cta_type == "video") {
	        if( isset($webinar_data->ty_cta_video_url) && !empty($webinar_data->ty_cta_video_url) ) {

		        $is_preview = WebinarignitionManager::url_is_preview_page();
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
                    <button class="wi_arrow_button button wiButton wiButton-block wiButton-lg addedArrow wi_videoPlayerUnmute"><?php echo apply_filters('wi_cta_video_unmute_text', esc_html__('Unmute', 'webinarignition')); ?></button>
                    <video id="wi_ctaVideoPlayer" class="video-js vjs-default-skin wi_videoPlayer" disablePictureInPicture oncontextmenu="return false;">
                        <source src="<?php echo $webinar_data->ty_cta_video_url; ?>" type='video/mp4'/>
                    </video>
                    <button class="wi_videoPlayerMute"><img src="<?php echo $assets . 'images/mute.svg' ?>" /></button>
                </div>
		        <?php
	        } else {
		        webinarignition_display(
			        do_shortcode( $webinar_data->ty_cta_video_code ), '<img src="' . $assets . 'images/novideo.png" />'
		        );
	        }
        } else if ($webinar_data->ty_cta_type == "html" || $webinar_data->ty_cta_type == "") {
            webinarignition_display(
                $webinar_data->ty_cta_html, '<h3>'.__( "Looking Forward To Seeing You", "webinarignition").'<br/> '.__( "On The Webinar!", "webinarignition").'</h3><p>'.__( "An email is being sent to you with all the information. If you want more reminders for the event add the event date to your calendar...", "webinarignition").'</p>'
            );
        } else if ($webinar_data->ty_cta_type == "image") {
            echo "<img src='";
            webinarignition_display($webinar_data->ty_cta_image, $assets . 'images/noctaimage.png');
            echo "' height='215' width='287' />";
        }
        ?>
    </div>

                <div class="remindersBlock">

                    <?php $wi_calendarOption = !empty($webinar_data->ty_add_to_calendar_option) ? $webinar_data->ty_add_to_calendar_option : 'enable'; ?>
                    <?php if ($wi_calendarOption === 'enable'): ?>
                    <div class="ticketSection ticketCalendarArea">
                        <div class="optinHeadline12 wiOptinHeadline2"><?php webinarignition_display(
                                $webinar_data->ty_calendar_headline,
                                __( "Add To Your Calendar", "webinarignition")
                            ); ?></div>

                        <!-- AUTO CODE BLOCK AREA -->
                        <?php if ($webinar_data->webinar_date == "AUTO") { ?>
                            <!-- AUTO DATE -->
                            <a href="?googlecalendarA" class="small button" target="_blank">
                                <i class="icon-google-plus"></i> <?php webinarignition_display(
                                    $webinar_data->ty_calendar_google,
                                   __(  "Google Calendar", "webinarignition")
                                ); ?>
                            </a>
                            <a href="?icsA" class="small button" target="_blank">
                                <i class="icon-calendar"></i> <?php webinarignition_display($webinar_data->ty_calendar_ical, __('iCal / Outlook', 'webinarignition')); ?>
                            </a>
                        <?php } else { ?>
                            <a href="?googlecalendar" class="small button" target="_blank">
                                <i class="icon-google-plus"></i> <?php webinarignition_display(
                                    $webinar_data->ty_calendar_google,
                                    __(  "Google Calendar", "webinarignition")
                                ); ?>
                            </a>
                            <a href="?ics" class="small button" target="_blank">
                                <i class="icon-calendar"></i> <?php webinarignition_display($webinar_data->ty_calendar_ical, __('iCal / Outlook', 'webinarignition')); ?>
                            </a>
                        <?php } ?>
                        <!-- END OF AUTO CODE BLOCK -->

                    </div>
                    <?php endif; ?>

                </div>


            </div>

            <br clear="both"/>


            <div class="cpUnderHeadline" style="display:<?php webinarignition_display($webinar_data->ty_share_toggle, "none"); ?>;">
                <?php
                webinarignition_display(
                    $webinar_data->ty_step2_headline,
                    __( 'Step #2: Share & Unlock Reward...', "webinarignition")
                );
                ?>
            </div>

            <div class="cpUnderCopy" style="display:<?php webinarignition_display($webinar_data->ty_share_toggle, "none"); ?>;">

    <div class="cpCopyArea">
        <!-- SHARE BLOCK -->
        <div class="shareBlock wi-block--sharing" style="display:<?php webinarignition_display($webinar_data->ty_share_toggle, "none"); ?>;">

            <?php
            if ($webinar_data->ty_fb_share == "off") {

            } else {
                ?>
                <div class="socialShare">
                    <div class="fb-like" data-href="<?php echo get_permalink($data->postID); ?>" data-send="false"
                         data-layout="box_count" data-width="48" data-show-faces="false" data-font="arial"></div>
                </div>
                <div class="socialDivider"></div>
            <?php } ?>

           <br clear="left"/>

        </div>

        <!-- SHARE REWARD - UNLOCK -->
        <div class="shareReward" style="display:<?php webinarignition_display($webinar_data->ty_share_toggle, "none"); ?>;">
            <div class="sharePRE">
                <?php
                webinarignition_display(
                    $webinar_data->ty_share_intro, '<h4>'.__( "Share This Webinar & Unlock Free Report", 'webinarignition').'</h4>
							<p>'.__( "Simply share the webinar on any of the social networks above, and you will get instant access to this reporcss..", 'webinarignition').'</p>'
                );
                ?>
            </div>
            <div class="shareREVEAL" style="display: none;">
                <?php
                webinarignition_display(
                    $webinar_data->ty_share_reveal, '<h4>'.__( "Congrats! Reward Unlocked", 'webinarignition').'</h4>
							<p>'.__( "Here is the text that would be shown when they unlock a reward...", 'webinarignition').'</p>'
                );
                ?>
            </div>
        </div>
    </div>

    <div class="cpCopyTY">
        <!-- ADD TO CALENDARS -->
        <div class="addCalendar" style="display:none;">

            <div class="addCalendarHeadline">
                <i class="icon-calendar icon-4x ticketIcon"></i>

                <?php if( !empty($webinar_data->ty_calendar_headline) ): ?>
                    <span class="optinHeadline1 wiOptinHeadline1"><?php webinarignition_display( $webinar_data->ty_calendar_headline, __( "Add To Your Calendar", "webinarignition") ); ?></span>
                <?php endif; ?>

                <?php if( !empty($webinar_data->ty_calendar_subheadline)): ?>
                    <span class="optinHeadline2 wiOptinHeadline2"><?php webinarignition_display( $webinar_data->ty_calendar_subheadline, __( "Remind Yourself Of The Event", "webinarignition") ); ?></span>
                <?php endif; ?>

                <br clear="left"/>
            </div>

        </div>


    </div>

    <br clear="all"/>

</div>

</div>

</div>


</div>

<?php require_once WEBINARIGNITION_PATH .  'inc/lp/partials/powered_by.php'; ?>


</body>
</html>
