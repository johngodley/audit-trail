<?php
	if (!defined ('ABSPATH')) die ('No direct access allowed');

	$support = get_option ('audit_support');
	if ( empty( $support ) || $support === false) :?>
<div style="text-align: center; width: 80px; height: 50px; float: right; margin: 5px 15px 1px 0; padding: 4px 3px 0px 3px;-moz-border-radius: 5px; -webkit-border-radius: 5px;" id="support-annoy">
	<a href="<?php echo admin_url( 'tools.php?page=audit-trail.php&amp;sub=support' ); ?>">
		<img src="<?php echo plugins_url( '/images/donate.gif', $this->base_url() ); ?>" alt="support" width="73" height="44" />
	</a>
</div>
<?php endif; ?>
