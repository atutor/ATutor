<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
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

?>
<form name="form" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="data" summary="" rules="groups" style="width: 90%">
<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('title');       ?></th>
	<th scope="col"><?php echo _AT('description'); ?></th>
	<th scope="col"><?php echo _AT('courses');     ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="4"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<tbody>
<tr>
	<th colspan="4"><?php echo _AT('shared_forums'); ?></th>
</tr>
<?php


	$all_forums    = get_forums(0);
	$num_shared    = count($all_forums['shared']);
	$num_nonshared = count($all_forums['nonshared']);

	if ($num_shared) {
		foreach ($all_forums['shared'] as $forum) {
			echo '<tr onmousedown="document.form[\'f'.$forum['forum_id'].'\'].checked = true; rowselect(this);"  id="r_'.$forum['forum_id'].'">';
			echo '<td><input type="radio" name="id" value="'. $forum['forum_id'].'" id="f'.$forum['forum_id'].'"></td>';
			echo '	<td><label for="f'.$forum['forum_id'].'">' . $forum['title'] . '</label></td>';
			echo '	<td>' . $forum['description'] . '</td>';
			echo '	<td>';

			$courses = array();
			$sql = "SELECT F.course_id FROM ".TABLE_PREFIX."forums_courses F WHERE F.forum_id=$forum[forum_id]";
			$c_result = mysql_query($sql, $db);
			while ($course = mysql_fetch_assoc($c_result)) {
				$courses[] = $system_courses[$course['course_id']]['title'];
			}
			natcasesort($courses);
			echo implode(', ', $courses);
			echo '</td>';
			echo '</tr>';
		}
	} else {
		echo '<tr>';
		echo '	<td colspan="4"><em>' . _AT('no_forums') . '</em></td>';
		echo '</tr>';
	}
?>
</tbody>
<tbody>
	<tr>
		<th colspan="4"><?php echo _AT('unshared_forums'); ?></th>
	</tr>
<?php if ($num_nonshared) : ?>
	<?php foreach ($all_forums['nonshared'] as $forum) : ?>
		<tr onmousedown="document.form['f<?php echo $forum['forum_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $forum['forum_id']; ?>">
			<td><input type="radio" name="id" value="<?php echo $forum['forum_id']; ?>" id="f<?php echo $forum['forum_id']; ?>" /></td>
			<td><label for="f<?php echo $forum['forum_id']; ?>"><?php echo $forum['title']; ?></label></td>
			<td><?php echo $forum['description']; ?></td>
			<td><?php echo $system_courses[$forum['course_id']]['title']; ?></td>
		</tr>
	<?php endforeach; ?>
<?php else: ?>
	<tr>
		<td colspan="4"><em><?php echo _AT('no_forums'); ?></em></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>