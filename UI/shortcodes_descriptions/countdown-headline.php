<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $is_list
 */

$webinarId = $webinar_data->id;

if (!empty($is_list)) {
    ?><hr><p>
        <?php echo __('Countdown Headline', 'webinarignition'); ?>
    </p>

    <div style="border: 1px solid #ddd; background-color: #eee;padding: 5px 10px;margin-bottom: 10px;"><?php
        webinarignition_display(
            $webinar_data->cd_headline, '<h4 class="subheader">'.__( "You Are Viewing A Webinar That Is Not Yet Live", "webinarignition" ).' - <b>'.__( "We Go Live Soon!", "webinarignition" ).'</b></h4>');
        ?></div><?php
} else {
    ?><?php
}
?>

<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="countdown_headline"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>
