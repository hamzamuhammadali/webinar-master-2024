<?php
defined( 'ABSPATH' ) || exit;

// WP_List_Table is not loaded automatically so we need to load it in our application
if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class WebinarIgnition_Admin_Webhooks_List_Table extends WP_List_Table {

	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$data = $this->table_data();
		usort( $data, array( &$this, 'sort_data' ) );

		$perPage = 10;
		$currentPage = $this->get_pagenum();
		$totalItems = count($data);

		$this->set_pagination_args( array(
			'total_items' => $totalItems,
			'per_page'    => $perPage
		) );

		$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $data;
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns() {
		$columns = array(
			'name' => 'Name',
			'trigger' => 'Trigger',
			'request_method' => 'Method',
			'request_format' => 'Format',
			'is_active' => 'Status',
			'modified' => 'Updated At'
		);

		return $columns;
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns() {
		return array();
	}

	/**
	 * Define the sortable columns
	 *
	 * @return Array
	 */
	public function get_sortable_columns() {
		return array('name' => array('name', false), 'trigger' => array('trigger', false), 'modified' => array('modified', true));
	}

	/**
	 * Get the table data
	 *
	 * @return Array
	 */
	private function table_data() {
		$data = array();
		global $wpdb;
		$table = "{$wpdb->prefix}webinarignition_webhooks";
		$query = "SELECT * FROM {$table} WHERE 1=1";
		$results = $wpdb->get_results($query, ARRAY_A);

		if($results) {
			$data = $results;
        }

//		print_r($data); exit;



		return $data;
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  Array $item        Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default( $item, $column_name ) {
		switch( $column_name ) {
			case 'name':
			case 'trigger':
			    $triggers = WebinarIgnitionWebhooks::webinarignition_webhook_get_triggers();
			    return isset($triggers[$item[ $column_name ]]) ? $triggers[$item[ $column_name ]] : '';
			case 'is_active':
			    return isset($item[ $column_name ]) && absint($item[ $column_name ]) === 1 ? __('Active') : __('Inactive');
			case 'request_method':
				return isset($item[ $column_name ]) && absint($item[ $column_name ]) === 1 ? __('POST') : __('GET');
			case 'request_format':
				return isset($item[ $column_name ]) && absint($item[ $column_name ]) === 1 ? __('FORM') : __('JSON');
            case 'modified':
	            $date_format = get_option( 'date_format' );
	            $time_format = get_option( 'time_format' );
				return wp_date("{$date_format} {$time_format}", strtotime($item[ $column_name ]));
			default:
				return print_r( $item, true ) ;
		}
	}

	function column_name($item) {
		$actions = array(
			'edit'      => sprintf('<a href="?page=%s&tab=%s&action=%s&id=%s">%s</a>',$_REQUEST['page'], $_REQUEST['tab'], 'edit', $item['id'], __('Edit')),
			'delete'      => sprintf('<a href="?page=%s&tab=%s&action=%s&id=%s">%s</a>',$_REQUEST['page'], $_REQUEST['tab'], 'delete', $item['id'], __('Delete'))
		);

		return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions) );
	}

	/**
	 * Allows you to sort the data by the variables set in the $_GET
	 *
	 * @return Mixed
	 */
	private function sort_data( $a, $b ) {
		// Set defaults
		$orderby = 'modified';
		$order = 'asc';

		// If orderby is set, use this as the sort column
		if(!empty($_GET['orderby'])) {
			$orderby = $_GET['orderby'];
		}

		// If order is set use this as the order
		if(!empty($_GET['order'])) {
			$order = $_GET['order'];
		}

		$result = strcmp( $a[$orderby], $b[$orderby] );

		if($order === 'asc') {
			return $result;
		}

		return -$result;
	}

	function extra_tablenav( $which ) {
		if ( $which == "top" ) {
			?>
			<a class="add-new-h2" href="<?php echo add_query_arg(['page' => 'webinarignition_settings', 'tab' => 'webhooks', 'action' => 'edit', 'id' => 0], admin_url('admin.php')); ?>"><?php _e('Add webhook');?></a>
			<?php
		}
	}
}