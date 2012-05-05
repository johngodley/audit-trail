<?php if (!defined ('ABSPATH')) die (); ?><?php _e ('Login', 'audit-trail'); ?>:<br/>
<input style="width: 95%" type="text" name="content" value="<?php echo esc_attr ($user->user_login) ?>" readonly="readonly"/>
<br/>

<?php _e ('Email', 'audit-trail'); ?>:<br/>
<input style="width: 95%" type="text" name="content" value="<?php echo esc_attr ($user->user_email) ?>" readonly="readonly"/>
<br/>

<?php _e ('URL', 'audit-trail'); ?>:<br/>
<input style="width: 95%" type="text" name="content" value="<?php echo esc_attr ($user->user_url) ?>" readonly="readonly"/>

