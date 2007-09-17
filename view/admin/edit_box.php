<?php if (count ($trail) > 0) : ?>
<div id="audit-trail-box" class="dbx-group">
	<div class="dbx-box-wrapper">
		<fieldset id="audit-trail" class="dbx-box">
			<div class="dbx-handle-wrapper">
			<h3 class="dbx-handle"><?php _e ('Audit Trail', 'audit-trail') ?></h3>
			</div>
	
			<div class="dbx-content-wrapper">
			<div class="dbx-content">
				<select id="audit-trail-option">
				<?php foreach ($trail AS $pos => $item) : ?>
					<option value="<?php echo $item->id ?>">#<?php echo $pos + 1?>: 
						<?php printf (__ ('Edited by %s on %s at %s', 'audit-trail'), $item->username, date (get_option ("date_format"), $item->happened_at), date (get_option ('time_format'), $item->happened_at)); ?></option>
				<?php endforeach; ?>
				</select>

				<input type="button" name="view" value="<?php _e ('View', 'audit-trail') ?>" id="view" onclick="audit_view(); return false"/>
				<div id="audit-view-box" style="display: none">
				</div>
			</div>
			</div>
		</fieldset>
	</div>
</div>
<?php endif; ?>