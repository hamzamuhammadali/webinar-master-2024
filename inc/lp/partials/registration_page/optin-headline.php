<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $input_get
 * @var $webinar_data
 */

$uid = wp_unique_id( $prefix = 'optinHeadline-' );
?>
<div class="optinHeadline wiOptinHeadline <?php echo $uid; ?>">
        <?php

        ob_start();
        
        if( (!empty( $webinar_data->latecomer )) && (!empty( $webinar_data->latecomer_registration_copy )) ){ ?>
        <div id="latecomer_copy"><?php echo $webinar_data->latecomer_registration_copy ?></div>
        <?php } else { ?>
        <div class="optinHeadline1 wiOptinHeadline1"><?php echo __('RESERVE YOUR SPOT!', 'webinarignition') ?></div>
        <div class="optinHeadline2 wiOptinHeadline2"><?php echo __('WEBINAR REGISTRATION', 'webinarignition') ?></div>
        <?php }

        if (isset($input_get['payment']) && $input_get['payment'] === 'success') {
            ?>
            <div class="optinHeadline2 wiOptinHeadline2"><?php echo __('Payment Success!', 'webinarignition') ?></div>
            <p>
                <?php echo __('Please finalize your registration by filling out the form below:', 'webinarignition'); ?>
            </p>
            <?php
        }

        $displayReserveSpot = ob_get_clean();

        webinarignition_display(
            $webinar_data->lp_optin_headline,
            $displayReserveSpot
        );
        ?>
    </div>