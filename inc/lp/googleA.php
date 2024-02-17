<?php
defined( 'ABSPATH' ) || exit;

/**
 * @var $webinar_data
 * @var $leadID
 * @var $leadId
 * @var $leadinfo
 */
?>
<?php

// Get Results
//$input_get     = filter_input_array(INPUT_GET);
//$leadID = $input_get['id'];
//$results = WebinarignitionManager::get_webinar_data($client);

$results = $webinar_data;
// error_log($leadID);

// Get DB Info
//global $wpdb;
//$table_db_name = $wpdb->prefix . "webinarignition_leads_evergreen";
//$data = $wpdb->get_row("SELECT * FROM $table_db_name WHERE id = '$leadID'", OBJECT);
$data = $leadinfo;

// Webinar Info
$title = $results->webinar_desc ? $results->webinar_desc : __( "Webinar Title", "webinarignition");
$desc = $results->webinar_desc ? $results->webinar_desc : __(  "Info on what you will learn on the webinar...", "webinarignition");
$host = $results->webinar_host ? $results->webinar_host : __( "Webinar Host", "webinarignition") ;

if ($results->ty_webinar_url == "custom") {
    $url = $results->ty_werbinar_custom_url;
} else {
    $url = get_permalink($data->postID) . "?live&lid=" . $leadId;
}

//encode url parameters
$title = urlencode($title);
$desc = urlencode($desc);
$host = urlencode($host);
$url = urlencode($url);

$date = DateTime::createFromFormat('Y-m-d H:i', $data->date_picked_and_live, new DateTimeZone($data->lead_timezone));
$date->setTimezone(new DateTimeZone('UTC'));

define('DATE_FORMAT', 'Ymd\THis');

// Build Final URL
$build_url = "http://www.google.com/calendar/event?action=TEMPLATE&text=" . $title . "&dates=" . $date->format(DATE_FORMAT) . 'Z' . "/" . $date->modify('+1 hour')->format(DATE_FORMAT) . 'Z' . "&details=" . $desc . "&location=" . $url . "&trp=true&sprop=" . $host . "&sprop=name:" . $url;


// echo $build_url;
header("Location: $build_url");