<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $assets
 */

$webinarignition_modern_page = get_query_var( 'webinarignition_modern_page' );

$is_replay = false;

if ($webinarignition_modern_page && $webinarignition_modern_page === 'replay_page') {
	$is_replay = true;
}

$webinar_type = 'live';

$is_cta_aside = false;
$is_cta_overlay = false;
$is_cta_timed = false;

$webinar_cta_by_position = WebinarignitionManager::get_webinar_cta_by_position($webinar_data);

if (!empty($webinar_cta_by_position)) {
	$webinar_type = 'evergreen';
	if ( !empty($webinar_cta_by_position['is_time']) ) $is_cta_timed = true;
	if ( !empty($webinar_cta_by_position['outer']) ) $is_cta_aside = true;
	if ( !empty($webinar_cta_by_position['overlay']) ) $is_cta_overlay = true;
}
?>
<div class="webinarVideo">
	<?php
	$is_preview = WebinarignitionManager::url_is_preview_page();
	if ( !$is_preview && $webinar_data->webinar_live_overlay == 1 && (!isset($webinar_data->webinar_live_video) || ! strpos($webinar_data->webinar_live_video, 'zoom')) ) : ?>
        <!-- disable video controls -->
        <div id="vidOvl" style="display:none;"></div>
	<?php endif ?>
    <div class="ctaArea">
		<?php if ( ! empty( $webinar_data->webinar_iframe_source ) ):
			if ( ( has_shortcode( $webinar_data->webinar_iframe_source, 'video' ) ) ): $GLOBALS['content_width'] = 1225; endif;
		endif; ?>

		<?php if ( ! empty( $webinar_data->webinar_live_video ) ):
			if ( ( has_shortcode( $webinar_data->webinar_live_video, 'video' ) ) ): $GLOBALS['content_width'] = 1225; endif;
		endif; ?>

        <div id="vidBox" class="<?php webinarignition_should_use_videojs( $webinar_data ) ? 'vidBoxjs' : '' ?>">
			<?php
			if ( $webinar_data->webinar_date == "AUTO" ) {
				?>
				<?php if ( webinarignition_should_use_videojs( $webinar_data ) ) : ?>
                    <div id="video-loading-block">
                        <div id="video-loading-overlay" type="button" name="button"></div>
                        <div id="video-loading-content-container">
                            <img id="video-loading-spinner"
                                 src="<?php echo WEBINARIGNITION_URL; ?>images/ajax-loader.gif"/>
                            <div id="video-loading-text">
								<?php echo $is_replay ? __('Starting replay', 'webinarignition') : __('Joining Webinar', 'webinarignition'); ?>
                            </div>
                        </div>
                    </div>

                    <div id="no-autoplay-block" style="display: none;">
                        <div id="mobile-overlay" type="button" name="button"></div>
                        <img id="mobile-play-button" src="<?php echo WEBINARIGNITION_URL; ?>images/play-button.png"
                             alt=""/>
                        <span id="mobile-play-button-text">
                                    <?php echo $is_replay ? __('Watch Replay', 'webinarignition') : __('Join Webinar', 'webinarignition'); ?>
                            </span>
                    </div>

                    <div id="muted-autoplay-block" style="display: none;">
                        <div id="muted-overlay" type="button" name="button"></div>
                        <div id="unmute-button">
                            <img id="unmute-icon" src="<?php echo WEBINARIGNITION_URL; ?>images/unmute.png"
                                 alt=""/>
							<?php echo __('Click for sound', 'webinarignition'); ?>

                        </div>
                    </div>
                    <div class="autoWebinarLoading"
                         style="z-index: 888888; background-color: rgba(0, 0, 0, 0.8); width: 100%; position:absolute; display: none">

                        <div class="autoWebinarLoadingCopy">
                            <i class="icon-spinner icon-spin icon-large autoWebinarLoader"></i>
                            <br/>
                            <p>
                                <b><?php webinarignition_display( $webinar_data->auto_video_load, __( "Please Wait - The Webinar Is Loading..." , "webinarignition") ); ?></b>
                            </p>
                        </div>
                    </div>

					<?php include WEBINARIGNITION_PATH . "inc/lp/partials/auto-video.php"; ?>
				<?php else :

                    $timeover = false;

                    if( isset( $_GET['lid'] ) ) {

                        $lead_id = sanitize_text_field( $_GET['lid'] );

                        wp_enqueue_script('limit-iframe-video');
                        wp_localize_script('limit-iframe-video', 'lcv_php_var', array(
                            'ajax_url' => admin_url('admin-ajax.php'),
                            'nonce'    =>  wp_create_nonce('limit-iframe-video'),
                            'lead_id'  => $lead_id,
                        ));

                        $watch_time = get_option('wi_lead_watch_time_'. $lead_id, true );

                        $statusCheck = WebinarignitionLicense::get_license_level();
                        if($statusCheck->name == 'ultimate_powerup_tier1a') {
                            $watch_limit = HOUR_IN_SECONDS * 2;
                        } else {
                            $watch_limit = MINUTE_IN_SECONDS * 45;
                        }

                        if( is_plugin_active('webinar-ignition-helper/webinar-ignition-helper.php') ) {
                            $watch_limit = 300;
                        }

                        if( intval( $watch_time ) >= $watch_limit ) {
                            $timeover = true;
                        }
                    }

                    if( !$timeover ) {
                        echo do_shortcode( $webinar_data->webinar_iframe_source );
                    } else {
                        $watch_time_limit_string = __('45 Minutes', 'webinarignition');
                        $statusCheck = WebinarignitionLicense::get_license_level();
                        if($statusCheck->name == 'ultimate_powerup_tier1a') {
                            $watch_time_limit_string = __('2 Hours', 'webinarignition');
                        }
                        ?>
                        <h3>
                            <?php echo sprintf(__('You have availed %s view time. Webinar is closed for you.', 'webinarignition'), $watch_time_limit_string); ?>
                        </h3>
				<?php
                    }
				endif;
			} else {

                if( isset( $_GET['lid'] ) ) {

                    $lead_id = sanitize_text_field( $_GET['lid'] );

                    wp_enqueue_script('limit-iframe-video');
                    wp_localize_script('limit-iframe-video', 'lcv_php_var', array(
                        'ajax_url' => admin_url('admin-ajax.php'),
                        'nonce'    =>  wp_create_nonce('limit-iframe-video'),
                        'lead_id'  => $lead_id,
                    ));
                }

				if ($is_replay) {
					webinarignition_display( do_shortcode( $webinar_data->replay_video ), '<img src="' . $assets . '/images/videoplaceholder.png" />' );
				} else {
					webinarignition_display( do_shortcode( $webinar_data->webinar_live_video ), '<img class="img-fluid" style="width: 85%;max-width: 100%;height: auto;" src="' . $assets . '/images/videoplaceholder.png" />' );
				}

			} ?>
        </div>
        <div id="vidOvlSpc" style="width:100%; height: 100%;"></div>
    </div>
    <!--/.ctaArea-->
</div>