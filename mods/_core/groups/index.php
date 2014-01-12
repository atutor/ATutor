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

define('AT_INCLUDE_PATH', '../../../include/');
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

$sql = "SELECT type_id, title FROM %sgroups_types WHERE course_id=%d ORDER BY title";
$rows_group_type = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));

?>
<div class="input-form">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AT('groups'); ?></legend>
<table class="data" summary="" style="width: 80%">
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
<?php 

if(count($rows_group_type) > 0){
	foreach($rows_group_type as $row){

			$sql = "SELECT group_id, title FROM %sgroups WHERE type_id=%d ORDER BY title";
			$rows_groups = queryDB($sql, array(TABLE_PREFIX, $row['type_id']));
			$num_groups = count($rows_groups);
		?>
		<tr onmousedown="document.form['g<?php echo $row['type_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['type_id']; ?>">
			<th>
				<input type="radio" id="g<?php echo $row['type_id']; ?>" name="id" value="<?php echo $row['type_id']; ?>" />
				<label for="g<?php echo $row['type_id']; ?>"><?php echo AT_print($row['title'], 'groups.title'); ?></label> (<?php echo $num_groups.' '._AT('groups'); ?>)
			</th>
		</tr>
		<?php 
		if($num_groups > 0){
			foreach($rows_groups as $group_row){
					$sql = "SELECT COUNT(*) AS cnt FROM %sgroups_members WHERE group_id=%d";
					$group_cnt = queryDB($sql, array(TABLE_PREFIX, $group_row['group_id']), TRUE);
				?>
				<tr onmousedown="document.form['g<?php echo $row['type_id'].'_'.$group_row['group_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['type_id'].'_'.$group_row['group_id']; ?>">
					<td class="indent"><input type="radio" id="g<?php echo $row['type_id'].'_'.$group_row['group_id']; ?>" name="id" value="<?php echo $row['type_id'].'_'.$group_row['group_id']; ?>" /> <label for="g<?php echo $row['type_id'].'_'.$group_row['group_id']; ?>"><?php echo AT_print($group_row['title'], 'groups.title'); ?></label> (<?php echo $group_cnt['cnt'].' '._AT('members'); ?>)</td>
				</tr>
			<?php }  ?>
		<?php } else { ?>
			<tr>
				<td class="indent"><strong><?php echo _AT('none_found'); ?></strong></td>
			</tr>
		<?php } //endif; 
		?>

	<?php } 
	    ?>
<?php }else{ ?>
	<tr>
		<td><strong><?php echo _AT('none_found'); ?></strong></td>
	</tr>
<?php } ?>
</tbody>
</table>
</fieldset>
</form><br />
</div>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>