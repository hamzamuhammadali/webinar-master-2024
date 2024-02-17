<?php
/**
 * @var $webinar_data
 * @var $uid
 * @var $is_compact
 */
defined( 'ABSPATH' ) || exit;
WebinarignitionManager::set_locale($webinar_data);
?>
<div class="eventDate <?php echo $uid; ?>" style="border:none; margin:0px; padding: 0 0 10px 0;">

    <div class="wiFormGroup wiFormGroup-lg">
        <?php
        if (!$is_compact) {
            ?>
            <label for="webinar_start_date">
                <h4 class="autoTitle">
                    <?php webinarignition_display(
                        $webinar_data->auto_translate_headline1,
                        __("Choose a Date To Attend... ", "webinarignition")
                    ); ?>
                </h4>
                <h5 class="autoSubTitle">
                    <?php webinarignition_display(
                        $webinar_data->auto_translate_subheadline1,
                        __("Select a date that best suits your schedule...", "webinarignition")
                    ); ?>
                </h5>
            </label>
            <?php
        }
        ?>
        <select id="webinar_start_date" class="wiFormControl">
            <option value="none"><?php _e( 'Loading Times...', 'webinarignition' ); ?></option>
        </select>
    </div>

    <div class="autoSep" <?= $webinar_data->auto_today == "yes" ? 'style="display: none;"' : '' ?> ></div>
    <div id="webinarTime" <?= $webinar_data->auto_today == "yes" ? 'style="display: none;"' : '' ?> >
        <div class="wiFormGroup wiFormGroup-lg">
            <?php
            if (!$is_compact) {
                ?>
                <label for="webinar_start_time">
                    <h4 class="autoTitle"><?php webinarignition_display( $webinar_data->auto_translate_headline2, __("What Time Is Best For You?", "webinarignition") ) ?></h4>
                </label>
                <?php
            }
            ?>

            <select id="webinar_start_time" class="wiFormControl">
                <?php                
                
                $webinar_times = [];
                
                if ( $webinar_data->auto_time_1 !== "no" ) {
                    $webinar_times[] = $webinar_data->auto_time_1;
                }                
                
                if ( $webinar_data->auto_time_2 !== "no" ) {
                    $webinar_times[] = $webinar_data->auto_time_2;
                }

                if ( $webinar_data->auto_time_3 !== "no" ) {
                   $webinar_times[] = $webinar_data->auto_time_3;
                }                 
                
                $is_multiple_auto_time_enabled = WebinarignitionPowerups::is_multiple_auto_time_enabled($webinar_data);
                
                if ($is_multiple_auto_time_enabled && !empty($webinar_data->multiple__auto_time)) {
                    foreach ($webinar_data->multiple__auto_time as $index => $item) {
                        if ( $item !== "no" ) {
                            $webinar_times[] = $item;
                        }
                    }
                }

                $webinar_times = array_unique($webinar_times);
                
                usort( $webinar_times, function(  $time1, $time2){
                  return ( strtotime($time1)  < strtotime($time2) ) ? -1 : 1;
                }  );               
              
                foreach ($webinar_times as $index => $item) {
                    echo "<option value='{$item}'>" . webinarignition_auto_custom_time($webinar_data, $item) . "</option>";
                }                

                ?>
            </select>
        </div>
    </div>
    <input type="hidden" id="timezone_user" value="<?= $webinar_data->auto_timezone_type === 'fixed' ? $webinar_data->auto_timezone_custom : '' ?>">
    <input type="hidden" id="today_date" value="<?php echo date( 'Y-m-d' ); ?>">
</div>

<?php
WebinarignitionManager::restore_locale($webinar_data);

$order_id = WebinarignitionManager::is_paid_webinar($webinar_data) && WebinarignitionManager::get_paid_webinar_type($webinar_data) === 'woocommerce' && WebinarignitionManager::url_has_valid_wc_order_id();
global $wpdb;

if($order_id) {
	$user = WebinarignitionManager::get_user_from_wc_order_id();
} else {
	$user = wp_get_current_user();
}

$selected_date = $selected_time = $selected_datetime = null;
$user_id = 0;
if( !empty($user) && isset($user->user_email) && !empty($user->user_email) ) {
	$user_id = $user->ID;
}
?>
<script>
    (function ($) {
        'use strict';
        var wi_webinar_id    = '<?php echo $webinar_data->id; ?>';
        var wi_user_id       = '<?php echo $user_id; ?>';
        var wi_cookie_name   = 'wi_selected_date' + '_' + wi_webinar_id + '_' + wi_user_id;
        var wi_selected_date = '';
        var wi_selected_time = '';

        /**
         * When the DOM is ready:
         */
        $(function () {
            var wi_selected_datetime = null;
            if(typeof $.cookie === "function") {
                wi_selected_datetime = $.cookie(wi_cookie_name);
            }
            if( wi_selected_datetime ) {
                wi_selected_datetime = wi_selected_datetime.split(' ');
                wi_selected_date     = wi_selected_datetime[0];
                wi_selected_time     = wi_selected_datetime[1];
            }

            $('#webinar_start_date').one('DOMSubtreeModified', function(){
                setTimeout(function() {

                    <?php if($order_id): ?>
                    $("#webinar_start_date").val(wi_selected_date).change();
                    $("#webinar_start_time").val(wi_selected_time).change();
                    $.removeCookie(wi_cookie_name);
                    <?php endif; ?>

                    $('#webinar_start_date, #webinar_start_time').change(function(e) {
                        wi_selected_date = $('#webinar_start_date').val();
                        wi_selected_time = $('#webinar_start_time').val();
                        if( wi_selected_date !== 'instant_access' ) {
                            wi_selected_date += ' ';
                            wi_selected_date += wi_selected_time;
                        }

                        $.cookie( wi_cookie_name, wi_selected_date, {expires:1} );
                    });

                }, 100);
            });
        });
    })(jQuery);
</script>
