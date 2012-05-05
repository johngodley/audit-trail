<?php if (!defined ('ABSPATH')) die (); ?><?php _e ('Author', 'audit-trail'); ?>:<br/>
<input style="width: 95%" type="text" name="content" value="<?php echo esc_attr ($comment->comment_author) ?>" readonly="readonly"/>
<br/>

<?php _e ('Content', 'audit-trail'); ?>:<br/>
<textarea rows="10" cols="5" readonly="readonly"><?php echo esc_html ($comment->comment_content)?></textarea><br/>
