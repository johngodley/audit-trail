<?php if (!defined ('ABSPATH')) die (); ?><div class="wrap">
	<?php screen_icon(); ?>

	<div class="csv">
		<a title="Download as CSV" href="<?php echo plugins_url( 'csv.php', $this->base_url() ); ?>">
			<img src="<?php echo plugins_url( '/images/csv.png', $this->base_url() ); ?>" width="16" height="16" alt="Csv"/>
		</a>
	</div>
	<h2><?php _e ('Audit Trail', 'audit-trail'); ?></h2>

	<?php $this->submenu (true); ?>

	<form method="POST" action="">
		<?php $table->display(); ?>
	</form>
</div>
</form>

<script type="text/javascript">
	( function($) {
		$( document ).ready( function() {
		var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';

		function clickers() {
			$( 'a.audit-view' ).unbind( 'click' ).click( function() {
				var item = $( this ).parents( 'tr' );
				var original = item.html();
				var itemid = this.href.replace( /.*?#(.*)/, '$1' );
				var nonce  = '<?php echo wp_create_nonce( 'audittrail_view' )?>';

				item.load( ajaxurl, {
					action: 'at_view',
					id: itemid,
					_ajax_nonce: nonce
				}, function() {
					item.data( 'original', original );

					$( item ).find( 'a' ).click( function() {
						item.html( item.data( 'original' ) );
						item.data( 'original', false );

						clickers();
						return false;
					});
				});

				return false;
			});
		}

		clickers();
		} );
	})( jQuery );
</script>
