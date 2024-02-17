<?php

defined( 'ABSPATH' ) || exit;

if (empty(get_option('webinarignition_installer_version'))) return;

/*
|-----------------------------------------------------------------------------------------------------------------------
| ADD THE OS AND BROWSER COLUMN TO LEADS TABLES
|-----------------------------------------------------------------------------------------------------------------------
*/

global $wpdb;

// CHECK DB VERSION AND RUN MIGRATIONS
$webinarignition_db_version = get_option('webinarignition_db_version');
$webinarignition_db_version = !empty($webinarignition_db_version) ? $webinarignition_db_version : 0;


if ($webinarignition_db_version < 9 ) {
    // add os/browser columns (live)
    $table_name = $wpdb->prefix . "webinarignition_leads";
    $row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '{$table_name}' AND column_name = 'gdpr_data' ");
    if (empty($row)) {
        $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN gdpr_data VARCHAR(256) DEFAULT NULL, ADD COLUMN event varchar(50), ADD COLUMN replay varchar(50),  ADD COLUMN trk1 varchar(50),ADD COLUMN trk2 varchar(50),ADD COLUMN trk3 varchar(50),ADD COLUMN trk4 varchar(50),ADD COLUMN trk5 varchar(50),ADD COLUMN trk6 varchar(50),ADD COLUMN trk7 varchar(50),ADD COLUMN trk8 varchar(50),ADD COLUMN trk9 varchar(50),ADD COLUMN lead_browser_and_os varchar(256)");
    }


    $table_name = $wpdb->prefix . "webinarignition_wi";
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'");

    if (empty($table_exists)) {
        $sql = "CREATE TABLE " . $table_name . " (
                  ID INTEGER(100) UNSIGNED AUTO_INCREMENT,
                  keyused varchar(150),
                  switch varchar(150),
                  UNIQUE KEY id (id)
                  )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }


    $table_name = $wpdb->prefix . "webinarignition_leads_evergreen";

    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'");

    if (empty($table_exists)) {
        $sql = "CREATE TABLE " . $table_name . " (
              ID INTEGER(100) UNSIGNED AUTO_INCREMENT,
              app_id varchar(20),
              name varchar(200),
              email varchar(200),
              phone varchar(200),
              skype varchar(200),
              created varchar(200),
              date_picked_and_live varchar(50),
              date_picked_and_live_check varchar(50),
              date_1_day_before varchar(50),
              date_1_day_before_check varchar(50),
              date_1_hour_before varchar(50),
              date_1_hour_before_check varchar(50),
              date_after_live varchar(50),
              date_after_live_check varchar(50),
              date_1_day_after varchar(50),
              date_1_day_after_check varchar(50),
              lead_timezone varchar(50),
              lead_status varchar(50),
              event varchar(50),
              replay varchar(50),
              trk1 varchar(50),
              trk2 varchar(50),
              trk3 varchar(50),
              trk4 varchar(50),
              trk5 varchar(50),
              trk6 varchar(50),
              trk7 varchar(50),
              trk8 varchar(50),
              trk9 varchar(50),
              lead_browser_and_os varchar(256),
              gdpr_data VARCHAR(256) DEFAULT NULL,
              UNIQUE KEY id (id)
              )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($sql);
    } else {
        // add os/browser columns (evergreen)
        $row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '{$table_name}' AND column_name = 'gdpr_data' ");
        if (empty($row)) {
            $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN gdpr_data VARCHAR(256) DEFAULT NULL, ADD COLUMN event varchar(50), ADD COLUMN replay varchar(50),  ADD COLUMN trk1 varchar(50),ADD COLUMN trk2 varchar(50),ADD COLUMN trk3 varchar(50),ADD COLUMN trk4 varchar(50),ADD COLUMN trk5 varchar(50),ADD COLUMN trk6 varchar(50),ADD COLUMN trk7 varchar(50),ADD COLUMN trk8 varchar(50),ADD COLUMN trk9 varchar(50),ADD COLUMN lead_browser_and_os varchar(256)");
        }
    }



    //if this is an upgrade from old, legacy, version then add these columns. DB version in legacy == 5
    $table_name = $wpdb->prefix . "webinarignition_questions";
    $row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '{$table_name}' AND column_name = 'name' ");
    if (empty($row)) {
        $wpdb->query("ALTER TABLE {$table_name} ADD COLUMN name VARCHAR(100) DEFAULT NULL, ADD COLUMN email varchar(100), ADD COLUMN attr1 varchar(50),ADD COLUMN attr2 varchar(50),ADD COLUMN attr3 varchar(50),ADD COLUMN attr4 varchar(50),ADD COLUMN attr5 varchar(50),ADD COLUMN answer TEXT NULL,ADD COLUMN webinarTime ");
    }
    
    
    
    //update old webinars with correct url of wi logo
    $qry = 'select id, camtype from '. $wpdb->prefix .'webinarignition';
    $lst = $wpdb->get_results($qry);

    if(!empty($lst)){

        foreach ($lst as $cmp) {
                  $webinar_data = WebinarignitionManager::get_webinar_data($cmp->id);

                  $webinar_data->email_notiff_body_1 = str_replace( "//images/wi-logo.png", "/images/wi-logo.png", $webinar_data->email_notiff_body_1 );
                  $webinar_data->email_notiff_body_2 = str_replace( "//images/wi-logo.png", "/images/wi-logo.png", $webinar_data->email_notiff_body_2 );
                  $webinar_data->email_notiff_body_3 = str_replace( "//images/wi-logo.png", "/images/wi-logo.png", $webinar_data->email_notiff_body_3 );
                  $webinar_data->email_notiff_body_4 = str_replace( "//images/wi-logo.png", "/images/wi-logo.png", $webinar_data->email_notiff_body_4 );
                  $webinar_data->email_notiff_body_5 = str_replace( "//images/wi-logo.png", "/images/wi-logo.png", $webinar_data->email_notiff_body_5 );
                  $webinar_data->email_signup_body   = str_replace( "//images/wi-logo.png", "/images/wi-logo.png", $webinar_data->email_signup_body );


                  $webinar_data->email_notiff_body_1 = str_replace( "webinarignition/images/wi-logo.png", "webinar-ignition/images/wi-logo.png", $webinar_data->email_notiff_body_1 );
                  $webinar_data->email_notiff_body_2 = str_replace( "webinarignition/images/wi-logo.png", "webinar-ignition/images/wi-logo.png", $webinar_data->email_notiff_body_2 );
                  $webinar_data->email_notiff_body_3 = str_replace( "webinarignition/images/wi-logo.png", "webinar-ignition/images/wi-logo.png", $webinar_data->email_notiff_body_3 );
                  $webinar_data->email_notiff_body_4 = str_replace( "webinarignition/images/wi-logo.png", "webinar-ignition/images/wi-logo.png", $webinar_data->email_notiff_body_4 );
                  $webinar_data->email_notiff_body_5 = str_replace( "webinarignition/images/wi-logo.png", "webinar-ignition/images/wi-logo.png", $webinar_data->email_notiff_body_5 );
                  $webinar_data->email_signup_body   = str_replace( "webinarignition/images/wi-logo.png", "webinar-ignition/images/wi-logo.png", $webinar_data->email_signup_body );

                  update_option('webinarignition_campaign_' . $cmp->id, $webinar_data);

                }

    }

    update_option('webinarignition_db_version', WEBINARIGNITION_DB_VERSION);

}

$webinarignition_db_version     = get_option('webinarignition_db_version');
if ( ! $webinarignition_db_version || $webinarignition_db_version < 12 ) {

    $table_name = $wpdb->prefix . "webinarignition_questions";
    $wpdb->query("ALTER TABLE {$table_name} MODIFY attr6 TEXT NULL");
    $wpdb->query("ALTER TABLE {$table_name} CHANGE COLUMN attr6 answer TEXT");

    $wpdb->query("ALTER TABLE {$table_name} MODIFY attr7 varchar NULL");
    $wpdb->query("ALTER TABLE {$table_name} CHANGE COLUMN attr7 webinarTime varchar(50)");

    update_option('webinarignition_db_version', WEBINARIGNITION_DB_VERSION);

}


add_action( 'init', 'webinarignition_smtp_migration' );

function webinarignition_smtp_migration(){

            $option_smtp_host       = get_option( 'webinarignition_smtp_host' );
            $option_smtp_migrated   = get_option( 'webinarignition_migrated_smtp' );

            if ( empty($option_smtp_migrated)  &&  empty($option_smtp_host)  &&   current_user_can('manage_options') ) {

                global $wpdb;

                $qry = 'select id from '. $wpdb->prefix .'webinarignition';
                $lst = $wpdb->get_results($qry);

                if(!empty($lst)){



                    foreach ($lst as $cmp) {
                            $webinar_data = WebinarignitionManager::get_webinar_data($cmp->id);

                            $option_smtp_host                = get_option( 'webinarignition_smtp_host' );
                            $option_smtp_port                = get_option( 'webinarignition_smtp_port' );
                            $option_smtp_protocol            = get_option( 'webinarignition_smtp_protocol' );
                            $option_smtp_user                = get_option( 'webinarignition_smtp_user' );
                            $option_smtp_pass                = get_option( 'webinarignition_smtp_pass' );
                            $option_smtp_name                = get_option( 'webinarignition_smtp_name' );
                            $option_smtp_email               = get_option( 'webinarignition_smtp_email' );
                            $option_reply_to_email           = get_option( 'webinarignition_reply_to_email' );

                            if( empty($option_smtp_host)  &&  empty($option_smtp_user) &&  empty($option_smtp_pass) && !empty( $webinar_data->smtp_user ) &&  !empty( $webinar_data->smtp_pass ) &&  !empty( $webinar_data->smtp_port ) &&  !empty( $webinar_data->smtp_host ) &&  !empty( $webinar_data->transfer_protocol ) &&  !empty( $webinar_data->smtp_name ) &&  !empty( $webinar_data->smtp_email )    ) {

                                      $smtp_test_results_array = webinarignition_test_smtp_phpmailer( $webinar_data->smtp_host,  $webinar_data->smtp_port, $webinar_data->smtp_user,  $webinar_data->smtp_pass );

                                      if( is_array($smtp_test_results_array) &&  isset($smtp_test_results_array['status'] )  && ( $smtp_test_results_array['status'] == 1 ) ){


                                            update_option( 'webinarignition_smtp_host',      $webinar_data->smtp_host  );
                                            update_option( 'webinarignition_smtp_port',      $webinar_data->smtp_port );
                                            update_option( 'webinarignition_smtp_protocol',  $webinar_data->transfer_protocol );
                                            update_option( 'webinarignition_smtp_user',      $webinar_data->smtp_user  );
                                            update_option( 'webinarignition_smtp_pass',      $webinar_data->smtp_pass );
                                            update_option( 'webinarignition_smtp_email',     $webinar_data->smtp_email );
                                            update_option( 'webinarignition_smtp_name',      $webinar_data->smtp_name );
                                            update_option( 'webinarignition_smtp_email',     $webinar_data->smtp_email );
                                            update_option( 'webinarignition_reply_to_email', $webinar_data->smtp_email );
                                            update_option( 'webinarignition_smtp_connect',   1  );

                                            update_option('webinarignition_migrated_smtp', 1);

                                            update_option('webinarignition_upgraded_smtp', 1);

                                            break;


                                      }

                            }

                    }

                }

                update_option('webinarignition_db_version', WEBINARIGNITION_DB_VERSION);

            }

}



add_action( 'admin_notices', 'webinarignition_smtp_migration_admin_notice' );

function webinarignition_smtp_migration_admin_notice() {

    $webinarignition_upgraded_smtp                = get_option( 'webinarignition_upgraded_smtp' );

    if( $webinarignition_upgraded_smtp == 1 ) { ?>

        <div id="webinarignition-smtp-notice" class="notice notice-success is-dismissible">
            <p><?php _e( 'Your WebinarIgnition SMTP settings have been migrated. You can find the new settings <a href="/wp-admin/admin.php?page=webinarignition_settings">here</a>', 'webinarignition' ); ?></p>
        </div>

        <script>

                    jQuery(document).on( 'click', '#webinarignition-smtp-notice .notice-dismiss', function() {

                        jQuery.ajax({
                            url: '/wp-admin/admin-ajax.php',
                            data: {
                                action: 'webinarignition_delete_smtp_updated_status',
                                security: '<?php echo wp_create_nonce("webinarignition_ajax_nonce"); ?>'
                            }
                        });

                    });

        </script>

        <?php

    }

}

