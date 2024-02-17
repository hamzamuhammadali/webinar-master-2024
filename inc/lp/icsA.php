<?php
defined( 'ABSPATH' ) || exit;

/**
 * @var $webinar_data
 * @var $leadID
 * @var $leadId
 * @var $leadinfo
 */

// Get Results
$results = $webinar_data;
// error_log($leadID);

// Get DB Info
//global $wpdb;
//$table_db_name = $wpdb->prefix . "webinarignition_leads_evergreen";
//$data = $wpdb->get_row("SELECT * FROM $table_db_name WHERE id = '$leadID'", OBJECT);
$data = $leadinfo;

// Webinar Info
$title = $results->webinar_desc ? $results->webinar_desc    :   __( "Webinar Title", "webinarignition");
$desc = $results->webinar_desc ? $results->webinar_desc     :   __(  "Info on what you will learn on the webinar...", "webinarignition");
$host = $results->webinar_host ? $results->webinar_host     :   __( "Webinar Host", "webinarignition") ;

if ($results->ty_webinar_url == "custom") {
    $url = $results->ty_werbinar_custom_url;
} else {
    $url = get_permalink($data->postID) . "?live&lid=" . $leadId;
}

if (!in_array($timezone[0], array('-', '+'))) {
    $timezone = '+' . $timezone;
}
$date = DateTime::createFromFormat('Y-m-d H:i', $data->date_picked_and_live, new DateTimeZone($data->lead_timezone));
$date->setTimezone(new DateTimeZone('UTC'));

define('DATE_FORMAT', 'Ymd\THis');

header("Content-type: application/text");
header("Content-Disposition: attachment; filename=webinar-date.ics");
header("Pragma: no-cache");
header("Expires: 0");

echo
    "BEGIN:VCALENDAR" . "\r\n" .
    "VERSION:2.0" . "\r\n" .
    "PRODID:-//project/author//NONSGML v1.0//EN" . "\r\n" .
    "CALSCALE:GREGORIAN" . "\r\n" .
    "METHOD:PUBLISH" . "\r\n" .
    "BEGIN:VTIMEZONE" . "\r\n" .
    "TZID:GMT" . "\r\n" .
    "BEGIN:STANDARD" . "\r\n" .
    "DTSTART:20071028T010000" . "\r\n" .
    "TZOFFSETTO:+0000" . "\r\n" .
    "TZOFFSETFROM:+0000" . "\r\n" .
    "END:STANDARD" . "\r\n" .
    "END:VTIMEZONE" . "\r\n" .
    "BEGIN:VEVENT" . "\r\n" .
    "DTSTART;TZID=GMT:" . $date->format(DATE_FORMAT) . "\r\n" .
    "DTEND;TZID=GMT:" . $date->modify('+1 hour')->format(DATE_FORMAT) . "\r\n" .
    "UID:" . $date->getTimestamp() . '@' . $leadId . "\r\n" .
    "DTSTAMP:" . gmdate(DATE_FORMAT) . 'Z' . "\r\n" .
    "SUMMARY:" . $title . "\r\n" .
    "DESCRIPTION:" . $desc . '. Visit ' . $url . "\r\n" .
    "URL;VALUE=URI:" . $url . "\r\n" .
    "END:VEVENT" . "\r\n" .
    "END:VCALENDAR";