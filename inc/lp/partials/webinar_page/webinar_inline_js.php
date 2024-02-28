<?php
defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_id
 * @var $webinar_data
 * @var $webinarignition_page
 * @var $pluginName
 */

$lead_id = !empty($leadinfo->ID) ? $leadinfo->ID : '';
$webinar_type = $webinar_data->webinar_date == "AUTO" ? 'evergreen' : 'live';
?>
<script type="text/javascript">
    jQuery.expr.pseudos.parents = function (a, i, m) {
        return jQuery(a).parents(m[3]).length < 1;
    };

	if( !$ ){
		$ = jQuery;
	}
    // AJAX FOR WP
    var globalOffset = 0;
    var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';

    (function ($) {

	    <?php if ( !empty(!empty($leadinfo) && !empty($webinar_data->limit_lead_visit) && 'enabled' === $webinar_data->limit_lead_visit) ) { ?>
        $(window).on('beforeunload', function(e) {
            e.preventDefault();

            webinarignition_mark_lead_status('complete');

            return null;
        });
	    <?php } ?>

        <?php if( get_query_var( 'webinarignition_page' ) == 'webinar' || ( isset( $webinarignition_page ) && $webinarignition_page === 'webinar' )  ):  ?>
            // TRACK +1 VIEW
            $getTrackingCookie = $.cookie('we-trk-live-<?php echo $webinar_id; ?>');

            // if not tracked yet
            if ($getTrackingCookie != "tracked") {
                // No Cookie Set - Track View
                $.cookie('we-trk-live-<?php echo $webinar_id; ?>', "tracked", {expires: 30});
                var data = {
                    action: '<?php echo $pluginName; ?>_track_view',
                    security: '<?php echo wp_create_nonce( "webinarignition_ajax_nonce" ); ?>',
                    id: "<?php echo $webinar_id; ?>",
                    page: "live"
                };
                $.post(ajaxurl, data, function (results) {
                });
            }

            // Track +1 Total
            var data = {
                action: '<?php echo $pluginName; ?>_track_view_total',
                security: '<?php echo wp_create_nonce( "webinarignition_ajax_nonce" ); ?>',
                id: "<?php echo $webinar_id; ?>",
                page: "live"
            };
            $.post(ajaxurl, data, function (results) {
            });

        <?php endif; ?>

        // VIDEO FIXES:
        $(".ctaArea").find("embed, object").height(518).width(920);

        <?php
	    $lead_id = isset( $input_get['lid'] ) ? $input_get['lid'] : '';
	    $is_auto_login_enabled = get_option( 'webinarignition_registration_auto_login', 1 ) == 1;
	    if( ($is_auto_login_enabled && is_user_logged_in() || !$is_auto_login_enabled) && get_query_var( 'webinarignition_page' ) == 'webinar' || ( isset( $webinarignition_page ) && $webinarignition_page === 'webinar' )  ):  ?>
            // Track Event Attendance
            $checkCookie = $.cookie('we-trk-<?php echo $webinar_id; ?>');
            // Post & Track
            $.post(ajaxurl, {
                action: 'webinarignition_update_view_status',
                security: '<?php echo wp_create_nonce( 'webinarignition_ajax_nonce' ); ?>',
                id: '<?php echo $webinar_id; ?>',
                lead_id: '<?php echo trim( $lead_id ); ?>'
            });

	    <?php if ( !empty(!empty($leadinfo) && !empty($webinar_data->limit_lead_visit) && 'enabled' === $webinar_data->limit_lead_visit) ) { ?>
        webinarignition_mark_lead_status('attending');
	    <?php } ?>

            <?php if( 'hide' !== $webinar_data->webinar_qa ) :  ?>

                            // Get Name / Email
                            <?php
                            if ( $webinar_data->webinar_date == "AUTO" ) {
                                ?>
                                var data = {
                                    action: 'webinarignition_get_qa_name_email2',
                                    security: '<?php echo wp_create_nonce( "webinarignition_ajax_nonce" ); ?>',
                                    cookie: "<?php echo $input_get['lid']; ?>",
                                    ip: "<?php echo $_SERVER['REMOTE_ADDR']; ?>"
                                };
                                <?php
                            } else {
                                ?>
                                var data = {
                                    action: 'webinarignition_get_qa_name_email',
                                    security: '<?php echo wp_create_nonce( "webinarignition_ajax_nonce" ); ?>',
                                    cookie: "<?php echo trim( webinarignition_getLid( $webinar_id ) ); ?>",
                                    ip: "<?php echo $_SERVER['REMOTE_ADDR']; ?>"
                                };
                                <?php
                            }
                            ?>

                            let optNameVal = '';
                            if( $('#optName').length > 0 ) {
                                optNameVal = $('#optName').val();
                            }

                            let optEmailVal = '';
                            if( $('#optEmail').length > 0 ) {
                                optEmailVal = $('#optEmail').val();
                            }

                            let leadIDVal = '';
                            if( $('#leadID').length > 0 ) {
                                leadIDVal = $('#leadID').val();
                            }

                            if ('' === optNameVal || '' === optEmailVal || '' === leadIDVal) {
                                $.post(ajaxurl, data, function (results) {
                                    if (results) {
                                        $qaInfo = results.split("//");

                                        if ('' === optNameVal.trim()) {
                                            $("#optName").val($qaInfo[0] !== 'undefined' ? $qaInfo[0] : "");
                                        }

                                        if ('' === optEmailVal.trim()) {
                                            $("#optEmail").val($qaInfo[1]);
                                        }

                                        if ('' === leadIDVal.trim()) {
                                            $("#leadID").val($qaInfo[2]);
                                        }

                                        // $("#optName").attr("disabled","disabled");
                                        // $("#optEmail").attr("disabled","disabled");
                                    }
                                });
                            }

            <?php endif;  ?>

        <?php endif;  ?>

        // Polling For Live Broadcast Message
        <?php if( property_exists( $webinar_data, 'webinar_date' ) && property_exists( $webinar_data, 'live_stats' ) ) {  ?>

        <?php
        if ( $webinar_data->webinar_date != "AUTO" && $webinar_data->live_stats != 'disabled' ) {

            ?>
            sessionStorage.removeItem('hash');
            $.Updater(ajaxurl, {
                method: 'get',
                data: {
                    action: 'webinarignition_broadcast_msg_poll_callback',
                    security: '<?php echo wp_create_nonce( "webinarignition_ajax_nonce" ); ?>',
                    id: "<?php echo $webinar_id; ?>",
                    lead_id: "<?php echo $lead_id; ?>",
                    ip: "<?php echo $_SERVER['REMOTE_ADDR']; ?>"
                },
                interval: 5000,
                type: 'text'
            }, function (jsonString) {

                var jsonobj     = JSON.parse(jsonString);
                var orderBTN    = $("#orderBTN");
                var hash        =  sessionStorage.getItem('hash');
                if (jsonobj.air_toggle !== "OFF" && ( !hash || hash != jsonobj.hash  )) {
                    $('.webinarVideoCTA').addClass('webinarVideoCTAActive');
                    orderBTN.show();
                    $("#orderBTNCopy").html(jsonobj.response);
                   if( jsonobj.hash ){
                      sessionStorage.setItem('hash', jsonobj.hash );
                   }

                }

                if (jsonobj.air_toggle == "OFF") {
                    $('.webinarVideoCTA').removeClass('webinarVideoCTAActive');
                    orderBTN.hide();
                }

            });
            <?php
        }
        ?>

        <?php } ?>

    })(jQuery);

    function webinarignition_mark_lead_status(status, always_callback) {
        return $.ajax({
            url: ajaxurl,
            method: 'POST',
            dataType: 'JSON',
            async: (/Firefox[\/\s](\d+)/.test(navigator.userAgent) && new Number(RegExp.$1) >= 4) === false,
            data: {
                action: 'webinarignition_lead_mark_' + status,
                nonce: '<?php echo wp_create_nonce('webinarignition_mark_lead_status'); ?>',
                webinar_id: '<?php echo $webinar_id; ?>',
                lead_id: '<?php echo $lead->ID ?>',
            },
            always: function(xhr, xhr_status, xhr_error) {
                <?php
                //TODO: Need to check why always/done/fail callbacks are not working here
                ?>
                if( always_callback && typeof always_callback === 'function' ) {
                    always_callback(xhr, xhr_status, xhr_error);
                }
            }
        });
    }
</script>
