<?php defined( 'ABSPATH' ) || exit;





// set :: force no time limit settings
// --------------------------------------------------------------------------------------
ignore_user_abort();
set_time_limit(120);

  global $wpdb;
// def :: define local vars
// -----------------------------------------------------------------------------------
  $rpl = ['new'=>'live'];                                  // replace string values
  $qry = 'select id, camtype from '. $wpdb->prefix .'webinarignition';     // query string
  $lst = $wpdb->get_results($qry);                         // job list
  $cmp = null;                                             // campaign

  require_once('wi-admin-functions.php');

if(!empty($lst)){

    require 'schedule_email_live_fn.php';

    foreach ($lst as $cmp) {
     if (is_numeric($cmp->camtype) || $cmp->camtype == 'import') {
        $sil = WebinarignitionManager::get_webinar_data($cmp->id);
        $cmp->camtype       = 'live';
        if($sil->webinar_date == 'AUTO') {
           $cmp->camtype    = 'auto';
        }
     }

     $cmp->camtype      = (isset($rpl[$cmp->camtype]) ? $rpl[$cmp->camtype] : $cmp->camtype);
     $fnBaseName        = "schedule_email_{$cmp->camtype}.php";
     $campaignID        = $cmp->id;
     include $fnBaseName;
  }

}