<?php defined( 'ABSPATH' ) || exit;
/**
 * Registration template 02
 *
 * @var $template_number
 * @var $webinarId
 * @var $webinar_data
 * @var $is_webinar_available
 * @var $assets
 * @var $user_info
 *
 */

webinarignition_get_lp_header($webinarId, $template_number, $webinar_data);
?>

<!-- TOP AREA -->
<?php webinarignition_get_lp_banner($webinar_data, true) ?>
<!--/.toparea -->

<!-- main wrapper -->
<div class="mainWrapper">
    <!-- HEADLINE AREA -->
    <div class="headlineArea" style="display: <?php echo $webinar_data->lp_main_headline == "" ? "none" : "block"; ?>;">
        <div class="wiContainer container">
            <div class="ssHeadline">
                <?php webinarignition_display( $webinar_data->lp_main_headline, '' ); ?>
            </div>
        </div>
    </div>


    <div class="wiContainer container">

        <!-- MAIN AREA -->
        <div class="cpWrapper">
            <div class="row">
                <div class="col-md-7">
                    <div class="cpLeftSide">
                        <!-- VIDEO / CTA AREA -->
                        <div class="videoBlock">
                            <!-- VIDEO / CTA BLOCK AREA HERE -->
                            <?php webinarignition_get_video_area($webinar_data, true) ?>
                            <!-- VIDEO / CTA BLOCK AREA HERE - End -->
                        </div>
                        <!--/.videoBlock -->


                        <!-- BAR AREA -->
                        <div class="innerHeadline addedArrow">
                            <span>
                                <?php webinarignition_display( $webinar_data->lp_sales_headline, __('What You Will Learn On The Webinar...', 'webinarignition')); ?>
                            </span>
                        </div>
                        <!--/.innerHeadline-->

                        <?php webinarignition_get_lp_host_info($webinar_data, true); ?>

                        <div class="ssSalesArea">

                            <?php
                            webinarignition_display(
                                $webinar_data->lp_sales_copy,
                                '<p>'.__('Your Amazing sales copy for your webinar would show up here...', 'webinarignition').'</p>'
                            );
                            ?>

                        </div>
                    </div>
                    <!--/.cpLeftSide -->
                </div>
                <!--/.col-md-7-->

                <div class="col-md-5">
                    <div class="cpRightSide">
                        <div class="ssRight">
                            <!-- OPT HEADLINE -->
                            <?php webinarignition_get_lp_optin_headline($webinar_data, true) ?>

                            <!-- Paid webinar Checker  -->
                            <?php webinarignition_get_lp_optin_section($webinar_data, true) ?>
                        </div>
                    </div>
                    <!--/.cpRightSide -->
                </div>
                <!--/.cpWrapper .com-md-5-->
            </div>
            <!--/.cpWrapper .row-->
        </div>
        <!--/.cpWrapper -->
    </div>
    <!--/.container -->
</div>
<!--/.mainwrapper-->

<!-- AR OPTIN INTEGRATION -->
<?php webinarignition_get_lp_arintegration($webinar_data, true) ?>
<!-- ========================================== -->

<?php webinarignition_get_lp_footer($webinarId, $template_number, $webinar_data, $user_info) ?>
