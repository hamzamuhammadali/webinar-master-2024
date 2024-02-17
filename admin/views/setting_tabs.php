<?php
$tab        = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_SPECIAL_CHARS );
$active_tab = isset( $tab ) ? $tab : 'general';

$tab_url = add_query_arg('page', 'webinarignition_settings', admin_url('admin.php'));

$statusCheck = WebinarignitionLicense::get_license_level();
?>

<div class="nav-tab-wrapper">
	<a href="<?php echo add_query_arg('tab', 'general', $tab_url); ?>" class="nav-tab <?php echo ($active_tab === 'general') ? 'nav-tab-active' : ''; ?>"><?php  _e( 'General', 'webinarignition' ); ?></a>
	<!--<a href="<?php /*echo add_query_arg('tab', 'smtp-settings', $tab_url); */?>" class="nav-tab <?php /*echo ($active_tab === 'smtp-settings') ? 'nav-tab-active' : ''; */?>">SMTP</a>-->
	<a href="<?php echo add_query_arg('tab', 'spam-test', $tab_url); ?>" class="nav-tab <?php echo ($active_tab === 'spam-test') ? 'nav-tab-active' : ''; ?>"><?php _e( 'Email Spammyness', 'webinarignition' ); ?></a>
	<a href="<?php echo add_query_arg('tab', 'email-templates', $tab_url); ?>" class="nav-tab <?php echo ($active_tab === 'email-templates') ? 'nav-tab-active' : ''; ?>"><?php  _e( 'Email Templates', 'webinarignition' ); ?></a>
    <?php
    if( ( !defined('WEBINAR_IGNITION_DISABLE_WEBHOOKS') || WEBINAR_IGNITION_DISABLE_WEBHOOKS === false ) && (WebinarignitionPowerups::is_ultimate() || ( isset( $statusCheck->is_trial ) && $statusCheck->is_trial ) ) ) {
    ?>
	<a href="<?php echo add_query_arg('tab', 'webhooks', $tab_url); ?>" class="nav-tab <?php echo ($active_tab === 'webhooks') ? 'nav-tab-active' : ''; ?>"><?php  _e( 'Webhooks', 'webinarignition' ); ?></a>
	<?php
    }
	?>
</div>
