<?php defined( 'ABSPATH' ) || exit; ?>
<div id="listapps" class="dashList" style="width: 940px;">

    <div id="appHeader" class="dashHeaderListing" style="">
        <span><i class="icon-dashboard" style="margin-right: 5px;"></i><?php  _e( 'Manage All Of Your Webinars', 'webinarignition' ); ?>:</span>
    </div>

    <?php

    // Display Apps:
    global $wpdb;
    $table_db_name = $wpdb->prefix . "webinarignition";
    $query = "(SELECT * FROM $table_db_name )";
    $results = $wpdb->get_results($query, OBJECT);
    foreach ($results as $results) {

        // Get Date // Date
        $ID = $results->ID;
        $results2 = WebinarignitionManager::get_webinar_data($ID);

        ?>

	<div class="editableSectionHeading editableSectionHeadingDASH" webinarID="<?php echo $results->ID; ?>" editsection="we_edit_webinar_settings" style="margin-right: 0px; margin-left: 0px;" >
        <a href="<?php echo add_query_arg(['page' => 'webinarignition-dashboard', 'id' => $ID], admin_url('admin.php')); ?>">
            <span class="editableSectionIcon">
                <i class="icon-<?php if ($results2->webinar_date == "AUTO") {
                    echo "refresh";
                } else {
                    echo "microphone";
                } ?> icon-2x"></i>
            </span>

            <span class="editableSectionTitle editableSectionTitleDash ">

                <span style="float:left;">
                    <span class="editableSectionWebinarTitle" title="<?php echo stripcslashes($results->appname); ?>"><?php echo stripcslashes($results->appname); ?></span>
				    <span class="editableSectionTitleSmall"><strong><?php  _e( 'Created', 'webinarignition' ); ?>:</strong> <?php echo stripcslashes($results->created); ?></span>
                </span>

                <span class="appedit">
                    <?php

                    // Get Total Leads
                    if ($results2->webinar_date == "AUTO") {
                        $table_db_name = $wpdb->prefix . "webinarignition_leads_evergreen";
                        $leads = $wpdb->get_results("SELECT * FROM $table_db_name WHERE app_id = '$ID' ", OBJECT);
                    } else {
                        $table_db_name = $wpdb->prefix . "webinarignition_leads";
                        $leads = $wpdb->get_results("SELECT * FROM $table_db_name WHERE app_id = '$ID' ", OBJECT);
                    }

                    $totalLeads = count($leads);
                    $totalLeads = number_format($totalLeads);

                    ?>
                    <?php
                    if ($results2->webinar_date == "AUTO") {
                        // Auto Webinar
                        ?>
                        <span class="ctrl" style="margin-right: 6px;">EVERGREEN</span>
                    <?php
                    } else {
                        // Live Webinar
                        ?>
			<span class="ctrl" style="margin-right: 6px;" ><span style="font-weight:normal;" >Webinar Date:</span> <?php echo $results2->webinar_date; ?></span>
                    <?php
                    }
                    ?>
			<span class="ctrl" style="margin-right: 6px;" ><span style="font-weight:normal;" ><?php  _e( 'Total Registrants', 'webinarignition' ); ?>:</span> <?php echo $totalLeads; ?></span>
                </span>

                <br clear="all">

            </span>


            <span class="editableSectionToggle">
                <?php if( !empty($results2->webinar_status) && ($results2->webinar_status == 'draft') ): ?>
                    <i class="toggleIcon icon-edit-sign icon-2x" title="<?php  _e( 'Draft', 'webinarignition' ); ?>"></i>
                    <?php else: ?>
                    <i class="toggleIcon icon-edit-sign icon-2x published" title="<?php  _e( 'Published', 'webinarignition' ); ?>"></i>
                <?php endif; ?>
            </span>

            <br clear="all">
        </a>
        </div>

    <?php

    }

    ?>

    <div class="appnew">

        <div class="blue-btn-2 btn newWebinarBTN" style="">
            <a href="<?php echo admin_url('?page=webinarignition-dashboard&create'); ?>">
                <i class="icon-plus-sign" style="margin-right: 5px;"></i>
                <?php  _e( 'Create a New Webinar', 'webinarignition' ); ?>
            </a>
        </div>

        <br clear="right">

    </div>

</div>

<br clear="left">
