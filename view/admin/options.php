<div class="wrap">
	<h2><?php _e ('Audit Trail Options', 'audit-trail'); ?></h2>
	
	<p><?php _e ('Select the groups of actions you want to monitor', 'audit-trail'); ?>:</p>

	<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" accept-charset="utf-8">
	<?php if (count ($methods) > 0) : ?>
		<ul class="options">
			<?php foreach ($methods AS $key => $name) : ?>
			<li><label><input type="checkbox" name="methods[]" value="<?php echo $key ?>"<?php if (in_array ($key, $current)) echo ' checked="checked"' ?>/>
				 <?php echo htmlspecialchars ($name) ?></label>
			</li>
			<?php endforeach; ?>
		</ul>

	<?php else : ?>
	<p><?php _e ('There are no actions to monitor', 'audit-trail'); ?></p>
	<?php endif; ?>

	<p><?php _e ('Auto-expire after', 'audit-trail'); ?> <input size="5" type="text" name="expiry" value="<?php echo $expiry ?>" id="expire"/> <?php _e ('days (0 for no expiry)', 'audit-trail'); ?></p>
	
	<p><label><?php _e ('Display versions when editing posts', 'audit-trail'); ?>: <input type="checkbox" name="post"<?php if ($post) echo ' checked="checked"' ?>/></label></p>

	<p><label><?php _e ('Display versions in ascending date order', 'audit-trail'); ?>: <input type="checkbox" name="post_order"<?php if ($post_order) echo ' checked="checked"' ?>/></label></p>

	<p><label><?php _e ('Check for new updates', 'audit-trail'); ?>: <input type="checkbox" name="version"<?php if ($version) echo ' checked="checked"' ?>/></label></p>
	
	<p><input type="submit" name="save" value="<?php _e ('Save Options', 'audit-trail'); ?>"/></p>

	</form>
</div>