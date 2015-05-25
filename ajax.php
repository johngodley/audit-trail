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
 **/

class AuditAjax {
	function __construct() {
		add_action( 'init', array( &$this, 'init' ) );

		if ( current_user_can( 'manage_options' ) ) {
			add_action( 'wp_ajax_at_view', array( &$this, 'at_view' ) );
			add_action( 'wp_ajax_at_close', array( &$this, 'at_close' ) );
		}
	}

	function at_view() {
		if ( check_ajax_referer( 'audittrail_view' ) ) {
			$id = intval( $_POST['id'] );

			$item = AT_Audit::get( $id );
			$this->render( 'trail_details', array( 'item' => $item ) );

			die();
		}
	}

	function at_close( $item ) {
		if ( check_ajax_referer( 'audittrail_view' ) ) {
			$id = intval( $_POST['id'] );

			$item = AT_Audit::get ($id);
			$this->render ('trail_item', array ('item' => $item));

			die();
		}
	}

	private function render( $template, $template_vars = array() ) {
		foreach ( $template_vars AS $key => $val ) {
			$$key = $val;
		}

		if ( file_exists( dirname( __FILE__ )."/view/admin/$template.php" ) )
			include dirname( __FILE__ )."/view/admin/$template.php";
	}
}
