<div class="wrap">
	<div class="csv"><a title="Download as CSV" href="<?php echo $this->url () ?>/csv.php"><img src="<?php echo $this->url () ?>/images/csv.png" width="16" height="16" alt="Csv"/></a></div>
	<h2><?php _e ('Audit Trail', 'audit-trail'); ?></h2>

	<?php $this->render_admin ('pager', array ('pager' => $pager)); ?>

	<?php if (count ($trail) > 0) : ?>
	  <table class="audit" cellpadding="5">
		  <tr>
			  <th width="100"><?php echo $pager->sortable ('user_id', __ ('User','audit-trail')) ?></th>
				<th><?php echo $pager->sortable ('operation', __ ('Action','audit-trail')) ?></th>
				<th><?php echo $pager->sortable ('item_id', __ ('Target','audit-trail')) ?></th>
				<th width="170"><?php echo $pager->sortable ('happened_at', __('Date','audit-trail')) ?></th>
				<th width="100"><?php echo $pager->sortable ('ip', __('IP','audit-trail')) ?></th>
			</tr>
			
			<tfoot>
				<tr>
					<td colspan="5">
						<div class="pager">
					<?php foreach ($pager->area_pages () AS $page) : ?>
						<?php echo $page ?>
					<?php endforeach; ?>&nbsp;
					</div>
				</td>
			</tfoot>
			
			<?php foreach ($trail AS $pos => $item) : ?>
				<tr id="trail_<?php echo $item->id ?>"<?php if ($pos % 2 == 1) echo ' class="alt"'; ?>>
					<?php $this->render_admin ('trail_item', array ('item' => $item)) ?>
				</tr>
			<?php endforeach; ?>
		</table>
		
		<div style="clear: both"></div>
	<?php else : ?>
		<p><?php _e ('There is nothing to display!', 'audit-trail') ?></p>
	<?php endif; ?>
</div>
