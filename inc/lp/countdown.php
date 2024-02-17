<?php
/**
 * @var $webinar_data
 */
defined( 'ABSPATH' ) || exit;
?><!DOCTYPE html>
<html>
<head>
    <title><?php webinarignition_display($webinar_data->lp_metashare_title, __( "Amazing Webinar", "webinarignition") ); ?></title>
    <meta name="description" content="<?php webinarignition_display($webinar_data->lp_metashare_desc, __( "Join this amazing webinar, and discover industry trade secrets!", "webinarignition") ); ?>">

    <?php
    if ($webinar_data->ty_share_image !== "") {
        ?>
        <meta property="og:image" content="<?php webinarignition_display($webinar_data->ty_share_image, ""); ?>"/>
        <?php
    }
    ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <?php wp_head(); ?>
</head>
<body class="webinar_page" id="webinarignition">

<!-- TOP AREA -->
<?php webinarignition_get_countdown_main_headline($webinar_data, true); ?>

<!-- Main Area -->
<div class="mainWrapper countdown_page">
    <!-- HEADLINE AREA -->
    <?php webinarignition_get_countdown_headline($webinar_data, true); ?>

    <!-- COUNTDOWN AREA -->
    <?php webinarignition_get_countdown_counter($webinar_data, true); ?>

    <!-- UNDER COUNTDOWN AREA -->
    <?php webinarignition_get_countdown_signup($webinar_data, true); ?>

</div>

<?php require_once WEBINARIGNITION_PATH .  'inc/lp/partials/powered_by.php'; ?>

<!--Extra code-->
<?php echo $webinar_data->footer_code; ?>
<?php wp_footer(); ?>
</body>
</html>
