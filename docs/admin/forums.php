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

$msg->addHelp('SHARED_FORUMS');
$msg->printHelps();

$msg->printAll();
?>

<p align="center"><a href="admin/forum_add.php"><?php echo _AT('add_forum'); ?></a></p>
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
<tr><td height="1" class="row2" colspan="7"></td></tr>
<tr>
	<td colspan="3"><small><strong><?php echo _AT('shared_forums'); ?></strong></small></td>
</tr>
<?php


	$all_forums    = get_forums(0);
	$num_shared    = count($all_forums['shared']);
	$num_nonshared = count($all_forums['nonshared']);

	if ($num_shared) {
		foreach ($all_forums['shared'] as $forum) {
			echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';
			echo '<tr>';
			echo '	<td class="row1">' . $forum['title'] . '</td>';
			echo '	<td class="row1">' . $forum['description'] . '</td>';
			echo '	<td class="row1">';

			$sql = "SELECT F.course_id FROM ".TABLE_PREFIX."forums_courses F WHERE F.forum_id=$forum[forum_id]";
			$c_result = mysql_query($sql, $db);
			while ($course = mysql_fetch_assoc($c_result)) {
				$courses[] = $system_courses[$course['course_id']]['title'];
			}
			natcasesort($courses);
			echo implode(', ', $courses);
			echo '</td>';

			echo '	<td class="row1" nowrap="nowrap"><small><a href="admin/forum_edit.php?forum=' . $forum['forum_id'] . '">' . _AT('edit') . '</a> |';
			echo '	<a href="admin/forum_delete.php?forum=' . $forum['forum_id'] . '">' . _AT('delete') . '</a></small></td>';
			echo '</tr>';
		}
	} else {
		echo '<tr><td height="1" class="row2" colspan="7"></td></tr>';
		echo '<tr>';
		echo '	<td class="row1" colspan="4"><small><em>' . _AT('no_forums') . '</em></small></td>';
		echo '</tr>';
	}
?>
	<tr><td height="1" class="row2" colspan="7"></td></tr>
	<tr>
		<td colspan="4"><small><strong><?php echo _AT('unshared_forums'); ?></strong></small></td>
	</tr>
<?php if ($num_nonshared) : ?>
	<?php foreach ($all_forums['nonshared'] as $forum) : ?>
		<tr><td height="1" class="row2" colspan="7"></td></tr>
		<tr>
			<td class="row1"><?php echo $forum['title']; ?></td>
			<td class="row1"><?php echo $forum['description']; ?></td>
			<td class="row1"><?php echo $system_courses[$forum['course_id']]['title']; ?></td>
			<td class="row1" nowrap="nowrap"><small><a href="admin/forum_edit.php?forum=<?php echo $forum['forum_id']; ?>"><?php echo _AT('edit'); ?></a> | 
			<a href="admin/forum_delete.php?forum=<?php echo $forum['forum_id']; ?>"><?php echo _AT('delete'); ?></a></small></td>
		</tr>
	<?php endforeach; ?>
<?php else: ?>
	<tr><td height="1" class="row2" colspan="7"></td></tr>
	<tr>
		<td class="row1" colspan="4"><small><em><?php echo _AT('no_forums'); ?></em></small></td>
	</tr>
<?php endif; ?>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>