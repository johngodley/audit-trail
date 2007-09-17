<ul id="subsubmenu">
  <li><a <?php if (!isset($_GET['sub'])) echo 'class="current"'; ?>href="<?php echo $url ?>"><?php _e ('Audit Trail','audit-trail') ?></a></li>
  <li><a <?php if ($_GET['sub'] == 'options') echo 'class="current"'; ?>href="<?php echo $url ?>&amp;sub=options"><?php _e ('Options','audit-trail') ?></a></li>
</ul>