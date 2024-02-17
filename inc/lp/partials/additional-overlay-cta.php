<?php
/**
 * @var $webinar_data
 */

$webinar_cta_by_position = WebinarignitionManager::get_webinar_cta_by_position($webinar_data);

if (
    empty($webinar_cta_by_position)
    || empty($webinar_cta_by_position['is_time'])
    || empty($webinar_cta_by_position['overlay'])
) {
    $additional_autoactions = [];
} else {
    $additional_autoactions = $webinar_cta_by_position['overlay'];
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

    $auto_action_title = __('Click here', 'webinarignition');

    if (!empty($additional_autoaction['auto_action_title'])) {
        $auto_action_title = $additional_autoaction['auto_action_title'];
    } elseif ( isset($additional_autoaction['auto_action_btn_copy']) ) {
        $auto_action_title = $additional_autoaction['auto_action_btn_copy'];
    }

	$max_width = isset($additional_autoaction['auto_action_max_width']) ? absint($additional_autoaction['auto_action_max_width']) : 0;

	if ( !empty($max_width) ) {
		$max_width = "max-width:{$max_width}px; margin: 0 auto;";
	}

    ?>
    <div  id="wi-cta-<?php echo $index; ?>-tab" class="Test_Class_HOLA additional_autoaction_item timedUnderArea <?php echo $cta_border_desktop . $cta_border_mobile; ?> timedUnderAreaOverlay wi-cta-tab" style="display: inline-block; left:100vw; <?php echo $max_width; ?>">
        <div id="overlayOrderBTNCopy_<?php echo $index ?>">
            <?php
            include WEBINARIGNITION_PATH . 'inc/lp/partials/print_cta.php';
            ?>
        </div>

        <div id="overlayOrderBTNArea_<?php echo $index ?>">
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

