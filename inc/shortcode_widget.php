<?php defined( 'ABSPATH' ) || exit;



function webinarignition_widget($atts) {

	ob_start();

	// Get ID
	/**
	 * @var $id
	 */
	extract(shortcode_atts(array(
		'id' => '1'
	), $atts));

	// Get Content From Options
	$webinar_data       = WebinarignitionManager::get_webinar_data($id);

	if( !empty($webinar_data) ){

		$date_format        = !empty($webinar_data->date_format ) ? $webinar_data->date_format  : get_option( "date_format" );
		if( !empty($webinar_data->time_format ) && ( $webinar_data->time_format == '12hour' || $webinar_data->time_format == '24hour'  ) ){ //old formats
			$webinar_data->time_format = get_option( "time_format", 'H:i' );
		}
		$time_format        = $webinar_data->time_format;
		$translated_date    = webinarignition_get_translated_date( $webinar_data->webinar_date, 'm-d-Y', $date_format );
		$timeonly           = ( empty($webinar_data->display_tz ) || ( !empty($webinar_data->display_tz) && ($webinar_data->display_tz == 'yes') ) ) ? false : true;
		$autoTime           = webinarignition_get_time_tz( $webinar_data->webinar_start_time, $time_format, $webinar_data->webinar_timezone, false, $timeonly );
		$TZID               = webinarignition_convert_utc_to_tzid($webinar_data->webinar_timezone);
		$dateTime           = new DateTime();
		$dateTime->setTimeZone(new DateTimeZone($TZID));
		$TZID               = $dateTime->format('T');

		?>

        <style>
            .wi_webinar_widget {
                width: 100%;
                background-color: #fff;
                border-radius: 5px;
                -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
                -moz-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
                margin: 15px 10px;

                font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
                font-weight: 300;
            }

            .webinar_title {
                text-align: center;
                font-size: 24px;
                line-height: 36px;
                padding: 20px;
                border-bottom: 1px solid #DDD;
                color: #222222;
            }

            .wi_webinar_date {
                background-color: #C95456;
                color: #FFF;
                /*font-weight: bold;*/
                text-align: center;
                padding: 10px 20px;
                text-transform: uppercase;
                border-bottom: 2px solid rgba(0, 0, 0, 0.2);
                border-top: 2px solid rgba(0, 0, 0, 0.2);
            }

            .wi_webinar_sign_up {
                text-align: center;
                background-color: #F7F7F7;
                padding: 20px;
                border-bottom: 2px solid rgba(0, 0, 0, 0.2);
                color: #222222;
            }

            .wi_webinar_headline1 {
                display: block;
                font-size: 24px;
                font-weight: bold;
            }

            .wi_webinar_headline2 {
                display: block;
                margin-top: 5px;
                font-size: 14px;
            }

            .wi_signup_btn {
                border: 1px solid rgba(0, 0, 0, 0.1);
                width: 100% !important;
                background-color: #55B369 !important;
                display: block !important;
                margin-top: 10px !important;
                font-size: 18px !important;
                font-weight: bold !important;
                padding: 10px !important;
                border-radius: 5px !important;
                border-bottom: 2px solid rgba(0, 0, 0, 0.2) !important;
                text-decoration: none !important;
                color: #FFF !important;
                height: 46px !important;
                line-height: 23px !important;
            }

            .wi_signup_btn:hover {
                text-decoration: none !important;
                color: #FFF !important;
                background-color: #4ba05e !important;
            }

            .wi_webinar_input {
                display: block;
                margin-top: 10px;
                width: 100%;
                border-radius: 5px;
                height: 46px;
                line-height: 46px;
                padding-left: 10px;
                padding-right: 10px;
                border: 1px solid rgba(0, 0, 0, 0.1);
                border-bottom: 2px solid rgba(0, 0, 0, 0.2);
                box-sizing: border-box;
                -moz-box-sizing: border-box;
            }

            .wi_webinar_spam {
                border-top: 1px solid #DDD;
                padding-top: 15px;
                margin-top: 15px;
                text-transform: uppercase;
                font-size: 10px;
                color: #757575;
            }
        </style>

        <div class="wi_webinar_widget">
            <!-- webinar title -->
            <div class="webinar_title">
				<?php echo $webinar_data->webinar_desc; ?>
            </div>

            <div class="wi_webinar_date">
				<?php echo $translated_date; ?>

                <span>
				<?php
				if ($webinar_data->lp_webinar_subheadline) {
					echo $webinar_data->lp_webinar_subheadline;
				} else {
					echo '@' . $autoTime . ' ', $TZID;
				}
				?>
	    </span>
            </div>

            <div class="wi_webinar_sign_up">
				<?php
				webinarignition_display(
					$webinar_data->lp_optin_headline, '<span class="wi_webinar_headline1">'.__("RESERVE YOUR SPOT!", "webinarignition").'</span><span class="optinHeadline2">'.__("WEBINAR REGISTRATION", "webinarignition").'</span>'
				);
				?>

                <div class="wi_optin_form">

                    <form name="input" action="<?php echo WEBINARIGNITION_URL . 'inc/lp/posted.php'; ?>" method="POST">
                        <input type="hidden" name="campaignID" value="<?php echo $id; ?>"/>
                        <input type="text"  required class="wi_webinar_input" id="name" name="name" placeholder="<?php _e( 'Enter your name... ', 'webinarignition' ); ?>">
                        <input type="email" required class="wi_webinar_input" id="email" name="email" placeholder="<?php _e( 'Enter your email address... ', 'webinarignition' ); ?>">
                        <input type="hidden" name="security" value="<?php echo wp_create_nonce("webinarignition_ajax_nonce"); ?>">

						<?php if ( ! empty( $webinar_data->ar_fields_order ) && is_array( $webinar_data->ar_fields_order ) ) {
							$alreadyAddedFields = [];
							$wi_showingGDPRHeading = false;

							foreach ( $webinar_data->ar_fields_order as $_field ) {
								if (in_array($_field, $alreadyAddedFields)) {
									continue;
								}
								$alreadyAddedFields[] = $_field;

								switch ( $_field ) {

									case 'ar_privacy_policy':
										webinarignition_showGDPRHeading($webinar_data);
										?>
                                        <div class="gdprConsentField gdpr-pp">
                                            <label for="gdpr-pp"><?= !empty($webinar_data->lp_optin_privacy_policy) ? $webinar_data->lp_optin_privacy_policy : __( 'Have read and understood our Privacy Policy', "webinarignition") ?></label>
                                            <input required type="checkbox" name="optGDPR_PP" id="gdpr-pp">
                                        </div>
										<?php
										break;
									default:
										break;
								}
							}

							webinarignition_closeGDPRSection();
						} ?>



                        <input type="submit" value="<?php webinarignition_display($webinar_data->lp_optin_btn, __( "Register For The Webinar", "webinarignition")  ); ?>"
                               class="wi_signup_btn"/>
                    </form>

                </div>

                <div class="wi_webinar_spam">
					<?php webinarignition_display($webinar_data->lp_optin_spam, __( "* we will not spam, rent, sell, or lease your information *", "webinarignition") ); ?>
                </div>
				<?php  if( get_option( 'webinarignition_show_footer_branding' ) ) { ?>
                    <div class="powered_by_text_wrap" style="margin-top: 15px;"><a href="<?php echo get_option( 'webinarignition_affiliate_link' ); ?>"  target="_blank"><b><?php echo get_option( 'webinarignition_branding_copy' ); ?></b></a> </div>
				<?php }  ?>
            </div>


        </div>

		<?php

	}

	return ob_get_clean();

	?>


	<?php
}

// Adding Widget
add_shortcode('wi_webinar', 'webinarignition_widget');

// make shortcode work in text widget
add_filter('widget_text', 'do_shortcode');

?>
