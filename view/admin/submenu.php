<?php if (!defined ('ABSPATH')) die (); ?>
<ul <?php echo $class ?>>
  <li><a <?php if (!isset($_GET['sub'])) echo 'class="current"'; ?> href="<?php echo admin_url( 'tools.php?page=audit-trail' ); ?>"><?php _e ('Audit Trail','audit-trail') ?></a><?php echo $trail; ?></li>
  <li><a <?php if (isset($_GET['sub']) && $_GET['sub'] == 'options') echo 'class="current"'; ?> href="<?php echo admin_url( 'tools.php?page=audit-trail' ); ?>&amp;sub=options"><?php _e ('Options','audit-trail') ?></a><?php echo $trail; ?></li>
  <li><a <?php if (isset($_GET['sub']) && $_GET['sub'] == 'support') echo 'class="current"'; ?> href="<?php echo admin_url( 'tools.php?page=audit-trail' ); ?>&amp;sub=support"><?php _e ('Support','audit-trail') ?></a></li>
</ul>
