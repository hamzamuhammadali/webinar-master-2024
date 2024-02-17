<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 */

if ( $webinar_data->webinar_date == "AUTO" ) {
    ?>
    <div class="webinarVideoCTA<?php echo $webinar_data->auto_action == "time" ? "" : " webinarVideoCTAActive"; ?>">
        <div class="ctaArea">
            <?php include WEBINARIGNITION_PATH . "inc/lp/partials/auto-cta-area.php"; ?>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="webinarVideoCTA">
        <div class="ctaArea">
            <div class="timedUnderArea" id="orderBTN" style="display: none;">
                <div id="orderBTNCopy"></div>
                <div id="orderBTNArea"></div>
            </div>
        </div>
    </div>
    <?php
}
?>
