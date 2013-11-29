<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
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
authenticate(AT_PRIV_ASSIGNMENTS);
tool_origin();
// initial values for controls
$id = 0;
$today = getdate();

// Are we editing an existing assignment or creating a new assignment?
if (isset ($_GET['id'])){
	// editing an existing assignment
	$id = intval($_GET['id']); 

	$sql = "SELECT *, DATE_FORMAT(date_due, '%%Y-%%m-%%d %%H:%%i:00') AS date_due, DATE_FORMAT(date_cutoff, '%%Y-%%m-%%d %%H:%%i:00') AS date_cutoff FROM %sassignments WHERE course_id=%d AND assignment_id=%d";
	$row_assignments = queryDB($sql,array(TABLE_PREFIX, $_SESSION['course_id'], $id), TRUE);

	if(count($row_assignments) == 0){
		// should not happen
		$msg->addFeedback('ASSIGNMENT_NOT_FOUND');
        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
		exit;
	}

	// get values of existing assignment from database
	$title			= $row_assignments['title'];
	$assign_to		= $row_assignments['assign_to'];
	$multi_submit	= $row_assignments['multi_submit'];

	$array1			= explode (' ', $row_assignments['date_due'], 2);
	$array_date_due	= explode ('-', $array1[0],3);
	$array_time_due	= explode (':', $array1[1]);
	$dueyear		= $array_date_due[0];
	$duemonth		= $array_date_due[1];
	$dueday			= $array_date_due[2];
	$duehour		= $array_time_due[0];
	$dueminute		= $array_time_due[1];

	if ($dueyear == '0000'){
		$has_due_date = 'false';
	} else {
		$has_due_date = 'true';
	}

	// use date from database
	$array2 = explode (' ', $row_assignments['date_cutoff'], 2);
	$array_date_cutoff = explode ('-', $array2[0],3);
	$array_time_cutoff = explode (':', $array2[1]);
	$cutoffyear		= $array_date_cutoff[0];
	$cutoffmonth	= $array_date_cutoff[1];
	$cutoffday		= $array_date_cutoff[2];
	$cutoffhour		= $array_time_cutoff[0];
	$cutoffminute	= $array_time_cutoff[1];

	if ($cutoffyear == '0000'){
		$late_submit	= '0'; // allow late submissions always
	} else if ($row_assignments['date_cutoff'] == $row_assignments['date_due']){
		$late_submit	= '1'; // allow late submissions never
		// use today's date as default
		$cutoffday		= $today['mday'];
		$cutoffmonth	= $today['mon'];
		$cutoffyear		= $today['year'];
		$cutoffhour		= $today['hours'];
		$cutoffminute	= $today['minutes'];
		// round the minute to the next highest multiple of 5 
		$cutoffminute = round($cutoffminute / '5' ) * '5' + '5';
		if ($cutoffminute > '55'){ $cutoffminute = '55'; }
	} else {
		$late_submit	= '2'; // allow late submissions until (date)
	}
}
else if (isset($_POST['cancel'])) {
	// cancel, nothing happened
	$msg->addFeedback('CANCELLED');
        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
		exit;
}
else if (isset($_POST['submit'])) {
	// user has submitted form to update database
	$id = intval ($_POST['id']);

	if ($_POST['multi_submit'] == 'on'){
		$multi_submit = '1';
	}

	// get values from form that was just submitted
	$title			= $addslashes($_POST['title']);
	$assign_to		= intval($_POST['assign_to']);
	$has_due_date	= $addslashes($_POST['has_due_date']);
	$late_submit	= intval($_POST['late_submit']);

	$dueday			= intval($_POST['day_due']);
	$duemonth		= intval($_POST['month_due']);
	$dueyear		= intval($_POST['year_due']);
	$duehour		= intval($_POST['hour_due']);
	$dueminute		= intval($_POST['min_due']);

	$cutoffday		= intval($_POST['day_cutoff']);
	$cutoffmonth	= intval($_POST['month_cutoff']);
	$cutoffyear		= intval($_POST['year_cutoff']);
	$cutoffhour		= intval($_POST['hour_cutoff']);
	$cutoffminute	= intval($_POST['min_cutoff']);

	// ensure title is not empty
	if (trim($title) == '') {
		$msg->addError(array('EMPTY_FIELDS', _AT('title')));
	} else {
		//ensure the title does not exceed db length, 60
		$title = validate_length($title, 60);
	}

	// If due date is set and user has selected 'accept late submission until'
	// then ensure cutoff date is greater or equal to due date.
	if (($has_due_date == 'true') && ($late_submit == '2')){
		if ($cutoffyear < $dueyear){
			$msg->addError('CUTOFF_DATE_WRONG');
		} else if ($cutoffyear == $dueyear){
			if ($cutoffmonth < $duemonth){
				$msg->addError('CUTOFF_DATE_WRONG');
			} else if ($cutoffmonth == $duemonth){
				if ($cutoffday < $dueday){
					$msg->addError('CUTOFF_DATE_WRONG');
				} else if ($cutoffday == $dueday){
					if ($cutoffhour < $duehour){
						$msg->addError('CUTOFF_DATE_WRONG');
					} else if ($cutoffhour == $duehour) {
						if ($cutoffminute < $dueminute){
							$msg->addError('CUTOFF_DATE_WRONG');
						}
					}
				}
			}
		}
	}

	if (!$msg->containsErrors()) {
		$multi_submit = 0;

		// create the date strings
		$date_due = '0';
		$date_cutoff = '0';

		// note: if due date is NOT set then ignore the late submission date
		if ($has_due_date == 'true'){
			$date_due = $dueyear. '-' .str_pad ($duemonth, 2, "0", STR_PAD_LEFT). '-' .str_pad ($dueday, 2, "0", STR_PAD_LEFT). ' '.str_pad ($duehour, 2, "0", STR_PAD_LEFT). ':' .str_pad ($dueminute, 2, "0", STR_PAD_LEFT) . ':00';
		}

		if ($late_submit == '1'){ // never accept late submissions
			$date_cutoff = $date_due; // cutoff date will be same as due date
		} else if ($late_submit == '2'){ // accept late submissions until date
			$date_cutoff = $cutoffyear. '-' .str_pad ($cutoffmonth, 2, "0", STR_PAD_LEFT). '-' .str_pad ($cutoffday, 2, "0", STR_PAD_LEFT). ' '.str_pad ($cutoffhour, 2, "0", STR_PAD_LEFT). ':' .str_pad ($cutoffminute, 2, "0", STR_PAD_LEFT) . ':00';
		}

		// Are we creating a new assignment or updating an existing assignment?
		if ($id == '0'){
			// creating a new assignment

			$sql = "INSERT INTO %sassignments VALUES (NULL, %d, '%s', '%s', '%s', '%s', '%s')";
			$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $title, $assign_to, $date_due, $date_cutoff, $multi_submit));
			$msg->addFeedback('ASSIGNMENT_ADDED');
			
		} else { // updating an existing assignment
			$assign_to = $_POST['assign_to'];

			$sql = "UPDATE %sassignments SET title='%s', assign_to=%d, date_due='%s', date_cutoff='%s' WHERE assignment_id=%d AND course_id=%d";
			$result = queryDB($sql,array(TABLE_PREFIX, $title, $assign_to, $date_due, $date_cutoff, $id, $_SESSION['course_id']));
			
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		}
        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
		exit;
	}
} else { // creating a new assignment
	$title			= '';
	$assign_to		= '0';
	$multi_submit	= '1';
	$has_due_date	= 'false';
	$late_submit	= '0'; // 0 == always, 1 == never, 2 = until (date)

	$dueday		= $today['mday'];
	$duemonth	= $today['mon'];
	$dueyear	= $today['year'];
	$duehour	= '12';
	$dueminute	= '0';

	$cutoffday		= $today['mday'];
	$cutoffmonth	= $today['mon'];
	$cutoffyear		= $today['year'];
	$cutoffhour		= '12';
	$cutoffminute	= '0';
}

// ensure the dates are valid
if ($dueyear == '0'){
	// use today's date as default
	$dueday		= $today['mday'];
	$duemonth	= $today['mon'];
	$dueyear	= $today['year'];
	$duehour	= $today['hours'];
	$dueminute	= $today['minutes'];
	// round the minute to the next highest multiple of 5 
	$dueminute = round($dueminute / '5' ) * '5' + '5';
	if ($dueminute > '55'){ $dueminute = '55'; }
}
if ($cutoffyear == '0'){
	// use today's date as default
	$cutoffday		= $today['mday'];
	$cutoffmonth	= $today['mon'];
	$cutoffyear		= $today['year'];
	$cutoffhour		= $today['hours'];
	$cutoffminute	= $today['minutes'];
	// round the minute to the next highest multiple of 5 
	$cutoffminute = round($cutoffminute / '5' ) * '5' + '5';
	if ($cutoffminute > '55'){ $cutoffminute = '55'; }
}

$onload = 'document.form.title.focus();';

// enable/disable date controls
if ($has_due_date == 'false'){ 
	$onload .= ' disable_dates (true, \'_due\');';
}

if ($late_submit != '2'){
	$onload .= ' disable_dates (true, \'_cutoff\');';
}

if($assign_to != 0) {
    $sql = "SELECT title FROM %sgroups_types WHERE type_id=%s AND course_id=%d";
    $type_row = queryDB($sql, array(TABLE_PREFIX, $assign_to, $_SESSION['course_id']), TRUE);
}

$sql = "SELECT type_id, title FROM %sgroups_types WHERE course_id=%d ORDER BY title";
$rows_group_types = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));

require(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('id', $id);
$savant->assign('title', $title);
$savant->assign('assign_to', $assign_to);
if($assign_to != 0) {
    $savant->assign('type_row', $type_row);
}
$savant->assign('rows_group_types', $rows_group_types);
$savant->assign('has_due_date', $has_due_date);
$savant->assign('late_submit', $late_submit);

$savant->assign('dueday', $dueday);
$savant->assign('duemonth', $duemonth);
$savant->assign('dueyear', $dueyear);
$savant->assign('duehour', $duehour);
$savant->assign('dueminute', $dueminute);

$savant->assign('cutoffday', $cutoffday);
$savant->assign('cutoffmonth', $cutoffmonth);
$savant->assign('cutoffyear', $cutoffyear);
$savant->assign('cutoffhour', $cutoffhour);
$savant->assign('cutoffminute', $cutoffminute);

$savant->display('instructor/assignments/add_assignment.tmpl.php');
?>



<script language="javascript" type="text/javascript">
function disable_dates (state, name) {
	document.form['day' + name].disabled=state;
	document.form['month' + name].disabled=state;
	document.form['year' + name].disabled=state;
	document.form['hour' + name].disabled=state;
	document.form['min' + name].disabled=state;
}
</script>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>