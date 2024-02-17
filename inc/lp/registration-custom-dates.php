<?php defined( 'ABSPATH' ) || exit; ?>
<div class="eventDate" style="border:none; margin:0px; padding: 0 0 10px 0;">
    <span class="autoTitle">
        <?php webinarignition_display(
            $webinar_data->auto_translate_headline1,
            __("Choose a Date To Attend... ", "webinarignition")
        ); ?>
    </span>
    <span class="autoSubTitle">
        <?php webinarignition_display(
            $webinar_data->auto_translate_subheadline1,
            __("Select a date that best suits your schedule...", "webinarignition")
        ); ?>
    </span>

    <select id="webinar_start_date">
        <option value="none"><?php _e( 'Loading Times...', 'webinarignition' ); ?></option>
    </select>

    <div class="autoSep" <?= $webinar_data->auto_today == "yes" ? 'style="display: none;"' : '' ?> ></div>
    <div id="webinarTime" <?= $webinar_data->auto_today == "yes" ? 'style="display: none;"' : '' ?> >
        <span class="autoTitle"><?php webinarignition_display($webinar_data->auto_translate_headline2, __("What Time Is Best For You?", "webinarignition") ) ?></span>
        <select id="webinar_start_time">
            <?php
            if ( $webinar_data->auto_time_1 !== "no" ) {
                echo "<option value='{$webinar_data->auto_time_1}'>" . webinarignition_auto_custom_time($webinar_data, $webinar_data->auto_time_1) . "</option>";
            }

            if ( $webinar_data->auto_time_2 !== "no" ) {
                echo "<option value='{$webinar_data->auto_time_2}'>" . webinarignition_auto_custom_time($webinar_data, $webinar_data->auto_time_2) . "</option>";
            }

            if ( $webinar_data->auto_time_3 !== "no" ) {
                echo "<option value='{$webinar_data->auto_time_3}'>" . webinarignition_auto_custom_time($webinar_data, $webinar_data->auto_time_3) . "</option>";
            }
            ?>
        </select>
    </div>
    <input type="hidden" id="timezone_user" value="<?= $webinar_data->auto_timezone_type === 'fixed' ? $webinar_data->auto_timezone_custom : '' ?>">
    <input type="hidden" id="today_date" value="<?php echo date( 'Y-m-d' ); ?>">
</div>
