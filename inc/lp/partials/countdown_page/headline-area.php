<?php defined( 'ABSPATH' ) || exit;

/**
 * @var $webinar_data
 */
?>

<?php if ($webinar_data->webinar_date == "AUTO") { ?>
    <div class="headlineArea">
        <?php
        webinarignition_display(
            $webinar_data->cd_headline, '<h4 class="subheader">'.__( "You Are Viewing A Webinar That Is Not Yet Live", "webinarignition").' - <b>'.__( "We Go Live Soon!", "webinarignition").'</b></h4>');
        ?>
    </div>
<?php } else { ?>
    <div class="headlineArea">
        <?php
        webinarignition_display(
            $webinar_data->cd_headline, '<h4 class="subheader">'.__( "You Are Viewing A Webinar That Is Not Yet Live", "webinarignition").' - <b>'.__( "We Go Live Soon!", "webinarignition").'</b></h4>
				 <h2 style="margin-top: -10px; margin-bottom: 30px;" >'.__( "Webinar Starts", "webinarignition").' {DATE}</h2>');
        ?>
    </div>
<?php } ?>
