<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $is_list
 */

$webinarId = $webinar_data->id;

if (!empty($is_list)) {
    ?><?php
} else {
    ?><?php
}
?>

<h4>
    <?php echo __('Webinar info', 'webinarignition'); ?>
</h4>

<p>
    <?php echo __('Webinar Title', 'webinarignition'); ?>: <strong><?php echo !empty( $webinar_data->webinar_desc ) ? esc_attr($webinar_data->webinar_desc) : '';?></strong>
</p>
<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="global_webinar_title"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>

<hr>

<p>
    <?php echo __('Webinar Host Name', 'webinarignition'); ?>: <strong><?php echo !empty( $webinar_data->webinar_host ) ? $webinar_data->webinar_host : '';?></strong>
</p>
<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="global_host_name"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>

<hr>

<p>
    <?php echo __('Webinar Giveaway section content', 'webinarignition'); ?>:
</p>
<div style="border: 1px solid #ddd; background-color: #eee;padding: 5px 10px;margin-bottom: 10px;">
    <?php webinarignition_display( $webinar_data->webinar_giveaway, "<h4>".__( 'Your Awesome Free Gift</h4><p>You can download this awesome report made you...', 'webinarignition')."</p><p>[ ".__( 'DOWNLOAD HERE', 'webinarignition')." ]</p>" ); ?>
</div>
<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="global_webinar_giveaway"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>

<hr>

<h4>
    <?php echo __('Lead info', 'webinarignition'); ?>
</h4>

<p>
    <?php echo __('Lead info could be only get after registration. So you should not use shortcodes below it on registration pages.', 'webinarignition'); ?>
</p>

<p>
    <?php echo __('Lead Name', 'webinarignition'); ?>: <strong><?php echo __('John Doe', 'webinarignition'); ?></strong>
</p>
<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="global_lead_name"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>

<hr>

<p>
    <?php echo __('Lead Email', 'webinarignition'); ?>: <strong><?php echo __('john.doe@maildomain.com', 'webinarignition'); ?></strong>
</p>

<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="global_lead_email"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>