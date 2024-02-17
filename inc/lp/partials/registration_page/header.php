<?php defined( 'ABSPATH' ) || exit;
/**
 * Registration page header template
 *
 * @var $template_number
 * @var $webinarId
 * @var $webinar_data
 * @var $is_webinar_available
 * @var $assets
 * @var $custom_lp_css_path
 */
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- META INFO -->
    <title>
        <?php webinarignition_display( $webinar_data->lp_metashare_title, __( "Amazing Webinar", "webinarignition") ); ?>
    </title>

    <meta name="description"
          content="<?php webinarignition_display(
              $webinar_data->lp_metashare_desc,
              __( "Join this amazing webinar, and discover industry trade secrets!", "webinarignition")
          ); ?>">

    <meta property="og:description"
          content="<?php webinarignition_display(
                  $webinar_data->lp_metashare_desc,
                  __( "Join this amazing webinar, and discover industry trade secrets!", "webinarignition")
          ); ?>">

    <?php if ( !empty($webinar_data->ty_share_image)  ){
        ?>
        <meta property="og:image" content="<?php webinarignition_display(
                $webinar_data->ty_share_image,
                ""
        ); ?>"/>
        <?php
    } ?>

    <script>
        window.wiRegJS  = {};
        window.wiRegJS.ajax_nonce = '<?php echo wp_create_nonce("webinarignition_ajax_nonce"); ?>'
    </script>

    <?php
    if( $webinar_data->wp_head_footer === 'enabled'  ){
        wp_head();
    } else {
        ?>
        <!-- Bootstrap -->
        <link href="<?php echo $assets; ?>css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo $assets; ?>css/foundation.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $assets; ?>js-libs/css/intlTelInput.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $assets; ?>css/main.css" rel="stylesheet" type="text/css"/>
        <?php
        if ('02' === $template_number || '03' === $template_number) {
            ?><link href="<?php echo $assets; ?>css/ss.css" rel="stylesheet" type="text/css"/><?php
        }
        ?>
        <link href="<?php echo $assets; ?>css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $assets; ?>css/utils.css" rel="stylesheet" type="text/css"/>

        <script type="text/javascript" src="<?php echo $assets; ?>js/moment.min.js"></script>
        <!--  fix :: utils  -->
        <script type="text/javascript" src="<?php echo $assets; ?>js/utils.js"></script>
        <script src="<?php echo $assets; ?>js/jquery.js"></script>

        <?php
        //include videojs resources when registration CTA type is video, and video URL is set
        if ( (empty($webinar_data->lp_cta_type) || $webinar_data->lp_cta_type == 'video') && !empty( $webinar_data->lp_cta_video_url ) ): ?>
        <link href="<?php echo $assets; ?>video-js-7.20.3/video-js.min.css" rel="stylesheet" type="text/css"/>
        <?php endif; ?>

        <?php
    }
    ?>

    <?php include( $custom_lp_css_path ); ?>
    <!-- CUSTOM CSS -->
    <style type="text/css">
        <?php webinarignition_display($webinar_data->custom_lp_css, ""); ?>
    </style>
    <!-- CUSTOM JS -->
    <?php webinarignition_display_custom_js($webinar_data, 'custom_lp_js'); ?>
</head>

<body id="webinarignition" class="<?php echo 'wi_registration registration-tpl-'.$template_number.' wi-version-' .WEBINARIGNITION_VERSION; ?>">
