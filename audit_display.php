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
 * Not sure
 *
 * @package Drain Hole
 * @author John Godley
 * @copyright Copyright (C) John Godley
 **/

function audit_details ($item, $show = 0)
{
	$pretty = array
	(
		AUDIT_WP_LOGIN              => __ ('User logged in', 'audit-trail'),
		AUDIT_WP_LOGOUT             => __ ('User logged out', 'audit-trail'),

		AUDIT_SWITCH_THEME          => __ ('Switched theme', 'audit-trail'),
                          
		AUDIT_EDIT_LINK             => __ ('Edit link %d', 'audit-trail'),
		AUDIT_ADD_LINK              => __ ('Added link %d', 'audit-trail'),
		AUDIT_DELETE_LINK           => __ ('Deleted link %d', 'audit-trail'),
                          
		AUDIT_EDIT_CATEGORY         => __ ('Edit category %d', 'audit-trail'),
		AUDIT_CREATE_CATEGORY       => __ ('Create category %d', 'audit-trail'),
		AUDIT_ADD_CATEGORY          => __ ('Add category %d', 'audit-trail'),
		AUDIT_DELETE_CATEGORY       => __ ('Delete category %d', 'audit-trail'),
                          
		AUDIT_USER_REGISTER         => __ ('User registration', 'audit-trail'),
		AUDIT_PROFILE_UPDATE        => __ ('Profile update', 'audit-trail'),
		AUDIT_DELETE_USER           => __ ('Delete user %d', 'audit-trail'),
		AUDIT_RETRIEVE_PASSWORD     => __ ('Password retrieval', 'audit-trail'),
                          
		AUDIT_EDIT_COMMENT          => __ ('Edit comment %d', 'audit-trail'),
		AUDIT_DELETE_COMMENT        => __ ('Delete comment %d', 'audit-trail'),
		AUDIT_WP_SET_COMMENT_STATUS => __ ('Set comment status of %d', 'audit-trail'),

		AUDIT_DELETE_ATTACHMENT     => __ ('Delete attachment from post %d', 'audit-trail'),
		AUDIT_ADD_ATTACHMENT        => __ ('Add attachment to post %d', 'audit-trail'),
		AUDIT_EDIT_ATTACHMENT       => __ ('Edit attachment of post %d', 'audit-trail'),
                          
		AUDIT_SAVE_POST             => __ ('Save post %d', 'audit-trail'),
		AUDIT_DELETE_POST           => __ ('Delete post %d', 'audit-trail'),
		AUDIT_PRIVATE_TO_PUBLISHED  => __ ('Publish post %d', 'audit-trail'),
		AUDIT_RESTORE_POST          => __ ('Post %d restored to previous version', 'audit-trail')
	);

	if ($item->data != '')
		echo '<a href="#" onclick="details('.$item->id.','.$show.');return false">';

	printf ($pretty[$item->operation], $item->item_id);
	if ($item->data != '')
	 	echo '</a>';
}
?>