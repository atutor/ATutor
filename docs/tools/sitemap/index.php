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

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';
$_section[1][0] = _AT('sitemap');

require(AT_INCLUDE_PATH.'header.inc.php');

	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/index.php?g=11"><img src="images/icons/default/square-large-tools.gif" border="0" vspace="2" class="menuimageh2" width="41" height="40" alt="" /></a> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/index.php?g=11">'._AT('tools').'</a>';
	}
	echo '</h2>';
	
	echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/sitemap-large.gif" class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('sitemap');
	}
	echo '</h3>';

	echo '<p><a href="index.php">'._AT('home').'</a><br />';

	$contentManager->printSiteMapMenu();

	echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" /> <a href="tools/">'._AT('tools').'</a>';
	echo '<br />';
	echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
	echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" /> <a href="tools/preferences.php">'._AT('preferences').'</a>';
	echo '<br />';
	echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
	echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" /> <img src="images/glossary.gif" alt="" class="menuimage8" /> <a href="glossary/">'._AT('glossary').'</a>';
	echo '<br />';
	echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
	if (authenticate(AT_PRIV_FILES, AT_PRIV_RETURN)) {
		echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" />';
	} else {
		echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="menuimage8" /> ';
	}
	echo ' <img src="images/toc.gif" alt="" class="menuimage8" /> <a href="tools/sitemap/">'._AT('sitemap').'</a>';

	if (authenticate(AT_PRIV_FILES, AT_PRIV_RETURN)) {
		echo '<br />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="menuimage8" /> <a href="tools/file_manager.php">'._AT('file_manager').'</a>';
	}

	echo '<br />';
	echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" /> <a href="resources/">'._AT('resources').'</a>';
	echo '<br />';
	echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
	echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="menuimage8" /> <a href="resources/links/">'._AT('links_database').'</a>';

	echo '<br />';
	echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" /> <a href="discussions/">'._AT('discussions').'</a>';
	echo '<br />';
	echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
	echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" /> '._AT('forums').' ';

	$sql	= "SELECT * FROM ".TABLE_PREFIX."forums WHERE course_id=$_SESSION[course_id] ORDER BY title";
	$result = mysql_query($sql, $db);
	$num_forums = mysql_num_rows($result);
	if ($row = mysql_fetch_assoc($result)) {
		do {
			$count++;
			echo '<br />';
			echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
			echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';

			if ($count < $num_forums) {
				echo '<img src="images/'.$rtl.'tree/tree_split.gif" alt="" class="menuimage8" />';
			} else {
				echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="menuimage8" />';
			}
			echo ' <a href="forum/index.php?fid='.$row['forum_id'].'">'.AT_print($row['title'], 'forums.title').'</a>';
		} while ($row = mysql_fetch_assoc($result));
	} else {
		echo '<br />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
		echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="menuimage8" />';
		echo _AT('no_forums');
	}

	echo '<br />';
	echo '<img src="images/'.$rtl.'tree/tree_vertline.gif" alt="" class="menuimage8" />';
	echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="menuimage8" /> <a href="discussions/achat/">'._AT('chat').'</a>';

	echo '<br />';
	echo '<img src="images/'.$rtl.'tree/tree_end.gif" alt="" class="menuimage8" /> <a href="help/">'._AT('help').'</a>';

	echo '</p>';

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>