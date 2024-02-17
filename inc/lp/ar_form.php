<?php defined( 'ABSPATH' ) || exit; ?>

<?php if( !empty( $webinar_data->ar_fields_order ) && is_array( $webinar_data->ar_fields_order ) ): ?>

    <iframe id="ar_submit_iframe" name="ar_submit_iframe"></iframe>
    <form action="<?php echo isset($webinar_data->ar_url) ? $webinar_data->ar_url : ''; ?>" id="AR-INTEGRATION" method="<?php echo isset($webinar_data->ar_method) ? $webinar_data->ar_method : 'POST'; ?>"
          target="ar_submit_iframe">

		<?php

		if ( ! empty( $webinar_data->ar_fields_order ) && is_array( $webinar_data->ar_fields_order ) ) {
			$alreadyAddedFields = [];
			foreach ( $webinar_data->ar_fields_order as $_field ) {
				if (in_array($_field, $alreadyAddedFields)) {
					continue;
				}
				$alreadyAddedFields[] = $_field;

				if ( empty( $webinar_data->$_field ) ) {
					continue;
				}

				$arCustomDateFormat = isset($webinar_data->ar_custom_date_format) ? $webinar_data->ar_custom_date_format : 'not-set';
				if ( $webinar_data->webinar_date !== "AUTO" ) {
					$dateParts = explode('-', $webinar_data->webinar_date);
					$wi_webinar_date = $dateParts[2] . '-' . $dateParts[0] . '-' . $dateParts[1];
					$wi_webinar_datetime = $wi_webinar_date . ' ' . $webinar_data->webinar_start_time;
					$webinarignition_webinar_timestamp = strtotime($wi_webinar_datetime);
				} else {
					$webinarignition_webinar_timestamp = 0;
				}

				switch ( $_field ) {
					case 'ar_name':
						?><input type="hidden" name="<?php echo $webinar_data->ar_name; ?>" id="ar-name" value="" /><?php
						break;
					case 'ar_lname':
						?><input type="hidden" name="<?php echo $webinar_data->ar_lname; ?>" id="ar-lname" value="" /><?php
						break;
					case 'ar_email':
						?><input type="hidden" name="<?php echo $webinar_data->ar_email; ?>" id="ar-email" value="" /><?php
						break;
					case 'ar_phone':
						?><input type="hidden" name="<?php echo $webinar_data->ar_phone; ?>" id="ar-phone" value="" /><?php
						break;
					case 'ar_custom_1':
					case 'ar_custom_2':
					case 'ar_custom_3':
					case 'ar_custom_4':
					case 'ar_custom_5':
					case 'ar_custom_6':
					case 'ar_custom_7':
					case 'ar_custom_8':
					case 'ar_custom_9':
					case 'ar_custom_10':
					case 'ar_custom_11':
					case 'ar_custom_12':
					case 'ar_custom_13':
					case 'ar_custom_14':
					case 'ar_custom_15':
					case 'ar_custom_16':
					case 'ar_custom_17':
					case 'ar_custom_18':
						$option_index = str_replace('ar_', 'lp_optin_', $_field);
						?>
                        <input type="hidden" id="<?php echo $_field; ?>" name="<?php echo $webinar_data->{$_field}; ?>" value="<?php echo $webinar_data->{$option_index}; ?>" />
						<?php
						break;
					case 'ar_utm_source':
						?><input type="hidden" name="<?php echo $webinar_data->ar_utm_source; ?>" id="ar_utm_source" value="<?php echo filter_input( INPUT_GET, 'utm_source', FILTER_SANITIZE_SPECIAL_CHARS ); ?>" /><?php
						break;
					case 'ar_privacy_policy':
						?><input type="hidden" name="<?php echo $webinar_data->ar_privacy_policy; ?>" id="ar-privacy-policy" value="" /><?php
						break;
					case 'ar_terms_and_conditions':
						?><input type="hidden" name="<?php echo $webinar_data->ar_terms_and_conditions; ?>" id="ar-terms-and-conditions" value="" /><?php
						break;
					case 'ar_mailing_list':
						?><input type="hidden" name="<?php echo $webinar_data->ar_mailing_list; ?>" id="ar-mailing-list" value="" /><?php
						break;
					case 'ar_webinar_title':
						?><input type="hidden" name="<?php echo $webinar_data->ar_webinar_title; ?>" id="ar-webinar-title" value="<?php echo $webinar_data->webinar_desc; ?>" /><?php
						break;
					case 'ar_webinar_host':
						?><input type="hidden" name="<?php echo $webinar_data->ar_webinar_host; ?>"  id="ar-webinar-host" value="<?php echo $webinar_data->webinar_host; ?>" /><?php
						break;
					case 'ar_webinar_url':
						?><input type="hidden" name="<?php echo $webinar_data->ar_webinar_url; ?>"  id="ar-webinar-url" value="" /><?php
						break;
					case 'ar_webinar_date':
						?><input type="hidden" name="<?php echo $webinar_data->ar_webinar_date; ?>"  id="ar-webinar-date" value="<?php echo webinarignition_format_date_for_ar_service($arCustomDateFormat, $webinarignition_webinar_timestamp) ?>" /><?php
						break;
					case 'ar_webinar_time':
						?><input type="hidden" name="<?php echo $webinar_data->ar_webinar_time; ?>"  id="ar-webinar-time" value="<?php echo date('g:i A', $webinarignition_webinar_timestamp); ?>" /><?php
						break;
					case 'ar_webinar_registration_date':
						?><input type="hidden" name="<?php echo $webinar_data->ar_webinar_registration_date; ?>"  id="ar-webinar-registration-date" value="<?php echo webinarignition_format_date_for_ar_service($arCustomDateFormat, time()) ?>" /><?php
						break;
					case 'ar_webinar_registration_time':
						?><input type="hidden" name="<?php echo $webinar_data->ar_webinar_registration_time; ?>"  id="ar-webinar-registration-time" value="<?php echo date('g:i A', time()); ?>" /><?php
						break;
					case 'ar_webinar_timezone':
						$wi_formatted_timezone = empty($webinar_data->webinar_timezone) ? '' : $webinar_data->webinar_timezone . " (UTC" . webinarignition_get_timezone_offset_by_name($webinar_data->webinar_timezone) . ")";
						?><input type="hidden" name="<?php echo $webinar_data->ar_webinar_timezone; ?>"  id="ar-webinar-timezone" value="<?php echo $wi_formatted_timezone; ?>" /><?php
						break;
					default:
						break;
				}
			}
		}
		?>

		<?php
		if(isset($webinar_data->ar_hidden)) {
			echo stripcslashes( $webinar_data->ar_hidden );
		}?>
        <input type="submit" name="wi_ar_submit_button" value="send" />
    </form>

<?php endif; ?>