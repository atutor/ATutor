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
if (isset($_POST['cancel'])) 
{
	$msg->addFeedback('CANCELLED');
	header('Location: import_export_external_marks.php');
	exit;
} 
else if (isset($_POST['import']))
{
	//IMPORT
	if ($_FILES['file']['size'] < 1) 
	{
		$msg->addError('FILE_EMPTY');
		header('Location: import_course_list.php');
		exit;
	} 
	else 
	{
		$fp = fopen($_FILES['file']['tmp_name'],'r');
		$line_number=0;
		while ($data = fgetcsv($fp, 100000, ',')) {
			$line_number++;
			if ($line_number > 1)
			{
				$num_fields = count($data);
				if ($num_fields == 4)
				{
					$students[] = check_user_info(array('fname' => $data[0], 'lname' => $data[1], 'email' => $data[2], 'grade' => $data[3], 'gradebook_test_id' => $_POST['gradebook_test_id']));
				} 
				else if ($num_fields != 1) 
				{
					$errors = array('INCORRECT_FILE_FORMAT', $line_number);
					$msg->addError($errors);
					header('Location: import_course_list.php');
					exit;
				} 
				else if (($num_fields == 1) && (trim($data[0]) != '')) 
				{
					$errors = array('INCORRECT_FILE_FORMAT', $line_number);
					$msg->addError($errors);
					header('Location: import_course_list.php');
					exit;
				}
			}
		}
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
		header('Location: import_export_external_marks.php');	
		exit;
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');
// STEP 2 - INTERNAL VERIFICATION
if ($still_errors || !isset($_POST['verify']) || isset($_POST['resubmit'])) { 
?>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<div class="input-form">
		<div class="row">
			<p><?php echo _AT('grade_info'); ?></p>
		</div>
	</div>

	<input type="hidden" name="verify" value="1" />
	<input type="hidden" name="gradebook_test_id" value="<?php echo $_POST["gradebook_test_id"]; ?>" />
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

			echo '			<td><input type="text" name="fname'.$i.'" value="'.$student['fname'].'" /></td>'."\n\r";
			echo '			<td><input type="text" name="lname'.$i.'" value="'.$student['lname'].'" /></td>'."\n\r";
			echo '			<td><input type="text" name="email'.$i.'" value="'.$student['email'].'" /></td>'."\n\r";
			echo '			<td><input type="text" name="grade'.$i.'" value="'.$student['grade'].'" /></td>'."\n\r";
			echo '			<td><input type="checkbox" ';					
			echo ($student['remove'] ? 'checked="checked" value="on"' : '');					  
			echo 'name="remove'.$i.'" /></td>'."\n\r";

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