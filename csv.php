<?php
/* ============================================================================================================
	 This software is provided "as is" and any express or implied warranties, including, but not limited to, the
	 implied warranties of merchantibility and fitness for a particular purpose are disclaimed. In no event shall
	 the copyright owner or contributors be liable for any direct, indirect, incidental, special, exemplary, or
	 consequential damages (including, but not limited to, procurement of substitute goods or services; loss of
	 use, data, or profits; or business interruption) however caused and on any theory of liability, whether in
	 contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of
	 this software, even if advised of the possibility of such damage.

	 This software is provided free-to-use, but is not free software.  The copyright and ownership remains
	 entirely with the author.  Please distribute and use as necessary, in a personal or commercial environment,
	 but it cannot be sold or re-used without express consent from the author.
   ============================================================================================================ */

/**
 * Provides CSV export
 *
 * @package Audit Trail
 * @author John Godley
 **/

include ('../../../wp-config.php');

if (!current_user_can ('edit_plugins'))
	die ('<p style="color: red">You are not allowed access to this resource</p>');

$id = 0;
if ( isset( $_GET['id'] ) )
	$id   = intval ($_GET['id']);

header ("Content-Type: application/vnd.ms-excel");
header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

function escape ($value)
{
	// Escape any special values
	$double = false;
	if (strpos ($value, ',') !== false)
		$double = true;

	if (strpos ($value, '"') !== false)
	{
		$double = true;
		$value  = str_replace ('"', '""', $value);
	}

	if ($double)
		$value = '"'.$value.'"';
	return $value;
}

header ('Content-Disposition: attachment; filename="audit-trail.csv"');

$trail = AT_Audit::get_everything ();
if (count ($trail) > 0)
{
	echo "Date,Time,User,Operation,Item,IP\r\n";

	foreach ($trail AS $item) {
		$csv = array();
		$csv[] = escape( date ('Y-m-d', $item->happened_at));
		$csv[] = escape( date ('H:i', $item->happened_at));
		$csv[] = escape( $item->username );
		$csv[] = escape( strip_tags ($item->get_operation ()));
		$csv[] = escape( strip_tags ($item->get_item ()));
		$csv[] = escape( long2ip ($item->ip));

		echo implode( ',', $csv )."\r\n";
	}
}

