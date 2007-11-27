<div class="wrap">
	<h2><?php _e ('Audit Trail Options', 'audit-trail'); ?></h2>
	
	<form action="<?php echo $this->url ($_SERVER['REQUEST_URI']) ?>" method="post" accept-charset="utf-8">

	<fieldset>
		<legend><?php _e ('Actions to monitor')?></legend>

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
	</fieldset>
	
	<fieldset>
		<legend>Version Display</legend>

		<table>
			<tr>
				<td><label for="post"><?php _e ('Display versions when editing posts', 'audit-trail'); ?>:</label></td>
				<td><input id="post" type="checkbox" name="post"<?php if ($post) echo ' checked="checked"' ?>/></td>
			</tr>
			<tr>
				<td><label for="post_order"><?php _e ('Display versions in ascending date order', 'audit-trail'); ?>:</label></td>
				<td><input id="post_order" type="checkbox" name="post_order"<?php if ($post_order) echo ' checked="checked"' ?>/></td>
			</tr>
		</table>
		<br/>
	</fieldset>
	
	<fieldset>
		<legend>Other Options</legend>

		<table>
			<tr>
				<td><?php _e ('Auto-expire', 'audit-trail'); ?></td>
				<td><input size="5" type="text" name="expiry" value="<?php echo $expiry ?>" id="expire"/> <?php _e ('days (0 for no expiry)', 'audit-trail'); ?></td>
			</tr>
			<tr>
				<td><?php _e ('Ignore users', 'audit-trail');?></td>
				<td><input type="text" name="ignore_users" value="<?php echo $ignore_users ?>"/> (separate user IDs with a comma)</td>
			</tr>
		</table>
	</fieldset>

	<p><input type="submit" name="save" value="<?php _e ('Save Options', 'audit-trail'); ?>"/></p>

	</form>
</div>