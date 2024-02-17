<?php defined( 'ABSPATH' ) || exit;



// DISPLAY A COLOR PICKER

function webinarignition_display_color($num, $data, $title, $id, $help, $placeholder) {

    // Output HTML

    ?>

    <div class="editSection">

        <div class="inputTitle">
            <div class="inputTitleCopy"><?php echo $title; ?></div>
            <div class="inputTitleHelp"><?php echo $help; ?></div>
        </div>

        <div class="inputSection ">
            <input class="inputField elem  cp-picker color-field-picker" placeholder="<?php echo $placeholder; ?>"
                   type="text" name="<?php echo $id; ?>" id="<?php echo $id; ?>"
                   value="<?php echo htmlspecialchars(stripcslashes($data)); ?>">
        </div>
        <br clear="left">

    </div>

<?php
}

	/**
	 * Displays pickadate.js date picker 
	 *
	 * @param str $format               The format that the passed in string is in.
	 * @param str $webinar_date_format  The format that user has chosen in webinar settings
	 */

        function webinarignition_display_date_picker($num, $data, $format, $title, $id, $help, $placeholder, $webinar_date_format = null) {

            $webinar_date_format        = !empty( $webinar_date_format) ? $webinar_date_format  : get_option( "date_format", 'l, F j, Y' );

            $webinarDateObject          = DateTime::createFromFormat( $format, htmlspecialchars(stripcslashes($data)) );
            if( is_object($webinarDateObject) ){
                $webinarTimestamp       = $webinarDateObject->getTimestamp();   
                $date                   = date_i18n( $webinar_date_format, $webinarTimestamp);    
            }

            ?>

            <div class="editSection">

                <div class="inputTitle">
                    <div class="inputTitleCopy"><?php echo $title; ?></div>
                    <div class="inputTitleHelp"><?php echo $help; ?></div>
                </div>

                <div class="inputSection ">
                    <input class="inputField elem dp-date date-field-picker" placeholder="<?php echo $placeholder; ?>"
                           type="text" name="<?php echo $id; ?>" id="<?php echo $id; ?>"
                        value="<?php echo !empty($date) ? $date : ''; ?>">
                </div>
                <br clear="left">

            </div>

        <?php
        }

// DISPLAY A TIME PICKER - 24hr
function webinarignition_display_time_picker($num, $data, $title, $id, $help, $placeholder = '') {

    // Output HTML

    ?>

    <div class="editSection">

        <div class="inputTitle">
            <div class="inputTitleCopy"><?php echo $title; ?></div>
            <div class="inputTitleHelp"><?php echo $help; ?></div>
        </div>

        <div class="inputSection ">

                <?php if (empty($data)) $data = '18:00'; ?>
            
            <input class="timepicker inputField inputFieldDash elem"" placeholder="<?php echo $placeholder; ?>"
                   type="text" name="<?php echo $id; ?>" id="<?php echo $id; ?>"
                   value="<?php echo webinarignition_get_localized_time($data); ?>">         

        </div>
        <br clear="left">

    </div>

<?php
}

// DISPLAY EDIT TOGLE

function webinarignition_display_edit_toggle($icon, $title, $ID, $exta) {
    ?>
    <div class="editableSectionHeading" editSection="<?php echo $ID; ?>">

        <div class="editableSectionIcon">
            <i class="icon-<?php echo $icon; ?> icon-2x"></i>
        </div>

        <div class="editableSectionTitle">
            <?php echo $title; ?>
            <span class="editableSectionTitleSmall"><?php if ($exta == "") {
                    echo __( "Not Setup yet...", 'webinarignition');
                } else {
                    echo $exta;
                } ?></span>
        </div>

        <div class="editableSectionToggle">
            <i class="toggleIcon  icon-chevron-down icon-2x"></i>
        </div>

        <br clear="all"/>

    </div>
    <div class="editableSectionSep"></div>
<?php
}

// Display Info Block
function webinarignition_display_info($title, $info) {
    ?>
    <div class="editSection infoSection">
        <h4><i class="icon-question-sign"></i> <?php echo $title; ?></h4>

        <p><?php echo $info; ?></p>
    </div>
<?php
}

// Display Info Block
function webinarignition_display_two_col_info($title, $info = "", $content = "") {
    ?>
    <div class="editSection">
        <div class="inputTitle">
            <div class="inputTitleCopy">
                <?php echo $title; ?>
            </div>

            <?php
            if (!empty($info)) {
                ?>
                <div class="inputTitleHelp">
                    <?php echo $info; ?>
                </div>
                <?php
            }
            ?>
        </div>

        <div class="inputSection ">
            <?php echo wpautop($content); ?>
        </div>

        <br clear="left">
    </div>
<?php
}


// Display TIMEZONES
function webinarignition_display_timezone_identifiers($num, $data, $title, $id, $help, $placeholder) {

    ?>

    <div class="editSection">

        <div class="inputTitle">
            <div class="inputTitleCopy"><?php echo $title; ?></div>
            <div class="inputTitleHelp"><?php echo $help; ?></div>
        </div>

        <div class="inputSection">
            <?php
            $timezone_list = timezone_identifiers_list();
            ?>
            <select name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="inputField elem">
                <?php foreach ($timezone_list as $_timezone) { ?>
                    <option value="<?php echo $_timezone; ?>" <?php if ($data == $_timezone) {
                        echo "selected";
                    } ?> ><?php echo $_timezone; ?></option>
                <?php } ?>
            </select>


        </div>
        <br clear="left">

    </div>

<?php

}

function get_select_start_time_options($id, $starttimeTZ, $template) {
    $time_options = [
        "00:00" => "00:00",
        "00:30" => "00:30",
        "01:00" => "01:00",
        "01:30" => "01:30",
        "02:00" => "02:00",
        "02:30" => "02:30",
        "03:00" => "03:00",
        "03:30" => "03:30",
        "04:00" => "04:00",
        "04:30" => "04:30",
        "05:00" => "5:00",
        "05:30" => "5:30",
        "06:00" => "6:00",
        "06:30" => "6:30",
        "07:00" => "7:00",
        "07:30" => "7:30",
        "08:00" => "8:00",
        "08:30" => "8:30",
        "09:00" => "9:00",
        "09:30" => "9:30",
        "10:00" => "10:00",
        "10:30" => "10:30",
        "11:00" => "11:00",
        "11:30" => "11:30",
        "12:00" => "12:00",
        "12:30" => "12:30",
        "13:00" => "13:00",
        "13:30" => "13:30",
        "14:00" => "14:00",
        "14:30" => "14:30",
        "15:00" => "15:00",
        "15:30" => "15:30",
        "16:00" => "16:00",
        "16:30" => "16:30",
        "17:00" => "17:00",
        "17:30" => "17:30",
        "18:00" => "18:00",
        "18:30" => "18:30",
        "19:00" => "19:00",
        "19:30" => "19:30",
        "20:00" => "20:00",
        "20:30" => "20:30",
        "21:00" => "21:00",
        "21:30" => "21:30",
        "22:00" => "22:00",
        "22:30" => "22:30",
        "23:00" => "23:00",
        "23:30" => "23:30"
    ];

    ob_start();
    $id_array = explode('__', $id);

    if (1 < count($id_array) && 'multiple' === $id_array[0]) {
        $name = $id_array[0] . '__' . $id_array[1] . '[]';
    } else {
        $name = $id;
    }
    ?>
    <select name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="inputField elem select_auto_time"<?php echo $template ? ' disabled' : '' ?>>
        <?php
        foreach ($time_options as $val => $label) {
            ?>
            <option value="<?php echo $val; ?>" <?php if ($starttimeTZ == $val) {
                echo "selected";
            } ?>><?php echo $label; ?>
            </option>
            <?php
        }
        ?>
    </select>
    <?php
    $html = ob_get_clean();

    return $html;
}

function get_select_weekdays_options($id, $weekdays, $template) {
    $weekdays_available = [
        'mon' => __( "Monday", 'webinarignition' ),
        'tue' => __( "Tuesday", 'webinarignition' ), 
        'wed' => __( "Wednesday", 'webinarignition' ),
        'thu' => __( "Thursday", 'webinarignition' ),
        'fri' => __( "Friday", 'webinarignition' ),
        'sat' => __( "Saturday", 'webinarignition' ),
        'sun' => __( "Sunday", 'webinarignition' ),
    ];

    if (false === $weekdays) {
        $weekdays = ['mon','tue','wed','thu','fri','sat','sun'];
    }
    ob_start();
    $id_array = explode('__', $id);
    
    if (1 < count($id_array) && 'multiple' === $id_array[0] && isset($id_array[2])) {
        $index = $id_array[2] - 1;
        $name = $id_array[0] . '__' . $id_array[1] . '['.$index.'][]';
    } else {
        $name = $id . '[]';
    }
    ?>
    <div class="auto_weekdays" style="margin-top: 15px;display:block;">
        <?php
        ?>
        <select
            multiple name="<?php echo $name; ?>"
            id="<?php echo $id; ?>"
            class="inputField elem select_auto_weekday"<?php echo $template ? ' disabled' : '' ?>
            style="height: 155px !important;padding: 10px;"
        >
            <?php
            foreach ($weekdays_available as $val => $label) {
                ?>
                <option value="<?php echo $val; ?>" <?php if (in_array($val, $weekdays)) {
                    echo "selected";
                } ?>><?php echo $label; ?>
                </option>
                <?php
            }
            ?>
        </select>
    </div>
    <?php
    $html = ob_get_clean();
    
    return $html;
}

// DISPLAY A TIME PICKER - 24hr
function webinarignition_display_time_auto($num, $data, $weekdays, $title, $id, $weekdays_id, $help, $template = false) {
    // Output HTML
    if (empty($data)) $data = "16:00";
    $webinar_data = WebinarignitionManager::get_webinar_data($num);

    $is_multiple_auto_time_enabled = WebinarignitionPowerups::is_multiple_auto_time_enabled($webinar_data);
    ?>
    <div class="editSection">
        <div class="inputTitle">
            <div class="inputTitleCopy"><?php echo $title; ?></div>
            <div class="inputTitleHelp"><?php echo $help; ?></div>
        </div>

        <div class="inputSection ">
            <?php $starttimeTZ = $data; ?>
            <?php echo get_select_start_time_options($id, $starttimeTZ, $template); ?>
            <div<?php echo $is_multiple_auto_time_enabled ? '' : ' style="display:none;"' ?>>
                <?php echo get_select_weekdays_options($weekdays_id, $weekdays, $template); ?>
            </div>
        </div>
        <br clear="left">
    </div>
<?php
}

function webinarignition_display_global_shortcodes($webinar_data, $num, $title, $help) {
    $available_shortcodes = WebinarignitionPowerupsShortcodes::get_available_shortcodes();
    $global_shortcodes = [];

    foreach ($available_shortcodes as $shortcode => $settings) {
        if (!empty($settings['page']) && $settings['page'] == 'global') {
            $global_shortcodes[$shortcode] = $settings;
        }
    }
    ?>
    <div class="editSection">
        <div class="inputTitle">
            <div class="inputTitleCopy"><?php echo $title; ?></div>
            <div class="inputTitleHelp"><?php echo $help; ?></div>
        </div>

        <div class="inputSection shortcodesList">
            <?php
            foreach ($global_shortcodes as $shortcode => $data) {
                WebinarignitionPowerupsShortcodes::show_shortcode_description($shortcode, $webinar_data);
            }
            ?>
        </div>
        <br clear="left">
    </div>
    <?php
}


// Display TIMEZONES
function webinarignition_display_template_dropdown_options($webinar_data, $num, $data, $title, $id, $help, $options, $params, $shortcodes, $placeholder) {
    $is_webinar_public = WebinarignitionManager::is_webinar_public($webinar_data);
    $public_params = str_replace('{{webinar_id}}', $num, $params);
    $protected_params = str_replace('{{webinar_id}}', $webinar_data->hash_id, $params);

    $name     = $id;
    $multiple = '';
    $class    = '';
	$data = (array) $data;
	$data = array_unique( array_filter($data) );
    if ('custom_registration_page' === $id) {
        $icon = 'icon-calendar';
        $view_label = __( "Preview Registration Page", 'webinarignition' );
        $name      .= '[]';
        $multiple   = 'multiple';
        $class      = 'multiSelectField';
    } elseif ('custom_thankyou_page' === $id) {
        $icon = 'icon-copy';
        $view_label = __( "Preview Thank You Page", 'webinarignition' );
    } elseif ('custom_webinar_page' === $id) {
        $icon = 'icon-microphone';
        $view_label = __( "Preview Webinar Page", 'webinarignition' );
    } elseif ('custom_countdown_page' === $id) {
        $icon = 'icon-time';
        $view_label = __( "Preview Countdown Page", 'webinarignition' );
    } elseif ('custom_replay_page' === $id) {
        $icon = 'icon-film';
        $view_label = __( "Preview Replay Page", 'webinarignition' );
    } elseif ('custom_closed_page' === $id) {
        $icon = 'icon-remove';
        $view_label = __( "Preview Closed Page", 'webinarignition' );
    }
    ?>
    <div class="editSection">
        <div class="inputTitle">
            <div class="inputTitleCopy"><?php echo $title; ?></div>
            <div class="inputTitleHelp"><?php echo $help; ?></div>
        </div>
        <?php
        $default_webinar_page_id = WebinarignitionManager::get_webinar_post_id($webinar_data->id);
        $default_paid_thank_you_url = '';
        if($default_webinar_page_id) {
	        $default_paid_thank_you_url = get_the_permalink( $default_webinar_page_id );
        }
        ?>
        <span id="default_paid_thank_you_url" data-url="<?php echo add_query_arg($webinar_data->paid_code, '', $default_paid_thank_you_url); ?>" style="display:none"></span>
        <div class="inputSection">
            <?php if( 'custom_registration_page' == $id ): ?>
                <?php

                $selected_page_links = array();

                $default_registration_page = empty( $webinar_data->default_registration_page ) ? $default_webinar_page_id : intval( $webinar_data->default_registration_page );

                $selected = '';
                $i_class  = '';

                if( !empty( $default_webinar_page_id ) && ( empty( $webinar_data->default_registration_page ) || $webinar_data->default_registration_page == $default_webinar_page_id ) && !in_array( $default_webinar_page_id, $data ) ) {

                    if( $default_registration_page == $default_webinar_page_id ) {
                        $selected = 'checked';
                        $i_class  = 'icon-circle';
                    }

                    $selected_page_links[] = sprintf('<div class="wi_webinar_preview_box wi_webinar_preview_box_%d %s"><input data-page_url="%s" name="default_registration_page" class="default_registration_page" value="%d" type="radio" %s><i class="icon %s"></i>%s<a href="%s" target="_blank" class="wi_page_link"><i class="icon-external-link"></i> %s</a></div>', $default_webinar_page_id, $selected, get_permalink($default_webinar_page_id), $default_webinar_page_id, $selected, $i_class, get_the_title($default_webinar_page_id), get_permalink($default_webinar_page_id), esc_html__('Preview', 'webinarignition') );
                }

                if( $default_registration_page !== $default_webinar_page_id && !in_array( $default_registration_page, $data ) ) {
                    $default_registration_page = reset( $data );
                }

                foreach( $data as $page_id ) {

                    if( $default_registration_page == $page_id ) {
                        $selected = 'checked';
                        $i_class  = 'icon-circle';
                    } else {
                        $selected = '';
                        $i_class  = 'icon-circle-blank';
                    }

                    if( empty( $page_id ) ) {
                        continue;
                    }

                    $selected_page_links[] = sprintf('<div class="wi_webinar_preview_box wi_webinar_preview_box_%d %s"><input data-page_url="%s" name="default_registration_page" class="default_registration_page" value="%d" type="radio" %s><i class="icon %s"></i>%s<a href="%s" target="_blank" class="wi_page_link"><i class="icon-external-link"></i> %s</a></div>', $page_id, $selected, get_permalink($page_id), $page_id, $selected, $i_class, get_the_title($page_id), get_permalink($page_id), esc_html__('Preview', 'webinarignition') );
                }

                if( !in_array($default_registration_page, $data ) ) {
                    $data[] = $default_registration_page;
                }

                if( !empty( $selected_page_links ) ): ?>
                    <div class="wi_selected_pages_links_container">
                        <div class="wi_selected_pages_links">
                            <?php echo implode('', $selected_page_links ); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <select
                    name="<?php echo $name; ?>"
                    id="<?php echo $id; ?>"
                    class="inputField inputFieldTemplateSelect elem <?php echo $class; ?>" style="width: 100%;max-width: 100%;margin-bottom: 15px;"
                    data-webinar-access="<?php echo $is_webinar_public ? 'public' : 'protected' ?>"
                    <?php echo $multiple; ?>
            >
                <?php
                $selected_url = '';
                $selected_url_params = '';
                if (!empty($placeholder)) {
                    ?>
                    <option value="" data-url="<?php echo __( "select page to see preview URL", 'webinarignition' ); ?>"><?php echo $placeholder ?></option>
                    <?php
                }



                foreach ($options as $val => $item) {
                    if (!empty(trim($val)) && !empty(trim($item['label']))) {

                        $is_selected = in_array( $val, $data );

                        if (false !== strpos($item['url'], '?')) {
                            $url_params = '&' . $public_params;
                        } else {
                            $url_params = '?' . $public_params;
                        }

                        if (false !== strpos($item['url'], '?')) {
                            $protected_url_params = '&' . $protected_params;
                        } else {
                            $protected_url_params = '?' . $protected_params;
                        }

	                    $paid_thank_you_url = add_query_arg($webinar_data->paid_code, '', get_the_permalink($val));

                        if ($is_selected) {
                            $selected_url = $item['url'];
                            $selected_url_params = $url_params;
                            $selected_protected_url_params = $protected_url_params;
                        }

                        $public_url = $item['url'] . $url_params;
                        $protected_url = $item['url'] . $protected_url_params;

                        //Add preview parameter
                        $public_url = add_query_arg('preview', 'true', $public_url);
	                    $protected_url = add_query_arg('preview', 'true', $protected_url);
                        ?>
                        <option
                                data-url="<?php echo $item['url'] . $url_params; ?>"
                                data-public-url="<?php echo $public_url; ?>"
                                data-protected-url="<?php echo $protected_url; ?>"
                                data-paid-thank-you-url="<?php echo $paid_thank_you_url; ?>"
                                value="<?php echo $val; ?>"
                            <?php if ($is_selected) {echo "selected";} ?>
                        >
	                        <?php echo $val; ?> - <?php echo $item['label']; ?>
                        </option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <br clear="left">

        <?php
        if (!empty($selected_url)) {
            if ($is_webinar_public) {
                $selected_url = $selected_url . $selected_url_params;
            } else {
                $selected_url = $selected_url . $selected_protected_url_params;
            }

	        //Add preview parameter
	        $selected_url = add_query_arg('preview', 'true', $selected_url);
        } else {
            $selected_url = '';
        }
        ?>

        <input class="webinarPreviewLinkInput" type="hidden" value="<?php echo $selected_url; ?>" data-page="<?php echo $id; ?>">

        <div class="webinarPreviewItem" style="margin-bottom: 10px;">
            <div class="webinarPreviewIcon"><i class="<?php echo $icon; ?> icon-2x"></i></div>
            <div class="webinarPreviewTitle">
                <?php
                if (!empty($selected_url)) {
                    ?>
                    <a
                            class="webinarPreviewLinkHolder <?php echo $id; ?>-webinarPreviewLinkHolder"
                            href="<?php echo $selected_url; ?>"
                            target="_blank"
                    >
                        <i class="icon-external-link"></i>
                        <?php echo $view_label; ?>
                    </a>
                    <a class="webinarPreviewLinkEmptyHolder <?php echo $id; ?>-webinarPreviewLinkEmptyHolder" style="display: none;">
                        <?php echo __( "select page to see preview URL", 'webinarignition' ); ?>
                    </a>
                    <?php
                } else {
                    ?>
                    <a
                            class="webinarPreviewLinkHolder <?php echo $id; ?>-webinarPreviewLinkHolder"
                            href="<?php echo $selected_url; ?>"
                            target="_blank" style="display: none;"
                    >
                        <i class="icon-external-link"></i>
                        <?php echo $view_label; ?>
                    </a>
                    <a class="webinarPreviewLinkEmptyHolder <?php echo $id; ?>-webinarPreviewLinkEmptyHolder">
                        <?php echo __( "select page to see preview URL", 'webinarignition' ); ?>
                    </a>
                    <?php
                }
                ?>
            </div>
            <br clear="both">
        </div>

        <?php
        if (!empty($shortcodes)) {
            ?>

            <div class="inputTitle">
                <div class="inputTitleCopy"><?php echo __( "Available shortcodes", 'webinarignition' ); ?></div>
            </div>

            <div class="inputSection shortcodesList">
                <?php
                foreach ($shortcodes as $shortcode => $shortcode_data) {
                    WebinarignitionPowerupsShortcodes::show_shortcode_description($shortcode, $webinar_data);
                }
                ?>
            </div>
            <br clear="left">
            <?php
        }
        ?>

    </div>

    <?php

}

function display_webinar_tabs_section($webinar_data) {

    $webinarId = $webinar_data->id;
    $default_webinar_tabs_settings = [];
    $webinar_tabs_settings = isset($webinar_data->webinar_tabs) ? $webinar_data->webinar_tabs : $default_webinar_tabs_settings;

    ?>
    <div id="webinar_tabs_container" class="webinar_tabs_container">
        <?php
        if (!empty($webinar_tabs_settings)) {
            foreach ($webinar_tabs_settings as $i => $webinar_tabs_setting) {
                $tab_name = !empty($webinar_tabs_setting['name']) ? $webinar_tabs_setting['name'] : '';
                $tab_content = !empty($webinar_tabs_setting['content']) ? $webinar_tabs_setting['content'] : '';
                $tab_type = !empty($webinar_tabs_setting['type']) ? $webinar_tabs_setting['type'] : 'editor_tab';
                ?>
                <div class="additional_auto_action_item auto_action_item webinar_tab_item">
                    <div class="auto_action_header">
                        <h4>
                            <?php _e( "Webinar Tab", 'webinarignition' ) ?>
                            <span class="index_holder"><?php echo $i + 1; ?></span>
                            <span class="auto_action_desc_holder"> </span>
                            <i class="icon-arrow-up"></i>
                            <i class="icon-arrow-down"></i>
                        </h4>
                    </div>

                    <div class="auto_action_body">
                        <div class="editSection">
                            <div class="inputTitle">
                                <div class="inputTitleCopy"><?php _e( "Tab title", 'webinarignition' ) ?></div>
                                <div class="inputTitleHelp">
                                    <?php _e( "Try to use short title to keep tabs template compact", 'webinarignition' ) ?>
                                </div>
                            </div>

                            <div class="inputSection">
                                <input
                                    class="inputField elem webinar_tabs_name"
                                    placeholder="<?php _e( "Input Tag Name", 'webinarignition' ) ?>"
                                    type="text"
                                    id="webinar_tabs_name_<?php echo $i ?>"
                                    name="webinar_tabs[<?php echo $i ?>][name]"
                                    value="<?php echo $tab_name ?>"
                                    inputmode="text"
                                >

                                <input
                                    class="webinar_tabs_type"
                                    type="hidden"
                                    id="webinar_tabs_type_<?php echo $i ?>"
                                    name="webinar_tabs[<?php echo $i ?>][type]"
                                    value="<?php echo $tab_type ?>"
                                >
                            </div>
                            <br clear="left">
                        </div>

                        <div class="editSection">
                            <div class="inputTitle">
                                <div class="inputTitleCopy"><?php _e( "Tab content", 'webinarignition' ) ?></div>
                                <div class="inputTitleHelp">
                                    <?php _e( "Put any html code or shortcode inside. If you are using shortcodes, please test it before publishing webinar", 'webinarignition' ) ?>
                                </div>
                            </div>

                            <div class="inputSection">
                                <?php
                                $txt_id = 'webinar_tabs_content_' . $i;
                                $txt_name = 'webinar_tabs[' . $i . '][content]';
                                $txt_content = 'webinar_tabs[' . $i . '][content]';

                                $settings = array(
                                    'wpautop' => true, // use wpautop - add p tags when they press enter
                                    'teeny' => false, // output the minimal editor config used in Press This
                                    'textarea_name' => $txt_name,
                                    'tinymce' => array(
                                        'height' => '250' // the height of the editor
                                    )
                                );

                                wp_editor( stripcslashes($tab_content) , $txt_id, $settings );
                                ?>
                            </div>
                            <br clear="left">
                        </div>
                    </div>

                    <div class="auto_action_footer" style="padding: 15px;">
                        <button type="button" class="blue-btn btn deleteWebinarTab" style="color:#FFF;float:none;">
                            <i class="icon-remove"></i> <?php _e( "Delete", 'webinarignition' ) ?>
                        </button>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <?php

    if (WebinarignitionPowerups::is_modern_template_enabled($webinar_data)) {
        ?>
        <div class="additional_auto_action_control editSection">
            <button type="button" id="createWebinarTab" class="blue-btn-44 btn" style="color:#FFF;float:none;">
                <i class="icon-plus"></i> <?php _e( "New Tab", 'webinarignition' ) ?>
            </button>

            <button
                    type="button"
                    id="createWebinarQATab"
                    class="blue-btn-44 btn shortcode_tab"
                    data-title="<?php _e( "Q&A", 'webinarignition' ) ?>"
                    data-content='[wi_webinar_block id="<?php echo $webinarId ?>" block="webinar_qa_compact"]'
                    data-type="qa_tab"
                    style="color:#FFF;float:none;"
            >
                <i class="icon-plus"></i> <?php _e( "Q&A Tab", 'webinarignition' ) ?>
            </button>

            <button
                    type="button"
                    id="createWebinarGiveawayTab"
                    class="blue-btn-44 btn shortcode_tab"
                    data-title="<?php _e( "Your Gift", 'webinarignition' ) ?>"
                    data-content='[wi_webinar_block id="<?php echo $webinarId ?>" block="webinar_giveaway_compact"]'
                    data-type="giveaway_tab"
                    style="color:#FFF;float:none;"
            >
                <i class="icon-plus"></i> <?php _e( "Giveaway Tab", 'webinarignition' ) ?>
            </button>
        </div>

        <div
                id="webinar_tabs_template_container"
                data-title="<?php echo __( 'Webinar Tab Settings', 'webinarignition' ); ?>"
                style="display: none"
        >
            <div class="additional_auto_action_item auto_action_item webinar_tab_item">
                <div class="auto_action_header">
                    <h4>
                        <?php _e( "Webinar Tab", 'webinarignition' ) ?>
                        <span class="index_holder"></span>
                        <span class="auto_action_desc_holder"> </span>
                        <i class="icon-arrow-up"></i>
                        <i class="icon-arrow-down"></i>
                    </h4>
                </div>

                <div class="auto_action_body">
                    <div class="editSection">
                        <div class="inputTitle">
                            <div class="inputTitleCopy"><?php _e( "Tab title", 'webinarignition' ) ?></div>
                            <div class="inputTitleHelp">
                                <?php _e( "Try to use short title to keep tabs template compact", 'webinarignition' ) ?>
                            </div>
                        </div>

                        <div class="inputSection">
                            <input class="inputField elem webinar_tabs_name" placeholder="<?php _e( "Input Tag Name", 'webinarignition' ) ?>" type="text" name="" value="" inputmode="text">
                            <input class="webinar_tabs_type" type="hidden" name="" value="editor">
                        </div>
                        <br clear="left">
                    </div>

                    <div class="editSection">
                        <div class="inputTitle">
                            <div class="inputTitleCopy"><?php _e( "Tab content", 'webinarignition' ) ?></div>
                            <div class="inputTitleHelp">
                                <?php _e( "Put any html code or shortcode inside. If you are using shortcodes, please test it before publishing webinar", 'webinarignition' ) ?>
                            </div>
                        </div>

                        <div class="inputSection">
                            <textarea name="" placeholder="<?php _e( "Tab content", 'webinarignition' ) ?>" class="inputTextarea elem webinar_tabs_content" ></textarea>
                        </div>
                        <br clear="left">
                    </div>
                </div>

                <div class="auto_action_footer" style="padding: 15px;">
                    <button type="button" class="blue-btn btn deleteWebinarTab" style="color:#FFF;float:none;">
                        <i class="icon-remove"></i> <?php _e( "Delete", 'webinarignition' ) ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
}

function display_time_tags_section($webinar_data) {
    if (WebinarignitionPowerups::is_multiple_cta_enabled($webinar_data)) {
        ?>
        <div class="additional_auto_action_control editSection" style="border-bottom: 3px solid #e4e4e4;padding-top: 20px;">
            <h3 style="margin: 0;"><?php _e( "Tracking Settings", 'webinarignition' ) ?></h3>
        </div>

        <div
                class="tracking_tags_template_container"
                data-title="<?php echo __( 'Additional CTA Settings', 'webinarignition' ); ?>"
                style="display: none"
        >
            <div class="additional_auto_action_item auto_action_item tracking_tag_item">
                <div class="auto_action_header">
                    <h4>
                        <?php _e( "Tracking Tag", 'webinarignition' ) ?>
                        <span class="index_holder"></span>
                        <span class="auto_action_desc_holder"> </span>
                        <i class="icon-arrow-up"></i>
                        <i class="icon-arrow-down"></i>
                    </h4>
                </div>

                <div class="auto_action_body">
                    <div class="editSection">
                        <div class="inputTitle">
                            <div class="inputTitleCopy"><?php _e( "Tracking Tag Time :: Minutes:Seconds", 'webinarignition' ) ?></div>
                            <div class="inputTitleHelp">
                                <?php _e( "This is when you want your webinar time tracked. Ie. when your video gets to (or passed) 1 min 59 sec, it will be tracked. NB: Minute mark should be clear like '1' second - '59'", 'webinarignition' ) ?>
                            </div>
                        </div>

                        <div class="inputSection">
                            <input class="inputField elem min_sec_mask_field tracking_tags_time" placeholder="<?php _e( "f.e. 1:59", "webinarignition" ); ?>" type="text" name="" value="" inputmode="text">
                        </div>
                        <br clear="left">

                    </div>

                    <div class="editSection">
                        <div class="inputTitle">
                            <div class="inputTitleCopy"><?php _e( "Tracking Tag Name", 'webinarignition' ) ?></div>
                            <div class="inputTitleHelp">
                                <?php _e( "Put tag name which will be saved for lead tracking tags field", 'webinarignition' ) ?>
                            </div>
                        </div>

                        <div class="inputSection">
                            <input class="inputField elem tracking_tags_name" placeholder="<?php _e( "Input Tag Name", 'webinarignition' ) ?>" type="text" name="" value="" inputmode="text">
                        </div>
                        <br clear="left">

                    </div>

                    <div class="editSection">
                        <div class="inputTitle">
                            <div class="inputTitleCopy"><?php _e( "Tracking Tag Field Name", 'webinarignition' ) ?></div>
                            <div class="inputTitleHelp">
                                <?php _e( "If you want your tracking tags save into separate field, provide tracking field name", 'webinarignition' ) ?>
                            </div>
                        </div>

                        <div class="inputSection">
                            <input class="inputField elem tracking_tags_slug" placeholder="<?php _e( "Input Tag Field Name", 'webinarignition' ) ?>" type="text" name="" value="" inputmode="text">
                        </div>
                        <br clear="left">

                    </div>

                    <div class="editSection">

                        <div class="inputTitle">
                            <div class="inputTitleCopy" ><?php _e( "Tracking Pixel Code", 'webinarignition' ) ?></div>
                            <div class="inputTitleHelp" >
                                <?php echo htmlspecialchars(__( "Put your tracking pixel code here. It will be added to <head> tag", 'webinarignition' )); ?>
                            </div>
                        </div>

                        <div class="inputSection">
                            <textarea name="" class="inputTextarea elem tracking_tags_pixel" ></textarea>
                        </div>
                        <br clear="left" >

                    </div>
                </div>

                <div class="auto_action_footer" style="padding: 15px;">
                    <button type="button" class="blue-btn-44 btn cloneTrackingTag" style="color:#FFF;float:none;">
                        <i class="icon-copy"></i> <?php _e( "Clone", 'webinarignition' ) ?>
                    </button>

                    <button type="button" class="blue-btn btn deleteTrackingTag" style="color:#FFF;float:none;">
                        <i class="icon-remove"></i> <?php _e( "Delete", 'webinarignition' ) ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }

    if (!WebinarignitionPowerups::is_multiple_cta_enabled($webinar_data)) {
        ?><div style="display: none;"><?php
    }

    $default_tracking_tags_settings = [];
    $tracking_tags_settings = isset($webinar_data->tracking_tags) ? $webinar_data->tracking_tags : $default_tracking_tags_settings;
    ?>
    <div id="tracking_tags_container" class="tracking_tags_container">
        <?php
        if (!empty($tracking_tags_settings) && is_array($tracking_tags_settings)) {
            foreach ($tracking_tags_settings as $tti => $tracking_tag) {
                ?>
                <div class="additional_auto_action_item auto_action_item tracking_tag_item">
                    <div class="auto_action_header">
                        <h4>
                            <?php _e( "Tracking Tag", 'webinarignition' ) ?>
                            <span class="index_holder"><?php echo $tti + 1 ?></span>
                            <span class="auto_action_desc_holder">
                                    (<?php echo $tracking_tag['time'] ?> - <?php echo $tracking_tag['name'] ?>)
                                </span>
                            <i class="icon-arrow-up"></i>
                            <i class="icon-arrow-down"></i>
                        </h4>
                    </div>

                    <div class="auto_action_body">
                        <div class="editSection">
                            <div class="inputTitle">
                                <div class="inputTitleCopy"><?php _e( "Tracking Tag Time :: Minutes:Seconds", 'webinarignition' ) ?></div>
                                <div class="inputTitleHelp">
                                    <?php _e( "This is when you want your webinar time tracked. Ie. when your video gets to (or passed) 1 min 59 sec, it will be tracked. NB: Minute mark should be clear like '1' second - '59'", 'webinarignition' ) ?>
                                </div>
                            </div>

                            <div class="inputSection">
                                <input
                                        class="inputField elem min_sec_mask_field tracking_tags_time"
                                        placeholder="<?php _e( "f.e. 1:59", "webinarignition" ); ?>"
                                        type="text"
                                        name="tracking_tags[<?php echo $tti ?>][time]"
                                        id="tracking_tags_time_<?php echo $tti ?>"
                                        value="<?php echo $tracking_tag['time'] ?>"
                                        inputmode="text"
                                >
                            </div>
                            <br clear="left">

                        </div>

                        <div class="editSection">
                            <div class="inputTitle">
                                <div class="inputTitleCopy"><?php _e( "Tracking Tag Name", 'webinarignition' ) ?></div>
                                <div class="inputTitleHelp">
                                    <?php _e( "Put tag name which will be saved for lead tracking tags field", 'webinarignition' ) ?>
                                </div>
                            </div>

                            <div class="inputSection">
                                <input
                                        class="inputField elem tracking_tags_name"
                                        placeholder="<?php _e( "f.e. 1:59", "webinarignition" ); ?>"
                                        type="text"
                                        name="tracking_tags[<?php echo $tti ?>][name]"
                                        id="tracking_tags_name_<?php echo $tti ?>"
                                        value="<?php echo $tracking_tag['name'] ?>"
                                        inputmode="text"
                                >
                            </div>
                            <br clear="left">

                        </div>

                        <div class="editSection">
                            <div class="inputTitle">
                                <div class="inputTitleCopy"><?php _e( "Tracking Tag Field Name", 'webinarignition' ) ?></div>
                                <div class="inputTitleHelp">
                                    <?php _e( "If you want your tracking tags save into separate field, provide tracking field name", 'webinarignition' ) ?>
                                </div>
                            </div>

                            <div class="inputSection">
                                <input
                                        class="inputField elem tracking_tags_slug"
                                        placeholder="<?php _e( "f.e. 1:59", "webinarignition" ); ?>"
                                        type="text"
                                        name="tracking_tags[<?php echo $tti ?>][slug]"
                                        id="tracking_tags_slug_<?php echo $tti ?>"
                                        value="<?php echo $tracking_tag['slug'] ?>"
                                        inputmode="text"
                                >
                            </div>
                            <br clear="left">

                        </div>

                        <div class="editSection">

                            <div class="inputTitle">
                                <div class="inputTitleCopy" ><?php _e( "Tracking Pixel Code", 'webinarignition' ) ?></div>
                                <div class="inputTitleHelp" >
                                    <?php echo htmlspecialchars(__( "Put your tracking pixel code here. It will be added to <head> tag", 'webinarignition' )); ?>
                                </div>
                            </div>

                            <div class="inputSection">
                                <textarea
                                        name="tracking_tags[<?php echo $tti ?>][pixel]"
                                        id="tracking_tags_pixel_<?php echo $tti ?>"
                                        class="inputTextarea elem tracking_tags_pixel"
                                ><?php
                                    $tracking_pixel = !empty($tracking_tag['pixel']) ? $tracking_tag['pixel'] : '';
                                    echo htmlspecialchars(stripcslashes($tracking_pixel));
                                ?></textarea>
                            </div>
                            <br clear="left" >

                        </div>
                    </div>

                    <div class="auto_action_footer" style="padding: 15px;">
                        <button type="button" class="blue-btn-44 btn cloneTrackingTag" style="color:#FFF;float:none;">
                            <i class="icon-copy"></i> <?php _e( "Clone", 'webinarignition' ) ?>
                        </button>

                        <button type="button" class="blue-btn btn deleteTrackingTag" style="color:#FFF;float:none;">
                            <i class="icon-remove"></i> <?php _e( "Delete", 'webinarignition' ) ?>
                        </button>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <?php

    if (!WebinarignitionPowerups::is_multiple_cta_enabled($webinar_data)) {
        ?></div><?php
    }

    if (WebinarignitionPowerups::is_multiple_cta_enabled($webinar_data)) {
        ?>
        <div class="additional_auto_action_control editSection" style="border-bottom: 3px solid #e4e4e4;">
            <button type="button" id="createTrackingTag" class="blue-btn-44 btn" style="color:#FFF;float:none;">
                <i class="icon-plus"></i> <?php _e( "Create New Tag", 'webinarignition' ) ?>
            </button>
        </div>
        <?php
    }
}
