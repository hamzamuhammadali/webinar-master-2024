<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $data
 * @var $leadId
 * @var $webinarId
 */

$uid = wp_unique_id( $prefix = 'tyCountdown-' );
$is_public = WebinarignitionManager::is_webinar_public($webinar_data);

if ( !$is_public )
    $webinarIdUrl = $webinar_data->hash_id;
else
    $webinarIdUrl = $webinarId;

$watch_type = 'live';

if ( isset( $webinar_data->ty_webinar_url ) && $webinar_data->ty_webinar_url == "custom" && !empty($webinar_data->ty_werbinar_custom_url) ) {
    echo $webinar_data->ty_werbinar_custom_url;
} else {
    $liveWebinarUrl = WebinarignitionManager::get_permalink($webinar_data,'webinar');
    $liveWebinarUrl = add_query_arg('live', '', $liveWebinarUrl);

    if( empty($leadId) && isset($getLiveIDByEmail->id) && !empty($getLiveIDByEmail->id) ) {
	    $leadId = $getLiveIDByEmail->id;
    }

    $liveWebinarUrl = add_query_arg('lid', $leadId, $liveWebinarUrl);
    if ( WebinarignitionManager::is_paid_webinar($webinar_data) ) {
	    $liveWebinarUrl = add_query_arg(md5( $webinar_data->paid_code ), '', $liveWebinarUrl);
    }

    $liveWebinarUrl = add_query_arg('watch_type', $watch_type, $liveWebinarUrl);

    echo $liveWebinarUrl;
}

