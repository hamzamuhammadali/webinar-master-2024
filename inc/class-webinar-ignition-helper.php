<?php
/**
 * Helper function class to be used globally
 *
 * @link       https://wp-centric.com/
 * @since      2.9.1
 *
 * @package    Webinar_Ignition
 * @subpackage Webinar_Ignition/inc
 * @since 2.9.1
 */
if( !class_exists('Webinar_Ignition_Helper') ) {

	class Webinar_Ignition_Helper {

		/**
		 * Debug Log
		 *
		 * @param $var
		 * @param bool $print
		 * @param bool $show_execute_at
		 */
		public static function debug_log($var, $print=true, $show_execute_at=false) {
			ob_start();

			if($show_execute_at) {
				$bt = debug_backtrace();
				$caller = array_shift($bt);
				$execute_at = $caller['file'] . ':' . $caller['line'] . "\n";
				echo $execute_at;
			}

			if( $print ) {
				if( is_object($var) || is_array($var) ) {
					echo print_r($var, true);
				} else {
					echo $var;
				}
			} else {
				var_dump($var);
			}

			error_log(ob_get_clean());
		}

		public static function doing_cron() {

			// Bail if not doing WordPress cron (>4.8.0)
			if ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) {
				return true;

				// Bail if not doing WordPress cron (<4.8.0)
			} elseif ( defined( 'DOING_CRON' ) && ( true === DOING_CRON ) ) {
				return true;
			}

			// Default to false
			return false;
		}

		public static function str_replace_first($search, $replace, $subject) {
			$search = '/'.preg_quote($search, '/').'/';
			return preg_replace($search, $replace, $subject, 1);
		}

		public static function ar_field_to_opt($ar_field_name) {
			$field_name = self::str_replace_first('ar_', '', $ar_field_name);
			$field_name = ucwords($field_name, '_');
			if(strpos($field_name, 'Custom', '0') === 0) {
				$field_name = 'opt' . $field_name;
			} else {
				$field_name = 'opt' . str_replace('_', '',$field_name);
			}

			if($field_name === 'optLname') {
				$field_name = 'optLName';
			} else if($field_name === 'optPrivacyPolicy') {
				$field_name = 'optGDPR_PP';
			} else if($field_name === 'optTermsAndConditions') {
				$field_name = 'optGDPR_TC';
			} else if($field_name === 'optMailingList') {
				$field_name = 'optGDPR_ML';
			}

			return $field_name;
		}

		/**
		 * Validate if given timezone string is valid
		 *
		 * @param $timezoneId
		 *
		 * @return DateTimeZone|false
		 */
		public static function getValidTimezoneId($timezoneId) {
			try {
				new DateTimeZone($timezoneId);
			} catch(Exception $e) {
				$timezoneId = wp_timezone_string();
			}

			return $timezoneId;
		}
	}
}