<?php defined( 'ABSPATH' ) || exit;

class WebinarignitionPowerups {
    public static function is_powerups_enabled($webinar_data) {
    	return true; //Disable license check, Ultimate for all
        $statusCheck = WebinarignitionLicense::get_license_level();

        if ( !empty($statusCheck->is_trial) || 'enterprise_powerup' === $statusCheck->switch ) {
            return true;
        }

        $type = $webinar_data->webinar_date === "AUTO" ? 'auto' : 'live';

        if ('free' === $statusCheck->switch) {
            if (!empty($statusCheck->is_registered) && 'auto' === $type) {
                return true;
            }
        }

        return false;
    }

    public static function is_shortcodes_enabled($webinar_data) {
        return self::is_powerups_enabled($webinar_data);
    }

    public static function is_multiple_cta_enabled($webinar_data) {
        return self::is_powerups_enabled($webinar_data);
    }

    public static function is_multiple_auto_time_enabled($webinar_data) {
        return self::is_powerups_enabled($webinar_data);
    }

    public static function is_too_late_lockout_enabled($webinar_data) {
        return self::is_powerups_enabled($webinar_data);
    }

    public static function is_modern_template_enabled($webinar_data) {
        return self::is_powerups_enabled($webinar_data);
    }

    public static function is_multiple_support_enabled($webinar_data) {
        return self::is_powerups_enabled($webinar_data);
    }

    public static function is_two_way_qa_enabled($webinar_data) {
        $type = $webinar_data->webinar_date === "AUTO" ? 'auto' : 'live';

        if ('auto' === $type) return false;

        return self::is_powerups_enabled($webinar_data);
    }

    public static function is_secure_access_enabled($webinar_data) {
        return self::is_powerups_enabled($webinar_data);
    }

    public static function is_ultimate($is_trial = false) {
    	return true; //Disable license check, Ultimate for all
	    $statusCheck = WebinarignitionLicense::get_license_level();

	    if ( 'enterprise_powerup' === $statusCheck->switch ) {
	    	if( $is_trial ) {
			    return !empty($statusCheck->is_trial);
		    }

	    	return true;
	    }

	    return false;
    }
}
