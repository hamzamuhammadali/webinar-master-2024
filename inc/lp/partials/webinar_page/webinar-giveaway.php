<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinarId
 * @var $webinar_data
 */

$uid = wp_unique_id( $prefix = 'webinarGivaway-' );

if ($webinar_data->webinar_giveaway_toggle !== "hide") {
    ?>
    <div id="<?php echo $uid ?>" class="webinarQA webinarGivaway webinarGivaway-<?php echo $webinarId; ?>">
        <div class="webinarTopBar">
            <i class="icon-question-sign"></i> <?php webinarignition_display( $webinar_data->webinar_giveaway_title, __( "Your Special Gift:" , "webinarignition")); ?>
        </div>
        <div class="webinarInner">
            <?php webinarignition_get_webinar_giveaway_compact($webinar_data, true); ?>
        </div>
    </div>
    <?php
}
?>
