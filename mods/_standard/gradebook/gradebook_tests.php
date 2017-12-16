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

<table summary="" class="data" align="center" style="width: 90%;">

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
				" FROM %sgradebook_tests g, %stests t".
				" WHERE g.type='ATutor Test'".
				" AND g.id = t.test_id".
				" AND t.course_id=%d)".
				" UNION (SELECT g.gradebook_test_id, g.type, a.title, grade_scale_id".
				" FROM %sgradebook_tests g, %sassignments a".
				" WHERE g.type='ATutor Assignment'".
				" AND g.id = a.assignment_id".
				" AND a.course_id=%d)".
				" UNION (SELECT gradebook_test_id, type, title, grade_scale_id".
				" FROM %sgradebook_tests".
				" WHERE course_id=%d)".
				" ORDER BY type, title";
$row_grades = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION["course_id"], TABLE_PREFIX, TABLE_PREFIX, $_SESSION["course_id"], TABLE_PREFIX, $_SESSION["course_id"]));

if(count($row_grades) == 0){
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

	$sql_scale_ids = "SELECT grade_scale_id from %sgrade_scales g";
	$rows_scale_ids = queryDB($sql_scale_ids, array(TABLE_PREFIX));

    foreach($rows_scale_ids as $row_scale_ids){

		$sql_detail = "SELECT * from %sgrade_scales_detail d WHERE d.grade_scale_id = %d ORDER BY d.percentage_to desc";
		$rows_detail = queryDB($sql_detail, array(TABLE_PREFIX, $row_scale_ids["grade_scale_id"]));
		
		$whole_scale_value = "";
		
		foreach($rows_detail as $row_detail){
			$whole_scale_value .= $row_detail['scale_value'] . ' = ' . $row_detail['percentage_from'] . ' to ' . $row_detail['percentage_to'] . '%<br>';
		}
		
		if ($whole_scale_value <> '') $scale_content[$row_scale_ids["grade_scale_id"]] = $whole_scale_value;
	}
	// End of initialize scale content array
    foreach($row_grades as $row){
?>
		<tr onmousedown="document.form['m<?php echo $row["gradebook_test_id"]; ?>'].checked = true; rowselect(this);" id="r_<?php echo $row["gradebook_test_id"]; ?>">
			<td width="10"><input type="radio" name="gradebook_test_id" value="<?php echo $row["gradebook_test_id"]; ?>" id="m<?php echo $row["gradebook_test_id"]; ?>" <?php if ($row["gradebook_test_id"]==$_POST['gradebook_test_id']) echo 'checked'; ?> /></td>
			<td><label for="m<?php echo $row["gradebook_test_id"]; ?>"><?php echo htmlspecialchars_decode(stripslashes($row["title"])); ?></label></td>
			<td><?php echo htmlspecialchars_decode(stripslashes($scale_content[$row["grade_scale_id"]])); ?></td>
			<?php if ($row["type"] == "External"){?>
				<td><?php echo _AT("external"); ?></td>
			<?php } elseif($row["type"] == "ATutor Test"){ ?>
				<td><?php echo _AT("atutor_test"); ?></td>
			<?php } else if($row["type"] == "ATutor Assignment") { ?>
				<td><?php echo _AT("atutor_assignment"); ?></td>
			<?php } ?>
			
		</tr>
<?php 
	}
}
?>

</tbody>
</table>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>
