<?php if (!defined ('ABSPATH')) die (); ?><div class="wrap">
	<?php	$this->render_admin ('annoy'); ?>
	<?php screen_icon(); ?>
	
	<div class="csv"><a title="Download as CSV" href="<?php echo $this->url () ?>/csv.php"><img src="<?php echo $this->url () ?>/images/csv.png" width="16" height="16" alt="Csv"/></a></div>
	<h2><?php _e ('Audit Trail', 'audit-trail'); ?></h2>

	<?php $this->submenu (true); ?>
	
	<?php $this->render_admin ('pager', array ('pager' => $pager)); ?>
	
	<form method="post" action="<?php echo $this->url ($pager->url) ?>">
		<div id="pager" class="tablenav">
			<div class="alignleft actions">
				<select name="action2" id="action2_select">
					<option value="-1" selected="selected"><?php _e('Bulk Actions'); ?></option>
					<option value="delete"><?php _e('Delete'); ?></option>
				</select>
				
				<input type="submit" value="<?php _e('Apply'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
				
				<?php $pager->per_page ('audit-trail'); ?>

				<input type="submit" value="<?php _e('Filter'); ?>" class="button-secondary" />

				<br class="clear" />
			</div>
		
			<div class="tablenav-pages">
				<?php echo $pager->page_links (); ?>
			</div>
		</div>
	
	<?php if (count ($trail) > 0) : ?>
	  <table  class="widefat post fixed" cellpadding="5">
		<thead>
		  <tr>
				<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
			  <th width="100"><?php echo $pager->sortable ('user_id', __ ('User','audit-trail')) ?></th>
				<th><?php echo $pager->sortable ('operation', __ ('Action','audit-trail')) ?></th>
				<th><?php echo $pager->sortable ('item_id', __ ('Target','audit-trail')) ?></th>
				<th width="170"><?php echo $pager->sortable ('happened_at', __('Date','audit-trail')) ?></th>
				<th width="100"><?php echo $pager->sortable ('ip', __('IP','audit-trail')) ?></th>
			</tr>
		</thead>
		
		<tfoot>
		  <tr>
				<th style="" class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
			  <th width="100"><?php echo $pager->sortable ('user_id', __ ('User','audit-trail')) ?></th>
				<th><?php echo $pager->sortable ('operation', __ ('Action','audit-trail')) ?></th>
				<th><?php echo $pager->sortable ('item_id', __ ('Target','audit-trail')) ?></th>
				<th width="170"><?php echo $pager->sortable ('happened_at', __('Date','audit-trail')) ?></th>
				<th width="100"><?php echo $pager->sortable ('ip', __('IP','audit-trail')) ?></th>
			</tr>
		</tfoot>

			<tbody>
			<?php foreach ($trail AS $pos => $item) : ?>
				<tr id="trail_<?php echo $item->id ?>"<?php if ($pos % 2 == 1) echo ' class="alt"'; ?>>
					<?php $this->render_admin ('trail_item', array ('item' => $item)) ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		
		<div class="tablenav">
			<div class="tablenav-pages">
				<?php echo $pager->page_links (); ?>
			</div>
		</div>
		
		<div style="clear: both"></div>
	<?php else : ?>
		<p><?php _e ('There is nothing to display!', 'audit-trail') ?></p>
	<?php endif; ?>
</div>
</form>

<script type="text/javascript" charset="utf-8">
	jQuery(document).ready( function() {
		var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
		
		function clickers() {
			jQuery( '.audit-view' ).click( function() {
				jQuery( '.audit-view' ).unbind( 'click' );

				var item   = jQuery( this ).parents( 'tr' );
				var itemid = this.href.replace( /.*?#(.*)/, '$1' );
				var nonce  = '<?php echo wp_create_nonce( 'audittrail_view' )?>';
			
				item.load( ajaxurl, {
					action: 'at_view',
					id: itemid,
					_ajax_nonce: nonce
				}, function() {
					jQuery( item ).find( 'a' ).click( function() {
						item.load( ajaxurl, {
							action: 'at_close',
							id: itemid,
							_ajax_nonce: nonce
						}, function() {
							clickers();
						} );
					
						return false;
					});
				});
			
				return false;
			});
		}
		
		clickers();
	});
</script>