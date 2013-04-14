<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_FORUMS);

if (isset($_GET['edit'], $_GET['id'])) {
	header('Location: forum_edit.php?forum='.$_GET['id']);
	exit;
} else if (isset($_GET['delete'], $_GET['id'])) {
	header('Location: forum_delete.php?forum='.$_GET['id']);
	exit;
} else if (isset($_GET['delete']) || isset($_GET['edit'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

include(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');

require(AT_INCLUDE_PATH.'header.inc.php'); 



	$all_forums    = get_forums(0);
	$num_shared    = count($all_forums['shared']);
	$num_nonshared = count($all_forums['nonshared']);

	$shared_forums = array();
	$i = 0;
		
	if ($num_shared) {
		foreach ($all_forums['shared'] as $forum) {
			$shared_forums[$i]["id"] = $forum['forum_id'];
			$shared_forums[$i]["title"] = AT_print($forum['title'], 'forums.title');   
			$shared_forums[$i]["desc"] = AT_print($forum['description'], 'forums.description');

			$courses = array();//create an empty array
			$sql = "SELECT F.course_id FROM ".TABLE_PREFIX."forums_courses F WHERE F.forum_id=$forum[forum_id]";
			$c_result = mysql_query($sql, $db);
			while ($course = mysql_fetch_assoc($c_result)) {
				$courses[] = $system_courses[$course['course_id']]['title'];
			}
			natcasesort($courses);
			$shared_forums[$i]["courses"] = $courses;
			
			$i++;

		}
	} else {
		echo '<tr>';
		echo '	<td colspan="4"><strong>' . _AT('no_forums') . '</strong></td>';
		echo '</tr>';
	}

$nonshared_forums = array();
$i = 0;

if ($num_nonshared) {
	foreach ($all_forums['nonshared'] as $forum) {
		$nonshared_forums[$i]["forum_id"] = $forum['forum_id'];
		$nonshared_forums[$i]["title"] = AT_print($forum['title'], 'forums.title');
		$nonshared_forums[$i]["desc"] = AT_print($forum['description'], 'forums.description');
		
		$i++;
	}
}

$savant->assign('system_courses', $system_courses);
$savant->assign('num_nonshared', $num_nonshared);
$savant->assign('courses', $courses);
$savant->assign('shared_forums', $shared_forums);
$savant->assign('nonshared_forums', $nonshared_forums);
$savant->assign('all_forums', $all_forums);
$savant->display('admin/courses/forums.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>