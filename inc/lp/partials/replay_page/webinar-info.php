<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinarId
 * @var $webinar_data
 */

$uid = wp_unique_id( $prefix = 'webinarInfo-' );
?>

<div id="<?php echo $uid ?>" class="webinarInfo webinarInfo-<?php echo $webinarId; ?>">
    <div class="webinarTopBar">
        <i class="icon-exclamation-sign"></i>
        <?php webinarignition_display( $webinar_data->webinar_info_block_title, __( "Webinar Information", "webinarignition") ) ?>
    </div>

    <div class="webinarInner" style="padding-top:0px;">
        <div class="webinarTitleBar"><i class="icon-microphone"></i>
            <?php webinarignition_display( $webinar_data->webinar_info_block_host, __( "Your Host:", "webinarignition") ) ?>
        </div>
        <div class="webinarInfoCopy">
            <?php webinarignition_display( $webinar_data->webinar_host, __( 'Your Name Here', "webinarignition") ) ?>
        </div>
        <div class="webinarTitleBar webinarTitleBarAdded"><i class="icon-info"></i>
            <?php webinarignition_display( $webinar_data->webinar_info_block_desc, __(  "What You Will Learn:", "webinarignition") ) ?>
        </div>
        <div class="webinarInfoCopy">
            <?php webinarignition_display( $webinar_data->webinar_desc, __( 'In this webinar, you will learn everything you need to know about the webinar...', "webinarignition") ); ?>
        </div>
    </div>
</div>
