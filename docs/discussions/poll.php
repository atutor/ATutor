<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/index.php';
$_section[1][0] = _AT('polls');
$_section[1][1] = 'discussions/polls.php';
$_section[2][0] = _AT('poll');

require(AT_INCLUDE_PATH.'header.inc.php'); 


echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="discussions/" class="hide" ><img src="images/icons/default/square-large-discussions.gif" vspace="2" border="0"  class="menuimageh2" width="42" height="40" alt="" /></a> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="discussions/" class="hide" >'._AT('discussions').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/polls-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="discussions/polls.php">'._AT('polls').'</a>';;
	}
echo '</h3>';


	if (isset($_POST['poll_submit'], $_POST['choice'])) {
		$poll_id = intval($_POST['poll_id']);

		$sql = "INSERT INTO ".TABLE_PREFIX."polls_members VALUES($poll_id, $_SESSION[member_id])";
		if ($result = mysql_query($sql, $db)) {
			$n = intval($_POST['choice']);

			$sql = "UPDATE ".TABLE_PREFIX."polls SET count$n=count$n+1, total=total+1 WHERE poll_id=$poll_id AND course_id=$_SESSION[course_id]";
			$result = mysql_query($sql, $db);
		}
	}

	if (!isset($include_all, $include_one)) {
		$include_one = ' checked="checked"';
	}

	$id = intval($_GET['id']);

	$sql = "SELECT * FROM ".TABLE_PREFIX."polls WHERE course_id=$_SESSION[course_id] AND poll_id=$id";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		echo '<form method="post" action="'.$_SERVER['REQUEST_URI'].'">';
		echo '<table width="70%" border="0" cellspacing="0" cellpadding="0" summary="" class="dropdown" align="center">';
		echo '<tr>';
		echo '<td valign="top" class="dropdown-heading" nowrap="nowrap" align="left"><strong>' . AT_print($row['question'], 'polls.question') . '</strong>';
		echo '<input type="hidden" name="poll_id" value="'.$row['poll_id'].'" /></td></tr>';

		if (!authenticate(AT_PRIV_POLLS, AT_PRIV_RETURN)) {
			$sql = "SELECT * FROM ".TABLE_PREFIX."polls_members WHERE poll_id=$row[poll_id] AND member_id=$_SESSION[member_id]";
			$result = mysql_query($sql, $db);
		}
		if (authenticate(AT_PRIV_POLLS, AT_PRIV_RETURN) || ($my_row = mysql_fetch_assoc($result))) {
			for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++) {
				if ($row['choice' . $i]) {
					if ($row['total']) {
						$width = round($row['count' . $i] / $row['total'] * 110);
					} else {
						$width = 0;
					}

					echo '<tr>';
					echo '<td valign="top" class="dropdown" nowrap="nowrap" align="left">';
					echo '<small>' . AT_print($row['choice' . $i], 'polls.choice') . '</small><br />';
					echo '<img src="'.$_base_path . 'images/blue.gif" height="5" width="'.$width.'" alt="" /> '.$row['count' . $i];
					echo '</td></tr>';
				}
			}
		} else {
			for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++) {
				if ($row['choice' . $i]) {
					echo '<tr>';
					echo '<td valign="top" class="dropdown" nowrap="nowrap" align="left">';
					echo '<small><input type="radio" name="choice" value="'.$i.'" id="xc'.$i.'" /><label for="xc'.$i.'">' . AT_print($row['choice' . $i], 'polls.choice') . '</label></small></td></tr>';
				}
			}

			echo '<tr>';
			echo '<td valign="top" class="dropdown" nowrap="nowrap" align="center"><input type="submit" name="poll_submit" value="'._AT('submit').'" class="button" />';
			echo '<br /><small>'._AT('vote_to_see_results').'</small>';
			echo '</td></tr>';
		}
		
		echo '</table></form>';
	}

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>