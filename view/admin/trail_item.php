<td>
<?php if ($item->user_id > 0) : ?>
	<a href="user-edit.php?user_id=<?php echo $item->user_id ?>&amp;wp_http_referer=%2Fsite%2Fwp-admin%2Fusers.php"><?php echo $item->username ?></a>
<?php endif; ?>
</td>
<td><?php echo $item->get_operation () ?></td>
<td><?php echo $item->get_item (); ?></td>
<td><?php echo date (str_replace ('F', 'M', get_option ('date_format')), $item->happened_at).' '.date ('H:i', $item->happened_at) ?></td>
<td id="ip_<?php echo $item->id ?>"><?php echo long2ip ($item->ip); ?></td>