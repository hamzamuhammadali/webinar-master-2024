<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $is_list
 */

$webinarId = $webinar_data->id;

if (!empty($is_list)) {
    ?><hr><p>
        <?php echo __('Main Sales Copy', 'webinarignition'); ?>
    </p>

    <div style="border: 1px solid #ddd; background-color: #eee;padding: 5px 10px;margin-bottom: 10px;">
        <?php
        webinarignition_display(
            do_shortcode( $webinar_data->lp_sales_copy ),
            '<p>'.__('Your Amazing sales copy for your webinar would show up here...', 'webinarignition').'</p>'
        );
        ?>
    </div>
    <?php
} else {
    ?><?php
}
?>

<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="reg_sales_copy"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>
