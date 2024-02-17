<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 * @var $assets
 * @var $paid_check
 * @var $paid_check_js
 * @var $loginUrl
 * @var $user - Who is user
 */
?>

<?php

if ( ! empty($is_webinar_available['available'])) {
    echo $paid_check_js;

    if ( (!empty( $webinar_data->webinar_switch ) && $webinar_data->webinar_switch == 'closed') ) {
        
        echo $webinar_data->lp_optin_closed ? $webinar_data->lp_optin_closed : __( 'Registration is closed for this webinar.', 'webinarignition' );
        
    } elseif( isset ($webinar_data->webinar_status) && ($webinar_data->webinar_status == 'draft') && ( ! current_user_can( 'edit_posts' ) ) ) { ?>
        
        <p><span style="font-weight:bold;"><?php _e( 'This Webinar Is Unpublished. Publish It To Show', "webinarignition" ); ?></span> <span><a target="_blank" href="https://webinarignition.tawk.help/article/this-webinar-is-unpublished-what-to-do-when-your-registration-form-wont-show"><?php _e( 'Read More...', "webinarignition" ); ?></a></span></p>
        
        <script>
            jQuery(document).ready(function($) {

                    setTimeout(function () {
                        window.location = "<?php echo get_home_url(); ?>";
                    }, 20000);

            });
        </script>
        
    <?php  } else { ?>
        
        <?php  if( $webinar_data->paid_status == "paid" ): ?>
        
        <!-- PAID WEBINAR AREA -->
        <div class="paidWebinarBlock" <?php echo $paid_check == "no" ? "style='display:block;'" : "style='display:none;'"; ?>>
            <div>
                <?php webinarignition_display($webinar_data->paid_headline, "<h5 style='text-align: center;'>".__( "Join The Webinar", 'webinarignition' )."<br>".__( "Order Your Spot Now!", 'webinarignition' )."</h5>" ); ?>
                <p class="payment-errors" style="color: #EE3B3B; padding: 1em 1em 0 1em; font-size: .9em; text-align:center; display:none;"></p>
                <p class="payment-success" style="color: #659D32; padding: 1em 1em 0 1em; font-size: .9em; text-align:center; display:none;"></p>
            </div>

            <?php if (webinarignition_usingStripePaymentOption($webinar_data)): ?>
                <form action="" method="POST" id="stripepayment">
                    <span class="payment­errors"></span>
                    <div class="form-row">
                        <label>
                            <span><?php _e( 'Card Number', "webinarignition" ); ?></span>
                            <input type="text" size="20" data-stripe="number" name="stripe_number">
                        </label>
                    </div>
                    <div class="form­row">
                        <label>
                            <span style="display:block;"><?php _e( 'Expiration (MM/YY)', "webinarignition" ); ?></span>
                            <input style="width:48%; display:inline;" type="text" maxlength="2" data­stripe="exp_month" name="stripe_exp_month">
                            <span> / </span>
                            <input style="width:48%; display:inline;" type="text" maxlength="2" data­stripe="exp_year" name="stripe_exp_year">
                        </label>
                    </div>
                    <div class="form­row">
                        <label>
                            <span>CVC</span>
                            <input type="text" size="4" data­stripe="cvc" name="stripe_cvc">
                        </label>
                    </div>
                    <div class="form­row">
                        <label>
                            <span><?php _e( 'Your Email Address', "webinarignition" ); ?></span>
                            <input type="text" size="4" data­stripe="email" name="stripe_receipt_email">
                        </label>
                    </div>
                </form>
            <?php elseif(!webinarignition_usingStripePaymentOption($webinar_data) && !webinarignition_usingPaypalPaymentOption($webinar_data) && $webinar_data->paid_button_type !== 'woocommerce' && $webinar_data->payment_form): ?>
                <?php echo $webinar_data->payment_form; ?>
            <?php endif; ?>

            <?php if(webinarignition_usingStripePaymentOption($webinar_data)): ?>
                <div class="ccCards" style="margin-top: 10px; font-size: 12px; background-color: #F9F9F9; padding: 10px; color: #878787; padding-right: 20px;padding-left: 0px; padding-bottom: 20px;border-radius: 6px; text-align: right;">
                    <img src="<?php echo  $assets . 'images/powered-by-stripe.png'; ?>" style="margin-top: -5px; width: 22%;height: auto;float: left;"><i class="icon-lock" style="margin-right: 10px;"></i> <?php _e( 'Secure Credit Card Processing', "webinarignition" ); ?>
                </div>
            <?php endif; ?>

            <?php

            if ( $webinar_data->paid_button_type != 'custom' ) {
                if (webinarignition_usingStripePaymentOption($webinar_data)) {
                    $wi_paymentUrl = '';
                } else if (webinarignition_usingPaypalPaymentOption($webinar_data)) {
                    $wi_paymentUrl = $webinar_data->paid_pay_url;
                } else if ( in_array($webinar_data->paid_button_type, ['woocommerce','other'] ) ) {
	                $wi_paymentUrl = isset($webinar_data->paid_pay_url) ? $webinar_data->paid_pay_url : '';
                } else {
                    $wi_paymentUrl = '';
                }
                ?>
                <a href="<?php webinarignition_display( $wi_paymentUrl, "#" ); ?>" class="large button" id="order_button"
                   style=" width:100%; background-color:<?php webinarignition_display(
                       $webinar_data->paid_btn_color,
                       "#5DA423"
                   ); ?>; border: 1px solid rgba(0, 0, 0, 0.5) !important;"><?php webinarignition_display(
                        ($webinar_data->paid_button_type == 'stripe') ?  $webinar_data->stripe_paid_btn_copy : $webinar_data->paypal_paid_btn_copy,
                        __( "Order Webinar Now", 'webinarignition')
                    ); ?></a>
                <?php
            } else {
                echo do_shortcode( $webinar_data->paid_button_custom );
            }
            ?>
        </div>
        
        <?php endif; ?>

        <?php if ( ! empty($is_webinar_available['available'])) { ?>
            <!-- OPTIN FORM -->
            <?php webinarignition_generate_optin_form($webinar_data, true); ?>

            <div class="arintegration" style="display:none;">
                <?php include(WEBINARIGNITION_PATH . "inc/lp/ar_form.php"); ?>
            </div>

        <?php
        } else {
		    ?>
            <div class="optinHeadline wiOptinHeadline">
                <span class="optinHeadline1 wiOptinHeadline1"><?php  echo __('Webinar is full, please contact the webinar host.', 'webinarignition'); ?></span>
            </div>
		    <?php
	    }
    }
} else {
	?>
    <div class="optinHeadline wiOptinHeadline">
        <span class="optinHeadline1 wiOptinHeadline1"><?php  echo __('Webinar is full, please contact the webinar host.', 'webinarignition'); ?></span>
    </div>
	<?php
}

