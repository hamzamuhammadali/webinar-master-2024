<?php defined( 'ABSPATH' ) || exit; ?>
<!DOCTYPE html>
<html>
<head>

    <!-- META INFO -->
    <title><?php webinarignition_display( $webinar_data->webinar_desc, __( "Amazing Webinar Training 101", "webinarignition") ); ?></title>
    <meta name="description" content="<?php webinarignition_display( $webinar_data->webinar_desc,
              __( "Join this amazing webinar May the 4th, and discover industry trade secrets!", "webinarignition") ); ?>">
    <!-- SOCIAL INFO -->
    <meta property="og:title" content="<?php webinarignition_display( $webinar_data->webinar_desc, __( "Amazing Webinar Training 101", "webinarignition") ); ?>"/>
    <meta property="og:image" content="<?php webinarignition_display( $webinar_data->ty_share_image, "" ); ?>"/>

    <?php include( "css/webinar_css.php" ); ?>

    <!-- CUSTOM JS -->
    <script>
        <?php webinarignition_display($webinar_data->custom_replay_js, ""); ?>
    </script>
    <!-- CUSTOM CSS -->
    <style>
        <?php webinarignition_display($webinar_data->custom_replay_css, ""); ?>
    </style>

    <?php wp_head(); ?>

</head>
<body class="webinar_closed">


<!-- TOP AREA -->
<div class="topArea">
    <div class="bannerTop">
        <?php
        if ($webinar_data->webinar_banner_image == "") {
            // echo "NONE";
        } else {
            echo "<img src='$webinar_data->webinar_banner_image' />";
        }

        if( isset( $_GET['preview'] ) && current_user_can('edit_posts') ) {
            update_option('wi_lead_watch_time_[lead_id]', 0);
        }
        ?>
    </div>
</div>

<!-- Main Area -->
<div class="mainWrapper">

    <!-- WEBINAR WRAPPER -->
    <div class="webinarWrapper container">
        <!-- CLOSED WEBINAR -->
        <div id="closed" class="webinarExtraBlock2">
            <?php
            $watch_time_limit_string = __('45 minutes', 'webinarignition');
            $statusCheck = WebinarignitionLicense::get_license_level();
            if($statusCheck->name == 'ultimate_powerup_tier1a') {
                $watch_time_limit_string = __('2 Hours', 'webinarignition');
            }

            webinarignition_display( $webinar_data->replay_closed, "<h1>". sprintf(esc_html__('Webinar watch time is limited to %s only.', 'webinarignition'), $watch_time_limit_string) . "</h1>" ); ?>
            <br>
            <?php webinarignition_display( $webinar_data->replay_closed, "<h3>". esc_html__('Contact site administrator for more details.', 'webinarignition') . "</h3>" ); ?>
        </div>

    </div>

</div>

<?php require_once WEBINARIGNITION_PATH .  'inc/lp/partials/powered_by.php'; ?>


<div id="fb-root"></div>
<?php require_once WEBINARIGNITION_PATH .  'inc/lp/partials/fb_share_js.php'; ?>
<?php require_once WEBINARIGNITION_PATH .  'inc/lp/partials/tw_share_js.php'; ?>


<!--Extra code-->
<?php echo $webinar_data->footer_code; ?>

<?php wp_footer(); ?>
</body>
</html>
