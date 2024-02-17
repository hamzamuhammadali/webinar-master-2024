<?php defined( 'ABSPATH' ) || exit; ?>
<div class="tabber" id="tab3" style="display: none;">

    <div class="titleBar">

        <div class="titleBarText">
            <h2><?php esc_html_e( 'Landing Page Settings:', 'webinarignition' ) ?></h2>
            <p><?php esc_html_e( 'Here you can edit & manage your webinar registration page...', 'webinarignition' ) ?></p>
        </div>

        <?php
        $registration_preview_url = add_query_arg( ['preview' => 'true'], get_the_permalink($data->postID));
        ?>

        <div class="launchConsole">
            <a
                    href="<?php echo $registration_preview_url; ?>"
                    target="_blank"
                    data-default-href="<?php echo $registration_preview_url; ?>"
                    class="custom_registration_page-webinarPreviewLinkDefaultHolder-1"
            >
                <i class="icon-external-link-sign"></i>
				<?php esc_html_e( "Preview Registration Page", 'webinarignition' ) ?>
            </a>
        </div>

        <br clear="all"/>

		<?php $input_get     = filter_input_array(INPUT_GET); ?>

    </div>

	<?php
	// Evergreen Check
	if ( $webinar_data->webinar_date == "AUTO" ) {
		// Evergreen
		webinarignition_display_edit_toggle(
			"calendar",
			esc_html__( 'Auto Webinar Dates & Times', 'webinarignition' ),
			"we_edit_lp_auto_dates",
			esc_html__( 'Select the dates & times for the auto webinar...', 'webinarignition' )
		);
		?>
        <div id="we_edit_lp_auto_dates" class="we_edit_area">
			<?php
			webinarignition_display_option(
				$input_get['id'],
				$webinar_data->lp_schedule_type,
				esc_html__( 'Webinar Schedule Type', 'webinarignition' ),
				"lp_schedule_type",
				esc_html__( 'Choose if you want to customize the dates and times when your webinar will be available, or choose a fixed date and time.', 'webinarignition' ),
				esc_html__( "Customized", 'webinarignition' ) . " [customized]," . esc_html__( "Fixed", 'webinarignition' ) . " [fixed], " . esc_html__( "Delayed", 'webinarignition' ) . " [delayed]"
			);
			?>
            <div class="lp_schedule_type" id="lp_schedule_type_customized">
				<?php
				//dates
				webinarignition_display_option(
					$input_get['id'], $webinar_data->auto_today,
					esc_html__( 'Today - Instant Access', 'webinarignition' ),
					"auto_today",
					esc_html__( 'You can allow people to watch the replay right away...', 'webinarignition' ),
					esc_html__( "Enable", 'webinarignition' ) . " [yes]," . esc_html__( "Disable", 'webinarignition' ) . " [no]"
				);

				webinarignition_display_field(
					$input_get['id'],
					$webinar_data->auto_day_offset,
					esc_html__( 'Delay available registration date by', 'webinarignition' ),
					"auto_day_offset",
					esc_html__( 'Specify by how many days to delay the first available registration date by.', 'webinarignition' ),
					esc_html__( 'Example: 3 (Defaults to 0)', 'webinarignition' )
				);

				webinarignition_display_field(
					$input_get['id'],
					$webinar_data->auto_day_limit,
					esc_html__( 'Limit number of available dates', 'webinarignition' ),
					"auto_day_limit",
					esc_html__( 'Specify how many dates to make available. Defaults to 7. Maximum is also 7.', 'webinarignition' ),
					esc_html__( 'Example: 5 (Defaults to 7)', 'webinarignition' )
				);

				webinarignition_display_option(
					$input_get['id'],
					$webinar_data->auto_monday,
					esc_html__( 'Monday', 'webinarignition' ),
					"auto_monday",
					esc_html__( 'You can choose to show this day as a possible day for the webinar, it will select the next possible occurrence within the week...', 'webinarignition' ),
					esc_html__( "Enable", 'webinarignition' ) . " [yes]," . esc_html__( "Disable", 'webinarignition' ) . " [no]"
				);

				webinarignition_display_option(
					$input_get['id'],
					$webinar_data->auto_tuesday,
					esc_html__( 'Tuesday', 'webinarignition' ),
					"auto_tuesday",
					esc_html__( 'You can choose to show this day as a possible day for the webinar, it will select the next possible occurrence within the week...', 'webinarignition' ),
					esc_html__( "Enable", 'webinarignition' ) . " [yes]," . esc_html__( "Disable", 'webinarignition' ) . " [no]"
				);

				webinarignition_display_option(
					$input_get['id'],
					$webinar_data->auto_wednesday,
					esc_html__( 'Wednesday', 'webinarignition' ),
					"auto_wednesday",
					esc_html__( "You can choose to show this day as a possible day for the webinar, it will select the next possible occurrence within the week...", 'webinarignition'),
					esc_html__( "Enable", 'webinarignition' ) . " [yes]," . esc_html__( "Disable", 'webinarignition' ) . " [no]"
				);
				webinarignition_display_option(
					$input_get['id'],
					$webinar_data->auto_thursday,
					esc_html__( 'Thursday', 'webinarignition' ),
					"auto_thursday",
					esc_html__( "You can choose to show this day as a possible day for the webinar, it will select the next possible occurrence within the week...", 'webinarignition'),
					esc_html__( "Enable", 'webinarignition' ) . " [yes]," . esc_html__( "Disable", 'webinarignition' ) . " [no]"
				);
				webinarignition_display_option(
					$input_get['id'],
					$webinar_data->auto_friday,
					esc_html__( 'Friday', 'webinarignition' ),
					"auto_friday",
					esc_html__( "You can choose to show this day as a possible day for the webinar, it will select the next possible occurrence within the week...", 'webinarignition'),
					esc_html__( "Enable", 'webinarignition' ) . " [yes]," . esc_html__( "Disable", 'webinarignition' ) . " [no]"
				);
				webinarignition_display_option(
					$input_get['id'],
					$webinar_data->auto_saturday,
					esc_html__( 'Saturday', 'webinarignition' ),
					"auto_saturday",
					esc_html__( "You can choose to show this day as a possible day for the webinar, it will select the next possible occurrence within the week...", 'webinarignition'),
					esc_html__( "Enable", 'webinarignition' ) . " [yes]," . esc_html__( "Disable", 'webinarignition' ) . " [no]"
				);
				webinarignition_display_option(
					$input_get['id'],
					$webinar_data->auto_sunday,
					esc_html__( 'Sunday', 'webinarignition' ),
					"auto_sunday",
					esc_html__( "You can choose to show this day as a possible day for the webinar, it will select the next possible occurrence within the week...", 'webinarignition'),
					esc_html__( "Enable", 'webinarignition' ) . " [yes]," . esc_html__( "Disable", 'webinarignition' ) . " [no]"
				);
				webinarignition_display_field(
					$input_get['id'],
					$webinar_data->auto_blacklisted_dates,
					esc_html__( 'Blacklist Dates', 'webinarignition' ),
					"auto_blacklisted_dates",
					__( 'Here you can hide certain dates or holidays...<br><br><b>The format must be Y-M-D and seperated by a comma and space.<br><br>IE: 2013-12-25, 2013-01-01</b>', 'webinarignition' ),
					""
				);

				$is_multiple_auto_time_enabled = WebinarignitionPowerups::is_multiple_auto_time_enabled($webinar_data);

				//times below
				webinarignition_display_time_auto(
					$input_get['id'],
					$webinar_data->auto_time_1,
					!isset($webinar_data->auto_weekdays_1) ? false : $webinar_data->auto_weekdays_1,
					esc_html__( 'Webinar Time #1', 'webinarignition' ),
					"auto_time_1",
					"auto_weekdays_1",
					esc_html__( 'Select Webinar available time', 'webinarignition' )
				);
				webinarignition_display_time_auto(
					$input_get['id'],
					$webinar_data->auto_time_2,
					!isset($webinar_data->auto_weekdays_2) ? false : $webinar_data->auto_weekdays_2,
					esc_html__( 'Webinar Time #2', 'webinarignition' ),
					"auto_time_2",
					"auto_weekdays_2",
					esc_html__( 'Select Webinar available time', 'webinarignition' )
				);
				webinarignition_display_time_auto(
					$input_get['id'],
					$webinar_data->auto_time_3,
					!isset($webinar_data->auto_weekdays_3) ? false : $webinar_data->auto_weekdays_3,
					esc_html__( 'Webinar Time #3', 'webinarignition' ),
					"auto_time_3",
					"auto_weekdays_3",
					esc_html__( 'Select Webinar available time', 'webinarignition' )
				);

				if ($is_multiple_auto_time_enabled) {
					?>
                    <div id="additional_auto_time_template" style="display: none;">
                        <div class="additional_auto_time_item">
							<?php
							webinarignition_display_time_auto(
								$input_get['id'], '', false,
								sprintf( esc_html__( 'Webinar Time #%s', 'webinarignition' ), '<span class="index_holder"></span>' ),
								"multiple__auto_time",
								"multiple__auto_weekdays",
								esc_html__( 'Select Webinar available time', 'webinarignition' ),
								true
							);
							?>

                            <button type="button" class="blue-btn btn deleteAutoTime" style="color:#FFF;float:none;">
                                <i class="icon-remove"></i>
                            </button>
                        </div>
                    </div>
					<?php
				}
				?>
                <div id="additional_auto_time_container"<?php echo $is_multiple_auto_time_enabled ? '' : ' style="display:none;"'?>>
					<?php
					if (!empty($webinar_data->multiple__auto_time)) {
						$multiple__auto_weekdays = !empty($webinar_data->multiple__auto_weekdays) ? $webinar_data->multiple__auto_weekdays : false;

						foreach ($webinar_data->multiple__auto_time as $index => $item) {
							$num = $index + 4;
							$num_id = $index + 1;
							$weekdays_selected = $multiple__auto_weekdays;

							if (false !== $multiple__auto_weekdays) {
								$weekdays_selected = !empty($multiple__auto_weekdays[$index]) ? $multiple__auto_weekdays[$index] : [];
							}
							?>
                            <div class="additional_auto_time_item">
								<?php
								webinarignition_display_time_auto(
									$input_get['id'],
									$item,
									$weekdays_selected,
									sprintf( esc_html__( 'Webinar Time #%s', 'webinarignition' ), '<span class="index_holder">'.$num.'</span>' ),
									"multiple__auto_time__" . $num_id,
									"multiple__auto_weekdays__" . $num_id,
									esc_html__( 'Select Webinar available time', 'webinarignition' )
								);
								?>

                                <button type="button" class="blue-btn btn deleteAutoTime" style="color:#FFF;float:none;">
                                    <i class="icon-remove"></i>
                                </button>
                            </div>
							<?php

						}

					}
					?>
                </div>
				<?php
				if ($is_multiple_auto_time_enabled) {
					?>
                    <div class="additional_auto_time_control editSection">
                        <button type="button" id="createAutoTime" class="blue-btn-44 btn" style="color:#FFF;float:none;">
                            <i class="icon-plus"></i> <?php esc_html_e( "Add New Webinar Time", 'webinarignition' ) ?>
                        </button>
                    </div>
					<?php
				}

				webinarignition_display_option(
					$input_get['id'],
					$webinar_data->auto_timezone_type,
					esc_html__( 'Choose timezone type', 'webinarignition' ),
					"auto_timezone_type",
					esc_html__( 'Choose whether you want to specify a fixed timezone, or let the user sign up for a time in their timezone.', 'webinarignition' ),
					esc_html__( "User Specific", 'webinarignition' ) . " [user_specific]," . esc_html__( "Fixed", 'webinarignition' ) . " [fixed]"
				);
				?>
                <div class="auto_timezone_type" id="auto_timezone_type_fixed">
					<?php
					webinarignition_display_timezone_identifiers(
						$input_get['id'],
						$webinar_data->auto_timezone_custom,
						esc_html__( 'Fixed Webinar Timezone', 'webinarignition' ),
						"auto_timezone_custom",
						esc_html__( 'Choose a timezone for your webinar.', 'webinarignition' ),
						esc_html__( 'Select webinar timezone', 'webinarignition' )
					);
					?>
                </div>

            </div>
            <div class="lp_schedule_type" id="lp_schedule_type_fixed">
				<?php

				webinarignition_display_date_picker(
					$input_get['id'],
					$webinar_data->auto_date_fixed,
					'Y-m-d',
					esc_html__( 'Fixed Webinar Date', 'webinarignition' ),
					"auto_date_fixed",
					esc_html__( 'Choose a fixed date for your evergreen webinar.', 'webinarignition' ),
					esc_html__( 'Choose date', 'webinarignition' ),
					$webinar_date_format
				);

				webinarignition_display_time_picker(
					$input_get['id'],
					$webinar_data->auto_time_fixed,
					esc_html__( 'Fixed Webinar Time', 'webinarignition' ),
					"auto_time_fixed",
					esc_html__( 'Choose a fixed time for your evergreen webinar.', 'webinarignition' )
				);

				webinarignition_display_timezone_identifiers(
					$input_get['id'],
					$webinar_data->auto_timezone_fixed,
					esc_html__( 'Fixed Webinar Timezone', 'webinarignition' ),
					"auto_timezone_fixed",
					esc_html__( 'Choose a timezone for your webinar.', 'webinarignition' ),
					esc_html__( 'Select webinar timezone', 'webinarignition' )
				);
				?>
            </div>
            <div class="lp_schedule_type" id="lp_schedule_type_delayed">
				<?php
				webinarignition_display_field(
					$input_get['id'],
					$webinar_data->delayed_day_offset,
					esc_html__( 'Delay available registration date by', 'webinarignition' ),
					"delayed_day_offset",
					esc_html__( 'Specify by how many days to delay the available registration date, based when the user visited the registration page.', 'webinarignition' ),
					esc_html__( 'Example: 3', 'webinarignition' )
				);

				webinarignition_display_time_picker(
					$input_get['id'],
					$webinar_data->auto_time_delayed,
					esc_html__( 'Fixed Webinar Time', 'webinarignition' ),
					"auto_time_delayed",
					esc_html__( 'Choose a fixed time for your evergreen webinar.', 'webinarignition' )
				);
				webinarignition_display_option(
					$input_get['id'],
					$webinar_data->delayed_timezone_type,
					esc_html__( 'Choose timezone type', 'webinarignition' ),
					"delayed_timezone_type",
					esc_html__( 'Choose whether you want to specify a fixed timezone, or let the user sign up for a time in their timezone.', 'webinarignition' ),
					esc_html__( "Fixed", 'webinarignition' ) . " [fixed]," . esc_html__( "User Specific", 'webinarignition' ) . " [user_specific]"
				);
				?>
                <div class="delayed_timezone_type" id="delayed_timezone_type_user_specific">
					<?php
					webinarignition_display_wpeditor(
						$input_get['id'],
						$webinar_data->auto_timezone_user_specific_name,
						esc_html__( 'Your Timezone translation', 'webinarignition' ),
						"auto_timezone_user_specific_name",
						esc_html__( 'Translate "Your Timezone" text into your language.', 'webinarignition' )
					);
					?>
                </div>
                <div class="delayed_timezone_type" id="delayed_timezone_type_fixed">
					<?php
					webinarignition_display_timezone_identifiers(
						$input_get['id'],
						$webinar_data->auto_timezone_delayed,
						esc_html__( 'Fixed Webinar Timezone', 'webinarignition' ),
						"auto_timezone_delayed",
						esc_html__( 'Choose a timezone for your webinar.', 'webinarignition' ),
						esc_html__( 'Select webinar timezone', 'webinarignition' )
					);
					?>
                </div>

				<?php
				webinarignition_display_field(
					$input_get['id'],
					$webinar_data->delayed_blacklisted_dates,
					esc_html__( "Blacklist Dates", 'webinarignition' ),
					"delayed_blacklisted_dates",
					__( "Here you can hide certain dates or holidays...<br><br><b>The format must be Y-M-D and seperated by a comma and space.<br><br>IE: 2013-12-25, 2013-01-01</b>", 'webinarignition' ),
					""
				);
				?>
            </div>
        </div>

		<?php
	}
	?>

	<?php

	if ( $webinar_data->webinar_date == "AUTO" ) {

		webinarignition_display_edit_toggle(
			"edit-sign",
			esc_html__( "Translation For Months / Days / Copy", 'webinarignition' ),
			"we_edit_lp_auto_times_translate",
			esc_html__( "Translation options for date. times & copy...", 'webinarignition' )
		);

	}
	?>
    <div id="we_edit_lp_auto_times_translate" class="we_edit_area">
		<?php
		if ( $webinar_data->webinar_date == "AUTO" ) {
			webinarignition_display_field(
				$input_get['id'],
				$webinar_data->auto_translate_instant,
				esc_html__( "Translate :: Instant Access/Today", 'webinarignition' ),
				"auto_translate_instant",
				esc_html__( "This is the text that is shown if they want to watch the replay...", 'webinarignition' ),
				esc_html__( "e.g. Watch Replay", 'webinarignition' )
			);
			webinarignition_display_field(
				$input_get['id'],
				$webinar_data->auto_translate_headline1,
				esc_html__( "Choose Date Headline", 'webinarignition' ),
				"auto_translate_headline1",
				esc_html__( "This is the headline text for choosing a date for the webinar...", 'webinarignition' ),
				esc_html__( "e.g. Choose a Date To Attend...", 'webinarignition' )
			);
			webinarignition_display_field(
				$input_get['id'],
				$webinar_data->auto_translate_subheadline1,
				esc_html__( "Choose Date Sub-Headline", 'webinarignition' ),
				"auto_translate_subheadline1",
				esc_html__( "This is shown under the headline above...", 'webinarignition' ),
				esc_html__( "e.g. Select a date that best suits your schedule...", 'webinarignition' )
			);
			webinarignition_display_field(
				$input_get['id'],
				$webinar_data->auto_translate_headline2,
				esc_html__( "Choose Time Headline", 'webinarignition' ),
				"auto_translate_headline2",
				esc_html__( "This is the headline text for choosing a time for the webinar...", 'webinarignition' ),
				esc_html__( "e.g. What Time Is Best For You?", 'webinarignition' )
			);
			webinarignition_display_field(
				$input_get['id'],
				$webinar_data->auto_translate_subheadline2,
				esc_html__( "Choose Time Sub-Headline", 'webinarignition' ),
				"auto_translate_subheadline2",
				esc_html__( "This is shown under the headline above and shows the users local time...", 'webinarignition' ),
				esc_html__( "e.g. Your Local Time is:", 'webinarignition' )
			);
		}
		?>
    </div>

	<?php
	webinarignition_display_edit_toggle(
		"picture",
		esc_html__( "Banner Settings", 'webinarignition' ),
		"we_edit_lp_header_image",
		esc_html__( "Your main banner image for the landing page...", 'webinarignition' )
	);
	?>

    <div id="we_edit_lp_header_image" class="we_edit_area">
		<?php WebinarignitionPowerupsShortcodes::show_shortcode_description('reg_banner', $webinar_data, true, true) ?>
		<?php
		webinarignition_display_option(
			$input_get['id'],
			$webinar_data->lp_banner_bg_style,
			esc_html__( "Banner Background Style", 'webinarignition' ),
			"lp_banner_bg_style",
			esc_html__( "You can choose between a simple background color, or to have a background image (repeating horiztonally)", 'webinarignition' ),
			esc_html__( "Show Banner Area", 'webinarignition' ) . " [show]," . esc_html__( "Hide Banner Area", 'webinarignition' ) . " [hide]"
		);
		?>
        <div class="lp_banner_bg_style" id="lp_banner_bg_style_show">
			<?php
			webinarignition_display_color(
				$input_get['id'],
				$webinar_data->lp_banner_bg_color,
				esc_html__( "Banner Background Color", 'webinarignition' ),
				"lp_banner_bg_color",
				esc_html__( "Choose a color for the top banner area, this will fill the entire top banner area...", 'webinarignition' ),
				"#FFFFFF"
			);
			?>
			<?php
			webinarignition_display_field_image_upd(
				$input_get['id'],
				$webinar_data->lp_banner_bg_repeater,
				esc_html__( "Banner Repeating BG Image", 'webinarignition' ),
				"lp_banner_bg_repeater",
				__( "This is the image that is repeated horiztonally in the background of the banner area... If you leave this blank, it will just show the banner BG color... <br><br><b>best results:</b> 89px high..", 'webinarignition' ),
				esc_html__( "http://yoursite.com/banner-bg.png", 'webinarignition' )
			);
			?>
			<?php
			webinarignition_display_field_image_upd(
				$input_get['id'],
				$webinar_data->lp_banner_image,
				esc_html__( "Banner Image URL:", 'webinarignition' ),
				"lp_banner_image",
				__( "This is the URL for the banner image you want to be shown. By defualt it is placed in the middle, perfect for a logo... <br><br><b>best results:</b> 89px high and 960px wide...", 'webinarignition' ),
				esc_html__( "http://yoursite.com/banner-image.png", 'webinarignition' )
			);

			webinarignition_display_info(
				esc_html__( "Note: Banner Sizing", 'webinarignition' ),
				esc_html__( "Your banner image size can be any height, but its best at 89px high. Also, your banner repeating graphic should be the same height...", 'webinarignition' )
			);
			?>
        </div>

    </div>

	<?php
	webinarignition_display_edit_toggle(
		"magic",
		esc_html__( "Background Style Settings", 'webinarignition' ),
		"we_edit_lp_bg",
		esc_html__( "Select the style of your background...", 'webinarignition' )
	);
	?>

    <div id="we_edit_lp_bg" class="we_edit_area">
		<?php
		webinarignition_display_color(
			$input_get['id'], $webinar_data->lp_background_color,
			esc_html__( "Background Color", 'webinarignition' ),
			"lp_background_color",
			esc_html__( "This is the color for the main section, this fills the entire landing page area...", 'webinarignition' ),
			"#DDDDDD"
		);
		webinarignition_display_field_image_upd(
			$input_get['id'], $webinar_data->lp_background_image,
			esc_html__( "Repeating Background Image URL", 'webinarignition' ),
			"lp_background_image",
			esc_html__( "You can have a repeating image to be shown as the background to add some flare to your landing page...", 'webinarignition' ),
			esc_html__( "http://yoursite.com/background-image.png", 'webinarignition' )
		);
		webinarignition_display_info(
			esc_html__( "Note: Background Image", 'webinarignition' ),
			esc_html__( "If you leave the background image blank, no bg image will be shown...", 'webinarignition' )
		);
		?>
    </div>

	<?php
	webinarignition_display_edit_toggle(
		"cogs",
		esc_html__( "Meta Information (Social Share Settings)", 'webinarignition' ),
		"we_edit_lp_metashare",
		esc_html__( "Edit your meta information used for the social sharing features...", 'webinarignition' )
	);
	?>

    <div id="we_edit_lp_metashare" class="we_edit_area">
		<?php
		webinarignition_display_field(
			$input_get['id'],
			$webinar_data->lp_metashare_title,
			esc_html__( "Meta Site Title", 'webinarignition' ),
			"lp_metashare_title",
			esc_html__( "This is your site title - this will be used as the main headline for social shares...", 'webinarignition' ),
			esc_html__( "e.g. Amazing Webinar Training!", 'webinarignition' )
		);

		webinarignition_display_field(
			$input_get['id'], $webinar_data->lp_metashare_desc,
			esc_html__( "Meta Description", 'webinarignition' ),
			"lp_metashare_desc",
			esc_html__( "This is your site description - this will be used as the main copy for social shares...", 'webinarignition' ),
			esc_html__( "e.g. Check out this awesome training, this is a one time webinar!", 'webinarignition' )
		);


		webinarignition_display_field_image_upd(
			$input_get['id'], $webinar_data->ty_share_image,
			esc_html__( "Social Share Image URL", 'webinarignition' ),
			"ty_share_image",
			esc_html__( "This is the image that is used with the social shares, for best results, keep it: 120px by 120px..", 'webinarignition' ),
			esc_html__( "e.g. http://yoursite.com/share-image.png", 'webinarignition' )
		);

		?>
    </div>

	<?php
	webinarignition_display_edit_toggle(
		"edit-sign",
		esc_html__("Main Headline", "webinarignition"),
		"we_edit_lp_headline",
		esc_html__( "Copy for the main headline on the landing page...", 'webinarignition' )
	);
	?>

    <div id="we_edit_lp_headline" class="we_edit_area">
		<?php WebinarignitionPowerupsShortcodes::show_shortcode_description('reg_main_headline', $webinar_data, true, true) ?>
		<?php
		webinarignition_display_wpeditor(
			$input_get['id'],
			$webinar_data->lp_main_headline,
			esc_html__("Main Headline", "webinarignition"),
			"lp_main_headline",
			esc_html__( "This appears above the main optin area. This should really get people excited for your event, so they really want to be there...", "webinarignition")
		);
		?>
    </div>

	<?php
	$cta_area_string = class_exists('WI_GRID') ? esc_html__( 'CTA Area - Video / Image / Grid Settings', 'webinarignition' ) : esc_html__( 'CTA Area - Video / Image Settings', 'webinarignition' );

	webinarignition_display_edit_toggle(
		"film",
		$cta_area_string,
		"we_edit_lp_cta_area",
		esc_html__( "The core CTA area, which can be a video or an image...", 'webinarignition' )
	);
	?>

    <div id="we_edit_lp_cta_area" class="we_edit_area">
		<?php WebinarignitionPowerupsShortcodes::show_shortcode_description('reg_video_area', $webinar_data, true, true) ?>
		<?php
		webinarignition_display_color(
			$input_get['id'],
			$webinar_data->lp_cta_bg_color,
			esc_html__( "CTA Area Background Color", 'webinarignition' ),
			"lp_cta_bg_color",
			esc_html__( "This is the color for the CTA area that video or image is displayed, a good contrast color will get a lot of attention for this area...", 'webinarignition' ),
			"#000000"
		);

		webinarignition_display_info(
			esc_html__( "Note: CTA BG Color", 'webinarignition' ),
			esc_html__( "This is also used for the thank you page for the CTA area there...", 'webinarignition' )
		);

		if( class_exists('WI_GRID') ):
		?>
        <div class="lp_grid_image_url" id="lp_grid_image_url">
		    <?php
		    webinarignition_display_field_image_upd(
			    $input_get['id'],
			    isset($webinar_data->lp_grid_image_url) ? $webinar_data->lp_grid_image_url : '', //275x200
			    esc_html__( "Grid Image", 'webinarignition' ),
			    "lp_grid_image_url",
			    esc_html__( "This is the image will be shown on WebinarIgnition Grid.", 'webinarignition' ),
			    esc_html__( "http://yoursite.com/grid-image.png", 'webinarignition' )
		    );
		    webinarignition_display_info(
			    esc_html__( "Note: Grid Image Size", 'webinarignition' ),
			    esc_html__( "For the best results, make sure your Grid image size is 275(w) X 200(h) pixels", 'webinarignition' )
		    );
		    ?>
        </div>
        <?php
        endif;
		webinarignition_display_option(
			$input_get['id'],
			$webinar_data->lp_cta_type,
			esc_html__( "CTA Type:", 'webinarignition' ),
			"lp_cta_type",
			esc_html__( "You can choose to display a video embed code or have an image to be shown here. A video will get higher results...", 'webinarignition' ),
			esc_html__( "Show Video", 'webinarignition' ) . " [video]," . esc_html__( "Show Image", 'webinarignition' ) . " [image]"
		);
		?>
        <div class="lp_cta_type" id="lp_cta_type_video">
			<?php
			webinarignition_display_field_add_media(
				$input_get['id'],
				isset($webinar_data->lp_cta_video_url) ? $webinar_data->lp_cta_video_url : '',
				esc_html__( "Webinar Video URL .MP4 *", 'webinarignition' ),
				'lp_cta_video_url',
				esc_html__( "The MP4 file that you want to play as your CTA... must be in .mp4 format as its uses a html5 video player...", 'webinarignition' ),
				esc_html__( "Ex. http://yoursite.com/webinar-video.mp4", 'webinarignition' )
			);

			webinarignition_display_textarea(
				$input_get['id'],
				$webinar_data->lp_cta_video_code,
				esc_html__( "Video Embed Code", 'webinarignition' ),
				"lp_cta_video_code",
				__( "This is your video embed code. Your video will be auto-resized to fit the area which is <strong>500px width and 281px height</strong> <br><br>EasyVideoPlayer users must resize their video manually...", 'webinarignition' ),
				esc_html__( "e.g. Youtube embed code, Vimeo embed code, etc", 'webinarignition' )
			);

			webinarignition_display_info(
				esc_html__( "Note: Video Size", 'webinarignition' ),
				esc_html__( "The video will auto-resized, but its best you have a video with the same aspect ratio of 500x281...", 'webinarignition' )
			);
			?>
        </div>

        <div class="lp_cta_type" id="lp_cta_type_image" style="display: none;">
			<?php
			webinarignition_display_field_image_upd(
				$input_get['id'],
				$webinar_data->lp_cta_image,
				esc_html__( "CTA Image URL", 'webinarignition' ),
				"lp_cta_image",
				__( "This is the image that will be shown in the main cta area, this image will be resized to fit the area: <strong>500px width and 281px height</strong>...", 'webinarignition' ),
				esc_html__( "http://yoursite.com/cta-image.png", 'webinarignition' )
			);

			webinarignition_display_info(
				esc_html__( "Note: CTA Image", 'webinarignition' ),
				esc_html__( "For the best results, make sure your CTA image is 500 wide...", 'webinarignition' )
			);
			?>
        </div>

    </div>

	<?php
	webinarignition_display_edit_toggle(
		"edit-sign",
		esc_html__( "Sales Copy", 'webinarignition' ),
		"we_edit_lp_sales_copy",
		esc_html__( "The main landing page copy that appears under the CTA video / image area...", 'webinarignition' )
	);
	?>

    <div id="we_edit_lp_sales_copy" class="we_edit_area">
		<?php WebinarignitionPowerupsShortcodes::show_shortcode_description('reg_sales_headline', $webinar_data, true, true) ?>
		<?php
		webinarignition_display_field(
			$input_get['id'],
			$webinar_data->lp_sales_headline,
			esc_html__( "Sales Copy Headline", 'webinarignition' ),
			"lp_sales_headline",
			esc_html__( "This is the copy that is shown above the sales copy for the landing page, it has a background color to make it pop on the page...", 'webinarignition' ),
			esc_html__( "e.g. What You Will Learn On The Webinar...", 'webinarignition' )
		);
		webinarignition_display_color(
			$input_get['id'],
			$webinar_data->lp_sales_headline_color,
			esc_html__( "Sales Copy Headline BG Color", 'webinarignition' ),
			"lp_sales_headline_color",
			esc_html__( "This is the background color for the headline area... Make it a color that stands out on the page. The sales copy headline will always be white, so make sure this color works well with white text...", 'webinarignition' ),
			"#0496AC"
		);
		webinarignition_display_info(
			esc_html__( "Note: Headline BG Color", 'webinarignition' ),
			esc_html__( "This color will also be used in the thank you page for the step headlines...", 'webinarignition' )
		);

		WebinarignitionPowerupsShortcodes::show_shortcode_description( 'reg_sales_copy', $webinar_data, true, true );

		webinarignition_display_wpeditor(
			$input_get['id'],
			$webinar_data->lp_sales_copy,
			esc_html__( "Main Sales Copy", 'webinarignition' ),
			"lp_sales_copy",
			esc_html__( "This is the main sales copy that is shown under the CTA area and sales headline. This is where you can explain all the finer details about the webinar...", 'webinarignition' )
		);
		webinarignition_display_info(
			esc_html__( "Note: Sales Copy", 'webinarignition' ),
			esc_html__( "This is shown below the video area, you can have the main bits of what they will learn on the webinar here...", 'webinarignition' )
		);
		?>
    </div>

	<?php
	webinarignition_display_edit_toggle(
		"edit-sign",
		esc_html__( "Optin Headline", 'webinarignition' ),
		"we_edit_lp_optin_headline",
		esc_html__( "The headline that appears over the optin area...", 'webinarignition' )
	);
	?>

    <div id="we_edit_lp_optin_headline" class="we_edit_area">
		<?php
		webinarignition_display_wpeditor(
			$input_get['id'],
			$webinar_data->lp_optin_headline,
			esc_html__( "Optin Headline", 'webinarignition' ),
			"lp_optin_headline",
			esc_html__( "This is shown on the right hand side of the page above the webinar date...", 'webinarignition' )
		);
		?>
    </div>

	<?php
	if ( $webinar_data->webinar_date != "AUTO" ) {

		webinarignition_display_edit_toggle(
			"calendar",
			esc_html__( "Optin Webinar Date", 'webinarignition' ),
			"we_edit_lp_optin_date",
			esc_html__( "Dates / Copy for the landing page...", 'webinarignition' )
		);

	}

	?>

    <div id="we_edit_lp_optin_date" class="we_edit_area">
		<?php

		webinarignition_display_field(
			$input_get['id'],
			$webinar_data->lp_webinar_subheadline,
			esc_html__( "Webinar Date Sub Headline", 'webinarignition' ),
			"lp_webinar_subheadline",
			esc_html__( "This is shown under the headline above, ideal for stating the time of the webinar...", 'webinarignition' ),
			esc_html__( "at 5pm Eastern, 2pm Pacific", 'webinarignition' )
		);
		webinarignition_display_info(
			esc_html__( "Note: Webinar Date", 'webinarignition' ),
			esc_html__( "The date format depends on the format you have chosen in WordPress's General Settings page.", 'webinarignition' )
		);
		?>
    </div>

	<?php
	webinarignition_display_edit_toggle(
		"user",
		esc_html__( "Webinar Host Info", 'webinarignition' ),
		"we_edit_lp_host",
		esc_html__( "Information about the webinar host, Photo & Text...", 'webinarignition' )
	);
	?>

    <div id="we_edit_lp_host" class="we_edit_area" style="display: none;">
		<?php
		webinarignition_display_option(
			$input_get['id'],
			$webinar_data->lp_webinar_host_block,
			esc_html__( "Banner Background Style", 'webinarignition' ),
			"lp_webinar_host_block",
			esc_html__( "You can choose to show or hide the webinar host info block...", 'webinarignition' ),
			esc_html__( "Show Host Info Area", 'webinarignition' ) . " [show]," . esc_html__( "Hide Host Info Area", 'webinarignition' ) . " [hide]"
		);
		?>
        <div class="lp_webinar_host_block" id="lp_webinar_host_block_show">

			<?php
			webinarignition_display_field_image_upd(
				$input_get['id'],
				$webinar_data->lp_host_image,
				esc_html__( "Webinar Host Photo URL", 'webinarignition' ),
				"lp_host_image",
				__( "This is the image for the person hosting the webinar, this is shown under the optin area... <b>best results: 100px wide and  100px high</b>", 'webinarignition' ),
				esc_html__( "http://yoursite.com/webinar-host.png", 'webinarignition' )
			);

			webinarignition_display_textarea(
				$input_get['id'],
				$webinar_data->lp_host_info,
				esc_html__( "Webinar Host Info", 'webinarignition' ),
				"lp_host_info",
				__( "This is the text that is show on the right side of the webinars host photo. This should tell the visitor who the host is and why they should listen them...(html allowed ie. <b>bold tags</b>)", 'webinarignition' ),
				""
			);
			?>

        </div>

    </div>

	<?php
	webinarignition_display_edit_toggle(
		"money",
		esc_html__( "Paid Webinar", 'webinarignition' ),
		"we_edit_lp_paid",
		esc_html__( "Require payment to sign up & view webinar..", 'webinarignition' )
	);
	?>
    <div id="we_edit_lp_paid" class="we_edit_area">
		<?php

		webinarignition_display_option(
			$input_get['id'],
			$webinar_data->paid_status,
			esc_html__( "Paid Status", 'webinarignition' ),
			"paid_status",
			esc_html__( "Choose to make it a free webinar, or a paid webinar...", 'webinarignition' ) . '<br><br> <a href="https://webinarignition.tawk.help/article/creating-paid-webinars" target="_blank">' . esc_html__( "KB article: Paid webinars", 'webinarignition' ) . '<a/>',
			esc_html__( "Free Webinar", 'webinarignition' ) . " [free]," . esc_html__( "Paid Webinar", 'webinarignition' ) . " [paid]"
		);

		?>
        <div class="paid_status" id="paid_status_paid" style="display: none;">
			<?php
			webinarignition_display_wpeditor(
				$input_get['id'],
				$webinar_data->paid_headline,
				esc_html__( "Pay Headline", 'webinarignition' ),
				"paid_headline",
				esc_html__( "This is the headline that is above the order button...", 'webinarignition' )
			);

			/* PAYMENT OPTION */
			// make backwards compatible with pre v1.9.79
			if ($webinar_data->paid_button_type == 'default') {
				if (webinarignition_usingStripePaymentOption($webinar_data)) {
					$webinar_data->paid_button_type = 'stripe';
				} else if (webinarignition_usingPaypalPaymentOption($webinar_data)) {
					$webinar_data->paid_button_type = 'paypal';
				}
			}

			$webinar_type = strtolower(trim($webinar_data->webinar_date)) != 'auto' ? 'evergreen' : 'live';
			$df_options = "Paypal [paypal], Stripe  [stripe]," . esc_html__( "Custom", 'webinarignition' ) . " [custom]";
			$statusCheck = WebinarignitionLicense::get_license_level();

			$is_ultimate_activated = false;
			if( !empty($statusCheck) && (isset($statusCheck->switch) || isset($statusCheck->is_trial)) ) {
				$is_ultimate_activated = !empty($statusCheck->is_trial) || $statusCheck->switch === 'enterprise_powerup'; //If enterprise_powerup, consider it as ultimate
			}

			$show_on_live = true;
			$show_on_eg   = true;

			$wi_wc_display_field = ( ($show_on_live && $webinar_type === 'live') || ($show_on_eg && $webinar_type === 'evergreen') ) ? 1 : 0;
            ?>
            <span style="display:none;" id="wi_wc_display_field" data-field-value="<?php echo $wi_wc_display_field; ?>"></span>
            <?php
//			if( ($show_on_live && $webinar_record['camtype'] === 'new') || ($show_on_eg && $webinar_record['camtype'] === 'auto') ) {
				$df_options .= "," . esc_html__( "WooCommerce", 'webinarignition' ) . " [woocommerce]";
//			}

            $df_options .= "," . esc_html__( "Other payment gateways or market places", 'webinarignition' ) . " [other]";

			webinarignition_display_option(
				$input_get['id'],
				$webinar_data->paid_button_type,
				esc_html__( "Payment Provider", 'webinarignition' ),
				"paid_button_type",
				esc_html__( "Choose your payment provider.", 'webinarignition' ),
                $df_options
			);
			?>
            <div class="paid_button_type" id="paid_button_type_custom" style="display: none;">
				<?php
				webinarignition_display_textarea(
					$input_get['id'],
					$webinar_data->paid_button_custom,
					esc_html__( "Easiest (Raw) HTML only Opt-in Code:", 'webinarignition' ),
					"paid_button_custom",
					esc_html__( "This is the custom code for your order button. Can be shortcode.", 'webinarignition' ),
					esc_html__( "This is the custom code for your order button. Can be shortcode.", 'webinarignition' )
				);

				webinarignition_display_field(
					$input_get['id'],
					! empty( $webinar_data->paid_code ) ? $webinar_data->paid_code : ( $paid_code = webinarignition_generate_key( 32 ) ),
					esc_html__( "Secret Paid Code", 'webinarignition' ),
					"paid_code",
					__( "This is the own chosen secret code used to verify that people returning from a successful payment are presented the webinar.<br/><strong>Do not edit this code after people started paying for your webinar, or they won't be able to access your webinar anymore!</strong>", 'webinarignition' ),
					""
				);
				webinarignition_display_field(
					$input_get['id'],
					add_query_arg(( $paid_code ?? $webinar_data->paid_code ), '', WebinarignitionManager::get_permalink($webinar_data, 'registration')),
					esc_html__( "Thank You Page URL", 'webinarignition' ),
					"paid_thank_you_url",
					esc_html__( "This is the url that you take people who purchased the webinar so they can sign up...", 'webinarignition' ),
					""
				);
				webinarignition_display_field(
					$input_get['id'],
					get_permalink( $data->postID ) . "?live&" . md5( ( isset( $paid_code ) ? $paid_code : $webinar_data->paid_code ) ),
					esc_html__( "Paid Webinar URL", 'webinarignition' ),
					"paid_webinar_url",
					esc_html__( "This is the new webinar live URL webinar - only paid members should know this URL...", 'webinarignition' ),
					""
				);
				?>
            </div>

            <!-- STRIPE SECTION -->
            <div class="paid_button_type" id="paid_button_type_stripe" style="display: none;">
				<?php
				webinarignition_display_stripe_stuff(
					$input_get['id'],
					$webinar_data->payment_form,
					esc_html__( "Instructions for Stripe", 'webinarignition' ),
					"payment_form",
					""
				);
				$stripe_paid_btn_copy = isset($webinar_data->stripe_paid_btn_copy) ? $webinar_data->stripe_paid_btn_copy : 'Order Now!';
				webinarignition_display_field(
					$input_get['id'],
					$stripe_paid_btn_copy,
					esc_html__( "Order Button Copy", 'webinarignition' ),
					"stripe_paid_btn_copy",
					esc_html__( "This is the copy that is displayed on the order button...", 'webinarignition' ),
					esc_html__( "Order Now!", 'webinarignition' )
				);
				webinarignition_display_color(
					$input_get['id'],
					$webinar_data->paid_btn_color,
					esc_html__( "Order button color...", 'webinarignition' ),
					"paid_btn_color",
					esc_html__( "This is the color of the order button...", 'webinarignition' ),
					"#000000"
				);
				webinarignition_display_field(
					$input_get['id'],
					$webinar_data->stripe_secret_key,
					esc_html__( "Stripe Secret Key", 'webinarignition' ),
					"stripe_secret_key",
					esc_html__( "Set your Secret Key: remember to change this to your Live Secret Key in production. Get your keys here https://dashboard.stripe.com/account/apikeys", 'webinarignition' ),
					esc_html__( "Stripe Secret Key", 'webinarignition' )
				);
				webinarignition_display_field(
					$input_get['id'],
					$webinar_data->stripe_publishable_key,
					esc_html__( "Stripe Publishable Key", 'webinarignition' ),
					"stripe_publishable_key",
					esc_html__( "Set your Publishable Key: remember to change this to your Live Publishable Key in production. Get your keys here https://dashboard.stripe.com/account/apikeys", 'webinarignition' ),
					esc_html__( "Stripe Publishable Key", 'webinarignition' )
				);
				webinarignition_display_field(
					$input_get['id'],
					$webinar_data->stripe_charge,
					esc_html__( "Charge", 'webinarignition' ),
					"stripe_charge",
					esc_html__( "Your charge (in cents; for Stripe payment only)", 'webinarignition' ),
					esc_html__( "Ex: 12000", 'webinarignition' )
				);

				webinarignition_display_field(
					$input_get['id'],
					! empty( $webinar_data->stripe_currency ) ? $webinar_data->stripe_currency : 'usd',
					esc_html__( "Payment currency code", 'webinarignition' ),
					"stripe_currency",
					__( 'Payment currency. See Stripe supported currencies <a target="_blank" href="https://stripe.com/docs/currencies">here.</a> ', 'webinarignition' ),
					esc_html__( "USD", 'webinarignition' )
				);

				webinarignition_display_field(
					$input_get['id'],
					$webinar_data->stripe_charge_description,
					esc_html__( "Stripe Charge Description", 'webinarignition' ),
					"stripe_charge_description",
					esc_html__( "Your charge descrfiption(for Stripe payment only)", 'webinarignition' ),
					esc_html__( "Charge for amazing webinar", 'webinarignition' )
				);

				webinarignition_display_field(
					$input_get['id'],
					! empty( $webinar_data->paid_code ) ? $webinar_data->paid_code : ( $paid_code = webinarignition_generate_key( 32 ) ),
					esc_html__( "Secret Paid Code", 'webinarignition' ),
					"paid_code",
					__( "This is the own chosen secret code used to verify that people returning from a successful payment are presented the webinar.<br/><strong>Do not edit this code after people started paying for your webinar, or they won't be able to access your webinar anymore!</strong>", 'webinarignition' ),
					""
				);
				webinarignition_display_field(
					$input_get['id'],
					add_query_arg(( $paid_code ?? $webinar_data->paid_code ), '', WebinarignitionManager::get_permalink($webinar_data, 'registration')),
					esc_html__( "Thank You Page URL", 'webinarignition' ),
					"paid_thank_you_url",
					esc_html__( "This is the url that you take people who purchased the webinar so they can sign up...", 'webinarignition' ),
					""
				);
				webinarignition_display_field(
					$input_get['id'],
					get_permalink( $data->postID ) . "?live&" . md5( ( isset( $paid_code ) ? $paid_code : $webinar_data->paid_code ) ),
					esc_html__( "Paid Webinar URL", 'webinarignition' ),
					"paid_webinar_url",
					esc_html__( "This is the new webinar live URL webinar - only paid members should know this URL...", 'webinarignition' ),
					""
				);
				?>
            </div>
            <!-- STRIPE SECTION END		-->

            <!-- PAYPAL SECTION START-->
            <div class="paid_button_type" id="paid_button_type_paypal" style="display: none;">
                <div id="wi_checkout_url_field">
		            <?php
		            $wi_dashboard_url = add_query_arg('page', 'webinarignition-dashboard', get_admin_url());

		            if( !isset($webinar_data->paid_pay_url) ) {
			            $webinar_data->paid_pay_url = '';
                    }

		            webinarignition_display_field(
			            $input_get['id'],
			            $webinar_data->paid_pay_url,
			            esc_html__( "Payment Checkout URL", 'webinarignition' ),
			            "paid_pay_url",
			            esc_html__( "This is the URL for your payment page, check out page for Paypal, 1shoppingcart, etc...", 'webinarignition' ),
			            "",
			            "",
			            [
				            'data-help-woocommerce' => esc_html__( 'This is the URL for your payment page, check out page for WooCommerce', 'webinarignition' ),
				            'data-url-woocommerce'  => esc_html__( 'Enter a valid product URL. Ex: https://your-domain.com/checkout/?add-to-cart=xxx', 'webinarignition' ),
				            'data-help-paypal'      => esc_html__( 'This is the URL for your payment page, check out page for PayPal', 'webinarignition' ),
				            'data-url-paypal'       => esc_html__( 'Enter a valid Paypal URL. Ex: https://paypal.com/order-product', 'webinarignition' ),
				            'data-message-woocommerce' => sprintf(__( 'Your plan does not allows you to use the WooCommerce checkout URL feature. If you want more, please upgrade your plan. Click <a href=\'%s\' target=\'_blank\'>here</a>!', 'webinarignition' ),$wi_dashboard_url),
				            'data-help-other' => sprintf(__( 'Paid links will be sharable and user data will not filled out automatically. When user data can be changed it could be used by others again and again. You can get a quote for a secure integration via the <a href=\'%s\' target=\'_blank\'>Support Chat</a>.', 'webinarignition' ), 'https://support.webinarignition.com'),
				            'data-url-other'  => esc_html__( 'Enter a valid product URL. Ex: https://your-domain.com/my-product', 'webinarignition' ),
			            ]
		            );
		            ?>
                </div>

                <?php
                $paypal_paid_btn_copy = isset($webinar_data->paypal_paid_btn_copy) ? $webinar_data->paypal_paid_btn_copy : esc_html__( "Order Now!", 'webinarignition' );
				webinarignition_display_field(
					$input_get['id'],
					$paypal_paid_btn_copy,
					esc_html__( "Order Button Copy", 'webinarignition' ),
					"paypal_paid_btn_copy",
					esc_html__( "This is the copy that is displayed on the order button...", 'webinarignition' ),
					esc_html__( "Order Now!", 'webinarignition' )
				);

				webinarignition_display_color(
					$input_get['id'],
					$webinar_data->paid_btn_color,
					esc_html__( "Order button color...", 'webinarignition' ),
					"paid_btn_color",
					esc_html__( "This is the color of the order button...", 'webinarignition' ),
					"#000000"
				);

				webinarignition_display_field(
					$input_get['id'],
					! empty( $webinar_data->paid_code ) ? $webinar_data->paid_code : ( $paid_code = webinarignition_generate_key( 32 ) ),
					esc_html__( "Secret Paid Code", 'webinarignition' ),
					"paid_code",
					__( "This is the own chosen secret code used to verify that people returning from a successful payment are presented the webinar.<br/><strong>Do not edit this code after people started paying for your webinar, or they won't be able to access your webinar anymore!</strong>", 'webinarignition' ),
					""
				);

				webinarignition_display_field(
					$input_get['id'],
					add_query_arg(( $paid_code ?? $webinar_data->paid_code ), '', WebinarignitionManager::get_permalink($webinar_data, 'registration')),
					esc_html__( "Thank You Page URL", 'webinarignition' ),
					"paid_thank_you_url",
					esc_html__( "This is the url that you take people who purchased the webinar so they can sign up...", 'webinarignition' ),
					""
				);

				webinarignition_display_field(
					$input_get['id'],
					get_permalink( $data->postID ) . "?live&" . md5( ( isset( $paid_code ) ? $paid_code : $webinar_data->paid_code ) ),
					esc_html__( "Paid Webinar URL", 'webinarignition' ),
					"paid_webinar_url",
					esc_html__( "This is the new webinar live URL webinar - only paid members should know this URL...", 'webinarignition' ),
					""
				);
				?>
            </div>
            <script>
                jQuery(document).ready(function ($) {
                    var $thank_you_url = $('#paid_thank_you_url'),
                        $webinar_url = $('#paid_webinar_url');
                    $thank_you_url.add($webinar_url).attr('readonly', 'readonly').on( "click", function () {
                        $(this).select();
                    });
                    $('#paid_code').on('change keyup', function () {
                        $thank_you_url.val('<?php echo get_permalink($data->postID) . "?"; ?>' + $(this).val());
                        $webinar_url.val('<?php echo get_permalink($data->postID) . "?live&"; ?>' + CryptoJS.MD5($(this).val()));
                    });

                    jQuery(document).on('blur', '#paid_pay_url.inputField.elem.paypal_check', function (e) {
                        var isValid = false;

                        try {
                            let urlString = (new URL($(this).val()));
                            isValid = urlString.hostname.split('.').slice(-2).join('.') === 'paypal.com';
                            if(!isValid) {
                                $(this).val('');
                            }
                        } catch(err) {
                            $(this).val('');
                        }
                    });
                });
            </script>
        </div>
    </div>

	<?php
	webinarignition_display_edit_toggle(
		"cog",
		esc_html__( "Optin Form Creator / AR Integration", 'webinarignition' ),
		"we_edit_lp_ar",
		esc_html__( "Setup your integration with your Auto-Responder", 'webinarignition' )
	);
	?>

    <div id="we_edit_lp_ar" class="we_edit_area">
		<?php
		WebinarignitionPowerupsShortcodes::show_shortcode_description('reg_optin_form', $webinar_data, true, true)
		?>
		<?php
		if ( $webinar_data->webinar_date !== "AUTO" ) {
			webinarignition_display_option(
				$input_get['id'],
				$webinar_data->lp_fb_button,
				esc_html__( "Facebook Connect Button", 'webinarignition' ),
				"lp_fb_button",
				esc_html__( "You can choose to use the Facebook connect button, by default its not shown, and if you do enable it, you must setup the FB connect settings in order for it to work...", 'webinarignition' ),
				esc_html__( "Disable - FB Connect", 'webinarignition' ) . " [hide]," . esc_html__( "Enable - FB Connect", 'webinarignition' ) . " [show]"
			);
		}
		?>

        <div class="lp_fb_button" id="lp_fb_button_show" style="display: none;">
			<?php
			webinarignition_display_field(
				$input_get['id'],
				$webinar_data->fb_id,
				esc_html__( "Facebook App ID", 'webinarignition' ),
				"fb_id",
				esc_html__( "This is your FB App ID", 'webinarignition' ),
				esc_html__( "Get From Facebook App...", 'webinarignition' )
			);

			webinarignition_display_field(
				$input_get['id'],
				$webinar_data->fb_secret,
				esc_html__( "Facebook App Secret", 'webinarignition' ),
				"fb_secret",
				esc_html__( "This is your FB App Secret", 'webinarignition' ),
				esc_html__( "Get From Facebook App...", 'webinarignition' )
			);

			webinarignition_display_field(
				$input_get['id'],
				$webinar_data->lp_fb_copy,
				esc_html__( "Facebook Connect Button Copy", 'webinarignition' ),
				"lp_fb_copy",
				esc_html__( "This is the text that is shown on the Facebook Connect sign up button...", 'webinarignition' ),
				esc_html__( "e.g. Register With Facebook", 'webinarignition' )
			);

			webinarignition_display_field(
				$input_get['id'],
				$webinar_data->lp_fb_or,
				esc_html__( "Custom Copy 'OR'", 'webinarignition' ),
				"lp_fb_or",
				esc_html__( "You can edit the copy displayed under the FB connect button...", 'webinarignition' ),
				esc_html__( "e.g. OR", 'webinarignition' )
			);
			webinarignition_display_info(
				esc_html__( "Note: FB Button", 'webinarignition' ),
				esc_html__( "You will need to make sure you setup the FB Connect info, it is editable at the bottom of this page...", 'webinarignition' )
			);
			?>
        </div>

		<?php
		webinarignition_display_textarea(
			$input_get['id'],
			$webinar_data->ar_code,
			esc_html__( "Easiest (Raw) HTML only Opt-in Code:", 'webinarignition' ),
			"ar_code",
			esc_html__( 'This should be the easiest (RAW) html version of the optin code your AR service provides you...', 'webinarignition') . "<br><br><a href='https://webinarignition.tawk.help/article/aweber-integration' target='_blank' ><b>".esc_html__( 'Aweber Integration Tutorial', 'webinarignition')."</b></a><br><br><a href='https://webinarignition.tawk.help/article/mailchimp-integration' target='_blank' ><b>".esc_html__( 'MailChimp Integration Tutorial', 'webinarignition')."</b></a>",
			esc_html__( "The RAW html form code given to you by your AR service...", 'webinarignition' )
		);
		?>
        <div class="editSection section--ar_fields">
            <div id="ar_templates" class="hidden">
                <div class="available-fields">
                    <li class="wi-form-field wi-form-field--available">
                        <span class="wi-field-add" data-hidden="false" data-name=""><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
                        {field_name}
                    </li>
                    <li class="wi-form-field wi-form-field--hidden">
                        <span class="wi-field-add" data-hidden="true" data-names=""><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
                        {field_names}
                    </li>
                </div>
                <div class="labels">
                    <input type="hidden" class="ar_name" value="<?php esc_html_e( 'First Name', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_lname" value="<?php esc_html_e( 'Last Name', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_email" value="<?php esc_html_e( 'Email', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_phone" value="<?php esc_html_e( 'Phone', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_1" value="<?php esc_html_e( 'Custom Field', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_2" value="<?php esc_html_e( 'Custom Field', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_3" value="<?php esc_html_e( 'Custom Field', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_4" value="<?php esc_html_e( 'Custom Field', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_5" value="<?php esc_html_e( 'Custom Checkbox Field', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_6" value="<?php esc_html_e( 'Custom Checkbox Field', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_7" value="<?php esc_html_e( 'Custom Textarea Field', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_8" value="<?php esc_html_e( 'Custom Hidden Field', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_9" value="<?php esc_html_e( 'Custom Hidden Field', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_10" value="<?php esc_html_e( 'Custom Hidden Field', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_11" value="<?php esc_html_e( 'Custom Hidden Field', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_12" value="<?php esc_html_e( 'Custom Hidden Field', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_13" value="<?php esc_html_e( 'Custom Hidden Field', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_14" value="<?php esc_html_e( 'Custom Hidden Field', 'webinarignition' ) ?>"/>

                    <input type="hidden" class="ar_custom_15" value="<?php esc_html_e( 'Dropdown Select Field', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_16" value="<?php esc_html_e( 'Dropdown Select Field', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_17" value="<?php esc_html_e( 'Dropdown Select Field', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_custom_18" value="<?php esc_html_e( 'Dropdown Select Field', 'webinarignition' ) ?>"/>

                    <input type="hidden" class="ar_privacy_policy" value="<?php esc_html_e( 'Privacy Policy', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_terms_and_conditions" value="<?php esc_html_e( 'Terms and Conditions', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_mailing_list" value="<?php esc_html_e( 'Mailing List', 'webinarignition' ) ?>"/>
                    <input type="hidden" class="ar_webinar_title" value="<?php esc_html_e( 'Webinar Title', 'webinarignition' ) ?>">
                    <input type="hidden" class="ar_webinar_host" value="<?php esc_html_e( 'Webinar Host', 'webinarignition' ) ?>">
                    <input type="hidden" class="ar_webinar_url" value="<?php esc_html_e( 'Webinar URL', 'webinarignition' ) ?>">
                    <input type="hidden" class="ar_webinar_date" value="<?php esc_html_e( 'Webinar Date', 'webinarignition' ) ?>">
                    <input type="hidden" class="ar_webinar_time" value="<?php esc_html_e( 'Webinar Time', 'webinarignition' ) ?>">
                    <input type="hidden" class="ar_webinar_timezone" value="<?php esc_html_e( 'Webinar Time Zone', 'webinarignition' ) ?>">
                    <input type="hidden" class="ar_webinar_registration_date" value="<?php esc_html_e( 'Webinar Registration Date', 'webinarignition' ) ?>">
                    <input type="hidden" class="ar_webinar_registration_time" value="<?php esc_html_e( 'Webinar Registration Time', 'webinarignition' ) ?>">
                    <input type="hidden" class="ar_utm_source" value="<?php esc_html_e( 'UTM Source', 'webinarignition' ) ?>">

                </div>
                <div class="label_names">
                    <input type="hidden" class="ar_name" value="lp_optin_name"/>
                    <input type="hidden" class="ar_lname" value="lp_optin_lname"/>
                    <input type="hidden" class="ar_email" value="lp_optin_email"/>
                    <input type="hidden" class="ar_phone" value="lp_optin_phone"/>
                    <input type="hidden" class="ar_custom_1" value="lp_optin_custom_1"/>
                    <input type="hidden" class="ar_custom_2" value="lp_optin_custom_2"/>
                    <input type="hidden" class="ar_custom_3" value="lp_optin_custom_3"/>
                    <input type="hidden" class="ar_custom_4" value="lp_optin_custom_4"/>
                    <input type="hidden" class="ar_custom_5" value="lp_optin_custom_5"/>
                    <input type="hidden" class="ar_custom_6" value="lp_optin_custom_6"/>
                    <input type="hidden" class="ar_custom_7" value="lp_optin_custom_7"/>
                    <input type="hidden" class="ar_custom_8" value="lp_optin_custom_8"/>
                    <input type="hidden" class="ar_custom_9" value="lp_optin_custom_9"/>
                    <input type="hidden" class="ar_custom_10" value="lp_optin_custom_10"/>
                    <input type="hidden" class="ar_custom_11" value="lp_optin_custom_11"/>
                    <input type="hidden" class="ar_custom_12" value="lp_optin_custom_12"/>
                    <input type="hidden" class="ar_custom_13" value="lp_optin_custom_13"/>
                    <input type="hidden" class="ar_custom_14" value="lp_optin_custom_14"/>

                    <input type="hidden" class="ar_custom_15" value="lp_optin_custom_15"/>
                    <input type="hidden" class="ar_custom_16" value="lp_optin_custom_16"/>
                    <input type="hidden" class="ar_custom_17" value="lp_optin_custom_17"/>
                    <input type="hidden" class="ar_custom_18" value="lp_optin_custom_18"/>

                    <input type="hidden" class="ar_utm_source" value="UTM Source">
                    <input type="hidden" class="ar_privacy_policy" value="lp_optin_privacy_policy"/>
                    <input type="hidden" class="ar_terms_and_conditions" value="lp_optin_terms_and_conditions"/>
                    <input type="hidden" class="ar_mailing_list" value="lp_optin_mailing_list"/>

                </div>
                <div class="form-builder">
                    <li class="wi-form-fieldblock wi-form-fieldblock-visible">
                        <div class="field-block--table">
                            <div class="field-block field-block--cell">
                                <small class="sublabel" style="background: #0074A2; color: white; border: none;"><?php esc_html_e( 'Field Type (Visible):', 'webinarignition' ) ?></small>
                                <input type="text" class="fieldblock field__ar-label" value="" disabled="disabled"/>
                            </div>
                            <div class="field-block field-block--cell">
                                <small class="sublabel"><?php esc_html_e( 'Map to AR Form Field', 'webinarignition' ) ?> (<span></span>):</small>
                                <select class="fieldblock field__ar-mapping">
                                    <option value=""><?php esc_html_e( '* Not mapped', 'webinarignition' ) ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="field-block">
                            <small id="placeHolderText" class="sublabel"><?php esc_html_e( 'Field label / placeholder:', 'webinarignition' ) ?></small>
                            <input class="fieldblock field__label" type="text"/>
                        </div>
                        <div class="field__actions">
                            <input type="checkbox" class="required_ar" style="width: 20px !important; height: 20px !important; margin-right:15px;"> <span><?php esc_html_e( "Required?", 'webinarignition' ) ?></span>
                            <a href="#" class="field__action js__fieldblock-remove field__action--remove"><?php esc_html_e( 'Remove', 'webinarignition' ) ?></a>
                        </div>
                        <div class="hidden">
                            <input type="hidden" class="field__label-name"/>
                            <input type="hidden" class="field__ar-name"/>
                        </div>
                    </li>


                    <li class="wi-form-fieldblock wi-form-fieldblock-custom">
                        <div class="field-block--table">
                            <div class="field-block field-block--cell">
                                <small class="sublabel" style="background: #0074A2; color: white; border: none;"><?php esc_html_e( 'Field Type (Visible):', 'webinarignition' ) ?></small>
                                <input type="text" class="fieldblock field__ar-label" value="" disabled="disabled"/>
                            </div>
                            <div class="field-block field-block--cell">
                                <small class="sublabel"><?php esc_html_e( 'Map to AR Form Field', 'webinarignition' ) ?> (<span></span>):</small>
                                <select class="fieldblock field__ar-mapping">
                                    <option value=""><?php esc_html_e( "* Not mapped", 'webinarignition' ) ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="field-block">
                            <small id="placeHolderText" class="sublabel"><?php esc_html_e( 'Hidden Field Value:', 'webinarignition' ) ?></small>
                            <input class="fieldblock field__label" type="text"/>
                        </div>
                        <div class="field__actions">
                            <!--					<a href="#" class="field__action js_fieldblock-move field__action--move">Order</a>-->
                            <a href="#" class="field__action js__fieldblock-remove field__action--remove"><?php esc_html_e( 'Remove', 'webinarignition' ) ?></a>
                        </div>
                        <div class="hidden">
                            <input type="hidden" class="field__label-name"/>
                            <input type="hidden" class="field__ar-name"/>
                        </div>
                    </li>

                    <li class="wi-form-fieldblock wi-form-fieldblock-select">
                        <div class="field-block--table">
                            <div class="field-block field-block--cell">
                                <small class="sublabel" style="background: #0074A2; color: white; border: none;"><?php esc_html_e( 'Field Type (Visible):', 'webinarignition' ) ?></small>
                                <input type="text" class="fieldblock field__ar-label" value="" disabled="disabled"/>
                            </div>
                            <div class="field-block field-block--cell">
                                <small class="sublabel"><?php esc_html_e( 'Map to AR Form Field', 'webinarignition' ) ?> (<span></span>):</small>
                                <select class="fieldblock field__ar-mapping">
                                    <option value=""><?php esc_html_e( '* Not mapped', 'webinarignition' ) ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="field-block">
                            <small id="placeHolderText" class="sublabel"><?php esc_html_e( 'Field label:', 'webinarignition' ) ?></small>
                            <input class="fieldblock field__label" type="text"/>
                        </div>
                        <div class="field-block">
                            <small id="placeHolderText" class="sublabel"><?php esc_html_e( 'Field options:', 'webinarignition' ) ?></small>
                            <textarea class="fieldblock field__options"></textarea>
                            <p>
								<?php esc_html_e( 'Enter each dropdown option on a new line.', 'webinarignition' ) ?><br>

                                <code><?php esc_html_e( 'Green', 'webinarignition' ) ?></code><br>
                                <code><?php esc_html_e( 'Blue', 'webinarignition' ) ?></code><br>

								<?php esc_html_e( 'For more control, you may specify both a value (save to database) and label (visible in dropdown) like this:', 'webinarignition' ) ?><br>

                                <code>AU :: <?php esc_html_e( 'Australia', 'webinarignition' ) ?></code><br>
                                <code>US :: <?php esc_html_e( 'USA', 'webinarignition' ) ?></code><br>

								<?php esc_html_e( 'If you want allow empty value put like this:', 'webinarignition' ) ?><br>

                                <code> :: -- <?php esc_html_e( 'select one', 'webinarignition' ) ?> -- </code><br>
                                <code>green :: <?php esc_html_e( 'Green', 'webinarignition' ) ?></code><br>
                                <code>blue :: <?php esc_html_e( 'Blue', 'webinarignition' ) ?></code><br>

                                <strong>
                                    <a href="https://webinarignition.tawk.help/article/dropdown-fields-in-webinar-registration" target="_blank">
										<?php esc_html_e( 'Help?', 'webinarignition' ) ?>
                                    </a>
                                </strong>
                            </p>
                        </div>
                        <div class="field__actions">
                            <input type="checkbox" class="required_ar" style="width: 20px !important; height: 20px !important; margin-right:15px;"> <span><?php esc_html_e( "Required?", 'webinarignition' ) ?></span>
                            <a href="#" class="field__action js__fieldblock-remove field__action--remove"><?php esc_html_e( 'Remove', 'webinarignition' ) ?></a>
                        </div>
                        <div class="hidden">
                            <input type="hidden" class="field__label-name"/>
                            <input type="hidden" class="field__ar-name"/>
                        </div>
                    </li>

                    <li class="wi-form-fieldblock wi-form-fieldblock-invisible">
                        <div class="field-block--table">
                            <div class="field-block field-block--cell">
                                <small class="sublabel" style="background: #EEEEEE; color: #777777; border: none;" ><?php esc_html_e( 'Field Type (Hidden):', 'webinarignition' ) ?></small>
                                <input type="text" class="fieldblock field__ar-label" value="" disabled="disabled"/>
                            </div>
                            <div class="field-block field-block--cell">
                                <small class="sublabel"><?php esc_html_e( 'Map to AR Form Field', 'webinarignition' ) ?> (<span></span>):</small>
                                <select class="fieldblock field__ar-mapping">
                                    <option value=""><?php esc_html_e( '* Not mapped', 'webinarignition' ) ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="field__actions">

                            <a href="#" class="field__action js__fieldblock-remove field__action--remove"><?php esc_html_e( 'Remove', 'webinarignition' ) ?></a>
                        </div>
                        <div class="hidden">
                            <input type="hidden" class="field__label-name"/>
                            <input type="hidden" class="field__ar-name"/>
                        </div>
                    </li>
                </div>
                <div class="form-builder-hidden-field">
                    <div class="field-block--table field-group">
                        <div class="field-block field-block--cell">
                            <small class="sublabel"><?php esc_html_e( 'Field name:', 'webinarignition' ) ?></small>
                            <input type="text" class="fieldblock fieldblock__name" value=""/>
                        </div>
                        <div class="field-block field-block--cell">
                            <small class="sublabel"><?php esc_html_e( 'Field value:', 'webinarignition' ) ?></small>
                            <input type="text" class="fieldblock fieldblock__value" value=""/>
                        </div>
                    </div>
                </div>
            </div>
            <section class="wi wi__ar_section extracted-form_fields">

                <h2><?php esc_html_e( 'Available Fields:', 'webinarignition' ) ?> </h2>
                <h3><?php esc_html_e( 'Visible Fields', 'webinarignition' ) ?></h3>
                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_name"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'First Name field', 'webinarignition' ) ?>
                </div>
                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_lname"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Last Name field', 'webinarignition' ) ?>
                </div>
                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_email"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Email field', 'webinarignition' ) ?>
                </div>
                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_phone"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Phone field', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_privacy_policy"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Privacy Policy Checkbox', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_terms_and_conditions"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Terms and Conditions Checkbox', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_mailing_list"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Mailing List Checkbox', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_1"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Custom Field', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_2"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Custom Field', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_3"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Custom Field', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_4"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Custom Field', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_5"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Custom Checkbox Field', 'webinarignition' ) ?>
                </div>


                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_6"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Custom Checkbox Field', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_7"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Custom Textarea Field', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_8"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Custom Hidden Field', 'webinarignition' ) ?>
                </div>


                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_9"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Custom Hidden Field', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_10"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Custom Hidden Field', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_11"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Custom Hidden Field', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_12"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Custom Hidden Field', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_13"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Custom Hidden Field', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_14"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Custom Hidden Field', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_15"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Dropdown Select Field', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_16"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Dropdown Select Field', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_17"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Dropdown Select Field', 'webinarignition' ) ?>
                </div>

                <div class="wi-form-field">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_custom_18"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Dropdown Select Field', 'webinarignition' ) ?>
                </div>


                <h3 style="margin-top: 20px;"><?php esc_html_e( 'Hidden Fields', 'webinarignition' ) ?></h3>
                <div class="wi-form-field wi-form-field-invisible">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_webinar_title"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Webinar Title', 'webinarignition' ) ?>
                </div>
                <div class="wi-form-field wi-form-field-invisible">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_webinar_host"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Webinar Host', 'webinarignition' ) ?>
                </div>
                <div class="wi-form-field wi-form-field-invisible">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_webinar_url"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Webinar URL', 'webinarignition' ) ?>
                </div>
                <div class="wi-form-field wi-form-field-invisible">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_webinar_date"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Webinar Date', 'webinarignition' ) ?>
                </div>
                <div class="wi-form-field wi-form-field-invisible">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_webinar_time"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Webinar Time', 'webinarignition' ) ?>
                </div>
                <div class="wi-form-field wi-form-field-invisible">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_webinar_timezone"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Webinar Time Zone', 'webinarignition' ) ?>
                </div>
                <div class="wi-form-field wi-form-field-invisible">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_webinar_registration_date"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Webinar Registration Date', 'webinarignition' ) ?>
                </div>
                <div class="wi-form-field wi-form-field-invisible">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_webinar_registration_time"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'Webinar Registration Time', 'webinarignition' ) ?>
                </div>
                <div class="wi-form-field wi-form-field-invisible">
                    <span class="wi-field-add" data-hidden="false" data-name="ar_utm_source"><?php esc_html_e( 'add', 'webinarignition' ) ?></span>
					<?php esc_html_e( 'UTM Source', 'webinarignition' ) ?>
                </div>

                <ul id="wi-available-fields" class="content"></ul>
                <div id="ar_available_mappings" class="hidden" data-mappings=""></div>
                <div class="clear"></div>
            </section>
            <section class="wi wi__ar_section form_builder">
                <h2><?php esc_html_e( 'Form Builder', 'webinarignition' ) ?></h2>

                <div class="field-block--table">
                    <div class="field-block field-block--cell field-block--form-action">
                        <label for="ar_url"><?php esc_html_e( 'Form Action URL:', 'webinarignition' ) ?></label>
                        <input type="text" id="ar_url"/>
                    </div>
                    <div class="field-block field-block--cell field-block--form-method">
                        <label for="ar_method"><?php esc_html_e( 'Form Method:', 'webinarignition' ) ?></label>
                        <select id="ar_method">
                            <option value="post">POST</option>
                            <option value="get">GET</option>
                        </select>
                    </div>
                    <div class="wi-form-field" style="background: #EEEEEE; color: #777;">
                        <span class="wi-field-set js__set-form-options" style="color: white;" ><?php esc_html_e( "set", 'webinarignition' ) ?></span>
						<?php esc_html_e( 'Set Action URL and Method from raw html', 'webinarignition' ) ?>
                    </div>
                </div>
                <div class="field-block">
                    <label>Form Fields:<?php esc_html_e( '', 'webinarignition' ) ?></label>
                    <ul id="wi-form-builder" class="wi-form-builder">
                        <li id="form-builder-gdpr-heading" style="display: none; ">
                            <div class="editSection" style="padding: 0;">

                                <div class="inputTitle" style="border: none; width: 100%;">
                                    <div class="inputTitleCopy"><h1><?php esc_html_e( 'GDPR Heading', 'webinarignition' ) ?></h1></div>
                                    <div class="inputTitleHelp"><?php esc_html_e( 'This is the heading that is shown above the GDPR fields.', 'webinarignition' ) ?></div>
                                </div>

                                <div class="inputSection" style="width: 100%;">
                                    <input class="inputField elem" placeholder="<?php esc_html_e( 'Ex. Please confirm that you:', 'webinarignition' ) ?>" type="text" name="gdpr_heading" id="gdpr_heading" value="<?= !empty($webinar_data->gdpr_heading) ? $webinar_data->gdpr_heading :  esc_html__( 'Please confirm that you:', 'webinarignition' ) ?>">
                                </div>
                                <br clear="left">

                            </div>
                        </li>
                    </ul>


                    <!--<label>Hidden Fields:</label>-->


                    <div class="wi-form-fields--hidden hidden">
                        <div id="wi-form-hidden-fields" class="fieldblock__content"></div>
                        <div class="field__actions">
                            <a href="#" class="field__action js__fieldblock-remove field__action--remove"> <?php esc_html_e( 'Remove Hidden Fields', 'webinarignition' ) ?></a>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </section>
            <div id="ar-settings" class="hidden ar-integration-settings">
				<?php
				$_props = array(
					'ar_url',
					'ar_method',
					'ar_name',
					'ar_lname',
					'ar_phone',
					'ar_email',
					'ar_privacy_policy',
					'ar_terms_and_conditions',
					'ar_mailing_list',
					'ar_webinar_title',
					'ar_webinar_url',
					'ar_webinar_date',
					'ar_webinar_time',
					'ar_webinar_timezone',
					'ar_webinar_registration_date',
					'ar_webinar_registration_time',
					'ar_utm_source',
					'ar_webinar_host',
					'ar_hidden',
					'lp_optin_name',
					'lp_optin_lname',
					'lp_optin_email',
					'lp_webinar_host',
					'lp_optin_phone',
					'lp_optin_privacy_policy',
					'lp_optin_terms_and_conditions',
					'lp_optin_mailing_list',
					'ar_fields_order',
					'ar_required_fields',
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

					'lp_optin_custom_1',
					'lp_optin_custom_2',
					'lp_optin_custom_3',
					'lp_optin_custom_4',
					'lp_optin_custom_5',
					'lp_optin_custom_6',
					'lp_optin_custom_7',
					'lp_optin_custom_8',
					'lp_optin_custom_9',
					'lp_optin_custom_10',
					'lp_optin_custom_11',
					'lp_optin_custom_12',
					'lp_optin_custom_13',
					'lp_optin_custom_14',

					'lp_optin_custom_15',
					'lp_optin_custom_16',
					'lp_optin_custom_17',
					'lp_optin_custom_18',

					'lp_optin_custom_select_15',
					'lp_optin_custom_select_16',
					'lp_optin_custom_select_17',
					'lp_optin_custom_select_18',

				);
				foreach ( $_props as $_prop ) {
					if ( property_exists( $webinar_data, $_prop ) ) {
						if ( ! is_array( $webinar_data->$_prop ) ) {
							if (false !== strpos($_prop, 'lp_optin_custom_select')) {
								?>
                                <textarea name="<?php echo $_prop; ?>"><?php echo htmlentities( $webinar_data->$_prop, ENT_QUOTES, "UTF-8" ); ?></textarea>
								<?php
							} else {
								?>
                                <input type="hidden" name="<?php echo $_prop; ?>"
                                       value="<?php echo htmlentities( $webinar_data->$_prop, ENT_QUOTES, "UTF-8" ); ?>"/>
								<?php
							}
							?>
							<?php
						} else {
							foreach ( $webinar_data->$_prop as $_key => $_val ) {
								?>
                                <input type="hidden" name="<?php echo $_prop; ?>[<?php echo $_key; ?>]"
                                       value="<?php echo htmlentities( $_val, ENT_QUOTES, "UTF-8" ); ?>"/>
								<?php
							}
						}
					}
				}
				?>
            </div>
        </div>

		<?php
		webinarignition_display_option(
			$input_get['id'],
			$webinar_data->ar_custom_date_format,
			esc_html__( "Custom Date Format", 'webinarignition' ),
			"ar_custom_date_format",
			esc_html__( "By default the AR form will submit date values in MM-DD-YYYY format and in most cases you can leave this at the default setting. But if your AR service requires you to use a different format, you can change it here.", 'webinarignition' ),
			"MM-DD-YYYY [MM-DD-YYYY], DD-MM-YYYY [DD-MM-YYYY], YYYY-MM-DD [YYYY-MM-DD]"
		);

		// fix :: ar integration test
		// --------------------------------------------------------------------------------------
        $ar_save_button = sprintf('<div style="margin-top:6px;display: inline-block" id="wi_test_ar" class="grey-btn">%s</div>',esc_html__('Save & Test AR Integration', 'webinarignition'));
		webinarignition_display_info
		(
			esc_html__( "AR Integration Help", 'webinarignition' ),
			sprintf( esc_html__( 'Use the button below to test your AR Integration setup. %s', 'webinarignition' ), $ar_save_button)
		);
		?>

        <script>
            jQuery(document).ready(function ($)
            {
                $('#wi_test_ar').on('click', function (e)
                {

                    e.preventDefault();
                    $trigger = $(this);
                    var bkpHtm = $trigger.html();
                    $trigger.html('<?php esc_html_e( "Saving...", 'webinarignition' ) ?>');

                    webinarignition_saveIt
                    (
                        function()
                        {
                            $trigger.html(bkpHtm);

                            modal
                            ({
                                name: $trigger.id+'_modal',
                                head: "<?php esc_html_e( "AR Integration Test", "webinarignition" ) ?>",
                                body: '<div style="width:100%; height:100%; padding:16px; overflow:auto">'+
                                    '<b>'+
                                    "<?php esc_html_e( "In order to test your AR integration setup, these steps may help:", "webinarignition" ) ?><br><br>"+
                                    '</b>'+
                                    "<li><?php _e( "Click the <strong>test button</strong> below.", 'webinarignition' ) ?></li>"+
                                    "<li><?php _e( "In the new window, fill in the registration form with dummy info for testing, then click <strong>register</strong>.", "webinarignition" ) ?></li>"+
                                    "<li><?php esc_html_e( "If all went well, the data should be in your autoresponder list. Check your autoresponder list to confirm.", "webinarignition" ) ?></li>"+
                                    '</div>',
                                foot: [
                                    {
                                        'name': "<?php esc_html_e('Test', 'webinarignition'); ?>",
                                        'callback': function() {
                                            window.open("<?php echo add_query_arg('artest', 1, WebinarignitionManager::get_permalink($webinar_data, 'registration')); ?>");
                                        }
                                    },
                                    {
                                        'name': "<?php esc_html_e('Integration Tutorials', 'webinarignition'); ?>",
                                        'callback': function() {
                                            window.open('https://webinarignition.tawk.help/category/e-mail-marketing-services');
                                        }
                                    },
                                    {
                                        'name': "<?php esc_html_e('Done', 'webinarignition'); ?>",
                                        'callback': function() {
                                            modal.exit();
                                        }
                                    }
                                ]
                            });
                        }
                    );
                });
            });
        </script>

		<?php
		// --------------------------------------------------------------------------------------
		webinarignition_display_option(
			$input_get['id'],
			$webinar_data->lp_optin_button,
			esc_html__( "Optin Button Style", 'webinarignition' ),
			"lp_optin_button",
			esc_html__( "You can choose between our optin button or your own custom image optin button...", 'webinarignition' ),
			esc_html__( "CSS Button", 'webinarignition' ) . " [color]," . esc_html__( "Custom Image Button", 'webinarignition' ) . " [image]"
		);
		?>

        <div class="lp_optin_button" id="lp_optin_button_color">
			<?php
			webinarignition_display_color(
				$input_get['id'],
				$webinar_data->lp_optin_btn_color,
				esc_html__( "Optin Button Color", 'webinarignition' ),
				"lp_optin_btn_color",
				esc_html__( "This is the color you want the optin button to be... by default it will be green...", 'webinarignition' ),
				"#74BB00"
			);
			?>
        </div>

        <div class="lp_optin_button" id="lp_optin_button_image" style="display:none;">
			<?php
			webinarignition_display_field_image_upd(
				$input_get['id'],
				$webinar_data->lp_optin_btn_image,
				esc_html__( "Custom Button Image URL", 'webinarignition' ),
				"lp_optin_btn_image",
				esc_html__( "This is the url for your custom optin button, for best results, it should be 327px wide...", 'webinarignition' ),
				esc_html__( "http://yoursite.com/custom-optin-image.png", 'webinarignition')
			);
			?>
        </div>

		<?php
		webinarignition_display_field(
			$input_get['id'],
			$webinar_data->lp_optin_btn,
			esc_html__( "Optin Button Copy", 'webinarignition' ),
			"lp_optin_btn",
			esc_html__( "This is the text that is shown on the optin button...", 'webinarignition' ),
			esc_html__( "e.g. Register For The Webinar", 'webinarignition' )
		);

		webinarignition_display_field(
			$input_get['id'],
			$webinar_data->lp_optin_spam,
			esc_html__( "Optin Spam Notice", 'webinarignition' ),
			"lp_optin_spam",
			esc_html__( "This is the spam notice that is shown under the optin area... Helps a lot for conversion rates...", 'webinarignition' ),
			esc_html__( "e.g. * we will not spam, sell, rent, or lease your information *", 'webinarignition' )
		);

		webinarignition_display_wpeditor(
			$input_get['id'],
			$webinar_data->lp_optin_closed,
			esc_html__( "Optin Closed Message", 'webinarignition' ),
			"lp_optin_closed",
			esc_html__( "This is message displayed when the webinar registration is closed.", 'webinarignition' )
		);

		webinarignition_display_option(
			$input_get['id'],
			$webinar_data->custom_ty_url_state,
			esc_html__( "Thank You URL", 'webinarignition' ),
			"custom_ty_url_state",
			esc_html__( "You can choose to keep default WebinarIgnition confirmation page, or redirect users to a custom URL.", 'webinarignition' ),
			esc_html__( "Keep Default", 'webinarignition' ) . " [hide]," . esc_html__( "Custom URL", 'webinarignition' ) . " [show]"
		);
		?>
        <div class="custom_ty_url_state" id="custom_ty_url_state_show" style="display: none;">
			<?php
			webinarignition_display_field(
				$input_get['id'],
				$webinar_data->custom_ty_url,
				esc_html__( "Custom Thank You URL", 'webinarignition' ),
				"custom_ty_url",
				esc_html__( "Instead of redirecting the user to the WebinarIgnition confirmation page, the user will be redirected to a custom thank you page that you define here.", 'webinarignition' ),
				"http://google.com"
			);
			?>
        </div>

		<?php
		webinarignition_display_option(
			$input_get['id'],
			isset($webinar_data->get_registration_notices_state) ? $webinar_data->get_registration_notices_state : '',
			esc_html__( "Get Registration Notices", 'webinarignition' ),
			"get_registration_notices_state",
			esc_html__( "You can choose to receive an email notification whenever someone registers.", 'webinarignition' ),
			esc_html__( "Disable", 'webinarignition' ) . " [hide]," . esc_html__( "Enable", 'webinarignition' ) . " [show]"
		);
		?>
        <div class="get_registration_notices_state" id="get_registration_notices_state_show" style="display: none;">
			<?php
			webinarignition_display_field(
				$input_get['id'],
				isset($webinar_data->registration_notice_email) ? $webinar_data->registration_notice_email : '',
				esc_html__( "Notification Email", 'webinarignition' ),
				"registration_notice_email",
				esc_html__( "Specify the email address to which the registration notifications should be sent", 'webinarignition' ),
				esc_html__( "name@example.com", 'webinarignition' )
			);
			?>
        </div>

    </div>

    <div class="bottomSaveArea">
        <a href="#" class="blue-btn-44 btn saveIt" style="color:#FFF;"><i class="icon-save"></i> <?php esc_html_e( "Save & Update", 'webinarignition' ) ?></a>
    </div>

</div>
