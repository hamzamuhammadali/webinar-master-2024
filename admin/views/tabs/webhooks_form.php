<?php

defined( 'ABSPATH' ) || exit; //Avoid direct access

$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS );
$webhook_id = absint(filter_input( INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS ));
$triggers = WebinarIgnitionWebhooks::webinarignition_webhook_get_triggers();
$integrations = WebinarIgnitionWebhooks::webinarignition_webhook_get_integrations();
$webhook_data = WebinarIgnitionWebhooks::webinarignition_webhook_get_data($webhook_id);
$webhook_data['conditions'] = maybe_unserialize($webhook_data['conditions']);
?>

<p><a href="<?php echo add_query_arg( [ 'page' => 'webinarignition_settings', 'tab' => 'webhooks' ], admin_url('admin.php') ); ?>" class="button button-primary button-large">
    <span class="dashicons dashicons-arrow-left-alt2" style="line-height: 32px;"></span>
    <?php _e('Webhooks List', 'webinarignition'); ?></a>
</p>

<form method="post">
    <input type="hidden" name="action" value="webinarignition_webhook_test_request">
    <input type="hidden" name="webhook_id" value="<?php echo $webhook_id; ?>">
    <table class="form-table">
        <tbody>
        <tr>
            <th><label><?php _e('Name'); ?></label></th>
            <td><input type="text" class="regular-text" name="webhook_name" value="<?php echo $webhook_data['name']; ?>"></td>
        </tr>
        <tr>
            <th><label><?php _e('Trigger','webinarignition'); ?></label></th>
            <td>
                <select name="webhook_trigger">
					<?php foreach ($triggers as $trigger => $trigger_name): ?>
                        <option value="<?php echo $trigger; ?>" <?php selected($webhook_data['trigger'], $trigger); ?>><?php echo $trigger_name; ?></option>
					<?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr style="display:none;">
            <th><label><?php _e('Data Template','webinarignition'); ?></label></th>
            <td>
                <select name="webhook_integration">
					<?php foreach ($integrations as $integration => $integration_name): ?>
                        <option value="<?php echo $integration; ?>" <?php selected($webhook_data['integration'], $integration); ?>><?php echo $integration_name; ?></option>
					<?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label><?php _e('Delivery URL','webinarignition'); ?></label></th>
            <td><input type="text" class="regular-text" name="webhook_url" value="<?php echo $webhook_data['url']; ?>"></td>
        </tr>
        <tr>
            <th><label><?php _e('Request Method','webinarignition'); ?></label></th>
            <td>
                <select name="webhook_request_method">
                    <option value="1" <?php selected( absint($webhook_data['request_method']), 1); ?>><?php _e('POST'); ?></option>
                    <option value="0" <?php selected( absint($webhook_data['request_method']), 0); ?>><?php _e('GET'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label><?php _e('Request Format','webinarignition'); ?></label></th>
            <td>
                <select name="webhook_request_format">
                    <option value="0" <?php selected( absint($webhook_data['request_format']), 0); ?>><?php _e('JSON'); ?></option>
                    <option value="1" <?php selected( absint($webhook_data['request_format']), 1); ?>><?php _e('FORM'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label><?php _e('Secret','webinarignition'); ?></label></th>
            <td><input type="text" class="regular-text" name="webhook_secret" value="<?php echo $webhook_data['secret']; ?>"></td>
        </tr>
        <tr>
            <th><label><?php _e('Status'); ?></label></th>
            <td>
                <select name="webhook_status">
                    <option value="1" <?php selected( absint($webhook_data['is_active']), 1); ?>><?php _e('Active'); ?></option>
                    <option value="0" <?php selected( absint($webhook_data['is_active']), 0); ?>><?php _e('Inactive'); ?></option>
                </select>
            </td>
        </tr>
        </tbody>
    </table>
    <hr>
    <h3><?php _e('Additional Fields:'); ?></h3> <button class="button button-secondary button-small" id="wi_webhooks_condition_add"><?php _e('Add'); ?></button>
	<?php

	$_props = array(
		'ar_name',
		'ar_lname',
		'ar_phone',
		'ar_email',
        'ar_webinar_host',
        'ar_privacy_policy',
		'ar_terms_and_conditions',
		'ar_mailing_list',
        'ar_utm_source',
        'ar_custom_1',
		'ar_custom_2',
		'ar_custom_3',
		'ar_custom_4',
		'ar_custom_5',
		'ar_custom_6',
		'ar_custom_7',
		'ar_custom_8',
		'ar_custom_9',
		'ar_custom_10',
		'ar_custom_11',
		'ar_custom_12',
		'ar_custom_13',
		'ar_custom_14',
		'ar_custom_15',
		'ar_custom_16',
		'ar_custom_17',
		'ar_custom_18',
	);

	//Hidden/DB fields
	$fields = [
        'id',
        'title',
		'url',
		'date',
		'time',
        'date_time',
		'timezone',
		'host',
		'registration_date',
		'registration_time',
        'registration_date_time',
		'lead_url'
    ];

	$operators = ['map' => __('Map', 'webinarignition'), 'equal' => __('Equal', 'webinarignition'), 'not_equal' => __('Not Equal', 'webinarignition'), 'greater_than' => __('Greater Than (number)', 'webinarignition'), 'less_than' => __('Less Than (number)', 'webinarignition')];
	foreach ($_props as $prop) {
		$field_name = Webinar_Ignition_Helper::ar_field_to_opt($prop);
		$fields[] = $field_name;
	}

	?>
    <div id="wi_webhooks_conditions_wrapper">
        <table id="wi_webhooks_condition_table" class="form-table">
            <tbody>
            <tr id="wi_webhooks_condition_table_headers">
                <th><?php _e('Field', 'webinarignition'); ?></th>
                <th><?php _e('Compare/Map', 'webinarignition'); ?></th>
                <th><?php _e('Compare Value', 'webinarignition'); ?></th>
                <th><?php _e('New field name', 'webinarignition'); ?></th>
                <th><?php _e('New field value', 'webinarignition'); ?></th>
            </tr>
            <tr id="wi_webhooks_condition_row" style="display:none;">
                <td class="wi_webhooks_condition_field">
                    <select name="wi_webhooks_condition[field][]">
						<?php foreach($fields as $field): ?>
                            <option value="<?php echo $field; ?>"><?php echo $field; ?></option>
						<?php endforeach; ?>
                    </select>
                </td>
                <td class="wi_webhooks_condition_operator">

                    <select name="wi_webhooks_condition[operator][]">
						<?php foreach($operators as $operator => $operator_name): ?>
                            <option value="<?php echo $operator; ?>"><?php echo $operator_name; ?></option>
						<?php endforeach; ?>
                    </select>
                </td>
                <td class="wi_webhooks_condition_value"><input type="text" value="" name="wi_webhooks_condition[value][]" readonly></td>
                <td class="wi_webhooks_condition_new_field_name"><input type="text" value="" name="wi_webhooks_condition[field_name][]"></td>
                <td class="wi_webhooks_condition_new_field_value"><input type="text" value="" name="wi_webhooks_condition[field_value][]" readonly></td>
                <td><a href="#" class="wi_webhooks_condition_delete" style="color:red; font-weight:700;">x</a> </td>
            </tr>
			<?php
			if( !empty($webhook_data['conditions']) ) {
				foreach ($webhook_data['conditions']['field'] as $index => $field):

					?>
                    <tr class="wi-webhook-condition-row">
                        <td class="wi_webhooks_condition_field">
                            <select name="wi_webhooks_condition[field][]">
								<?php foreach($fields as $field):
                                    //Rename "date_registered_on" and "time_registered_on" to "webinar_registration_date" and "webinar_registration_time" respectively
                                    if($webhook_data['conditions']['field'][$index] == 'date_registered_on') {
                                        $webhook_data['conditions']['field'][$index] = 'webinar_registration_date';
                                    } else if($webhook_data['conditions']['field'][$index] == 'time_registered_on') {
                                        $webhook_data['conditions']['field'][$index] = 'webinar_registration_time';
                                    }

                                    if( $webhook_data['conditions']['field'][$index] == "webinar_{$field}" ) {
                                        $webhook_data['conditions']['field'][$index] = $field;
                                    }
                                    ?>
                                    <option value="<?php echo $field; ?>" <?php selected($webhook_data['conditions']['field'][$index], $field); ?>><?php echo $field; ?></option>
								<?php endforeach; ?>
                            </select>
                        </td>
                        <td class="wi_webhooks_condition_operator">

                            <select name="wi_webhooks_condition[operator][]">
								<?php foreach($operators as $operator => $operator_name): ?>
                                    <option value="<?php echo $operator; ?>" <?php selected($webhook_data['conditions']['operator'][$index], $operator); ?>><?php echo $operator_name; ?></option>
								<?php endforeach; ?>
                            </select>
                        </td>
                        <td class="wi_webhooks_condition_value"><input type="text" value="<?php echo $webhook_data['conditions']['value'][$index]; ?>" name="wi_webhooks_condition[value][]" <?php wp_readonly($webhook_data['conditions']['operator'][$index], 'map'); ?>></td>
                        <td class="wi_webhooks_condition_new_field_name"><input type="text" value="<?php echo $webhook_data['conditions']['field_name'][$index]; ?>" name="wi_webhooks_condition[field_name][]"></td>
                        <td class="wi_webhooks_condition_new_field_value"><input type="text" value="<?php echo $webhook_data['conditions']['field_value'][$index]; ?>" name="wi_webhooks_condition[field_value][]"  <?php wp_readonly($webhook_data['conditions']['operator'][$index], 'map'); ?>></td>
                        <td><a href="#" class="wi_webhooks_condition_delete" style="color:red; font-weight:700;">x</a> </td>
                    </tr>

				<?php
				endforeach;
			}
			?>
            <tr class="wi-webhook-condition-no-rows-message" style="<?php echo !empty($webhook_data['conditions']['field']) ? 'display:none;' : ''; ?>">
                <td colspan="6"><?php _e('Click the "Add" button above to define additional custom fields.'); ?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <p>&nbsp;</p>
    <p><button type="submit" name="save" class="button button-primary button-large"><?php _e('Save webhook','webinarignition'); ?></button></p>
    <!--<button name="test_webhook" id="webinar_ignition_test_webhook" class="button button-secondary button-large"><?php /*_e('Test webhook','webinarignition'); */?></button>-->
	<?php wp_nonce_field( 'webinarignition-webhook-save' ); ?>
</form>
<script>
    (function( $ ) {
        $( document ).ready(function() {
            var condition_table = $('#wi_webhooks_condition_table');
            $('#wi_webhooks_condition_add').on('click', function(e) {
                e.preventDefault();

                let condition_row = $('#wi_webhooks_condition_row').clone().removeAttr('id').addClass('wi-webhook-condition-row').show();
                condition_table.append(condition_row);

                if( condition_table.find('.wi-webhook-condition-row').length > 0) {
                    $('.wi-webhook-condition-no-rows-message').hide();
                    let tableDiv = $(condition_table).parent('div');
                    $(tableDiv).animate({
                        scrollTop: $(tableDiv)[0].scrollHeight - $(tableDiv)[0].clientHeight
                    }, 1000);
                }
            });

            $(document).on('click', '.wi_webhooks_condition_delete', function(e) {
                e.preventDefault();

                var result = confirm("<?php _e('Are you sure you want to delete?', 'webinarignition'); ?>");

                if (result) {
                    $(this).closest('tr', condition_table).remove();
                    if (condition_table.find('.wi-webhook-condition-row').length === 0) {
                        $('.wi-webhook-condition-no-rows-message').show();
                    }
                }
            });

            $(document).on('change', '.wi_webhooks_condition_operator > select', function(e) {
                let this_value = $(this).val();
                console.log(this_value);
                let condition_value_input = $(this).closest('tr', condition_table).find('.wi_webhooks_condition_value input');
                let condition_new_field_value_input = $(this).closest('tr', condition_table).find('.wi_webhooks_condition_new_field_value input');
                if(this_value === 'map') {
                    condition_value_input.val('').attr('readonly', true);
                    condition_new_field_value_input.val('').attr('readonly', true);
                } else {
                    condition_value_input.attr('readonly', false);
                    condition_new_field_value_input.attr('readonly', false);
                }
            });

            $('#webinar_ignition_test_webhook').on('click', function(e) {
                e.preventDefault();
                var form_data = $(this).parent('form').serialize();

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: form_data
                }).done(function(response) {
                    console.log(response);
                });
            });
        });
    })( jQuery );
</script>