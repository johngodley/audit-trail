<div class="audit">
	<?php echo $diff->show (); ?>
</div>

<input type="button" name="cancel" value="<?php _e ('Cancel', 'audit-trail') ?>" onclick="$('audit-view-box').innerHTML = ''"/>
<input type="button" name="cancel" value="<?php _e ('Delete', 'audit-trail') ?>" onclick="audit_delete(<?php echo $id ?>)"/>
<input type="button" name="cancel" value="<?php _e ('Restore', 'audit-trail') ?>" onclick="audit_restore (<?php echo $id ?>)"/>
