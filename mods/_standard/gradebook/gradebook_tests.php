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

$page = 'gradebook';

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GRADEBOOK);

if (isset($_POST['remove'], $_POST['gradebook_test_id'])) 
{
	header('Location: gradebook_delete_tests.php?gradebook_test_id='.$_POST['gradebook_test_id']);
	exit;
} 
else if (isset($_POST['edit'], $_POST['gradebook_test_id'])) 
{
	header('Location: gradebook_edit_tests.php?gradebook_test_id='.$_POST['gradebook_test_id']);
	exit;
} 
else if (!empty($_POST) && !isset($_POST['gradebook_test_id'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

?>

<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<table summary="" class="data" rules="cols" align="center" style="width: 90%;">

<thead>
<tr>
	<th scope="col">&nbsp;</th>
	<th scope="col"><?php echo _AT('name'); ?></th>
	<th scope="col"><?php echo _AT('grade_scale'); ?></th>
	<th scope="col"><?php echo _AT('type'); ?></th>
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
</tfoot>
<tbody>
<?php

$sql = "(SELECT g.gradebook_test_id, type, t.title, grade_scale_id".
				" FROM ".TABLE_PREFIX."gradebook_tests g, ".TABLE_PREFIX."tests t".
				" WHERE g.type='ATutor Test'".
				" AND g.id = t.test_id".
				" AND t.course_id=".$_SESSION["course_id"].")".
				" UNION (SELECT g.gradebook_test_id, g.type, a.title, grade_scale_id".
				" FROM ".TABLE_PREFIX."gradebook_tests g, ".TABLE_PREFIX."assignments a".
				" WHERE g.type='ATutor Assignment'".
				" AND g.id = a.assignment_id".
				" AND a.course_id=".$_SESSION["course_id"].")".
				" UNION (SELECT gradebook_test_id, type, title, grade_scale_id".
				" FROM ".TABLE_PREFIX."gradebook_tests".
				" WHERE course_id=".$_SESSION["course_id"].")".
				" ORDER BY type, title";
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
	// Initialize scale content array
	$scale_content[0] = _AT("raw_final_score");
	$sql_scale_ids = "SELECT grade_scale_id from ".TABLE_PREFIX."grade_scales g";
	$result_scale_ids = mysql_query($sql_scale_ids, $db) or die(mysql_error());

	while ($row_scale_ids = mysql_fetch_assoc($result_scale_ids))
	{
		$sql_detail = "SELECT * from ".TABLE_PREFIX."grade_scales_detail d WHERE d.grade_scale_id = ".$row_scale_ids["grade_scale_id"]." ORDER BY d.percentage_to desc";
		$result_detail = mysql_query($sql_detail, $db) or die(mysql_error());
		
		$whole_scale_value = "";
		
		while ($row_detail = mysql_fetch_assoc($result_detail))
		{
			$whole_scale_value .= $row_detail['scale_value'] . ' = ' . $row_detail['percentage_from'] . ' to ' . $row_detail['percentage_to'] . '%<br>';
		}
		
		if ($whole_scale_value <> '') $scale_content[$row_scale_ids["grade_scale_id"]] = $whole_scale_value;
	}
	// End of initialize scale content array

	while ($row = mysql_fetch_assoc($result))
	{
?>
		<tr onmousedown="document.form['m<?php echo $row["gradebook_test_id"]; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row["gradebook_test_id"]; ?>">
			<td width="10"><input type="radio" name="gradebook_test_id" value="<?php echo $row["gradebook_test_id"]; ?>" id="m<?php echo $row["gradebook_test_id"]; ?>" <?php if ($row["gradebook_test_id"]==$_POST['gradebook_test_id']) echo 'checked'; ?> /></td>
			<td><label for="m<?php echo $row["gradebook_test_id"]; ?>"><?php echo $row["title"]; ?></label></td>
			<td><?php echo $scale_content[$row["grade_scale_id"]]; ?></td>
			<td><?php echo $row["type"]; ?></td>
		</tr>
<?php 
	}
}
?>

</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
