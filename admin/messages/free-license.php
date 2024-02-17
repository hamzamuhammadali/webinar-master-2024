<?php
defined('ABSPATH') || exit;


$license_version = empty( $statusCheck->switch ) ? 'free' : $statusCheck->switch;
$is_trial        = ( isset($statusCheck->is_trial) && !empty($statusCheck->is_trial) ) ? $statusCheck->is_trial : false;

$opacity_class = !isset( $statusCheck->is_registered ) || !$statusCheck->is_registered ? 'opacity-50' : 'opacity-100';

?>

    <div class="unlockTitle<?php echo !isset($input_get['id']) && !isset($input_get['create']) ? '' : ' noBorder'; ?>">

        <?php
        if (!isset($input_get['id']) && !isset($input_get['create'])) {
            ?>
            <div class="unlockTitle3 free-license-table-container">
                <div style="text-align:center;">
                    <h3><?php esc_html_e('Welcome to WebinarIgnition', 'webinarignition'); ?></h3>
                    <p><?php esc_html_e('You get all features for unlimited time, the only limit is the webinar registrations over all webinars per month.', 'webinarignition' ); ?></p>
                </div>
                <br>
                <table class="free_license_table license_registration_table <?php echo $license_version; ?>">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Status', 'webinarignition'); ?></th>
                            <th><?php esc_html_e('Total Registrations', 'webinarignition'); ?></th>
                            <?php if('free' == $license_version): ?>
                                <th><?php esc_html_e('Attendees Webinar Time', 'webinarignition'); ?></th>
		                    <?php endif; ?>
                            <th><?php esc_html_e('Requirements', 'webinarignition'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <label class="license-table-checkbox-wrap">
                                    <input type="checkbox" class="<?php echo 'enterprise_powerup' == $license_version ? 'active' : 'inactive'; ?>" value="enterprise_powerup" <?php echo checked('enterprise_powerup', $license_version ); ?> disabled>
                                    <span class="geekmark"></span>
                                </label>
                            </td>
                            <td>∞</td>
	                        <?php if('free' == $license_version): ?>
                                <td>∞</td>
	                        <?php endif; ?>
                            <td>
                                <?php esc_html_e('Paid Ultimate', 'webinarignition'); ?>-
                                <a class="wi-dashboard-link" href="<?php echo $statusCheck->upgrade_url; ?>">
                                    <?php esc_html_e('get plan here', 'webinarignition'); ?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="license-table-checkbox-wrap">
                                    <input type="checkbox" class="<?php echo true == $statusCheck->is_free ? 'active' : 'inactive'; ?>" <?php echo checked(true, $statusCheck->is_free ); ?> disabled>
                                    <span class="geekmark"></span>
                                </label>
                            </td>
                            <td>5</td>
	                        <?php if('free' == $license_version): ?>
                                <td>45 <?php esc_html_e('minutes', 'webinarignition'); ?></td>
	                        <?php endif; ?>
                            <td>
                                <?php esc_html_e('By activating the plugin', 'webinarignition'); ?>
                            </td>
                        </tr>
                        <tr class="<?php echo esc_attr( $opacity_class ); ?>">
                            <td>
                                <label class="license-table-checkbox-wrap">
                                    <input type="checkbox" class="<?php echo true == $statusCheck->is_registered ? 'active' : 'inactive'; ?>" <?php echo checked( true, $statusCheck->is_registered ); ?> disabled>
                                    <span class="geekmark"></span>
                                </label>
                            </td>
                            <td>25</td>
	                        <?php if('free' == $license_version): ?>
                                <td>45 <?php esc_html_e('minutes', 'webinarignition'); ?></td>
	                        <?php endif; ?>
                            <td>
                                <?php esc_html_e('By Opt in and confirm email', 'webinarignition'); ?>
                                <?php if( !$statusCheck->is_registered ): ?>
                                    -<a class="wi-dashboard-link" href="<?php echo esc_url( $statusCheck->reconnect_url ); ?>">
                                        <?php esc_html_e('Click here', 'webinarignition'); ?> 
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr class="<?php echo esc_attr( $opacity_class ); ?>">
                            <td>
                                <label class="license-table-checkbox-wrap">
                                    <input type="checkbox" class="<?php echo !empty($is_trial) ? 'active' : 'inactive'; ?>" <?php echo checked( true, !empty($is_trial)); ?> disabled>
                                    <span class="geekmark"></span>
                                </label>
                            </td>
                            <td>250</td>
	                        <?php if('free' == $license_version): ?>
                                <td>45 <?php esc_html_e('minutes', 'webinarignition'); ?></td>
	                        <?php endif; ?>
                            <td>
                                <?php esc_html_e('By Opt-in and start 14 days trial', 'webinarignition'); ?>-
                                <a class="wi-dashboard-link" href="<?php echo $statusCheck->trial_url; ?>&plan_name=ultimate_powerup&checkout=true"><?php esc_html_e('start here', 'webinarignition'); ?></a>
                            </td>
                        </tr>
                        <?php if(empty($is_trial)): ?>
                        <tr class="<?php echo esc_attr( $opacity_class ); ?>">
                            <td>
                                <label class="license-table-checkbox-wrap">
                                    <input type="checkbox" class="<?php echo WebinarignitionLicense::is_branding_enable_and_valid() ? 'active' : 'inactive'; ?>" <?php echo checked( true, WebinarignitionLicense::is_branding_enable_and_valid() ); ?> disabled>
                                    <span class="geekmark"></span>
                                </label>
                            </td>
                            <td>100</td>
	                        <?php if('free' == $license_version): ?>
                                <td>45 <?php esc_html_e('minutes', 'webinarignition'); ?></td>
	                        <?php endif; ?>
                            <td>
                                <?php esc_html_e('By Opt-in and showing branding', 'webinarignition'); ?>-
                                <a class="wi-dashboard-link" href="<?php echo esc_url( admin_url('admin.php?page=webinarignition-dashboard&action=toggle_branding') ); ?>"><?php esc_html_e('Enable/Disable here', 'webinarignition'); ?></a>
                                <a target="_blank" class="wi-dashboard-link" href="https://webinarignition.tawk.help/article/save-earn-with-a-branded-webinarignition-plugin">(<?php esc_html_e('Earn money', 'webinarignition'); ?>)</a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if( !isset( $statusCheck->is_registered ) || !$statusCheck->is_registered ): ?>
                    <div class="opt-in-popup">
                        <a class="btn btn-primary btn-orange popup-btn-opt-in" href="<?php echo esc_url( $statusCheck->reconnect_url ); ?>">
                            <span class="dashicons dashicons-arrow-right-alt"></span>
                            Opt-In
                        </a>
                        <p>
                            <?php esc_html_e('For more free registrations, License Options', 'webinarignition'); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        }
        ?>
    </div>

    <?php
    if (!isset($input_get['id']) && !isset($input_get['create']) && 'free' == $license_version ) {
        ?>
        <div class="unlockTitle noBorder">
            <?php
            //webinarignition_display_upgrade_buttons($statusCheck);

            if (empty($statusCheck->is_trial)) {
                ?>
                <script>
                    (function( $ ) {
                        $(document).ready(function(){
                            $("#unlockFormsContainer").on('shown.bs.collapse', function() {
                                $("#unlockFormsContainer #unlockFormsContainer").remove(); //Remove duplicate element
                                $('html, body').animate({
                                    scrollTop: $(this).offset().top
                                }, 2000);
                            });
                        });
                    })( jQuery );
                </script>
                <button type="button" class="btn btn-link" style="color:white;" data-toggle="collapse" data-target="#unlockFormsContainer" aria-expanded="false" aria-controls="unlockFormsContainer">
                    <?php  _e( 'I have Webinarignition.com key', 'webinarignition' ); ?>
                    (<?php  _e( 'license bought before 01/2021', 'webinarignition' ); ?>)
                </button>
                <?php
            }
            ?>
        </div>
        <?php
            webinarignition_display_manage_license_form($statusCheck);
    }
    ?>
