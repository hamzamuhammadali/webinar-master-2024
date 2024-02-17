<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $is_list
 */

$webinarId = $webinar_data->id;

if (!empty($is_list)) {
    ?><h4><?php echo __('Selected Date / Time', 'webinarignition'); ?></h4><?php
} else {
    ?><?php
}
if (!empty($is_list)) {
    ?><p><?php echo __('Selected Date / Time section', 'webinarignition'); ?></p><?php
} else {
    ?><?php
}
?>

<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="ty_ticket_date"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>

<?php
if (!empty($is_list)) {
    ?><p><?php echo __('InlineSelected Date', 'webinarignition'); ?></p><?php
} else {
    ?><?php
}
?>

<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="ty_date_inline"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>

<?php
if (!empty($is_list)) {
    ?><p><?php echo __('InlineSelected Time', 'webinarignition'); ?></p><?php
} else {
    ?><?php
}
?>

<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="ty_time_inline"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>
<?php
if ($webinar_data->webinar_date !== 'AUTO') {
    if (!empty($is_list)) {
        ?><p><?php echo __('InlineSelected Timezone (works only for live webinars)', 'webinarignition'); ?></p><?php
    } else {
        ?><?php
    }
    ?>
    <p class="code-example">
        <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="ty_timezone_inline"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
    </p>
    <?php
}

if (!empty($is_list)) {
    ?>
    <hr><?php
} else {
    ?><?php
}
?>
