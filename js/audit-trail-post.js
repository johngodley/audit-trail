function audit_view ()
{
  // And now for the AJAX bit
  new Ajax.Updater ('audit-view-box', audit_base + 'ajax_post.php?cmd=view&id=' + $('audit-trail-option').value,
    {
      onLoading: function () { $('audit-view-box').innerHTML = audit_loading; Element.show ('audit-view-box')}
    });
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
