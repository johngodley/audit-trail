<?php if (!defined ('ABSPATH')) die (); ?>
<th class="check-column" scope="row">
	<input type="checkbox" value="<?php echo esc_attr( $item->id ); ?>" name="item[]"/>
</th>
<td>
<?php if ($item->user_id > 0) : ?>
	<a href="user-edit.php?user_id=<?php echo esc_attr( $item->user_id ) ?>&amp;wp_http_referer=%2Fsite%2Fwp-admin%2Fusers.php"><?php echo esc_html( $item->username ) ?></a>
<?php endif; ?>
</td>

<td colspan="2"><?php echo $item->get_details() ?></td>
<td><?php echo date( get_option( 'date_format' ), $item->happened_at ).' '.date( get_option( 'time_format' ), $item->happened_at ) ?></td>
<td id="ip_<?php echo esc_attr( $item->id ) ?>"><?php echo long2ip( $item->ip ); ?></td>
