<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 */
?>

<?php if ($webinar_data->webinar_date !== "AUTO")  { ?>
    <div class="headlineArea countdownHeadlineArea" style="margin-top: 30px;">

        <?php
        webinarignition_display(
            $webinar_data->cd_headline2,
            '<h4 class="subheader">'.__( "Not Signed Up Yet For The Awesome Webinar?", "webinarignition").'</h4><h2 style="margin-top: -10px; margin-bottom: 30px;">'.__( "Signup To The Webinar", "webinarignition").'</h2>'
        );
        ?>

        <?php if ($webinar_data->cd_button_show !== 'hidden') {
            $signup_url = ( $webinar_data->cd_button == "custom" ) && !empty($webinar_data->cd_button_url) ? $webinar_data->cd_button_url : $webinar_data->webinar_permalink;
            ?>
            <a
                href="<?php echo $signup_url;  ?>"
                id="optinBTN"
                class="large button wiButton wiButton-success wiButton-block wiButton-lg addedArrow"
               style="border: 1px solid rgba(0,0,0,0.10); background-color: <?php webinarignition_display($webinar_data->cd_button_color, "#74BB00"); ?>;">
                <?php webinarignition_display($webinar_data->cd_button_copy, __( "Register For Webinar", "webinarignition")); ?>
            </a>
            <?php
        } ?>

    </div>

<?php } ?>
