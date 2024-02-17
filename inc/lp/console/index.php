<?php
/**
 * @var $is_host
 * @var $is_support
 * @var $webinar_id
 * @var $webinar_data
 * @var $post
 */
defined( 'ABSPATH' ) || exit;
?><!DOCTYPE html>
<html lang="en" style="margin-top:0 !important;">
<head>
    <title><?php _e( 'WebinarIgnition - Live Webinar Console', 'webinarignition') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <script>
        window.webinarId            = <?php echo $webinar_id ?>;
        window.webinarType          = '<?php echo webinarignition_is_auto($webinar_data) ? 'evergreen' : 'live' ?>';
        window.ajaxurl              = '<?php echo admin_url('admin-ajax.php') ?>';
        window.adminPostUrl         = '<?php echo admin_url('admin-post.php') ?>';
        window.webinarUrl           = '<?php echo get_permalink($post->ID) ?>';
        window.webinarIgnitionUrl   = '<?php echo WEBINARIGNITION_URL ?>';
        window.wiRegJS              = {};
        window.wiRegJS.ajax_nonce   = '<?php echo wp_create_nonce("webinarignition_ajax_nonce"); ?>';
        window.is_support           = <?php echo empty( $is_support  ) ? "false" : "true"; ?>;
    </script>

    <?php wp_head(); ?>

    <?php  if( !$is_support ): ?>
    	<script>
            jQuery(document).ready(function ($) {

                    $('.dashTopBTN').on( 'click', function () {

                            $ID = $(this).attr("tabID");
                            // Toggle Tabs
                            $(".consoleTabs").hide();
                            $("#" + $ID).show();
                            // Style Link
                            $('.dashTopBTN').removeClass("success").addClass("secondary").addClass("lc-btn");
                            $(this).addClass("secondary");
                            $(this).removeClass("lc-btn");
                            $(this).addClass("success");
                            return false;

                    });

                    // $('#airCopy').summernote({
                    //         toolbar: [
                    //           ['style', ['style']],
                    //           ['font', ['bold', 'underline', 'clear']],
                    //           ['fontname', ['fontname']],
                    //           ['color', ['color']],
                    //           ['para', ['ul', 'ol', 'paragraph']],
                    //           ['table', ['table']],
                    //           ['insert', ['link']],
                    //           ['view', ['fullscreen', 'codeview', 'help']]
                    //         ]
                    // });

                    // TotalEvents
                    var $totalEvent = 0;
                    $('.checkEvent').each(function () {

                            var $check = $(this).text();

                            if ($check == "Yes") {
                            $totalEvent = $totalEvent + 1;

                            $("#eventTotal").text($totalEvent);
                            // Get Conversion
                            var $totalLeads = $("#leadTotal").text();

                            $totalLeads = parseInt($totalLeads);

                            var $conversion = Math.round(($totalEvent / $totalLeads) * 100);
                            //$("#conversion1").text($conversion + "%");

                            }

                    });

                    // TotalReplay
                    /*var $totalReplay = 0;
                    $('.checkReplay').each(function () {

                            var $check = $(this).text();
                            if ($check == "Yes") {
                                $totalReplay = $totalReplay + 1;
                                $("#replayTotal").text($totalReplay);
                                // Get Conversion
                                $conversion = Math.round(($totalReplay / $totalEvent) * 100);
                                //$("#conversion2").text($conversion + "%");
                            }

                    });*/

                    // TotalOrder
                    $totalOrder = 0;
                    $('.checkOrder').each(function () {

                            var $check = $(this).text();
                            if ($check == "Yes") {
                            $totalOrder = $totalOrder + 1;
                            $("#orderTotal").text($totalOrder);
                            // Get Conversion
                            $totalLeads = $("#leadTotal").text();
                            $totalLeads = parseInt($totalLeads);
                            $conversion = Math.round(($totalOrder / $totalLeads) * 100);
                            //$("#conversion3").text($conversion + "%");
                            }

                    });

                    // LEADS - DASHBOARD
                    $('#leads').dataTable({
                        'iDisplayLength': 10,
                            "language": {
                            "emptyTable"    :       "<?php _e( 'No data available in table', 'webinarignition') ?>",
                            "info"          :       "<?php _e( 'Showing _START_ to _END_ of _TOTAL_ entries', 'webinarignition') ?>",
                            "infoEmpty"     :       "<?php _e( 'Showing 0 to 0 of 0 entries', 'webinarignition') ?>",
                            "lengthMenu"    :       "<?php _e( 'Show _MENU_ entries', 'webinarignition') ?>",
                            "loadingRecords":       "<?php _e( 'Loading...', 'webinarignition') ?>",
                            "processing"    :       "<?php _e( 'Processing...', 'webinarignition') ?>",
                            "search"        :       "<?php _e( 'Search:', 'webinarignition') ?>",          
                            "zeroRecords"   :       "<?php _e( 'No matching records found', 'webinarignition') ?>",
                            "paginate"      :   {
                                "first"     :       "<?php _e( 'First', 'webinarignition') ?>",
                                "last"      :       "<?php _e( 'Last', 'webinarignition') ?>",
                                "next"      :       "<?php _e( 'Next', 'webinarignition') ?>",
                                "previous"  :       "<?php _e( 'Previous', 'webinarignition') ?>"
                             }                         
                          }
                    });
                    var oTable = $('#leads').dataTable();
                    $("#leads_filter").find("input").attr("placeholder", "<?php _e( "Search Through Your Leads Here...", "webinarignition" ); ?>");

                    // DELETE LEAD
                    $('body').on('click', '.delete_lead', function () {

                            var lead_id     = $(this).attr("lead_id");
                            var answer      = confirm("<?php _e( 'Are You Sure You Want To Delete This Lead?', 'webinarignition') ?>");
                            var action      = window.webinarType == 'evergreen' ? "webinarignition_delete_lead_auto" : "webinarignition_delete_lead";

                            if (answer) {

                                var data = { security: window.wiRegJS.ajax_nonce, action  : action,  id: "" + lead_id + "" };

                                $.ajax({

                                    type                :   "post",
                                    url                 :   window.ajaxurl,
                                    data                :   data,
                                    success             :   function(){
                                                                $("#table_lead_" + lead_id).fadeOut("fast");
                                    }
                                });

                            }

                            return false;

                    });

                    setInterval(function(){
                        $.ajax({
                            type    :   "post",
                            url :   window.ajaxurl,
                            data    :   {
                                webinar_id  :   window.webinarId,
                                webinar_type  :   window.webinarType,
                                action      :   "webinarignition_get_users_online",
                                security    :   window.wiRegJS.ajax_nonce
                            },
                            success             :   function(response){
                                var count = '0';
                                var decoded;

                                console.log(response);

                                try {
                                    decoded = $.parseJSON(response);

                                    if (decoded.count) {
                                        count = decoded.count;
                                    }
                                } catch(err) {
                                    console.log(err);
                                    count = response;
                                }



                                $("#usersOnlineCount").html(count);
                            }
                        });
                    }, 5000);

                    $('#showtrackingcode').on('click', function () {

                        event.preventDefault();
                        prompt("<?php _e( 'Paste This iFrame Code On Your Download Page:', 'webinarignition') ?> ", "<iframe src='"+window.webinarUrl+"?trkorder=" + window.webinarId + "' height='0' width='0' style='display:none;' ></iframe>");

                    });


                    $('#importLeads').on('click', function () {
                        $(".importCSVArea").toggle();
                        return false;
                    });

                    $('#addCSV').on('click', function () {

                            var $csv = $("#importCSV").val(),
                                data = {
                                        action      : 'webinarignition_import_csv_leads',
                                        id          : window.webinarId,
                                        csv         : "" + $csv + "",
                                        security    : window.wiRegJS.ajax_nonce
                                        };

                            $.post(window.ajaxurl, data, function () {
                                location.reload();
                            });
                            return false;

                    });

            });
        </script>
    <?php endif; ?>


</head>

<body id="webinarignition_console" class="webinarignition console">

    <?php if ( ! $is_support && ! current_user_can('manage_options') ) { ?>
            <center>
                <h2 style="margin-top: 30px;"><?php _e( 'Not Available - Only Viewable<br> By Admin / Webinar Host', 'webinarignition') ?></h2>

                <p><?php _e( '* If you are seeing this as an error, please log into your WP Admin area... *', 'webinarignition') ?></p>
            </center>
    <?php die(); } ?>

    <!-- TOP AREA -->
    <div class="topArea">
        <div class="consoleLogo">
            <?php
            $logo = $assets . 'images/logoC.png';
            if (!empty($webinar_data->live_console_logo)) $logo = $webinar_data->live_console_logo;
            ?>
            <img src="<?php echo $logo; ?>">
        </div>
    </div>

    <!-- Main Area -->
    <div class="mainWrapper">

        <!-- ACTIVE QUESTIONS -->
        <div class="activeQuestionsHeadline">
             <?php  if( !$is_support ): ?>
                <?php
                if (!$is_host) {
                    ?>
                    <a style="text-decoration:none;padding-left: 1em; padding: 0.38em 1em; border: none;font-weight: normal;" href="<?php echo admin_url() . 'admin.php?page=webinarignition-dashboard'; ?>" class="button button-primary"><i class="icon-wp"></i>WP Admin</a>
                    <?php
                }
                ?>
                <a href="#" class="dashTopBTN button success small" tabID="dashboardTab"><i class="icon-cogs"></i><?php _e( 'Console Dashboard', 'webinarignition') ?> </a>
                 <?php
                 if (!$is_host) {
                     ?>
                     <a href="#" <?php  if ($webinar_data->webinar_date == "AUTO") { echo 'style="display:none;"'; } ?> class="dashTopBTN button secondary small lc-btn" tabID="onairTab"> <i class="icon-microphone"></i> <?php _e( 'On Air', 'webinarignition') ?></a>
                     <?php
                 }
                 ?>
                <a href="#" class="dashTopBTN button secondary small lc-btn" tabID="questionTab" id="questionTabBTN"><i  class="icon-question-sign"></i> <?php _e( 'Manage Questions', 'webinarignition') ?></a>
                <a href="#" class="dashTopBTN button secondary small lc-btn" tabID="leadTab" id="leadTabBTN"><i class="icon-group"></i> <?php _e( 'Manage Registrants', 'webinarignition') ?> </a>

             <?php endif; ?>
        </div>

       <?php
       if( !$is_support ){
            include "dash.php";
            if (!$is_host) include "air.php";
       }

       // Questions
       include "question.php";
       if( !$is_support )include "lead.php";
       ?>
    </div>

    <?php include "partials/footerArea.php"; ?>

    <div id="overlay" style="position: fixed; display: none;  width: 100%;  height: 100%;   top: 0;  left: 0;  right: 0; bottom: 0;  background-color: rgba(0,0,0,0.5);   z-index: 2;  cursor: pointer;"></div>
    <?php echo isset($webinar_data->live_console_footer_code) ? do_shortcode($webinar_data->live_console_footer_code) : ''; ?>
    <?php wp_footer(); ?>


    <style>

        #webinarignition_console [class^="icon-"], #webinarignition_console [class*=" icon-"], #webinarignition_console [class^="icon-"]:before, #webinarignition_console [class*=" icon-"]:before {
            font-family: FontAwesome !important;
        }

    </style>

</body>
</html>
