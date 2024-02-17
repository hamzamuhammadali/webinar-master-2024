<?php
/**
 * @var $webinar_id
 * @var $is_support_stuff_token
 * @var $is_host_presenters_token
 */
defined( 'ABSPATH' ) || exit;

?><!DOCTYPE html>
<html lang="en" style="margin-top:0 !important;">
<head>
    <title><?php _e( 'WebinarIgnition - Live Webinar Console', 'webinarignition') ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <?php wp_head(); ?>

    <script>
        window.webinarId            = <?php echo $webinar_id ?>;
        window.ajaxurl              = '<?php echo admin_url('admin-ajax.php') ?>';
    </script>

    <style>
        .registerSupport {
            width: 100%;
            max-width: 920px;
            padding: 25px 15px;
            margin: 0 auto;
        }

        #registerSupport_form {
            width: 100%;
            max-width: 460px;
            margin: 0 auto;
        }

        .registerSupport_row {
            margin-bottom: 25px;
        }

        .registerSupport_row.errored label {
            color: #900000;
        }

        .registerSupport_row.errored input {
            border-color: #900000;
        }

        .registerSupport_row:last-child {
            margin-bottom: 0;
        }

        #registerSupport_button {
            margin: 0 !important;
            width: 100%;
        }
    </style>
</head>

<body id="webinarignition_console" class="webinarignition console">

    <!-- TOP AREA -->
    <div class="topArea">
        <div class="consoleLogo">
            <?php
            $logo = $assets . 'images/logoC.png';

            if (!empty($webinar_data->live_console_logo)) {
                $logo = $webinar_data->live_console_logo;
            }
            ?>
            <img src="<?php echo $logo; ?>">
        </div>
    </div>

    <div class="mainWrapper">
        <div id="registerSupport_container" class="registerSupport">
            <form id="registerSupport_form">
                <input name="app_id" type="hidden" value="<?php echo $webinar_id; ?>">
                <input name="support_stuff_url" type="hidden" value="<?php echo $is_support_stuff_token; ?>">
                <input name="host_presenters_url" type="hidden" value="<?php echo $is_host_presenters_token; ?>">

                <p id="registerSupport_email" class="registerSupport_row">
                    <label for="support_email">
                        <?php _e( 'Email (required)', 'webinarignition') ?>
                    </label>

                    <input class="registerSupport_field" type="email" name="email" id="support_email">
                </p>

                <p id="registerSupport_first_name" class="registerSupport_row">
                    <label for="support_first_name">
                        <?php _e( 'First Name (required)', 'webinarignition') ?>
                    </label>

                    <input class="registerSupport_field" type="text" name="first_name" id="support_first_name">
                </p>

                <p id="registerSupport_last_name" class="registerSupport_row">
                    <label for="support_last_name">
                        <?php _e( 'Last Name (required)', 'webinarignition') ?>
                    </label>

                    <input class="registerSupport_field" type="text" name="last_name" id="support_last_name">
                </p>

                <p class="registerSupport_row">
                    <button id="registerSupport_button" type="button" class="button radius success">
                        <i class="icon-save"></i> <?php _e( 'Register', 'webinarignition') ?>
                    </button>
                </p>
            </form>
        </div>

        <?php include "partials/footerArea.php"; ?>
    </div>

    <?php wp_footer(); ?>

    <script>
        (function ($) {
            $(document.body).on('click', '#registerSupport_button', function() {
                var btn = $(this);
                var formData = $("#registerSupport_form").serializeArray();
                var data = {'action': 'webinarignition_register_support', 'formData': formData};
                var fields = $('.registerSupport_row');

                fields.each(function () {
                    $(this).removeClass('errored');
                });

                ajaxRequest(data, function(response) {
                    console.log(response);
                    if (response.replace) {
                        $('#registerSupport_container').empty().html($(response.replace));
                    }
                }, function(response) {
                    if (response.errors) {
                        $.each(response.errors, function(field, error) {
                            $('#registerSupport_' + field).addClass('errored');
                        });
                    }
                    console.log(response);
                });
            });

            function ajaxRequest(data, cb, cbError) {
                $.ajax({
                    type: 'post',
                    url: ajaxurl,
                    data: data,
                    success: function (response) {
                        var decoded;

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
        })(jQuery);
    </script>
</body>
</html>