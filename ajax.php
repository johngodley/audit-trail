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

class AuditAjax extends AT_Plugin {
	function AuditAjax() {
		$this->register_plugin( 'audit-trail', __FILE__ );

		add_action( 'init', array( &$this, 'init' ) );
	}

	function init() {
		if ( current_user_can( 'manage_options' ) ) {
			$this->register_ajax( 'at_view' );
			$this->register_ajax( 'at_close' );
		}
	}

	function at_view() {
		if ( check_ajax_referer( 'audittrail_view' ) ) {
			$id = intval( $_POST['id'] );

			$item = AT_Audit::get( $id );
			$this->render_admin( 'trail_details', array( 'item' => $item ) );

			die();
		}
	}

	function at_close( $item ) {
		if ( check_ajax_referer( 'audittrail_view' ) ) {
			$id = intval( $_POST['id'] );

			$item = AT_Audit::get ($id);
			$this->render_admin ('trail_item', array ('item' => $item));

			die();
		}
	}
}

