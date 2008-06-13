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
// $Id: grade_scale_add.php 7208 2008-05-28 16:07:24Z cindy $

$page = 'gradebook';

define('AT_INCLUDE_PATH', '../../include/');
require_once(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_GRADEBOOK);

require_once("lib/gradebook.inc.php");

// Checks if the given test has students taken it more than once, if has, don't add
// print feedback, otherwise, add this test into gradebook.
function add_test($test_id)
{
	global $db;
	
	$no_error = true;
	
	$studs_take_num = get_studs_take_more_than_once($_SESSION["course_id"], $test_id);
	
	foreach ($studs_take_num as $student => $num)
	{
			if ($no_error) $no_error = false;
			
			$f = array('ADD_TEST_INTO_GRADEBOOK',
							$row['title'], 
							$student . ": " . $num . " times");
			$msg->addFeedback($f);
	}
	
	if ($no_error)  // add into gradebook
	{
		$sql_insert = "INSERT INTO ".TABLE_PREFIX."gradebook_tests (test_id, grade_scale_id)
		               VALUES (". $test_id. ", ".$_POST["selected_grade_scale_id"].")";
		$result_insert = mysql_query($sql_insert, $db) or die(mysql_error());
	}
}

if (isset($_POST['cancel'])) 
{
	$msg->addFeedback('CANCELLED');
	header('Location: gradebook_tests.php');
	exit;
} 
else if (isset($_POST['addATutorTest'])) 
{
	if ($_POST['test_id'] == -1)
	{
		$msg->addError('NO_ITEM_SELECTED');
	}
	else if ($_POST['test_id'] == 0) // add all applicable tests
	{
		$sql = "SELECT * FROM ".TABLE_PREFIX."tests t WHERE course_id=".$_SESSION["course_id"]." AND num_takes = 1 AND NOT EXISTS (SELECT 1 FROM ".TABLE_PREFIX."gradebook_tests g WHERE g.test_id = t.test_id)";
		$result	= mysql_query($sql, $db) or die(mysql_error());
		
		while ($row = mysql_fetch_assoc($result))
		{
			add_test($row["test_id"]);
		}
	}
	else // add one test_id
	{
		add_test($_POST["test_id"]);
	}

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: gradebook_tests.php');
	exit;
} 
else if (isset($_POST['addExternalTest'])) 
{
	$missing_fields = array();

	if ($_POST['title'] == '') {
		$missing_fields[] = _AT('title');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) 
	{
		$sql_insert = "INSERT INTO ".TABLE_PREFIX."gradebook_tests (course_id, title, due_date, grade_scale_id)
		               VALUES (".$_SESSION["course_id"].", '". $_POST["title"]. "', '".$_POST["due_date"] . "', ".$_POST["selected_grade_scale_id"].")";
		$result_insert = mysql_query($sql_insert, $db) or die(mysql_error());

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: gradebook_tests.php');
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

?>
<script type='text/javascript' src='calendar.js'></script>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('add_atutor_test'); ?></legend>

	<div class="row">
		<p><?php echo _AT('add_atutor_test_info'); ?></p>
	</div>

<?php
$sql = "SELECT * FROM ".TABLE_PREFIX."tests t WHERE course_id=".$_SESSION["course_id"]." AND num_takes = 1 AND NOT EXISTS (SELECT 1 FROM ".TABLE_PREFIX."gradebook_tests g WHERE g.test_id = t.test_id)";
$result	= mysql_query($sql, $db) or die(mysql_error());

if (mysql_num_rows($result) == 0)
{
	 echo _AT('none_found');
}
else
{
	echo '	<div class="row">'."\n\r";
	echo '		<label for="select_tid">'. _AT("name") .'</label><br />'."\n\r";
	echo '		<select name="test_id" id="select_tid">'."\n\r";
	echo '			<option value="0">'. _AT('all_atutor_tests') .'</option>'."\n\r";
	echo '			<option value="-1"></option>."\n\r"';

	while ($row = mysql_fetch_assoc($result))
	{
		echo '			<option value="'.$row[test_id].'">'.$row[title].'</option>'."\n\r";
	}
	echo '		</select>'."\n\r";
	echo '	</div>'."\n\r";

?>
	<div class="row">
		<label for="selected_grade_scale_id"><?php echo _AT('grade_scale'); ?></label><br />
		<?php print_grade_scale_selectbox($_POST["selected_grade_scale_id"]); ?>
	</div>

	<div class="row buttons">
		<input type="submit" name="addATutorTest" value="<?php echo _AT('add'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>
<?php
}
?>
	</fieldset>

</div>
</form>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('add_external_test'); ?></legend>

	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" id="title" size="30" value="<?php echo $_POST['title']; ?>" />
	</div>

	<div class="row">
		<label for="selected_grade_scale_id"><?php echo _AT('grade_scale'); ?></label><br />
		<?php print_grade_scale_selectbox($_POST["selected_grade_scale_id"]); ?>
	</div>

	<div class="row">
		<label for="due_date"><?php echo _AT('due_date'); ?>(YYYY-MM-DD)</label><br />
		<input id='due_date' name='due_date' type='text' value='<?php echo $_POST[due_date]; ?>' />
		<img src='images/calendar.gif' style="vertical-align: middle; cursor: pointer;" onclick="scwShow(scwID('due_date'),event);" />
	</div>

	<div class="row buttons">
		<input type="submit" name="addExternalTest" value="<?php echo _AT('add'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>

	</fieldset>

</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>
