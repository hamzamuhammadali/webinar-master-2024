<?php
/**
 * @var $webinar_data
 * @var $is_preview
 */

$main = false;

$timeover = false;

if( isset( $_GET['lid'] ) ) {

    $lead_id = sanitize_text_field( $_GET['lid'] );

    wp_enqueue_script('limit-custom-video');
    wp_localize_script('limit-custom-video', 'lcv_php_var', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    =>  wp_create_nonce('limit-custom-video'),
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

    if( absint($watch_time) >= $watch_limit ) {
        $timeover = true;
    }
}
?>

<?php if( !$timeover ) : ?>
<video disablePictureInPicture <?php echo !empty( $webinar_data->webinar_show_videojs_controls ) || !empty($is_preview) ? 'controls' : ''; ?> id="autoReplay" class="video-js vjs-default-skin">
    <source src="<?php echo $webinar_data->auto_video_url; ?>" type='video/mp4'/>
    <source src="<?php echo $webinar_data->auto_video_url2; ?>" type="video/webm"/>
</video>

<input type="hidden" id="autoVideoTime">
<?php else: ?>
    <h3><?php
        $watch_time_limit_string = __('45 Minutes', 'webinarignition');
        $statusCheck = WebinarignitionLicense::get_license_level();
        if($statusCheck->name == 'ultimate_powerup_tier1a') {
            $watch_time_limit_string = __('2 Hours', 'webinarignition');
        }

        echo sprintf( __('You have availed %s view time. Webinar is closed for you.', 'webinarignition'), $watch_time_limit_string ); ?></h3>
<?php endif; ?>