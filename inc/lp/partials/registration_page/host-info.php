<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 */
$lp_webinar_host_block = $webinar_data->lp_webinar_host_block;
$uid = wp_unique_id( $prefix = 'hostInfoBlock-' );

if ("hide" !== $lp_webinar_host_block) {
    ?>
    <div class="hostInfoBlock <?php echo $uid ?>">
        <div class="hostInfoPhoto">
            <img src="<?php webinarignition_display( $webinar_data->lp_host_image,
                WEBINARIGNITION_URL . "images/generic-headshot-male.jpg" ); ?>"/>
        </div>

        <div class="hostInfoCopy">
            <?php
            webinarignition_display( $webinar_data->lp_host_info,
               __( "It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software...", "webinarignition") . '<br><br><b>'. __( "Your Name Here", "webinarignition").'</b>'
            );
            ?>
        </div>
    </div>
    <?php
}
?>
