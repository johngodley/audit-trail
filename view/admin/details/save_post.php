<?php if (!defined ('ABSPATH')) die (); ?><?php _e ('Title', 'audit-trail'); ?>:<br/>

<input style="width: 95%" type="text" name="content" value="<?php echo esc_attr( $post->post_title ) ?>" readonly="readonly"/>
<br/>

<?php _e ('URL', 'audit-trail'); ?>:<br/>
<input style="width: 95%" type="text" name="content" value="<?php echo esc_attr( $post->post_name ) ?>" readonly="readonly"/>
<br/>

<?php _e ('Content', 'audit-trail'); ?>:<br/>
<textarea rows="10" cols="40" style="width:95%" readonly="readonly"><?php echo esc_html( $post->post_content )?></textarea><br/>

