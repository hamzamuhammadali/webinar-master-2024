<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $is_list
 */

$webinarId = $webinar_data->id;

if (!empty($is_list)) {
    ?>
    <p>
    <?php echo __( "Main Headline and Ticket Sub Headline", 'webinarignition' ) ?>:
    </p>

    <div style="border: 1px solid #ddd; background-color: #eee;padding: 5px 10px;margin-bottom: 10px;"><h2 style="margin: 0 0 10px 0"><?php webinarignition_display( $webinar_data->ty_ticket_headline, __( "Congrats - You Are All Signed Up!", "webinarignition") ); ?></h2>
    <h3 style="margin: 0"><?php webinarignition_display( $webinar_data->ty_ticket_subheadline, __( "Below is all the information you need for the webinar...", "webinarignition") ) ?></h3></div><?php
} else {
    ?><?php
}
?>

<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="ty_headline"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>
<?php
if (!empty($is_list)) {
    ?>
    <hr><?php
} else {
    ?><?php
}
?>
