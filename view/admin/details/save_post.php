<?php if (!defined ('ABSPATH')) die (); ?><?php _e ('Title', 'audit-trail'); ?>:<br/>
<input style="width: 95%" type="text" name="content" value="<?php echo htmlspecialchars ($post->post_title) ?>" readonly="readonly"/>
<br/>

<?php _e ('URL', 'audit-trail'); ?>:<br/>
<input style="width: 95%" type="text" name="content" value="<?php echo htmlspecialchars ($post->post_name) ?>" readonly="readonly"/>
<br/>

<?php _e ('Content', 'audit-trail'); ?>:<br/>
<textarea rows="10" cols="5" readonly="readonly"><?php echo htmlspecialchars ($post->post_content)?></textarea><br/>

<?php $difftext = $diff->show (); ?>
<?php if (strlen ($difftext) > 0) : ?>
<br/>
<?php _e ('Difference between this and current version', 'audit-trail'); ?>:
<div class="diff" style="width: 95%">
	<?php echo $difftext ?>
</div>
<?php endif; ?>

