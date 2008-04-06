<?php
/*
Plugin Name: Audit Trail
Plugin URI: http://urbangiraffe.com/plugins/audit-trail/
Description: Keep a log of exactly what is happening behind the scenes of your WordPress blog
Author: John Godley
Version: 1.0.9
Author URI: http://urbangiraffe.com
============================================================================================================

0.1   - Initial release
0.2   - Added versioning history
0.3   - Made work with different database prefixes
1.0   - Revised code, more AJAX action, extensible auditors
1.0.3 - Fix typos.  Add option to reverse post edit order
1.0.4 - Support for Admin SSL
1.0.5 - Fix expiry, stop logging auto-saves
1.0.6 - Fix warning, allow searching by username
1.0.7 - Fix favicon.ico logs, ignore certain users, track failed login attempts
1.0.8 - Show log items according to blog timezone offset
1.0.9 - WP 2.5 compatability

============================================================================================================
This software is provided "as is" and any express or implied warranties, including, but not limited to, the
implied warranties of merchantibility and fitness for a particular purpose are disclaimed. In no event shall
the copyright owner or contributors be liable for any direct, indirect, incidental, special, exemplary, or
consequential damages (including, but not limited to, procurement of substitute goods or services; loss of
use, data, or profits; or business interruption) however caused and on any theory of liability, whether in
contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of
this software, even if advised of the possibility of such damage.

This software is provided free-to-use, but is not free software.  The copyright and ownership remains entirely
with the author.  Please distribute and use as necessary, in a personal or commercial environment, but it cannot
be sold or re-used without express consent from the author.
============================================================================================================ 

Available filters are:
  - audit_collect        - Passed an array of methods to monitor, return the array with any additions
	- audit_show_operation - Passed an AT_Audit object, return the object with 'message' changed for type of operation
  - audit_show_item      - Passed an AT_Audit object, return the object with 'message' changed for type of item
  - audit_show_details   - Passed an AT_Audit object, return a message to display when the operation is clicked for more details

Available actions are:
  - audit_listen - Passed the name of a method to monitor.  Add appropriate filters/actions to monitor the method
*/

include (dirname (__FILE__).'/plugin.php');

define ('AUDIT_TRAIL_VERSION', '0.3');


/**
 * Audit Trail plugin
 *
 * @package Audit Trail
 **/

class Audit_Trail extends AT_Plugin
{
	var $auditor;
	
	
	/**
	 * Constructor hooks all the appropriate filters and actions for the plugin, as well as creating the auditor
	 * object which monitors everything else
	 *
	 * @return void
	 **/
	
	function Audit_Trail ()
	{
		// Check database is setup
		include (dirname (__FILE__).'/models/auditor.php');
		include (dirname (__FILE__).'/models/audit.php');
		
		if (is_admin ())
		{
			include (dirname (__FILE__).'/models/pager.php');
			
			$this->register_plugin ('audit-trail', __FILE__);
			
			$this->add_action ('admin_menu');
			$this->add_action ('activate_audit-trail/audit-trail.php', 'activate');
			
			wp_enqueue_script ('prototype');
			
			if (strstr ($_SERVER['REQUEST_URI'], 'audit-trail.php') !== false)
				$this->add_action ('admin_head');

			if (get_option ('audit_post') && (strpos ($_SERVER['REQUEST_URI'], 'post.php') !== false || strpos ($_SERVER['REQUEST_URI'], 'page.php') !== false))
			{
				$this->add_action ('admin_head',         'admin_post', 10);
				
				if ($this->is_25 ())
				{
					$this->add_action ('dbx_post_advanced',  'edit_box_advanced', 10);
					$this->add_action ('edit_page_form',  'edit_box_advanced', 10);
				}
				else
				{
					$this->add_action ('edit_page_form',     'edit_box', 10);
					$this->add_action ('edit_form_advanced', 'edit_box', 10);
				}
			}
		}
		
		// Add ourself to the Audit Trail functions
		$this->auditor = new AT_Auditor;

		$this->add_action ('plugins_loaded');
	}


	/**
	 * After all the plugins have loaded this starts listening for all registered filters/actions
	 *
	 * @return void
	 **/
	
	function plugins_loaded ()
	{
		$methods = get_option ('audit_methods');
		if (!empty ($methods))
		{
			foreach ($methods AS $name)
				do_action ('audit_listen', $name);
		}
	}


	/**
	 * Creates the database and upgrades any existing data
	 *
	 * @return void
	 **/
	
	function activate ()
	{
		global $wpdb;
		
		if (get_option ('audit_trail') == '0.1' || get_option ('audit_trail') == 'true')
			$wpdb->query ("DROP TABLE {$wpdb->prefix}audit_trail");
		
		if (get_option ('audit_trail') != '0.2')
		{
			$wpdb->query ("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}audit_trail` (
			  `id` int(11) NOT NULL auto_increment,
			  `operation` varchar(40) NOT NULL default '',
			  `user_id` int(11) NOT NULL,
  			`ip` int(11) unsigned NOT NULL default '0',
			  `happened_at` datetime NOT NULL,
			  `item_id` int(11) default NULL,
			  `data` longtext,
			  `title` varchar(100) default NULL,
			  PRIMARY KEY  (`id`)
			)");
		}
		
		if (get_option ('audit_trail') != '0.3')
			$wpdb->query ("ALTER TABLE `{$wpdb->prefix}audit_trail` CHANGE `ip` `ip` int(11) UNSIGNED NOT NULL DEFAULT '0'");
	
		update_option ('audit_trail', AUDIT_TRAIL_VERSION);
	}
	
	
	function is_25 ()
	{
		global $wp_version;
		if (version_compare ('2.5', $wp_version) <= 0)
			return true;
		return false;
	}
	
	/**
	 * Injects CSS and JS into the audit trail section
	 *
	 * @return void
	 **/
	
	function admin_head ()
	{
		$this->render_admin ('head');
	}
	
	
	/**
	 * Inject CSS/JS for post editing screen
	 *
	 * @return void
	 **/
	
	function admin_post ()
	{
		$this->render_admin ('head_post');
	}
	
	
	/**
	 * Inject Audit Trail into the menu
	 *
	 * @return void
	 **/
	
	function admin_menu ()
	{
		if (current_user_can ('edit_plugins') || current_user_can ('audit_trail'))
  		add_management_page (__("Audit Trail",'audit-trail'), __("Audit Trail",'audit-trail'), "edit_post", basename (__FILE__), array ($this, "admin_screen"));
	}
	
	
	/**
	 * Inserts the edit box into the edit post/page area
	 *
	 * @return void
	 **/
	
	function edit_box ()
	{
		global $post;
		$this->render_admin ('edit_box', array ('trail' => AT_Audit::get_by_post ($post->ID)));
	}
	
	function edit_box_advanced ()
	{
		global $post;
		$this->render_admin ('edit_box_25', array ('trail' => AT_Audit::get_by_post ($post->ID)));
	}
	
	function submenu ($inwrap = false)
	{
		// Decide what to do
		$sub = isset ($_GET['sub']) ? $_GET['sub'] : '';
	  $url = explode ('&', $_SERVER['REQUEST_URI']);
	  $url = $url[0];

		if (!$this->is_25 () && $inwrap == false)
			$this->render_admin ('submenu', array ('url' => $url, 'sub' => $sub, 'class' => 'id="subsubmenu"'));
		else if ($this->is_25 () && $inwrap == true)
			$this->render_admin ('submenu', array ('url' => $url, 'sub' => $sub, 'class' => 'class="subsubsub"', 'trail' => ' | '));
			
		return $sub;
	}
	
	/**
	 * Displays the admin screen
	 *
	 * @return void
	 **/
	
	function admin_screen ()
	{
		if (!current_user_can ('edit_plugins') && !current_user_can ('audit_trail'))
			return;
			
		// Decide what to do
		$sub = $this->submenu ();

		AT_Audit::expire (get_option ('audit_expiry') === false ? 30 : get_option ('audit_expiry'));
		
		if ($sub == '')
			$this->screen_trail ();
		else if ($sub == 'options')
			$this->screen_options ();
	}
	
	
	/**
	 * Displays the audit trail log
	 *
	 * @return void
	 **/
	
	function screen_trail ()
	{
		$pager = new AT_Pager ($_GET, $_SERVER['REQUEST_URI'], 'happened_at', 'DESC');
		$this->render_admin ('trail', array ('trail' => AT_Audit::get_all ($pager), 'pager' => $pager));
	}
	
	
	/**
	 * Display audit trail options
	 *
	 * @return void
	 **/	
	
	function screen_options ()
	{
		if (isset ($_POST['save']))
		{
			$_POST = stripslashes_deep ($_POST);
			
			update_option ('audit_methods',    $_POST['methods']);
			update_option ('audit_expiry',     intval ($_POST['expiry']));
			update_option ('audit_post',       isset ($_POST['post']) ? true : false);
			update_option ('audit_post_order', isset ($_POST['post_order']) ? true : false);
			update_option ('audit_version',    isset ($_POST['version']) ? 'true' : 'false');
			update_option ('audit_ignore',     preg_replace ('/[^0-9,]/', '', $_POST['ignore_users']));
			
			$this->render_message (__ ('Options have been updated', 'audit-trail'));
		}
		
		$current = get_option ('audit_methods');
		if (!is_array ($current))
			$current = array ();
		
		$methods = apply_filters ('audit_collect', array ());
		if (is_array ($methods))
			ksort ($methods);

		$expiry = get_option ('audit_expiry');
		if ($expiry === false)
			$expiry = 30;

		$this->render_admin ('options', array ('methods' => $methods, 'current' => $current, 'expiry' => $expiry, 'post' => get_option ('audit_post'), 'post_order' => get_option ('audit_post_order'), 'version' => get_option ('audit_version') == 'false' ? false : true, 'ignore_users' => get_option ('audit_ignore')));
	}
}


/**
 * Instantiate the audit trail object
 *
 * @global
 **/

$obj = new Audit_Trail;
?>