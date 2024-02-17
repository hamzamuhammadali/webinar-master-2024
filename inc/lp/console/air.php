<!-- ON AIR AREA -->
<div id="onairTab" style="display:none;" class="consoleTabs">
    <div class="statsDashbord">
        <div class="statsTitle statsTitle-Air">
            <div class="statsTitleIcon">
                <i class="icon-microphone icon-2x"></i>
            </div>

            <div class="statsTitleCopy">
                <h2><?php _e( 'On Air', 'webinarignition' ) ?></h2>
                <p><?php _e( 'Manage the live broadcasting message to live viewers...', 'webinarignition' ) ?></p>
            </div>

            <br clear="left"/>
        </div>
    </div>

    <div class="innerOuterContainer">
        <div class="innerContainer">
            <div class="airSwitch">
                <div class="airSwitchLeft">
                    <span class="airSwitchTitle"><?php _e( 'On Air Broadcast Switch', 'webinarignition' ) ?></span>
                    <span class="airSwitchInfo"><?php _e( 'If set to ON, the message/html below will appear under the webinar (instantly) for people on the webinar...', 'webinarignition' ) ?></span>
                </div>

                <div class="airSwitchRight">
                    <p class="field switch">
                        <input type="hidden" id="airToggle"
                               value="<?php if ( ! isset( $webinar_data->air_toggle ) || $webinar_data->air_toggle == "" || $webinar_data->air_toggle == "on" ) {
                                   echo "on";
                               } else {
                                   echo $webinar_data->air_toggle;
                               } ?>">
                        <label for="radio1"
                               class="cb-enable <?php if ( ! isset( $webinar_data->air_toggle ) || $webinar_data->air_toggle == "" || $webinar_data->air_toggle == "on" ) {
                                   echo "selected";
                               } ?> "><span><?php _e( 'On', "webinarignition" ); ?></span></label>
                        <label for="radio2"
                               class="cb-disable <?php if ( isset( $webinar_data->air_toggle ) && $webinar_data->air_toggle == "off" ) {
                                   echo "selected";
                               } ?>"><span><?php _e( 'Off', "webinarignition" ); ?></span></label>
                    </p>
                </div>

                <br clear="all"/>
            </div>

            <div class="airEditorArea" style="margin-top: 20px;">
                <!-- <div name="content"
                     id="airCopy"><?php// echo isset( $webinar_data->air_html ) ? stripcslashes( $webinar_data->air_html ) : ""; ?></div> -->

                     <?php
                    $editor_content = isset( $webinar_data->air_html ) ? stripcslashes( $webinar_data->air_html ) : "";
                    // added default wordpress editor instead of summernote editor
                    $content = $editor_content; // Initial content
                    $editor_id = 'airCopy_editor'; // Unique ID for the editor
                    $settings = array(
                        'textarea_name' => 'airCopy_textarea', // Name attribute for the textarea
                        'textarea_rows' => 10, // Number of rows for the textarea
                        'tinymce' => array(
                            'toolbar' => array(
                                array('style', 'style'),
                                array('font', array('bold', 'underline', 'clear')),
                                array('fontname', array('fontname')),
                                array('color', array('color')),
                                array('para', array('ul', 'ol', 'paragraph')),
                                array('table', array('table')),
                                array('insert', array('link')),
                                array('view', array('fullscreen', 'codeview', 'help'))
                            )
                        )
                    );
                    // Generate the editor
                    wp_editor($content, $editor_id, $settings);
                    ?>
                <div class="airExtraOptions">
                    <span class="airSwitchTitle"><?php _e( 'Order Button To Copy', 'webinarignition' ) ?></span>
                    <span class="airSwitchInfo"><?php _e( 'This is the copy that is displayed on the button...', 'webinarignition' ) ?></span>
                    <input type="text" style="margin-top: 10px;"
                           placeholder="<?php _e( 'Ex: Click Here To Download Your Copy', 'webinarignition' ) ?>"
                           id="air_btn_copy"
                           value="<?php echo isset( $webinar_data->air_btn_copy ) ? stripcslashes( $webinar_data->air_btn_copy ) : ""; ?>">
                </div>

                <div class="airExtraOptions">
                    <span class="airSwitchTitle"><?php _e( 'Order Button URL', 'webinarignition' ) ?></span>
                    <span class="airSwitchInfo"><?php _e( 'This is the url the button goes to (leave blank if you don\'t want the button to appear)...', 'webinarignition' ) ?></span>
                    <input type="text" style="margin-top: 10px;" placeholder="<?php _e( "Ex: http://yoursite.com/order-now", "webinarignition" ); ?>"
                           id="air_btn_url"
                           value="<?php echo isset( $webinar_data->air_btn_url ) ? stripcslashes( $webinar_data->air_btn_url ) : ""; ?>">
                </div>


                <div class="airExtraOptions">
                    <?php
                    $air_btn_color = ! empty( $webinar_data->air_btn_color ) ? $webinar_data->air_btn_color : "#6BBA40";
                    webinarignition_display_color(
                        $ID,
                        $air_btn_color,
                        __( "CTA Button Color", 'webinarignition' ),
                        "air_btn_color",
                        __( "This is the color of the CTA button...", 'webinarignition' ),
                        "#6BBA40"
                    );
                    ?>
                </div>
                <script>
                    // Color Picker

                    jQuery(document).ready(function ($) {

                        // Toggle For On Air
                        $(".cb-enable").on('click', function () {

                            var parent = $(this).parents('.switch');
                            $('.cb-disable', parent).removeClass('selected');
                            $(this).addClass('selected');
                            $("#airToggle").val("on");
                            saveAirCTA(false);
                            bootbox.alert({
                                message: "<?php _e( 'Broadcast Messaging Successfully Enabled.', "webinarignition" ); ?>",
                                backdrop: true
                            });

                        });

                        $(".cb-disable").on('click', function () {

                            var parent = $(this).parents('.switch');
                            $('.cb-enable', parent).removeClass('selected');
                            $(this).addClass('selected');
                            $("#airToggle").val("off");
                            saveAirCTA(false);
                            bootbox.alert({
                                message: "<?php _e( 'Broadcast Messaging Successfully Disabled.', "webinarignition" ); ?>",
                                backdrop: true
                            });

                        });

                        // Save AIR Settings
                        $('#saveAir').on('click', function(e) {
                            saveAirCTA(true);
                        }) ;

                        function saveAirCTA(show_success_message) {
                            var contenta;
                            var editor = tinyMCE.get('airCopy_editor');
                            if (editor) {
                                // Ok, the active tab is Visual
                                contenta = editor.getContent();
                            } else {
                                // The active tab is HTML, so just query the textarea
                                contenta = $('#'+'airCopy_editor').val();
                            }
                            var $toggle = $("#airToggle").val(),
                                $html = contenta,
                                $btncopy = $("#air_btn_copy").val(),
                                $btnurl = $("#air_btn_url").val(),
                                $btncolor = $("#air_btn_color").val(),
                                data = {
                                    action: 'webinarignition_save_air',
                                    id: "<?php echo $webinar_data->id; ?>",
                                    toggle: "" + $toggle + "",
                                    btncopy: "" + $btncopy + "",
                                    btnurl: "" + $btnurl + "",
                                    btncolor: "" + $btncolor + "",
                                    html: "" + $html + "",
                                    security: window.wiRegJS.ajax_nonce
                                };

                            $.post(window.ajaxurl, data, function () {
                                if(show_success_message) {
                                    alert("<?php _e( 'Saved Broadcast Message Settings', 'webinarignition' ) ?>");
                                }
                            });
                            return false;

                        }
                    });




                </script>
            </div>

            <div class="airSwitchSaveArea">

                <div class="airSwitchFooterRight" style="margin-bottom: 20px;">
                    <a href="#" id="saveAir" class="small button radius success" style="margin-right:0px;"><i
                                class="icon-save"></i> <?php _e( 'Save On Air Settings', 'webinarignition' ) ?></a>
                </div>

                <br clear="all"/>

            </div>

        </div>
    </div>

</div>