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
// $Id: grade_scale.php 7208 2008-05-28 16:07:24Z cindy $

$page = 'gradebook';

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GRADEBOOK);

function print_row($grade_scale_id, $scale_name, $value, $created_date, $post_grade_scale_id, $print_radio_button=true)
{
?>
		<tr onmousedown="document.form['m<?php echo $grade_scale_id; ?>'].checked = true; rowselect(this);" id="r_<?php echo $grade_scale_id; ?>">
<?php
	if ($print_radio_button)
	{
?>
			<td width="10"><input type="radio" name="grade_scale_id" value="<?php echo $grade_scale_id; ?>" id="m<?php echo $grade_scale_id; ?>" <?php if ($grade_scale_id==$post_grade_scale_id) echo 'checked'; ?> /></td>
			<td><label for="m<?php echo $grade_scale_id; ?>"><?php echo $scale_name; ?></label></td>
<?php
	}
	else
	{
?>
			<td><?php echo $scale_name; ?></td>
<?php
	}
?>
			<td><?php echo $value; ?></td>
			<td><?php echo $created_date; ?></td>
		</tr>
<?php
}

if (isset($_POST['remove'], $_POST['grade_scale_id'])) 
{
	header('Location: grade_scale_delete.php?grade_scale_id='.$_POST['grade_scale_id']);
	exit;
} 
else if (isset($_POST['edit'], $_POST['grade_scale_id'])) 
{
	header('Location: grade_scale_edit.php?grade_scale_id='.$_POST['grade_scale_id']);
	exit;
} 
else if (!empty($_POST) && !isset($_POST['grade_scale_id'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 
?>
<div class="toolcontainer">
<form name="form" method="post" action="mods/_standard/gradebook/grade_scale.php">

<h3 align="center"><?php echo _AT('custom_grade_scale'); ?></h3>

<table summary="" class="data" rules="cols" align="center" style="width: 90%;">

<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('name'); ?></th>
	<th scope="col"><?php echo _AT('grade_scale'); ?></th>
	<th scope="col"><?php echo _AT('created_date'); ?></th>
</tr>
</thead>
<tfoot>
<tr>
	<td colspan="5">
		<div class="row buttons">
		<input type="submit" name="edit" value="<?php echo _AT('edit'); ?>" /> 
		<input type="submit" name="remove" value="<?php echo _AT('remove'); ?>" /> 
		</div>
	</td>
</tr>
<tr>
	<td colspan="5"></td>
</tr>
</tfoot>
<tbody>
<?php

$sql = "SELECT g.*, d.* from ".TABLE_PREFIX."grade_scales g, ".TABLE_PREFIX."grade_scales_detail d WHERE g.member_id= ".$_SESSION["member_id"]." AND g.grade_scale_id = d.grade_scale_id ORDER BY g.grade_scale_id, d.percentage_to desc";

$result = mysql_query($sql, $db) or die(mysql_error());

if (mysql_num_rows($result) == 0)
{
?>
	<tr>
		<td colspan="5"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php 
}
else
{
	$prev_row['grade_scale_id'] = 0;
	while ($row = mysql_fetch_assoc($result))
	{
		if ($row['grade_scale_id'] <> $prev_row['grade_scale_id'])
		{
			// print row
			if ($prev_row['grade_scale_id'] <> 0) print_row($prev_row['grade_scale_id'], $prev_row['scale_name'], $whole_scale_value, $prev_row['created_date'], $_POST['grade_scale_id']);

			// initialize next $whole_scale_value
			$whole_scale_value = $row['scale_value'] . ' = ' . $row['percentage_from'] . ' to ' . $row['percentage_to'] . '%';
			$prev_row = $row;
		}
		else
		{
			$whole_scale_value .= '<br />'.$row['scale_value'] . ' = ' . $row['percentage_from'] . ' to ' . $row['percentage_to'] . '%';
		}
	}
	// print last row
	if ($prev_row['grade_scale_id'] <> 0) print_row($prev_row['grade_scale_id'], $prev_row['scale_name'], $whole_scale_value, $prev_row['created_date'], $_POST['grade_scale_id']);
}
?>

</tbody>
</table>
</form>

<h3 align="center"><?php echo _AT('preset_grade_scale'); ?></h3>
<table summary="" class="data" rules="cols" align="center" style="width: 90%;">
<thead>
<tr>
	<th scope="col"><?php echo _AT('name'); ?></th>
	<th scope="col"><?php echo _AT('grade_scale'); ?></th>
	<th scope="col"><?php echo _AT('created_date'); ?></th>
</tr>
</thead>

<tbody>
<?php

// print preset scale table
$sql = "SELECT g.*, d.* from ".TABLE_PREFIX."grade_scales g, ".TABLE_PREFIX."grade_scales_detail d WHERE g.member_id= 0 AND g.grade_scale_id = d.grade_scale_id ORDER BY g.grade_scale_id, d.percentage_to desc";

$result = mysql_query($sql, $db) or die(mysql_error());

if (mysql_num_rows($result) == 0)
{
?>
	<tr>
		<td colspan="5"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php 
}
else
{
	$prev_row['grade_scale_id'] = 0;
	while ($row = mysql_fetch_assoc($result))
	{
		if ($row['grade_scale_id'] <> $prev_row['grade_scale_id'])
		{
			// print row
			if ($prev_row['grade_scale_id'] <> 0) print_row($prev_row['grade_scale_id'], $prev_row['scale_name'], $whole_scale_value, $prev_row['created_date'], $_POST['grade_scale_id'], false);

			// initialize next $whole_scale_value
			$whole_scale_value = $row['scale_value'] . ' = ' . $row['percentage_from'] . ' to ' . $row['percentage_to'] . '%';
			$prev_row = $row;
		}
		else
		{
			$whole_scale_value .= '<br />'.$row['scale_value'] . ' = ' . $row['percentage_from'] . ' to ' . $row['percentage_to'] . '%';
		}
	}
	// print last row
	if ($prev_row['grade_scale_id'] <> 0) print_row($prev_row['grade_scale_id'], $prev_row['scale_name'], $whole_scale_value, $prev_row['created_date'], $_POST['grade_scale_id'], false);
}
?>

</tbody>
</table>
</div>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
