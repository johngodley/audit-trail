<?php if (!defined ('ABSPATH')) die (); ?>
<link rel="stylesheet" href="<?php echo $this->url () ?>/post.css" type="text/css" media="screen"/>

<script type="text/javascript">
var audit_base    = '<?php echo $this->url () ?>/';
var audit_loading = '<img src="<?php echo $this->url () ?>/images/progress.gif" alt="loading" width="50" height="16"/>';
</script>
<script type="text/javascript" src="<?php echo $this->url () ?>/js/audit-trail-post.js"></script>
<?php if (!Audit_Trail::is_25 ()) : ?>
<script type="text/javascript" charset="utf-8">
	addLoadEvent( function() {
	var manager = new dbxGroup('audit-trail-box',
	  'vertical', 		// orientation ['vertical'|'horizontal']
		'10', 			// drag threshold ['n' pixels]
		'yes',			// restrict drag movement to container axis ['yes'|'no']
		'10', 			// animate re-ordering [frames per transition, or '0' for no effect]
		'yes', 			// include open/close toggle buttons ['yes'|'no']
		'closed', 		// default state ['open'|'closed']
		'open', 		// word for "open", as in "open this box"
		'close', 		// word for "close", as in "close this box"
		'click-down and drag to move this box', // sentence for "move this box" by mouse
		'click to %toggle% this box', // pattern-match sentence for "(open|close) this box" by mouse
		'use the arrow keys to move this box', // sentence for "move this box" by keyboard
		', or press the enter key to %toggle% it',  // pattern-match sentence-fragment for "(open|close) this box" by keyboard
		'%mytitle%  [%dbxtitle%]' // pattern-match syntax for title-attribute conflicts
		);
	});

</script>
<?php endif; ?>