<?php defined( 'ABSPATH' ) || exit;  

    $footer_copy            = get_option( 'webinarignition_footer_text' );
    $footer_copy            = str_replace( "{site_title}", get_bloginfo( 'name' ), $footer_copy );
    $footer_copy            = str_replace( "{year}", date( "Y" ), $footer_copy );
    $footer_copy            = str_replace( "{site_description}", get_bloginfo( 'description' ), $footer_copy );
    $privacy_policy_link    = get_privacy_policy_url();
    $privacy_policy         = '<a href="'.$privacy_policy_link.'" target="_blank">'.__( "Privacy Policy", "webinarignition").'</a>';
    $footer_copy            = str_replace( "{privacy_policy}", $privacy_policy, $footer_copy );
    $imprint_page           = wi_get_page_by_title('Imprint');
    $imprint_page           =  empty( $imprint_page ) ? wi_get_page_by_title('Impressum') : $imprint_page;
    $imprint_page_url       = !empty( $imprint_page ) ? get_permalink($imprint_page->ID) : '';
    $imprint_page_link      = '<a href="'.$imprint_page_url.'" target="_blank">'.__( "Imprint", 'webinarignition').'</a>';    
    $footer_copy            = is_object($imprint_page) ?  str_replace( "{imprint}", $imprint_page_link, $footer_copy ) :  str_replace( "{imprint}", ' ', $footer_copy ); 

?>

<!-- BOTTOM AREA -->
<div class="bottomArea">
    <div class="wiContainer container">
        
        <div><?php echo $footer_copy; ?></div>
        
        <?php  if( get_option( 'webinarignition_show_footer_branding' ) ) { ?>
                <div style="margin-top: 15px;"><a href="<?php echo get_option( 'webinarignition_affiliate_link' ); ?>"  target="_blank"><b><?php echo get_option( 'webinarignition_branding_copy' ); ?></b></a> </div>
            <script>
                setTimeout(() => {
                    $('.autoReplay-dimensions').append('<a href="https://webinarignition.com/" target="_blank"><img style="position: absolute; z-index: 99999999999; bottom: 24px; width: 47px; right: 11px;" src="<?php echo WEBINARIGNITION_URL; ?>images/watermark-webinar.png" /></a>');
                }, 1000);

            </script>
        <?php }  ?>
        
    </div>
</div>