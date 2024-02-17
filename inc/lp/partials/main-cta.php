<?php
/**
 * @var $webinar_data
 */

$cta_position_default = 'outer';
$cta_position_allowed = 'outer';

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
        #orderBTN {
            max-width: <?php echo $auto_action_max_width ?>px !important;
            margin: auto;
        }
    </style>
    <?php
}
include WEBINARIGNITION_PATH . "inc/lp/partials/additional-cta.php";

?>