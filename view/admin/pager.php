<?php if (!defined ('ABSPATH')) die (); ?><div class="pager">
	<form method="get" action="<?php echo $pager->url ?>">
		<input type="hidden" name="page" value="audit-trail.php"/>
		<input type="hidden" name="curpage" value="<?php echo $pager->current_page () ?>"/>

		<?php _e ('Search', 'audit-trail'); ?>: 
		<input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars ($_GET['search']) : '' ?>"/>
		
		<?php _e ('Results per page', 'audit-trail') ?>: 
		<select name="perpage">
			<?php foreach ($pager->steps AS $step) : ?>
		  	<option value="<?php echo $step ?>"<?php if ($pager->per_page == $step) echo ' selected="selected"' ?>><?php echo $step ?></option>
			<?php endforeach; ?>
		</select>
		
		<input class="button-secondary" type="submit" name="go" value="<?php _e ('Go', 'audit-trail') ?>"/>
	</form>
</div>
