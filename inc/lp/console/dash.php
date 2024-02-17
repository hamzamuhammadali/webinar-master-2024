<?php

/**
 * @var $is_host
 * @var $is_support
 * @var $webinar_id
 * @var $webinar_data
 * @var $post
 */
?>
<!-- DASHBOARD AREA -->
<div id="dashboardTab" class="consoleTabs">

    <div class="statsDashbord">

        <div class="statsTitle statsTitle-Dassh">

            <div class="statsTitleIcon">
                <i class="icon-cogs icon-2x"></i>
            </div>

            <div class="statsTitleCopy">
                <?php if ($webinar_data->webinar_date == "AUTO") {
                    echo '<h2>Auto Webinar Console Dashboard</h2>';
                } else {
                    ?>
                    <h2><?php _e( 'Live Console Dashboard', 'webinarignition') ?></h2>
                <?php } ?>
                <p><?php _e( 'Overview of your webinar campaign...', 'webinarignition') ?></p>
            </div>

            <div class="statsTitleEvent">
                <span class="infoLabel"><?php _e( 'Webinar Title', 'webinarignition') ?>:</span>
                <span class="infoLabelInner"><?php echo esc_attr($data->appname); ?></span>
            </div>

            <br clear="all"/>

        </div>

    </div>

    <div class="innerOuterContainer">

        <div class="innerContainer">
            
        <?php if ( (current_user_can( 'manage_options' ) ) && ($webinar_data->webinar_date != "AUTO") ) { ?>   
            
            <div class="dash-wrapper-left">
                <ul class="webinarStatus">
                    <li><a href="#" class="webinarStatus <?php echo ($webinar_data->webinar_switch == "countdown"  || $webinar_data->webinar_switch == "")  ? "webinarStatusSelected"  : "";  ?>" data="countdown"><i class="icon-time"></i>    <?php  _e( 'Countdown', 'webinarignition' ); ?></a></li>
                    <li><a href="#" class="webinarStatus <?php echo ($webinar_data->webinar_switch == "live")       ? "webinarStatusSelected"  : "";  ?>" data="live"><i class="icon-microphone"></i>   <?php  _e( 'Live', 'webinarignition' ); ?></a></li>
                    <li><a href="#" class="webinarStatus <?php echo ($webinar_data->webinar_switch == "replay")     ? "webinarStatusSelected"  : "";   ?>" data="replay"><i class="icon-refresh"></i>   <?php  _e( 'Replay', 'webinarignition' ); ?></a></li>
                    <li><a href="#" class="webinarStatus <?php echo ($webinar_data->webinar_switch == "closed")     ? "webinarStatusSelected"  : "";   ?>" data="closed"><i class="icon-lock"></i>      <?php  _e( 'Closed', 'webinarignition' ); ?></a></li>   
                </ul>
            </div>         
            
        <?php } ?>    

            <div class="dash-wrapper-left">

                <div class="dash-stat-block dash-block-1" <?php if ($webinar_data->webinar_date == "AUTO") { echo 'style="display:none;"';  } ?> >
                    <div class="dash-stat-number" id="usersOnlineCount">0</div>
                    <div class="dash-stat-label"><?php _e( 'Live Viewers On Webinar', 'webinarignition') ?></div>
                </div>

                <div class="dash-stat-block dash-block-2">
                    <div class="dash-stat-number"><?php echo $totalLeads; ?></div>
                    <div class="dash-stat-label"><?php _e( 'Total Registrants', 'webinarignition') ?></div>
                </div>

                <div class="dash-stat-block dash-block-5">
                    <div class="dash-stat-number"><?php echo $totalOrders; ?></div>
                    <div class="dash-stat-label"><?php _e( 'Total Orders', 'webinarignition') ?></div>
                </div>

                <div class="dash-stat-block dash-block-3">
                    <div class="dash-stat-number" id="dashTotalQ"><?php echo $totalQuestions; ?></div>
                    <div class="dash-stat-label"><?php _e( 'Total Questions', 'webinarignition') ?></div>
                </div>

                <div class="dash-stat-block dash-block-4" <?php if ($webinar_data->webinar_date == "AUTO") {
                    echo 'style="display:none;"';
                } ?> >
                    <div class="dash-stat-number" id="dashTotalActiveQ"><?php echo $totalQuestionsActive; ?></div>
                    <div class="dash-stat-label"><?php _e( 'Total Active Questions', 'webinarignition') ?></div>
                </div>

                <div class="dash-stat-block dash-block-6" <?php if ($webinar_data->webinar_date == "AUTO" || $is_host) { echo 'style="display:none;"'; } ?> >
                    <?php
                    $go_to_dash_url = 'https://www.youtube.com/live_dashboard';
                    $go_to_dash_btn_text = __( 'Go to Youtube Live', 'webinarignition');

                    if (!empty($webinar_data->live_dash_url)) {
                        $go_to_dash_url = $webinar_data->live_dash_url;
                    }

                    if (!empty($webinar_data->live_dash_btn_text)) {
                        $go_to_dash_btn_text = $webinar_data->live_dash_btn_text;
                    }
                    ?>
                    <div class="dash-stat-label" style="padding-bottom: 20px">
                            <a
                                id="youtube-live-button"
                                href="<?php echo $go_to_dash_url; ?>" target="_blank">
                                <i class="icon-youtube-play"></i>
                                <?php echo $go_to_dash_btn_text; ?>
                            </a>

                    </div>
                </div>

                <br clear="left"/>

            </div>

        </div>
    </div>

</div>

<?php if ($webinar_data->webinar_date != "AUTO") { ?>

    <script>
        jQuery(document).ready(function($) {
            
            var webinarStatus;
            
            $('.webinarStatus').on('click', function () {

                webinarStatus = $(this).attr("data");
                $("#webinar_switch").val(webinarStatus);

                $(".webinarStatus").removeClass("webinarStatusSelected");
                $(this).addClass("webinarStatusSelected");
                
                $.post(
                    window.ajaxurl,
                    {
                        action          :   'webinarignition_update_webinar_status',
                        webinarId       :   window.webinarId,
                        security        :   window.wiRegJS.ajax_nonce,
                        webinar_switch  :   webinarStatus
                    }
                );                
                
                return false;
            });            

        });
    </script>
    
<?php } ?>
