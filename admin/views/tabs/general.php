<?php defined( 'ABSPATH' ) || exit;

/**
* Renders the contents of the settings submenu page
* @since    2.2.7	 *
*/

?>
<div class="wrap">
    <h1><?php echo __( 'WebinarIgnition Settings', 'webinarignition' ); ?></h1>
	<?php include_once WEBINARIGNITION_PATH . 'admin/views/setting_tabs.php'; ?>
    
          <div id="webinarignition-settings-tab" class="container wrap" style="float: left;border: 1px solid #ccd0d4;box-shadow: 0 1px 1px rgba(0,0,0,.04); background: #fff">

            <div class="row">
              <div class="col-xs-12">
                <form id="general_settings" action="" class="form-horizontal" method="post">

                  <h4 style="margin-top:45px; margin-bottom:25px; font-weight:bold;"><?php  _e( 'General Settings', 'webinarignition' ); ?></h4>
                  
                  <p><?php  _e( 'Like the plugin? Become our ambassador and earn cash! Refer new customers to WebinarIgnition by showing the branding on your footer and earn 40% commission on each successful sale you refer! You can sign up for an affiliate link', 'webinarignition' ); ?> <a href="<?php echo admin_url('admin.php'); ?>?page=webinarignition-dashboard-affiliation"><b><?php  _e( 'here', 'webinarignition' ); ?></b></a>.</p>
                  
                  
                  <p><strong>Note: </strong><?php  _e( 'If you like to change the from email address and name, then install any SMTP plugin. Details ', 'webinarignition' ); ?> <a href="https://webinarignition.tawk.help/article/smtp-setup" target="_blank"><b><?php  _e( 'here', 'webinarignition' ); ?></b></a>.</p>
                  <br>


                  <?php if (!empty($statusCheck->account_url)): ?>
                  
                  <div class="form-group">
                    <label class="col-sm-3 control-label"><?php  _e( 'Footer text', 'webinarignition' ); ?></label>
                    <div class="col-sm-9">
                        <textarea name="webinarignition_footer_text" id="webinarignition_footer_text" style="width:100%; height: 75px;" class="" placeholder="<?php _e('{privacy_policy} | {imprint} | © Copyright {year} {site_title}') ?>"><?php echo ! empty( $webinarignition_footer_text ) ? esc_html( $webinarignition_footer_text ) : '{privacy_policy} | {imprint} | © Copyright {year} {site_title}'; ?></textarea> 
                        <span class="help-block"><?php  _e( 'The text to appear in the footer of all WebinarIgnition pages and emails. Available placeholders: {site_title}, {year}, {imprint}, {privacy_policy}, {site_description}', 'webinarignition' ); ?></span>
                        <span class="help-block"><?php  _e( 'To display the footer please copy this shortcode', 'webinarignition' ); ?><code>[webinarignition_footer]</code></span>
                    </div>
                  </div>
                  
                  <?php else: ?>  
                  
                  <div class="form-group">
                    <label class="col-sm-3 control-label"><?php  _e( 'Footer text', 'webinarignition' ); ?></label>
                    <div class="col-sm-9">
                        <textarea name="webinarignition_footer_text" id="webinarignition_footer_text" style="width:100%; height: 75px;" class="" placeholder="{site_title} | © Copyright {year} All rights reserved. {imprint} - {privacy_policy} {site_description}"><?php echo ! empty( $webinarignition_footer_text ) ? esc_html( $webinarignition_footer_text ) : '{site_title} | © Copyright {year} All rights reserved. {imprint} - {privacy_policy} {site_description}'; ?></textarea> 
                        <span class="help-block"><?php  _e( 'The text to appear in the footer of all WebinarIgnition pages and emails. Available placeholders:', 'webinarignition' ); ?> {site_title}, {year}, {imprint}, {privacy_policy}, {site_description}</span>
                        <span class="help-block"><?php  _e( 'To display the footer please copy this shortcode', 'webinarignition' ); ?><code>[webinarignition_footer]</code></span>
                    </div>
                  </div>

                  <?php endif; ?>
                  
                  <div class="form-group">
                    <label class="col-sm-3 control-label"><?php  _e( 'Footer Text color', 'webinarignition' ); ?></label>
                    <div class="col-sm-9">
                       <input type="text" class="form-control color_picker" name="webinarignition_footer_text_color" value="<?php echo ! empty( $webinarignition_footer_text_color ) ? esc_html( $webinarignition_footer_text_color) : '#3f3f3f'; ?>"> 
                    </div>
                  </div>   
                  
                  <?php if (!empty($latest_webinar_permalink)): ?>
                  <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                       <p><a target="_blank" href="<?php echo $latest_webinar_permalink; ?>"><?php  _e( 'Click here to preview your webinar page template', 'webinarignition' ); ?></a>. </p>
                    </div>
                  </div>                     
                  <?php endif; ?>
                  
                  <div id="branding_settings">

                    <div class="form-group">
                      <label class="col-sm-3 control-label"><?php  _e( 'Show WebinarIgnition branding?', 'webinarignition' ); ?></label>
                      <div class="col-sm-9">
                         <input type="checkbox" class="form-control" id="webinarignition_show_footer_branding" name="webinarignition_show_footer_branding" value="<?php echo $webinarignition_show_footer_branding; ?>" <?php echo !empty($webinarignition_show_footer_branding) ? 'checked' : ''; ?>> 
                         <span class="help-block"><?php  _e( 'You can optionally show this text on your WebinarIgnition pages and emails. Useful for affiliate marketing.', 'webinarignition' ); ?></span>
                      </div>
                    </div>   

                    <div id="show_hide_branding_settings" style="display:<?php echo empty( $webinarignition_show_footer_branding ) ? 'none' : 'block'; ?>">

                          <div class="form-group">
                            <label class="col-sm-3 control-label"><?php  _e( 'Branding Copy', 'webinarignition' ); ?></label>
                            <div class="col-sm-9">
                            <?php echo ! empty( $webinarignition_branding_copy ) ? esc_html( $webinarignition_branding_copy ) : __( "Webinar Powered By WebinarIgnition", 'webinarignition'); ?>
                            <br>
                      <span class="help-block"><?php  _e( 'This is what the link says for WebinarIgnition to your affiliate link... "Webinar Powered By WebinarIgnition" text is necessary to available free registrations upto 100.', 'webinarignition' ); ?></span>
                            </div>
                          </div>  
                  
                        <div class="form-group">
                          <label class="col-sm-3 control-label"><?php  _e( 'Branding Background color', 'webinarignition' ); ?></label>
                          <div class="col-sm-9">
                             <input type="text" class="form-control color_picker" name="webinarignition_branding_background_color" value="<?php echo ! empty( $webinarignition_branding_background_color ) ? esc_html( $webinarignition_branding_background_color ) : '#000'; ?>"> 
                      <span class="help-block"><?php  _e( 'Background color for branding. Make sure your branding text is visible to avail free registrations.', 'webinarignition'); ?></span>
                          </div>
                        </div>                         
                  
                        <div class="form-group">
                          <label class="col-sm-3 control-label"><?php  _e( 'Show WebinarIgnition logo in footer?', 'webinarignition' ); ?></label> 
                          <div class="col-sm-9">

                              <div class="row">
                                  <div class="col-sm-9">
                                        <input type="checkbox" class="form-control" id="show_webinarignition_footer_logo" name="show_webinarignition_footer_logo" value="<?php echo $show_webinarignition_footer_logo; ?>" <?php echo !empty($show_webinarignition_footer_logo) ? 'checked' : ''; ?>>   
                                        <span class="help-block"><?php  _e( "You can optionally show WebinarIgnition's logo in your email footer as part of the branding", 'webinarignition' ); ?></span>
                                  </div>    
                              </div>    

                          </div>
                        </div>    
                        

                        <?php if (!empty($statusCheck->account_url)): ?>
                            
                          <div class="form-group">
                            <label class="col-sm-3 control-label"><?php  _e( 'Your Affiliate Link', 'webinarignition' ); ?></label>
                            <div class="col-sm-9">
                               <input type="text" class="form-control" name="webinarignition_affiliate_link" id="webinarignition_affiliate_link" value="<?php echo ! empty( $webinarignition_affiliate_link ) ? esc_html( $webinarignition_affiliate_link ) : 'https://webinarignition.com/'; ?>"> 
                               <span class="help-block"><?php  _e( 'Your freemius affiliate link if you want to earn money from this branding. This can be used in your webinar reminder emails and in email answers to attendee questions.', 'webinarignition' ); ?></span>
                            </div>
                          </div>   
                        
                        <?php else: ?>    
                        
                          <div class="form-group">
                            <label class="col-sm-3 control-label"><?php  _e( 'Your Affiliate Link', 'webinarignition' ); ?></label>
                            <div class="col-sm-9">
                                <a href="/wp-admin/admin.php?page=webinarignition-dashboard-affiliation"><button type="button" class="btn btn-primary"><?php  _e( 'Yes, show more!', 'webinarignition' ); ?></button></a>
                                <span class="help-block"><?php  _e( 'Please activate freemius to join the affiliate program and to avoid page with "Sorry, you are not allowed to access this page."', 'webinarignition' ); ?></span>
                            </div>
                          </div>                          
                            
                        <?php endif; ?>

                    </div>  
                      
                    <div class="form-group">
                      <label class="col-sm-3 control-label"><?php  _e( 'Auto Clean Log Database?', 'webinarignition' ); ?></label>
                      <div class="col-sm-9">
                         <input type="checkbox" class="form-control" id="webinarignition_auto_clean_log_db" name="webinarignition_auto_clean_log_db" value="<?php echo $webinarignition_auto_clean_log_db; ?>" <?php echo !empty($webinarignition_auto_clean_log_db) ? 'checked' : ''; ?>> 
                         <span class="help-block"><?php  _e( 'WebinarIgnition can automatically delete notification logs older than 14 days', 'webinarignition' ); ?></span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label"><?php  _e( 'Allow auto-login on registration?', 'webinarignition' ); ?></label>
                      <div class="col-sm-9">
                         <input type="checkbox" class="form-control" id="webinarignition_registration_auto_login" name="webinarignition_registration_auto_login" value="<?php echo $webinarignition_registration_auto_login; ?>" <?php checked($webinarignition_registration_auto_login == 1, true); ?>>
                         <span class="help-block">
                             <?php  _e( 'Automatically log-in user on webinar registration.', 'webinarignition' ); ?><br>
                             <?php _e('If user email does not found, it will create a new user before auto-login.'); ?>
                         </span>
                      </div>
                    </div>
                    <div class="form-group" id="wi-auto-login-password-email" <?php echo $webinarignition_registration_auto_login != 1 ? 'style="display: none;"' : ''; ?>>
                      <label class="col-sm-3 control-label"><?php  _e( 'Send password email to new auto-login users?', 'webinarignition' ); ?></label>
                      <div class="col-sm-9">
                         <input type="checkbox" class="form-control" id="webinarignition_auto_login_password_email" name="webinarignition_auto_login_password_email" value="<?php echo $webinarignition_auto_login_password_email; ?>" <?php checked($webinarignition_auto_login_password_email == 1, true); ?>>
                         <span class="help-block"><?php  _e( 'Enable to send login details/password reset emails, to new users who got logged-in for the first time.', 'webinarignition' ); ?></span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label"><?php  _e( 'Hide top admin bar from webinar pages?', 'webinarignition' ); ?></label>
                      <div class="col-sm-9">
                         <input type="checkbox" class="form-control" id="webinarignition_hide_top_admin_bar" name="webinarignition_hide_top_admin_bar" value="<?php echo $webinarignition_hide_top_admin_bar; ?>" <?php checked($webinarignition_hide_top_admin_bar == 1, true); ?>>
                         <span class="help-block"><?php  _e( 'Enable to hide admin bar for logged-in users from webinar pages. Users with administrator role can still see it.', 'webinarignition' ); ?></span>
                      </div>
                    </div>

                  </div>

                  <input type="hidden" name="submit-webinarignition-general-settings" value="1">
                  <p>
                    <?php submit_button( __( "Save", 'webinarignition'), 'primary', 'submit-webinarignition-general-settings', false ); ?>
                  </p>

                  <?php wp_nonce_field( 'webinarignition-general-settings-save', 'webinarignition-general-settings-save-nonce' ); ?>

                </form>

              </div>

             </div>
          </div>    
    
</div>



<script>
    jQuery(document).ready(function ($) {
        
        $('.color_picker').wpColorPicker();
 
        const show_hide_branding_settings = $('#show_hide_branding_settings');
        
        $(":checkbox").on('change', function () {
            if(this.checked) { 
                this.value = 1;
            } else {
                this.value = '';
            }
        }); 
        
        $('#webinarignition_show_footer_branding').on('change', function (e) {
            
            if(this.checked) { 
                show_hide_branding_settings.show();
            } else {
                
                show_hide_branding_settings.hide();
            }

        });          
        
        
        const $webinarignition_affiliate_link                = $('input#webinarignition_affiliate_link');
        const $webinarignition_affiliate_link_val_before     = $('input#webinarignition_affiliate_link').val();

        $webinarignition_affiliate_link.on("blur", function(){

          const $webinarignition_affiliate_link_val    = $('input#webinarignition_affiliate_link').val();

          if( $webinarignition_affiliate_link_val.length &&  $webinarignition_affiliate_link_val.indexOf("https://r.freemius.com") < 0 ){
              
              alert('<?php _e( "Your affilliate link should be to freemius!", "webinarignition" ); ?>');

              if($webinarignition_affiliate_link_val_before.indexOf("https://r.freemius.com") < 0 ){
                $webinarignition_affiliate_link.val('');
              } else {
                $webinarignition_affiliate_link.val($webinarignition_affiliate_link_val_before);
              }

          }

        }); 
        
        var $webinarignition_branding_text                  = $('input#webinarignition_branding_copy');
        var acceptedWords                                   = /Webinarignition|WebinarIgnition|webinarignition/;

        $webinarignition_branding_text.on("blur", function(){

          const $webinarignition_branding_text_val    = $('input#webinarignition_branding_copy').val();

          if( $webinarignition_branding_text_val.length &&  ! acceptedWords.test($webinarignition_branding_text_val ) ){
              
              alert('<?php _e( 'Your branding copy should contain "Webinar Powered By WebinarIgnition"!', "webinarignition" ); ?>');
              $webinarignition_branding_text.val('<?php _e( 'Powered By WebinarIgnition', "webinarignition" ); ?>');

          }

        });

        $(document).on('change', '#webinarignition_registration_auto_login', function(e) {
            if( $(this).is(':checked') ) {
                $('#wi-auto-login-password-email').slideDown();
            } else {
                $('#wi-auto-login-password-email').slideUp();
            }
        });

        $('#wi-auto-login-password-email').trigger('change');
        
    });
</script>