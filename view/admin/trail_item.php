<?php if (!defined ('ABSPATH')) die (); ?>

<th class="check-column" scope="row">
		<input type="checkbox" value="<?php echo esc_attr( $item->id ); ?>" name="item[]"/>
</th>

<td>
<?php if ($item->user_id > 0) : ?>
	<a href="user-edit.php?user_id=<?php echo $item->user_id ?>&amp;wp_http_referer=%2Fsite%2Fwp-admin%2Fusers.php"><?php echo esc_attr( $item->username ) ?></a>
<?php endif; ?>
</td>
<td><?php echo $item->get_operation () ?></td>
<td><?php echo $item->get_item (); ?></td>
<td><?php echo current_time($item->happened_at) ?> <?php echo date_i18n (get_option ('date_format'), $item->happened_at).' '.gmdate ('H:i', $item->happened_at) ?></td>
<td id="ip_<?php echo $item->id ?>"><a href="http://urbangiraffe.com/map/?ip=<?php echo long2ip ($item->ip); ?>&amp;from=audittrail"><?php echo long2ip ($item->ip); ?></a></td>