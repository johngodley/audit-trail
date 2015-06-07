<?php
/*
Plugin Name: Audit Trail
Plugin URI: http://urbangiraffe.com/plugins/audit-trail/
Description: Keep a log of exactly what is happening behind the scenes of your WordPress blog
Author: John Godley
Version: 1.2.4
Author URI: http://urbangiraffe.com
============================================================================================================
This software is provided "as is" and any express or implied warranties, including, but not limited to, the
implied warranties of merchantibility and fitness for a particular purpose are disclaimed. In no event shall
the copyright owner or contributors be liable for any direct, indirect, incidental, special, exemplary, or
consequential damages(including, but not limited to, procurement of substitute goods or services; loss of
use, data, or profits; or business interruption) however caused and on any theory of liability, whether in
contract, strict liability, or tort(including negligence or otherwise) arising in any way out of the use of
this software, even if advised of the possibility of such damage.
============================================================================================================

Available filters are:
  - audit_collect        - Passed an array of methods to monitor, return the array with any additions
	- audit_show_operation - Passed an AT_Audit object, return the object with 'message' changed for type of operation
  - audit_show_item      - Passed an AT_Audit object, return the object with 'message' changed for type of item
  - audit_show_details   - Passed an AT_Audit object, return a message to display when the operation is clicked for more details

Available actions are:
  - audit_listen - Passed the name of a method to monitor.  Add appropriate filters/actions to monitor the method
*/

define( 'AUDIT_TRAIL_VERSION', '0.3' );

/**
 * Audit Trail plugin
 *
 * @package Audit Trail
 **/

class Audit_Trail {
	private static $instance = null;
	private $auditor;

	static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Audit_Trail();

			load_plugin_textdomain( 'audit-trail', false, dirname( plugin_basename( __FILE__ ) ).'/locale/' );
		}

		return self::$instance;
	}

	/**
	 * Constructor hooks all the appropriate filters and actions for the plugin, as well as creating the auditor
	 * object which monitors everything else
	 *
	 * @return void
	 **/

	function __construct() {
		// Check database is setup
		include( dirname( __FILE__).'/models/auditor.php' );
		include( dirname( __FILE__).'/models/audit.php' );

		if ( is_admin() ) {
			if ( !class_exists( 'WP_List_Table' ) )
			    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';

			include( dirname( __FILE__).'/models/pager.php' );

			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'activate_audit-trail/audit-trail.php', array( $this, 'activate' ) );
			add_action( 'load-tools_page_audit-trail', array( $this, 'admin_head' ) );

			// Ajax functions
			if (  defined( 'DOING_AJAX' ) ) {
				include_once dirname( __FILE__ ).'/ajax.php';
				$this->ajax = new AuditAjax();
			}

			// XXX easier way?
			add_action( 'plugin_action_links_'.basename( dirname( __FILE__ ) ).'/'.basename( __FILE__ ), array( &$this, 'plugin_settings' ), 10, 4 );
		}

		// Add ourself to the Audit Trail functions
		$this->auditor = new AT_Auditor;
		$this->plugins_loaded();
	}

	function plugin_settings( $links) {
		$settings_link = '<a href="tools.php?page='.basename( __FILE__ ).'">'.__('Trail', 'audit-trail' ).'</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * After all the plugins have loaded this starts listening for all registered filters/actions
	 *
	 * @return void
	 **/

	function plugins_loaded() {
		$methods = get_option( 'audit_methods' );

		if ( !empty( $methods) && is_array( $methods ) ) {
			foreach( $methods AS $name)
				do_action( 'audit_listen', $name);
		}
	}

	function base_url() {
		return __FILE__;
	}


	/**
	 * Creates the database and upgrades any existing data
	 *
	 * @return void
	 **/

	function activate() {
		global $wpdb;

		if ( get_option( 'audit_trail' ) == '0.1' || get_option( 'audit_trail' ) == 'true' )
			$wpdb->query( "DROP TABLE {$wpdb->prefix}audit_trail");

		if ( get_option( 'audit_trail' ) != '0.2' ) {
			$wpdb->query( "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}audit_trail`(
			  `id` int(11) NOT NULL auto_increment,
			  `operation` varchar(40) NOT NULL default '',
			  `user_id` int(11) NOT NULL,
  			`ip` int(11) unsigned NOT NULL default '0',
			  `happened_at` datetime NOT NULL,
			  `item_id` int(11) default NULL,
			  `data` longtext,
			  `title` varchar(100) default NULL,
			  PRIMARY KEY ( `id`)
			)");
		}

		if ( get_option( 'audit_trail' ) != '0.3' )
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}audit_trail` CHANGE `ip` `ip` int(11) UNSIGNED NOT NULL DEFAULT '0'");

		update_option( 'audit_trail', AUDIT_TRAIL_VERSION);
	}


	/**
	 * Inject Audit Trail into the menu
	 *
	 * @return void
	 **/

	function admin_menu() {
		if ( current_user_can( 'edit_plugins' ) || current_user_can( 'audit_trail' ) )
  			add_management_page( __("Audit Trail",'audit-trail' ), __("Audit Trail",'audit-trail' ), "publish_posts", basename( __FILE__), array( $this, "admin_screen") );
	}


	/**
	 * Inserts the edit box into the edit post/page area
	 *
	 * @return void
	 **/

	function edit_box() {
		global $post;
		$this->render( 'edit_box', array( 'trail' => AT_Audit::get_by_post( $post->ID) ));
	}

	function edit_box_advanced() {
		global $post;
		$this->render( 'edit_box_25', array( 'trail' => AT_Audit::get_by_post( $post->ID) ));
	}

	function submenu( $inwrap = false) {
		// Decide what to do
		$sub = isset( $_GET['sub']) ? $_GET['sub'] : '';
		if ( !in_array( $sub, array( 'options', 'support' ) ) )
			$sub = '';

		if ( $inwrap == true)
			$this->render( 'submenu', array( 'sub' => $sub, 'class' => 'class="subsubsub"', 'trail' => ' | ' ) );

		return $sub;
	}

	/**
	 * Displays the admin screen
	 *
	 * @return void
	 **/

	function admin_screen() {
		if ( !current_user_can( 'edit_plugins' ) && !current_user_can( 'audit_trail' ) )
			return;

		// Decide what to do
		$sub = $this->submenu();

		AT_Audit::expire( get_option( 'audit_expiry' ) === false ? 30 : get_option( 'audit_expiry' ) );

		if ( $sub == '' )
			$this->screen_trail();
		else if ( $sub == 'options' )
			$this->screen_options();
		else if ( $sub == 'support' )
			$this->render( 'support' );
	}


	/**
	 * Displays the audit trail log
	 *
	 * @return void
	 **/

	function screen_trail() {
		$table = new Audit_Trail_Table();
		$table->prepare_items();

		$this->render( 'trail', array( 'table' => $table ) );
	}


	/**
	 * Display audit trail options
	 *
	 * @return void
	 **/

	function screen_options() {
		if ( isset( $_POST['save']) && check_admin_referer( 'audittrail-update_options' ) ) {
			update_option( 'audit_methods',    stripslashes_deep( $_POST['methods'] ) );
			update_option( 'audit_expiry',     intval( $_POST['expiry']) );
			update_option( 'audit_post',       isset( $_POST['post']) ? true : false);
			update_option( 'audit_post_order', isset( $_POST['post_order']) ? true : false);
			update_option( 'audit_version',    isset( $_POST['version']) ? 'true' : 'false' );
			update_option( 'audit_ignore',     preg_replace( '/[^0-9,]/', '', $_POST['ignore_users']) );
			update_option( 'audit_support',    isset( $_POST['support'] ) ? true : false );
			update_option( 'audit_error_log',  isset( $_POST['error_log'] ) ? true : false );

			$this->render_message( __( 'Options have been updated', 'audit-trail' ) );
		}

		$current = get_option( 'audit_methods' );
		if ( !is_array( $current) )
			$current = array();

		$methods = apply_filters( 'audit_collect', array() );
		if ( is_array( $methods) )
			ksort( $methods);

		$error_log = get_option( 'audit_error_log' );
		$support   = get_option( 'audit_support' );

		$expiry = get_option( 'audit_expiry' );
		if ( $expiry === false)
			$expiry = 30;

		$this->render( 'options', array( 'methods' => $methods, 'current' => $current, 'support' => $support, 'expiry' => $expiry, 'error_log' => $error_log, 'post' => get_option( 'audit_post' ), 'post_order' => get_option( 'audit_post_order' ), 'version' => get_option( 'audit_version' ) == 'false' ? false : true, 'ignore_users' => get_option( 'audit_ignore' ) ));
	}

	function admin_head() {
		wp_enqueue_style( 'audit-trail', plugin_dir_url( __FILE__ ).'admin.css' );
	}

	function version() {
		$plugin_data = implode( '', file( __FILE__) );

		if ( preg_match( '|Version:(.*)|i', $plugin_data, $version) )
			return trim( $version[1]);
		return '';
	}

	private function render( $template, $template_vars = array() ) {
		foreach ( $template_vars AS $key => $val ) {
			$$key = $val;
		}

		if ( file_exists( dirname( __FILE__ )."/view/admin/$template.php" ) )
			include dirname( __FILE__ )."/view/admin/$template.php";
	}

	private function capture( $ug_name, $ug_vars = array() ) {
		ob_start();

		$this->render( $ug_name, $ug_vars );
		$output = ob_get_contents();

		ob_end_clean();
		return $output;
	}

	private function render_message( $message, $timeout = 0 ) {
		?>
<div class="updated" id="message" onclick="this.parentNode.removeChild(this)">
	<p><?php echo esc_html( $message ) ?></p>
</div>
	<?php
	}
}

add_action( 'init', array( 'Audit_Trail', 'init' ) );
