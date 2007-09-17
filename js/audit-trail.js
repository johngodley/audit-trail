function view_audit_open (item)
{
	new Ajax.Updater ('trail_' + item, wp_base + '?cmd=show&id=' + item,
		 {
			asynchronous: true,
		  onLoading: function(request){ $('ip_' + item).innerHTML = wp_loading;}
		 });
	return false;
}

function view_audit_close (item)
{
	new Ajax.Updater ('trail_' + item, wp_base + '?cmd=close&id=' + item,
		 {
			asynchronous: true,
		  onLoading: function(request){ $('ip_' + item).innerHTML = wp_loading;}
		 });
	return false;
}
