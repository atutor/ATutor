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

$page = 'discussions';
define('AT_INCLUDE_PATH', '../include/');

require (AT_INCLUDE_PATH.'vitals.inc.php');

//$_section[0][0] = _AT('discussions');

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/';
$_section[1][0] = _AT('forums');
$_section[1][1] = 'forum/';

require (AT_INCLUDE_PATH.'header.inc.php');

	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-discussions.gif" width="42" height="38" border="0" alt="" class="menuimageh2" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo _AT('discussions');
	}
	echo '</h2><br />';

	if ((authenticate(AT_PRIV_FORUMS, AT_PRIV_RETURN) || authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) && $_SESSION['prefs'][PREF_EDIT]) {
		$help[] = AT_HELP_CREATE_FORUMS;
		$help[] = array(AT_HELP_ENABLE_EDITOR, $_my_uri);
		print_help($help);
	}


	$sql	= "SELECT * FROM ".TABLE_PREFIX."forums ORDER BY title";
	$result = mysql_query($sql, $db);

	if ($row = mysql_fetch_array($result)) {

		echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="95%">';
		echo '<tr>';
		echo '	<th colspan="7" class="cyan">'._AT('forums').'</th>';
		echo '</tr>';
		echo '<tr>';
		echo '	<th scope="col" class="cat"><a name="list"></a><small>'._AT('forum').'</small></th>';
		echo '	<th scope="col" class="cat"><small>'._AT('forum_topics').'</small></th>';
		echo '	<th scope="col" class="cat"><small>'._AT('posts').'</small></th>';
		echo '	<th scope="col" class="cat"><small>'._AT('last_post').'</small></th>';
		echo '</tr>';

		$counter = 0;
		do {
			$counter++;
			echo '<tr>';
			echo '<td class="row1 lineL"><a href="forum/index.php?fid='.$row['forum_id'].'"><b>'.$row['title'].'</b></a>';
			echo '<p>'.$row['description'].'</p></td>';
			echo '<td class="row1" align="center" valign="top">'.$row['num_topics'].'</td>';
			echo '<td class="row1" align="center" valign="top">'.$row['num_posts'].'</td>';
			echo '<td class="row1 lineR" align="right" nowrap="nowrap" valign="top">';

			if ($row['last_post'] == '0000-00-00 00:00:00') {
				echo '<em>N/A</em>';
			} else {
				echo $row['last_post'];
			}
			echo '</td>';
			echo '</tr>';
		} while ($row = mysql_fetch_array($result));
		echo '</table>';
	} else {
		echo '<p><i>'._AT('no_forum').'</i></p>';
	}

	
	require (AT_INCLUDE_PATH.'footer.inc.php');
?>