<?php

defined( 'ABSPATH' ) || exit;

?>

<div id="listapps" class="createWrapper" style="width: 940px;">

<div id="appHeader" class="dashHeaderListing" style="display: none;">
    <span><i class="icon-edit" style="margin-right: 5px;"></i> <?php  _e( 'Create a New LIVE Webinar', 'webinarignition' ); ?>:</span>
</div>

<div id="formArea" class="createWebinar" style="padding:20px; ">

<div class="weCreateLeft">

    <div class="weCreateTitle">

        <div class="weCreateTitleCopy">
            <span class="weCreateTitleHeadline"><?php  _e( 'Create New Webinar', 'webinarignition' ); ?></span>
            <span class="weCreateTitleSubHeadline"><?php  _e( 'Here you can set up a new webinar...', 'webinarignition' ); ?></span>
        </div>

        <div class="weCreateTitleIcon">
            <i class="icon-arrow-right icon-3x weCreateTitleIconI"></i>
        </div>

        <br clear="both"/>

    </div>

    <div class="weCreateExtraSettings">
        
        <div class="createTitleCopy1">Webinar Type: <span style="font-weight:normal;">
                <?php  _e( 'Select the webinar type...', 'webinarignition' ); ?></span>
        </div>
        <p class="createTitleCopy2"><?php  _e( 'You can create a Live Webinar, Auto Webinar, OR Clone a Webinar...', 'webinarignition' ); ?></p>

        <select class="inputField inputFieldDash2" name="cloneapp" id="cloneapp" autocomplete="off">
            <optgroup label="<?php _e('Create New', 'webinarignition'); ?>">
                <option value="auto"><?php _e( 'Evergreen', 'webinarignition' ); ?></option>
                <option value="new"><?php _e( 'Live', 'webinarignition' ); ?></option>
                <option value="import"><?php _e( 'Import', 'webinarignition' ); ?></option>
            </optgroup>
            <optgroup label="<?php _e('Clone Exisiting', 'webinarignition'); ?>">
                <?php
                    global $wpdb;
                    $table_db_name = $wpdb->prefix . "webinarignition";

                    $templates = $wpdb->get_results("SELECT * FROM $table_db_name", ARRAY_A);

                    foreach ($templates as $template) {

                        $name = stripslashes($template['appname']);
                        $id   = stripslashes($template['ID']);

                        $webinar_data_to_clone = WebinarignitionManager::get_webinar_data($id);

                        $webinar_to_clone_type = $webinar_data_to_clone->webinar_date === "AUTO" ? 'AUTO' : 'live';

                        echo "<option  value='$id'>$name</option>";
                    }

                    $current_user               = wp_get_current_user();
                    $current_user_first_name    = $current_user->user_firstname ? $current_user->user_firstname : '';
                    $current_user_last_name     = $current_user->user_lastname  ? $current_user->user_lastname  : '';
                    $current_user_name          = ( $current_user->user_firstname &&  $current_user->user_lastname ) ? $current_user_first_name . ' ' . $current_user_last_name : $current_user->display_name;
                    $tzstring                   = get_option( 'timezone_string' );
                    $date_formats               = [ __( 'F j, Y' ), 'Y-m-d', 'm/d/Y', 'd/m/Y' ];
                    $default_date_format        = $date_formats[0];
                    $time_formats               = [ __( 'g:i a' ), 'g:i A', 'H:i' ];
                    $time_format                = $time_formats[0];
                ?>
            </optgroup>
        </select>

        <div class="createTitleCopy1"><?php  _e( 'Webinar Name', 'webinarignition' ); ?>: <span style="font-weight:normal;"><?php  _e( 'Give your new webinar a name / pretty url...', 'webinarignition' ); ?></span>
        </div>
        <p class="createTitleCopy2">** <?php  _e( 'Used for the URL: ie:', 'webinarignition' ); ?> <b><?php _e( 'http://yoursite.com/webinar-name', 'webinarignition' ); ?></b></p>
        <input class="inputField inputFieldDash2" placeholder="<?php  _e( 'Webinar Name', 'webinarignition' ); ?>" type="text" name="appname" id="appname" value="">

        <div class="createTitleCopy1" id="webinar_language">
            
            <?php  _e( 'Webinar Language', 'webinarignition' ); ?>: <span style="font-weight:normal;">
            <?php  _e( 'Select the webinar language...', 'webinarignition' ); ?></span>
            
            <?php

            $statusCheck = WebinarignitionLicense::get_license_level();

            $is_ultimate_activated = false;
            if( !empty($statusCheck) && (isset($statusCheck->switch) || isset($statusCheck->is_trial)) ) {
	            $is_ultimate_activated = !empty($statusCheck->is_trial) || $statusCheck->switch === 'enterprise_powerup'; //If enterprise_powerup, consider it as ultimate
            }

            $show_all_live_languages = true;
            $show_all_eg_languages   = true;

            ?>
            <input type="hidden" id="wi_segl" name="wi_segl" value="<?php echo $show_all_eg_languages ? 1 : 0;?>" disabled />
            <input type="hidden" id="wi_sll" name="wi_sll" value="<?php echo $show_all_live_languages ? 1 : 0;?>" disabled />

            <?php
            require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
            $translations        = wp_get_available_translations();
            $available_languages = webinarignition_get_available_languages();
            $available_languages = array_merge(['en_US'], $available_languages);
            $translations        = array_merge(['en_US' => ['native_name' => __('English')]], $translations);
            $selected_language   = get_locale();
            ?>
            <select class="inputField inputFieldDash2" id="applang" name="applang" autocomplete="off">
                <?php foreach ($available_languages as $language): ?>
                    <option value="<?php echo $language; ?>" <?php selected($selected_language, $language, true); ?>><?php echo $translations[$language]['native_name']; ?></option>
                <?php endforeach; ?>
            </select>
            <span id="wi_new_webinar_lang_select" class="spinner wi-spinner"></span>
            <p class="createTitleCopy2"><a title="Want to overwrite language strings?" href="https://webinarignition.tawk.help/article/overwrite-webinar-language" target="_blank"><?php  _e( 'Want to overwrite language strings?', 'webinarignition' ); ?></a></p>
            <p class="createTitleCopy2"><a title="Want to add a language?" href="https://webinarignition.tawk.help/article/add-language-to-webinarignition" target="_blank"><?php  _e( 'Want to add a language?', 'webinarignition' ); ?></a></p>

            <div class="createTitleCopy1">
                <?php  _e( 'Use webinar language in webinar settings?', 'webinarignition' ); ?>
            </div>
            <select class="inputField inputFieldDash2" id="settings_language" name="settings_language" autocomplete="off" ?>>
                <option value="no"><?php _e( 'No', 'webinarignition' ); ?></option>
                <option value="yes" <?php selected( $selected_language === 'en_US', true, true); ?>><?php _e( 'Yes', 'webinarignition' ); ?></option>
            </select>

            <input type="hidden" name="site_default_language" id="site_default_language" value="<?php echo determine_locale(); ?>">
        </div>
        
        <div class="importArea" style="display:none;">
            <div class="createTitleCopy1"><?php  _e( 'Import Campaign', 'webinarignition' ); ?>: <span style="font-weight:normal;"><?php  _e( 'Clone From External WI Webinar', 'webinarignition' ); ?></span>
            </div>
            <p class="createTitleCopy2"><?php  _e( 'Paste in the export code below from another WI campaign...', 'webinarignition' ); ?></p>
            <textarea id="importcode" style="width:100%; height: 150px;"
                      placeholder="<?php _e( "add import code here...", "webinarignition" ); ?>"></textarea>
        </div>
        
        
            
            <div class="weDashSection locale_formats date_formats">
                <span class="weDashSectionTitle"><?php  _e( 'Date Format', 'webinarignition' ); ?> * <span class="weDashSectionIcon"><i class="icon-calendar"></i></span> </span>

                <br/>
                <?php
                foreach ($date_formats as $date_format) {
                    ?>
                        <label id='default_date_radio_label'>
                            <input type='radio' name="date_format" value="<?php echo esc_attr( $date_format ); ?>" <?php checked($default_date_format === $date_format, true, true); ?> /><span class="date-time-text format-i18n"><?php echo date_i18n( $date_format ); ?></span><code><?php echo esc_html( $date_format ); ?></code>
                        </label>
                        <br/><br/>
                    <?php
                }
                ?>
                <input type="hidden" id="apptz" value="<?php echo wp_timezone_string(); ?>" />
                <div id="wi_show_day_wrap">
                <label>
                    <input name="wi_show_day" type="checkbox" checked><span style="margin-left: 15px;"><?php _e('Show Day', 'webinarignition'); ?></span> (<code id="wi_day_string"><?php echo date_i18n('D'); ?></code>)
                    <div id="wi_day_string_input" style="width: 160px; display: flex;float: right;">
                        <label style="text-align: right;"><input type="radio" name="day_string" value="D" data-string="<?php echo date_i18n('D'); ?>" checked> <?php _e('Short','webinarignition');?></label>
                        <label style="text-align: right;"><input type="radio" name="day_string" value="l" data-string="<?php echo date_i18n('l'); ?>"> <?php _e('Long','webinarignition');?></label>
                    </div>
                </label>
                <br/>
                <br/>
                </div>
                <label>
                    <input type="radio" name="date_format" id="date_format_custom_radio" value="custom" />
                    <span class="date-time-text date-time-custom-text"><?php _e( 'Custom:' ); ?></span>
                    <input type="text" name="date_format_custom" id="date_format_custom" value="<?php echo esc_attr( $default_date_format ); ?>" class="float-right small-text" autocomplete="off" />
                </label>
                <br/><br/>
                <p>
                    <strong class="preview_text"><?php _e( 'Preview:' ); ?></strong>
                    <span class="formatPreview" id="date_format_preview"><?php echo date_i18n( $default_date_format ); ?></span>
                </p>

            </div>    
            
            <div class="weDashSection locale_formats time_formats">
                <span class="weDashSectionTitle"><?php  _e( 'Time Format', 'webinarignition' ); ?> * <span class="weDashSectionIcon"><i class="icon-time"></i></span> </span>
                <br/>
                <?php
                    
                    $custom         = true;

                    foreach ( $time_formats as $format ) {
                        echo "\t<label id='default_time_radio_label'><input type='radio' name='time_format' value='" . esc_attr( $format ) . "'";
                        if ( $time_format === $format ) { 
                                echo " checked='checked'";
                                $custom = false;
                        }
                        echo ' /> <span class="date-time-text format-i18n">' . date_i18n( $format ) . '</span><code>' . esc_html( $format ) . "</code></label><br/><br/>\n";
                    }

                    echo '<label><input type="radio" name="time_format" id="time_format_custom_radio" value="'.esc_attr( $time_format ).'"';
                    checked( $custom );
                    echo '/> <span class="date-time-text date-time-custom-text">' . __( 'Custom:' ) . '</span><input type="text" name="time_format_custom" id="time_format_custom" value="' . esc_attr( $time_format ) . '" class="float-right small-text" /></label>';
                    echo '<br/><br/><p><strong class="preview_text">' . __( 'Preview:' ) . '</strong> <span class="formatPreview" id="time_format_preview">' . date_i18n( get_option( 'time_format' ) ) . '</span>';

                    echo "\t<p class='date-time-doc'>" . __( '<a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank">Documentation on date and time formatting</a>.' ) . "</p>\n";
                    
                ?>
                
            </div>            

    </div>

    <div class="timezoneRef" style="color: #FFF;">
        <div class="timezoneRefTitle"><b><?php  _e( 'REFERENCE', 'webinarignition' ); ?></b> :: <?php  _e( 'Current Time:', 'webinarignition' ); ?> <span class="timezoneRefZ"></span></div>
    </div>

</div>

<div class="weCreateRight">

    <div class="weDashRight" style="margin-top: 0px;">

        <div class="weDashDateTitle">
            <!-- <i class="icon-ticket"></i> Webinar Event Info: -->
            <div class="dashWebinarTitleIcon"><i class="icon-ticket icon-3x"></i></div>

            <div class="dashWebinarTitleCopy">
                <h2 style="margin:0px; margin-top: 3px;"><?php  _e( 'Webinar Event Info', 'webinarignition' ); ?></h2>

                <p style="margin:0px; margin-top: 3px;"><?php  _e( 'The core settings for your webinar event...', 'webinarignition' ); ?></p>
            </div>

            <br clear="left">
        </div>

        <div class="weDashDateInner">

            <div class="weDashSection">
				<span class="weDashSectionTitle"><?php  _e( 'Webinar Title', 'webinarignition' ); ?> *
					<span class="weDashSectionIcon"><i class="icon-desktop"></i></span>
				</span>
                <br clear="right">
                <input type="text" class="inputField inputFieldDash elem" name="webinar_desc" id="webinar_desc" value=""
                       placeholder="<?php  _e( 'Your Webinar Title', 'webinarignition' ); ?>...">
            </div>

            <div class="weDashSection">
				<span class="weDashSectionTitle"><?php  _e( 'Webinar Host(s)', 'webinarignition' ); ?> *
					<span class="weDashSectionIcon"><i class="icon-user"></i></span>
				</span>
                <br clear="right">
                <input type="text" class="inputField inputFieldDash elem" name="webinar_host" id="webinar_host" value="<?php echo $current_user_name; ?>"
                       placeholder="<?php  _e( 'The Name Of The Host(s)', 'webinarignition' ); ?>...">
            </div>




            <div class="weDashSection" id="createToggle1">
				<span class="weDashSectionTitle"><?php  _e( 'Event Date', 'webinarignition' ); ?> * <span class="weDashSectionIcon"><i class="icon-calendar"></i></span>
				</span>
                <br clear="right">
                <input type="text" class="inputField inputFieldDash elem dp-date" name="webinar_date" id="webinar_date" value="<?php echo date_i18n( $default_date_format )  ?>">
            </div>

            <div class="weDashSection" id="createToggle2">
                        <span class="weDashSectionTitle"><?php  _e( 'Event Time', 'webinarignition' ); ?> * 
					<span class="weDashSectionIcon"><i class="icon-time"></i></span>
				</span>
                <br clear="right">
                <input type="text" class="timepicker inputField inputFieldDash elem" name="webinar_start_time" id="webinar_start_time" value="<?php echo date_i18n( $time_format ); ?>"/>
            </div>

            <div class="weDashSection" id="createToggle3">
				<span class="weDashSectionTitle"><?php  _e( 'Event Timezone', 'webinarignition' ); ?> *
					<span class="weDashSectionIcon"><i class="icon-globe"></i></span>
				</span>
                <br clear="right">
                <select name="webinar_timezone" id="webinar_timezone" class="inputField inputFieldDash elem ">
                        <?php echo webinarignition_create_tz_select_list( $tzstring, get_user_locale() ); ?>
                </select> 
            </div>

        </div>

    </div>

</div>
    
<div class="blue-btn-2create btn" id="createnewappBTN" style="float: right; width: 350px;">
    <a id="createnewapp" href="<?php echo admin_url('?page=webinarignition-dashboard&create'); ?>">
        <i class="icon-plus-sign" style="margin-right: 5px;"></i>
        <?php  _e( 'Create New Webinar', 'webinarignition' ); ?>
    </a>
</div>      

<br clear="all"/>



</div>
      

</div>

<br clear="left"/>
