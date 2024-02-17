<?php
defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_id
 * @var $webinar_data
 * @var $webinarignition_page
 * @var $pluginName
 * @var $leadId
 * @var $lead
 */

$webinar_data           = !empty( $webinar_data ) ? $webinar_data : get_query_var( 'webinar_data' );
$webinarignition_page   = !empty( $webinarignition_page ) ? $webinarignition_page : get_query_var( 'webinarignition_page' );

?>

<script>
/**
 * Global scripts
 */
(function ($) {
    var ajaxurl         = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    var webinar_id      = '<?php echo $webinar_id ?>';
    window.wiRegJS      = {};
    wiRegJS.ajax_nonce  = '<?php echo wp_create_nonce( "webinarignition_ajax_nonce" ); ?>';

    var zoom_container = $('#zoom_video_uri');

    if (zoom_container.length) {
        zoom_container.hide();

        $('#zoom_video_uri iframe').on( 'load', function() {
            $('#zoom_video_uri iframe').contents().find('.vczapi-zoom-browser-meeting--info').remove();

            <?php if (!empty($lead->name)) {
                ?>
            var lead_name = '<?php echo $lead->name ?>';
            var lead_name_field = $('#zoom_video_uri iframe').contents().find('#vczapi-jvb-display-name');

            if (lead_name_field.length) {
                lead_name_field.val(lead_name);
            }

            <?php
            } ?>

            <?php if (!empty($lead->email)) {
                ?>
            var lead_email = '<?php echo $lead->email ?>';
            var lead_email_field = $('#zoom_video_uri iframe').contents().find('#vczapi-jvb-email');

            if (lead_email_field.length) {
                lead_email_field.val(lead_email);
            }

            <?php
            } ?>
            console.log('iframe  loaded');

            zoom_container.show();
        } );
    }

    // ASK QUESTION
    $('#askQuestion').on('click', function () {
        function validateEmail(email) {
            var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        }

        $question = $("#question").val();
        $Name = $("#optName").val();
        $Email = $("#optEmail").val();
        $ID = $("#leadID").val();

        if (!validateEmail($Email)) {
            $('#optEmail').addClass("errorField");
            return false;
        }

        const is_first_question = localStorage.getItem('webinar_<?php echo $webinar_id; ?>_question_submitted') ? false : true;

        if ($question == "") {
            $("#question").addClass("errorField");
        } else {
            // Submit Question...
            $("#question").removeClass("errorField");

            var video_live_time        = '<?php echo ($webinar_data->webinar_date == 'AUTO') ? $lead->date_picked_and_live : $webinar_data->webinar_date . ' '. $webinar_data->webinar_start_time; ?>';
                video_live_time        = new Date(video_live_time).getTime();
            var timeNow                = Date.now();
            var timeDifference         = timeNow-video_live_time;
            var webinarTime            = (timeDifference)/60000;


            // AJAX Question
            var data = {
                action: 'webinarignition_submit_question',
                security: '<?php echo wp_create_nonce( "webinarignition_ajax_nonce" ); ?>',
                id: "<?php echo $webinar_id; ?>",
                question: "" + $question + "",
                name: "" + $Name + "",
                email: "" + $Email + "",
                lead: "" + $ID + "",
                webinar_type: '<?php echo $webinar_data->webinar_date == 'AUTO' ? 'evergreen' : 'live'; ?>',
                is_first_question: is_first_question,
                webinarTime: webinarTime.toFixed()
            };

            $("#askQArea").hide();
            $("#askQThankyou").show();

            $.post(ajaxurl, data, function (results) {

                    if(is_first_question && results) {
                        localStorage.setItem('webinar_<?php echo $webinar_id; ?>_question_submitted', true);
                    }

                setTimeout(function () {
                     $("#askQArea").show();
                     $("#askQThankyou").hide();
                     $("#question").val("");
                }, 15000);

            });

        }

        return false;
    });

	<?php if( $webinar_data->webinar_date == 'AUTO' ) { //Define auto webinar JS functions ?>
    function maybeSendAfterWebinarQuestionsNotification() {

        const is_after_auto_webinar_questions_sent = localStorage.getItem('after_auto_webinar_<?php echo $webinar_id; ?>_questions_sent') ? true : false;

        if( is_after_auto_webinar_questions_sent ){
            return;
        }

        var attendeeName    = $("#optName").val();
        var attendeeEmail   = $("#optEmail").val();
        var leadID          = $("#leadID").val();

        var data = {
            action      : 'webinarignition_after_auto_webinar',
            security    : '<?php echo wp_create_nonce( "webinarignition_ajax_nonce" ); ?>',
            webinar_id  : "<?php echo $webinar_id; ?>",
            name        : "" + attendeeName + "",
            email       : "" + attendeeEmail + "",
            lead        : "" + leadID + ""
        };

        if( !is_after_auto_webinar_questions_sent ){

            $.post(ajaxurl, data, function (results) {
                localStorage.setItem('after_auto_webinar_<?php echo $webinar_id; ?>_questions_sent', true);
            });

        }
    }

    <?php //TODO: ajaxRequest() Maybe not in used should be checked and removed ?>
    function ajaxRequest(data, cb, cbError) {
        $.ajax({
            type: 'post',
            url: ajaxurl,
            data: data,
            success: function (response) {
                var decoded;

                // console.log(response);

                try {
                    decoded = $.parseJSON(response);
                } catch(err) {
                    console.log(err);
                    decoded = false;
                }

                if (decoded) {
                    if (decoded.success) {
                        if (decoded.message) {
                            alert(decoded.message);
                        }

                        if (decoded.url) {
                            window.location.replace(decoded.url);
                        } else if (decoded.reload) {
                            window.location.reload();
                        }

                        if (typeof cb === 'function') {
                            cb(decoded);
                        }
                    } else {
                        if (decoded.message) {
                            alert(decoded.message);
                        }

                        if (typeof cbError === 'function') {
                            cbError(decoded);
                        }
                    }
                } else {
                    alert('<?php _e( 'Something went wrong', "webinarignition" ); ?>');
                }
            }
        });
    }

	<?php } else { //Define live webinar JS functions ?>

    function webinarignition_check_qna_enabled() {
        var webinarExtraBlock = $('.webinarExtraBlock');

        setInterval(function(){

            $.post(ajaxurl, {
                action      : 'webinarignition_check_if_q_and_a_enabled',
                security    : window.wiRegJS.ajax_nonce,
                webinar_id  : webinar_id
            }, function (results) {
                if( results && results.data && results.data.enable_qa && ( results.data.enable_qa == 'no'  ) ) {
                    webinarExtraBlock.hide();
                } else {
                    webinarExtraBlock.show();
                }
            });

        }, 10000);
    }

    <?php } ?>

    $(document).ready(function () {

        <?php if( $webinar_data->webinar_date == 'AUTO' && WebinarignitionManager::url_is_confirmed_set() === false ) { //Define scripts to run for auto webinar ?>
            window.addEventListener('beforeunload', function (e) {
                e.preventDefault();
                maybeSendAfterWebinarQuestionsNotification();
                return undefined;
            });
	    <?php } else { //Define scripts to run for live webinar ?>
            <?php if(empty($webinar_data->webinar_qa) || 'chat' !== $webinar_data->webinar_qa) { ?>
            if (typeof webinarignition_check_qna_enabled === "function") {
                webinarignition_check_qna_enabled();
            }
            <?php }  ?>

        <?php } ?>

        var iframeBlocks = $("#vidBox").find("iframe");

        if (iframeBlocks.length) {
            var webinarVideo = $('#webinarContent');
            var isModern = webinarVideo.length;

            if (!isModern) {
                iframeBlocks.each(function() {
                    var iframeBlock = $(this);

                    var styles = {
                        "position" : "absolute",
                        "width": "100%",
                        "height": "100%",
                        "left": "0",
                        "top": "0"
                    };

                    iframeBlock.css( styles );

                    iframeBlock.wrap( "<div class='ctaAreaVideo-aspect-ratio' style='position: relative;width: 100%;height: 0;padding-bottom: 56.25%;'></div>" );
                });
            }
        }
    });
})(jQuery);
</script>