<?php
/* ============================================================================================================
	 This software is provided "as is" and any express or implied warranties, including, but not limited to, the
	 implied warranties of merchantibility and fitness for a particular purpose are disclaimed. In no event shall
	 the copyright owner or contributors be liable for any direct, indirect, incidental, special, exemplary, or
	 consequential damages (including, but not limited to, procurement of substitute goods or services; loss of
	 use, data, or profits; or business interruption) however caused and on any theory of liability, whether in
	 contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of
	 this software, even if advised of the possibility of such damage.
   
	 This software is provided free-to-use, but is not free software.  The copyright and ownership remains
	 entirely with the author.  Please distribute and use as necessary, in a personal or commercial environment,
	 but it cannot be sold or re-used without express consent from the author.
   ============================================================================================================ */

/**
 * Class to represent audit trail log items
 *
 * @package Audit Trail
 * @author John Godley
 * @copyright Copyright (C) John Godley
 **/

class AT_Audit
{
	/**
	 * Constructor accepts an array of values with which to seed the object
	 *
	 * @param array $details Array of starting values (variable name => value)
	 * @return void
	 **/
	
	function AT_Audit ($details = '') {
		if (is_array ($details)) {
			foreach ($details AS $key => $value)
				$this->$key = $value;
		}
		
		$this->happened_at = mysql2date ('U', $this->happened_at);
	}
	
	
	/**
	 * Get a log item (username is filled in)
	 *
	 * @param int $id Log ID
	 * @return AT_Audit
	 **/
	
	function get ($id) {
		global $wpdb;
		
		$row = $wpdb->get_row ("SELECT {$wpdb->prefix}audit_trail.*,{$wpdb->users}.user_nicename AS username FROM {$wpdb->prefix}audit_trail LEFT JOIN {$wpdb->users} ON {$wpdb->users}.ID={$wpdb->prefix}audit_trail.user_id WHERE {$wpdb->prefix}audit_trail.id='$id'", ARRAY_A);
		if ($row)
			return new AT_Audit ($row);
		return false;
	}
	
	
	/**
	 * Get all log items (username is filled in)
	 *
	 * @return array Array of AT_Audit items
	 **/
	
	function get_everything () {
		global $wpdb;
		
		$rows = $wpdb->get_results ("SELECT {$wpdb->prefix}audit_trail.*,{$wpdb->users}.user_nicename AS username FROM {$wpdb->prefix}audit_trail LEFT JOIN {$wpdb->users} ON {$wpdb->users}.ID={$wpdb->prefix}audit_trail.user_id", ARRAY_A);
		$data = array ();
		if ($rows) {
			foreach ($rows AS $row)
				$data[] = new AT_Audit ($row);
		}
		
		return $data;
	}
	
	
	/**
	 * Get all log items restricted by a pager (username is filled in)
	 *
	 * @param AT_Pager $pager Pager object
	 * @return array Array of AT_Audit items
	 **/
	
	function get_all (&$pager) {
		global $wpdb;
		
		$pager->set_total ($wpdb->get_var ("SELECT COUNT(*) FROM {$wpdb->prefix}audit_trail ".$pager->to_conditions ('', array ('data'))));
		$rows = $wpdb->get_results ("SELECT {$wpdb->prefix}audit_trail.*,{$wpdb->users}.user_nicename AS username FROM {$wpdb->prefix}audit_trail LEFT JOIN {$wpdb->users} ON {$wpdb->users}.ID={$wpdb->prefix}audit_trail.user_id ".$pager->to_limits ('', array ('data', "{$wpdb->users}.user_nicename")), ARRAY_A);
		$data = array ();
		if ($rows) {
			foreach ($rows AS $row)
				$data[] = new AT_Audit ($row);
		}
		
		return $data;
	}
	
	
	/**
	 * Get all log items for a given post (username is filled in)
	 *
	 * @param int $id Post ID
	 * @param int $max Maximum number of items to return
	 * @return array Array of AT_Audit items
	 **/
	
	function get_by_post ($id, $max = 10) {
		global $wpdb;
		
		if (get_option ('audit_post_order') == true)
			$order = 'ASC';
		else
			$order = 'DESC';

		$rows = $wpdb->get_results ("SELECT {$wpdb->prefix}audit_trail.*,{$wpdb->users}.user_nicename AS username FROM {$wpdb->prefix}audit_trail LEFT JOIN {$wpdb->users} ON {$wpdb->users}.ID={$wpdb->prefix}audit_trail.user_id WHERE item_id=$id AND operation='save_post' ORDER BY happened_at $order LIMIT 1,$max", ARRAY_A);
		$data = array ();
		if ($rows) {
			foreach ($rows AS $row)
				$data[] = new AT_Audit ($row);
		}
		
		return $data;
	}
	
	
	/**
	 * Delete a log item
	 *
	 * @param int $id Item to delete
	 * @return void
	 **/
	
	function delete ($id) {
		global $wpdb;
		$wpdb->query ("DELETE FROM {$wpdb->prefix}audit_trail WHERE id=$id");
	}
	
	
	/**
	 * Create a new log item
	 *
	 * @param string $operation What function is being monitored (e.g. 'save_post')
	 * @param int $item ID to the item being monitored (e.g post ID, comment ID)
	 * @param string $data Any data associated with the item (e.g. the post)
	 * @param string $title A title string in case the data may change in the future (i.e the current post title)
	 * @param int $user The user ID (if different from the current user)
	 * @return void
	 **/
	
	function create ($operation, $item = '', $data = '', $title = '', $user = '') {
		global $wpdb, $user_ID;

		$ip = 0;
		if (isset ($_SERVER['REMOTE_ADDR']))
		  $ip = $_SERVER['REMOTE_ADDR'];
		else if (isset ($_SERVER['HTTP_X_FORWARDED_FOR']))
		  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		
		$ip = sprintf ('%u', ip2long ($ip));

		if ($user == '')
			$user = $user_ID;

		$data = maybe_serialize( $data );

		$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}audit_trail (user_id,ip,operation,item_id,happened_at,data,title) VALUES(%d,%s,%s,%s,%s,%s,%s)", $user, $ip, $operation, $item, current_time( 'mysql' ), $data, $title ) );
	}
	
	
	/**
	 * undocumented function
	 * @todo explain
	 * @return void
	 **/
	
	function get_operation ($open = true) {
		$this->message = '';
		$obj = apply_filters ('audit_show_operation', $this);
		if (is_object ($obj) && !empty ($obj->message)) {
			if ($open == true)
				return str_replace ('view_audit', 'view_audit_open', $obj->message);
			return str_replace ('view_audit', 'view_audit_close', $obj->message);
		}
		
		return $this->operation;
	}
	
	
	/**
	 * undocumented function
	 *
	 * @todo explain
	 * @return void
	 **/
	
	function get_item () {
		$this->message = '';
		$obj = apply_filters ('audit_show_item', $this);
		if (is_object ($obj) && !empty ($obj->message))
			return $obj->message;
		return $this->item_id;
	}
	
	
	/**
	 * undocumented function
	 *
	 * @todo explain
	 * @return void
	 **/
	
	function get_details () {
		$this->message = '';
		$obj = apply_filters ('audit_show_details', $this);
		if (is_object ($obj) && !empty ($obj->message))
			$details = $obj->message;
		return $this->get_operation (false).'<br/>'.$details;
	}
	
	
	/**
	 * Delete all items that are over a given number of days old
	 *
	 * @param int $days Number of days old
	 * @return void
	 **/
	
	function expire ($days) {
		global $wpdb;
		
		if ($days > 0)
			$wpdb->query ("DELETE FROM {$wpdb->prefix}audit_trail WHERE DATE_SUB(CURDATE(),INTERVAL $days DAY) > happened_at");
	}
}

?>
