<?php
/**
 * @var $webinar_data
 */
defined( 'ABSPATH' ) || exit;

$statusCheck = WebinarignitionLicense::get_license_level();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        <?php
        if ( $webinar_data->meta_site_title_webinar == "" ) {
             webinarignition_display( $webinar_data->lp_metashare_title, __( "Amazing Webinar", "webinarignition") );
        } else {
            echo $webinar_data->meta_site_title_webinar;
        }

        ?>
    </title>
    <meta name="description" content="<?php
    if ( $webinar_data->meta_desc_webinar == "" ) {
        webinarignition_display( $webinar_data->lp_metashare_desc, __(  "Join this amazing webinar, and discover industry trade secrets!", "webinarignition") );
    } else {
        echo $webinar_data->meta_desc_webinar;
    }
    ?>">

    <?php if ( $webinar_data->ty_share_image != "" ) : ?>
        <meta property="og:image" content="<?php webinarignition_display( $webinar_data->ty_share_image, "" ) ?>"/>
    <?php endif ?>

    <?php wp_head(); ?>
</head>

<body class="webinar_page" id="webinarignition">

<?php if ( property_exists( $webinar_data, 'live_stats' ) ): ?>
    <?php if ( $webinar_data->live_stats != 'disabled' ) : ?>
        <div style="display: none;">
            <script src="<?php echo WEBINARIGNITION_URL; ?>inc/lp/livecounter.php?s&c"></script>
        </div>
    <?php endif ?>
<?php endif ?>

<!-- TOP AREA -->
<div class="topArea">
    <div class="bannerTop container">
        <?php if ( $webinar_data->webinar_banner_image != "" ) : ?>
            <img src='<?php echo $webinar_data->webinar_banner_image; ?>'/>
        <?php endif ?>
    </div>
</div>

<!-- Main Area -->
<div class="mainWrapper">
    <!-- WEBINAR WRAPPER -->
    <div class="webinarWrapper container">
        <!-- WEBINAR MAIN BLOCK LEFT -->
        <div class="webinarBlock">
            <!-- WEBINAR TOP AREA -->
            <div class="webinarTopArea">
                <div class="webinarSound" style="color: <?php webinarignition_display( $webinar_data->webinar_speaker_color, "#222" ); ?>;">
                    <i class="icon-volume-up"></i> <?php webinarignition_display( $webinar_data->webinar_speaker, __( "Turn Up Your Speakers", "webinarignition") ); ?>
                </div>

                <?php if ( $webinar_data->social_share_links !== 'disabled' ) {
                    require_once WEBINARIGNITION_PATH . 'inc/lp/partials/webinar_page/social_share_links.php';
                } ?>

                <br clear="all"/>
            </div>
            <!-- WEBINAR VIDEO -->
            <div class="webinarVideo wi_position_relative">
                <?php
                if( 'free' == $statusCheck->switch || $statusCheck->name == 'ultimate_powerup_tier1a' || empty( $statusCheck->switch ) ) {
                    require WEBINARIGNITION_PATH . 'inc/lp/partials/timeout_page/timeout-countdown-five-minutes.php';
                }
                ?>
                <div class="ctaArea">
                    <?php if ( ! empty( $webinar_data->webinar_iframe_source ) ):
                        if ( ( has_shortcode( $webinar_data->webinar_iframe_source, 'video' ) ) ): $GLOBALS['content_width'] = 1225; endif;
                    endif; ?>

                    <?php if ( ! empty( $webinar_data->webinar_live_video ) ):
                        if ( ( has_shortcode( $webinar_data->webinar_live_video, 'video' ) ) ): $GLOBALS['content_width'] = 1225; endif;
                    endif; ?>

                    <div id="vidBox"
                         class="<?= webinarignition_should_use_videojs( $webinar_data ) ? 'vidBoxjs' : '' ?>"
                         style="display:inline-block; position:absolute">
	                    <?php
	                    $is_preview = WebinarignitionManager::url_is_preview_page();
	                    if ( !$is_preview && $webinar_data->webinar_live_overlay == 1 && (!isset($webinar_data->webinar_live_video) || ! strpos($webinar_data->webinar_live_video, 'zoom')) ) : ?>
                            <!-- disable video controls -->
                            <div id="vidOvl" style="display:none;"></div>
	                    <?php endif ?>

                        <?php if ( $webinar_data->webinar_date == "AUTO" ) { ?>
                            <?php if ( webinarignition_should_use_videojs( $webinar_data ) ) : ?>
                                <div id="video-loading-block">
                                    <div id="video-loading-overlay" type="button" name="button"></div>
                                    <div id="video-loading-content-container">
                                        <img id="video-loading-spinner"
                                             src="<?php echo WEBINARIGNITION_URL; ?>images/ajax-loader.gif"/>
                                        <div id="video-loading-text"><?php _e( 'Joining Webinar', "webinarignition" ); ?></div>
                                    </div>
                                </div>

                                <div id="no-autoplay-block" style="display: none;">
                                    <div id="mobile-overlay" type="button" name="button"></div>
                                    <img id="mobile-play-button" src="<?php echo WEBINARIGNITION_URL; ?>images/play-button.png"
                                         alt=""/>
                                    <span id="mobile-play-button-text"><?php _e( 'Join Webinar', "webinarignition" ); ?></span>
                                </div>

                                <div id="muted-autoplay-block" style="display: none;">
                                    <div id="muted-overlay" type="button" name="button"></div>
                                    <div id="unmute-button">
                                        <img id="unmute-icon" src="<?php echo WEBINARIGNITION_URL; ?>images/unmute.png"
                                             alt=""/>
                                        <?php _e( 'Click for sound', "webinarignition" ); ?>
                                    </div>
                                </div>
                                <div class="autoWebinarLoading"
                                     style="z-index: 888888; background-color: rgba(0, 0, 0, 0.8); width: 100%; position:absolute; display: none">

                                    <div class="autoWebinarLoadingCopy">
                                        <i class="icon-spinner icon-spin icon-large autoWebinarLoader"></i>
                                        <br/>
                                        <p>
                                            <b><?php webinarignition_display( $webinar_data->auto_video_load, __( "Please Wait - The Webinar Is Loading...", "webinarignition") ); ?></b>
                                        </p>
                                    </div>
                                </div>

                                <?php include WEBINARIGNITION_PATH . "inc/lp/partials/auto-video.php"; ?>
                            <?php else : ?>
                                <?= do_shortcode( $webinar_data->webinar_iframe_source ) ?>
                            <?php endif ?>

                        <?php } else { ?>
                            <?php webinarignition_display( do_shortcode( $webinar_data->webinar_live_video ), '<img class="img-fluid" style="width: 100%;max-width: 100%;height: auto;" src="' . $assets . '/images/videoplaceholder.png" />' ); ?>
                        <?php } ?>
                    </div>
                    <div id="vidOvlSpc" style="width:100%; height: 100%;"></div>

                    <?php
                    /** TODO: Need clean up **/
                    $is_cta_aside = false;
                    $is_cta_overlay = false;
                    /** TODO: Need clean up - END **/

                    $has_overlay_ctas = false;

                    if ( $webinar_data->webinar_date == "AUTO" ) {
	                    $webinar_cta_by_position = WebinarignitionManager::get_webinar_cta_by_position($webinar_data);
	                    $has_overlay_ctas = !empty($webinar_cta_by_position['overlay']); //Webinar has overlay CTAs
	                    /** TODO: Need clean up **/
	                    $is_cta_aside = !empty($webinar_cta_by_position['outer']);
	                    $is_cta_overlay = $has_overlay_ctas;
	                    /** TODO: Need clean up - END **/
                    } else {
	                    $has_overlay_ctas = (
                            WebinarignitionPowerups::is_multiple_cta_enabled($webinar_data) &&
                            isset($webinar_data->cta_position) &&
                            $webinar_data->cta_position === 'overlay'
                        );
                    }
                    ?>

	                <?php
                    if( $has_overlay_ctas ) {
                        $cta_transperancy = absint($webinar_data->cta_transparancy);

                        if( $cta_transperancy > 100 ) {
                            $cta_transperancy = 100;
                        }

                        $cta_transperancy = 100 - $cta_transperancy;
                        $cta_transperancy = $cta_transperancy / 100;
                        ?>
                        <style>
                            .ctaArea {
                                position: relative;
                            }

                            .timedUnderArea {
                                border: 1px solid rgba(33, 33, 33, <?php echo ($cta_transperancy < 1) ? $cta_transperancy : 1 ?>);
                            }
                            .timedUnderArea:after {
                                display: none;
                            }

                            .timedUnderArea.timedUnderAreaOverlay, .additional_autoaction_item {
                                position: absolute;
                                height: auto;
                                max-height: 98%;
                                left: 100vw;
                                bottom: 0;
                                right: 0;
                                overflow: auto;
                                <?php echo ($cta_transperancy < 1) ? "background-color: rgba(255, 255, 255, {$cta_transperancy});" : "" ?>
                            }

                            @media only screen and (max-width : 768px) {
                                .timedUnderArea.timedUnderAreaOverlay, .additional_autoaction_item {
                                    height: auto;
                                    max-height: none!important;
                                }
                            }
                        </style>
                    <?php } ?>


                    <?php if ( $webinar_data->webinar_date == "AUTO" ) {
                        include WEBINARIGNITION_PATH . "inc/lp/partials/auto-overlay-cta-area.php";
                    } else { ?>
                        <div class="timedUnderArea" id="orderBTN" style="display: none;">
                            <div id="orderBTNCopy"></div>
                            <div id="orderBTNArea"></div>
                        </div>
                    <?php } ?>
                </div>
                    <?php
                    if ($is_cta_aside) {
                        ?>
                        <div class="ctaAreaOuter" style="padding-bottom: 3px;">
                            <?php include WEBINARIGNITION_PATH . "inc/lp/partials/auto-cta-area.php"; ?>
                        </div>
                        <?php
                    }
                    ?>
                <!--/.ctaArea-->
            </div>
            <!--/.webinarVideo-->

            <!-- WEBINAR BOTTOM AREA -->
            <div class="webinarBottomArea" style="display:none;">
                <?php if ( $webinar_data->webinar_callin != "hide" ) : ?>
                    <div class="webinarSound"
                         style="color: <?php webinarignition_display( $webinar_data->webinar_callin_color, "#222" ); ?>;">
                        <i class="icon-phone"></i> <?php webinarignition_display( $webinar_data->webinar_callin_copy, __( "To Join Call:" , "webinarignition")); ?>
                        <a style="color: <?php webinarignition_display( $webinar_data->webinar_callin_color2, "#3E8FC7" ); ?>;"><?php webinarignition_display( $webinar_data->webinar_callin_number, "1-555-555-5555" ); ?></a>
                    </div>
                <?php endif ?>

                <div class="webinarShare">
                    <div class="webinarShareCopy">
                        <div class="webinarLive"
                             style="color: <?php webinarignition_display( $webinar_data->webinar_live_color, "#498A00" ); ?>;">
                            <?php webinarignition_display( $webinar_data->webinar_live, __( "Webinar Is Live", "webinarignition") ); ?> <i class="icon-circle"></i>
                        </div>
                    </div>
                </div>
                <br clear="all"/>
            </div>

            <!-- WEBINAR UNDER EXTRA CTA AREA -->
            <div class="webinarUnderArea" style="margin-top: 30px;">
                <div class="row">
                    <div class="col-md-4">
                        <!-- WEBINAR BLOCK RIGHT -->
                        <div class="webinarBlockRight">

                            <!-- WEBINAR INFO BLOCK -->
                            <?php webinarignition_get_webinar_info($webinar_data, true) ?>

                            <!-- GIVE AWAY BLOCK -->
                            <?php webinarignition_get_webinar_giveaway($webinar_data, true); ?>

                        </div>
                        <!--/.webinarBlockRight-->
                    </div>


                    <?php if ( $webinar_data->webinar_qa !== "hide" ) {
                        ?>
                        <div class="col-md-8">
                            <?php webinarignition_get_webinar_qa($webinar_data, true); ?>
                        </div>
                        <?php
                    } ?>
                </div>
            </div><!--/.webinarUnderArea -->

        </div>
        <br clear="left"/>

    </div>

</div>

<?php require_once WEBINARIGNITION_PATH . 'inc/lp/partials/powered_by.php'; ?>

<div id="fb-root"></div>

<?php wp_footer(); ?>

<?php echo isset($webinar_data->footer_code) ? do_shortcode($webinar_data->footer_code) : ''; ?>

</body>
</html>
