<?php
/**
 * Class WebinarignitionLeadsManager
 */

class WebinarignitionLeadsManager {
    private static function get_meta_table_name($table) {
        global $wpdb;

        if ('leads' === $table) {
            return $wpdb->prefix . 'webinarignition_leadmeta';
        } elseif ('leads_evergreen' === $table) {
            return $wpdb->prefix . 'webinarignition_lead_evergreenmeta';
        }
    }

    public static function get_meta_schema($table) {
        if (!in_array($table, ['leads', 'leads_evergreen'])) {
            return false;
        }

        global $wpdb;

        $collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';
        $table_name = self::get_meta_table_name($table);
        $max_index_length = 191;

        $sql = "
CREATE TABLE {$table_name} (
    meta_id bigint(20) unsigned NOT NULL auto_increment,
	lead_id bigint(20) unsigned NOT NULL default '0',
	meta_key varchar(255) default NULL,
	meta_value longtext,
	PRIMARY KEY  (meta_id),
	KEY lead_id (lead_id),
	KEY meta_key (meta_key($max_index_length))
) {$collate};
        ";

        return $sql;
    }

    public static function create_meta_tables() {
        global $wpdb;

        $wpdb->hide_errors();

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta( self::get_meta_schema('leads') );
        dbDelta( self::get_meta_schema('leads_evergreen') );
    }

    public static function create_lead_meta($lead_id, $meta_key, $meta_value, $webinar_type) {
        $table = 'live' === $webinar_type ? 'leads' : 'leads_evergreen';
        $table_name = self::get_meta_table_name($table);

        global $wpdb;

        $wpdb->insert(
            $table_name,
            [
                'lead_id'    => $lead_id,
                'meta_key'    => $meta_key,
                'meta_value'     => $meta_value,
            ], [ '%d', '%s', '%s' ] );

		return $wpdb->insert_id;
    }

    public static function update_lead_meta($lead_id, $meta_key, $meta_value, $webinar_type) {
        $table = 'live' === $webinar_type ? 'leads' : 'leads_evergreen';
        $table_name = self::get_meta_table_name($table);

        global $wpdb;

        $lead_meta = self::get_lead_meta($lead_id, $meta_key, $webinar_type);

        if (empty($lead_meta['meta_id'])) {
            return self::create_lead_meta($lead_id, $meta_key, $meta_value, $webinar_type);
        }

        $wpdb->update(
            $table_name,
            [
                'lead_id'    => $lead_id,
                'meta_key'    => $meta_key,
                'meta_value'     => $meta_value,
            ], [ 'meta_id' => $lead_meta['meta_id'] ], [ '%d', '%s', '%s' ], [ '%d' ] );

        return $wpdb->insert_id;
    }

    public static function get_lead_meta($lead_id, $meta_key, $webinar_type, $single = true) {
        $table = 'live' === $webinar_type ? 'leads' : 'leads_evergreen';
        $table_name = self::get_meta_table_name($table);

        global $wpdb;
        $sql        = "SELECT * FROM {$table_name} WHERE lead_id = %d AND meta_key = %s ORDER BY meta_id DESC";
        $safe_query = $wpdb->prepare( $sql,  [ $lead_id, $meta_key ]  );
        $result     = $wpdb->get_results($safe_query, ARRAY_A);

        if (!empty($result)) {
            return $result[0];
        }

        return [];
    }

	/**
	 * Fix optName field from optFName to optName if available
	 *
	 * @param array $lead_meta
	 *
	 * @return array
	 */
    public static function fix_optName( array &$lead_meta ) {

	    if( isset( $lead_meta['optName']['value'] ) && $lead_meta['optName']['value'] === '#firstlast#' && isset( $lead_meta['optFName']['value'] ) && isset( $lead_meta['optLName']['value'] ) ) {
	    	unset($lead_meta['optName']);
		    $lead_meta = self::replace_key_function( $lead_meta, 'optFName', 'optName' );
	    }

		return $lead_meta;
	}

	private static function replace_key_function($array, $key1, $key2) {
		$keys = array_keys($array);
		$index = array_search($key1, $keys);

		if ($index !== false) {
			$keys[$index] = $key2;
			$array = array_combine($keys, $array);
		}

		return $array;
	}
}