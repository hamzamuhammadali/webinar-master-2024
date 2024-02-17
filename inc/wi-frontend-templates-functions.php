<?php defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'webinarignition_get_lp_header' ) ) {
    function webinarignition_get_lp_header($webinarId, $template_number, $webinar_data) {
        $assets         = WEBINARIGNITION_URL . "inc/lp/";
	    WebinarignitionManager::set_locale($webinar_data);
        $custom_lp_css_path = WEBINARIGNITION_PATH . "inc/lp/css/lp_css.php";

        if ('02' === $template_number) {
            $custom_lp_css_path = WEBINARIGNITION_PATH . "inc/lp/css/ss_css.php";
        }

        include WEBINARIGNITION_PATH . "inc/lp/partials/registration_page/header.php";
	    WebinarignitionManager::restore_locale($webinar_data);
    }
}

if ( ! function_exists( 'webinarignition_get_lp_footer' ) ) {
    function webinarignition_get_lp_footer($webinarId, $template_number, $webinar_data, $user_info) {
        $assets         = WEBINARIGNITION_URL . "inc/lp/";
	    WebinarignitionManager::set_locale($webinar_data);
        include WEBINARIGNITION_PATH . "inc/lp/partials/registration_page/footer.php";
	    WebinarignitionManager::restore_locale($webinar_data);
    }
}
#--------------------------------------------------------------------------------
#region Global Shortcodes
#--------------------------------------------------------------------------------
function webinarignition_get_webinar_title($webinar_data, $display = false) {
    extract(webinarignition_get_global_templates_vars($webinar_data));
    ob_start();
    if (!empty($webinar_data->webinar_desc))
        echo $webinar_data->webinar_desc;
    else
        echo '';
    $html = ob_get_clean();
    if (!$display) return $html;
    echo $html;
}


function webinarignition_get_host_name($webinar_data, $display = false) {
    extract(webinarignition_get_global_templates_vars($webinar_data));
    ob_start();
    if (!empty($webinar_data->webinar_host))
        echo $webinar_data->webinar_host;
    else
        echo '';
    $html = ob_get_clean();
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_webinar_giveaway_compact($webinar_data, $display = false) {
    extract(webinarignition_get_global_templates_vars($webinar_data));
    ob_start();
    if ($webinar_data->webinar_giveaway_toggle !== "hide"){
        webinarignition_display( $webinar_data->webinar_giveaway, "<h4>".__('Your Awesome Free Gift', 'webinarignition')."</h4><p>".__('You can download this awesome report made you...', 'webinarignition')."</p><p>[ ".__('DOWNLOAD HERE', 'webinarignition'). "]</p>" );
    }  else{
        echo '';
    }
    $html = ob_get_clean();
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_lead_name($webinar_data, $display = false) {
    extract(webinarignition_get_global_templates_vars($webinar_data));
    ob_start();
    if (!empty($leadinfo->name))
        echo $leadinfo->name;
    else
        echo '';
    $html = ob_get_clean();
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_lead_email($webinar_data, $display = false) {
    extract(webinarignition_get_global_templates_vars($webinar_data));
    ob_start();
    if (!empty($leadinfo->email))
        echo $leadinfo->email;
    else
        echo '';
    $html = ob_get_clean();
    if (!$display) return $html;
    echo $html;
}
#endregion

#--------------------------------------------------------------------------------
#region Registration Page
#--------------------------------------------------------------------------------
/**
 * @param $webinar_data
 * @param false $display
 *
 * @return false|string
 */
function webinarignition_get_lp_banner_short($webinar_data, $display = false) {
    $webinar_banner_bg_style = $webinar_data->lp_banner_bg_style;
    $html = '';

    if ('show' === $webinar_banner_bg_style) {
        $uid = wp_unique_id( $prefix = 'topArea-' );
        ob_start();
        ?><style>
.topArea.<?php echo $uid ?>{
<?php if($webinar_data->lp_banner_bg_style == "hide"){ echo "display: none;";} ?>
background-color: <?php if($webinar_data->lp_banner_bg_color == ""){ echo "#FFF"; } else { echo $webinar_data->lp_banner_bg_color; } ?>;
<?php
if($webinar_data->lp_banner_bg_repeater == "") {
    echo "  border-top: 3px solid rgba(0,0,0,0.20); border-bottom: 3px solid rgba(0,0,0,0.20);";
} else{
    echo "  background-image: url($webinar_data->lp_banner_bg_repeater);";
}
?>
}</style>
        <div class="topArea <?php echo $uid ?>">
            <div class="bannerTop">
                    <?php
                    if ( !empty($webinar_data->lp_banner_image) ) {
                        echo "<img src='$webinar_data->lp_banner_image' />";
                    }
                    ?>
            </div>
        </div>
        <?php
        $html = ob_get_clean();
    }

    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_lp_banner($webinar_data, $display = false) {
    $webinar_banner_bg_style = $webinar_data->lp_banner_bg_style;
    $html = '';

    if ('show' === $webinar_banner_bg_style) {
        $uid = wp_unique_id( $prefix = 'topArea-' );
        ob_start();
        ?><style>
.topArea.<?php echo $uid ?>{
<?php if($webinar_data->lp_banner_bg_style == "hide"){ echo "display: none;";} ?>
background-color: <?php if($webinar_data->lp_banner_bg_color == ""){ echo "#FFF"; } else { echo $webinar_data->lp_banner_bg_color; } ?>;
<?php
if($webinar_data->lp_banner_bg_repeater == "") {
    echo "  border-top: 3px solid rgba(0,0,0,0.20); border-bottom: 3px solid rgba(0,0,0,0.20);";
} else{
    echo "  background-image: url($webinar_data->lp_banner_bg_repeater);";
    echo "  background-repeat: repeat;";
}
?>
}</style>
        <div class="topArea <?php echo $uid ?>">
            <div class="bannerTop container">
                <div class="row">
                    <?php
                    if ( !empty($webinar_data->lp_banner_image) ) {
                        echo "<img src='$webinar_data->lp_banner_image' />";
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        $html = ob_get_clean();
    }

    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_lp_main_headline($webinar_data, $display = false) {
    if (empty($webinar_data->lp_main_headline)) {
        $html = '';
    } else {
        ob_start();
        webinarignition_display( $webinar_data->lp_main_headline, '' );
        $html = ob_get_clean();
    }

    if (!$display) return $html;
    echo $html;
}

/**
 * @param $webinar_data
 * @param false $display
 * @param null $content_width
 *
 * @return false|string
 */
function webinarignition_get_video_area($webinar_data, $display = false, $content_width = null) {
    $uid = wp_unique_id( $prefix = 'ctaAreaVideo-' );
    $assets         = WEBINARIGNITION_URL . "inc/lp/";

    ob_start();
    ?>
    <div class="ctaArea video <?php echo $uid ?>">
        <?php
        if(!empty($content_width) && has_shortcode( $webinar_data->lp_cta_video_code, 'video') ){
            $GLOBALS['content_width'] = $content_width;//see /wp-includes/media.php::wp_video_shortcode();
        }
        ?>

        <?php
        if ( $webinar_data->lp_cta_type == "" || $webinar_data->lp_cta_type == "video" ) {

            if( !empty($webinar_data->lp_cta_video_url) ) {
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
                        //Workaround: Play the video programmatically, few seconds after player is ready,
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
                        <source src="<?php echo $webinar_data->lp_cta_video_url; ?>" type='video/mp4'/>
                    </video>
                    <button class="wi_videoPlayerMute"><img src="<?php echo $assets . 'images/mute.svg' ?>" /></button>
                </div>
            <?php
            } else {
	            webinarignition_display(
		            do_shortcode( $webinar_data->lp_cta_video_code ),
		            '<img src="' . $assets . 'images/novideo.png" />'
	            );
            }
        } else {
            echo "<img src='";
            webinarignition_display( $webinar_data->lp_cta_image, $assets . 'images/noctaimage.png' );
            echo "' height='281' width='500' />";
        }
        ?>
    </div>
    <?php
    $html = ob_get_clean();

    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_lp_optin_headline($webinar_data, $display = false) {
    $html = webinarignition_get_lp_block_template($webinar_data, 'optin-headline.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_lp_sales_headline($webinar_data, $display = false) {
    $html = webinarignition_get_lp_block_template($webinar_data, 'sales-headline.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_lp_sales_copy($webinar_data, $display = false) {
    WebinarignitionManager::set_locale($webinar_data);
    ob_start();

    ?>
    <div class="wiSalesCopy">
        <?php
        webinarignition_display(
            do_shortcode( $webinar_data->lp_sales_copy ),
            '<p>'.__('Your Amazing sales copy for your webinar would show up here...', 'webinarignition').'</p>'
        );
        ?>
    </div>
    <?php

    $html = ob_get_clean();
	WebinarignitionManager::restore_locale($webinar_data);
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_lp_optin_section($webinar_data, $display = false) {
    $html = webinarignition_get_lp_block_template($webinar_data, 'optin-section.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_lp_optin_form($webinar_data, $display = false) {
    $html = webinarignition_get_lp_block_template($webinar_data, 'optin-form.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_lp_optin_form_compact($webinar_data, $display = false) {
    $html = webinarignition_get_lp_block_template($webinar_data, 'optin-form.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_generate_optin_form($webinar_data, $display = false) {
    $html = webinarignition_get_lp_block_template($webinar_data, 'optin-form-generate.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_lp_event_dates($webinar_data, $display = false) {
    ob_start();

    if ( $webinar_data->webinar_date != "AUTO" && $webinar_data->paid_status == "paid" ) {
        $paid_check = "no";
        ?>
        <script> var paid_code = {"code": <?php echo "'" . $webinar_data->paid_code . "'"; ?>} </script>
        <?php
    } else {
        $paid_check = "yes";
    }

// check if campaign ID is in the URL, if so, its the thank you url...
    if ( is_object($webinar_data) && property_exists( $webinar_data, 'paid_code' ) && isset( $input_get[ $webinar_data->paid_code ] ) ) {
        $paid_check = "yes";
    }

    if ( is_object($webinar_data) && $webinar_data->webinar_date == "AUTO" ) {
        // Evergreen
        if ( $paid_check == 'yes' ) {
            webinarignition_get_lp_auto_event_dates($webinar_data, false, true);
        } else {
            ?>
            <div class="autoSep"></div>
            <?php
        }
    } else {
        webinarignition_get_lp_live_event_dates($webinar_data, false, true);
    }
    $html = ob_get_clean();

    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_lp_event_dates_compact($webinar_data, $display = false) {
    ob_start();

    if ( $webinar_data->webinar_date != "AUTO" && $webinar_data->paid_status == "paid" ) {
        $paid_check = "no";
        ?>
        <script> var paid_code = {"code": <?php echo "'" . $webinar_data->paid_code . "'"; ?>} </script>
        <?php
    } else {
        $paid_check = "yes";
    }

// check if campaign ID is in the URL, if so, its the thank you url...
    if (is_object($webinar_data) && property_exists( $webinar_data, 'paid_code' ) && isset( $input_get[ $webinar_data->paid_code ] ) ) {
        $paid_check = "yes";
    }

    if ( is_object($webinar_data) &&  $webinar_data->webinar_date == "AUTO" ) {
        // Evergreen
        if ( $paid_check == 'yes' ) {
            webinarignition_get_lp_auto_event_dates($webinar_data, true, true);
        } else {
            ?>
            <div class="autoSep"></div>
            <?php
        }
    } else {
        webinarignition_get_lp_live_event_dates($webinar_data, true, true);
    }
    $html = ob_get_clean();

    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_lp_auto_event_dates($webinar_data, $is_compact = false, $display = false) {
    $uid = wp_unique_id( $prefix = 'eventDate-' );
//	WebinarignitionManager::set_locale($webinar_data);
    ob_start();
    if ( $webinar_data->lp_schedule_type == 'fixed' ) {
        require WEBINARIGNITION_PATH . "inc/lp/partials/registration_page/fixed-dates.php";
    } elseif ( $webinar_data->lp_schedule_type == 'delayed' ) {
        require WEBINARIGNITION_PATH . "inc/lp/partials/registration_page/delayed-dates.php";
    } else {
        require WEBINARIGNITION_PATH . "inc/lp/partials/registration_page/custom-dates.php";
    }
    $html = ob_get_clean();
//	WebinarignitionManager::restore_locale($webinar_data);
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_lp_live_event_dates($webinar_data, $is_compact = false, $display = false) {
    
    global $wp_locale;
//	WebinarignitionManager::set_locale($webinar_data);
    if( !empty($webinar_data->time_format ) && ( $webinar_data->time_format == '12hour' || $webinar_data->time_format == '24hour'  ) ){ //old formats
        $webinar_data->time_format = get_option( "time_format", 'H:i' );
    }    

    $uid                    = wp_unique_id( $prefix = 'eventDate-' );
    $date_format            = !empty($webinar_data->date_format ) ? $webinar_data->date_format  : get_option( "date_format");
    $time_format            = $webinar_data->time_format;
    $webinarDateObject      = DateTime::createFromFormat( 'm-d-Y', $webinar_data->webinar_date);
    if( $webinarDateObject instanceof DateTime ){
        $webinarTimestamp       = $webinarDateObject->getTimestamp();    
        $localized_date         = date_i18n( $date_format, $webinarTimestamp);    
        $localized_month        = $wp_locale->get_month( $webinarDateObject->format('m') ); 
        $localized_week_day     = $wp_locale->get_weekday( $webinarDateObject->format('w') );        
    }

    ob_start();
    if( $webinarDateObject instanceof DateTime ):
        if ( $is_compact )  {
            require WEBINARIGNITION_PATH . "inc/lp/partials/registration_page/live-dates-compact.php";
        } else {
            require WEBINARIGNITION_PATH . "inc/lp/partials/registration_page/live-dates.php";
        }
    endif;
//	WebinarignitionManager::restore_locale($webinar_data);
    $html = ob_get_clean();

    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_lp_host_info($webinar_data, $display = false) {
    $html = webinarignition_get_lp_block_template($webinar_data, 'host-info.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_lp_block_template($webinar_data, $path) {
    extract(webinarignition_get_lp_templates_vars($webinar_data));
    ob_start();
	WebinarignitionManager::set_locale($webinar_data);
    require_once WEBINARIGNITION_PATH . "inc/lp/partials/registration_page/{$path}";
	WebinarignitionManager::restore_locale($webinar_data);
    return ob_get_clean();
}

function webinarignition_get_lp_templates_vars($webinar_data) {
    global $webinarignition_lp_templates_vars;
    if (empty($webinarignition_lp_templates_vars)) $webinarignition_lp_templates_vars = array();

    $webinarignition_lp_templates_vars = array_merge(
        webinarignition_get_global_templates_vars($webinar_data),
        $webinarignition_lp_templates_vars
    );

    /**
     * @var $input_get
     * @var $is_preview
     * @var $webinar_id
     * @var $webinarId
     * @var $data
     * @var $isAuto
     * @var $pluginName
     * @var $leadinfo
     * @var $assets
     */
    extract($webinarignition_lp_templates_vars);

    if (
            !isset($webinarignition_lp_templates_vars['paid_check']) ||
            !isset($webinarignition_lp_templates_vars['paid_check_js'])
    ) {
        $paid_check_js = '';

        if ( $webinar_data->paid_status == "paid" ) {
            $paid_check = "no";
            ob_start();
            ?>
            <script> var paid_code = {"code": <?php echo "'" . $webinar_data->paid_code . "'"; ?>} </script>
            <?php
            $paid_check_js = ob_get_clean();
        } else {
            $paid_check = "yes";
        }

        // check if campaign ID is in the URL, if so, its the thank you url...
        if (is_object($webinar_data) && property_exists( $webinar_data, 'paid_code' ) && isset( $input_get[ $webinar_data->paid_code ] ) ) {
            $paid_check = "yes";
        }

        $webinarignition_lp_templates_vars['paid_check'] = $paid_check;
        $webinarignition_lp_templates_vars['paid_check_js'] = $paid_check_js;
    }

    if (
        !isset($webinarignition_lp_templates_vars['loginUrl']) ||
        !isset($webinarignition_lp_templates_vars['user_info'])
    ) {
        $user_info = array();
        $loginUrl = '';

        if( !empty($webinar_data->fb_id) && !empty($webinar_data->fb_secret)  ) {
            include( WEBINARIGNITION_PATH . "inc/lp/fbaccess.php" );
            /**
             * @var $user_info
             */
            $isSigningUpWithFB                      = true;
            $fbUserData['name']                     = $user_info['name'];
            $fbUserData['email']                    = $user_info['email'];
        }

        $webinarignition_lp_templates_vars['loginUrl'] = $loginUrl;
        $webinarignition_lp_templates_vars['user_info'] = $user_info;
    }

    return $webinarignition_lp_templates_vars;
}
/**
 * @param $webinar_data
 * @param false $display
 *
 * @return false|string
 */
function webinarignition_get_lp_arintegration($webinar_data, $display = false) {
    $html = '';

//    if( !empty($webinar_data->ar_url) && !empty($webinar_data->ar_method)) {
//        ob_start();
//        ?>
<!--        <div class="arintegration" style="display:none;">-->
<!--            --><?php //include(WEBINARIGNITION_PATH . "inc/lp/ar_form.php"); ?>
<!--        </div>-->
<!--        --><?php
//        $html = ob_get_clean();
//    }

    if (!$display) return $html;
    echo $html;
}
#endregion

#--------------------------------------------------------------------------------
#region ThankYou Page
#--------------------------------------------------------------------------------
function webinarignition_get_ty_banner($webinar_data, $display = false) {
    $html = webinarignition_get_lp_banner($webinar_data);
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_headline($webinar_data, $display = false) {
    $html = webinarignition_get_ty_block_template($webinar_data, 'ty-headline.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_message_area($webinar_data, $display = false) {
    $html = webinarignition_get_ty_block_template($webinar_data, 'ty_message_area.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_reminders_block($webinar_data, $display = false) {
    /**
     * @var $instantTest
     */
    extract(webinarignition_get_ty_templates_vars($webinar_data));
    ob_start();
    ?>
    <div class="remindersBlock" <?php echo $instantTest; ?> >
        <?php webinarignition_get_ty_calendar_reminder($webinar_data, true); ?>

        <!-- PHONE REMINDER -->
        <?php webinarignition_get_ty_sms_reminder($webinar_data, true); ?>
    </div>
    <?php
    $html = ob_get_clean();
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_calendar_reminder($webinar_data, $display = false) {
	if( webinarignition_is_instant_lead($webinar_data) ) return;

    $html = webinarignition_get_ty_block_template($webinar_data, 'ty-calendar-reminder.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_calendar_reminder_google($webinar_data, $display = false) {
    /**
     * @var $leadId
     * @var $is_preview
     * @var $input_get
     */
    extract(webinarignition_get_ty_templates_vars($webinar_data));

    $googleCalendarURL = '#';
	if( !WebinarignitionManager::url_is_preview_page() ) { //If not preview page
        $calendarType = 'googlecalendar';
        if(webinarignition_is_auto($webinar_data)) {
            $calendarType .= 'A';
        }

        $thankyou_URL = WebinarignitionManager::get_permalink($webinar_data,'thank_you');
        $googleCalendarURL = add_query_arg([$calendarType => '', 'lid' => $leadId, 'id' => $leadId], $thankyou_URL);
    }

    ob_start();
    ?>
    <a href="<?php echo $googleCalendarURL; ?>" target="_blank"><?php webinarignition_display( $webinar_data->ty_calendar_google, __( "Google Calendar", "webinarignition") ); ?></a>
    <?php
    $html = ob_get_clean();
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_calendar_reminder_outlook($webinar_data, $display = false) {
    /**
     * @var $leadId
     * @var $is_preview
     * @var $input_get["webinar"]
     */
    extract(webinarignition_get_ty_templates_vars($webinar_data));

	$iCalendarURL = '#';
	if( !WebinarignitionManager::url_is_preview_page() ) { //If not preview page
		$calendarType = 'ics';
		if(webinarignition_is_auto($webinar_data)) {
			if( $lead && $lead->trk8 === 'yes') {
			    return; //Skip rendering for instant leads
			}

			$calendarType .= 'A';
		}

		$thankyou_URL = WebinarignitionManager::get_permalink($webinar_data,'thank_you');
		$iCalendarURL = add_query_arg([$calendarType => '', 'lid' => $leadId, 'id' => $leadId], $thankyou_URL);
	}

    ob_start();
	?>
    <a href="<?php echo $iCalendarURL; ?>" target="_blank"><?php webinarignition_display( $webinar_data->ty_calendar_ical, __('iCal / Outlook', 'webinarignition') ); ?></a>
    <?php
    $html = ob_get_clean();
    if (!$display) return $html;
    echo $html;
}

/**
 * Check if current webinar lead date is instant access
 *
 * @param $webinar_data
 *
 * @return bool
 */
function webinarignition_is_instant_lead($webinar_data) {
	if( isset($webinar_data->webinar_date) && $webinar_data->webinar_date === 'AUTO' ) {
		extract( webinarignition_get_ty_templates_vars( $webinar_data ) );
		return ( isset( $lead ) && isset( $lead->trk8 ) && $lead->trk8 === 'yes' );
	}

	return false;
}

function webinarignition_get_ty_sms_reminder($webinar_data, $display = false) {
	if( webinarignition_is_instant_lead($webinar_data) ) return;

	$html = webinarignition_get_ty_block_template($webinar_data, 'ty-sms-reminder.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_sms_reminder_compact($webinar_data, $display = false) {
	if( webinarignition_is_instant_lead($webinar_data) ) return;

    $html = webinarignition_get_ty_block_template($webinar_data, 'ty-sms-reminder-compact.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_webinar_url($webinar_data, $display = false) {
    $html = webinarignition_get_ty_block_template($webinar_data, 'ty-webinar-url.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_webinar_url_inline($webinar_data, $display = false) {
    $html = webinarignition_get_ty_block_template($webinar_data, 'ty-webinar-url-inline.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_ticket_date($webinar_data, $display = false) {
    $html = webinarignition_get_ty_block_template($webinar_data, 'ty-ticket-date.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_date_time_inline($webinar_data, $display = false) {
    /**
     * @var $is_preview
     * @var $autoDate_format
     * @var $autoTime
     * @var $isAuto
     * @var $autoTimeNoTZ
     */
    extract(webinarignition_get_ty_templates_vars($webinar_data));

    ob_start();
    echo $autoDate_format;
    echo ' ' . $autoTimeNoTZ;
    $html = ob_get_clean();
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_date_inline($webinar_data, $display = false) {
    /**
     * @var $autoDate_format
     */
    extract(webinarignition_get_ty_templates_vars($webinar_data));

    ob_start();
    echo $autoDate_format;
    $html = ob_get_clean();
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_time_inline($webinar_data, $display = false) {
    /**
     * @var $autoTimeNoTZ
     * @var $autoTime
     * @var $isAuto
     */
    extract(webinarignition_get_ty_templates_vars($webinar_data));

    ob_start();
    echo $autoTimeNoTZ;
    $html = ob_get_clean();

    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_timezone_inline($webinar_data, $display = false) {
    /**
     * @var $autoTimeTZ
     * @var $autoTimeNoTZ
     * @var $autoTime
     * @var $isAuto
     */
    extract(webinarignition_get_ty_templates_vars($webinar_data));

    ob_start();
    echo $webinar_data->webinar_timezone . ' ' . $autoTimeTZ;
    $html = ob_get_clean();
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_ticket_webinar($webinar_data, $display = false) {
    $html = webinarignition_get_ty_block_template($webinar_data, 'ty-ticket-webinar.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_ticket_webinar_inline($webinar_data, $display = false) {
    $html = webinarignition_get_ty_block_template($webinar_data, 'ty-ticket-webinar-inline.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_ticket_host($webinar_data, $display = false) {
    $html = webinarignition_get_ty_block_template($webinar_data, 'ty-ticket-host.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_ticket_host_inline($webinar_data, $display = false) {
    $html = webinarignition_get_ty_block_template($webinar_data, 'ty-ticket-host-inline.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_countdown($webinar_data, $display = false) {
    $html = webinarignition_get_ty_block_template($webinar_data, 'ty-countdown.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_countdown_compact($webinar_data, $display = false) {
    $html = webinarignition_get_ty_block_template($webinar_data, 'ty-countdown-compact.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_share_gift($webinar_data, $display = false) {
    if ('none' === $webinar_data->ty_share_toggle) {
        $html = '';
    } else {
        $html = webinarignition_get_ty_block_template($webinar_data, 'ty-share-gift.php');
    }

    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_share_gift_compact($webinar_data, $display = false) {
    if ('none' === $webinar_data->ty_share_toggle) {
        $html = '';
    } else {
        $html = webinarignition_get_ty_block_template($webinar_data, 'ty-share-gift-compact.php');
    }

    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_ty_block_template($webinar_data, $path) {
    extract(webinarignition_get_ty_templates_vars($webinar_data));
    ob_start();
    require_once WEBINARIGNITION_PATH . "inc/lp/partials/thank_you_page/{$path}";
    return ob_get_clean();
}

function webinarignition_get_ty_templates_vars($webinar_data) {
    global $webinarignition_ty_templates_vars;
    $date_format    = !empty($webinar_data->date_format ) ? $webinar_data->date_format  : get_option( "date_format" );
    if( !empty($webinar_data->time_format ) && ( $webinar_data->time_format == '12hour' || $webinar_data->time_format == '24hour'  ) ){ //old formats
        $webinar_data->time_format = get_option( "time_format", 'H:i' );
    }      

    if (empty($webinarignition_ty_templates_vars)) $webinarignition_ty_templates_vars = array();

    $webinarignition_ty_templates_vars = array_merge(
        webinarignition_get_global_templates_vars($webinar_data),
        $webinarignition_ty_templates_vars
    );

    /**
     * @var $input_get
     * @var $is_preview
     * @var $webinar_id
     * @var $webinarId
     * @var $data
     * @var $isAuto
     * @var $pluginName
     * @var $leadinfo
     * @var $assets
     */
    extract($webinarignition_ty_templates_vars);

    if (!isset($webinarignition_ty_templates_vars['instantTest'])) {
        $instantTest  = "";

        if ( $isAuto && !empty($lead) && !empty($lead->trk8) && $lead->trk8 == "yes")
            $instantTest = "style='display:none;'";

        $webinarignition_ty_templates_vars['instantTest'] = $instantTest;
    }

    if (
            !isset($webinarignition_ty_templates_vars['autoTZ']) ||
            !isset($webinarignition_ty_templates_vars['autoDate_format']) ||
            !isset($webinarignition_ty_templates_vars['autoTime']) ||
            !isset($webinarignition_ty_templates_vars['liveEventMonth']) ||
            !isset($webinarignition_ty_templates_vars['liveEventDateDigit'])
    ) {
        if ( $isAuto ) {
            $autoDate_format = webinarignition_display_date( $webinar_data, $lead );
            $autoTime        = webinarignition_display_time( $webinar_data, $lead );
            $autoTimeNoTZ        = webinarignition_display_time( $webinar_data, $lead, false );
            $liveEventMonth     = webinarignition_event_month( $webinar_data, $lead );
            $liveEventDateDigit = webinarignition_event_day( $webinar_data, $lead );
            $autoTZ = false;

            $webinarignition_ty_templates_vars['autoTimeNoTZ'] = $autoTimeNoTZ;
        } else {

            $autoDate_format = webinarignition_get_translated_date( $webinar_data->webinar_date, 'm-d-Y', $date_format );
            $time_format     = $webinar_data->time_format;
            
            $autoTime_format = $webinar_data->webinar_start_time;
            $timeonly        = ( empty($webinar_data->display_tz ) || ( !empty($webinar_data->display_tz) && ($webinar_data->display_tz == 'yes') ) ) ? false : true;
            $autoTime        = webinarignition_get_time_tz( $autoTime_format, $time_format, $webinar_data->webinar_timezone, false, $timeonly );
            $autoTimeNoTZ    = webinarignition_get_time_tz( $autoTime_format, $time_format, $webinar_data->webinar_timezone, false, $timeonly );
            $autoTimeTZ      = webinarignition_get_time_tz( $autoTime_format, $time_format, $webinar_data->webinar_timezone, true, $timeonly );

            $webinarignition_ty_templates_vars['autoTimeNoTZ'] = $autoTimeNoTZ;
            $webinarignition_ty_templates_vars['autoTimeTZ'] = $autoTimeTZ;

            $dtz           = new DateTimeZone( $webinar_data->webinar_timezone );
            $time_in_sofia = new DateTime( 'now', $dtz );
            $autoTZ        = $dtz->getOffset( $time_in_sofia ) / 3600;
            $autoTZ        = ( $autoTZ < 0 ? $autoTZ : "+" . $autoTZ );
        }

        $webinarignition_ty_templates_vars['autoTZ'] = $autoTZ;
        $webinarignition_ty_templates_vars['autoDate_format'] = $autoDate_format;
        $webinarignition_ty_templates_vars['autoTime'] = $autoTime;

    }

    return $webinarignition_ty_templates_vars;
}
#endregion

#--------------------------------------------------------------------------------
#region Countdown page
#--------------------------------------------------------------------------------
function webinarignition_get_countdown_main_headline($webinar_data, $display = false) {
    $html = webinarignition_get_countdown_block_template($webinar_data, 'main-headline-area.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_countdown_headline($webinar_data, $display = false) {
    $html = webinarignition_get_countdown_block_template($webinar_data, 'headline-area.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_countdown_counter($webinar_data, $display = false) {
    $html = webinarignition_get_countdown_block_template($webinar_data, 'counter.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_countdown_signup($webinar_data, $display = false) {
    $html = webinarignition_get_countdown_block_template($webinar_data, 'signup-area.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_countdown_block_template($webinar_data, $path) {
    extract(webinarignition_get_countdown_templates_vars($webinar_data));
    ob_start();
    require_once WEBINARIGNITION_PATH . "inc/lp/partials/countdown_page/{$path}";
    return ob_get_clean();
}

function webinarignition_get_countdown_templates_vars($webinar_data) {
    global $webinarignition_countdown_templates_vars;

    if (empty($webinarignition_countdown_templates_vars)) $webinarignition_countdown_templates_vars = array();

    $webinarignition_countdown_templates_vars = array_merge(
        webinarignition_get_global_templates_vars($webinar_data),
        $webinarignition_countdown_templates_vars
    );

    return $webinarignition_countdown_templates_vars;
}
#endregion

#--------------------------------------------------------------------------------
#region Replay page
#--------------------------------------------------------------------------------
function webinarignition_get_replay_main_headline($webinar_data, $display = false) {
    $html = webinarignition_get_replay_block_template($webinar_data, 'main-headline-area.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_replay_video_under_cta($webinar_data, $display = false) {
    $html = webinarignition_get_replay_block_template($webinar_data, 'webinar-cta.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_replay_video($webinar_data, $display = false) {
    $html = webinarignition_get_replay_block_template($webinar_data, 'webinar-video.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_replay_info($webinar_data, $display = false) {
    $html = webinarignition_get_replay_block_template($webinar_data, 'webinar-info.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_replay_giveaway($webinar_data, $display = false) {
    $html = webinarignition_get_replay_block_template($webinar_data, 'webinar-giveaway.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_replay_headline($webinar_data, $display = false) {
    $html = webinarignition_get_replay_block_template($webinar_data, 'headline-area.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_replay_block_template($webinar_data, $path) {
    extract(webinarignition_get_replay_templates_vars($webinar_data));
    ob_start();
    require_once WEBINARIGNITION_PATH . "inc/lp/partials/replay_page/{$path}";
    return ob_get_clean();
}

function webinarignition_get_replay_templates_vars($webinar_data) {
    global $webinarignition_replay_templates_vars;

    if (empty($webinarignition_replay_templates_vars)) $webinarignition_replay_templates_vars = array();

    $webinarignition_replay_templates_vars = array_merge(
        webinarignition_get_global_templates_vars($webinar_data),
        $webinarignition_replay_templates_vars
    );

    return $webinarignition_replay_templates_vars;
}
#endregion

#--------------------------------------------------------------------------------
#region Closed page
#--------------------------------------------------------------------------------
function webinarignition_get_closed_headline($webinar_data, $display = false) {
    $html = webinarignition_get_closed_block_template($webinar_data, 'headline-area.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_closed_block_template($webinar_data, $path) {
    extract(webinarignition_get_closed_templates_vars($webinar_data));
    ob_start();
    require_once WEBINARIGNITION_PATH . "inc/lp/partials/closed_page/{$path}";
    return ob_get_clean();
}

function webinarignition_get_closed_templates_vars($webinar_data) {
    global $webinarignition_closed_templates_vars;
    $input_get = filter_input_array( INPUT_GET );

    if (empty($webinarignition_closed_templates_vars)) {
        $webinarignition_closed_templates_vars = array();
    }

    return $webinarignition_closed_templates_vars;
}
#endregion

#--------------------------------------------------------------------------------
#region Webinar page
#--------------------------------------------------------------------------------
function webinarignition_get_webinar_video_cta_comb($webinar_data, $display = false) {
	set_query_var( 'webinarignition_page' ,'webinar' );
	set_query_var( 'webinar_data' ,$webinar_data );
    $html = webinarignition_get_webinar_block_template($webinar_data, 'webinar-video-cta.php');
    if (!$display) return $html;
    echo $html;
}
//new sidebar section
function webinarignition_get_webinar_video_cta_sidebar($webinar_aside, $display = false) {
	set_query_var( 'webinarignition_page' ,'webinar' );
	// set_query_var( 'webinar_data' ,$webinar_data );
    $html = webinarignition_get_webinar_block_template($webinar_aside, 'webinar-video-cta-sidebar.php');
    if (!$display) return $html;
    echo $html;
}
function webinarignition_get_webinar_video_cta($webinar_data, $display = false) {
	set_query_var( 'webinarignition_page' ,'webinar' );
	set_query_var( 'webinar_data' ,$webinar_data );
    $html = webinarignition_get_webinar_block_template($webinar_data, 'webinar-video.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_webinar_sidebar($webinar_data, $display = false) {
	set_query_var( 'webinarignition_page' ,'webinar' );
	set_query_var( 'webinar_data' ,$webinar_data );
    $html = webinarignition_get_webinar_block_template($webinar_data, 'webinar-sidebar.php' );
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_webinar_video_under_cta($webinar_data, $display = false) {
    $html = webinarignition_get_webinar_block_template($webinar_data, 'webinar-cta.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_webinar_video_under_overlay_cta($webinar_data, $display = false) {
    $html = webinarignition_get_webinar_block_template($webinar_data, 'webinar-overlay-cta.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_webinar_info($webinar_data, $display = false) {
    $html = webinarignition_get_webinar_block_template($webinar_data, 'webinar-info.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_webinar_giveaway($webinar_data, $display = false) {
    $html = webinarignition_get_webinar_block_template($webinar_data, 'webinar-giveaway.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_webinar_qa($webinar_data, $display = false) {
    $html = webinarignition_get_webinar_block_template($webinar_data, 'webinar-qa.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_webinar_qa_compact($webinar_data, $display = false) {
    $html = webinarignition_get_webinar_block_template($webinar_data, 'webinar-qa-compact.php');
    if (!$display) return $html;
    echo $html;
}

function webinarignition_get_webinar_block_template($webinar_data, $path) {
    extract(webinarignition_get_webinar_templates_vars($webinar_data));
    ob_start();
    require_once WEBINARIGNITION_PATH . "inc/lp/partials/webinar_page/{$path}";
    return ob_get_clean();
}

function webinarignition_get_webinar_templates_vars($webinar_data) {
    global $webinarignition_webinar_templates_vars;

    if (empty($webinarignition_webinar_templates_vars)) {
        $webinarignition_webinar_templates_vars = array();
    }

    $webinarignition_webinar_templates_vars = array_merge(
        webinarignition_get_global_templates_vars($webinar_data),
        $webinarignition_webinar_templates_vars
    );

    /**
     * @var $input_get
     * @var $webinar_id
     * @var $webinarId
     * @var $data
     * @var $isAuto
     * @var $pluginName
     * @var $leadinfo
     * @var $assets
     */
    extract($webinarignition_webinar_templates_vars);

    if (!isset($webinarignition_webinar_templates_vars['individual_offset'])) {
        global $wpdb;
        $individual_offset = 0;

        if ( $webinar_data->webinar_date == "AUTO" ):
            $evergreen_leads_table = $wpdb->prefix . "webinarignition_leads_evergreen";
            $individual_offset     = 0;

            if ( ! empty( $input_get["lid"] ) && (int) $input_get["lid"] !== 0 ) {
                $lead_row          = $wpdb->get_row( $wpdb->prepare( "SELECT date_picked_and_live FROM {$evergreen_leads_table} WHERE id = %d ", $leadinfo->ID ) );
                $st_timestamp      = strtotime( $lead_row->date_picked_and_live );
                $individual_offset = time() - $st_timestamp;
            }
        endif;

        $webinarignition_webinar_templates_vars['individual_offset'] = $individual_offset;
    }

    if (!isset($webinarignition_webinar_templates_vars['webinarignition_page'])) {
        $webinarignition_webinar_templates_vars['webinarignition_page'] = 'webinar';
    }

    return $webinarignition_webinar_templates_vars;
}
#endregion

#--------------------------------------------------------------------------------
#region Template vars
#--------------------------------------------------------------------------------
function webinarignition_get_global_templates_vars($webinar_data) {
    global $webinarignition_global_templates_vars;
    if (empty($webinarignition_global_templates_vars)) {
        $webinarignition_global_templates_vars = array();
    }

    $input_get = filter_input_array( INPUT_GET );
    extract($webinarignition_global_templates_vars);

    $webinarignition_global_templates_vars['input_get'] = $input_get;

    if (!isset($is_preview)) {
        $is_preview = WebinarignitionManager::url_is_preview_page();
        $webinarignition_global_templates_vars['is_preview'] = $is_preview;
    }

    if (empty($webinarId) || empty($webinar_id)) {
        $webinarId = $webinar_data->id;
        $webinarignition_global_templates_vars['webinarId'] = $webinarId;
        $webinarignition_global_templates_vars['webinar_id'] = $webinarId;
    }

    if (!isset($is_webinar_available)) {
        $webinarignition_global_templates_vars['is_webinar_available'] = WebinarignitionLicense::is_webinar_available($webinarId, $webinar_data);
    }

    if (!isset($data)) {
        global $wpdb;
        $db_table_name = $wpdb->prefix . "webinarignition";
        $data          = $wpdb->get_row( "SELECT * FROM {$db_table_name} WHERE id = {$webinarId}", OBJECT );
        $webinarignition_global_templates_vars['data'] = $data;
    }

    if (!isset($isAuto)) {
        $isAuto = webinarignition_is_auto( $webinar_data );
        $webinarignition_global_templates_vars['isAuto'] = $isAuto;
    }

    if (empty($pluginName)) $webinarignition_global_templates_vars['pluginName'] = "webinarignition";

    if ( !isset($leadId) || !isset($lead) ) {
        if (!empty($is_preview)) {
            $lead = WebinarignitionPowerupsShortcodes::get_dummy_lead($webinar_data);
            $leadId = $lead->ID;
        } else {
            $lead = false;
            $leadId = '';

            if (!empty($input_get['lid'])) $leadId = $input_get['lid'];

            if ( empty( $leadId ) && ! empty( $input_get['email'] ) ) {
                $is_lead_protected = !empty($webinar_data->protected_lead_id) && 'protected' === $webinar_data->protected_lead_id;
                $getLiveIDByEmail = webinarignition_live_get_lead_by_email( $webinarId, $input_get['email'], $is_lead_protected );
                $leadId           = $getLiveIDByEmail->ID;
            }

	        /*if ( ! empty( $_COOKIE[ 'we-trk-' . $webinarId ] ) ) {
		        $leadId = ! empty( $input_get['lid'] ) ? $input_get['lid'] : $_COOKIE[ 'we-trk-' . $webinarId ];
	        }*/

            if ( ! empty( $leadId ) )$lead = webinarignition_get_lead_info($leadId, $webinar_data);
        }

        $webinarignition_global_templates_vars['leadId'] = $leadId;
        $webinarignition_global_templates_vars['lead'] = $lead;
    }

    if (!isset($leadinfo)) {
        $leadinfo = $lead;
        $webinarignition_global_templates_vars['leadinfo'] = $lead;
    }

    if (!isset($webinar_status) && !empty($lead)) {
        $webinarignition_global_templates_vars['webinar_status'] = webinarignition_get_lead_status($webinar_data,$lead);
    }

    if (empty($assets)) $webinarignition_global_templates_vars['assets'] = WEBINARIGNITION_URL . "inc/lp/";

    return $webinarignition_global_templates_vars;
}
#endregion

/**
 * Get lead status (countdown/live/replay/closed) based on lead datetime
 *
 * @param $webinar_id
 * @param $lead_id
 *
 * @return string|void
 */
function webinarignition_get_lead_status($webinar_data, $lead = null) {

	if( empty($webinar_data) || ( webinarignition_is_auto($webinar_data) && empty($lead) ) ) { //lead is required only for auto webinar
		return; //bail here
	}

	$lead_status = 'countdown';
    $watch_type = 'live';

	if( !empty(filter_input( INPUT_GET, 'watch_type', FILTER_SANITIZE_SPECIAL_CHARS )) ) {
        $watch_type = sanitize_text_field( trim(filter_input( INPUT_GET, 'watch_type', FILTER_SANITIZE_SPECIAL_CHARS )) );
    }

    if( webinarignition_is_auto($webinar_data) ) {

        //Get lead status from the URL when pre-viewing
	    if( WebinarignitionManager::url_is_preview_page() ) {
		    $input_get = filter_input_array( INPUT_GET );
	        $lead_status = 'countdown';
		    if( isset($input_get['countdown']) ) {
			    $input_get['live'] = 1;
			    $lead_status = 'countdown';
		    } elseif( isset($input_get['webinar']) ) {
			    $lead_status = 'live';
		    } elseif( isset($input_get['replay']) ) {
			    $lead_status = 'replay';
		    } else {
			    $lead_status = '';
		    }
	    } else {

		    $webinar_timezone = webinarignition_get_webinar_timezone( $webinar_data, null, $lead );
		    $datetime_lead    = date_create( $lead->date_picked_and_live, new DateTimeZone( $webinar_timezone ) );

		    $video_length_in_minutes = absint( $webinar_data->auto_video_length );
		    if ( empty( $video_length_in_minutes ) ) {
			    $video_length_in_minutes = 60;
		    }

		    $replay_length_in_days = sanitize_text_field( $webinar_data->auto_replay ) == '' ? 3 : absint( $webinar_data->auto_replay );

		    if ( $datetime_lead ) { //If valid datetime provided

			    $datetime_now = date_create( 'now', new DateTimeZone( $webinar_timezone ) );

			    //Live start timestamp
			    $lead_live_start_ts = $datetime_lead->getTimestamp();

			    //Live end timestamp
			    $datetime_lead->modify( "+$video_length_in_minutes minutes" );
			    $lead_live_end_ts = $datetime_lead->getTimestamp();

			    //Replay end timestamp
			    $datetime_lead->modify( "+$replay_length_in_days days" );
			    $datetime_lead->modify( "-$video_length_in_minutes minutes" );
			    $lead_replay_end_ts = $datetime_lead->getTimestamp();

			    $lead_live_started = $datetime_now->getTimestamp() > $lead_live_start_ts;
			    $lead_live_ended   = $datetime_now->getTimestamp() > $lead_live_end_ts;
			    $lead_replay_ended = $datetime_now->getTimestamp() > $lead_replay_end_ts;

			    if ( $lead_replay_ended || $lead->lead_status === 'watched' ) {
				    $lead_status = 'closed';
			    } else if ( $lead_live_ended ) {
				    $lead_status = 'replay';
			    } else if ( $lead_live_started ) {
				    $lead_status = 'live';
			    }

			    //To avoid replay using the same URL
			    if ( in_array( $lead_status, [ 'live', 'replay' ] ) && $watch_type !== $lead_status ) {
				    // $lead_status = 'closed';
			    }
		    }
	    }

    } else {
        $lead_status = empty($webinar_data->webinar_switch) ? 'countdown' : trim($webinar_data->webinar_switch);
    }

    return $lead_status;
}

function webinarignition_showGDPRHeading($webinar) {
    global $wi_showingGDPRHeading;

    if (!$wi_showingGDPRHeading) {
        ?>
        <div class="gdprSectionWrapper">
        <div class="gdprHeading">
            <?= !empty($webinar->gdpr_heading) ? $webinar->gdpr_heading : __('Please confirm that you:', 'webinarignition') ?>
        </div>
        <?php
        $wi_showingGDPRHeading = true;
    }
}

function webinarignition_closeGDPRSection() {
    global $wi_showingGDPRHeading;

    if ($wi_showingGDPRHeading) {
        echo '</div>';
    }
}
