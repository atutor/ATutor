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
// $Id: verify_list.php 7208 2008-01-09 16:07:24Z greg $

$page = 'gradebook';

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ENROLLMENT);
require('lib/gradebook.inc.php');

/************  GETTING INFO FROM CREATE/IMPORT CALLS  **********/
if (isset($_POST['cancel']) || !isset($_POST["gradebook_test_id"]) || !isset($_POST["test_id"])) 
{
	$msg->addFeedback('CANCELLED');
	header('Location: update_gradebook.php');
	exit;
} 
else if (isset($_POST['combine']))
{
	//Check if the "combine from test" has students taking it more than once
	$no_error = true;
	
	$sql = "SELECT title FROM ".TABLE_PREFIX."tests WHERE test_id=".$_POST["test_id"];
	$result = mysql_query($sql, $db) or die(mysql_error());
	$row = mysql_fetch_assoc($result);

	$studs_take_num = get_studs_take_more_than_once($_SESSION["course_id"], $_POST["test_id"]);
	
	foreach ($studs_take_num as $student => $num)
	{
		if ($no_error) $no_error = false;
		$error_msg .= $student . ": " . $num . " times<br>";
	}
	
	if (!$no_error)
	{
		$error = array('COMBINE_TESTS',
						$row["title"], 
						$error_msg);
		$msg->addError($error);
	}
	
	if (!$msg->containsErrors()) 
	{
		$sql = "SELECT id, grade_scale_id FROM ".TABLE_PREFIX."gradebook_tests WHERE gradebook_test_id = ". $_POST["gradebook_test_id"];
		$result = mysql_query($sql, $db) or die(mysql_error());
		$row = mysql_fetch_assoc($result) or die(mysql_error());
		$grade_scale_id = $row["grade_scale_id"];

		$sql = "SELECT m.first_name, m.last_name, m.email, e.member_id FROM ".TABLE_PREFIX."members m, ".TABLE_PREFIX."course_enrollment e WHERE m.member_id = e.member_id AND e.course_id=".$_SESSION["course_id"]." AND e.approved='y' AND e.role<>'Instructor' ORDER BY m.first_name,m.last_name";
		$result	= mysql_query($sql, $db) or die(mysql_error());
		
		while ($row = mysql_fetch_assoc($result))
		{
			$grade = get_member_grade($_POST["test_id"], $row["member_id"], $grade_scale_id);
			
			if ($grade <> "")
				$students[] = check_user_info(array('member_id' => $row["member_id"], 'fname' => $row["first_name"], 'lname' => $row["last_name"], 'email' => $row["email"], 'grade' => $grade, 'gradebook_test_id' => $_POST['gradebook_test_id']));
		}

		if (count($students) == 0)
		{
			$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
			header('Location: update_gradebook.php');
			exit;
		}
		
	}
	else
	{
		header('Location: update_gradebook.php');
		exit;
	} 
}

/*************  INFO GATHERED  **************/

if ($_POST['verify']) {
	for ($i=0; $i < $_POST['count']; $i++) 
	{
		$info = array('fname' => $_POST['fname'.$i], 'lname' => $_POST['lname'.$i], 'email' => $_POST['email'.$i], 'grade' => $_POST['grade'.$i], 'remove' => $_POST['remove'.$i], 'gradebook_test_id' => $_POST["gradebook_test_id"], 'solve_conflict' => $_POST["solve_conflict"]);
		$students[] = check_user_info($info);

		if (!empty($students[$i]['error']))
			$still_errors = TRUE;
	}

	/**************************************************************************/
	// !!!!!!STEP 3 - INSERT INTO DB !!!!!!!
	
	if (!$still_errors && isset($_POST['update'])) 
	{
		update_gradebook_external_test($students, $_POST["gradebook_test_id"]);
		header('Location: update_gradebook.php');	
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');
// STEP 2 - INTERNAL VERIFICATION
if ($still_errors || !isset($_POST['verify']) || isset($_POST['resubmit'])) { 
?>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="verify" value="1" />
	<input type="hidden" name="gradebook_test_id" value="<?php echo $_POST["gradebook_test_id"]; ?>" />
	<input type="hidden" name="test_id" value="<?php echo $_POST["test_id"]; ?>" />
	<input type="hidden" name="solve_conflict" value="<?php echo $_POST["solve_conflict"]; ?>" />
	<input type="hidden" name="count" value="<?php echo count($students); ?>" />
		
	<table class="data static" summary="" rules="cols">
	<thead>
	<tr>
		<th scope="col"><?php echo _AT('status');     ?></th>
		<th scope="col"><?php echo _AT('first_name'); ?></th>
		<th scope="col"><?php echo _AT('last_name');  ?></th>
		<th scope="col"><?php echo _AT('email');      ?></th>
		<th scope="col"><?php echo _AT('grade'); ?></th>
		<th scope="col"><?php echo _AT('remove');     ?></th>
	</tr>
	</thead>
<?php

	$err_count = 0;
	$i=0;

	if (is_array($students)) {
		echo '	<tbody>'."\n\r";
		foreach ($students as $student) {

			if (!empty($student['conflict']))
				$has_conflict = TRUE;
	
			echo '		<tr>'."\n\r";
			echo '			<td>'."\n\r";

			//give status
			if(!empty($student['error'])) {
				echo '<span style="color: red;">'.$student['error'];
			}

			if (empty($student['error'])) 
			{
				if ($student['remove'])
					echo '<span style="color: purple;">'._AT('removed');
				else 
					echo '<span style="color: green;">'._AT('ok');								
			} 
			else 
				$err_count++;

			echo '</span></td>'."\n\r";

			echo '			<td>'.$student['fname'].'</td>'."\n\r";
			echo '			<td>'.$student['lname'].'</td>'."\n\r";
			echo '			<td>'.$student['email'].'</td>'."\n\r";
			echo '			<td><input type="text" name="grade'.$i.'" value="'.$student['grade'].'" /></td>'."\n\r";
			echo '			<td><input type="checkbox" ';					
			echo ($student['remove'] ? 'checked="checked" value="on"' : '');					  
			echo 'name="remove'.$i.'" /></td>'."\n\r";

			echo '			<input type="hidden" name="fname'.$i.'" value="'.$student['fname'].'" />'."\n\r";
			echo '			<input type="hidden" name="lname'.$i.'" value="'.$student['lname'].'" />'."\n\r";
			echo '			<input type="hidden" name="email'.$i.'" value="'.$student['email'].'" />'."\n\r";

			$i++;
			echo '		</tr>'."\n\r";
		}
		echo '	</tbody>'."\n\r";
	}

	$dsbld = '';
	if ($still_errors || $err_count>0) {
		$dsbld = 'disabled="disabled"';
	} 
?>

	<tfoot>
	<tr>
		<td colspan="6">
			<input type="submit" name="resubmit" value="<?php echo _AT('resubmit'); ?>" />
			<input type="submit" name="update" value="<?php echo _AT('update'); ?>" <?php echo $dsbld; ?> />
<?php
if ($has_conflict)
{
?>
			<span style="padding:0px 10px">|</span> 
			
			<select name="solve_conflict">
				<option value="0"><?php echo _AT('how_to_solve_conflict'); ?></option>
					<option value="<?php echo USE_HIGHER_GRADE; ?>"><?php echo _AT('use_higher_grade'); ?></option>
					<option value="<?php echo USE_LOWER_GRADE; ?>"><?php echo _AT('use_lower_grade'); ?></option>	
					<option value="<?php echo NOT_OVERWRITE; ?>"><?php echo _AT('not_overwrite'); ?></option>
					<option value="<?php echo OVERWRITE; ?>"><?php echo _AT('overwrite'); ?></option>				
			</select>
<?php
}
?>
		</td>
	</tr>
	</tfoot>

	</table>
	</form><?php
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>