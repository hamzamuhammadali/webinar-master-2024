<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $data
 * @var $leadId
 * @var $webinarId
 */

$uid = wp_unique_id( $prefix = 'tyCountdown-' );
$url = '';
if ( isset( $webinar_data->ty_webinar_url ) && $webinar_data->ty_webinar_url == "custom" && !empty($webinar_data->ty_werbinar_custom_url) ) {
	$url = $webinar_data->ty_werbinar_custom_url;
} else {
	$watch_type = 'live';
	$url = WebinarignitionManager::get_permalink($webinar_data, 'webinar');

	if( ( !isset($leadId) || empty($leadId) ) && ( isset($getLiveIDByEmail->id) && !empty($getLiveIDByEmail->id) ) ) {
		$leadId = $getLiveIDByEmail->id;
	}

	$webinar_page_query_args = [
		'live' => '',
		'lid'  => $leadId,
		'watch_type'  => $watch_type,
	];

	//append paid_code to the URL
	if ( WebinarignitionManager::is_paid_webinar($webinar_data) ) {
		$webinar_page_query_args[ md5( $webinar_data->paid_code ) ] = '';
	}

	$url = add_query_arg( $webinar_page_query_args, $url );
}
?>

<div class="webinarURLArea">
    <div class="webinarURLHeadline">
        <i class="icon-bookmark" style="margin-right: 10px; color: #878787;"></i>
        <?php
            $url_message = __( 'Here Is Your Webinar Event URL...', 'webinarignition' );
            webinarignition_display( $webinar_data->ty_webinar_headline, $url_message );
        ?>
    </div>

    <!-- AUTO CODE BLOCK AREA -->
    <div class="wiFormGroup wiFormGroup-lg">
        <input type="url" id="webbyURL" class="radius fieldRadius wiRegForm optNamer wiFormControl" value="<?php echo esc_url($url); ?>">
    </div>
    <!-- END AUTO CODE BLOCK AREA -->

    <div class="webinarURLHeadline2">
        <?php
            $save_bkmrk_msg = __( 'Save and bookmark this URL so you can get access to the live webinar and webinar replay...', 'webinarignition' );
            webinarignition_display( $webinar_data->ty_webinar_subheadline, $save_bkmrk_msg );
        ?>
    </div>
</div>
