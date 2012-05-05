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
 * Default monitoring class
 *
 * @package Audit Trail
 * @author John Godley
 **/

class AT_Auditor extends AT_Plugin
{
	/**
	 * Register appropriate hooks
	 *
	 * @return void
	 **/

	function AT_Auditor ()
	{
		$this->register_plugin ('audit-trail', dirname (__FILE__));

		$this->add_filter ('audit_collect');
		$this->add_action ('audit_listen');
		$this->add_filter ('audit_show_operation');
		$this->add_filter ('audit_show_item');
		$this->add_filter ('audit_show_details');
	}


	/**
	 * Register the types of data we can collect
	 *
	 * @return array Types to listen for
	 **/

	function audit_collect ($items)
	{
		$items['post']     = __ ('Post & page management', 'audit-trail');
		$items['attach']   = __ ('File attachments', 'audit-trail');
		$items['user']     = __ ('User profiles & logins', 'audit-trail');
		$items['theme']    = __ ('Theme switching', 'audit-trail');
		$items['link']     = __ ('Link management', 'audit-trail');
		$items['category'] = __ ('Category management', 'audit-trail');
		$items['comment']  = __ ('Comment management', 'audit-trail');
		$items['viewing']  = __ ('User page visits', 'audit-trail');
		$items['audit']    = __ ('Audit Trail actions', 'audit-trail');

		return $items;
	}


	/**
	 * Insert the hooks to listen for, given a particular area, into the list of actions
	 *
	 * @param string $method The type of area we are listening in
	 * @return void
	 **/

	function audit_listen ($method)
	{
		$ignore = get_option ('audit_ignore');
		if ($ignore)
		{
			$current = wp_get_current_user ();
			$users   = explode (',', $ignore);

			if (in_array ($current->ID, $users))
				return;
		}

		$actions = array ();
		if ($method == 'post')
			$actions = array ('delete_post', 'save_post', 'private_to_published');
		else if ($method == 'attach')
			$actions = array ('delete_attachment', 'add_attachment', 'edit_attachment');
		else if ($method == 'user')
			$actions = array ('wp_login', 'wp_logout', 'user_register', 'profile_update', 'delete_user', 'retrieve_password', 'login_errors');
		else if ($method == 'theme')
			$actions = array ('switch_theme');
		else if ($method == 'link')
			$actions = array ('edit_link', 'add_link', 'delete_link');
		else if ($method == 'category')
			$actions = array ('edit_category', 'add_category', 'delete_category');
		else if ($method == 'comment')
			$actions = array ('edit_comment', 'delete_comment');
		else if ($method == 'viewing')
			$actions = array ('template_redirect');

		foreach ($actions AS $name)
			$this->add_action ($name);
	}


	/**
	 * Given a log item will display the details
	 *
	 * @param AT_Audit $item
	 * @return AT_Audit
	 **/
	function audit_show_details ($item) {
		global $wpdb;

		switch ($item->operation)
		{
			case 'user_register' :
			case 'profile_update' :
				$user = unserialize ($item->data);

				$item->message = '<br/>'.$this->capture_admin ('details/profile_update', array ('item' => $item, 'user' => $user));
				break;

			case 'add_link' :
			case 'edit_link' :
				$link = unserialize ($item->data);

				$item->message = '<br/>'.$this->capture_admin ('details/edit_link', array ('item' => $item, 'link' => $link));
				break;

			case 'add_category' :
			case 'edit_category' :
				$cat = unserialize ($item->data);

				$item->message = '<br/>'.$this->capture_admin ('details/edit_category', array ('item' => $item, 'cat' => $cat));
				break;

			case 'edit_comment' :
				$original = get_comment ($item->item_id);
				$comment  = unserialize ($item->data);

				$item->message = '<br/>'.$this->capture_admin ('details/'.$item->operation, array ('item' => $item, 'comment' => $comment));
				break;

			case 'save_post' :
				$original = get_post ($item->item_id);
				$post     = unserialize ($item->data);

				$item->message = '<br/>'.$this->capture_admin ('details/'.$item->operation, array ('item' => $item, 'post' => $post));
				break;
		}

		return $item;
	}


	/**
	 * Given a log item will pretty-print the item
	 *
	 * @param AT_Audit $item
	 * @return AT_Audit
	 **/

	function audit_show_item ($item)
	{
		switch ($item->operation)
		{
			case 'delete_link' :
			case 'switch_theme' :
				$item->message = $item->data;
				break;

			case 'profile_update' :
			case 'wp_logout':
			case 'login_failed' :
			case 'wp_login' :
				$user = get_userdata ($item->item_id);
				if ($user === false)
					$item->message = $item->item_id;
				else
					$item->message = '<a href="user-edit.php?user_id='.$item->item_id.'">'.$user->user_nicename."</a>";
				break;

			case 'user_register' :
				$user = unserialize ($item->data);
				$item->message = '<a href="user-edit.php?user_id='.$user->ID.'">'.$user->user_nicename."</a>";
				break;

			case 'delete_user' :
			case 'retrieve_password' :
				$user = get_userdatabylogin ($item->data);
				if ($user === false)
					$item->message = $item->data;
				else
					$item->message = '<a href="user-edit.php?user_id='.$item->item_id.'">'.$user->user_nicename."</a>";
				break;

			case 'add_link' :
			case 'edit_link' :
				$link = unserialize ($item->data);
			 	$item->message = '<a href="link.php?link_id='.$link->link_id.'&action=edit">'.$link->link_name.'</a>';
				break;

			case 'edit_category' :
			case 'add_category' :
				$cat = unserialize ($item->data);
		 		$item->message = '<a href="categories.php?action=edit&amp;cat_ID='.$cat->cat_ID.'">'.$cat->cat_name.'</a>';
				break;

			case 'edit_comment' :
				$item->message = '<a href="comment.php?action=editcomment&amp;c='.$item->item_id.'">'.$item->item_id.'</a>';
				break;

			case 'save_post' :
				$post = unserialize ($item->data);
				if ( $post )
					$item->message = '<a href="post.php?action=edit&amp;post='.$post->ID.'">'.$post->post_title.'</a>';
				break;

			case 'private_to_published':
				$post = get_post ($item->item_id);
				if ( $post )
					$item->message = '<a href="post.php?action=edit&amp;post='.$post->ID.'">'.$post->post_title.'</a>';
				break;

			case 'add_attachment' :
			case 'edit_attachment' :
				$post = get_post ($item->item_id);
				$text = '<a href="media.php?action=edit&amp;attachment_id='.$item->item_id.'">'.basename ($item->data).'</a>';
				if (!empty ($post) && $post->post_parent > 0)
					$text .= ' (post <a href="post.php?action=edit&amp;post='.$post->post_parent.'">'.$post->post_parent.'</a>)';
				$item->message = $text;
				break;

			case 'template_redirect':
				if ($item->item_id > 0)
					$item->message = '<a href="post.php?action=edit&amp;post='.$item->item_id.'">'.$item->data.'</a>';
				else
					$item->message = $item->data;
				break;
		}

		return $item;
	}


	/**
	 * Given a log item will pretty-print the operation
	 *
	 * @param AT_Audit $item
	 * @return AT_Audit
	 **/

	function audit_show_operation ($item)
	{
		switch ($item->operation)
		{
			case 'switch_theme' :
				$item->message = __ ('Theme switch', 'audit-trail');
				break;

			case 'profile_update' :
				$user = get_userdata ($item->item_id);
				if ($user === false)
					$text = __ ('Profile updated for deleted user', 'audit-trail');
				else
					$text = __ ('Profile updated', 'audit-trail');

				$item->message = '<a href="#'.$item->id.'" class="audit-view">'.$text.'</a>';
				break;

			case 'wp_login' :
				$item->message = __ ('Logged In', 'audit-trail');
				break;

			case 'wp_logout' :
				$item->message = __ ('Logged Out', 'audit-trail');
				break;

			case 'login_failed' :
				$item->message = __ ('Login failed', 'audit-trail');
				break;

			case 'user_register' :
				$item->message = '<a href="#'.$item->id.'" class="audit-view">'.__ ('New user registration', 'audit-trail').'</a>';
				break;

			case 'retrieve_password' :
				$item->message = __ ('Retrieve password', 'audit-trail');
				break;

			case 'delete_user' :
				$item->message = __ ('Delete user', 'audit-trail');
				break;

			case 'add_link' :
				$item->message = '<a href="#'.$item->id.'" class="audit-view">'.__ ('Add link', 'audit-trail').'</a>';
				break;

			case 'edit_link':
				$link = unserialize ($item->data);
				$item->message = '<a href="#'.$item->id.'" class="audit-view">'.__ ('Edit link', 'audit-trail').'</a>';
				break;

			case 'delete_link':
				$item->message = __ ('Delete link', 'audit-trail');
				break;

			case 'edit_category' :
				$item->message = '<a href="#'.$item->id.'" class="audit-view">'.__ ('Edit category ', 'audit-trail').'</a>';
				break;

			case 'add_category':
				$item->message = '<a href="#'.$item->id.'" class="audit-view">'.__ ('Add category', 'audit-trail').'</a>';
				break;

			case 'delete_category' :
				$item->message = __ ('Delete category', 'audit-trail');
				break;

			case 'edit_comment' :
				$item->message = '<a href="#'.$item->id.'" class="audit-view">'.__ ('Edit comment', 'audit-trail').'</a>';
				break;

			case 'delete_comment':
				$item->message = __ ('Delete comment', 'audit-trail');
				break;

			case 'delete_post' :
				$item->message = __ ('Delete post', 'audit-trail');
				break;

			case 'save_post' :
				$post = unserialize ($item->data);
				if ($post && $post->post_type == 'post')
					$text = __ ('Save post', 'audit-trail');
				else
					$text = __ ('Save page', 'audit-trail');
				$item->message = '<a href="#'.$item->id.'" class="audit-view">'.$text.'</a>';;
				break;

			case 'private_to_published' :
				$item->message = 'Published';
				break;

			case 'add_attachment' :
				$item->message = '<a href="media.php?action=edit&amp;attachment_id='.$item->item_id.'">'.__ ('Add attachment', 'audit-trail').'</a>';
				break;

			case 'delete_attachment' :
				$item->message = 'Delete attachment';
				break;

			case 'edit_attachment' :
				$item->message = '<a href="media.php?action=edit&amp;attachment_id='.$item->item_id.'">'.__ ('Edit attachment', 'audit-trail').'</a>';
				break;

			case 'template_redirect' :
				$item->message = __( 'View page', 'audit-trail' );
				break;
		}

		return $item;
	}


	/**
	 * Default listening methods
	 **/

	// Actions to track
	function delete_post ($id)
	{
		AT_Audit::create ('delete_post', $id);
	}

	function private_to_published ($id)
	{
		AT_Audit::create ('private_to_published', $id);
	}

	function save_post ($id)
	{
		if (!defined ('DOING_AJAX'))
			AT_Audit::create ('save_post', $id, get_post( $id ) );
	}

	function wp_login ($user)
	{
		$data = get_user_by( 'login', $user );
		AT_Audit::create ('wp_login', $data->ID, '', '', $data->ID);
	}

	function wp_logout ()
	{
		global $user_ID;
		AT_Audit::create ('wp_logout', $user_ID);
	}

	function login_errors ($errors)
	{
		if (strpos ($errors, __('<strong>ERROR</strong>: Incorrect password.')) !== false)
		{
			$login = get_user_by( 'login', sanitize_user ($_POST['log']));
			AT_Audit::create ('login_failed', $login->ID, sanitize_user ($_POST['log']));
		}

		return $errors;
	}

	function switch_theme ($newtheme)
	{
		AT_Audit::create ('switch_theme', '', $newtheme);
	}

	function edit_link ($id)
	{
		global $wpdb;
		$link = $wpdb->get_row ("SELECT * FROM {$wpdb->links} WHERE link_id=$id");
		AT_Audit::create ('edit_link', $id, serialize ($link));
	}

	function delete_link ($id)
	{
		global $wpdb;
		$link = $wpdb->get_row ("SELECT * FROM {$wpdb->links} WHERE link_id=$id");
		AT_Audit::create ('delete_link', $id, $link->link_name);
	}

	function add_link ($id)
	{
		global $wpdb;
		$link = $wpdb->get_row ("SELECT * FROM {$wpdb->links} WHERE link_id=$id");
		AT_Audit::create ('add_link', $id, serialize ($link));
	}

	function edit_category ($id)
	{
		// We filter here otherwise we get a lot of annoying messages whenever the admin does anything useful
		if (strpos ($_SERVER['REQUEST_URI'], 'categories.php') !== false)
			AT_Audit::create ('edit_category', $id, serialize (get_category ($id)));
	}

	function create_category ($id)
	{
		AT_Audit::create ('create_category', $id, serialize (get_category ($id)));
	}

	function add_category ($id)
	{
		AT_Audit::create ('add_category', $id, serialize (get_category ($id)));
	}

	function delete_category ($id)
	{
		$cat = get_category ($id);
		AT_Audit::create ('delete_category', $id, $cat->cat_nicename);
	}

	function user_register ($id)
	{
		AT_Audit::create ('user_register', $id, serialize (get_userdata ($id)));
	}

	function profile_update ($id)
	{
		AT_Audit::create ('profile_update', $id, serialize (get_userdata ($id)));
	}

	function delete_user ($id)
	{
		$user = get_userdata ($id);
		AT_Audit::create ('delete_user', $id, $user->user_nicename);
	}

	function retrieve_password ($name)
	{
		AT_Audit::create ('retrieve_password', '', $name);
	}

	function delete_comment ($id)
	{
		AT_Audit::create ('delete_comment', $id);
	}

	function edit_comment ($id)
	{
		global $wpdb;
		$comment = $wpdb->get_row ("SELECT * FROM {$wpdb->comments} WHERE comment_ID=$id");
		AT_Audit::create ('edit_comment', $id, serialize ($comment));
	}

	function delete_attachment ($postid)
	{
		AT_Audit::create ('delete_attachment', $postid);
	}

	function add_attachment ($postid)
	{
		global $wpdb;

		$attach = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_key='_wp_attached_file' ORDER BY meta_id DESC LIMIT 1", $postid ) );
		AT_Audit::create ('add_attachment', $postid, $attach->meta_value);
	}

	function edit_attachment ($postid)
	{
		global $wpdb;

		$attach = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_key='_wp_attached_file' ORDER BY meta_id DESC LIMIT 1", $postid ) );
		AT_Audit::create ('edit_attachment', $postid, $attach->meta_value);
	}

	function template_redirect ()
	{
		// Don't log 404's
		if (!is_404 ())
		{
			global $post, $posts;
			if (isset ($_GET['preview']) && $_GET['preview'] == 'true')
				return;
			AT_Audit::create ('template_redirect', count ($posts) > 1 ? 0 : $post->ID, $_SERVER['REQUEST_URI']);
		}
	}
}

?>
