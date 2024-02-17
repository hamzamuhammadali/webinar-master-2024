<?php defined( 'ABSPATH' ) || exit;

use Carbon\Carbon;

class WiDateHelpers
{
    /**
     * @param string $name e.g. 'Africa/Johannesburg'
     * @return string e.g. 'SAST' or 'UTC +8' if there is no abbreviation.
     */
    static function get_tz_abbr_from_name($name)
    {
        $dt = Carbon::now(new DateTimeZone($name));
        $abbr = $dt->format('T');

        if (is_numeric($abbr)) {
            // Get UTC offset.
            if (strlen($abbr) > 2 && $abbr[1] == 0) {
                $abbr = $abbr[0] . $abbr[2];
            }
            return 'UTC ' . $abbr;
        }

        return $abbr;
    }

    /**
     * NOT IN USE ANYMORE, SHOULD BE REMOVED
     *
     * Get next days for custom schedule
     * @param integer $offset e.g. skip the next 2 days.
     * @param int $max e.g. max number of days to return max 7.
     * @param array $toggle_days
     * @param string $blacklist e.g. '2019-01-27, 2019-01-28'
     * @return array
     */
    public static function get_next_days($offset, $max, $toggle_days, $blacklist, $date_format = "l, F j, Y")
    {
        // $max can not be higher than 7.
        if ($max > 7) $max = 7;
        if ($offset < 1) $offset = 1;

        $excludedWeekdays = array_keys(array_filter($toggle_days, function($weekday) { return $weekday === 'no'; }));
        $blacklisted_dates = array_map('trim', explode(',', $blacklist));

        $arr = [];
        $date = Carbon::now()->addDays($offset - 1);
        for ($i=$offset; count($arr)<$max; $i++) {
            $d = $date->addDay();

            $day = strtolower($d->format('l'));
            if (in_array($day, $excludedWeekdays))
                continue;

            $Ymd = $d->format('Y-m-d');
            if (in_array($Ymd, $blacklisted_dates))
                continue;

            $arr[$Ymd] = $d->format($date_format);
        }

        return $arr;
    }

    /** Get next days for custom schedule
     * @param integer $offset e.g. skip the next 2 days.
     * @param int $max e.g. max number of days to return max 7.
     */
//    public static function get_next_days($offset, $max, $date_format="F j, Y")
//    {
//        // $max can not be higher than 7.
//        // The method returns an array that uses the weekday names as keys. E.g. wednesday.
//        // For this reason the array will only ever have 7 days, since the keys will be overwritten by next wednesday.
//        // So we give $max a hard limit of 7 so that this does not happen and cause confusion.
//        if ($max > 7) $max = 7;
//        if ($offset < 1) $offset = 1;
//
//        $arr = [];
//        $date = Carbon::now()->addDays($offset - 1);
//        for ($i=$offset; count($arr)<$max; $i++) {
//            $d = $date->addDay();
//            $day = strtolower($d->format('l'));
//            $arr[] = [$d->format('Y-m-d'), $d->format($date_format), $day];
//        }
////        return $arr;
//        $newArr = [];
//        $weekdays = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
//        foreach ($weekdays as $wd) {
//            foreach ($arr as $a) {
//                if ($wd === $a[2])
//                    $newArr[$wd] = [$a[0], $a[1]];
//            }
//        }
//
//        return $newArr;
//    }

    
    /**
     * Retrieve list of translated months
     *
     */
    public static function get_locale_months( )
    {
        
                global $wp_locale;

                $translated_months = [];
                for ( $month_index = 1; $month_index <= 12; $month_index++ ) :
                         $translated_months[] =  $wp_locale->get_month( $month_index );
                endfor;   

                return $translated_months;
    }   
    
    
    /**
     * Retrieve list of translated days
     *
     */
    public static function get_locale_days()
    {
        
                global $wp_locale;

                $translate_days = [];
                for ( $day_index = 0; $day_index <= 6; $day_index++ ) :
                         $translate_days[] =  $wp_locale->get_weekday( $day_index );
                endfor;      

                return $translate_days;
    
    }
    
    
    /**
     * Retrieve list of translated days
     *
     */
    public static function get_locale_weekday_abbrev()
    {
        
                global $wp_locale;

                $translate_days = [];
                for ( $day_index = 0; $day_index <= 6; $day_index++ ) :
                          $weekday_name =   $wp_locale->get_weekday( $day_index );
                          $translate_days[] =  $wp_locale->get_weekday_abbrev( $weekday_name );
                endfor;      

                return $translate_days;
    
    }     
    
    
    
}
