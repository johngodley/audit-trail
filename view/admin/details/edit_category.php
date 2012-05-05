<?php if (!defined ('ABSPATH')) die (); ?><?php _e ('Name', 'audit-trail'); ?>:<br/>
<input style="width: 95%" type="text" name="content" value="<?php echo esc_attr ($cat->cat_name) ?>" readonly="readonly"/>
<br/>

<?php _e ('URL', 'audit-trail'); ?>:<br/>
<input style="width: 95%" type="text" name="content" value="<?php echo esc_attr ($cat->category_nicename) ?>" readonly="readonly"/>
<br/>

<?php _e ('Description', 'audit-trail'); ?>:<br/>
<textarea rows="5" cols="5" readonly="readonly"><?php echo esc_html ($cat->category_description)?></textarea><br/>

