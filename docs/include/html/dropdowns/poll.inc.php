<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }
global $_my_uri;
global $_base_path, $include_all, $include_one;
global $savant;

$savant->assign('tmpl_popup_help', AT_HELP_POLL_MENU);
$savant->assign('tmpl_access_key', '');

$savant->assign('tmpl_menu_url', '');	

if ($_SESSION['prefs'][PREF_POLL] == 1){
	if (isset($_POST['poll_submit'], $_POST['choice'])) {
		$poll_id = intval($_POST['poll_id']);

		$sql = "INSERT INTO ".TABLE_PREFIX."polls_members VALUES($poll_id, $_SESSION[member_id])";
		if ($result = mysql_query($sql, $db)) {
			$n = intval($_POST['choice']);

			$sql = "UPDATE ".TABLE_PREFIX."polls SET count$n=count$n+1, total=total+1 WHERE poll_id=$poll_id AND course_id=$_SESSION[course_id]";
			$result = mysql_query($sql, $db);

		}

	}

	ob_start(); 

	if (!isset($include_all, $include_one)) {
		$include_one = ' checked="checked"';
	}

	$sql = "SELECT * FROM ".TABLE_PREFIX."polls WHERE course_id=$_SESSION[course_id] ORDER BY created_date DESC LIMIT 1";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		if (!authenticate(AT_PRIV_POLLS, AT_PRIV_RETURN)) {
			$sql = "SELECT * FROM ".TABLE_PREFIX."polls_members WHERE poll_id=$row[poll_id] AND member_id=$_SESSION[member_id]";
			$result = mysql_query($sql, $db);
		}
		if (authenticate(AT_PRIV_POLLS, AT_PRIV_RETURN) || ($my_row = mysql_fetch_assoc($result))) {
			echo '<tr>';
			echo '<td valign="top" class="dropdown" align="left"><strong>' . AT_print($row['question'], 'polls.question') . '</strong>';
			echo '</td></tr>';

			// we already voted
			for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++) {
				if ($row['choice' . $i]) {
					if ($row['total']) {
						$width = round($row['count' . $i] / $row['total'] * 110);
					} else {
						$width = 0;
					}

					echo '<tr>';
					echo '<td valign="top" class="dropdown"  align="left">';
					echo '<small>' . AT_print($row['choice' . $i], 'polls.choice') . '</small><br />';
					echo '<img src="'.$_base_path . 'images/blue.gif" height="5" width="'.$width.'" alt="" /> '.$row['count' . $i];
					echo '</td></tr>';
				}
			}
		} else {
			// show the form to vote
			echo '<tr>';
			echo '<td valign="top" class="dropdown" align="left"><strong>' . AT_print($row['question'], 'polls.question') . '</strong>';
			echo '<form method="post" action="'.$_SERVER['REQUEST_URI'].'"><input type="hidden" name="poll_id" value="'.$row['poll_id'].'" />';
			echo '</td></tr>';

			for ($i=1; $i<= AT_NUM_POLL_CHOICES; $i++) {
				if ($row['choice' . $i]) {
					echo '<tr>';
					echo '<td valign="top" class="dropdown" align="left">';
					echo '<small><input type="radio" name="choice" value="'.$i.'" id="c'.$i.'" /><label for="c'.$i.'">' . AT_print($row['choice' . $i], 'polls.choice') . '</label></small></td></tr>';
				}
			}

			echo '<tr>';
			echo '<td valign="top" class="dropdown" align="center"><input type="submit" name="poll_submit" value="'._AT('submit').'" class="button" />';
			echo '<br /><small>'._AT('vote_to_see_results').'</small>';
			echo '</form></td>';
		}
		echo '</tr>';

	} else {
		echo '<tr>';
		echo '<td valign="top" class="dropdown" align="left"><small><em>' . _AT('no_polls_found') . '</em></small></td></tr>';
	}


	$savant->assign('tmpl_dropdown_contents', ob_get_contents());
	ob_end_clean();
	$savant->assign('tmpl_close_url', $_my_uri.'disable='.PREF_POLL.SEP.'menu_jump=7');
	$savant->assign('tmpl_dropdown_close', _AT('close_poll'));
	$savant->display('dropdown_open.tmpl.php');

} else {		
	$savant->assign('tmpl_open_url', $_my_uri.'enable='.PREF_POLL.SEP.'menu_jump=7');
	$savant->assign('tmpl_dropdown_open', _AT('open_poll'));
	$savant->display('dropdown_closed.tmpl.php');
}

?>