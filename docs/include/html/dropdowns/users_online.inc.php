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
global $db;
global $_my_uri;
global $_base_path;
global $savant;

$savant->assign('tmpl_popup_help', 'USERS_MENU');
$savant->assign('tmpl_access_key', '');

if ($_GET['menu_jump']) {
	$savant->assign('tmpl_menu_url', '<a name="menu_jump4"></a>');	
} else {
	$savant->assign('tmpl_menu_url', '');	
}

if ($_SESSION['prefs'][PREF_ONLINE] == 1){
	ob_start(); 

	echo '<tr>';
	echo '<td class="dropdown" align="left">';

	$sql	= "SELECT * FROM ".TABLE_PREFIX."users_online WHERE course_id=$_SESSION[course_id] AND expiry>".time()." ORDER BY login";
	$result	= mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		do {
			echo '&#176; <a href="'.$_base_path.'users/send_message.php?l='.$row['member_id'].SEP.'g=1">'.AT_print($row['login'], 'members.login').'</a><br />';
		} while ($row = mysql_fetch_assoc($result));
	} else {
		echo '<small><em>'._AT('none_found').'.</em></small><br />';
	}

	echo '<small><em>'._AT('guests_not_listed').'</em></small>';
	echo '</td></tr>';

	$savant->assign('tmpl_dropdown_contents', ob_get_contents());
	ob_end_clean();
	$savant->assign('tmpl_close_url', $_my_uri.'disable='.PREF_ONLINE.SEP.'menu_jump=4');
	$savant->assign('tmpl_dropdown_close', _AT('close_users_online'));
	$savant->display('dropdown_open.tmpl.php');

} else {		
	$savant->assign('tmpl_open_url', $_my_uri.'enable='.PREF_ONLINE.SEP.'menu_jump=4');
	$savant->assign('tmpl_dropdown_open', _AT('open_users_online'));
	$savant->display('dropdown_closed.tmpl.php');
}

?>