<?php
/**
 * Admin View: Email Template Preview
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div style="margin-bottom: 40px;">
    <table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
        <tr>
            <td class="bg_white">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td class="bg_white">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td>
                                        <div class="heading-section">
                                            <p><?php  _e( 'Hi {FIRSTNAME}.' ); ?></p>

                                            <p><?php  _e( '%%INTRO%%' ); ?></p>

                                            <p><?php  _e( 'Date: Join us live on {DATE}' ); ?></p>

                                            <p><?php  _e( 'Webinar Topic: {TITLE}' ); ?></p>

                                            <p><?php  _e( 'Hosts: {HOST}' ); ?></p>

                                            <p><strong><?php  _e( 'How To Join The Webinar' ); ?></strong></p>

                                            <p><?php  _e( 'Click the following link to join.' ); ?></p>

                                            <p style="text-align:center;"><a target = "_blank" href="/"><?php _e( 'Join the webinar','webinarignition' ); ?></a></p>

                                            <p><?php  _e( 'You will be connected to video via your browser using your computer, tablet, or mobile phone\'s microphone and speakers. A headset is recommended.' ); ?></p>

                                            <p><strong><?php  _e( 'Webinar Requirements' ); ?></strong></p>

                                            <p><?php  _e( 'A recent browser version of Mozilla Firefox, Google Chrome, Apple Safari, Microsoft Edge or Opera.' ); ?></p>

                                            <p><?php  _e( 'You can join the webinar on mobile, tablet or desktop.' ); ?></p>

                                        </div>
                                    </td>
                                </tr>

                            </table>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>

    </table>
</div>    

