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

$page = 'courses';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if ($_SESSION['course_id'] > -1) { exit; }

require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');
require(AT_INCLUDE_PATH.'lib/forums.inc.php');

global $savant;
$msg =& new Message($savant);

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.$_base_href.'admin/courses.php');
	exit;
} else if (isset($_POST['add_forum'])) {

	//add forum
	$sql	= "INSERT INTO ".TABLE_PREFIX."forums (title, description) VALUES ('" . $_POST['title'] . "','" . $_POST['description'] ."')";
	$result	= mysql_query($sql, $db);
	$forum_id = mysql_insert_id($db);

	//for each course, add an entry to the forums_courses table
	foreach ($_POST['courses'] as $course) {
		$sql	= "INSERT INTO ".TABLE_PREFIX."forums_courses VALUES (" . $forum_id . "," . $course . ")";
		$result	= mysql_query($sql, $db);
	}

	$msg->addFeedback('FORUM_CREATED');
	header('Location: '.$_base_href.'admin/courses.php');
	exit;	
}

require(AT_INCLUDE_PATH.'header.inc.php'); 
echo '<h3>'._AT('shared_forums').'</h3><br />';

$msg->printAll();
?>

<p align="center"><a href="admin/create_forum.php"><?php echo _AT('create_forum'); ?></a></p>
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="95%" align="center">
<tr>
	<th colspan="8" class="cyan"><?php echo _AT('forums'); ?></th>
</tr>
<tr>
	<th scope="col" class="cat"><?php echo _AT('title'); ?></th>
	<th scope="col" class="cat"><?php echo _AT('description'); ?></th>
	<th scope="col" class="cat"><?php echo _AT('courses'); ?></th>
	<th scope="col" class="cat" width="1%"></th>
</tr>

<?php

	//go through forums_courses for shared
	$sql = "SELECT * FROM ".TABLE_PREFIX."forums_courses HAVING count(*) > 1 ORDER BY course_id";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$shared[] = $row;
	}

	//go through each course
	$sql = "SELECT course_id, title FROM ".TABLE_PREFIX."courses ORDER BY title";
	$result = mysql_query($sql, $db);

	while ($course = mysql_fetch_assoc($result)) {

		//get its forums - output the non-shared courses
		if ($forums = get_forums($course['course_id'])) {
			foreach ($forums as $forum) {
				if (in_array($forum['forum_id'],$shared)) {
					echo '<tr>';
					echo '	<td class="row1">' . _AT('shared_forums') . '</td>';
					echo '</tr>';
					echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';
				} else {
					echo '<tr>';
					echo '	<td class="row1">' . $forum['title'] . '</td>';
					echo '	<td class="row1">' . $forum['description'] . '</td>';
					echo '	<td class="row1">' . $course['title'] . '</td>';
					echo '	<td class="row1" nowrap="nowrap"><small><a href="edit_forum.php?f=' . $forum['forum_id'] . '">' . _AT('edit') . '</a> |';
					echo '	<a href="delete_forum.php?f=' . $forum['forum_id'] . '">' . _AT('delete') . '</a></small></td>';
					echo '</tr>';
					echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';
				}
			}
		}
	}

?>

</table>

<? require(AT_INCLUDE_PATH.'footer.inc.php'); ?>