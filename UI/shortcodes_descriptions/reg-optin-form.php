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

<hr>
<h4>
    <?php echo __( "Registration form", 'webinarignition' ) ?>
</h4>
<p>
    <?php echo __( "This shortcode will show optin section with webinar dates selection and registration form together in one column", 'webinarignition' ) ?>
</p>

<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="reg_optin_section"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>

<p>
    <?php echo __( "If you need to show dates selection and registration form in different page blocks, you need to use both shortcodes below", 'webinarignition' ) ?>
</p>

<p><?php echo __( "Webinar dates selection", 'webinarignition' ) ?></p>
<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="reg_optin_dates"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>

<p><?php echo __( "Optin form fields", 'webinarignition' ) ?></p>
<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="reg_optin_form"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>

<p>
    <?php echo __( "If you want to show dates selection and optin fields without heading, you can use compact views below", 'webinarignition' ) ?>
</p>

<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="reg_optin_dates_compact"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>

<p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="reg_optin_form_compact"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
</p>


<?php
if ($webinar_data->webinar_date !== 'AUTO') {
    ?>
    <p class="code-example">
    <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="reg_date_inline"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
    </p>

    <p class="code-example">
        <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="reg_time_inline"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
    </p>

    <p class="code-example">
        <span class="code-example-value">[wi_webinar_block id="<?php echo $webinarId ?>" block="reg_timezone_inline"]</span><!--
    --><span class="code-example-copy"><?php echo __('Copy', 'webinarignition'); ?></span><!--
    --><span class="code-example-copied"><?php echo __('Copied. Input into your content!', 'webinarignition'); ?></span>
    </p>
    <?php
}
?>


