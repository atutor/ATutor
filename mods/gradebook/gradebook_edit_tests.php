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
if (isset($_POST['cancel'])) 
{
	$msg->addFeedback('CANCELLED');
	header('Location: gradebook_tests.php');
	exit;
} 
else if (isset($_POST['save'])) 
{
	$missing_fields = array();

	if (isset($_POST['title']) && $_POST['title'] == '') {
		$missing_fields[] = _AT('title');
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) 
	{
		$sql = "UPDATE ".TABLE_PREFIX."gradebook_tests SET ";
		
		if (isset($_POST["title"])) $sql .= "title = '".$_POST["title"]. "', ";
		if (isset($_POST["due_date"])) $sql .= "due_date = '".$_POST["due_date"]. "', ";
		
		$sql .= "grade_scale_id=".$_POST["selected_grade_scale_id"]." WHERE gradebook_test_id = ". $_REQUEST["gradebook_test_id"];
		$result = mysql_query($sql, $db) or die(mysql_error());

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: gradebook_tests.php');
		exit;
	}
} 

$sql = "SELECT * FROM ".TABLE_PREFIX."gradebook_tests WHERE gradebook_test_id=" . $_REQUEST["gradebook_test_id"];
$result = mysql_query($sql, $db) or die(mysql_error());

require(AT_INCLUDE_PATH.'header.inc.php');

?>
<script type='text/javascript' src='calendar.js'></script>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?gradebook_test_id='.$_REQUEST['gradebook_test_id']; ?>">
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('edit_tests'); ?></legend>

<?php
$sql = "(SELECT g.gradebook_test_id, g.grade_scale_id, t.title, g.due_date, 1 is_atutor_test from ".TABLE_PREFIX."gradebook_tests g, ".TABLE_PREFIX."tests t WHERE g.gradebook_test_id=".$_REQUEST["gradebook_test_id"]." AND g.test_id=t.test_id) UNION (SELECT gradebook_test_id, grade_scale_id, title, due_date, 0 is_atutor_test FROM ".TABLE_PREFIX."gradebook_tests WHERE gradebook_test_id=".$_REQUEST["gradebook_test_id"].")";
//$sql = "SELECT g.*, t.title from ".TABLE_PREFIX."gradebook_tests g LEFT JOIN ".TABLE_PREFIX."tests t ON (g.test_id = t.test_id) WHERE gradebook_test_id=".$_REQUEST["gradebook_test_id"];
$result	= mysql_query($sql, $db) or die(mysql_error());
$row = mysql_fetch_assoc($result);

if ($row["is_atutor_test"])
{
?>
	<div class="row">
		<?php echo _AT('title'); ?><br />
		<?php echo $row["title"]; ?>
	</div>

<?php
}
else
{
?>
	<div class="row">
		<div class="required" title="<?php echo _AT('required_field'); ?>">*</div><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" id="title" size="40" name="title" value="<?php echo $row["title"]; ?>" />
	</div>

<?php
}
?>
	<div class="row">
		<label for="selected_grade_scale_id"><?php echo _AT('grade_scale'); ?></label><br />
<?php 
		print_grade_scale_selectbox($row['grade_scale_id']); 
?>
	</div>

	<div class="row">
		<label for="due_date"><?php echo _AT('due_date'); ?>(YYYY-MM-DD)</label><br />
		<input id='due_date' name='due_date' type='text' value='<?php echo $row["due_date"]?>' />
		<img src='images/calendar.gif' style="vertical-align: middle; cursor: pointer;" onclick="scwShow(scwID('due_date'),event);" />
	</div>

	<div class="row buttons">
		<input type="submit" name="save" value="<?php echo _AT('save'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>

	</fieldset>

</div>
</form>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>
