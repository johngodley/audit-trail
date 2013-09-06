<?php

class Audit_Trail_Table extends WP_List_Table {
	function __construct(){
		global $status, $page;

		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'item',     //singular name of the listed records
			'plural'    => 'items',    //plural name of the listed records
			'ajax'      => false        //does this table support ajax?
		) );
	}

	function column_user_id( $item ){
		if ( $item->user_id > 0 )
			return '<a href="user-edit.php?user_id='.esc_attr( $item->user_id ).'&amp;wp_http_referer=%2Fsite%2Fwp-admin%2Fusers.php">'.esc_html( $item->username ).'</a>';
		return '';
	}

	function column_happened_at( $item ) {
		return date_i18n( get_option( 'date_format' ), $item->happened_at ).' '.gmdate( get_option( 'time_format' ), $item->happened_at );
	}

	function column_ip( $item ) {
		return '<a href="http://urbangiraffe.com/map/?ip='.esc_attr( long2ip( $item->ip ) ).'&amp;from=audittrail">'.long2ip( $item->ip ).'</a>';
	}

	function column_item_id( $item ) {
		return $item->get_item();
	}

	function column_operation( $item ) {
		return $item->get_operation();
	}

	function column_cb($item){
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/ $item->id                //The value of the checkbox should be the record's id
		);
	}

	function get_columns(){
		$columns = array(
			'cb'          => '<input type="checkbox" />', //Render a checkbox instead of text
			'user_id'     => __( 'User', 'audit-trail' ),
			'operation'   => __( 'Action', 'audit-trail' ),
			'item_id'     => __( 'Target', 'audit-trail' ),
			'happened_at' => __( 'Date', 'audit-trail' ),
			'ip'          => __( 'IP', 'audit-trail' ),
		);
		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'user_id'     => array( 'user_id',false ),
			'operation'   => array( 'operation',false),
			'item_id'     => array( 'item_id',false ),
			'happened_at' => array( 'item_id',true ),
			'ip'          => array( 'item_id',false ),
		);
		return $sortable_columns;
	}

	function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'audit-trail' )
		);
		return $actions;
	}

	function process_bulk_action() {
		if ( 'delete' === $this->current_action() ) {
			foreach( $_POST['item'] AS $id ) {
				AT_Audit::delete( intval( $id ) );
			}
		}
	}

	function prepare_items() {
		global $wpdb;

		$per_page = 25;
		$hidden   = array();
		$columns  = $this->get_columns();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		// Process any stuff
		$this->process_bulk_action();

		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'id';
		$order   = ( ! empty($_GET['order'] ) ) ? strtolower( $_GET['order'] ) : 'desc';

		if ( !in_array( $orderby, array_keys( $this->get_sortable_columns() ) ) )
			$orderby = 'id';

		if ( !in_array( $order, array( 'asc', 'desc' ) ) )
			$order = 'desc';

		$total_items  = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}audit_trail" );

		global $wpdb;

		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT {$wpdb->prefix}audit_trail.*,{$wpdb->users}.user_nicename AS username FROM {$wpdb->prefix}audit_trail LEFT JOIN {$wpdb->users} ON {$wpdb->users}.ID={$wpdb->prefix}audit_trail.user_id ORDER BY ".$orderby.' '.$order." LIMIT %d,%d", ( $this->get_pagenum() - 1 ) * $per_page, $per_page ) );
		$data = array();
		foreach ( (array)$rows AS $row ) {
			$data[] = new AT_Audit( $row );
		}

		$this->items = $data;

		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
		) );
	}
}
