<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

	$page = 'resources';
	define('AT_INCLUDE_PATH', '../include/');
	
	require (AT_INCLUDE_PATH.'vitals.inc.php');
	$_section[0][0] = _AT('resources');

	require (AT_INCLUDE_PATH.'header.inc.php');
	
echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-resources.gif" class="menuimage" border="0" vspace="2" width="42" height="40" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('resources');
	}
echo '</h2>';

?>

<br /><table border="0" cellspacing="0" cellpadding="3" summary="">
<tr>
	<?php 
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
				echo '<td rowspan="2" valign="top"><a href="resources/links/index.php?g=29"><img src="images/icons/default/links-small.gif" class="menuimage"width="28" height="25" border="0" alt="*" /></a></td>';
			}
			echo '<td>';
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
				echo ' <a href="resources/links/index.php?g=29">'._AT('links_database').'</a>';
			}
			echo '</td></tr><tr><td>';
			echo _AT('links_database_text');
		?></td>
</tr>
<tr>
	<?php 
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
				echo '<td rowspan="2" valign="top"><a href="resources/tile/index.php?g=29"><img src="images/icons/default/search_tile-small.gif" class="menuimage"width="28" height="25" border="0" alt="*" /></a></td>';
			}
			echo '<td>';
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
				echo ' <a href="resources/tile/index.php?g=29">'._AT('tile_search').'</a>';
			}
			echo '</td></tr><tr><td>';
			echo _AT('tile_search_text');
		?></td>
</tr>
</table>

<?php
	require (AT_INCLUDE_PATH.'footer.inc.php');
?>
