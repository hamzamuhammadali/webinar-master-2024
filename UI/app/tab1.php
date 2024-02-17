<?php defined( 'ABSPATH' ) || exit; ?>

<div class="tabber" id="tab1">

<div class="titleBar">

    <div class="titleBarIcon">
        <!-- <i class="icon-dashboard icon-4x"></i> -->
    </div>

    <div class="titleBarText">
        <h2><?php  esc_html_e( 'Dashboard - Your Webinar Settings', 'webinarignition' ); ?></h2>

        <p><?php  esc_html_e( 'In the console, you will find your leads, questions, call-to-actions (live only) ...', 'webinarignition' ); ?></p>
    </div>

    <div class="launchConsole">
        <a href="<?php
        $console_link = webinarignition_fixPerma($data->postID);
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
            $console_link = str_replace('http://', 'https://', $console_link);
        }
        echo $console_link;
        ?>console#/dashboard" target="_blank"><i
                class="icon-external-link-sign"></i> <?php  esc_html_e( 'Show Live Console', 'webinarignition' ); ?></a>
    </div>

    <br clear="all"/>
</div>

<!-- NEW AREA TOP -->

<div class="weDashLeft">

<?php
// Evergreen Check
if ($webinar_data->webinar_date == "AUTO") {
    // Evergreen
} else {
    ?>

    <div class="weDashWebinarTitle">

        <div class="dashWebinarTitleIcon"><i class="icon-play-sign icon-3x"></i></div>

        <div class="dashWebinarTitleCopy">
            <h2 style="color:#FFF !important;"><?php  esc_html_e( 'Webinar Master Switch:', 'webinarignition' ); ?></h2>

            <p><?php  esc_html_e( 'Toggle the event / webinar status', 'webinarignition' ); ?></p>
        </div>

        <br clear="left"/>

    </div>

    <div class="weDashWebinarInner" style="margin-bottom: 15px;">
        <div class="webinarURLArea">
            <div class="webinarURLAreaStatus">
                <ul class="webinarStatusGroup">
                    <li><a href="#" class="webinarStatus webinarStatusFirst <?php
                        if ($webinar_data->webinar_switch == "countdown" || $webinar_data->webinar_switch == "") {
                            echo "webinarStatusSelected";
                        }
                        ?>" data="countdown"><i class="icon-time"></i> <?php  esc_html_e( 'Countdown', 'webinarignition' ); ?></a></li>
                    <li><a href="#" class="webinarStatus <?php
                        if ($webinar_data->webinar_switch == "live") {
                            echo "webinarStatusSelected";
                        }
                        ?>" data="live"><i class="icon-microphone"></i> <?php  esc_html_e( 'Live', 'webinarignition' ); ?></a></li>
                    <li><a href="#" class="webinarStatus <?php
                        if ($webinar_data->webinar_switch == "replay") {
                            echo "webinarStatusSelected";
                        }
                        ?>" data="replay"><i class="icon-refresh"></i> <?php  esc_html_e( 'Replay', 'webinarignition' ); ?></a></li>
                    <li><a href="#" class="webinarStatus webinarStatusEnd <?php
                        if ($webinar_data->webinar_switch == "closed") {
                            echo "webinarStatusSelected";
                        }
                        ?>" data="closed"><i class="icon-lock"></i> <?php  esc_html_e( 'Closed', 'webinarignition' ); ?></a></li>
                    <input type="hidden" name="webinar_switch" id="webinar_switch"
                           value="<?php echo $webinar_data->webinar_switch; ?>">
                    <br clear="left"/>
                </ul>
            </div>
        </div>
    </div>
<?php
}
?>

    <div class="weDashWebinarTitle">
        <div class="dashWebinarTitleIcon"><i class="icon-share-sign icon-3x"></i></div>
        <div class="dashWebinarTitleCopy">
            <h2 style="color:#FFF !important;"><?php  esc_html_e( 'Your Webinar URL', 'webinarignition' ); ?></h2>

            <p>
                <?php
                if ($webinar_data->webinar_date == "AUTO") {
                    esc_html_e( 'This is the URL for your live webinar you can share with your audience.', 'webinarignition' );
                }  else {
                    esc_html_e( 'This is the URL for your evergreen webinar you can share with your audience.', 'webinarignition' );
                }
                ?>
            </p>
        </div>

        <br clear="left"/>
    </div>

    <div class="weDashWebinarInner">
        <div style="padding: 15px;">
            <span style="font-size:16px; font-weight:bold; display:block;"><?php  esc_html_e( 'Registration Page URL:', 'webinarignition' ); ?></span>
            <input
                    id="custom_registration_page-shareUrl-1"
                    style="margin-top:15px;"
                    onclick="this.select()"
                    type="text"
                    class="inputField inputFieldDash"
                    data-default-value="<?php echo get_permalink($data->postID); ?>"
                    value="<?php  esc_html_e( 'Loading URL...', 'webinarignition' ); ?>"
                    readonly
            >
        </div>
        <?php
        if ($webinar_data->webinar_date == "AUTO") {

        } else {
            ?>
            <div style="padding: 0 15px 15px 15px;">
                <span style="font-size:16px; font-weight:bold; display:block;"><?php  esc_html_e( 'One Click Registration URL:', 'webinarignition' ); ?></span>
                <span><?php  esc_html_e( 'Use the link in emails. Replace NAME/EMAIL with placeholder e.g. %Subscriber:CustomFieldFirstName% %Subscriber:EmailAddress%', 'webinarignition' ); ?></span>
                <input style="margin-top:15px;" onclick="this.select()" type="text" class="inputField inputFieldDash"
                       value="<?php echo webinarignition_fixPerma($data->postID); ?>register-now&n=NAME&e=EMAIL">
            </div>
            <?php
        }
        ?>

        <?php $host_presenters_url = WebinarignitionManager::get_host_presenters_url($ID); ?>
        <?php $support_stuff_url = WebinarignitionManager::get_support_stuff_url($ID); ?>
        <input type="hidden" name="host_presenters_url" value="<?php echo $host_presenters_url; ?>">
        <input type="hidden" name="support_stuff_url" value="<?php echo $support_stuff_url; ?>">

        <?php
        if (WebinarignitionPowerups::is_multiple_support_enabled($webinar_data)) {
            ?>
            <div style="padding: 0 15px 15px 15px;">
                <span style="font-size:16px; font-weight:bold; display:block;"><?php  esc_html_e( 'Host/Presenters URL:', 'webinarignition' ); ?></span>
                <p style="margin: 5px 0 0 0;">
                    <?php  esc_html_e( 'Make sure ', 'webinarignition' ); ?>
                    <strong><?php  esc_html_e( 'Notifications', 'webinarignition' ); ?></strong> >
                    <strong><?php  esc_html_e( 'Live Console Q&A', 'webinarignition' ); ?></strong> >
                    <strong><?php  esc_html_e( 'Enable Multiple Hosts', 'webinarignition' ); ?></strong>
                    <?php  esc_html_e( 'is enabled', 'webinarignition' ); ?>
                </p>
                <input
                        style="margin-top:15px;" onclick="this.select()"
                        type="text" class="inputField inputFieldDash"
                        value="<?php echo $console_link . 'console&_host_presenters_token='. $host_presenters_url; ?>"
                >

            </div>

            <div style="padding: 0 15px 15px 15px;">
                <span style="font-size:16px; font-weight:bold; display:block;"><?php  esc_html_e( 'Support Staff URL:', 'webinarignition' ); ?></span>
                <p style="margin: 5px 0 0 0;">
                    <?php  esc_html_e( 'Make sure ', 'webinarignition' ); ?>
                    <strong><?php  esc_html_e( 'Notifications', 'webinarignition' ); ?></strong> >
                    <strong><?php  esc_html_e( 'Live Console Q&A', 'webinarignition' ); ?></strong> >
                    <strong><?php  esc_html_e( 'Enable Question Notifications', 'webinarignition' ); ?></strong> >
                    <strong><?php  esc_html_e( 'Enable Support Staff', 'webinarignition' ); ?></strong>
                    <?php  esc_html_e( 'is enabled', 'webinarignition' ); ?>
                </p>
                <input style="margin-top:15px;" onclick="this.select()" type="text" class="inputField inputFieldDash"
                       value="<?php echo $console_link . 'console&_support_stuff_token='. $support_stuff_url; ?>">

            </div>
            <?php
        }
        ?>

    </div>

<div class="statsLabelx" style="text-align:right; padding-top:15px;">
    <?php  esc_html_e( 'Total Views', 'webinarignition' ); ?> / <b><?php  esc_html_e( 'Unique Views', 'webinarignition' ); ?></b>
</div>

<div class="webinarPreviewItem webinarPreviewItemTop" style="<?php
if ($webinar_data->webinar_date == "AUTO") {
    echo "margin-top:0px;";
}
?>">
    <?php
    // Get Total & Uniques
    $getTotal_lp = $data->total_lp;
    $getTotal_lp = explode("%%", $getTotal_lp);
    $registration_preview_url = add_query_arg( ['preview' => 'true'], get_the_permalink($data->postID));
    ?>
    <div class="webinarPreviewIcon"><i class="icon-calendar icon-2x"></i></div>
    <div class="webinarPreviewTitle">
        <a
                href="<?php echo $registration_preview_url; ?>"
                target="_blank"
                data-default-href="<?php echo $registration_preview_url; ?>"
                class="custom_registration_page-webinarPreviewLinkDefaultHolder-1"
        >
            <i class="icon-external-link"></i>
            <?php  esc_html_e( 'View Registration Page', 'webinarignition' ); ?>
        </a>
    </div>
    <!-- <div class="webinarPreviewStat"><span class="dashViews" >Total: </span> <?php
    if ($getTotal_lp[1] == "") {
        echo "0";
    } else {
        echo $getTotal_lp[1];
    }
    ?> <span class="dashViews" >Uniques:</span> <?php
    if ($getTotal_lp[0] == "") {
        echo "0";
    } else {
        echo $getTotal_lp[0];
    }
    ?> </div> -->
    <div class="webinarPreviewStat"><span style="font-weight: normal;"><?php
            if ($getTotal_lp[1] == "") {
                echo "0";
            } else {
                echo $getTotal_lp[1];
            }
            ?> / </span> <?php
        if ($getTotal_lp[0] == "") {
            echo "0";
        } else {
            echo $getTotal_lp[0];
        }
        ?> </div>
    <br clear="both"/>
</div>

<div class="webinarPreviewItem">
    <?php
 // Get Total & Uniques

    $getTotal_ty = $data->total_ty;
    $getTotal_ty = explode("%%", $getTotal_ty);
    $thank_you_preview_url = add_query_arg( ['thankyou' =>'', 'lid' => '[lead_id]', 'preview' => 'true'], get_the_permalink($data->postID));
    ?>
    <div class="webinarPreviewIcon"><i class="icon-copy icon-2x"></i></div>
    <div class="webinarPreviewTitle">
        <a href="<?php echo $thank_you_preview_url; ?>" target="_blank" data-default-href="<?php echo $thank_you_preview_url; ?>" class="custom_thankyou_page-webinarPreviewLinkDefaultHolder">
            <i class="icon-external-link"></i>
            <?php  esc_html_e( 'View Thank You Page', 'webinarignition' ); ?>
        </a>
    </div>
    <!-- <div class="webinarPreviewStat"><span class="dashViews" >Total: </span> <?php
    if ($getTotal_ty[1] == "") {
        echo "0";
    } else {
        echo $getTotal_ty[1];
    }
    ?> <span class="dashViews" >Uniques:</span> <?php
    if ($getTotal_ty[0] == "") {
        echo "0";
    } else {
        echo $getTotal_ty[0];
    }
    ?> </div> -->
    <div class="webinarPreviewStat"><span style="font-weight: normal;"><?php
            if ($getTotal_ty[1] == "") {
                echo "0";
            } else {
                echo $getTotal_ty[1];
            }
            ?> / </span> <?php
        if ($getTotal_ty[0] == "") {
            echo "0";
        } else {
            echo $getTotal_ty[0];
        }
        ?> </div>
    <br clear="both"/>
</div>



<?php
if ($webinar_data->webinar_date == "AUTO") {
    // Evergreen
} else {
	$countdown_preview_url = add_query_arg( ['countdown' =>'', 'lid' => '[lead_id]', 'preview' => 'true'], get_the_permalink($data->postID));
    ?>

    <div class="webinarPreviewItem">
        <div class="webinarPreviewIcon"><i class="icon-time icon-2x"></i></div>
        <div class="webinarPreviewTitle">
            <a
                    href="<?php echo $countdown_preview_url; ?>"
                    target="_blank"
                    data-default-href="<?php echo $countdown_preview_url; ?>"
                    class="custom_countdown_page-webinarPreviewLinkDefaultHolder"
            >
                <i class="icon-external-link"></i>
                <?php  esc_html_e( 'Preview Countdown Page', 'webinarignition' ); ?>
            </a>
        </div>

        <br clear="both"/>
    </div>

<?php
}
?>

<div class="webinarPreviewItem">
    <?php
    // Get Total & Uniques
    $getTotal_live = $data->total_live;
    $getTotal_live = explode("%%", $getTotal_live);
    $webinar_preview_url = add_query_arg( ['webinar' =>'', 'lid' => '[lead_id]', 'preview' => 'true'], get_the_permalink($data->postID));
    ?>
    <div class="webinarPreviewIcon"><i class="icon-microphone icon-2x"></i></div>
    <div class="webinarPreviewTitle">
        <a
                href="<?php echo $webinar_preview_url; ?>"
                target="_blank"
                data-default-href="<?php echo $webinar_preview_url; ?>"
                class="custom_webinar_page-webinarPreviewLinkDefaultHolder"
        >
            <i class="icon-external-link"></i>
            <?php  esc_html_e( 'Preview Webinar Page', 'webinarignition' ); ?>
        </a>
    </div>
    <br clear="both"/>
</div>

<div class="webinarPreviewItem webinarPreviewItemBottom">
    <?php
    // Get Total & Uniques
    $getTotal_replay = $data->total_replay;
    $getTotal_replay = explode("%%", $getTotal_replay);
    $replay_preview_url = add_query_arg( ['replay' =>'', 'lid' => '[lead_id]', 'preview' => 'true'], get_the_permalink($data->postID));
    ?>
    <div class="webinarPreviewIcon"><i class="icon-film icon-2x"></i></div>
    <div class="webinarPreviewTitle">
        <a
                href="<?php echo $replay_preview_url; ?>"
                target="_blank"
                data-default-href="<?php echo $replay_preview_url; ?>"
                class="custom_replay_page-webinarPreviewLinkDefaultHolder"
        >
            <i class="icon-external-link"></i>
            <?php  esc_html_e( 'Preview Replay Page', 'webinarignition' ); ?>
        </a>
    </div>
    <br clear="both"/>
</div>

<div class="timezoneRef" style="<?php
if ($webinar_data->webinar_date == "AUTO") {
    echo "display:none;";
}
?>">
    <div class="timezoneRefTitle"><b><?php  esc_html_e( 'REFERENCE', 'webinarignition' ); ?></b> :: <?php  esc_html_e( 'Current Time:', 'webinarignition' ); ?> <span class="timezoneRefZ"></span></div>
</div>

<?php if ($webinar_data->webinar_date == "AUTO") { ?>

    <div class="timezoneRef">
        <b><?php  esc_html_e( 'Notice:', 'webinarignition' ); ?></b> <?php  esc_html_e( 'The previews above are for the Thank You Page, Webinar & Replay are just previews. They change depending  on the time & date chosen by the lead...', 'webinarignition' ); ?>
    </div>

<?php } ?>

</div>

<div class="weDashRight">

<div class="weDashDateTitle">
    <!-- <i class="icon-ticket"></i> Webinar Event Info: -->
    <div class="dashWebinarTitleIcon"><i class="icon-ticket icon-3x"></i></div>

    <div class="dashWebinarTitleCopy">
        <h2 style="margin:0px; margin-top: 3px;"><?php  esc_html_e( 'Webinar Event Info', 'webinarignition' ); ?></h2>

        <p style="margin:0px; margin-top: 3px;"><?php  esc_html_e( 'The core settings for your webinar event...', 'webinarignition' ); ?></p>
    </div>

    <br clear="left"/>
</div>

<div class="weDashDateInner">

<div class="weDashSection">
    <span class="weDashSectionTitle"><?php  esc_html_e( 'Webinar Title', 'webinarignition' ); ?>
            <span class="weDashSectionIcon"><i class="icon-desktop"></i></span>
    </span>

    <br clear="right"/>
    <input type="text" class="inputField inputFieldDash elem" name="webinar_desc" id="webinar_desc" value="<?php echo esc_attr($webinar_data->webinar_desc); ?>"/>
</div>

    <?php
    if ( !empty( $webinar_data->webinar_lang ) ) {
        
        require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
        $languages    = wp_get_available_translations();
        $webinar_lang = ( $webinar_data->webinar_lang === "en_US" ) ? 'English' : $languages[$webinar_data->webinar_lang]['native_name'];
	    $webinar_lang_auto_set = false;
        if( isset($webinar_data->id) && !empty($webinar_data->id) ) {
	        $webinar_lang_auto_set = get_option("webinarignition_lang_auto_set_{$webinar_data->id}", false);
        }
       
        ?>
        <div class="weDashSection">
            <span class="weDashSectionTitle"><?php  esc_html_e( 'Webinar Language', 'webinarignition' ); ?>
                    <span class="weDashSectionIcon"><i class="icon-desktop"></i></span>
            </span>

            <br clear="right"/>
            <div class="inputField inputFieldDash" style="display: block;width: 100%;background-color: #f0f0f1;padding: 0 8px;min-height: 30px;border-radius: 4px;"><?php echo $webinar_lang; ?> <?php echo $webinar_lang_auto_set ? '<span>(<a href="https://webinarignition.tawk.help/article/auto-set-webinar-language-for-webinars-created-before-version-290" target="_blank">'. esc_html__('auto set', 'webinarignition') .'</a>)</span>' : ''; ?></div>
            <input type="hidden" class="inputField inputFieldDash elem" readonly name="webinar_lang" id="webinar_lang" value="<?php echo $webinar_data->webinar_lang; ?>"/>
        </div>
        <?php
    }
    ?>

<div class="weDashSection">
    <span class="weDashSectionTitle"><?php  esc_html_e( 'Webinar Host(s)', 'webinarignition' ); ?>
            <span class="weDashSectionIcon"><i class="icon-user"></i></span>
    </span>
    <br clear="right"/>
    <input type="text" class="inputField inputFieldDash elem" name="webinar_host" id="webinar_host"
           value="<?php echo $webinar_data->webinar_host; ?>"/>
</div>

<?php
// Evergreen Check
if ($webinar_data->webinar_date == "AUTO") {
    // Evergreen
    ?>
    <input type="hidden" class="inputField inputFieldDash elem" name="webinar_date" id="webinar_date"
           value="<?php echo $webinar_data->webinar_date; ?>"/>
<?php
} else {
    ?>

    <div class="weDashSection">
                                        <span class="weDashSectionTitle"><?php  esc_html_e( 'Event Date', 'webinarignition' ); ?>
                                                <span class="weDashSectionIcon"><i class="icon-calendar"></i></span>
                                        </span>
        <br clear="right"/>
        <input type="text" class="inputField inputFieldDash elem dp-date" name="webinar_date" id="webinar_date" value="<?php echo webinarignition_get_localized_date($webinar_data); ?>"/>
    </div>

    <div class="weDashSection">
                                        <span class="weDashSectionTitle"><?php  esc_html_e( 'Event Time', 'webinarignition' ); ?>
                                                <span class="weDashSectionIcon"><i class="icon-time"></i></span>
                                        </span>
        <br clear="right"/>
        <input type="text" class="timepicker inputField inputFieldDash elem" name="webinar_start_time" id="webinar_start_time" value="<?php echo webinarignition_get_localized_time( $webinar_data->webinar_start_time ); ?>"/>
    </div>

    <div class="weDashSection">
        <span class="weDashSectionTitle"><?php  esc_html_e( 'Event Timezone', 'webinarignition' ); ?>
                <span class="weDashSectionIcon"><i class="icon-globe"></i></span>
        </span>
        <br clear="right"/>
        <?php  $webinarTZ = webinarignition_convert_utc_to_tzid($webinar_data->webinar_timezone); ?>
        
        <select name="webinar_timezone" id="webinar_timezone" class="inputField inputFieldDash elem ">
                <?php echo webinarignition_create_tz_select_list( $webinarTZ, get_user_locale() ); ?>
        </select>   
        
    </div>


<?php
}
?>
	<?php
    $statusCheck = WebinarignitionLicense::get_license_level();
	$is_eg_webinar = strtolower($webinar_data->webinar_date) === 'auto'; //is evergreen webinar
	$is_basic_pro = in_array($statusCheck->switch, ['pro','basic']);

	$shortcode_string_old = "[wi_webinar id=\"{$ID}\"]";
	$shortcode_string_link_old = sprintf( ' %s <a href="https://webinarignition.tawk.help/article/shortcode-sign-up-widget-wi_webinar-id105-outdated" target="_blank">%s</a>', esc_html__( 'Outdated', 'webinarignition' ), esc_html__( 'Read more', 'webinarignition' ) );
	$shortcode_string = $shortcode_string_new = "[wi_webinar_block id=\"{$ID}\" block=\"reg_optin_section\"]";
	$shortcode_string_link = '';

	if( !$is_basic_pro ) {
//	    if($is_eg_webinar) {
//		    $shortcode_string_link = sprintf( '<a href="' . admin_url( '?page=webinarignition-dashboard' ) . '" target="_blank">%s</a>', esc_html__( 'Upgrade to Ultimate now!', 'webinarignition' ) );
//	    } else {
		    $shortcode_string_link = sprintf(' <a href="https://webinarignition.tawk.help/article/create-your-own-designed-webinar-landing-pages_webinar-registration-pages" target="_blank">%s</a>', esc_html__('Read more', 'webinarignition'));
//        }
    } else {
        $shortcode_string_link = sprintf(' %s <a href="https://webinarignition.tawk.help/article/create-your-own-designed-webinar-landing-pages_webinar-registration-pages" target="_blank">%s</a>', esc_html__('Please use new!', 'webinarignition'), esc_html__('Read more', 'webinarignition'));
    }
	?>

    <!-- NEW SHORTCODE -->
    <div class="weDashSection">
        <span class="weDashSectionTitle"><?php echo esc_html_e( 'Registration Shortcode', 'webinarignition' ); ?>

                <span class="weDashSectionIcon"><i class="icon-code"></i></span>
        </span>
        <br clear="right">

        <?php if( $is_basic_pro  ): ?>
            <div class="inputField inputFieldDash" style="display: block;width: 100%;background-color: #f0f0f1;padding: 0 8px;min-height: 30px;border-radius: 4px;text-decoration: line-through;"><?php echo $shortcode_string_old; ?></div>
            <span style="float:right;"><?php echo $shortcode_string_link_old; ?>&nbsp;</span><br><br>
        <?php endif; ?>

        <!--        --><?php //if( $is_basic_pro  ): ?>
        <input type="text" class="inputField inputFieldDash elem" value='<?php echo $shortcode_string; ?>'><br>
        <!--        --><?php //endif; ?>
        <span style="float:right;"><?php echo $shortcode_string_link; ?>&nbsp;</span><div style="clear:both;">

        <?php
        $pages = get_posts([
            'numberposts' => -1,
            'orderby'     => 'post_title',
            'order'       => 'ASC',
            'post_type'   => 'page',
            'suppress_filters' => true
        ]);
        $param_webinar_id = WebinarignitionManager::is_webinar_public($webinar_data) ? $webinar_data->id : $webinar_data->hash_id;
        $default_page_id  = WebinarignitionManager::get_webinar_post_id( $webinar_data->id);

        if( !empty($pages) ) {

            $selected_page_id    = isset($webinar_data->custom_registration_page) ? (array) $webinar_data->custom_registration_page : array();
            $selected_page_id    = is_array( $selected_page_id ) ? array_unique( array_filter($selected_page_id) ) : $selected_page_id;
            $selected_page_links = array();

            $default_registration_page = empty( $webinar_data->default_registration_page ) ? $default_page_id : intval( $webinar_data->default_registration_page );

            $selected = '';
            $i_class  = '';

            if( !empty( $default_page_id ) && ( empty( $webinar_data->default_registration_page ) || $webinar_data->default_registration_page == $default_page_id ) && !in_array( $default_page_id, $selected_page_id ) ) {

                if( $default_registration_page == $default_page_id ) {
                    $selected   = 'checked';
                    $i_class  = 'icon-circle';
                }

                $selected_page_links[] = sprintf('<div class="wi_webinar_preview_box wi_webinar_preview_box_%d %s"><input data-page_url="%s" name="default_registration_page" class="default_registration_page" value="%d" type="radio" %s><i class="icon %s"></i>%s<a href="%s" target="_blank" class="wi_page_link"><i class="icon-external-link"></i> %s</a></div>', $default_page_id, $selected, get_permalink($default_page_id), $default_page_id, $selected, $i_class, get_the_title($default_page_id), get_permalink($default_page_id), esc_html__('Preview', 'webinarignition') );
            }

	        if( $default_registration_page !== $default_page_id && !in_array( $default_registration_page, $selected_page_id ) ) {
		        $default_registration_page = reset( $selected_page_id );
	        }

	        foreach( (array) $selected_page_id as $page_id ) {

                if( $default_registration_page == $page_id ) {
                    $selected = 'checked';
                    $i_class  = 'icon-circle';
                } else {
                    $selected = '';
                    $i_class  = 'icon-circle-blank';
                }

                $selected_page_links[] = sprintf('<div class="wi_webinar_preview_box wi_webinar_preview_box_%d %s"><input data-page_url="%s" name="default_registration_page" class="default_registration_page" value="%d" type="radio" %s><i class="icon %s"></i>%s<a href="%s" target="_blank" class="wi_page_link"><i class="icon-external-link"></i> %s</a></div>', $page_id, $selected, get_permalink($page_id), $page_id, $selected, $i_class, get_the_title($page_id), get_permalink($page_id), esc_html__('Preview', 'webinarignition') );
            }

            if( !in_array($default_registration_page, $selected_page_id ) ) {
                $selected_page_id[] = $default_registration_page;
            }

            if( !empty( $selected_page_links ) ): ?>
                <div class="wi_selected_pages_links_container">
                    <p>
                        <?php echo esc_html__('If you are using multiple registration pages, select the default registration page below.', 'webinarignition'); ?>
                    </p>
                    <div class="wi_selected_pages_links">
                        <?php echo implode('', $selected_page_links ); ?>
                    </div>
                </div>
            <?php endif; ?>
                <p><?php esc_html_e('If you are using the shortcode, select the page where you are using it. The default registration page will be replaced!','webinarignition'); ?></p>
             <select id="custom_registration_page_1" name="custom_registration_page[]" class="inputField multiSelectField elem" multiple>
                 <option value=""><?php esc_html_e('-- Select Custom Registration Page --'); ?></option>
                 <?php foreach ($pages as $page):
                    $page_url = add_query_arg('webinar', $param_webinar_id, get_the_permalink($page->ID));
                    $page_thank_you_url = add_query_arg($webinar_data->paid_code, '', get_the_permalink($page->ID));
                    $data_params = [];
                    foreach (['url', 'public-url', 'protected-url'] as $data_type) {
                        $data_params[] = 'data-' . $data_type . '="' . $page_url . '"';
                    }

                    $data_params[] = 'data-paid-thank-you-url="' . $page_thank_you_url . '"';
                 ?>
                     <option value="<?php echo $page->ID; ?>" <?php echo implode(' ', $data_params); ?> <?php selected( in_array($page->ID, $selected_page_id), true); ?>><?php echo $page->ID; ?> - <?php echo $page->post_title; ?></option>
                 <?php endforeach; ?>
             </select>
            <?php
        }
        ?>

        </div>
    </div>

</div>


</div>

<br clear="left"/>

<!-- NEW AREA END -->


<div style="">
    <!--
                                <div class="statsDashbord" style="display:none;" >

                                                <div class="statsDashBlock">
                                                                <div class="statsDashBlockNumber"><?php
    if ($data->total_lp == "") {
        echo "0";
    } else {
        echo $data->total_lp;
    }
    ?></div>
                                                                <div class="statsDashBlockTag">landing page</div>
                                                </div>

                                                <div class="statsDashBlock">
                                                                <div class="statsDashBlockNumber"><?php
    if ($data->total_ty == "") {
        echo "0";
    } else {
        echo $data->total_ty;
    }
    ?></div>
                                                                <div class="statsDashBlockTag">thank you page</div>
                                                </div>

                                                <div class="statsDashBlock">
                                                                <div class="statsDashBlockNumber"><?php
    if ($data->total_live == "") {
        echo "0";
    } else {
        echo $data->total_live;
    }
    ?></div>
                                                                <div class="statsDashBlockTag">live webinar</div>
                                                </div>

                                                <div class="statsDashBlock">
                                                                <div class="statsDashBlockNumber"><?php
    if ($data->total_replay == "") {
        echo "0";
    } else {
        echo $data->total_replay;
    }
    ?></div>
                                                                <div class="statsDashBlockTag">webinar replay</div>
                                                </div>

                                                <br clear="left" />

                                </div>

                                <br clear="left" /> -->

    <div class="editableSectionHeading2" style="display:none;">

        <?php
        // Display Leads For This App
        $get_input     = filter_input_array(INPUT_GET);
        $getVersion = "webinarignition_leads";
        $table_db_name = $wpdb->prefix . $getVersion;

        $ID = $get_input['id'];

        $leads = $wpdb->get_results("SELECT * FROM $table_db_name WHERE app_id = '$ID' ", OBJECT);
        $leads2 = $wpdb->get_results("SELECT * FROM $table_db_name WHERE app_id = '$ID' ", ARRAY_A);

        $totalLeads = count($leads2);
        ?>

        <div class="editableSectionTitle">
            <i class="icon-user"></i>
            <?php  esc_html_e( 'Manage Your Leads', 'webinarignition' ); ?> ( <?php  esc_html_e( 'Total Leads:', 'webinarignition' ); ?> <?php echo $totalLeads; ?> )
        </div>

        <div class="editableSectionToggle">
            <!-- <i class="toggleIcon  icon-chevron-down "></i> -->
        </div>

        <br clear="all"/>

    </div>

    <div class="leads" style="clear: both; display:none;">
        <table id="leads" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th><i class="icon-user" style="margin-right: 5px;"></i><?php  esc_html_e( 'Full Name', 'webinarignition' ); ?></th>
                    <th><i class="icon-envelope-alt" style="margin-right: 5px;"></i><?php  esc_html_e( 'Email Address', 'webinarignition' ); ?></th>
                    <th><i class="icon-mobile-phone" style="margin-right: 5px;"></i><?php  esc_html_e( 'Phone', 'webinarignition' ); ?></th>
                    <th><i class="icon-calendar" style="margin-right: 5px;"></i><?php  esc_html_e( 'Sign Up Date', 'webinarignition' ); ?></th>
                    <th width="70"><i class="icon-trash" style="margin-right: 5px;"></i> <?php  esc_html_e( 'Delete', 'webinarignition' ); ?></th>
                </tr>
            </thead>
            <tbody>

                <?php
                foreach ($leads as $leads) {
                    ?>
                    <tr id="table_lead_<?php echo $leads->ID; ?>">
                        <td><?php echo $leads->name; ?></td>
                        <td><?php echo $leads->email; ?></td>
                        <td><?php echo $leads->phone; ?></td>
                        <td><?php echo $leads->created; ?></td>
                        <td>
                            <center><i class="icon-remove delete_lead" lead_id="<?php echo $leads->ID; ?>"></i></center>
                        </td>
                    </tr>
                <?php
                }
                ?>

            </tbody>
        </table>
    </div>

</div>

<br clear="all"/>

<div style="border-top: 1px dotted #e2e2e2; padding-top: 15px; margin-top: 25px; ">
    <span style="float: right;" id="deleteCampaign"
          data-nonce="<?php echo wp_create_nonce('wi_delete_campaign_' . $get_input['id']); ?>" class="grey-btn"><i
            class="icon-trash" style="margin-right: 5px;"></i> <?php  esc_html_e( 'Delete This Campaign', 'webinarignition' ); ?></span>
    <span style="float: left;" id="exportCampaign" class="grey-btn"><a
            href="#TB_inline?width=637&height=550&inlineId=export-campaign" class="thickbox"><i class="icon-magic"
                                                                                                style="margin-right: 5px;"></i>
            <?php  esc_html_e( 'Export Campaign', 'webinarignition' ); ?></a></span>
    <span style="float: right; margin-right: 15px;" id="resetStats2" class="grey-btn"><i class="icon-bar-chart"
                                                                                         style="margin-right: 5px;"></i> <a
            href="#" id="resetStats"><?php  esc_html_e( 'Reset View Stats', 'webinarignition' ); ?></a></span>
    <br clear="right"/>
</div>

<!-- Export Modal -->
<?php add_thickbox(); ?>
<div id="export-campaign" style="display:none;">
    <p style="font-weight: bold; font-size: 18px;"><?php  esc_html_e( 'Export Campaign Code:', 'webinarignition' ); ?></p>

    <p style="margin-top:-25px;"><?php  esc_html_e( 'Copy & paste this code to the target website: Open the WebinarIgniton Dashboard, click the "Create a new webinar" button, select "Import campaign" from the drop-down menu, paste the code and click "Create new webinar".', 'webinarignition' ); ?></p>
    <textarea onclick="this.select()"
              style="width:100%; height:250px;"><?php echo base64_encode(serialize($webinar_data)); ?></textarea>
</div>


</div>

