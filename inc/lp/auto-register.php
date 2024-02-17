<?php defined( 'ABSPATH' ) || exit; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $webinar_data->webinar_desc; ?></title>

    <?php wp_head(); ?>

</head>
<body id="auto-register">


    <script>
        <?php
        //make the thank you page url
        if($webinar_data->custom_ty_url_state === 'show' && !empty($webinar_data->custom_ty_url)) {
            $thank_you_page_url = $webinar_data->custom_ty_url;
        } else {
            $thank_you_page_url = ( $webinar_data->webinar_switch == 'live' ) ?  $webinar_data->webinar_permalink . '?live' : $webinar_data->webinar_permalink . '?confirmed';
            if($webinar_data->paid_status === 'paid')
                $thank_you_page_url .= '&' . urlencode($webinar_data->paid_code);
        }

        $thank_you_page_url .= '&email='.$email; ?>

        var thank_you_url = '<?php echo $thank_you_page_url; ?>';

        jQuery(document).ready(function () {

            jQuery('#ar_submit_iframe').on('load', function (event) {
                if (!jQuery(this).data('can_load')) {
                    return false;
                }
                window.location.href = thank_you_url;
            });

            // on load submit information & submit AR Form...

            // AJAX FOR WP
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            var data = {
                action: 'webinarignition_add_lead_auto_reg',
                security:       '<?php echo wp_create_nonce("webinarignition_ajax_nonce"); ?>',
                id:     "<?php echo $webinar_id; ?>",
                name:   "<?php echo $name; ?>",
                email:  "<?php echo $email; ?>",
                ip:     "<?php echo $_SERVER['REMOTE_ADDR']; ?>",
                source: "AutoReg"
            };

            jQuery.post(ajaxurl, data, function ( response ) {
                
                thank_you_url = thank_you_url+'&lid='+response;
                
                if (jQuery("#AR-INTEGRATION").length > 0) {
                    jQuery('#ar_submit_iframe').data('can_load', 'true');
                    HTMLFormElement.prototype.submit.call(jQuery("#AR-INTEGRATION")[0]);
                } else {
                    window.location.href = thank_you_url;
                }
            });

        });
    </script>

<div class="informationBox">
    <h2 style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px dashed #b3b3b3;"><?php echo $webinar_data->webinar_desc; ?></h2>
    <h4 style="font-weight:normal;"><?php echo $webinar_data->webinar_host; ?></h4>
</div>

<div class="loaderBox">
    <i class="fa fa-spinner fa-spin fa-4x"></i>
</div>


<!-- AR OPTIN INTEGRATION -->
<div class="arintegration" style="display:none;">
	<?php include(WEBINARIGNITION_PATH . "inc/lp/ar_form.php"); ?>
</div>
</body>
</html>