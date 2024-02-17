<?php defined( 'ABSPATH' ) || exit;
/**
 * Registration page footer template
 *
 * @var $template_number
 * @var $webinarId
 * @var $webinar_data
 * @var $assets
 * @var $user_info
 */
?>

<?php require_once WEBINARIGNITION_PATH .  'inc/lp/partials/powered_by.php'; ?>

<?php if( $webinar_data->wp_head_footer === 'enabled'  ):  ?>
    <?php wp_footer(); ?>
<?php else: ?>

	<?php
	//include videojs resources when registration CTA type is video, and video URL is set
    if ( (empty($webinar_data->lp_cta_type) || $webinar_data->lp_cta_type == 'video') && !empty( $webinar_data->lp_cta_video_url ) ): ?>
    <script type="text/javascript" src="<?php echo $assets; ?>video-js-7.20.3/video-js.min.js"></script>
	<?php endif; ?>
    <script type="text/javascript" src="<?php echo $assets; ?>js/cookie.js"></script>
    <script type="text/javascript" src="<?php echo $assets; ?>js-libs/js/intlTelInput.js"></script>
    <script type="text/javascript" src="<?php echo $assets; ?>js/frontend.js"></script>
    <script type="text/javascript" src="<?php echo $assets; ?>js/tz.js"></script>
    <script type="text/javascript" src="<?php echo $assets; ?>js/luxon.min.js"></script>

    <?php if ($webinar_data->stripe_publishable_key): ?>
        <?php  $stripe_publishable_key = $webinar_data->stripe_publishable_key; // don't know what this is for ?>
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <script type="text/javascript">
            Stripe.setPublishableKey('<?php echo $webinar_data->stripe_publishable_key; ?>');
        </script>
    <?php endif; ?>

    <script>
        <?php
        $wi_parsed = webinarignition_parse_registration_page_data($webinarId, $webinar_data);
        $wi_parsed['leadDeviceInfo'] = !empty($webinarignition_lead_browser_and_os) ? $webinarignition_lead_browser_and_os : array();

        $isSigningUpWithFB = false;
        $fbUserData = [];
        if (isset($_GET['code'])) {
            $isSigningUpWithFB = true;
            $fbUserData['name'] = $user_info['name'];
            $fbUserData['email'] = $user_info['email'];
        }
        $wi_parsed['isSigningUpWithFB'] = $isSigningUpWithFB;
        $wi_parsed['fbUserData'] = $fbUserData;
        ?>

        if( typeof wiParsed === 'undefined' ) {
            var wiParsed = {};
        }

        if( typeof wiParsed.translations === 'undefined' ) {
            wiParsed.translations = {};
        }
            
        // set data to be used by registration-page.js
        window.webinarignition                               = '<?php echo json_encode($wi_parsed, JSON_HEX_APOS); ?>';
        webinarignitionTranslations                          = {};
        webinarignitionTranslations['ar_modal_head']         = "<?php echo  __( "AR Data Submitted!", 'webinarignition' ); ?>";
        webinarignitionTranslations['ar_modal_body']         = "<?php echo  __( "If all went well, the data should be in your autoresponder list. Check your autoresponder list to confirm.", 'webinarignition' ); ?>";
        webinarignitionTranslations['someWrong']             = "<?php echo  __( "Something went wrong", 'webinarignition' ); ?>";
        webinarignitionTranslations['noScheduledWebinars']   = "<?php echo  __( "No webinars scheduled for this week", 'webinarignition' ); ?>";
        webinarignitionTranslations['done']                  = "<?php echo  __( "Done", 'webinarignition' ); ?>";

        if( typeof wiParsed.translations !== 'undefined' ) {
            wiParsed.translations = webinarignitionTranslations;
        }
        
    </script>

    <script src="<?= $assets ?>js/registration-page.js"></script>
<?php endif; ?>


<?php echo $webinar_data->footer_code;?>
</body>
</html>
