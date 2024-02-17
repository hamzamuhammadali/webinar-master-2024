<?php defined( 'ABSPATH' ) || exit;

/**
* Renders the contents of the settings submenu page
* @since    2.2.7	 *
*/

?>



<div class="wrap">
    
            <div class="row">
              <div class="col-xs-12 col-md-8">
                <h2><?php  _e( 'WebinarIgnition Spammyness Test', 'webinarignition' ); ?></h2>
              </div>
           </div>

	<?php include_once WEBINARIGNITION_PATH . 'admin/views/setting_tabs.php'; ?>
    
          <div id="webinarignition-settings-tab" class="container wrap" style="float: left;border: 1px solid #ccd0d4;box-shadow: 0 1px 1px rgba(0,0,0,.04); background: #fff">

              <?php if ( isset( $emailSent )  && ( $emailSent === true ) ): ?>
              

                <div class="row">
                  <div class="col-xs-12 col-md-8">
                    <div id="message" class="notice notice-success is-dismissible">
                        <p><?php _e( "Your spam test email from WebinarIgnition was successfully sent. Go back to mail-tester.com and click on 'Then check your score' to see the results.", 'webinarignition' );  ?></p>
                    </div>
                  </div>
                </div>              

              <?php elseif( isset( $emailSent )  && ( $emailSent === false )  ): ?>

                <div class="row">
                  <div class="col-xs-12 col-md-8">
                    <div id="message" class="notice notice-warning is-dismissible">
                        <p><?php  _e( "Your spam test email could not be sent. There seems to be a problem with your server.", 'webinarignition' ); ?></p>
                    </div>
                  </div>
                </div>              

              <?php endif; ?>

           <div class="row">

          </div>

            <div class="row">
              <div class="col-xs-12 col-md-8">
                <form action="" method="post">

                  <h4><?php  _e( "Test The Spammyness Of Your Email", 'webinarignition' ); ?></h4>
                  
                  <p><?php  _e( 'Get your test email address at', 'webinarignition' ); ?> <a href="https://www.mail-tester.com/?lang=<?php  echo $locale; ?>" target="_blank">mail-tester.com</a> <?php  _e( 'Copy and paste the email address from mail-tester.com into the field below, then click the "Send Email" button. Then go back to the Mail-tester.com website and click the "Then check your score" button to see the results of the spammyness test.', 'webinarignition' ); ?></p>

                  <div class="form-group">
                    <label for="webinarignition_spam_test_email"><?php  _e( 'Mail-tester email address', 'webinarignition' ); ?></label>
                    <input type="email" class="form-control" name="webinarignition_spam_test_email" value="" placeholder="<?php  _e( "Insert test email address from mail-tester.com", 'webinarignition' ); ?>">
                  </div>     

                  <p>
                    <?php submit_button( __( "Send Email", 'webinarignition'), 'primary', 'submit-webinarignition-settings', false ); ?>
                  </p>

                  <?php wp_nonce_field( 'webinarignition-spam-test-save', 'webinarignition-spam-test-save-nonce' ); ?>

                </form>

              </div>

             </div>
          </div>    
    
</div>

