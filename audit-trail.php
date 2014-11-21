<?php
/*
Plugin Name: Audit Trail
Plugin URI: http://urbangiraffe.com/plugins/audit-trail/
Description: Keep a log of exactly what is happening behind the scenes of your WordPress blog
Author: John Godley
Version: 1.1.6
Author URI: http://urbangiraffe.com
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
			
			$this->add_action( 'admin_menu' );
			$this->add_action( 'activate_audit-trail/audit-trail.php', 'activate' );

			$this->add_action( 'admin_head', 'wp_print_styles' );
			$this->add_action( 'admin_print_styles', 'wp_print_styles' );
			$this->add_filter( 'contextual_help', 'contextual_help', 10, 2 );
			$this->add_action( 'admin_footer' );
			
			// Ajax functions
			if ( defined( 'DOING_AJAX' ) ) {
				include_once dirname( __FILE__ ).'/ajax.php';
				$this->ajax = new AuditAjax();
			}
			
			$this->register_plugin_settings( __FILE__ );
		}
		
		// Add ourself to the Audit Trail functions
		$this->auditor = new AT_Auditor;

		$this->add_action ('plugins_loaded');
	}

	function contextual_help($help, $screen) {
		if ($screen == 'settings_page_audittrail' ) {
			$help .= '<h5>' . __('Audit Trail Help', 'audit-trail') . '</h5><div class="metabox-prefs">';
			$help .= '<a href="http://urbangiraffe.com/plugins/audit-trail/">'.__ ('Audit Trail Documentation', 'audit-trail').'</a><br/>';
			$help .= '<a href="http://urbangiraffe.com/support/forum/audit-trail">'.__ ('Audit Trail Support Forum', 'audit-trail').'</a><br/>';
			$help .= '<a href="http://urbangiraffe.com/tracker/projects/audit-trail/issues?set_filter=1&amp;tracker_id=1">'.__ ('Audit Trail Bug Tracker', 'audit-trail').'</a><br/>';
			$help .= __ ('Please read the documentation and check the bug tracker before asking a question.', 'audit-trail');
			$help .= '</div>';
		}
		
		return $help;
	}
	
	function plugin_settings ($links)	{
		$settings_link = '<a href="tools.php?page='.basename( __FILE__ ).'">'.__('Trail', 'audit-trail').'</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
	
	/**
	 * After all the plugins have loaded this starts listening for all registered filters/actions
	 *
	 * @return void
	 **/
	
	function plugins_loaded ()
	{
		$methods = get_option ('audit_methods');
		if (!empty ($methods) && is_array( $methods ) )
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
	 * Inject Audit Trail into the menu
	 *
	 * @return void
	 **/
	
	function admin_menu ()
	{
		if (current_user_can ('edit_plugins') || current_user_can ('audit_trail'))
  		add_management_page (__("Audit Trail",'audit-trail'), __("Audit Trail",'audit-trail'), "publish_posts", basename (__FILE__), array ($this, "admin_screen"));
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
		else if ($sub == 'support')
			$this->render_admin( 'support' );
	}
	
	
	/**
	 * Displays the audit trail log
	 *
	 * @return void
	 **/
	
	function screen_trail ()
	{
		if ( isset( $_POST['item'] ) && isset( $_POST['action2'] ) && $_POST['action2'] == 'delete' ) {
			foreach ( $_POST['item'] AS $id ) {
				AT_Audit::delete( intval( $id ) );
			}
		}
		
		$pager = new AT_Pager ($_REQUEST, $_SERVER['REQUEST_URI'], 'happened_at', 'DESC');
		$this->render_admin ('trail', array ('trail' => AT_Audit::get_all ($pager), 'pager' => $pager));
	}
	
	
	/**
	 * Display audit trail options
	 *
	 * @return void
	 **/	
	
	function screen_options () {
		if (isset ($_POST['save']) && check_admin_referer ('audittrail-update_options'))
		{
			update_option ('audit_methods',    stripslashes_deep( $_POST['methods'] ) );
			update_option ('audit_expiry',     intval ($_POST['expiry']));
			update_option ('audit_post',       isset ($_POST['post']) ? true : false);
			update_option ('audit_post_order', isset ($_POST['post_order']) ? true : false);
			update_option ('audit_version',    isset ($_POST['version']) ? 'true' : 'false');
			update_option ('audit_ignore',     preg_replace ('/[^0-9,]/', '', $_POST['ignore_users']));
			update_option( 'audit_support',    isset( $_POST['support'] ) ? true : false );
			
			$this->render_message (__ ('Options have been updated', 'audit-trail'));
		}
		
		$current = get_option ('audit_methods');
		if (!is_array ($current))
			$current = array ();
		
		$methods = apply_filters ('audit_collect', array ());
		if (is_array ($methods))
			ksort ($methods);

		$support = get_option ('audit_support');

		$expiry = get_option ('audit_expiry');
		if ($expiry === false)
			$expiry = 30;

		$this->render_admin ('options', array ('methods' => $methods, 'current' => $current, 'support' => $support, 'expiry' => $expiry, 'post' => get_option ('audit_post'), 'post_order' => get_option ('audit_post_order'), 'version' => get_option ('audit_version') == 'false' ? false : true, 'ignore_users' => get_option ('audit_ignore')));
	}
	
	function wp_print_styles() {
		if ( ( isset ($_GET['page']) && $_GET['page'] == 'audit-trail.php') ) {
			echo '<link rel="stylesheet" href="'.$this->url ().'/admin.css" type="text/css" media="screen" title="no title" charset="utf-8"/>';

			if (!function_exists ('wp_enqueue_style'))
				echo '<style type="text/css" media="screen">
				.subsubsub {
					list-style: none;
					margin: 8px 0 5px;
					padding: 0;
					white-space: nowrap;
					font-size: 11px;
					float: left;
				}
				.subsubsub li {
					display: inline;
					margin: 0;
					padding: 0;
				}
				</style>';
		}
	}
	
	
	function locales() {
		$locales = array();
		$readme  = @file_get_contents( dirname( __FILE__ ).'/readme.txt' );
		if ( $readme ) {
			if ( preg_match_all( '/^\* (.*?) by \[(.*?)\]\((.*?)\)/m', $readme, $matches ) ) {
				foreach ( $matches[1] AS $pos => $match ) {
					$locales[$match] = '<a href="'.$matches[3][$pos].'">'.$matches[2][$pos].'</a>';
				}
			}
		}
		
		ksort( $locales );
		return $locales;
	}
	
	function admin_footer() {
			if ( isset($_GET['page']) && $_GET['page'] == basename( __FILE__ ) ) {
				$support = get_option ('audit_support');

				if ( $support == false ) {
	?>
	<script type="text/javascript" charset="utf-8">
		jQuery(function() {
			jQuery('#support-annoy').animate( { opacity: 0.2, backgroundColor: 'red' } ).animate( { opacity: 1, backgroundColor: 'yellow' });
		});
	</script>
	<?php
				}
			}
		}
		
	function version() {
		$plugin_data = implode ('', file (__FILE__));
		
		if (preg_match ('|Version:(.*)|i', $plugin_data, $version))
			return trim ($version[1]);
		return '';
	}
}


/**
 * Instantiate the audit trail object
 *
 * @global
 **/

$obj = new Audit_Trail;
