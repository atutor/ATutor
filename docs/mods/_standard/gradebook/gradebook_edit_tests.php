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
// $Id: grade_scale_add.php 7208 2008-05-28 16:07:24Z cindy $

$page = 'gradebook';

define('AT_INCLUDE_PATH', '../../../include/');
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
		if ($_POST["has_due_date"] == 'true')
		{
			$due_date = $_POST["year_due"]. '-' .str_pad ($_POST["month_due"], 2, "0", STR_PAD_LEFT). '-' .str_pad ($_POST["day_due"], 2, "0", STR_PAD_LEFT). ' '.str_pad ($_POST["hour_due"], 2, "0", STR_PAD_LEFT). ':' .str_pad ($_POST["min_due"], 2, "0", STR_PAD_LEFT) . ':00';
			$sql .= "due_date = '".$due_date. "', ";
		}
		else
			$sql .= "due_date = '', ";
		
		$sql .= "grade_scale_id=".$_POST["selected_grade_scale_id"]." WHERE gradebook_test_id = ". $_REQUEST["gradebook_test_id"];
		$result = mysql_query($sql, $db) or die(mysql_error());

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: gradebook_tests.php');
		exit;
	}
} 

$sql = "SELECT * FROM ".TABLE_PREFIX."gradebook_tests WHERE gradebook_test_id=" . $_REQUEST["gradebook_test_id"];
$result = mysql_query($sql, $db) or die(mysql_error());

$sql = "(SELECT g.gradebook_test_id, g.type, t.title, DATE_FORMAT(end_date, '%Y-%m-%d %H:%i:00') AS due_date, g.grade_scale_id".
				" FROM ".TABLE_PREFIX."gradebook_tests g, ".TABLE_PREFIX."tests t".
				" WHERE g.type='ATutor Test'".
				" AND g.id = t.test_id".
				" AND g.gradebook_test_id=".$_GET['gradebook_test_id'].")".
				" UNION (SELECT g.gradebook_test_id, g.type, a.title, DATE_FORMAT(date_due, '%Y-%m-%d %H:%i:00') AS due_date, g.grade_scale_id".
				" FROM ".TABLE_PREFIX."gradebook_tests g, ".TABLE_PREFIX."assignments a".
				" WHERE g.type='ATutor Assignment'".
				" AND g.id = a.assignment_id".
				" AND g.gradebook_test_id=".$_GET['gradebook_test_id'].")".
				" UNION (SELECT gradebook_test_id, type, title, DATE_FORMAT(due_date, '%Y-%m-%d %H:%i:00') AS due_date,grade_scale_id ".
				" FROM ".TABLE_PREFIX."gradebook_tests".
				" WHERE type='External'".
				" AND gradebook_test_id=".$_GET['gradebook_test_id'].")";
$result	= mysql_query($sql, $db) or die(mysql_error());
$row_this = mysql_fetch_assoc($result);

if ($row_this["type"] == "External")
{
	$array1			= explode (' ', $row_this['due_date'], 2);
	$array_date_due	= explode ('-', $array1[0],3);
	$array_time_due	= explode (':', $array1[1]);
	$today_year		= $array_date_due[0];
	$today_mon		= $array_date_due[1];
	$today_day			= $array_date_due[2];
	$today_hour		= $array_time_due[0];
	$today_min		= $array_time_due[1];

	if ($today_year == '0000')
	{
		$has_due_date = 'false';
		
		// if due date is not set, use today's date as default
		$today = getdate();
		$today_day		= $today['mday'];
		$today_mon	= $today['mon'];
		$today_year	= $today['year'];
		$today_hour	= $today['hours'];
		$today_min	= $today['minutes'];
		// round the minute to the next highest multiple of 5 
		$today_min = round($today_min / '5' ) * '5' + '5';
		if ($today_min > '55')  $today_min = '55';
	} 
	else
		$has_due_date = 'true';
}

if ($has_due_date == 'false') $onload .= ' disable_dates (true, \'_due\');';
require(AT_INCLUDE_PATH.'header.inc.php');

?>
<form method="post" name="form" action="<?php echo $_SERVER['PHP_SELF'].'?gradebook_test_id='.$_REQUEST['gradebook_test_id']; ?>">
<div class="input-form">
	<fieldset class="group_form"><legend class="group_form"><?php echo _AT('edit_tests'); ?></legend>

<?php
if ($row_this["type"] == "External")
{
?>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="title"><?php echo _AT('title'); ?></label><br />
		<input type="text" id="title" size="40" name="title" value="<?php echo $row_this["title"]; ?>" />
	</div>

<?php
}
else
{
?>
	<div class="row">
		<?php echo _AT('title'); ?><br />
		<?php echo $row_this["title"]; ?>
	</div>

<?php
}
?>
	<div class="row">
		<label for="selected_grade_scale_id"><?php echo _AT('grade_scale'); ?></label><br />
<?php 
		print_grade_scale_selectbox($row_this['grade_scale_id']); 
?>
	</div>

	<div class="row">
		<?php  echo _AT('due_date'); ?><br />

<?php 
if ($row_this["type"] == "External")
{
?>
		<input type="radio" name="has_due_date" value="false" id="noduedate"  <?php if ($has_due_date == 'false') { echo 'checked="checked"'; } ?> 
		onfocus="disable_dates (true, '_due');" />
		<label for="noduedate" title="<?php echo _AT('due_date'). ': '. _AT('none');  ?>"><?php echo _AT('none'); ?></label><br />

		<input type="radio" name="has_due_date" value="true" id="hasduedate"  <?php if ($has_due_date == 'true') { echo 'checked="checked"'; } ?> onfocus="disable_dates (false, '_due');" />
		<label for="hasduedate"  title="<?php echo _AT('due_date') ?>"><?php  echo _AT('date'); ?></label>

<?php
		$name = '_due';
		require(AT_INCLUDE_PATH.'html/release_date.inc.php');
	}
	else
	{
		echo $row_this["due_date"];
	}
?>
	</div>

	<div class="row buttons">
		<input type="submit" name="save" value="<?php echo _AT('save'); ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</div>

	</fieldset>

</div>
</form>

<script language="javascript" type="text/javascript">
function disable_dates (state, name) {
	document.form['day' + name].disabled=state;
	document.form['month' + name].disabled=state;
	document.form['year' + name].disabled=state;
	document.form['hour' + name].disabled=state;
	document.form['min' + name].disabled=state;
}
</script>

<?php require (AT_INCLUDE_PATH.'footer.inc.php');  ?>
