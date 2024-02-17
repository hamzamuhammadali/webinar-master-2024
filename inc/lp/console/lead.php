<?php error_reporting( 0 ); ?>
<!-- ON AIR AREA -->
<div id="leadTab" style="display:none;" class="consoleTabs">
    <div class="statsDashbord">
        <div class="statsTitle statsTitle-Lead">
            <div class="statsTitleIcon">
                <i class="icon-group icon-2x"></i>
            </div>

            <div class="statsTitleCopy">
                <h2><?php _e( 'Manage Registrants For Webinar', 'webinarignition' ) ?></h2>

                <p><?php _e( 'All your Registrants / Leads for the event...', 'webinarignition' ) ?></p>
            </div>

            <br clear="left"/>
        </div>
    </div>

    <div class="innerOuterContainer">
        <div class="innerContainer">
            <div class="questionMainTa2b" style="margin-top: 20px;">
                <div class="airSwitch" style="padding-top: 0px;">
                    <div class="airSwitchLeft">
                        <span class="airSwitchTitle"><?php _e( 'Your Registrants Command Center', 'webinarignition' ) ?></span>
                        <span class="airSwitchInfo"><?php _e( 'All the stats you will need for your registrants...', 'webinarignition' ) ?></span>
                    </div>

                    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method='post'
                          id='webinarignition_export_leads_form' target="_blank">
                        <div id="ezrestrict-verify-inputs">
                            <input type='hidden' name='action' value='webinarignition_export_leads'>
                            <input type='hidden' name='webinarignition_webinar_id' value='<?php echo $webinar_id ?>'>
                            <input type="hidden" name="webinarignition_leads_type" id="webinarignition_leads_type"
                                   value="<?php echo webinarignition_is_auto( $webinar_data ) ? 'evergreen' : 'live' ?>">
                    </form>
                    <script>
                        function exportLeads(type) {
                            document.getElementById('webinarignition_leads_type').setAttribute('value', type)
                            document.getElementById('webinarignition_export_leads_form').submit();
                        }
                    </script>
                    <div class="airSwitchRight">
                        <?php if ( $webinar_data->webinar_date == "AUTO" ) { ?>
                            <button type="button" href="#" onclick="exportLeads('evergreen_normal')"
                                    class="small button secondary" style="margin-right: 0px;"><i
                                        class="icon-file-text"></i>
                                <?php _e( 'Export CSV', 'webinarignition' ) ?>
                            </button>
                            <button type="button" href="#" onclick="exportLeads('evergreen_hot')"
                                    class="small button secondary" style="margin-right: 0px;"><i
                                        class="icon-file-text"></i>
                                <?php _e( 'HOT LEADS', 'webinarignition' ) ?>
                            </button>
                            <a href="#" id="showtrackingcode" class="small button secondary" style="margin-right: 0px;"><i
                                        class="icon-dollar"></i> <?php _e( 'Get Order Code', 'webinarignition' ) ?></a>
                        <?php } else { ?>
                            <button type="button" href="#" onclick="exportLeads('live_normal')"
                                    class="small button secondary" style="margin-right: 0px;"><i
                                        class="icon-file-text"></i>
                                <?php _e( 'Export CSV', 'webinarignition' ) ?>
                            </button>
                            <button type="button" href="#" onclick="exportLeads('live_hot')"
                                    class="small button secondary" style="margin-right: 0px;"><i
                                        class="icon-file-text"></i>
                                <?php _e( 'HOT LEADS', 'webinarignition' ) ?>
                            </button>
                            <a href="#" id="showtrackingcode" class="small button secondary" style="margin-right: 0px;"><i
                                        class="icon-dollar"></i> <?php _e( 'Get Order Code', 'webinarignition' ) ?></a>
                            <a href="#" id="importLeads" class="small button secondary thickbox"
                               style="margin-right: 0px;"><i
                                        class="icon-group"></i> <?php _e( 'Import Leads (CSV)', 'webinarignition' ) ?>
                            </a>
                        <?php } ?>
                    </div>

                    <br clear="all"/>

                    <!-- Import CSV File Area -->
                    <div class="importCSVArea" style="display: none;">
                        <h2><?php _e( 'Import Leads Into This Campaign', 'webinarignition' ) ?></h2>
                        <h4><?php _e( 'Paste in your CSV in the area below. <b>Must Follow This Format: NAME, EMAIL, PHONE', 'webinarignition' ) ?></b></h4>
                        <textarea id="importCSV"
                                  placeholder="<?php _e( 'Add your CSV Code here...', 'webinarignition' ) ?>"></textarea>
                        <a href="#" class="button secondary"
                           id="addCSV"><?php _e( 'Import Leads Now', 'webinarignition' ) ?></a>
                        <br>
                        <hr>
                        <div class="reh-upload-csv">
                            <form id="csv-upload-form" method="POST" enctype="multipart/form-data">
                                <label for="csv_file"> <?php _e( 'Upload CSV File here', 'webinarignition' ) ?> </label>
                                <input type="file" name="csv_file"  accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .txt, .csv" />
                                <!-- <input type="file" name="csv_file" accept=".csv"> -->
                                <input type="submit" value="Upload">
                            </form>
                        </div>
                    </div>

                    <!-- New Stats Funnel Design Leads -->
                    <div class="leadStatBlock">

                        <div class="leadStat leadStat1">
                            <div class="leadStatTop" id="leadTotal"><?php echo $totalLeads; ?></div>
                            <div class="leadStatLabel"
                                 style="border-bottom-left-radius: 5px;"><?php _e( 'total leads', 'webinarignition' ) ?></div>
                        </div>

                        <div class="leadStat leadStat2">
                            <div class="leadStatTop"><span id="eventTotal">0</span></div>
                            <div class="leadStatLabel"><?php _e( 'attended live event', 'webinarignition' ) ?></div>
                        </div>

<!--                        <div class="leadStat leadStat3">
                            <div class="leadStatTop"><span id="replayTotal">0</span></div>
                            <div class="leadStatLabel"><?php /*_e( 'watched replay', 'webinarignition' ) */?></div>
                        </div>
-->
                        <div class="leadStat leadStat4">
                            <div class="leadStatTop"><span id="orderTotal">0</span></div>
                            <div class="leadStatLabel"
                                 style="border-bottom-right-radius: 5px;"><?php _e( 'purchased', 'webinarignition' ) ?></div>
                        </div>

                        <br clear="left"/>

                    </div>
                </div>

                <table id="leads" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th class="leadHead"><i class="icon-user"
                                                style="margin-right: 5px;"></i><?php _e( 'Registrants Information', 'webinarignition' ) ?>
                            :
                        </th>
                        <th><i class="icon-eye-open"
                               style="margin-right: 5px;"></i><?php _e( 'Attended Event', 'webinarignition' ) ?>:
                        </th><!--
                        <th><i class="icon-film"
                               style="margin-right: 5px;"></i><?php /*_e( 'Watched Replay', 'webinarignition' ) */?>:
                        </th>-->
                        <th><i class="icon-dollar"
                               style="margin-right: 5px;"></i><?php _e( 'Ordered', 'webinarignition' ) ?>:
                        </th>
                        <th width="90"><i class="icon-trash"
                                          style="margin-right: 5px;"></i> <?php _e( 'Delete', 'webinarignition' ) ?>
                        </th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach ( $leads as $lead ): ?>

                        <tr id="table_lead_<?php echo $lead['ID']; ?>" class="leadTableBlock">
                            <td style="padding: 15px; width: 350px;">
                                <span class="leadName"><span class="fbLead"
                                                             style="display: <?php if ( $lead['trk1'] == "FB" ) {
                                                                 echo "inline";
                                                             } else {
                                                                 echo "none";
                                                             } ?>;"><i
                                                class="icon-facebook-sign"></i></span> <?php echo $lead['name']; ?> </span>
                                <span class="leadInfoSub">
                                    <i class="icon-calendar"
                                       style="margin-right: 5px;"></i> <?php echo $lead['created']; ?>
                                    <b><i class="icon-envelope-alt"
                                          style="margin-right: 5px; margin-left: 5px;"></i> <?php echo $lead['email']; ?>
                                    </b>
                                </span>
                                <span class="leadInfoSub" style="margin-top: 5px;"><i class="icon-mobile-phone"
                                                                                      style="margin-right: 5px;"></i> <?php if ( $lead['phone'] == "undefined" || $lead['phone'] == "" ) {
                                        echo "No Phone";
                                    } else {
                                        echo $lead['phone'];
                                    } ?>
                                </span>
                                <?php if ( $webinar_data->webinar_date == "AUTO" ) { ?>
                                    <span class="leadInfoSub">
                                                    <i class="icon-time"
                                                       style="margin-right: 5px; color: green"></i> <?php echo $lead['date_picked_and_live']; ?>
                            <b><i class="icon-sun"
                                  style="margin-right: 5px; margin-left: 5px;color: orangered"></i> <?php echo $lead['lead_timezone']; ?>
                            </b>
                                </span>
                                <?php } ?>
                                <?php
                                $lead_meta = WebinarignitionLeadsManager::get_lead_meta($lead['ID'], 'wiRegForm', $webinar_data->webinar_date == "AUTO" ? 'evergreen' : 'live');

                                if (!empty($lead_meta['meta_value'])) {
                                    $lead_meta_data = maybe_unserialize($lead_meta['meta_value']);
	                                $lead_meta_data = WebinarignitionLeadsManager::fix_optName($lead_meta_data);
                                    if (is_array($lead_meta_data)) {
                                        if( isset($lead_meta_data['optName']) || isset($lead_meta_data['optEmail']) ) {
                                            foreach ($lead_meta_data as $field_name => $field) {
                                                $field_label = $field['label'];
                                                $field_value = $field['value'];
                                                ?>
                                                <span class="leadInfoSub">
                                                    <?php echo $field_label; ?>:
                                                    <b><?php echo $field_value; ?></b>
                                                </span>
                                                <?php
                                            }
                                        } else { //compatibility with old lead data
	                                        foreach ($lead_meta_data as $field_label => $field_value) {
		                                        ?>
                                                <span class="leadInfoSub">
                                                    <?php echo $field_label; ?>:
                                                    <b><?php echo $field_value; ?></b>
                                                </span>
		                                        <?php
	                                        }
                                        }
                                    }
                                }
                                ?>
                            </td>
                            <td><span class="radius checkEvent <?php if ( $lead['event'] == "No" || $lead['event'] == "" ) {
                                    echo "secondary";
                                } else {
                                    echo "success";
                                } ?> label"><?php if ( $lead['event'] == "No" || $lead['event'] == "" ) {
                                        echo "No";
                                    } else {
                                        echo "Yes";
                                    } ?></span></td>
                            <!--<td><span
                                        class="radius checkReplay <?php /*if ( $lead['replay'] == "No" || $lead['replay'] == "" ) {
                                            echo "secondary";
                                        } else {
                                            echo "success";
                                        } */?> label"><?php /*if ( $lead['replay'] == "No" || $lead['replay'] == "" ) {
                                        echo "No";
                                    } else {
                                        echo "Yes";
                                    } */?></span></td>
                            --><td><span class="radius checkOrder <?php if ( $lead['trk2'] == "" ) {
                                    echo "secondary";
                                } else {
                                    echo "success";
                                } ?> label"><?php if ( $lead['trk2'] == "" ) {
                                        echo "No";
                                    } else {
                                        echo $lead['trk2'];
                                    }; ?></span></td>

                            <td>
                                <center><i class="icon-remove delete_lead" lead_id="<?php echo $lead['ID']; ?>"></i>
                                </center>
                            </td>
                        </tr>
                    <?php

                    endforeach; ?>

                    </tbody>
                </table>

                <br clear="all"/>
            </div>
        </div>
    </div>
</div>

