<?php defined( 'ABSPATH' ) || exit; ?>
<?php
// Get ID
//$ID = $client;
$input_get     = filter_input_array(INPUT_GET);
$webinarId = !empty($input_get['trkorder']) ? trim($input_get['trkorder']) : $client;
if (!$webinarId) {
    wp_send_json('no webinar id');
}
?>
<script src="<?= WEBINARIGNITION_URL ?>inc/lp/js/jquery.js"></script>
<script src="<?= WEBINARIGNITION_URL ?>inc/lp/js/cookie.js"></script>

<script type="text/javascript">
    jQuery(document).ready(function () {
        $.post('<?= admin_url('admin-ajax.php'); ?>', {
            action: 'webinarignition_track_order',
            security:       '<?php echo wp_create_nonce("webinarignition_ajax_nonce"); ?>',
            id: <?= $webinarId; ?>,
            lead: $.cookie('we-trk-<?= $webinarId ?>')
        }, function (results) {
            // do nothing
        });
    });
</script>