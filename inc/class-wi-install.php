<?php

defined('ABSPATH') || exit;

abstract class WI_Install{

	public static function install(){

		$page_number = get_option('wi_data_conversion_page', 0);

		if( !empty($page_number ) ) {
			return;
		}

		update_option('wi_data_conversion_status', 'start');

	}
}