<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 */

 if ($webinar_data->webinar_date == "AUTO") {
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
        <?php
        if ($webinar_data->replay_timed_style == "" || $webinar_data->replay_timed_style == "button") {
            ?>
            <div class="timedUnderArea" id="orderBTN" style="display: none;">
                <a href="<?php webinarignition_display( $webinar_data->replay_order_url, "#" ); ?>" target="_blank"
                   class="radius success replayOrder large button wiButton wiButton-success wiButton-block wiButton-lg addedArrow"
                   style="background-color: <?php webinarignition_display( !empty($webinar_data->replay_order_color) ? $webinar_data->replay_order_color : '',
                       "#6BBA40" ); ?>; border: 1px solid rgba(0,0,0,0.20);"><?php
                    webinarignition_display(
                        !empty($webinar_data->replay_order_copy) ? $webinar_data->replay_order_copy : '',
                       __(  "Order Your Copy Now!!!", "webinarignition")
                    ); ?></a>
            </div>
            <?php
        } else {
            ?>
            <div class="timedUnderArea" id="orderBTN" style="display: none;">
                <?php webinarignition_display( $webinar_data->replay_order_html, __( "Custom HTML Here...", "webinarignition") ); ?>
            </div>
            <?php
        }
        ?>
        </div>
    </div>
    <?php
 }
 ?>
