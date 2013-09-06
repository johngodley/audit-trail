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

class AT_Auditor extends AT_Plugin {
	/**
	 * Register appropriate hooks
	 *
	 * @return void
	 **/

	function __construct() {
		$this->register_plugin( 'audit-trail', dirname( __FILE__ ) );

		$this->add_filter( 'audit_collect' );
		$this->add_action( 'audit_listen' );
		$this->add_filter( 'audit_show_operation' );
		$this->add_filter( 'audit_show_item' );
		$this->add_filter( 'audit_show_details' );
	}


	/**
	 * Register the types of data we can collect
	 *
	 * @return array Types to listen for
	 **/

	function audit_collect( $items ) {
		$items['post']     = __( 'Post & page management', 'audit-trail' );
		$items['attach']   = __( 'File attachments', 'audit-trail' );
		$items['user']     = __( 'User profiles & logins', 'audit-trail' );
		$items['theme']    = __( 'Theme switching', 'audit-trail' );
		$items['link']     = __( 'Link management', 'audit-trail' );
		$items['category'] = __( 'Category management', 'audit-trail' );
		$items['comment']  = __( 'Comment management', 'audit-trail' );
		$items['viewing']  = __( 'User page visits', 'audit-trail' );
		$items['audit']    = __( 'Audit Trail actions', 'audit-trail' );

		return $items;
	}


	/**
	 * Insert the hooks to listen for, given a particular area, into the list of actions
	 *
	 * @param string $method The type of area we are listening in
	 * @return void
	 **/

	function audit_listen( $method ) {
		$ignore = get_option( 'audit_ignore' );
		if ( $ignore ) {
			$current = wp_get_current_user();
			$users   = explode( ',', $ignore );

			if ( in_array( $current->ID, $users ) )
				return;
		}

		$map = array(
			'post' => array(
				'delete_post',
				'save_post',
				'private_to_published',
			),
			'attach' => array(
				'delete_attachment',
				'add_attachment',
				'edit_attachment'
			),
			'user' => array(
				'wp_login',
				'wp_logout',
				'user_register',
				'profile_update',
				'delete_user',
				'retrieve_password',
				'login_errors',
				'wp_login_failed'
			),
			'theme' => array(
				'switch_theme',
			),
			'link' => array(
				'edit_link',
				'add_link',
				'delete_link'
			),
			'category' => array(
				'edit_category',
				'add_category',
				'delete_category'
			),
			'comment' => array(
				'edit_comment',
				'delete_comment'
			),
			'viewing' => array(
				'template_redirect'
			)
		);

		if ( isset( $map[$method] ) ) {
			foreach ( $map[$method] AS $name ) {
				$this->add_action( $name );
			}
		}
	}


	/**
	 * Given a log item will display the details
	 *
	 * @param AT_Audit $item
	 * @return AT_Audit
	 **/
	function audit_show_details( $item ) {
		switch ( $item->operation ) {
			case 'user_register' :
			case 'profile_update' :
				$user = unserialize( $item->data );

				$item->message = '<br/>'.$this->capture_admin( 'details/profile_update', array( 'item' => $item, 'user' => $user ) );
				break;

			case 'add_link' :
			case 'edit_link' :
				$link = unserialize( $item->data );

				$item->message = '<br/>'.$this->capture_admin( 'details/edit_link', array( 'item' => $item, 'link' => $link ) );
				break;

			case 'add_category' :
			case 'edit_category' :
				$cat = unserialize( $item->data );

				$item->message = '<br/>'.$this->capture_admin( 'details/edit_category', array( 'item' => $item, 'cat' => $cat ) );
				break;

			case 'edit_comment' :
				$original = get_comment( $item->item_id );
				$comment  = unserialize( $item->data );

				$item->message = '<br/>'.$this->capture_admin( 'details/'.$item->operation, array( 'item' => $item, 'comment' => $comment ) );
				break;

			case 'save_post' :
				$original = get_post ($item->item_id);
				$post     = unserialize ($item->data);

				$item->message = '<br/>'.$this->capture_admin( 'details/'.$item->operation, array( 'item' => $item, 'post' => $post ) );
				break;

			default:
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

	function audit_show_item( $item ) {
		switch ( $item->operation )	{
			case 'wp_login_failed' :
			case 'delete_link' :
			case 'switch_theme' :
				$item->message = esc_html( $item->data );
				break;

			case 'profile_update' :
			case 'wp_logout':
			case 'login_failed' :
			case 'wp_login' :
				$user = get_userdata( $item->item_id );
				if ( $user === false )
					$item->message = intval( $item->item_id );
				else
					$item->message = '<a href="user-edit.php?user_id='.esc_attr( $item->item_id ).'">'.esc_html( $user->user_nicename )."</a>";
				break;

			case 'user_register' :
				$user = unserialize( $item->data );
				$item->message = '<a href="user-edit.php?user_id='.esc_attr( $user->ID ).'">'.esc_html( $user->user_nicename )."</a>";
				break;

			case 'delete_user' :
			case 'retrieve_password' :
				$user = get_userdatabylogin( $item->data );
				if ( $user === false )
					$item->message = esc_html( $item->data );
				else
					$item->message = '<a href="user-edit.php?user_id='.esc_attr( $item->item_id ).'">'.esc_html( $user->user_nicename )."</a>";
				break;

			case 'add_link' :
			case 'edit_link' :
				$link = unserialize( $item->data );
			 	$item->message = '<a href="link.php?link_id='.esc_attr( $link->link_id ).'&action=edit">'.esc_html( $link->link_name ).'</a>';
				break;

			case 'edit_category' :
			case 'add_category' :
				$cat = unserialize( $item->data );
		 		$item->message = '<a href="categories.php?action=edit&amp;cat_ID='.esc_attr( $cat->cat_ID ).'">'.esc_html( $cat->cat_name ).'</a>';
				break;

			case 'edit_comment' :
				$item->message = '<a href="comment.php?action=editcomment&amp;c='.esc_attr( $item->item_id ).'">'.esc_html( $item->item_id ).'</a>';
				break;

			case 'save_post' :
				$post = unserialize( $item->data );
				if ( $post )
					$item->message = '<a href="post.php?action=edit&amp;post='.esc_attr( $post->ID ).'">'.esc_html( $post->post_title ).'</a>';
				break;

			case 'private_to_published':
				$post = get_post( $item->item_id );
				if ( $post )
					$item->message = '<a href="post.php?action=edit&amp;post='.esc_attr( $post->ID ).'">'.esc_html( $post->post_title ).'</a>';
				break;

			case 'add_attachment' :
			case 'edit_attachment' :
				$post = get_post( $item->item_id );
				$text = '<a href="media.php?action=edit&amp;attachment_id='.esc_attr( $item->item_id ).'">'.esc_html( basename( $item->data ) ).'</a>';
				if ( !empty( $post ) && $post->post_parent > 0)
					$text .= ' (post <a href="post.php?action=edit&amp;post='.esc_attr( $post->post_parent ).'">'.esc_html( $post->post_parent ).'</a>)';
				$item->message = $text;
				break;

			case 'template_redirect':
				if ( $item->item_id > 0 )
					$item->message = '<a href="post.php?action=edit&amp;post='.esc_attr( $item->item_id ).'">'.esc_html( $item->data ).'</a>';
				else
					$item->message = esc_html( $item->data );
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

	function audit_show_operation( $item ) {
		$map = array(
			'switch_theme'         => __( 'Theme switch', 'audit-trail' ),
			'wp_login'             => __( 'Logged In', 'audit-trail' ),
			'wp_logout'            => __( 'Logged Out', 'audit-trail' ),
			'wp_login_failed'      => __( 'Failed login', 'audit-trail' ),
			'login_failed'         => __( 'Login failed', 'audit-trail' ),
			'retrieve_password'    => __( 'Retrieve password', 'audit-trail' ),
			'delete_user'          => __( 'Delete user', 'audit-trail' ),
			'delete_link'          => __( 'Delete link', 'audit-trail' ),
			'delete_comment'       => __( 'Delete comment', 'audit-trail' ),
			'delete_post'          => __( 'Delete post', 'audit-trail' ),
			'private_to_published' => __( 'Published', 'audit-trail' ),
			'delete_category'      => __( 'Delete category', 'audit-trail' ),
			'delete_attachment'    => __( 'Delete attachment', 'audit-trail' ),
			'template_redirect'    => __( 'View page', 'audit-trail' ),
		);

		$item->message = false;

		if ( isset( $map[$item->operation] ) )
			$item->message = $map[$item->operation];

		switch ( $item->operation ) {
			case 'profile_update' :
				$user = get_userdata( $item->item_id );
				if ($user === false)
					$text = __( 'Profile updated for deleted user', 'audit-trail' );
				else
					$text = __( 'Profile updated', 'audit-trail' );

				$item->message = '<a href="#'.$item->id.'" class="audit-view">'.$text.'</a>';
				break;

			case 'user_register' :
				$item->message = '<a href="#'.$item->id.'" class="audit-view">'.__( 'New user registration', 'audit-trail').'</a>';
				break;

			case 'add_link' :
				$item->message = '<a href="#'.$item->id.'" class="audit-view">'.__( 'Add link', 'audit-trail').'</a>';
				break;

			case 'edit_link':
				$item->message = '<a href="#'.$item->id.'" class="audit-view">'.__( 'Edit link', 'audit-trail').'</a>';
				break;

			case 'edit_category' :
				$item->message = '<a href="#'.$item->id.'" class="audit-view">'.__( 'Edit category ', 'audit-trail').'</a>';
				break;

			case 'add_category':
				$item->message = '<a href="#'.$item->id.'" class="audit-view">'.__( 'Add category', 'audit-trail').'</a>';
				break;

			case 'edit_comment' :
				$item->message = '<a href="#'.$item->id.'" class="audit-view">'.__( 'Edit comment', 'audit-trail').'</a>';
				break;

			case 'save_post' :
				$post = unserialize( $item->data );
				if ($post && $post->post_type == 'post')
					$text = __( 'Save post', 'audit-trail' );
				else
					$text = __( 'Save page', 'audit-trail' );
				$item->message = '<a href="#'.$item->id.'" class="audit-view">'.$text.'</a>';;
				break;

			case 'add_attachment' :
				$item->message = '<a href="media.php?action=edit&amp;attachment_id='.$item->item_id.'">'.__( 'Add attachment', 'audit-trail').'</a>';
				break;

			case 'edit_attachment' :
				$item->message = '<a href="media.php?action=edit&amp;attachment_id='.$item->item_id.'">'.__( 'Edit attachment', 'audit-trail').'</a>';
				break;

			default:
				break;
		}

		return $item;
	}


	/**
	 * Default listening methods
	 **/

	// Actions to track
	function delete_post( $post_id ) {
		AT_Audit::create( 'delete_post', $post_id );
	}

	function private_to_published( $post_id ) {
		AT_Audit::create( 'private_to_published', $post_id );
	}

	function save_post( $post_id ) {
		if ( !defined( 'DOING_AJAX' ) )
			AT_Audit::create( 'save_post', $post_id, get_post( $post_id ) );
	}

	function wp_login ($user) {
		$data = get_user_by( 'login', $user );
		AT_Audit::create( 'wp_login', $data->ID, '', '', $data->ID );
	}

	function wp_logout () {
		global $user_ID;
		AT_Audit::create( 'wp_logout', $user_ID );
	}

	function login_errors ($errors) {
		if ( strpos( $errors, __( '<strong>ERROR</strong>: Incorrect password.' ) ) !== false )	{
			$login = get_user_by( 'login', sanitize_user( $_POST['log'] ) );
			AT_Audit::create( 'login_failed', $login->ID, sanitize_user( $_POST['log'] ) );
		}

		return $errors;
	}

	/**
	 * Login failed
	 * Called from wp_authenticate()
	 */
	function wp_login_failed( $username ) {
		AT_Audit::create( 'wp_login_failed', 0, $username );

		if ( get_option( 'audit_error_log' ) )
			error_log( 'WordPress Login Failure: '.$username.' from '.AT_Audit::get_ip() );
	}

	function switch_theme ($newtheme) {
		AT_Audit::create( 'switch_theme', '', $newtheme );
	}

	function edit_link( $link_id ) {
		global $wpdb;

		$link = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->links} WHERE link_id=%d", $link_id ) );
		if ( $link )
			AT_Audit::create( 'edit_link', $link_id, serialize( $link ) );
	}

	function delete_link( $link_id ) {
		global $wpdb;

		$link = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->links} WHERE link_id=%d", $link_id ) );
		if ( $link )
			AT_Audit::create( 'delete_link', $link_id, $link->link_name );
	}

	function add_link( $link_id ) {
		global $wpdb;

		$link = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->links} WHERE link_id=%d", $link_id ) );
		if ( $link )
			AT_Audit::create( 'add_link', $link_id, serialize( $link ) );
	}

	function edit_category( $cat_id ) {
		// We filter here otherwise we get a lot of annoying messages whenever the admin does anything useful
		if ( strpos( $_SERVER['REQUEST_URI'], 'categories.php') !== false )
			AT_Audit::create( 'edit_category', $cat_id, serialize( get_category( $cat_id ) ) );
	}

	function create_category( $cat_id ) {
		AT_Audit::create( 'create_category', $cat_id, serialize( get_category( $cat_id ) ) );
	}

	function add_category( $cat_id ) {
		AT_Audit::create( 'add_category', $cat_id, serialize( get_category( $cat_id ) ) );
	}

	function delete_category( $cat_id ) {
		$cat = get_category ($cat_id);
		AT_Audit::create( 'delete_category', $cat_id, $cat->cat_nicename );
	}

	function user_register( $user_id ) {
		AT_Audit::create( 'user_register', $user_id, serialize( get_userdata( $user_id ) ) );
	}

	function profile_update( $user_id ) {
		AT_Audit::create( 'profile_update', $user_id, serialize( get_userdata( $user_id ) ) );
	}

	function delete_user( $user_id ) {
		$user = get_userdata ($user_id);
		AT_Audit::create( 'delete_user', $user_id, $user->user_nicename );
	}

	function retrieve_password( $name ) {
		AT_Audit::create( 'retrieve_password', '', $name );
	}

	function delete_comment( $comment_id ) {
		AT_Audit::create( 'delete_comment', $comment_id );
	}

	function edit_comment( $comment_id ) {
		global $wpdb;

		$comment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->comments} WHERE comment_ID=%d", $comment_id ) );
		if ( $comment )
			AT_Audit::create( 'edit_comment', $comment_id, serialize( $comment ) );
	}

	function delete_attachment( $postid ) {
		AT_Audit::create( 'delete_attachment', $postid );
	}

	function add_attachment( $postid ) {
		global $wpdb;

		$attach = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_key='_wp_attached_file' ORDER BY meta_id DESC LIMIT 1", $postid ) );
		AT_Audit::create( 'add_attachment', $postid, $attach->meta_value );
	}

	function edit_attachment( $postid ) {
		global $wpdb;

		$attach = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_key='_wp_attached_file' ORDER BY meta_id DESC LIMIT 1", $postid ) );
		AT_Audit::create( 'edit_attachment', $postid, $attach->meta_value );
	}

	function template_redirect() {
		// Don't log 404's
		if ( !is_404() )	{
			global $post, $posts;

			if ( isset( $_GET['preview'] ) && $_GET['preview'] == 'true' )
				return;

			AT_Audit::create( 'template_redirect', count( $posts ) > 1 ? 0 : $post->ID, $_SERVER['REQUEST_URI'] );
		}
	}
}

?>
