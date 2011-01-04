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
// $Id: verify_list.php 10142 2010-08-17 19:17:26Z hwong $

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ENROLLMENT);
require(AT_INCLUDE_PATH.'../mods/_core/enrolment/lib/enroll.inc.php');

/************  GETTING INFO FROM CREATE/IMPORT CALLS  **********/
if (isset($_POST['addmore'])) {
	//$msg->addFeedback('ADDMORE');
	header('Location: create_course_list.php');
	exit;
} else if (isset($_POST['return'])) {
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php');
	exit;
} else if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');	
	exit;
} else if (isset($_POST['submit']) && !$_POST['verify']) {
	//CREATE COURSE LIST!!!!!!
	if ($_POST['from'] == 'create') {
		if (empty($_POST['first_name1']) && empty($_POST['last_name1']) && empty($_POST['email1'])) {
			$msg->addError('INCOMPLETE');
			header('Location: ./create_course_list.php');
			exit;
		} else {
			$j=1;
			while ($_POST['first_name'.$j] || $_POST['last_name'.$j] || $_POST['email'.$j]) {
				$students[] = checkUserInfo(array('fname' => $_POST['first_name'.$j], 'lname' => $_POST['last_name'.$j], 'email' => $_POST['email'.$j]));
				$j++;
			}
		}
	} 
	//IMPORT COURSE LIST!!!!!!
	else if ($_POST['from'] == 'import') {
		if ($_FILES['file']['size'] < 1) {
			$msg->addError('FILE_EMPTY');
			header('Location: ./import_course_list.php');
			exit;
		} else {
			$fp = fopen($_FILES['file']['tmp_name'],'r');
			$line_number=0;
			while ($data = fgetcsv($fp, 100000, ',')) {
				$line_number++;
				$num_fields = count($data);
				if ($num_fields == 3) {
					$students[] = checkUserInfo(array('fname' => $data[0], 'lname' => $data[1], 'email' => $data[2]));
				} else if ($num_fields != 1) {
					$errors = array('INCORRECT_FILE_FORMAT', $line_number);
					$msg->addError($errors);
					header('Location: ./import_course_list.php');
					exit;
				} else if (($num_fields == 1) && (trim($data[0]) != '')) {
					$errors = array('INCORRECT_FILE_FORMAT', $line_number);
					$msg->addError($errors);
					header('Location: ./import_course_list.php');
					exit;
				}
			}
		}

	}
} 
/*************  INFO GATHERED  **************/

require(AT_INCLUDE_PATH.'header.inc.php');


if ($_POST['verify']) {
	for ($i=0; $i<$_POST['count']; $i++) {
		$info = array('fname' => $_POST['fname'.$i], 'lname' => $_POST['lname'.$i], 'email' => $_POST['email'.$i], 'uname' => $_POST['uname'.$i], 'remove' => $_POST['remove'.$i]);
		$students[] = checkUserInfo($info);

		if (!empty($students[$i]['err_email']) || !empty($students[$i]['err_uname'])) {
			$still_errors = TRUE;
		}
	}

	/**************************************************************************/
	// !!!!!!STEP 3 - INSERT INTO DB !!!!!!!
	if (!$still_errors && (isset($_POST['submit_unenr']) || isset($_POST['submit_enr']))) {			

		$enroll = 'y';
		if (isset($_POST['submit_unenr'])) {
			$enroll = 'n';
		}

		add_users($students, $enroll, $_SESSION['course_id']);


		  ?>
		 <div id="container">
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="finalform" />
		<div class="input-form">
		<?php
		$msg->printFeedbacks();
		?>
			<div class="row buttons">
				<input type="submit" name="addmore" value="<?php echo _AT('add_more'); ?>" />
				<input type="submit" name="return"  value="<?php echo _AT('done'); ?>" />
			</div>
		</div>
		</form></div><?php				
	}

}

// STEP 2 - INTERNAL VERIFICATION
if ($still_errors || !isset($_POST['verify']) || isset($_POST['resubmit'])) { ?>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="verify" value="1" />
	<input type="hidden" name="count" value="<?php echo count($students); ?>" />
		
	<table class="data static" summary="" rules="cols">
	<thead>
	<tr>
		<th scope="col"><?php echo _AT('status');     ?></th>
		<th scope="col"><?php echo _AT('first_name'); ?></th>
		<th scope="col"><?php echo _AT('last_name');  ?></th>
		<th scope="col"><?php echo _AT('email');      ?></th>
		<th scope="col"><?php echo _AT('login_name'); ?></th>
		<th scope="col"><?php echo _AT('remove');     ?></th>
	</tr>
	</thead><?php

	$err_count = 0;
	$i=0;
	if (is_array($students)) {
		echo '<tbody>';
		foreach ($students as $student) {
			echo '<tr><small>';
			echo '<td><span style="color: red;">';

			//give status
			if(!empty($student['err_email'])) {
				echo $student['err_email'];
			}

			if(!empty($student['err_uname'])) {
				if(!empty($student['err_email'])) {
					echo '<br />';
				}
				echo $student['err_uname'];
			} 		
			if (empty($student['err_uname']) && empty($student['err_email'])) {
				 
				if ($student['remove']) {
					echo '</span><span style="color: purple;">'._AT('removed');
				} else if ($student['err_disabled']) {
					echo '</span><span style="color: purple;">'._AT('disabled');								
				} else if (!empty($student['exists'])) {
					echo '</span><span style="color: green;">'._AT('ok').' - '.$student['exists'];
				} else {
					echo '</span><span style="color: green;">'._AT('ok');								
				}
			} else {
				$err_count++;
			}
			echo '</span></td>';

			if (empty($student['exists'])) {
				echo '<td><input type="text" name="fname'.$i.'" value="'.$student['fname'].'" /></td>';
				echo '<td><input type="text" name="lname'.$i.'" value="'.$student['lname'].'" /></td>';
				echo '<td><input type="text" name="email'.$i.'" value="'.$student['email'].'" /></td>';		
				echo '<td><input type="text" name="uname'.$i.'" value="'.$student['uname'].'" />';	
				echo '<td><input type="checkbox" ';					
				echo ($student['remove'] ? 'checked="checked" value="on"' : '');					  
				echo 'name="remove'.$i.'" />';
			} else {
				echo '<input type="hidden" name="fname'.$i.'" value="'.$student['fname'].'" />';		
				echo '<input type="hidden" name="lname'.$i.'" value="'.$student['lname'].'" />';		
				echo '<input type="hidden" name="email'.$i.'" value="'.$student['email'].'" />';		
				echo '<input type="hidden" name="uname'.$i.'" value="'.$student['uname'].'" />';		

				echo '<td>'.AT_print($student['fname'], 'members.first_name').'</td>';
				echo '<td>'.AT_print($student['lname'], 'members.last_name').'</td>';
				echo '<td>'.AT_print($student['email'], 'members.email').'</td>';
				echo '<td>'.AT_print($student['uname'], 'members.login').'</td>';
				echo '<td><input type="checkbox" ';					
				echo ($student['remove'] ? 'checked="checked" value="on"' : '');					  
				echo 'name="remove'.$i.'" />';
			}
			$i++;
			echo '</tr>';
		}
		echo '</tbody>';
	}

	$dsbld = '';
	if ($still_errors || $err_count>0) {
		$dsbld = 'disabled="disabled"';
	} ?>

	<tfoot>
	<tr>
		<td colspan="6">
			<input type="submit" name="resubmit" value="<?php echo _AT('resubmit'); ?>" />
			<input type="submit" name="submit_enr" value="<?php echo _AT('list_add_enrolled_list'); ?>" <?php echo $dsbld; ?> />
		</td>
	</tr>
	</tfoot>

	</table>
	</form><?php
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>