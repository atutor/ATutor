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

require(AT_INCLUDE_PATH.'header.inc.php'); 
echo '<h3>'._AT('forums').'</h3><br />';

$msg->printAll();
?>

<p align="center"><a href="admin/create_forum.php"><?php echo _AT('add_forum'); ?></a></p>
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
	echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';
	echo '<tr>';
	echo '	<td colspan="3"><small><strong>' . _AT('shared_forums') . '</strong></small></td>';
	echo '</tr>';
	echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';


	//get shared forums 
	$sql = "SELECT * FROM ".TABLE_PREFIX."forums_courses GROUP BY forum_id HAVING count(*) > 1 ORDER BY course_id";
	$result = mysql_query($sql, $db);

	while ($row = mysql_fetch_assoc($result)) {
		$shared[]	= $row['forum_id'];
		$forum		= get_forum($row['forum_id']); 
		echo '<tr>';
		echo '	<td class="row1">' . $forum['title'] . '</td>';
		echo '	<td class="row1">' . $forum['description'] . '</td>';

		$sql = "SELECT C.title FROM ".TABLE_PREFIX."forums_courses F, ".TABLE_PREFIX."courses C WHERE F.forum_id=$row[forum_id] AND F.course_id=C.course_id ORDER BY C.title";
		$c_result = mysql_query($sql, $db);
		echo '	<td class="row1">';
		$courses = '';
		while ($course = mysql_fetch_assoc($c_result)) {
			$courses .= $course['title'].", ";
		}
		echo substr($courses, 0, -2);

		echo '</td>';

		echo '	<td class="row1" nowrap="nowrap"><small><a href="admin/edit_forum.php?forum=' . $forum['forum_id'] . '">' . _AT('edit') . '</a> |';
		echo '	<a href="admin/delete_forum.php?forum=' . $forum['forum_id'] . '">' . _AT('delete') . '</a></small></td>';
		echo '</tr>';
		echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';
	}
	
	echo '<tr>';
	echo '	<td colspan="3"><small><strong>' . _AT('unshared_forums') . '</strong></small></td>';
	echo '</tr>';
	echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';

	//go through each course
	$sql = "SELECT course_id, title FROM ".TABLE_PREFIX."courses ORDER BY title";
	$result = mysql_query($sql, $db);

	while ($course = mysql_fetch_assoc($result)) {

		//get its forums - output the non-shared courses
		if ($forums = get_forums($course['course_id'])) {
			foreach ($forums as $forum) {
				if (!in_array($forum['forum_id'],$shared)) {
					echo '<tr>';
					echo '	<td class="row1">' . $forum['title'] . '</td>';
					echo '	<td class="row1">' . $forum['description'] . '</td>';
					echo '	<td class="row1">' . $course['title'] . '</td>';
					echo '	<td class="row1" nowrap="nowrap"><small><a href="admin/edit_forum.php?forum=' . $forum['forum_id'] . '">' . _AT('edit') . '</a> |';
					echo '	<a href="admin/delete_forum.php?forum=' . $forum['forum_id'] . '">' . _AT('delete') . '</a></small></td>';
					echo '</tr>';
					echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';
				}
			}
		}
	}

?>

</table>

<? require(AT_INCLUDE_PATH.'footer.inc.php'); ?>