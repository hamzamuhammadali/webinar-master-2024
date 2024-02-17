<?php
/**
 * @var $webinar_data
 */

$webinar_cta_by_position = WebinarignitionManager::get_webinar_cta_by_position($webinar_data);

if (
    empty($webinar_cta_by_position)
    || empty($webinar_cta_by_position['is_time'])
    || empty($webinar_cta_by_position['outer'])
) {
    $additional_autoactions = [];
} else {
    $additional_autoactions = $webinar_cta_by_position['outer'];
}

$cta_border_desktop = '';
$cta_border_mobile = '';

if (WebinarignitionPowerups::is_multiple_cta_enabled($webinar_data)) {
	if ( isset( $webinar_data->cta_border_desktop ) && 'no' === $webinar_data->cta_border_desktop ) {
		$cta_border_desktop = ' cta_border_hide_desktop';
	}

	if ( isset( $webinar_data->cta_border_mobile ) && 'no' === $webinar_data->cta_border_mobile ) {
		$cta_border_mobile = ' cta_border_hide_mobile';
	}
}

foreach ($additional_autoactions as $index => $additional_autoaction) {
    $cta_position = $cta_position_default;

    if (!empty($additional_autoaction['cta_position'])) {
        $cta_position = $additional_autoaction['cta_position'];
    }

    if ($cta_position !== $cta_position_allowed) {
        continue;
    }

    $max_width = '';

    if (!empty($additional_autoaction['auto_action_max_width'])) {
        $max_width = $additional_autoaction['auto_action_max_width'] . 'px';
    }

    $auto_action_title = __('Click here', 'webinarignition');

    if (!empty($additional_autoaction['auto_action_title'])) {
        $auto_action_title = $additional_autoaction['auto_action_title'];
    } elseif ($additional_autoaction['auto_action_btn_copy']) {
        $auto_action_title = $additional_autoaction['auto_action_btn_copy'];
    }
    ?>
    <div id="wi-cta-<?php echo $index ?>-tab" class="timedUnderArea <?php echo $cta_border_desktop . $cta_border_mobile; ?> wi-cta-tab" style="display: inline-block; left:100vw;">
        <div id="orderBTNCopy_<?php echo $index ?>">
	        <?php
	        include WEBINARIGNITION_PATH . 'inc/lp/partials/print_cta.php';
	        ?>
        </div>

        <div id="orderBTNArea_<?php echo $index ?>">
            <?php if ( $additional_autoaction['auto_action_url'] != "" ) :
                $btn_id = wp_unique_id( 'orderBTN_' );
                $bg_color = empty( $additional_autoaction['replay_order_color'] ) ? '#6BBA40' : $additional_autoaction['replay_order_color'];
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
                <a href="<?php webinarignition_display( $additional_autoaction['auto_action_url'], "#" ); ?>"
                   id="<?php echo $btn_id ?>"
                   target="_blank"
                   class="large radius button success addedArrow replayOrder wiButton wiButton-lg wiButton-block"
                   style="border: 1px solid rgba(0,0,0,0.20);">
                    <?php webinarignition_display( $additional_autoaction['auto_action_btn_copy'], __( "Click Here To Grab Your Copy Now", "webinarignition") ); ?>
                </a>
            <?php endif ?>
        </div>
    </div>
    <?php
}

