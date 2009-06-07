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
 * Provides Audit Trail post AJAX
 *
 * @package Drain Hole
 * @author John Godley
 * @copyright Copyright (C) John Godley
 **/

include ('../../../wp-config.php');

class AT_AJAX extends AT_Plugin
{
	function AT_AJAX ($id, $command)
	{
		if (!current_user_can ('edit_post', $id))
			die ('<p style="color: red">You are not allowed access to this resource</p>');
		
		$_POST = stripslashes_deep ($_POST);
	
		include (dirname (__FILE__).'/models/diff.php');
		
		$this->register_plugin ('drain-hole', __FILE__);
		if (method_exists ($this, $command))
			$this->$command ($id);
		else
			die ('<p style="color: red">That function is not defined</p>');
	}

	function view ($id)
	{
		$item = AT_Audit::get ($id);
		$post = unserialize ($item->data);
		$real = get_post ($item->item_id);
		
		$diff = new AT_Diff ($post->post_content, $real->post_content);
		
		$this->render_admin ('view_post', array ('item' => $item, 'diff' => $diff, 'id' => $id));
	}
	
	function restore ($id)
	{
		$item = AT_Audit::get ($id);
		$post = unserialize ($item->data);

		do_action ('audit_restore', $item->id);

		$_POST['ID']           = $item->item_id;
		$_POST['post_content'] = $wpdb->escape ($post->post_content);
		$_POST['post_excerpt'] = $wpdb->escape ($post->post_excerpt);
		$_POST['post_title']   = $post->post_title;
		$_POST['post_name']    = $post->post_name;
		$_POST['post_author']  = $post->post_author;

		wp_update_post ($_POST);	
	}
	
	function delete ($id)
	{
		AT_Audit::delete ($id);
	}
}

$id  = $_GET['id'];
$cmd = $_GET['cmd'];

$obj = new AT_AJAX ($id, $cmd);

?>