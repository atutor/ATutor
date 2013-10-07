<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008                                      */
/* Written by Greg Gay & Joel Kronenberg & Chris Ridpath        */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_READING_LIST);
tool_origin();
if (isset($_GET['edit'])) {

	if (!isset($_GET['reading'])) {
		$msg->addError('NO_ITEM_SELECTED');
		header('Location: index_instructor.php');
		exit;
	}

	// get resource ID of reading
	$sql = "SELECT resource_id FROM %sreading_list WHERE course_id=%d AND reading_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $_GET['reading']), TRUE);
	
	if(count($row) > 0){
		// what kind of resource is it? (book, URL, file etc.)

		$sql = "SELECT type FROM %sexternal_resources WHERE course_id=%d AND resource_id=%d";
		$row2 = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $row['resource_id']), TRUE);

		if(count($row2) > 0){
			// display the correct page for editing the resource
			header('Location: edit_reading_'.substr($_rl_types[$row2['type']], 3).'.php?id='. $_GET['reading']);
			exit;
		}
	}
	$msg->addError('ITEM_NOT_FOUND');
} else if (isset($_GET['delete'])) {
	if (!isset($_GET['reading'])) {
		$msg->addError('NO_ITEM_SELECTED');
		header('Location: index_instructor.php');
		exit;
	}
	$_GET['reading'] = intval($_GET['reading']);
	header('Location: delete_reading.php?id='. $_GET['reading']);
	exit;
} else if (isset($_GET['create'])){
	$_GET['new_reading'] = intval($_GET['new_reading']);

	if (isset($_rl_types[$_GET['new_reading']])){
		// Note: the '3' substring is used here to strip out the 'rl_' from the name
		header('Location: new_reading_'.substr($_rl_types[$_GET['new_reading']], 3).'.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php'); 
?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form" style="width: 90%">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('create'); ?></legend>
	<div class="row">
		<label for="question"><?php echo _AT('rl_type_of_reading'); ?></label><br />
		<select name="new_reading" class="dropdown" id="type">

		<?php foreach ($_rl_types as $key => $value): ?>
			<option value="<?php echo $key; ?>"><?php echo _AT($value); ?></option>
		<?php endforeach; ?>

		</select>
	</div>
	<div class="row buttons">
		<input type="submit" name="create" value="<?php echo _AT('create'); ?>" />
	</div>
	</fieldset>
</div>
</form>

<?php

$sql = "SELECT * FROM %sreading_list WHERE course_id=%d ORDER BY date_start";
$rows_rlists = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));

?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<table class="data" style="width: 90%;">
<thead>
<tr>
	<th>&nbsp;</th>
	<th><?php echo _AT('rl_start'); ?></th>
	<th><?php echo _AT('rl_end'); ?></th>
	<th><?php echo _AT('title'); ?></th>
	<th><?php echo _AT('required'); ?></th>
	<th><?php echo _AT('comment'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="6">
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
		<input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" />
	</td>
</tr>
</tfoot>
<?php 
    if(count($rows_rlists) > 0):
?>
<tbody>
		<?php 
		    foreach($rows_rlists as $row){ 
		?>

			<?php // get the external resource using the resource ID from the reading
			$id = $row['resource_id'];
			$sql = "SELECT title FROM %sexternal_resources WHERE course_id=%d AND resource_id=%d";
			$resource_row = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $id), TRUE);
			
			if(count($resource_row) > 0){
			?>
				<tr onmousedown="document.form['t<?php echo $row['reading_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['reading_id']; ?>_0">
				
				<td><input type="radio" id="t<?php echo $row['reading_id']; ?>" name="reading" value="<?php echo $row['reading_id']; ?>" 

				<?php // set first item as checked if nothing selected
				if (isset($_GET['reading'])){
					if ($_GET['reading'] == $row['reading_id']){ 
						echo ' checked="checked"'; 
					} 
				} else {
					echo ' checked="checked"';
					$_GET['reading'] = $row['reading_id'];
				}
				?> /></td>
				<td><?php  if ($row['date_start'] == '0000-00-00'){
					echo _AT ('none');
				}else {
					echo AT_Date(_AT('rl_date_format'), $row['date_start'], AT_DATE_MYSQL_DATETIME);
				}?></td>

				<td><?php  if ($row['date_end'] == '0000-00-00'){
					echo _AT ('none');
				}else {
					echo AT_Date(_AT('rl_date_format'), $row['date_end'], AT_DATE_MYSQL_DATETIME);
				}?></td>
				<td><label for="t<?php echo $row['reading_id'];?>"><strong><?php echo AT_print($resource_row['title'], 'reading_list.title'); ?></strong></label></td>
				<td><?php echo _AT ($row['required']); ?></td>
				<td><?php echo AT_print($row['comment'], 'reading_list.comment'); ?></td>
				</tr>

			<?php } ?>

		<?php } ?>
</tbody>
<?php else: ?>
	<tr>
		<td colspan="6"><strong><?php echo _AT('none_found'); ?></strong></td>
	</tr>
<?php endif; ?>
</table>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>