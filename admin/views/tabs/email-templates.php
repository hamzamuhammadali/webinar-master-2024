<?php defined( 'ABSPATH' ) || exit;

/**
* Renders the contents of the email templates settings submenu page
*/                

?>


<div class="wrap">
    
            <div class="row">
              <div class="col-xs-12 col-md-8">
                <h2><?php  _e( 'WebinarIgnition Email Templates Settings', 'webinarignition' ); ?></h2>
              </div>
           </div>    
 
            <?php include_once WEBINARIGNITION_PATH . 'admin/views/setting_tabs.php'; ?>
    
            <div id="webinarignition-settings-tab" class="container wrap" style="float: left;border: 1px solid #ccd0d4;box-shadow: 0 1px 1px rgba(0,0,0,.04); background: #fff">


            <div class="row">
              <div class="col-xs-12">
                <form id="emailSettingsForm" action="" class="form-horizontal" method="post">

                  <h4 style="margin-top:45px; margin-bottom:25px; font-weight:bold;"><?php  _e( 'Email Template Settings', 'webinarignition' ); ?></h4>     
                  
                  <p><?php  _e( 'This section lets you customize the WebinarIgnition emails. ', 'webinarignition' ); ?> <a target="_blank" href="<?php echo wp_nonce_url( admin_url( '?preview-webinarignition-template=true' ), 'preview-mail' ) ; ?>"><?php  _e( 'Click here to preview your email template', 'webinarignition' ); ?></a>. </p>
                  <p><strong><?php  _e( 'NB: Placeholders will not be replaced in the preview.', 'webinarignition' ); ?></strong></p>
                  <div id="headerImgSettingsCont">
                  
                        <div class="form-group">
                          <label class="col-sm-3 control-label"><?php  _e( 'Show Header Image In Emails?', 'webinarignition' ); ?></label>
                          <div class="col-sm-9">
                              <button type="button"  data-enable="1" class="btn webinarignition_yes_no_switch <?php echo $webinarignition_show_email_header_img ? 'btn-primary' : ''; ?>"><?php  _e( 'Yes', 'webinarignition' ); ?></button>
                              <button type="button"  data-enable="0"  class="btn webinarignition_yes_no_switch <?php echo $webinarignition_show_email_header_img ? '' : 'btn-primary'; ?>"><?php  _e( 'No', 'webinarignition' ); ?></button>
                             <input type="hidden" class="form-control" id="webinarignition_yes_no_switch" name="webinarignition_show_email_header_img" value="<?php echo $webinarignition_show_email_header_img ? '1' : '0'; ?>"> 
                          </div>
                        </div>  
                      
                      <div id="show_hide_header_settings" style="display:<?php echo empty( $webinarignition_show_email_header_img ) ? 'none' : 'block'; ?>">

                            <div class="form-group">
                              <label class="col-sm-3 control-label"><?php  _e( 'Header Image', 'webinarignition' ); ?></label>
                              <div class="col-sm-9">

                                  <div id="input_image_holder" class="input_image_holder" style="width:70%; margin: 0 auto; float:<?php echo $header_img_algnmnt ? $header_img_algnmnt: 'none'; ?>">
                                          <img src="<?php echo !empty($webinarignition_email_logo_url) ? $webinarignition_email_logo_url : $default_webinarignition_email_logo_url; ?>">
                                  </div>

                                 <input type="text" class="imgUrlField form-control" name="webinarignition_email_logo_url" value="<?php echo ! empty( $webinarignition_email_logo_url ) ? esc_html( $webinarignition_email_logo_url ) : $default_webinarignition_email_logo_url; ?>" placeholder="<?php  _e( 'Header Image URL', 'webinarignition' ); ?>"> 
                                 <span class="help-block"><?php  _e( 'This is your header image url', 'webinarignition' ); ?></span>
                                 <button type="button" class="btn wi_upload_image_btn btn-primary"><?php  _e( 'Choose Image', 'webinarignition' ); ?></button> <button type="button" class="btn wi_delete_image_btn btn-danger"  style="display:<?php echo empty( $webinarignition_email_logo_url ) ? 'none' : 'inline'; ?>"><?php  _e( 'Delete Image', 'webinarignition' ); ?></button>

                              </div>
                            </div>

                            <div class="form-group">
                              <label class="col-sm-3 control-label"><?php  _e( 'Header Image Alignment', 'webinarignition' ); ?></label>
                              <div class="col-sm-9">

                                  <div class="row">
                                      <div class="col-sm-3">
                                            <input type="radio" class="header_img_algnmnt" <?php echo ( ! empty( $header_img_algnmnt ) && $header_img_algnmnt == 'left' ) ? 'checked': ''; ?> name="header_img_algnmnt" value="left">
                                            <label><?php  _e( 'Left', 'webinarignition' ); ?></label>                           
                                              </div>   
                                              <div class="col-sm-3">
                                             <input type="radio" class="header_img_algnmnt" <?php echo ( ! empty( $header_img_algnmnt ) && $header_img_algnmnt == 'none' ) ? 'checked': ''; ?> name="header_img_algnmnt" value="none">
                                            <label><?php  _e( 'Center', 'webinarignition' ); ?></label>                               
                                              </div> 
                                              <div class="col-sm-3">
                                            <input type="radio" class="header_img_algnmnt" <?php echo ( ! empty( $header_img_algnmnt ) && $header_img_algnmnt == 'right' ) ? 'checked': ''; ?> name="header_img_algnmnt" value="right">
                                            <label><?php  _e( 'Right', 'webinarignition' ); ?></label>                                
                                      </div>                             
                                  </div>    

                              </div>
                            </div> 

                            <div class="form-group">
                              <label class="col-sm-3 control-label"><?php  _e( 'Enable max-width on header image?', 'webinarignition' ); ?></label>
                              <div class="col-sm-9">
                                 <input type="checkbox" class="form-control" name="webinarignition_enable_header_img_max_width" <?php echo ($webinarignition_enable_header_img_max_width == 'yes') ? 'checked' : ''; ?>  value="yes"> 
                              </div>
                            </div>
                          
                          
                            <div id="enable_header_img_max_width_div" class="form-group" style="display:<?php echo empty( $webinarignition_enable_header_img_max_width ) ? 'none' : 'block'; ?>">
                              <label class="col-sm-3 control-label"><?php  _e( 'Max-width', 'webinarignition' ); ?></label>
                              <div class="col-sm-9">
                                 <input type="number" class="form-control" name="webinarignition_email_logo_max_width"  value="<?php echo $webinarignition_email_logo_max_width; ?>"> 
                              </div>
                            </div>                          
                          
                          
                      </div>                       
                      
                  </div>                
                  
                  <div class="form-group">
                    <label class="col-sm-3 control-label"><?php  _e( 'Background color', 'webinarignition' ); ?></label>
                    <div class="col-sm-9">
                       <input type="text" class="form-control color_picker" name="webinarignition_email_background_color"  value="<?php echo ! empty( $webinarignition_email_background_color ) ? esc_html( $webinarignition_email_background_color ) : '#ffffff'; ?>"> 
                    </div>
                  </div>
                  
                   
                  <div class="form-group">
                    <label class="col-sm-3 control-label"><?php  _e( 'Headings color', 'webinarignition' ); ?></label>
                    <div class="col-sm-9">
                       <input type="text" class="form-control color_picker" name="webinarignition_headings_color" value="<?php echo ! empty( $webinarignition_headings_color ) ? esc_html( $webinarignition_headings_color ) : '#ffffff'; ?>"> 
                    </div>
                  </div>
                  
                  
                  
                  <div class="form-group">
                    <label class="col-sm-3 control-label"><?php  _e( 'Heading background color', 'webinarignition' ); ?></label>
                    <div class="col-sm-9">
                       <input type="text" class="form-control color_picker" name="webinarignition_heading_background_color" value="<?php echo ! empty( $webinarignition_heading_background_color ) ? esc_html( $webinarignition_heading_background_color ) : '#000000'; ?>"> 
                    </div>
                  </div> 
                  
                  
                  <div class="form-group">
                    <label class="col-sm-3 control-label"><?php  _e( 'Heading text color', 'webinarignition' ); ?></label>
                    <div class="col-sm-9">
                       <input type="text" class="form-control color_picker" name="webinarignition_heading_text_color" value="<?php echo esc_html( $webinarignition_heading_text_color ); ?>"> 
                    </div>
                  </div>                      
                  
                  
                  <div class="form-group">
                    <label class="col-sm-3 control-label"><?php  _e( 'Body Background color', 'webinarignition' ); ?></label>
                    <div class="col-sm-9">
                       <input type="text" class="form-control color_picker" name="webinarignition_email_body_background_color" value="<?php echo ! empty( $webinarignition_email_body_background_color ) ? esc_html( $webinarignition_email_body_background_color ) : '#ededed'; ?>"> 
                    </div>
                  </div>   
                  
                  
                  <div class="form-group">
                    <label class="col-sm-3 control-label"><?php  _e( 'Text color', 'webinarignition' ); ?></label>
                    <div class="col-sm-9">
                       <input type="text" class="form-control color_picker" name="webinarignition_email_text_color" value="<?php echo ! empty( $webinarignition_email_text_color ) ? esc_html( $webinarignition_email_text_color ) : '#3f3f3f'; ?>"> 
                    </div>
                  </div>
                  

                  <div class="form-group">
                    <label class="col-sm-3 control-label"><?php  _e( 'Body text font size', 'webinarignition' ); ?></label>
                    <div class="col-sm-9">
                       <input type="number" class="form-control" name="webinarignition_email_font_size" value="<?php echo ! empty( $webinarignition_email_font_size ) ? esc_html( $webinarignition_email_font_size ) : '14'; ?>"> 
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label class="col-sm-3 control-label"><?php  _e( 'Body text line-height', 'webinarignition' ); ?></label>
                    <div class="col-sm-9">
                       <input type="text" class="form-control" name="webinarignition_body_text_line_height" value="<?php echo esc_html( $webinarignition_body_text_line_height ) ; ?>"> 
                       <span class="help-block"><?php  _e( "Example values: 'normal', '1.6', '80%', '200%'", 'webinarignition' ); ?></span>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label"><?php  _e( 'Email Signature', 'webinarignition' ); ?></label>
                    <div class="col-sm-9">
                        <?php  wp_editor( $webinarignition_email_signature, 'webinarignition_email_signature', $wp_editor_settings ); ?>
                    </div>
                  </div>                   

                  <input type="hidden" name="submit-webinarignition-email-templ-settings" value="1"> 
                  <p>
                    <?php submit_button( __( "Save", 'webinarignition'), 'primary', 'submit-webinarignition-template-settings', false ); ?> 
                  </p>

                  <?php wp_nonce_field( 'webinarignition-template-settings-save', 'webinarignition-template-settings-save-nonce' ); ?>

                </form>

              </div>

             </div>                
                
                

            </div>    

    
</div>

<script>
    
jQuery(document).ready(function($){
    
        const form                          = $('#emailSettingsForm');
        const header_img_settings_container = $('#headerImgSettingsCont');
        const show_hide_header_settings   = $('#show_hide_header_settings');
        
        $('.webinarignition_yes_no_switch').on('click', function (e) {
             
                    if( $(this).hasClass( 'btn-primary') ) { return; }

                    var input = header_img_settings_container.find("input[type=hidden]");
                    header_img_settings_container.find('.webinarignition_yes_no_switch').toggleClass('btn-primary');

                    if( $(this).data('enable') === 1) { 

                         show_hide_header_settings.show();
                         $(input).val( 1 );

                    } else { 

                        show_hide_header_settings.hide();
                        $(input).val( 0 ); 
                     }               

        });     
    
    
    $('.color_picker').wpColorPicker();
    
    
    $(document.body).on('click', '.wi_upload_image_btn', function() {
        
                    var btn             = $(this);
                    var container       = btn.parents('.form-group');
                    var img_holder      = container.find('#input_image_holder');
                    var input           = container.find('.imgUrlField');
                    var delete_btn      = container.find('.wi_delete_image_btn');

                    var custom_uploader = wp.media({
                        title: '<?php _e( "Insert image", "webinarignition" ); ?>',
                        library : {
                            type : 'image'
                        },
                        button: {
                            text: '<?php _e( "Use this image", "webinarignition" ); ?>'  
                        },
                        multiple: false  
                    }).on('select', function() {
                        var attachment = custom_uploader.state().get('selection').first().toJSON();
                        var url = attachment.url;

                        img_holder.html('<img src="' + attachment.url + '" />');
                        input.val(url);
                        delete_btn.show();
                    }).open();
        
    });   
    
    $(document.body).on('click', '.wi_delete_image_btn', function() {
    
                    var btn             = $(this);
                    var container       = btn.parents('.form-group');
                    var img_holder      = container.find('#input_image_holder');
                    var input           = container.find('.imgUrlField');

                    btn.hide();
                    img_holder.empty();
                    input.val('');
                    
    });    
    
    $(document.body).on('change', 'input[name=header_img_algnmnt]', function() {
        
                    var btn                 =   $(this);
                    var newValue            =   btn.val();
                    var input_image_holder  = $('#input_image_holder');

                    if( 'center' ===  newValue ){
                        input_image_holder.css( 'float', 'none' );
                    } else {
                        input_image_holder.css( 'float', newValue );
                    }
        
    });
    
    
    $(document.body).on('change', 'input[name=webinarignition_enable_header_img_max_width]', function() {
        
                    var btn               =   $(this);
                    var max_width_div     =   $('#enable_header_img_max_width_div');

                    if (btn.is(":checked")) {
                       max_width_div.show();
                    } else {
                        max_width_div.hide();
                    }
        
    });      
    
    
    
});   
    
</script>