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
 * Wraps up the PEAR diff engine
 *
 * @package Audit Trail
 * @author John Godley
 * @copyright Copyright (C) John Godley
 **/


class AT_Diff
{
	var $diff;
	
	
	/**
	 * Constructor takes two strings and generates a diff
	 *
	 * @param string $original
	 * @param string $new
	 * @return void
	 **/
	
	function AT_Diff ($original, $new)
	{
		set_include_path (get_include_path ().PATH_SEPARATOR.dirname (dirname (__FILE__)).'/lib/');
		
		include ('Text/Diff.php');
		include ('Text/Diff/Renderer/inline.php');
		
		$original = explode ("\r", $original);
		$new      = explode ("\r", $new);
		
		$this->diff = new Text_Diff ($new, $original);
	}
	
	
	/**
	 * Returns the computed difference
	 *
	 * @return string Difference
	 **/
	
	function show ()
	{
		$renderer = &new Text_Diff_Renderer_inline();
		$text = $renderer->render ($this->diff);
		if ($text)
			return wpautop ($text);
		return '';
	}
}

?>