<?php

defined( 'ABSPATH' ) || exit;
// Try to move all date and time related functions into this file, then see how they can be improved.

/*
 *  Translate month name if possible else just return english month
 *  @param $date string mm-dd-yyyy e.g. 12-25-2016
 *  @param $results wp options (object)
  *  */
function webinarignition_translate_date($date, $results) {
    $split_date = explode('-', $date);
    $d = $split_date[1];
    $m = $split_date[0];
    $y = $split_date[2];

    $translated_months = explode(',', $results->auto_translate_months);
    if ( ! empty($translated_months[$m - 1]) ) {
        $translated_month = trim($translated_months[$m - 1]);
        return "{$d} {$translated_month}, {$y}";
    } else {
    return date('j M, Y', strtotime("{$y}-{$m}-{$d}"));
}
}
/**
 * @param object $webinar
 * @param string $timeString e.g. 16:20
 * @return string $time;
 */
function webinarignition_auto_custom_time($webinar_data, $timeString) {

    $tz = '';

    // Only display timezone abbreviation for fixed timezone.
    if ( isset($webinar_data->auto_timezone_type) && $webinar_data->auto_timezone_type === 'fixed' ) {
        $tz = $webinar_data->auto_timezone_custom;
    }
    
    if( !empty($webinar_data->time_format ) && ( $webinar_data->time_format == '12hour' || $webinar_data->time_format == '24hour'  ) ){ //old formats
        $webinar_data->time_format = get_option( "time_format", 'H:i' );
    }      
    
    $time_format = $webinar_data->time_format;

    return webinarignition_format_time($timeString, $time_format, $tz  );
}

// Attempt to create a function that can be used
// for formatting all time values in this plugin.
/**
 * webinarignition_format_time
 * @param (object) $webinar
 * @param (object) $lead only required for evergreen
 * @return (string)
 */
function webinarignition_display_time($webinar, $lead=null) {

    if (webinarignition_is_auto($webinar)) {

        if ( empty($lead) ) {
        	return '';
        }

        $timeString = explode(' ', $lead->date_picked_and_live)[1];
        $tz = $lead->lead_timezone;
    } else {
        $timeString = date('H:i', strtotime($webinar->webinar_start_time));
        $tz = $webinar->webinar_timezone;
    }
    
    if( !empty($webinar->time_format ) && ( $webinar->time_format == '12hour' || $webinar->time_format == '24hour'  ) ){ //old formats
        $webinar->time_format = get_option( "time_format", 'H:i' );
    }      
    
    $time_format = $webinar->time_format;
    
    if( !empty($webinar->display_tz) && ( $webinar->display_tz == 'yes' ) ){
        return webinarignition_format_time( $timeString, $time_format, $tz);
    }

    return webinarignition_format_time( $timeString, $time_format);
}

function webinarignition_format_time( $timeString, $time_format, $tz = null ) {

    $time = date( $time_format, strtotime( $timeString ) );
    $time .= ' ';

    if ( !empty($tz) ) {
        $time .= " " . WiDateHelpers::get_tz_abbr_from_name($tz);
    }

    return $time;
}


/**
 * webinarignition_format_time
 * @param (object) $webinar
 * @param (object) $lead only required for evergreen
 * @return (string)
 */
function webinarignition_display_date($webinar, $lead=null) {

    $date_format = !empty($webinar->date_format ) ? $webinar->date_format  : ( ($webinar->webinar_date == "AUTO") ? 'l, F j, Y' : get_option( "date_format") );

    if (webinarignition_is_auto($webinar)) {
    	
        if ( empty($lead) ) {
            return '';
        }

        $dateTimeString = explode(" ", $lead->date_picked_and_live);

    } else {
        $dateTimeString = '';
    }

    $dateString = $dateTimeString[0];
    $date = date($date_format, strtotime($dateString));

    return $date;
}

function webinarignition_event_month($webinar, $lead=null) {

    if (webinarignition_is_auto($webinar)) {

        if ( empty($lead) ) {
            return '';
        }

        $dateTimeString = explode(" ", $lead->date_picked_and_live);

    } else {
        $dateTimeString = '';
    }

    return date('M', strtotime($dateTimeString[0]));
}

function webinarignition_event_day($webinar, $lead=null) {

    if (webinarignition_is_auto($webinar)) {

    	if ( empty($lead) ) {
            return '';
        }

        $dateTimeString = explode(" ", $lead->date_picked_and_live);

    } else {
        $dateTimeString = '';
    }

    return date('d', strtotime($dateTimeString[0]));
}

function webinarignition_get_tzOffset($tzString)
{
    if (empty($tzString)) {
        $tzString = 'Pacific/Samoa'; // Todo why Somoa utc +13 ??
    }

    $tz = new DateTimeZone($tzString);
    $dateTime = new DateTime('now', $tz);
    $offset = $tz->getOffset($dateTime) / 3600;

    return ($offset < 0 ? '' : '+') . $offset;
}



/**
@param (string) $tzName
@return (string)
 */
function webinarignition_get_timezone_offset_by_name($tzName) {
    if (empty($tzName)) {
        // return '' if tz is empty otherwise new \DateTimeZone will error
        return '';
    }

    $tz = new \DateTime('now', new \DateTimeZone($tzName));
    $tz_offset = $tz->format("P");
    $offset_parts = explode(':', $tz_offset);
    $offset = $offset_parts[0];
    if ( (strlen($offset) === 3) && $offset[1] === "0") {
        $offset = str_replace("0", "", $offset_parts[0]);
    }
    return $offset;
}


// get max time for evergreen custom schedule
function webinarignition_get_cs_max_time($webinar_data) {
    
    if( !empty($webinar_data->time_format ) && ( $webinar_data->time_format == '12hour' || $webinar_data->time_format == '24hour'  ) ){ //old formats
        $webinar_data->time_format = get_option( "time_format", 'H:i' );
    }      

    if (
            isset($webinar_data->auto_time_1) ||
            isset($webinar_data->auto_time_2) ||
            isset($webinar_data->auto_time_3) ||
            isset($webinar_data->multiple__auto_time)
    ) {

        $csDates = array();
        if ( $webinar_data->auto_time_1 !== "no" ) {
            $csDates[] = strtotime( "$webinar_data->auto_time_1" );
        }

        if ( $webinar_data->auto_time_2 !== "no" ) {
            $csDates[] = strtotime( "$webinar_data->auto_time_2" );
        }

        if ( $webinar_data->auto_time_3 !== "no" ) {
            $csDates[] = strtotime( "$webinar_data->auto_time_3" );
        }

        $is_multiple_auto_time_enabled = WebinarignitionPowerups::is_multiple_auto_time_enabled($webinar_data);

        if ($is_multiple_auto_time_enabled && !empty($webinar_data->multiple__auto_time)) {
            foreach ($webinar_data->multiple__auto_time as $index => $item) {
                if ( $item !== "no" ) {
                    $csDates[] = strtotime( "$item" );
                }
            }
        }

        if ($getMaxTime = max($csDates)) {
            $format_string  = $webinar_data->time_format;
            $csMaxTime      = date( $format_string, $getMaxTime);
        }

        return !empty($csMaxTime) ? $csMaxTime : 0;
    }

    return 0;
}

function webinarignition_format_date_for_ar_service ($format, $timestamp) {
    $formattedDate = '';
    switch($format) {
        case 'MM-DD-YYYY':
            $formattedDate = date('m-d-Y', $timestamp);
            break;
        case 'DD-MM-YYYY':
            $formattedDate = date('d-m-Y', $timestamp);
            break;
        case 'YYYY-MM-DD':
            $formattedDate = date('Y-m-d', $timestamp);
            break;
        default:
            $formattedDate = date('m-d-Y', $timestamp);
            break;
    }
    return $formattedDate;
}

function webinarignition_make_delayed_date($webinar) {

    $dateTime = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
    $offset = intval( $webinar->delayed_day_offset );
    $offset = $offset > 0 ? $offset : abs($offset);
    if ($offset < 0) {
        $offset = abs($offset);
    }

    $dateTime->modify( '+ ' . $offset . ' days' );
    if ( $webinar->delayed_timezone_type !== 'user_specific' ) {
        $dateTime->setTimezone( new DateTimeZone( $webinar->auto_timezone_delayed ) );
    }

    if ( ! empty( $webinar->delayed_blacklisted_dates ) ) {
        $blackListedDatesDelayed = explode(',', str_replace(' ', '', $webinar->delayed_blacklisted_dates));

        while ( in_array($dateTime->format('Y-m-d'), $blackListedDatesDelayed) ) {
            // if the date is blacklisted, just keep adding 1 day until a valid date is found
            $dateTime->modify( '+1 days' );
            if ( $webinar->delayed_timezone_type !== 'user_specific' ) {
                $dateTime->setTimezone( new DateTimeZone( $webinar->auto_timezone_delayed ) );
            }
        }
    }

    return $dateTime;
}

function webinarignition_tz_list()
{
    $zones_array = array();
    $timestamp = time();

    foreach (timezone_identifiers_list() as $key => $zone) {
        $zones_array[$key]['zone'] = $zone;
        $zones_array[$key]['diff_from_GMT'] = webinarignition_get_time_tz( $timestamp, null, $zone, true );
    }
    return $zones_array;
}

function webinarignition_get_time_tz($time, $time_format, $timezone,  $zoneonly = false, $timeonly = false) {
    $current_timezone = date_default_timezone_get();
    if( !empty($timezone) && in_array($timezone, timezone_identifiers_list()) ) {
        date_default_timezone_set($timezone);
    }
    
    if( empty($time_format) ){
        $time_format = get_option('time_format');
    }

    // Times
    $time = date( $time_format, strtotime($time) );  


    $utc = date('Z', strtotime(($time))) / 60 / 60;
    $utc = $utc > 0 ? '+'.$utc : $utc;

    if ($timeonly) {
        $formatted_time = $time;
    } else {
        $formatted_time = ($zoneonly ? "" : $time . " ") . ('UTC' . $utc);
    }

    date_default_timezone_set($current_timezone);
    return $formatted_time;
}


function webinarignition_create_tz_select_list(  $selected_zone, $locale = null )
{
	static $mo_loaded = false, $locale_loaded = null;

	$continents = array( 'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific' );

	// Load translations for continents and cities.
	if ( ! $mo_loaded || $locale !== $locale_loaded ) {
		$locale_loaded = $locale ? $locale : get_locale();
		$mofile        = WP_LANG_DIR . '/continents-cities-' . $locale_loaded . '.mo';
		unload_textdomain( 'continents-cities' );
		load_textdomain( 'continents-cities', $mofile );
		$mo_loaded = true;
	}

	$zonen = array();
	foreach ( timezone_identifiers_list() as $zone ) {
		$zone = explode( '/', $zone );
		if ( ! in_array( $zone[0], $continents, true ) ) {
			continue;
		}

		// This determines what gets set and translated - we don't translate Etc/* strings here, they are done later.
		$exists    = array(
			0 => ( isset( $zone[0] ) && $zone[0] ),
			1 => ( isset( $zone[1] ) && $zone[1] ),
			2 => ( isset( $zone[2] ) && $zone[2] ),
		);
		$exists[3] = ( $exists[0] && 'Etc' !== $zone[0] );
		$exists[4] = ( $exists[1] && $exists[3] );
		$exists[5] = ( $exists[2] && $exists[3] );

		// phpcs:disable WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText
		$zonen[] = array(
			'continent'   => ( $exists[0] ? $zone[0] : '' ),
			'city'        => ( $exists[1] ? $zone[1] : '' ),
			'subcity'     => ( $exists[2] ? $zone[2] : '' ),
			't_continent' => ( $exists[3] ? translate( str_replace( '_', ' ', $zone[0] ), 'continents-cities' ) : '' ),
			't_city'      => ( $exists[4] ? translate( str_replace( '_', ' ', $zone[1] ), 'continents-cities' ) : '' ),
			't_subcity'   => ( $exists[5] ? translate( str_replace( '_', ' ', $zone[2] ), 'continents-cities' ) : '' ),
		);
		// phpcs:enable
	}
	usort( $zonen, '_wp_timezone_choice_usort_callback' );

	$structure = array();

	if ( empty( $selected_zone ) ) {
		$structure[] = '<option selected="selected" value="">' . __( 'Select a city' ) . '</option>'; 
	}

	foreach ( $zonen as $key => $zone ) {
		// Build value in an array to join later.
		$value = array( $zone['continent'] );

		if ( empty( $zone['city'] ) ) {
			// It's at the continent level (generally won't happen).
			$display = $zone['t_continent'];
		} else {
			// It's inside a continent group.

			// Continent optgroup.
			if ( ! isset( $zonen[ $key - 1 ] ) || $zonen[ $key - 1 ]['continent'] !== $zone['continent'] ) {
				$label       = $zone['t_continent'];
				$structure[] = '<optgroup label="' . esc_attr( $label ) . '">';
			}

			// Add the city to the value.
			$value[] = $zone['city'];

			$display = $zone['t_city'];
			if ( ! empty( $zone['subcity'] ) ) {
				// Add the subcity to the value.
				$value[]  = $zone['subcity'];
				$display .= ' - ' . $zone['t_subcity'];
			}
		}

		// Build the value.
		$value    = implode( '/', $value );
		$selected = '';
		if ( $value === $selected_zone ) {
			$selected = 'selected="selected" ';
		}
		$structure[] = '<option ' . $selected . 'value="' . esc_attr( $value ) . '">' . esc_html( $display ) . '</option>';

		// Close continent optgroup.
		if ( ! empty( $zone['city'] ) && ( ! isset( $zonen[ $key + 1 ] ) || ( isset( $zonen[ $key + 1 ] ) && $zonen[ $key + 1 ]['continent'] !== $zone['continent'] ) ) ) {
			$structure[] = '</optgroup>';
		}
	}
        
        return implode( "\n", $structure );

 }
 
 
function webinarignition_get_locale_month($webinar_data, $lead = null)
{
    global $wp_locale;

	$webinar_date_ts = webinarignition_get_webinar_date_ts($webinar_data, $lead);

    return $wp_locale->get_month(wp_date( 'm', $webinar_date_ts ));

}

function webinarignition_get_live_date_day($webinar_data, $lead = null)
{
    
    if( $webinar_data->webinar_date == "AUTO" ){ 
        $autoDate_info          =   explode(" ", $lead->date_picked_and_live);
        $webinarDateArray       =   explode( '-', $autoDate_info[0] );   
        return $webinarDateArray[2];
    } 
    
    $webinarDateArray       = explode( '-', $webinar_data->webinar_date );   
    return $webinarDateArray[1];    

}

function webinarignition_get_localized_date($webinar_data, $lead = null)
{
            global $wp_locale;

            if( $lead && ($webinar_data->webinar_date == "AUTO") ){ 

                $autoDate_info          =   explode(" ", $lead->date_picked_and_live);
                $webinarDateObject      =   DateTime::createFromFormat( 'Y-m-d', $autoDate_info[0], new DateTimeZone('UTC'));       

            } else {
                $webinarDateObject      = DateTime::createFromFormat( 'm-d-Y', $webinar_data->webinar_date, new DateTimeZone('UTC'));

            }
            
            if(is_object($webinarDateObject) ){
                $webinarTimestamp       = $webinarDateObject->getTimestamp();  
                $date_format            = !empty($webinar_data->date_format ) ? $webinar_data->date_format  : ( ($webinar_data->webinar_date == "AUTO") ? 'l, F j, Y' : get_option( "date_format") );
                return date_i18n( $date_format, $webinarTimestamp);                   
            }

            return;
}


/**
 * Get the fully translated date, given the date_string, its format, and the required format of returned string. 
 *
 */
function webinarignition_get_translated_date($date_string, $date_format, $webinar_date_format = '')
{
        $webinar_date_format = !empty( $webinar_date_format ) ? $webinar_date_format  : get_option( "date_format", 'l, F j, Y' );
        $webinarDateObject   = wp_date( $webinar_date_format, strtotime($date_string), new DateTimeZone( 'UTC' )  );

        if($webinarDateObject !== false) {
            return $webinarDateObject;
        }

        return '';
}

function webinarignition_get_localized_time($time)
{
     return date( __( 'g:i a' ), strtotime($time));  
}


function webinarignition_get_localized_week_day($webinar_data, $lead = null) {
    global $wp_locale;

	$webinar_date_ts = webinarignition_get_webinar_date_ts($webinar_data, $lead);

	return $wp_locale->get_weekday(wp_date('w', $webinar_date_ts));
}

/**
 * Convert webinar date into timestamp, based on webinar type and its respective date format
 *
 * @param $webinar_data
 * @param null $lead
 *
 * @return string
 */
function webinarignition_get_webinar_date_ts($webinar_data, $lead = null) {
    $datetime = webinarignition_get_webinar_datetime($webinar_data, $lead);

    return strtotime($datetime);
}

function webinarignition_get_webinar_datetime($webinar_data, $lead = null) {
    $datetime = current_time('mysql');

    if( empty($lead) ) {
        return '';
    }

    if( $lead && $webinar_data->webinar_date === 'AUTO' ) {
        if ( isset( $lead->date_picked_and_live ) && ! empty( $lead->date_picked_and_live ) ) {
            $datetime = $lead->date_picked_and_live;
        }
    } else {
        if( isset($webinar_data->webinar_date) && !empty($webinar_data->webinar_date) ) {
            $datetime = date_format(date_create_from_format('m-d-Y H:i', $webinar_data->webinar_date . ' '. $webinar_data->webinar_start_time), 'Y-m-d H:i:s');
        }
    }

    return $datetime;
}

// ####################################
//
//  Convert UTC To TZID (Olson Time)
//
// ####################################
function webinarignition_convert_utc_to_tzid($utc)
{
    switch ($utc) {
        case "-11":
            return "US/Samoa";
            break;
        case "-10":
            return "HST";
            break;
        case "-930":
            return "Pacific/Marquesas";
            break;
        case "-9":
            return "America/Adak";
            break;
        case "-8":
            return "America/Anchorage";
            break;
        case "-7":
            return "MST";
            break;
        case "-6":
            return "US/Mountain";
            break;
        case "-5":
            return "EST";
            break;
        case "-430":
            return "America/Caracas";
            break;
        case "-4":
            return "America/New_York";
            break;
        case "-3":
            return "Canada/Atlantic";
            break;
        case "-2":
            return "Atlantic/South_Georgia";
            break;
        case "-1":
            return "Atlantic/Cape_Verde";
            break;
        case "0":
            return "GMT";
            break;
        case "+1":
            return "Europe/London";
            break;
        case "+2":
            return "CET";
            break;
        case "+3":
            return "EET";
            break;
        case "+4":
            return "Asia/Dubai";
            break;
        case "+5":
            return "Indian/Maldives";
            break;
        case "+530":
            return "Asia/Calcutta";
            break;
        case "+545":
            return "Asia/Katmandu";
            break;
        case "+6":
            return "Asia/Dacca";
            break;
        case "+630":
            return "Indian/Cocos";
            break;
        case "+7":
            return "Asia/Bangkok";
            break;
        case "+8":
            return "Hongkong";
            break;
        case "+9":
            return "Japan";
            break;
        case "+930":
            return "Australia/Adelaide";
            break;
        case "+10":
            return "Australia/Melbourne";
            break;
        case "+11":
            return "Asia/Sakhalin";
            break;
        case "+12":
            return "NZ";
            break;
        default:
            return $utc;
    }
}


function webinarignition_convert_wp_to_js_date_format ( $webinar_id ) {
    
                if( !empty( $webinar_id ) ){
                    $webinar_data = WebinarignitionManager::get_webinar_data( $webinar_id );
                    $date_format  = !empty( $webinar_data->date_format ) ? $webinar_data->date_format  : ( ($webinar_data->webinar_date == "AUTO") ? 'l, F j, Y' : get_option( "date_format") );
                } else {
                    $current_locale             = determine_locale();
                    $date_format                = __( 'F j, Y' );                   
                }
                
                $patterns = array();
                $patterns[] = '/\\\d/';
                $patterns[] = '/\\\e/';
                $patterns[] = '/l/';
                $patterns[] = '/F/';
                $patterns[] = '/Y/';
                $patterns[] = '/d\s/';
                $patterns[] = '/j/';

                $replacements = array();
                $replacements[] = '!d';
                $replacements[] = 'e';
                $replacements[] = 'dddd';
                $replacements[] = 'mmmm';
                $replacements[] = 'yyyy';
                $replacements[] = 'dd ';
                $replacements[] = 'd';

                return preg_replace($patterns, $replacements, $date_format);                
    
}

function webinarignition_convert_php_to_js_date_format ( $date_format ) {
    
                $patterns = array();
                $patterns[] = '/\\\d/';
                $patterns[] = '/\\\e/';
                $patterns[] = '/l/';
                $patterns[] = '/F/';
                $patterns[] = '/Y/';
                $patterns[] = '/d\s/';
                $patterns[] = '/j/';
                $patterns[] = '/D/'; 

                $replacements = array();
                $replacements[] = '!d';
                $replacements[] = 'e';
                $replacements[] = 'dddd';
                $replacements[] = 'mmmm';
                $replacements[] = 'yyyy';
                $replacements[] = 'dd ';
                $replacements[] = 'd';
                $replacements[] = 'ddd';

                return preg_replace($patterns, $replacements, $date_format);   
    
}

function webinarignition_convert_wp_to_js_time_format ( $wp_time_format = '' ) {

	if( empty( $wp_time_format ) ){
		$wp_time_format = get_option("time_format");
		if( empty( $wp_time_format ) ) {
			$wp_time_format = 'g:i a'; //Use default time format if missing in settings
		}
	}

    $patterns = array();
    $patterns[] = '/\\\h/';
    $patterns[] = '/h/'; //Hour, 12-hour, with leading zeros	01–12
    $patterns[] = '/g/'; //Hour, 12-hour, without leading zeros	1–12
    $patterns[] = '/H/'; //Hour, 24-hour, with leading zeros	00-23
    $patterns[] = '/G/'; //Hour, 24-hour, without leading zeros	0-23
    $patterns[] = '/!hh/';

    $replacements = array();
    $replacements[] = '!h';
    $replacements[] = 'hh'; //Hour in 12-hour format with a leading zero	01 – 12
    $replacements[] = 'h'; //Hour in 12-hour format	1 – 12
    $replacements[] = 'HH'; //Hour in 24-hour format with a leading zero	00 – 23
    $replacements[] = 'H'; //Hour in 24-hour format	0 – 23
    $replacements[] = '!h';

     return preg_replace($patterns, $replacements, $wp_time_format);
}

/**
 * Get webinar timezone depending on webinar type and timezone type i.e. fixed or user specific
 *
 * @param $webinar_data
 * @param null $lead
 *
 * @return mixed|string
 */
function webinarignition_get_webinar_timezone($webinar_data, $user_js_detected_timezone, $lead = null) {
	$webinar_time_zone = wp_timezone_string(); //Set wp timezone by default

	if( webinarignition_is_auto($webinar_data) ) { //Evergreen webinar, get fixed or user specific timezone

		$timezone_type   = isset($webinar_data->auto_timezone_type) ? $webinar_data->auto_timezone_type : null;

		if( !empty($timezone_type) && $timezone_type == 'fixed' ) { //Get fixed timezone
			if( isset($webinar_data->auto_timezone_custom) && !empty($webinar_data->auto_timezone_custom) ) {
				$webinar_time_zone = $webinar_data->auto_timezone_custom;
			}
		} else { //Get user specific timezone
			if( !empty($user_js_detected_timezone) ) {
				$webinar_time_zone = $user_js_detected_timezone;
			} else if( !empty($lead) && isset($lead->lead_timezone) && !empty($lead->lead_timezone) ) {
				$webinar_time_zone = $lead->lead_timezone;
			}
		}

	} else { //Live webinar, get fixed timezone
		if( isset($webinar_data->webinar_timezone) && !empty($webinar_data->webinar_timezone) ) {
			$webinar_time_zone = $webinar_data->webinar_timezone;
		}
	}

	return $webinar_time_zone;
}