<?php defined( 'ABSPATH' ) || exit;

add_action('admin_enqueue_scripts', 'webinarignition_enqueue_admin_scripts');
function webinarignition_enqueue_admin_scripts($hook) {

    wp_enqueue_script( 'webinarignition-admin-notice-js', WEBINARIGNITION_URL . 'inc/js/admin-notice.js', array('jquery'), '1.0.0', true );
    wp_localize_script( 'webinarignition-admin-notice-js', 'wi_notice_var', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('webinarignition-notice')
    ));

      $assets         = WEBINARIGNITION_URL . "inc/lp/";
      $current_screen = get_current_screen();

    if(
        ($current_screen->base === 'webinarignition_page_webinarignition_settings') ||
        ( $current_screen->base === 'webinarignition_page_webinarignition_welcome' ) ||
        ( $current_screen->base === 'webinarignition_page_webinarignition_changelog' )
    ): 
           wp_enqueue_style( 'webinarignition-admin',          WEBINARIGNITION_URL . 'css/webinarignition-admin.css', [], '2.2.9' );
    endif;

    wp_enqueue_media();

    if( isset( $_GET['id'] ) ){
        $webinar_data           = WebinarignitionManager::get_webinar_data( $_GET['id'] );
        if ( !empty( $webinar_data->webinar_lang )  ) { 
            $applang = $webinar_data->webinar_lang;
            switch_to_locale( $applang ); 
            unload_textdomain( 'webinarignition' );
            load_textdomain( 'webinarignition', WEBINARIGNITION_PATH . 'languages/webinarignition-' . $applang . '.mo' );            
        }         
    }

    if ( $current_screen->base === 'toplevel_page_webinarignition-dashboard' ):
        //<head> css
        wp_enqueue_style( 'google-font',      '//fonts.googleapis.com/css?family=Source+Sans+Pro:400,600' );
        wp_enqueue_style( 'webinarignition-admin',          WEBINARIGNITION_URL . 'css/webinarignition-admin.css', [], '2.0.84-' . time() );
        wp_enqueue_script('wp-color-picker');
	    wp_enqueue_style( 'wp-color-picker' );

	    wp_add_inline_script(
		    'wp-color-picker',
		    'jQuery( function() {		         
                    jQuery( ".cp-picker" ).wpColorPicker({
                        clear: function() {
                            jQuery(this).prev().find(\'.cp-picker\').val( \'transparent\' );
                        }
                    }); 
		        } );'
	    );
        wp_enqueue_script( 'webinarignition-admin-js',      WEBINARIGNITION_URL . 'inc/js/webinarignition-admin.js', ['jquery', 'jquery-ui-sortable'], '2.0.1-' . time(), false );
        wp_enqueue_editor();
	    wp_enqueue_script( 'webinarignition_moment',   WEBINARIGNITION_URL . 'inc/js/moment/moment-with-locales.min.js' );
	    wp_enqueue_script( 'webinarignition_moment_tz',   WEBINARIGNITION_URL . 'inc/js/moment/moment-timezone-with-data-10-year-range.min.js' );
	    wp_enqueue_script( 'webinarignition-admin-custom-js',      WEBINARIGNITION_URL . 'inc/js/webinarignition-admin-custom.js', ['webinarignition-admin-js'], '2.0.1-' . time(), true );
        
        $privacy_policy_link    = get_privacy_policy_url();
        $privacy_policy         = '<a href="'.$privacy_policy_link.'" target="_blank">'.__( "Privacy Policy", "webinarignition").'</a>';        
        
        wp_localize_script( 'webinarignition-admin-custom-js', 'webinarignitionTranslations', array(
            'wpMediaImgTitle'           => __( 'Insert image', 'webinarignition' ),
            'wpMediaImgButtonText'      => __( 'Use this image', 'webinarignition' ),
            'wpMediaVidTitle'           => __( 'Insert video', 'webinarignition' ),
            'wpMediaVidButtonText'      => __( 'Use this video', 'webinarignition' ),
            'someWrong'                 => __( 'Something went wrong', 'webinarignition' ),
            'monthsArray'               => WiDateHelpers::get_locale_months(),
            'weekdaysFull'              => WiDateHelpers::get_locale_days(),
            'weekdaysShort'             => WiDateHelpers::get_locale_weekday_abbrev(),
            'today'                     => __( 'Today', 'webinarignition' ),
            'clear'                     => __( 'clear', 'webinarignition' ),
            'close'                     => __( 'close', 'webinarignition' ),
            'mailingList'               => __( 'Want to be added to our mailing list', 'webinarignition' ),
            'privacyPolicy'             => __( 'Understand our', 'webinarignition' ) . ' ' . $privacy_policy,
            'termsAndConditions'        => __( 'Agree to our <a href="https://example.com" target="_blank">Terms and Conditions</a>', 'webinarignition' )
            
        ) );        
        
    endif;
    
    if ( !empty( $webinar_data ) && !empty( $webinar_data->webinar_lang ) ) { restore_previous_locale(); } 
}



function webinarignition_test_smtp_phpmailer( $webinarignition_smtp_host,  $webinarignition_smtp_port, $webinarignition_smtp_user,  $webinarignition_smtp_pass){

                $return_array = [];
                global $phpmailer;

                      if ( ! ( $phpmailer instanceof PHPMailer\PHPMailer\PHPMailer ) ) {
                              require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
                              require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
                              require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
                              $phpmailer = new PHPMailer\PHPMailer\PHPMailer( true );
                      }

                /*SMTP Test: See https://github.com/PHPMailer/PHPMailer/blob/master/examples/smtp_check.phps*/
                date_default_timezone_set('Etc/UTC');
                $smtp = $phpmailer->getSMTPInstance();

                try {
                    //Connect to an SMTP server
                    if (!$smtp->connect($webinarignition_smtp_host, $webinarignition_smtp_port)) {
                        throw new Exception(__( 'SMTP Connection Test Failed.', "webinarignition"));
                    }
                    //Say hello
                    if (!$smtp->hello(gethostname())) {
                        throw new Exception( __( 'EHLO failed: ', "webinarignition") . $smtp->getError()['error']);
                    }
                    //Get the list of ESMTP services the server offers
                    $e = $smtp->getServerExtList();
                    //If server can do TLS encryption, use it
                    if (is_array($e) && array_key_exists('STARTTLS', $e)) {
                        $tlsok = $smtp->startTLS();
                        if (!$tlsok) {
                            throw new Exception( __( 'Failed to start encryption: ', "webinarignition") . $smtp->getError()['error']);
                        }
                        //Repeat EHLO after STARTTLS
                        if (!$smtp->hello(gethostname())) {
                            throw new Exception( __( 'EHLO (2) failed: ', "webinarignition") . $smtp->getError()['error']);
                        }
                        //Get new capabilities list, which will usually now include AUTH if it didn't before
                        $e = $smtp->getServerExtList();
                    }
                    //If server supports authentication, do it (even if no encryption)
                    if (is_array($e) && array_key_exists('AUTH', $e)) {
                        if ($smtp->authenticate($webinarignition_smtp_user, $webinarignition_smtp_pass)) {

                            $return_array['message'] = __( 'SMTP Connection Test Successful!', "webinarignition");
                            $return_array['status'] = 1;

                        } else {
                            throw new Exception( __( 'Authentication failed: ', "webinarignition") . $smtp->getError()['error']);
                        }
                    }
                } catch (Exception $e) {

                            $return_array['message'] = __( 'SMTP error: ', "webinarignition") . $e->getMessage();
                            $return_array['status'] = 0;

                }

                $smtp->quit();

                return $return_array;

}


/**
 * Check if saved Webinarignition SMTP settings are still valid. If not, disable them.
 *
 */

function webinarignition_test_smtp_options() {

    $option_smtp_host                = get_option( 'webinarignition_smtp_host' );
    $option_smtp_port                = get_option( 'webinarignition_smtp_port' );
    $option_smtp_user                = get_option( 'webinarignition_smtp_user' );
    $option_smtp_pass                = get_option( 'webinarignition_smtp_pass' );
    $can_use_smtp                    = get_option( 'webinarignition_smtp_connect' );

    if( !empty( $can_use_smtp ) &&  !empty( $option_smtp_host ) &&  !empty( $option_smtp_port ) && !empty( $option_smtp_user ) && !empty( $option_smtp_pass ) && !empty($can_use_smtp) ){

        $smtp_test_results_array = webinarignition_test_smtp_phpmailer( $option_smtp_host,  $option_smtp_port, $option_smtp_user,  $option_smtp_pass );

        if( is_array($smtp_test_results_array) &&  isset($smtp_test_results_array['status'] )  && ( $smtp_test_results_array['status'] == 0 ) ){

            update_option( 'webinarignition_smtp_connect',   0  );              //don't use wi smtp settings
            update_option( 'webinarignition_smtp_credentials_failed',   1  );   //show global smtp-failed notice

            return false;
        }

    }

}

function webinarignition_display_only_ultimate_upgrade($statusCheck, $webinar_data = null) {
    ?>
    <p><?php echo __( "Available only in Ultimate version.", 'webinarignition' ); ?></p>
    <?php
    webinarignition_display_upgrade_buttons($statusCheck);
}

function webinarignition_display_upgrade_buttons($statusCheck) {
    if (!empty($statusCheck->upgrade_url)) {
        ?>
        <a href="<?php echo $statusCheck->upgrade_url; ?>" class="btn btn-success">
            <?php  _e( 'Upgrade', 'webinarignition' ); ?>
        </a>
        <a href="https://webinarignition.com/#pricing" target="_blank" class="btn btn-warning">
            <?php  _e( 'Compare', 'webinarignition' ); ?>
        </a>
        <?php
    }

    if (!empty($statusCheck->trial_url)) {
        ?>
        <a href="<?php echo $statusCheck->trial_url; ?>&plan_name=ultimate_powerup&checkout=true" class="btn btn-info">
            <?php  _e( 'Try 30 days trial', 'webinarignition' ); ?>
        </a>
        <?php
    }
}