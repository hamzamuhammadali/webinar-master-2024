<?php
/**
 * @var $webinar_data
 */

$cta_position_default = 'outer';
$cta_position_allowed = 'overlay';

if (!empty($webinar_data->cta_position) ) {
    $cta_position_default = $webinar_data->cta_position;
}

$cta_border_desktop = '';
$cta_border_mobile = '';

$show_main_cta = false;

if ($webinar_data->auto_action !== "time" && $cta_position_default === $cta_position_allowed) {
    $show_main_cta = true;
}

if (WebinarignitionPowerups::is_multiple_cta_enabled($webinar_data)) {
    if (isset($webinar_data->cta_border_desktop) && 'no' === $webinar_data->cta_border_desktop) {
        $cta_border_desktop = ' cta_border_hide_desktop';
    }

    if (isset($webinar_data->cta_border_mobile) && 'no' === $webinar_data->cta_border_mobile) {
        $cta_border_mobile = ' cta_border_hide_mobile';
    }
}

if ($webinar_data->auto_action !== "time" && !empty($webinar_data->auto_action_max_width)) {
    $auto_action_max_width = $webinar_data->auto_action_max_width;
    ?>
    <style>
        #overlayOrderBTN {
            max-width: <?php echo $auto_action_max_width ?>px !important;
            margin: auto;
        }
    </style>
    <?php
}
?>
<?php
if ($show_main_cta) {
?>
<div class="timedUnderArea <?php echo $cta_border_desktop . $cta_border_mobile; ?> timedUnderAreaOverlay wi-cta-tab" id="wi-cta-default-overlay" style="display:none" data-cta-index="default">
    <div id="overlayOrderBTNCopy" style="color:#212529; font-size:initial;">
        <?php
        if ($show_main_cta) {
	        include WEBINARIGNITION_PATH . 'inc/lp/partials/print_cta.php';
        }
        ?>
    </div>

    <div id="overlayOrderBTNArea">
        <?php if ( $show_main_cta ) {
            if ($webinar_data->auto_action_url != "") {
                $btn_id = wp_unique_id( 'orderBTN_' );
                $bg_color = empty( $webinar_data->replay_order_color ) ? '#6BBA40' : $webinar_data->replay_order_color;
                $text_color = webinarignition_get_text_color_from_bg_color($bg_color);
                $hover_color = webinarignition_get_hover_color_from_bg_color($bg_color);
                $text_hover_color = webinarignition_get_text_color_from_bg_color($hover_color);
                ?>
                <style>
                    #<?php echo $btn_id ?> {
                        background-color: <?php echo $bg_color ?>;
                        color: <?php echo $text_color ?>;
                        white-space: normal;
                    }
                    #<?php echo $btn_id ?>:hover {
                        background-color: <?php echo $hover_color ?>;
                        color: <?php echo $text_hover_color ?>;
                    }
                </style>
                <a
                        href="<?php webinarignition_display( $webinar_data->auto_action_url, "#" ); ?>"
                        id="<?php echo $btn_id ?>"
                        target="_blank"
                        class="large radius button success addedArrow replayOrder wiButton wiButton-lg wiButton-block"
                        style="border: 1px solid rgba(0,0,0,0.20); width: 100%; margin-top:0px;"
                >
                    <?php webinarignition_display( $webinar_data->auto_action_btn_copy, __("Click Here To Grab Your Copy Now", "webinarignition") ); ?>
                </a>
                <?php
            }
        } ?>
    </div>

</div>
<?php
}
?>