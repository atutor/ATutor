<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: posts.inc.php 10540 2010-12-16 19:35:58Z hwong $

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $db;
global $_base_path;
global $savant;

//Number of posts to display
$post_limit = 5;
	
ob_start();

// global $_course_id is set when a guest accessing a public course. 
// This is to solve the issue that the google indexing fails as the session vars are lost.
global $_course_id;
if (isset($_SESSION['course_id'])) $_course_id = $_SESSION['course_id'];

$forum_list = get_group_concat('forums_courses', 'forum_id', "course_id={$_course_id}");
if ($forum_list != 0) {
	$sql = "SELECT subject, post_id, forum_id, member_id FROM ".TABLE_PREFIX."forums_threads WHERE parent_id=0 AND forum_id IN ($forum_list) ORDER BY last_comment DESC LIMIT $post_limit";
	$result = mysql_query($sql, $db);

	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)) {
			echo '&#176; <a href="' . $_base_path.url_rewrite('mods/_standard/forums/forum/view.php?fid=' . $row['forum_id'] . SEP . 'pid=' . $row['post_id']) . '" title="' . AT_print($row['subject'], 'forums_threads.subject') . ': ' . htmlspecialchars(get_display_name($row['member_id'])) . '">' . AT_print(validate_length($row['subject'], 20, VALIDATE_LENGTH_FOR_DISPLAY), 'forums_threads.subject') . '</a><br />';
		}
	} else {
		echo '<strong>'._AT('none_found').'</strong>';
	}
} else {
	echo '<strong>'._AT('none_found').'</strong>';
}

$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('forum_posts'));
$savant->display('include/box.tmpl.php');
?>