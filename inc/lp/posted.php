<?php
define( 'WP_USE_THEMES', false );
require( '../../../../../wp-blog-header.php' );
status_header( 200 );

// universal variables
$full_path                  = get_site_url();
$assets                     = WEBINARIGNITION_URL . "inc/lp/";
$post_input                 = filter_input_array(INPUT_POST);

$post_input['campaignID']   = isset( $post_input['campaignID'] )  ? sanitize_text_field( $post_input['campaignID'] ) : null;
$post_input['name']         = isset( $post_input['name'] )        ? sanitize_text_field( $post_input['name'] ) : null;
$post_input['email']        = isset( $post_input['email'] )       ? sanitize_email( $post_input['email'] ) : null;
$post_input['phone']        = isset( $post_input['phone'] )       ? sanitize_text_field( $post_input['phone'] ) : null;

// Get DB Info
global $wpdb;
$table_db_name              = $wpdb->prefix . "webinarignition";
$ID                         = $post_input["campaignID"];
$post_input['id']           = $post_input["campaignID"];
$data                       = $wpdb->get_row( "SELECT * FROM $table_db_name WHERE id = '$ID'", OBJECT );

$pluginName                 = "webinarignition";
$sitePath                   = WEBINARIGNITION_URL;

// Get Results
$id                         = $post_input["campaignID"];
$results = WebinarignitionManager::get_webinar_data($id);

// JUST CONNECTED - STORE LEAD - REDIRECT TO AR OR THANK YOU PAGE
webinarignition_add_lead_callback(  ); ?>

<!DOCTYPE HTML>

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>You are being registered for the webinar</title>
    <style>
        .main {
            margin: 0 auto;
            font-family: "Lato", sans-serif;
            text-align: center
        }

        @import url("//fonts.googleapis.com/css?family=Lato:100,300,700");
        h1 {
            font-family: Lato;
            color: #000;
            text-transform: uppercase;
            display: inline-block;
            font-size: 1em;
            letter-spacing: 1.5px;
            text-align: center;
            margin-top: 20px;
            -webkit-animation: fade 3s infinite
        }

        .container {
            width: 110px;
            padding-top: 180px;
            margin: auto;
            vertical-align: middle
        }

        .ex {
            color: #000;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            text-align: center;
            -webkit-animation: fade 3s infinite;
            font-family: flamenco;
            font-size: 4em;
            width: 40px;
            height: 40px;
            margin-top: -17px;
            display: inline-block;
            border: 4px double #333
        }

        .ex:nth-child(1) {
            -webkit-animation: spin1 3s infinite 1s;
            -webkit-transform-origin: 50% 52%;
            margin-left: 10px
        }

        .ex:nth-child(2) {
            -webkit-animation: spin2 3s infinite 1s;
            -webkit-transform-origin: 50% 52%;
            margin-left: -20px
        }

        .ex:nth-child(3) {
            -webkit-animation: spin2 3s infinite 1s;
            -webkit-transform-origin: 50% 52%;
            margin-left: 10px
        }

        .ex:nth-child(4) {
            -webkit-animation: spin1 3s infinite 1s;
            -webkit-transform-origin: 50% 52%;
            margin-left: -20px
        }

        @-webkit-keyframes spin1 {
            0% {
                -webkit-transform: rotate(0deg)
            }
            100% {
                -webkit-transform: rotate(360deg)
            }
        }

        @-moz-keyframes spin1 {
            0% {
                -webkit-transform: rotate(0deg)
            }
            100% {
                -webkit-transform: rotate(360deg)
            }
        }

        @-o-keyframes spin1 {
            0% {
                -webkit-transform: rotate(0deg)
            }
            100% {
                -webkit-transform: rotate(360deg)
            }
        }

        @keyframes spin1 {
            0% {
                -webkit-transform: rotate(0deg)
            }
            100% {
                -webkit-transform: rotate(360deg)
            }
        }

        @-webkit-keyframes spin2 {
            0% {
                -webkit-transform: rotate(0deg)
            }
            100% {
                -webkit-transform: rotate(-360deg)
            }
        }

        @-moz-keyframes spin2 {
            0% {
                -webkit-transform: rotate(0deg)
            }
            100% {
                -webkit-transform: rotate(-360deg)
            }
        }

        @-o-keyframes spin2 {
            0% {
                -webkit-transform: rotate(0deg)
            }
            100% {
                -webkit-transform: rotate(-360deg)
            }
        }

        @keyframes spin2 {
            0% {
                -webkit-transform: rotate(0deg)
            }
            100% {
                -webkit-transform: rotate(-360deg)
            }
        }

        @-webkit-keyframes fade {
            50% {
                opacity: .5
            }
            100% {
                opacity: 1
            }
        }

        @-moz-keyframes fade {
            50% {
                opacity: .5
            }
            100% {
                opacity: 1
            }
        }

        @-o-keyframes fade {
            50% {
                opacity: .5
            }
            100% {
                opacity: 1
            }
        }

        @keyframes fade {
            50% {
                opacity: .5
            }
            100% {
                opacity: 1
            }
        }
    </style>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
</head>

<body>

    <section class="main">

        <div class="wiContainer container">
            <div class="ex"></div>
            <div class="ex"></div>
            <div class="ex"></div>
            <div class="ex"></div>
        </div>
        <h1>Signing you up ...</h1>

    </section>

    <!-- AR OPTIN INTEGRATION -->
    <div class="arintegration" style="display:none;">

        <?php if ($results->ar_url !== "") { ?>
	        <?php
            $webinar_data = $results;
            include(WEBINARIGNITION_PATH . "inc/lp/ar_form.php");
            ?>
        <?php } ?>

        <script>
            <?php
            //make the thank you page url
            if($results->custom_ty_url_state === 'show' && !empty($results->custom_ty_url)) {
                $thank_you_page_url = $results->custom_ty_url;
            } else {
                $thank_you_page_url = $results->webinar_permalink . '?confirmed';
            }
            ?>
            var thank_you_url = '<?php echo $thank_you_page_url; ?>';
            jQuery(document).ready(function () {
                $('#ar_submit_iframe').load(function () {
                    if (!$(this).data('can_load'))
                        return false;
                    window.location.href = thank_you_url;
                });

                if (document.getElementById('AR-INTEGRATION')) {
                    $('#ar_submit_iframe').data('can_load', 'true');
                    HTMLFormElement.prototype.submit.call(document.getElementById('AR-INTEGRATION'));
                } else {
                    window.location.href = thank_you_url;
                }
            });
        </script>

    </div>

</body>

</html>
