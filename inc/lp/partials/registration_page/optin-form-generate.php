<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $assets
 * @var $paid_check
 * @var $paid_check_js
 * @var $loginUrl
 * @var $user - Who is user
 */
?>
<div class="optinFormArea" <?php if ( $paid_check == "no" ) echo "style='display:none;'"; ?>>
    <?php
    if ( $webinar_data->webinar_date == "AUTO" ) {

    } else {
        if ( $webinar_data->lp_fb_button == "" || $webinar_data->lp_fb_button == "hide" ) {

        } else {
            ?>
            <a href="<?php echo !empty($user) ? webinarignition_fixPerma() . "confirmed" : $loginUrl; ?>" id="optinBTNFB" class="button wiButton wiButton-success wiButton-block wiButton-lg">
                <?php webinarignition_display( $webinar_data->lp_fb_copy, __( "Register With Facebook", "webinarignition") ); ?>
            </a>

            <div class="optOR"><?php webinarignition_display( $webinar_data->lp_fb_or, __( "OR", "webinarignition") ); ?></div>
            <?php
        }
    }

    /**
     * Pre-fill user data on opt-in form
     */
    $user_full_name = $user_first_name = $user_last_name = null;
    $user_email = ( isset($input_get['sremail']) && !empty($input_get['sremail']) ) ? trim(sanitize_text_field($input_get['sremail'])) : null;

    $order_id = WebinarignitionManager::is_paid_webinar($webinar_data) && WebinarignitionManager::get_paid_webinar_type($webinar_data) === 'woocommerce' && WebinarignitionManager::url_has_valid_wc_order_id();
    $disable_reg_fields = false;
    if( $order_id ) {
	    $disable_reg_fields = true;
	    $user = WebinarignitionManager::get_user_from_wc_order_id();
    } else {
	    $user = wp_get_current_user();
    }

    if ( ! empty( $user ) ) {
        $user_full_name  = $user->display_name;
        $user_first_name = $user->first_name;
        $user_last_name  = $user->last_name;
        if ( empty( $user_email ) ) {
            $user_email = $user->user_email;
        }
    }

    $WPreadOnlyMethod = 'wp_readonly';
    if( !function_exists($WPreadOnlyMethod) ) {
	    $WPreadOnlyMethod = 'readonly';
    }

    if ( ! empty( $webinar_data->ar_fields_order ) && is_array( $webinar_data->ar_fields_order ) ) {
        $alreadyAddedFields = [];
        $wi_showingGDPRHeading = false;

        foreach ( $webinar_data->ar_fields_order as $_field ) {
            if (in_array($_field, $alreadyAddedFields)) {
                continue;
            }
            $alreadyAddedFields[] = $_field;

            switch ( $_field ) {
                case 'ar_name':
                    
                    $required =   ( isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( 'ar_name', $webinar_data->ar_required_fields ) ) ? true : false;
                    
                    if (  ! in_array( 'ar_lname', $webinar_data->ar_fields_order ) ) {

                        ?>
                        <div class="wiFormGroup wiFormGroup-lg">
                            <input type="text" <?php $WPreadOnlyMethod($disable_reg_fields, true, true); ?> class="radius fieldRadius wiRegForm wiFormControl <?php echo $required ? ' required' : ''; ?>" id="optName"
                                   placeholder="<?php webinarignition_display( $webinar_data->lp_optin_name, __( "Enter Your Full Name...", 'webinarignition') );  echo $required ? '*' : ''; ?>" value="<?php echo $user_full_name; ?>" autocomplete="off">
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="wiFormGroup wiFormGroup-lg">
                            <input type="text" <?php $WPreadOnlyMethod($disable_reg_fields, true, true); ?> class="radius fieldRadius wiRegForm optNamer wiFormControl <?php echo $required ? 'required' : ''; ?>" id="optFName" placeholder="<?php webinarignition_display( $webinar_data->lp_optin_name,  __( "Enter Your First Name...", 'webinarignition') ); echo $required ? '*' : ''; ?>" value="<?php echo $user_first_name; ?>" autocomplete="off">
                        </div>
                        <?php
                    }
                    break;
                case 'ar_lname':
                    $required =   ( isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( 'ar_lname', $webinar_data->ar_required_fields ) ) ? true : false;
                    ?>
                    <div class="wiFormGroup wiFormGroup-lg">
                        <input type="text" <?php $WPreadOnlyMethod($disable_reg_fields, true, true); ?> class="radius fieldRadius wiRegForm optNamer wiFormControl <?php echo $required ? 'required' : ''; ?>" id="optLName"
                               placeholder="<?php webinarignition_display( $webinar_data->lp_optin_lname,  __( "Enter Your Last Name...", 'webinarignition') ); echo $required ? '*' : ''; ?>" value="<?php echo $user_last_name; ?>" autocomplete="off" >
                    </div>
                    <input type="hidden" id="optName" value="#firstlast#">
                    <?php
                    break;
                case 'ar_email':
                    ?>
                    <div class="wiFormGroup wiFormGroup-lg">
                        <input type="email" <?php $WPreadOnlyMethod($disable_reg_fields, true, true); ?> required class="radius fieldRadius wiRegForm wiFormControl" id="optEmail"
                               placeholder="<?php webinarignition_display(
                                   $webinar_data->lp_optin_email,
                                   __( "Enter Your Best Email...", 'webinarignition')
                               ); ?>*"
                               value="<?php echo $user_email; ?>" autocomplete="off">
                    </div>
                    <?php
                    break;
                case 'ar_phone':
                    ?>
                    <div class="wiFormGroup wiFormGroup-lg">
                        <input type="tel" class="radius fieldRadius wiRegForm wi_phone_number wiFormControl <?php echo ( isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( 'ar_phone', $webinar_data->ar_required_fields ) ) ? ' required' : ''; ?>" id="optPhone"
                               placeholder="<?php webinarignition_display( $webinar_data->lp_optin_phone, __( "Enter Your Phone Number...", 'webinarignition') ); echo ( isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( 'ar_phone', $webinar_data->ar_required_fields ) ) ? '*' : ''; ?>" >
                    </div>
                    <?php
                    break;

                case 'ar_custom_1':
                    ?>
                    <div class="wiFormGroup wiFormGroup-lg">
                        <input type="text" class="radius fieldRadius wiRegForm wi_optCustom_1 wiFormControl <?php echo ( isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( 'ar_custom_1', $webinar_data->ar_required_fields ) ) ? ' required' : ''; ?>" id="optCustom_1"
                               placeholder="<?php webinarignition_display( $webinar_data->lp_optin_custom_1, "" ); echo ( isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( 'ar_custom_1', $webinar_data->ar_required_fields ) ) ? '*' : ''; ?>" >
                    </div><?php
                    break;

                case 'ar_custom_2':
                    ?>
                    <div class="wiFormGroup wiFormGroup-lg">
                        <input type="text" class="radius fieldRadius wiRegForm wi_optCustom_2 wiFormControl <?php echo ( isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( 'ar_custom_2', $webinar_data->ar_required_fields ) ) ? 'required' : ''; ?>" id="optCustom_2"
                               placeholder="<?php webinarignition_display( $webinar_data->lp_optin_custom_2, "" ); echo ( isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( 'ar_custom_2', $webinar_data->ar_required_fields ) ) ? '*' : ''; ?>" >
                    </div><?php
                    break;

                case 'ar_custom_3':
                    ?>
                    <div class="wiFormGroup wiFormGroup-lg">
                        <input type="text" class="radius fieldRadius wiRegForm wi_optCustom_3 wiFormControl <?php echo ( isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( 'ar_custom_3', $webinar_data->ar_required_fields ) ) ? 'required' : ''; ?>" id="optCustom_3"
                               placeholder="<?php webinarignition_display( $webinar_data->lp_optin_custom_3, "" ); echo ( isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( 'ar_custom_3', $webinar_data->ar_required_fields ) )  ? '*' : ''; ?>" >
                    </div><?php
                    break;

                case 'ar_custom_4':
                    ?>
                    <div class="wiFormGroup wiFormGroup-lg">
                        <input type="text" class="radius fieldRadius wiRegForm wi_optCustom_4 wiFormControl <?php echo ( isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( 'ar_custom_4', $webinar_data->ar_required_fields ) )  ? 'required' : ''; ?>" id="optCustom_4"
                               placeholder="<?php webinarignition_display($webinar_data->lp_optin_custom_4,""); echo ( isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( 'ar_custom_4', $webinar_data->ar_required_fields ) ) ? '*' : ''; ?>" >
                    </div><?php
                    break;

                case 'ar_custom_5':
                case 'ar_custom_6':
	                $index = str_replace('ar_custom_', '', $_field);
	                $options_setting_str = 'lp_optin_custom_' . $index;
	                $is_required = isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( $_field, $webinar_data->ar_required_fields );
                    ?>
                    <div class="customFieldDiv wiFormCheckbox wiFormCheckbox-lg">
                        <label for="optCustom_<?php echo $index; ?>"><?php echo $webinar_data->{$options_setting_str}; ?><?php echo $is_required ? ' *' : ''; ?></label>
                        <input type="checkbox" id="optCustom_<?php echo $index; ?>" class="wiRegForm wi_optCustom_<?php echo $index; ?><?php echo $is_required ? ' required' : ''; ?>" <?php echo $is_required ? ' required' : ''; ?>>
                    </div>
                    <?php
                    break;

                case 'ar_custom_7':
                    ?>
                    <div class="customFieldDiv wiFormGroup wiFormGroup-lg">
                        <label for="optCustom_7"><?= $webinar_data->lp_optin_custom_7 ?></label>
                        <textarea id="optCustom_7" class="wiRegForm wi_optCustom_7 wiFormControl <?php echo ( isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( 'ar_custom_7', $webinar_data->ar_required_fields ) ) ? 'required' : ''; ?>" placeholder="<?= $webinar_data->lp_optin_custom_7 ?><?php echo ( isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( 'ar_custom_7', $webinar_data->ar_required_fields ) ) ? ' (Required)' : ''; ?>" rows="4" cols="50"></textarea>
                    </div>
                    <?php
                    break;

                case 'ar_custom_15':
                case 'ar_custom_16':
                case 'ar_custom_17':
                case 'ar_custom_18':
                    $index = str_replace('ar_custom_', '', $_field);
                    $label_setting = 'lp_optin_custom_' . $index;
                    $options_setting_str = 'lp_optin_custom_select_' . $index;
                    $options_setting = $webinar_data->{$options_setting_str};
                    $is_required = isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( $_field, $webinar_data->ar_required_fields );

                    if (!empty(trim($options_setting))) {
                        $options_array = explode("\n", $options_setting);
                        ?>
                        <div class="customFieldDiv customFieldSelectDiv wiFormGroup wiFormGroup-lg">
                            <label for="optCustom_<?php echo $index; ?>">
                                <span><?php echo $webinar_data->{$label_setting}; ?></span>
                                <?php echo $is_required ? ' <strong>(' . __('Required', 'webinarignition') . ')</strong>' : ''; ?>
                            </label>

                            <select
                                    id="optCustom_<?php echo $index; ?>"
                                    class="wiRegForm wi_optCustom_<?php echo $index; ?> wiFormControl<?php echo $is_required ? ' required' : ''; ?>"
                            >
                                <?php
                                foreach ($options_array as $option) {
                                    $option_array = explode("::", $option);

                                    if (1 === count($option_array)) {
                                        $value = trim($option_array[0]);
                                        $label = trim($option_array[0]);
                                    } else {
                                        $value = trim($option_array[0]);
                                        $label = trim($option_array[1]);
                                    }
                                    ?>
                                    <option value="<?php echo $value ?>"><?php echo $label ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <?php
                    }
                    break;

                case 'ar_privacy_policy':
	                $is_required = isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( $_field, $webinar_data->ar_required_fields );
                    webinarignition_showGDPRHeading($webinar_data);
                    ?>
                    <div class="gdprConsentField gdpr-pp wiFormCheckbox wiFormCheckbox-sm">
                        <label for="gdpr-pp"><?= !empty($webinar_data->lp_optin_privacy_policy) ? $webinar_data->lp_optin_privacy_policy : __( "Have read and understood our Privacy Policy", "webinarignition") ?><?php echo $is_required ? ' *' : ''; ?></label>
                        <input type="checkbox" name="optGDPR_PP" id="gdpr-pp" <?php echo $is_required ? ' required' : ''; ?> class="<?php echo $is_required ? 'required' : ''; ?>" style="margin:4px 0 -5px;">
                    </div>
                    <?php
                    break;
                case 'ar_terms_and_conditions':
	                $is_required = isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( $_field, $webinar_data->ar_required_fields );
                    webinarignition_showGDPRHeading($webinar_data);
                    ?>
                    <div class="gdprConsentField gdpr-tc wiFormCheckbox wiFormCheckbox-sm">
                    <label for="gdpr-tc"><?= !empty($webinar_data->lp_optin_terms_and_conditions) ? $webinar_data->lp_optin_terms_and_conditions : __( "Accept our Terms &amp; Conditions", "webinarignition") ?><?php echo $is_required ? ' *' : ''; ?></label>
                    <input type="checkbox" name="optGDPR_TC" id="gdpr-tc" <?php echo $is_required ? ' required' : ''; ?> class="<?php echo $is_required ? 'required' : ''; ?>" style="margin:4px 0 -5px;">
                    </div><?php
                    break;
                case 'ar_mailing_list':
	                $is_required = isset( $webinar_data->ar_required_fields ) && is_array( $webinar_data->ar_required_fields ) && in_array( $_field, $webinar_data->ar_required_fields );
                    webinarignition_showGDPRHeading($webinar_data);
                    ?>
                    <div class="gdprConsentField gdpr-ml wiFormCheckbox wiFormCheckbox-sm">
                    <label for="gdpr-ml"><?= !empty($webinar_data->lp_optin_mailing_list) ? $webinar_data->lp_optin_mailing_list : __( "Want to be added to our mailing list", "webinarignition") ?><?php echo $is_required ? ' *' : ''; ?></label>
                    <input type="checkbox" name="optGDPR_ML" id="gdpr-ml" <?php echo $is_required ? ' required' : ''; ?> class="<?php echo $is_required ? 'required' : ''; ?>" style="margin:4px 0 -5px;">
                    </div><?php
                    break;
                default:
                    break;
            }
        }

        webinarignition_closeGDPRSection();
    }

    if ( $webinar_data->lp_optin_button == "" || $webinar_data->lp_optin_button == "color" ) {
        ?>
        <button href="#" id="optinBTN" class="large button wiButton wiButton-block wiButton-lg addedArrow">
            <span id="optinBTNText">
                <?php webinarignition_display( $webinar_data->lp_optin_btn, __( "Register For The Webinar", "webinarignition") ); ?>
            </span>

            <span id="optinBTNLoading" style="display: none;" >
                <img src="<?php echo WEBINARIGNITION_URL . 'inc/lp/images/loading_dots_cropped_small.gif' ?>" style="width: auto; height: 20px;"/>
            </span>
        </button>
        <?php
    } else {
        ?>
        <a href="#" id="optinBTN" class="optinBTN optinBTNimg">
            <img src="<?php webinarignition_display( $webinar_data->lp_optin_btn_image, "" ); ?>" width="327" border="0"/>
        </a>
        <?php
    }
    ?>
    <div class="spam wiSpamMessage">
        <?php webinarignition_display( $webinar_data->lp_optin_spam, __( "* we will not spam, rent, sell, or lease your information *", "webinarignition")  ); ?>
    </div>
     <?php  if( get_option( 'webinarignition_show_footer_branding' ) ) { ?>
        <div class="powered_by_text_wrap" style="margin-top: 15px;"><a href="<?php echo get_option( 'webinarignition_affiliate_link' ); ?>"  target="_blank"><b><?php echo get_option( 'webinarignition_branding_copy' ); ?></b></a> </div>
    <?php }  ?>
</div>
