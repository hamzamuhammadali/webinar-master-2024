<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $is_list
 */

$webinarId = $webinar_data->id;

if (!empty($is_list)) {
    ?><hr><p>
    <?php echo __('Sales Copy Headline', 'webinarignition'); ?>: <strong><?php webinarignition_display( $webinar_data->lp_sales_headline,  __('What You Will Learn On The Webinar...', 'webinarignition')); ?></strong>
    </p><?php
} else {
    ?><?php
}
?>
<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="reg_sales_headline"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>
