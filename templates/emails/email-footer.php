<?php defined( 'ABSPATH' ) || exit; ?>                


                                        </td><!-- #content_cell -->
                                    </tr><!-- #content_row -->

                                    <?php if( !empty( $email_data ) &&  !empty( $email_data->footerContent )  ):  ?>
                                        <?php echo $email_data->footerContent ; ?>
                                    <?php else: ?>

                                    <?php  if( get_option( 'webinarignition_show_footer_branding' ) ) { ?>
                                        <tr id="footer_row">
                                            <td style="padding:30px;text-align:center;font-size:12px;color:#fff;">

                                                    <a href="<?php echo get_option( 'webinarignition_affiliate_link' ); ?>"  target="_blank">
                                                        <p><?php echo get_option( 'webinarignition_branding_copy' ); ?></p>
                                                        <?php  if( ($show_webinarignition_footer_logo == 'yes') || ( $show_webinarignition_footer_logo == '1' ) ) { ?><img alt="WebinarIgnition Logo" border="0" class="welogo" src="<?php echo WEBINARIGNITION_URL . 'images/wi-logo.png'; ?>" width="284"><?php }  ?>
                                                    </a>

                                            </td>
                                        </tr>
                                    <?php }  ?>

                                    <?php endif; ?>
                                        
                                    <tr id="webinarignition_email_signature">
                                        <td>
                                            <?php echo wpautop(get_option( 'webinarignition_email_signature', '' ), true)  ; ?>            
                                        </td>
                                    </tr>                                        

                                    <tr id="credit"> 
                                        <td style="padding:30px;text-align:center;font-size:12px;">
                                            <?php echo wp_kses_post( wpautop( wptexturize( apply_filters( 'webinarignition_email_footer_text', get_option( 'webinarignition_footer_text', '{site_title} | © Copyright {year} All rights reserved. {imprint} - {privacy_policy} {site_description}' ) ) ) ) ); ?>
                                        </td>
                                    </tr>

                                </table>
                            <!--[if mso]>
                            </td>
                            </tr>
                            </table>
                            <![endif]-->


                    </td>
                </tr>
            </table>


        </div>
    </body>
</html>
