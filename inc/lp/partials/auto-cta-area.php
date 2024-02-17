<?php defined( 'ABSPATH' ) || exit;
/**
 * @var $webinar_data
 */
?>

<?php include WEBINARIGNITION_PATH . "inc/lp/partials/main-cta.php"; ?>
<?php
//Include this file only for classic template
$statusCheck = WebinarignitionLicense::get_license_level();
$webinar_template = !empty($webinar_data->webinar_template) ? $webinar_data->webinar_template : 'classic';
if( !in_array($statusCheck->switch, ['pro','basic']) && 'classic' === $webinar_template ) {
	include WEBINARIGNITION_PATH . "inc/lp/partials/additional-cta.php";
}
?>