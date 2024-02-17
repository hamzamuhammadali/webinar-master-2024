<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$post_input = filter_input_array(INPUT_POST);

$ID   = isset($post_input['webinarignition_webinar_id']) ? absint($post_input['webinarignition_webinar_id']) : 0;
$type = isset($post_input['webinarignition_leads_type']) ? sanitize_text_field($post_input['webinarignition_leads_type']) : '';

if ( empty($ID) || empty($type) ) {
    exit;
}

global $wpdb;

if ( 'evergreen_normal' === $type || 'evergreen_hot'=== $type ) {
	$table_db_name      = $wpdb->prefix . "webinarignition_leads_evergreen";
    $table_meta_db_name = $wpdb->prefix . 'webinarignition_lead_evergreenmeta';
} else {
	$table_db_name      = $wpdb->prefix . "webinarignition_leads";
	$table_meta_db_name = $wpdb->prefix . 'webinarignition_leadmeta';
}

$leads_meta = $wpdb->get_results($wpdb->prepare("SELECT LM.lead_id, LM.meta_value FROM {$table_meta_db_name} LM WHERE LM.meta_key = %s", "wiRegForm_{$ID}"), ARRAY_A);

$meta_fields = [];
$meta_leads = [];

if (!empty($leads_meta)) {
    foreach ($leads_meta as $lead_meta) {
        $lead_meta_id = $lead_meta['lead_id'];
        $lead_meta_data = $lead_meta['meta_value'];

        if (!empty($lead_meta_data)) {
            $lead_meta_data = maybe_unserialize($lead_meta_data);
            $lead_meta_data = WebinarignitionLeadsManager::fix_optName($lead_meta_data);
	        if (is_array($lead_meta_data)) {
		        $meta_leads[$lead_meta_id] = $lead_meta_data;
		        if( isset($lead_meta_data['optName']) || isset($lead_meta_data['optEmail']) ) {
			        foreach ($lead_meta_data as $field_name => $field) {
				        $field_label = $field['label'];
				        $field_value = $field['value'];
				        $meta_fields[$field_name] = $field_label;
			        }
		        } else { //compatibility with old lead data
			        foreach ($lead_meta_data as $field_label => $field_value) {
				        $meta_fields[$field_label] = $field_label;
			        }
		        }
	        }
        }
    }
}

$query = "SELECT * FROM $table_db_name WHERE app_id = %d";
$query_params = [$ID];

if( $type === 'live_hot' || $type === 'evergreen_hot' ) {
	$query .= " AND event=%s";
	$query_params[] = 'Yes';
}

$results = $wpdb->get_results($wpdb->prepare($query, $query_params));

$export_filename = sprintf("webinarignition-leads-%d-%s", $ID, date('Y-m-d_H-i-s',current_time('U')));

// CSV Header:
header("Content-type: application/text");
header("Content-Disposition: attachment; filename={$export_filename}.csv");
header("Pragma: no-cache");
header("Expires: 0");

$headers = [
	__('Sign Up At', 'webinarignition'),
	__('Attended', 'webinarignition')
];

echo implode(',',$headers);

if (!empty($meta_fields)) {
    echo ', ';
    echo implode(', ', $meta_fields);
}
echo "\n";

foreach ($results as $r) {

    echo str_replace(',', ' -', $r->created);
    echo ",";
    echo strtolower($r->event) === 'yes' ? 'yes' : 'no';

    if (!empty($meta_fields)) {
        foreach ($meta_fields as $meta_field_key => $meta_field) {
            if (!empty($meta_leads[$r->ID][$meta_field_key])) {
                echo ", ";
                echo sanitize_text_field($meta_leads[$r->ID][$meta_field_key]['value']);
            } else {
                echo ", ";
            }
        }
    }

    echo "\n";
}