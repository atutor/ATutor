<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006                                      */
/* Written by Greg Gay, Joel Kronenberg & Chris Ridpath         */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_READING_LIST);


if (isset($_GET['edit'])) {
	$_GET['resource_id'] = intval($_GET['resource_id']);

	// what kind of resource is user going to edit?
	$sql = "SELECT type FROM ".TABLE_PREFIX."external_resources WHERE course_id=$_SESSION[course_id] AND resource_id=$_GET[resource_id]";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)){
		header('Location: add_resource_'.substr($_rl_types[$row['type']], 3).'.php?id='. $_GET['resource_id']. SEP. 'page_return=display_resources.php');
	}
}
else if (isset($_GET['delete'])) {
	$_GET['resource_id'] = intval($_GET['resource_id']);
	header('Location: delete_resource.php?id='. $_GET['resource_id']);
	exit;
}
else if (isset($_GET['create'])){
	$_GET['new_resource'] = intval($_GET['new_resource']);
	if (isset($_rl_types[$_GET['new_resource']])){
		// Note: the '3' substring is used here to strip out the 'rl_' from the name
		header('Location: add_resource_'.substr($_rl_types[$_GET['new_resource']], 3).'.php'. '?page_return=display_resources.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

$sql = "SELECT title, resource_id FROM ".TABLE_PREFIX."external_resources WHERE course_id=$_SESSION[course_id] AND type=".RL_TYPE_BOOK." ORDER BY title";
$result = mysql_query($sql, $db);
$num_books = mysql_num_rows($result);
?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form" style="width: 30%">
	<div class="row">
		<label for="question"><?php echo _AT('rl_type_of_resource'); ?></label><br />
		<select name="new_resource" class="dropdown" id="type">

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
$sql = "SELECT * FROM ".TABLE_PREFIX."external_resources WHERE course_id=$_SESSION[course_id] ORDER BY type";
$result = mysql_query($sql, $db);
?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="form">
<table class="data" style="width: 90%;">
<thead>
<tr>
	<th>&nbsp;</th>
	<th><?php echo _AT('type'); ?></th>
	<th><?php echo _AT('title'); ?></th>
	<th><?php echo _AT('rl_author'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="6"><input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
				    <input type="submit" name="delete" value="<?php echo _AT('delete'); ?>" /></td>
</tr>
</tfoot>
<?php if ($row = mysql_fetch_assoc($result)) : ?>
<tbody>
		<?php $first=true; // check the first radio button ?>
		<?php do { ?>
			<tr onmousedown="document.form['t<?php echo $row['resource_id']; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row['resource_id']; ?>_0">
				
			<td><input type="radio" id="t<?php echo $row['resource_id'];?>" name="resource_id" value="<?php echo $row['resource_id']; ?>"
			<?php if ($first == true){ echo 'checked="checked"'; $first=false;} ?>/></td>
			<td><?php echo _AT($_rl_types[$row['type']]); ?></td>
			<td><label for="t<?php echo $row['resource_id'];?>"><strong><?php echo $row['title']; ?></strong></label></td>
			<td><?php echo $row['author']; ?></td>
			</tr>
		<?php } while($row = mysql_fetch_assoc($result)); ?>
</tbody>
<?php else: ?>
	<tr>
		<td colspan="2"><em><?php echo _AT('none_found'); ?></em></td>
	</tr>
<?php endif; ?>
</table>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>
