<?php
$cta_index = isset($index) ? absint($index) : 0;
$cta_iframe = '';
$cta_iframe_sc = '';
$cta_content = '';

if( $cta_index === 0 ) { //Main CTA

	if( isset($webinar_data->cta_iframe) ) {
		$cta_iframe = $webinar_data->cta_iframe;
	}

	if( isset($webinar_data->cta_iframe_sc) ) {
		$cta_iframe_sc = $webinar_data->cta_iframe_sc;
	}

	if( isset($webinar_data->auto_action_copy) ) {
		$cta_content = $webinar_data->auto_action_copy;
	}

} else { //Additional CTA

	if( isset($additional_autoaction['cta_iframe'])) {
		$cta_iframe = $additional_autoaction['cta_iframe'];
	}

	if( isset($additional_autoaction['cta_iframe_sc'])) {
		$cta_iframe_sc = $additional_autoaction['cta_iframe_sc'];
	}

	if( isset($additional_autoaction['auto_action_copy']) ) {
		$cta_content = $additional_autoaction['auto_action_copy'];
	}
}

$cta_iframe = strtolower($cta_iframe);
$cta_iframe_sc = wp_kses_post($cta_iframe_sc);
$cta_content = stripcslashes( wpautop($cta_content) );
$statusCheck = WebinarignitionLicense::get_license_level();
$webinar_id = absint($webinar_data->id);

if ( in_array($statusCheck->switch, ['enterprise_powerup','pro','free']) || !empty($statusCheck->is_trial) ) {

	$cta_content  = apply_filters( 'webinarignition_additional_cta_content', $cta_content );
	if( class_exists('advancediFrame') && $cta_iframe == 'yes' ) {

		$cta_content .= get_cta_aiframe_sc($webinar_id, $cta_index, $cta_iframe_sc);
		$cta_content = apply_filters( 'ai_handle_temp_pages', $cta_content );
	}

	echo do_shortcode($cta_content);

} else {
	echo $cta_content;
}
