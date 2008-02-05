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

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GROUPS);

if (isset($_GET['edit'], $_GET['id'])) {
	$parts = explode('_', $_GET['id'], 2);
	if (isset($parts[1]) && $parts[1]) {
		header('Location: edit_group.php?id='.$parts[1]);
		exit;
	} else if ($parts[0]) {
		header('Location: edit_type.php?id='.$parts[0]);
		exit;
	}
} else if (isset($_GET['delete'], $_GET['id'])) {
	$parts = explode('_', $_GET['id'], 2);
	if (isset($parts[1]) && $parts[1]) {
		header('Location: delete_group.php?id='.$parts[1]);
		exit;
	} else if ($parts[0]) {
		header('Location: delete_type.php?id='.$parts[0]);
		exit;
	}
} else if (isset($_GET['members'])) {
	$parts = explode('_', $_GET['id'], 2);
	if (isset($parts[1]) && $parts[1]) {
		header('Location: members.php?id='.$parts[0].SEP.'gid='.$parts[1]);
		exit;
	} else if ($parts[0]) {
		header('Location: members.php?id='.$parts[0]);
		exit;
	} else {
		$msg->addError('NO_ITEM_SELECTED');
	}
} else if (isset($_GET['members']) || isset($_GET['delete']) || isset($_GET['edit'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php');

$sql = "SELECT type_id, title FROM ".TABLE_PREFIX."groups_types WHERE course_id=$_SESSION[course_id] ORDER BY title";
$result = mysql_query($sql, $db);
?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="form">
<table class="data" summary="" rules="cols" style="width: 50%">
<tfoot>
<tr>
	<td>
		<input type="submit" name="edit"    value="<?php echo _AT('edit'); ?>" />
		<input type="submit" name="members" value="<?php echo _AT('members'); ?>" />
		<input type="submit" name="delete"  value="<?php echo _AT('delete'); ?>" />
	</td>
</tr>
</tfoot>
<tbody>
<?php if ($row = mysql_fetch_assoc($result)): ?>
	<?php do { ?>

		<?php 
			$sql = "SELECT group_id, title FROM ".TABLE_PREFIX."groups WHERE type_id=$row[type_id] ORDER BY title";
			$group_result = mysql_query($sql, $db);
			$num_groups = mysql_num_rows($group_result);
		?>
		<tr onmousedown="document.form['g<?php echo $row['type_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['type_id']; ?>">
			<th>
				<input type="radio" id="g<?php echo $row['type_id']; ?>" name="id" value="<?php echo $row['type_id']; ?>" />
				<label for="g<?php echo $row['type_id']; ?>"><?php echo $row['title']; ?></label> (<?php echo $num_groups.' '._AT('groups'); ?>)</td>
			</th>
		</tr>
		<?php if ($num_groups) : ?>
			<?php while ($group_row = mysql_fetch_assoc($group_result)): ?>
				<?php
					$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."groups_members WHERE group_id=$group_row[group_id]";
					$group_cnt_result = mysql_query($sql, $db);
					$group_cnt = mysql_fetch_assoc($group_cnt_result);
				?>
				<tr onmousedown="document.form['g<?php echo $row['type_id'].'_'.$group_row['group_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['type_id'].'_'.$group_row['group_id']; ?>">
					<td class="indent"><input type="radio" id="g<?php echo $row['type_id'].'_'.$group_row['group_id']; ?>" name="id" value="<?php echo $row['type_id'].'_'.$group_row['group_id']; ?>" /> <label for="g<?php echo $row['type_id'].'_'.$group_row['group_id']; ?>"><?php echo $group_row['title']; ?></label> (<?php echo $group_cnt['cnt'].' '._AT('members'); ?>)</td>
				</tr>
			<?php endwhile; ?>
		<?php else: ?>
			<tr>
				<td class="indent"><em><?php echo _AT('none_found'); ?></em></td>
			</tr>
		<?php endif; ?>

	<?php } while ($row = mysql_fetch_assoc($result)); ?>
<?php else: ?>
	<tr>
		<td><em><?php echo _AT('none_found'); ?></em></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>