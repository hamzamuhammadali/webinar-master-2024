<?php defined( 'ABSPATH' ) || exit;
/**
 * Registration template 03
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


<!-- Main Area -->
<div class="mainWrapper">
    <div class="wiContainer container">
        <!-- HEADLINE AREA -->
        <div class="headlineArea" style="display: <?php echo $webinar_data->lp_main_headline == "" ? "none" : "block"; ?>;">
            <?php webinarignition_display( $webinar_data->lp_main_headline, '' ); ?>
        </div>

        <!-- Paid webinar Checker  -->
        <?php
        if ( $webinar_data->paid_status == "paid" ) {
            $paid_check = "no";
            ?>
            <script> var paid_code = {"code": <?php echo "'" . $webinar_data->paid_code . "'"; ?>} </script>
            <?php
        } else {
            $paid_check = "yes";
        }
        // check if campaign ID is in the URL, if so, its the thank you url...
        if ( isset( $input_get[ $webinar_data->paid_code ] ) ) {
            $paid_check = "yes";
        }
        ?>

        <!-- MAIN AREA -->
        <div class="cpWrapper">
            <div class="row">
                <div class="col-md-5">
                    <div class="cpLeftSide">
                        <?php webinarignition_get_lp_optin_headline($webinar_data, true) ?>

                        <?php webinarignition_get_lp_optin_section($webinar_data, true) ?>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="cpRightSide">
                        <!-- VIDEO / CTA BLOCK AREA HERE -->
                        <?php webinarignition_get_video_area($webinar_data, true) ?>
                        <!-- VIDEO / CTA BLOCK AREA HERE - End -->

                        <div class="innerHeadline addedArrow">
                        <span>
                              <?php webinarignition_display( $webinar_data->lp_sales_headline, __('What You Will Learn On The Webinar...', 'webinarignition') ); ?>
                        </span>
                        </div>

                        <div class="cpUnderCopy">
                            <?php webinarignition_get_lp_host_info($webinar_data, true); ?>

                            <div class="cpCopyArea">
                                <?php
                                webinarignition_display(
                                    $webinar_data->lp_sales_copy,
                                    '<p>'.__('Your Amazing sales copy for your webinar would show up here...', 'webinarignition').'</p>'
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                        <!--/.cpLeftSide -->
                    </div>
                    <!--/.cpWrapper .colmd6 -->

                    <div class="col-md-6">
                        <div class="cpRightSide">
                        </div>
                        <!--/.cpRightSide-->
                    </div>
                    <!--/.cpWrapper .colmd6-->

                    <br clear="both"/>
                    <!--/.cpUnderCopy-->
                </div>
                <!--/.cpWrapper .row-->
            </div>
            <!--/.cpWrapper-->
        </div>
        <!--/.mainWrapper container-->
    </div>
    <!--/.mainWrapper -->

<!-- AR OPTIN INTEGRATION -->
<?php webinarignition_get_lp_arintegration($webinar_data, true) ?>
<!-- ========================================== -->

<?php webinarignition_get_lp_footer($webinarId, $template_number, $webinar_data, $user_info) ?>
