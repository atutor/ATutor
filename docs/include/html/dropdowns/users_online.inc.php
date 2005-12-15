<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $db;
global $_my_uri;
global $_base_path;
global $savant;

ob_start(); 

$sql	= "SELECT * FROM ".TABLE_PREFIX."users_online WHERE course_id=$_SESSION[course_id] AND expiry>".time()." ORDER BY login";
$result	= mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)) {
	do {
		echo '&#176; <a href="'.$_base_path.'profile.php?id='.$row['member_id'].'">'.AT_print($row['login'], 'members.login').'</a><br />';
	} while ($row = mysql_fetch_assoc($result));
} else {
	echo '<em>'._AT('none_found').'</em><br />';
}

echo '<em>'._AT('guests_not_listed').'</em>';

$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();
$savant->assign('title', _AT('users_online'));
$savant->display('include/box.tmpl.php');
?>