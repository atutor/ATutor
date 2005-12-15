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

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ENROLLMENT);

/* make sure we own this course that we're approving for! */
$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id] AND member_id=$_SESSION[member_id]";
$result	= mysql_query($sql, $db);

if (!($result) || !authenticate(AT_PRIV_ENROLLMENT, AT_PRIV_RETURN)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('NOT_OWNER');
	require (AT_INCLUDE_PATH.'footer.inc.php'); 
	exit;
}

if (isset($_POST['edit'])) {
	if ($_POST['group']) {
		header('Location: groups_manage.php?gid='.$_POST['group']);
		exit;
	} else {
		$msg->addError('GROUP_NOT_FOUND');
	}

} else if (isset($_POST['delete'])) {
	if (isset($_POST['group'])) {
		//confirm
		header('Location: groups_delete.php?gid='.$_POST['group']);
		exit;

	} else {
		$msg->addError('GROUP_NOT_FOUND');
	}	
} else if (isset ($_POST['members'])) {
	if (isset($_POST['group'])) {
		header('Location: groups_members.php?gid='.$_POST['group']);
		exit;
	} else {
		$msg->addError('GROUP_NOT_FOUND');
	}	
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<table class="data" summary="" rules="cols">
<thead>
<tr>
	<th scope="col"><?php echo _AT('groups');         ?></th>
</tr>
</thead>

<tbody>
<?php
	$sql	= "SELECT * FROM ".TABLE_PREFIX."groups WHERE course_id=$_SESSION[course_id] ORDER BY title";
	$result	= mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		do {
?>
			<tr onmousedown="document.form['g_<?php echo $row['group_id']; ?>'].checked = true;">
				<td>
					<input type="radio" id="g_<?php echo $row['group_id']; ?>" name="group" value="<?php echo $row['group_id']; ?>" />
					<label for="g_<?php echo $row['group_id']; ?>"><?php echo $row['title']; ?></label>
				</td>
			</tr>
<?php	} while ($row = mysql_fetch_assoc($result)); ?>

		<tfoot>
		<tr>
			<td colspan="6">
				<input type="submit" name="members" value="<?php echo _AT('members'); ?>" />
				<input type="submit" name="edit"   value="<?php echo _AT('edit'); ?>" />
				<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
			</td>
		</tr>
		</tfoot>

<?php	
	} else {?>
		<tr>
			<td><?php echo _AT('none_found'); ?></td>
		</tr>
<?php } ?>
</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
