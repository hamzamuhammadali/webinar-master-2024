<?php
/**
 * @var $webinar_data
 */
$default_footerArea_content = '<p>' . __( 'Live Console For WebinarIgnition - All Rights Reserved', 'webinarignition') . ' @ ' . date('Y') . '</p>';
$footerArea_content = isset($webinar_data->live_console_footer_area_content) ? $webinar_data->live_console_footer_area_content : $default_footerArea_content;

if ( empty($footerArea_content) ) return '';

if (false !== strpos($footerArea_content, '{{currentYear}}')) {
    $footerArea_content = str_replace('{{currentYear}}', date('Y'), $footerArea_content);
}
?>

<div class="footerArea">
    <?php echo $footerArea_content ?>
</div>
