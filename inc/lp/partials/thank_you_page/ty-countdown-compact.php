<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinarId
 * @var $webinar_data
 * @var $data
 * @var $leadId
 * @var $instantTest
 * @var $autoDate_format
 * @var $autoTime
 * @var $liveEventMonth
 * @var $liveEventDateDigit
 */

$uid = wp_unique_id( $prefix = 'tyCountdown-' );
$is_public = WebinarignitionManager::is_webinar_public($webinar_data);

if ( !$is_public )
    $webinarIdUrl = $webinar_data->hash_id;
else
    $webinarIdUrl = $webinarId;

$watch_type = sanitize_text_field( filter_input( INPUT_GET, 'watch_type', FILTER_SANITIZE_SPECIAL_CHARS ) );
if( empty($watch_type) ) {
	$watch_type = 'live';
}
?>

<div id="<?php echo $uid ?>" class="tyCountdown tyCountdown-<?php echo $webinarId; ?> ticketCDArea ticketSection ticketSectionNew">
    <!-- AUTO CODE BLOCK AREA -->
    <?php if ( $webinar_data->webinar_date == "AUTO" ) {
        $url = webinarignition_fixPerma( $data->postID ) . "live&lid=" . $leadId . ( $webinar_data->paid_status === 'paid' ? ( "&" . md5( $webinar_data->paid_code ) ) : '' );

        if (!empty($webinar_data->custom_webinar_page)) {
            $custom_webinar_page = get_post($webinar_data->custom_webinar_page);

            if (!empty($custom_webinar_page)) {
                $custom_webinar_page_url = get_permalink($custom_webinar_page);
	            $custom_webinar_page_url = add_query_arg('lid', $leadId, $custom_webinar_page_url);

	            if ( WebinarignitionManager::is_paid_webinar($webinar_data) ) {
		            $custom_webinar_page_url = add_query_arg(md5( $webinar_data->paid_code ), '', $custom_webinar_page_url);
	            }

                $url = $custom_webinar_page_url;
            }
        }
	    $url = remove_query_arg('webinar', $url); //Remove webinar ID, as we can retrieve it from lead ID
	    $url = add_query_arg(['live' => '', 'watch_type' => $watch_type], $url);
        ?>
        <a href="<?php echo $url; ?>"
           class="ticketCDAreaBTN button alert radius disabled addedArrow  wiButton wiButton-success wiButton-block wiButton-lg"
           id="webinarBTNNN">
            <?php webinarignition_display( $webinar_data->tycd_countdown, __( "Webinar Starts Soon:", "webinarignition") ); ?>
            <div id="defaultCountdown"></div>
        </a>
    <?php } else {
	    if ( isset( $webinar_data->ty_webinar_url ) && $webinar_data->ty_webinar_url == "custom" && !empty($webinar_data->ty_werbinar_custom_url) ) {
                $liveWebinarUrl = $webinar_data->ty_werbinar_custom_url;
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
            }
            ?>
            <a href="<?php echo $liveWebinarUrl; ?>"
               class="ticketCDAreaBTN button alert radius disabled addedArrow  wiButton wiButton-success wiButton-block wiButton-lg"
               id="webinarBTNNN">
                <?php webinarignition_display( $webinar_data->tycd_countdown, __( "Webinar Starts Soon:", "webinarignition") ); ?>
                <div id="defaultCountdown"></div>
            </a>
    <?php } ?>
    <!-- END AUTO CODE BLOCK AREA -->

</div>
