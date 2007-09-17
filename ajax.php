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
 * Provide Audit Trail AJAX
 *
 * @package Audit Trail
 * @author John Godley
 * @copyright Copyright (C) John Godley
 **/

include ('../../../wp-config.php');

class AT_AJAX extends AT_Plugin
{
	function AT_AJAX ($id, $command)
	{
		if (!current_user_can ('edit_plugins'))
			die ('<p style="color: red">You are not allowed access to this resource</p>');
		
		$_POST = stripslashes_deep ($_POST);
	
		include (dirname (__FILE__).'/models/diff.php');
		
		$this->register_plugin ('drain-hole', __FILE__);
		if (method_exists ($this, $command))
			$this->$command ($id);
		else
			die ('<p style="color: red">That function is not defined</p>');
	}

	function show ($item)
	{
		$item = AT_Audit::get ($item);
		$this->render_admin ('trail_details', array ('item' => $item));
	}
	
	function close ($item)
	{
		$item = AT_Audit::get ($item);
		$this->render_admin ('trail_item', array ('item' => $item));
	}
}

$id  = $_GET['id'];
$cmd = $_GET['cmd'];

$obj = new AT_AJAX ($id, $cmd);

?>