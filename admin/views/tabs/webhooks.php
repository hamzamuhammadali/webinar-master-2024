<?php
defined( 'ABSPATH' ) || exit;
$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS );
?>
<div class="wrap">
    <h1><?php echo __( 'WebinarIgnition Settings', 'webinarignition' ); ?></h1>
    <?php include_once WEBINARIGNITION_PATH . 'admin/views/setting_tabs.php'; ?>

    <div style="background-color: #FFF; padding:20px 10px 10px 10px; margin:20px 0;">
        <?php
        if( $action === 'edit' ) {
            webinar_ignition_table_list_form();
        } else {
            webinar_ignition_table_list_output();
        }
        ?>
    </div>
</div>