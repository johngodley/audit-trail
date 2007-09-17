function audit_view ()
{
  // Show a nice loading box
  $('audit-view-box').innerHTML = audit_loading;
  Element.show ('audit-view-box');
  
  // And now for the AJAX bit
  var sel = $('audit-trail-option');
  new Ajax.Updater('audit-view-box', audit_base + 'ajax_post.php?cmd=view&id=' + sel.value, {});
}

function audit_restore (item)
{
  if (confirm ('Are you sure you want restore this version?'))
  {
    new Ajax.Request (audit_base + 'ajax_post.php?cmd=restore&id=' + item, { onComplete: function (req) {window.location.reload ()} });
  }
}

function audit_delete (item)
{
  if (confirm ('Are you sure you want to delete this version?'))
  {
    new Ajax.Request (audit_base + 'ajax_post.php?cmd=delete&id=' + item, { onComplete: function (req) {window.location.reload ()} });
  }  
}

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