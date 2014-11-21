<?php if (!defined ('ABSPATH')) die (); ?><div class="wrap">
	<?php	$this->render_admin ('annoy'); ?>
	<?php screen_icon(); ?>
	<h2><?php _e ('Audit Trail Options', 'audit-trail'); ?></h2>
	
	<?php $this->submenu (true); ?>
	
	<form action="<?php echo $this->url ($_SERVER['REQUEST_URI']) ?>" method="post" accept-charset="utf-8" style="clear: both">
		<?php wp_nonce_field ('audittrail-update_options'); ?>

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

		<h3><?php _e( 'Other Options', 'audit-trail' ); ?></h3>

		<table class="form-table">
			<tr>
				<th><label for="support"><?php _e ('Plugin Support', 'audit-trail'); ?>:</label></th>
				<td>
					<input type="checkbox" name="support" id="support"<?php if ($support) echo ' checked="checked"' ?>/>
					<span class="sub"><?php printf( __( 'Click this if you have <a href="%s">supported</a> the author', 'audit-trail'), $this->base().'?page=search-unleashed.php&amp;sub=support'); ?></span>
				</td>
			</tr>
			<tr>
				<th><?php _e ('Auto-expire', 'audit-trail'); ?></th>
				<td><input size="5" type="text" name="expiry" value="<?php echo $expiry ?>" id="expire"/> <?php _e ('days (0 for no expiry)', 'audit-trail'); ?></td>
			</tr>
			<tr>
				<th><?php _e ('Ignore users', 'audit-trail');?></th>
				<td><input type="text" name="ignore_users" value="<?php echo $ignore_users ?>"/> (<?php _e( 'separate user IDs with a comma', 'audit-trail'); ?>)</td>
			</tr>
		</table>

	<p><input class="button-primary" type="submit" name="save" value="<?php _e ('Save Options', 'audit-trail'); ?>"/></p>

	</form>
</div>