<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }
global $db;
if ($_SESSION['prefs'][PREF_ONLINE] == 1){
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
	echo '<tr><td class="catd" valign="top">';
	print_popup_help(AT_HELP_USERS_MENU);
	if($_GET['menu_jump']){
		echo '<a name="menu_jump4"></a>';
	}
	echo '<a class="white" href="'.$_my_uri.'disable='.PREF_ONLINE.SEP.'menu_jump=4">';
	echo _AT('close_users_online');
	echo '</a>';
	echo '</td></tr>';
	echo '<tr>';
	echo '<td class="row1" align="left">';

	$sql	= "SELECT * FROM ".TABLE_PREFIX."users_online WHERE course_id=$_SESSION[course_id] AND expiry>".time()." ORDER BY login";
	$result	= mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		do {
			echo '&#176; <a href="'.$_base_path.'send_message.php?l='.$row['member_id'].SEP.'g=1">'.AT_print($row['login'], 'members.login').'</a><br />';
		} while ($row = mysql_fetch_assoc($result));
	} else {
		echo '<small><em>'._AT('none_found').'.</em></small><br />';
	}

	echo '<small><em>'._AT('guests_not_listed').'</em></small>';
	echo '</td></tr></table>';

} else {
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
	echo '<tr><td class="catd" valign="top">';
	print_popup_help(AT_HELP_USERS_MENU);
	if($_GET['menu_jump']){
		echo '<a name="menu_jump4"></a>';
	}
	echo '<a class="white" href="'.$_my_uri.'enable='.PREF_ONLINE.SEP.'menu_jump=4">';
	echo _AT('open_users_online');
	echo '</a>';
	echo '</td></tr></table>';
}

?>