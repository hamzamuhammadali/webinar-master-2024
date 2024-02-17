<?php defined( 'ABSPATH' ) || exit; ?>
<?php
/**
 * @var $webinar_data
 */
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php
        if ( $webinar_data->meta_site_title_ty == "" ) {
            webinarignition_display( $webinar_data->lp_metashare_title, __( "Amazing Webinar", "webinarignition") );
        } else {
            echo $webinar_data->meta_site_title_ty;
        }
        ?>
    </title>

    <meta name="description" content="<?php
    if ( $webinar_data->meta_desc_ty == "" ) {
        webinarignition_display( $webinar_data->lp_metashare_desc, __( "Join this amazing webinar, and discover industry trade secrets!", "webinarignition") );
    } else {
        echo $webinar_data->meta_desc_ty;
    }
    ?>">

    <?php if ( $webinar_data->ty_share_image != "" ) : ?>
        <meta property="og:image" content="<?php webinarignition_display( $webinar_data->ty_share_image, '' ) ?>"/>
    <?php endif ?>

    <?php wp_head(); ?>
    <?php require("css/ty_css.php"); ?>
</head>
<body class="page-thankyou_cp" id="webinarignition">

<!-- TOP AREA -->
<?php webinarignition_get_ty_banner( $webinar_data, true ); ?>

<!-- Main Area -->
<div class="mainWrapper">
    <!-- HEADLINE AREAA -->
    <div class="headlineArea">
        <div class="wiContainer container">
            <?php echo webinarignition_get_ty_headline( $webinar_data, true ); ?>
            <br clear="left"/>
        </div>
        <!-- /.headlineArea .container-->
    </div>
    <!-- /.headlineArea -->

    <!-- MAIN AREA -->
    <div class="cpWrapperWrapper">
        <div class="wiContainer container">
            <div class="row">
                <div class="cpWrapper">
                    <div class="cpLeftSide col-md-6">
                        <div class="ticketWrapper">
                            <?php webinarignition_get_ty_ticket_date($webinar_data, true); ?>

                            <div class="ticketInfo">
                                <div class="ticketInfoNew">
                                    <?php webinarignition_get_ty_ticket_webinar($webinar_data, true); ?>

                                    <?php webinarignition_get_ty_ticket_host($webinar_data, true); ?>

                                    <?php webinarignition_get_ty_countdown($webinar_data, true); ?>
                                </div>

                                <?php webinarignition_get_ty_webinar_url($webinar_data, true); ?>
                            </div>
                        </div>
                    </div>

                    <div class="cpRightSide col-md-6">
                        <!-- VIDEO / CTA BLOCK AREA HERE -->
                        <?php webinarignition_get_ty_message_area($webinar_data, true); ?>

                        <?php webinarignition_get_ty_reminders_block($webinar_data, true); ?>
                    </div>

                    <br clear="both"/>

                    <?php webinarignition_get_ty_share_gift($webinar_data, true); ?>
                </div>
            </div><!--/.cpWrapperWrapper .row-->
        </div><!--/.cpWrapperWrapper .container-->
    </div><!--/.cpWrapperWrapper -->
</div>

<?php require_once WEBINARIGNITION_PATH . 'inc/lp/partials/powered_by.php'; ?>

<?php wp_footer(); ?>

<!--Extra code-->
<?php echo $webinar_data->footer_code; ?>
<?php echo $webinar_data->footer_code_ty; ?>

</body>
</html>
