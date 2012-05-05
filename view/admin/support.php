<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>
<div class="wrap supporter">
	<?php screen_icon(); ?>

	<h2><?php _e ('Audit Trail | Support', 'audit-trail'); ?></h2>
	<?php $this->submenu (true); ?>

	<p style="clear: both">
		<?php _e( 'Audit Trail is free to use - life is wonderful and lovely!  However, it has required a great deal of time and effort to develop and if it has been useful you can help support this development by <strong>making a small donation</strong>.', 'audit-trail'); ?>
		<?php _e( 'This will act as an incentive for me to carry on developing, providing countless hours of support, and including new features and suggestions. You get some useful software and I get to carry on making it.  Everybody wins.', 'audit-trail'); ?>
	</p>

	<p><?php _e( 'If you are using this plugin in a commercial setup, or feel that it\'s been particularly useful, then you may want to consider a <strong>commercial donation</strong>.  If you really really want to show your appreciation then there is the <strong>Super Smashing Great</strong> donation which, along with making my day, will earn you a badge of honour (125x125 image of your choosing + nofollow link) to be displayed on the Audit Trail page for a period of two months.', 'audit-trail' )?>

	<ul class="donations">
		<li>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="admin@urbangiraffe.com">
				<input type="hidden" name="item_name" value="Audit Trail - Individual">
				<input type="hidden" name="amount" value="12.00">
				<input type="hidden" name="buyer_credit_promo_code" value="">
				<input type="hidden" name="buyer_credit_product_category" value="">
				<input type="hidden" name="buyer_credit_shipping_method" value="">
				<input type="hidden" name="buyer_credit_user_address_change" value="">
				<input type="hidden" name="no_shipping" value="1">
				<input type="hidden" name="return" value="http://urbangiraffe.com/plugins/audit-trail/">
				<input type="hidden" name="no_note" value="1">
				<input type="hidden" name="currency_code" value="USD">
				<input type="hidden" name="tax" value="0">
				<input type="hidden" name="lc" value="US">
				<input type="hidden" name="bn" value="PP-DonationsBF">
				<input type="image" style="border: none" src="<?php echo plugins_url( '/images/donate.gif', $this->base_url() ); ?>" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!"/>
			</form>

			<p><strong>$12</strong><br/><?php _e( 'Individual<br/>Donation', 'audit-trail' ); ?></p>
		</li>
		<li>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="admin@urbangiraffe.com">
				<input type="hidden" name="item_name" value="Audit Trail - Commercial">
				<input type="hidden" name="amount" value="42.00">
				<input type="hidden" name="buyer_credit_promo_code" value="">
				<input type="hidden" name="buyer_credit_product_category" value="">
				<input type="hidden" name="buyer_credit_shipping_method" value="">
				<input type="hidden" name="buyer_credit_user_address_change" value="">
				<input type="hidden" name="no_shipping" value="1">
				<input type="hidden" name="return" value="http://urbangiraffe.com/plugins/audit-trail/">
				<input type="hidden" name="no_note" value="1">
				<input type="hidden" name="currency_code" value="USD">
				<input type="hidden" name="tax" value="0">
				<input type="hidden" name="lc" value="US">
				<input type="hidden" name="bn" value="PP-DonationsBF">
				<input type="image" style="border: none" src="<?php echo plugins_url( '/images/donate.gif', $this->base_url() ); ?>" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!"/>
			</form>
			<p><strong>$42</strong><br/><?php _e( 'Commercial<br/>Donation', 'audit-trail' ); ?></p>
		</li>
		<li>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="admin@urbangiraffe.com">
				<input type="hidden" name="item_name" value="Audit Trail - Super Smashing Great">
				<input type="hidden" name="amount" value="142.00">
				<input type="hidden" name="buyer_credit_promo_code" value="">
				<input type="hidden" name="buyer_credit_product_category" value="">
				<input type="hidden" name="buyer_credit_shipping_method" value="">
				<input type="hidden" name="buyer_credit_user_address_change" value="">
				<input type="hidden" name="no_shipping" value="1">
				<input type="hidden" name="return" value="http://urbangiraffe.com/plugins/audit-trail/">
				<input type="hidden" name="no_note" value="1">
				<input type="hidden" name="currency_code" value="USD">
				<input type="hidden" name="tax" value="0">
				<input type="hidden" name="lc" value="US">
				<input type="hidden" name="bn" value="PP-DonationsBF">
				<input type="image" style="border: none" src="<?php echo plugins_url( '/images/donate.gif', $this->base_url() ); ?>" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!"/>
			</form>
			<p><strong>$142</strong><br/><?php _e( 'Super Smashing<br/>Great Donation', 'audit-trail' ); ?></p>
		</li>
	</ul>

	<h3 style="clear: both"><?php _e( 'Translations', 'audit-trail' )?></h3>

	<p><?php _e( 'If you\'re multi-lingual then you may want to consider donating a translation.', 'audit-trail' )?>

	<p><?php _e( 'Full details of producing a translation can be found in this <a href="http://urbangiraffe.com/articles/translating-wordpress-themes-and-plugins/">guide to translating WordPress plugins</a>.', 'audit-trail' )?>
</div>
