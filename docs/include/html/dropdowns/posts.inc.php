<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
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

$savant->assign('tmpl_popup_help', 'USERS_POSTS');
$savant->assign('tmpl_access_key', '');

if ($_GET['menu_jump']) {
	$savant->assign('tmpl_menu_url', '<a name="menu_jump4"></a>');	
} else {
	$savant->assign('tmpl_menu_url', '');	
}

if ($_SESSION['prefs'][PREF_POSTS] == 1) {

	//Number of posts to display
	$post_limit = 8;
	
	ob_start(); 
	
	$sql = "SELECT T.login, T.subject, T.post_id, T.forum_id, F.title FROM ".TABLE_PREFIX."forums_threads T, ".TABLE_PREFIX."forums_courses FC, ".TABLE_PREFIX."forums F WHERE FC.course_id=". $_SESSION['course_id']." AND T.forum_id=FC.forum_id AND T.forum_id=F.forum_id AND T.parent_id=0 ORDER BY T.last_comment DESC LIMIT $post_limit";

	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		do {
			echo '&#176; <a href="'.$_base_path.'forum/view.php?fid='.$row['forum_id'].SEP.'pid='.$row['post_id'].'" title="'.$row['title'].': '.$row['subject'].': '.$row['login'].'">'.AT_print($row['subject'], 'forums_threads.subject').'</a><br />';
		} while ($row = mysql_fetch_assoc($result));
	} else {
		echo '<small><em>'._AT('none_found').'.</em></small><br />';
	}

	$savant->assign('tmpl_dropdown_contents', ob_get_contents());
	ob_end_clean();
	$savant->assign('tmpl_close_url', $_my_uri.'disable='.PREF_POSTS.SEP.'menu_jump=4');
	$savant->assign('tmpl_dropdown_close', _AT('close_forum_posts'));
	$savant->display('dropdown_open.tmpl.php');

} else {		
	$savant->assign('tmpl_open_url', $_my_uri.'enable='.PREF_POSTS.SEP.'menu_jump=4');
	$savant->assign('tmpl_dropdown_open', _AT('open_forum_posts'));
	$savant->display('dropdown_closed.tmpl.php');
}

?>
