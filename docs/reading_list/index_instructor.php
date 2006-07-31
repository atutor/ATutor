<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006                                      */
/* Written by Greg Gay & Joel Kronenberg & Chris Ridpath        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_READING_LIST);

if (isset($_GET['edit'])) {
	if (!isset($_GET['reading'])) {
		$msg->addError('NO_ITEM_SELECTED');
		header('Location: index_instructor.php');
		exit;
	}

	// reading ID of item that will be edited
	$_GET['reading'] = intval($_GET['reading']);

	// get resource ID of reading
	$sql = "SELECT resource_id FROM ".TABLE_PREFIX."reading_list WHERE course_id=$_SESSION[course_id] AND reading_id=$_GET[reading]";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)){
		// what kind of resource is it? (book, URL, file etc.)
		$sql = "SELECT type FROM ".TABLE_PREFIX."external_resources WHERE course_id=$_SESSION[course_id] AND resource_id=$row[resource_id]";
		$result = mysql_query($sql, $db);
		if ($row = mysql_fetch_assoc($result)){
			// display the correct page for editing the resource
			header('Location: edit_reading_'.substr($_rl_types[$row['type']], 3).'.php?id='. $_GET['reading']);
			exit;
		}
	}
	$msg->addError('RL_READING_NOT_FOUND');
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
<div class="input-form" style="width: 30%">
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
</div>
</form>

<?php
$sql = "SELECT * FROM ".TABLE_PREFIX."reading_list WHERE course_id=$_SESSION[course_id] ORDER BY date_start";
$result = mysql_query($sql, $db);
?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<table class="data" style="width: 90%;">
<thead>
<tr>
	<th>&nbsp;</th>
	<th><?php echo _AT('rl_start'); ?></th>
	<th><?php echo _AT('rl_end'); ?></th>
	<th><?php echo _AT('title'); ?></th>
	<th><?php echo _AT('rl_required'); ?></th>
	<th><?php echo _AT('rl_comment'); ?></th>
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
<?php if ($row = mysql_fetch_assoc($result)): ?>
<tbody>
		<?php do { ?>

			<?php // get the external resource using the resource ID from the reading
			$id = $row['resource_id'];
			$sql = "SELECT title FROM ".TABLE_PREFIX."external_resources WHERE course_id=$_SESSION[course_id] AND resource_id=$id";
			$resource_result = mysql_query($sql, $db);
			if ($resource_row = mysql_fetch_assoc($resource_result)){ 
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
				<td><label for="t<?php echo $row['reading_id'];?>"><strong><?php echo $resource_row['title']; ?></strong></label></td>
				<td><?php echo _AT ($row['required']); ?></td>
				<td><?php echo $row['comment']; ?></td>
				</tr>

			<?php } ?>

		<?php } while($row = mysql_fetch_assoc($result)); ?>
</tbody>
<?php else: ?>
	<tr>
		<td colspan="6"><em><?php echo _AT('none_found'); ?></em></td>
	</tr>
<?php endif; ?>
</table>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>