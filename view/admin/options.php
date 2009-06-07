<?php if (!defined ('ABSPATH')) die (); ?><div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php _e ('Audit Trail Options', 'audit-trail'); ?></h2>
	
	<?php $this->submenu (true); ?>
	
	<form action="<?php echo $this->url ($_SERVER['REQUEST_URI']) ?>" method="post" accept-charset="utf-8" style="clear: both">

		<h3><?php _e ('Actions to monitor')?></h3>

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

		<h3>Version Display</h3>

		<table class="form-table">
			<tr>
				<th><label for="post"><?php _e ('Display versions when editing posts', 'audit-trail'); ?>:</label></th>
				<td><input id="post" type="checkbox" name="post"<?php if ($post) echo ' checked="checked"' ?>/></td>
			</tr>
			<tr>
				<th><label for="post_order"><?php _e ('Display versions in ascending date order', 'audit-trail'); ?>:</label></th>
				<td><input id="post_order" type="checkbox" name="post_order"<?php if ($post_order) echo ' checked="checked"' ?>/></td>
			</tr>
		</table>
		<br/>

		<h3>Other Options</h3>

		<table class="form-table">
			<tr>
				<th><?php _e ('Auto-expire', 'audit-trail'); ?></th>
				<td><input size="5" type="text" name="expiry" value="<?php echo $expiry ?>" id="expire"/> <?php _e ('days (0 for no expiry)', 'audit-trail'); ?></td>
			</tr>
			<tr>
				<th><?php _e ('Ignore users', 'audit-trail');?></th>
				<td><input type="text" name="ignore_users" value="<?php echo $ignore_users ?>"/> (separate user IDs with a comma)</td>
			</tr>
		</table>

	<p><input class="button-primary" type="submit" name="save" value="<?php _e ('Save Options', 'audit-trail'); ?>"/></p>

	</form>
</div>