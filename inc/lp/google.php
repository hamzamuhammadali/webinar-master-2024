<?php defined( 'ABSPATH' ) || exit;



// Get DB Info
global $wpdb;
$table_db_name      = $wpdb->prefix . 'webinarignition';
$data               = $wpdb->get_row("SELECT * FROM $table_db_name WHERE id = '$webinar_id'", OBJECT);

// Webinar Info
$title              = $webinar_data->webinar_desc ? $webinar_data->webinar_desc : __( "Webinar Title", "webinarignition");
$desc               = $webinar_data->webinar_desc ? $webinar_data->webinar_desc : __( "Info for what you will learn on the webinar...", "webinarignition") ;
$host               = $webinar_data->webinar_host ? $webinar_data->webinar_host : __( "Webinar Host", "webinarignition") ;

$lid = filter_input(INPUT_GET, 'lid', FILTER_SANITIZE_URL);

if ( isset( $webinar_data->ty_webinar_url ) && $webinar_data->ty_webinar_url == "custom" && !empty($webinar_data->ty_werbinar_custom_url) ) {
    $url            = $webinar_data->ty_werbinar_custom_url;
} else {
    $url            = get_permalink($data->postID) . "?live&lid=".$lid;
}

//encode url parameters
$title      = urlencode($title);
$desc       = urlencode($desc);
$host       = urlencode($host);
$url        = urlencode($url);

$timezone = $webinar_data->webinar_timezone;
if (!in_array($timezone[0], array('-', '+'))) {
    $timezone = '+' . $timezone;
}
$timezone_sign      = $timezone[0];
$timezone_offset    = str_pad(str_replace('0', '', substr($timezone, 1)), 4, '0', STR_PAD_BOTH);


$webinar_data->webinar_start_time  = date("H:i", strtotime($webinar_data->webinar_start_time));


$date               = DateTime::createFromFormat('m-d-Y H:i:s', $webinar_data->webinar_date . ' ' . $webinar_data->webinar_start_time . ':00', new DateTimeZone($timezone_offset));
$date->setTimezone(new DateTimeZone('UTC'));


define('DATE_FORMAT', 'Ymd\THis');

// Build Final URL
$build_url = "http://www.google.com/calendar/event?action=TEMPLATE&text=" . $title . "&dates=" . $date->format(DATE_FORMAT) . 'Z' . "/" . $date->modify('+1 hour')->format(DATE_FORMAT) . 'Z' . "&details=" . $desc . "&location=" . $url . "&trp=true&sprop=" . $host . "&sprop=name:" . $url;

// echo $build_url;
header("Location: $build_url");
