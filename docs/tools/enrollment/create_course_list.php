<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_ENROLLMENT);

$course = $_SESSION['course_id'];

if (isset($_POST['addmore'])) {
	$msg->addFeedback('ADDMORE');
	header('Location: create_course_list.php');
	exit;
} else if (isset($_POST['return'])) {
	$msg->addFeedback('COMPLETED');
	header('Location: index.php');
	exit;
} else if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: index.php');	
	exit;
}


require(AT_INCLUDE_PATH.'header.inc.php');


if ($_POST['submit'] && !$_POST['verify']) {

	if (empty($_POST['first_name1']) && empty($_POST['last_name1']) && empty($_POST['email1'])) {
		$msg->addError('INCOMPLETE');
		$msg_error = TRUE;
	} else {
		$j=1;
		while ($_POST['first_name'.$j] || $_POST['last_name'.$j] || $_POST['email'.$j]) {
			$students[] = checkUserInfo(array('fname' => $_POST['first_name'.$j], 'lname' => $_POST['last_name'.$j], 'email' => $_POST['email'.$j]));
			$j++;
		}
	}	
	$msg->printErrors();
}


/**********************************************************************************************/
// !!!!!!STEP 1 - GET USER LIST !!!!!!!
if ((!isset($_POST['submit_unenr']) && !isset($_POST['submit_enr']) && !isset($_POST['resubmit']) && !isset($_POST['submit'])) || $msg_error) {
	$msg->addHelp('CREATE_LIST');
	$msg->printHelps();

	if (($_POST['sep_choice'] == '_') || empty($_POST['sep_choice'])) { 
		$under = ' checked="checked"'; 
	} else if ($_POST['sep_choice'] == '.') { 
		$period = ' checked="checked"'; 
	}
?>
	
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<div class="input-form">
	<div class="row">
		<label for="sep_choice"><?php echo _AT('import_sep_txt'); ?><br /></label>
		<input type="radio" name="sep_choice" id="und" value="_" <?php echo $under; ?> />
		<label for="und"><?php echo _AT('underscore'); ?></label>
		<input type="radio" name="sep_choice" id="per" value="." <?php echo $period; ?> />
		<label for="per"><?php echo _AT('period'); ?></label>
	</div>
</div>
		
<table class="data static" summary="" rules="cols">
<thead>
<tr>
	<th>&nbsp;</th>
	<th><?php echo _AT('first_name'); ?></th>
	<th><?php echo _AT('last_name'); ?></th>
	<th><?php echo _AT('email'); ?></th>
</tr>
</thead>

<tfoot>
<tr>
	<td colspan="4">
		<input type="submit" name="submit" value="<?php echo _AT('list_add_course_list');  ?>" />
		<input type="submit" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</td>
</tr>
</tfoot>

<tbody>
<?php for ($i=1; $i <= 5; $i++): ?>
	<tr>
		<td><?php echo $i; ?></td>
		<td><input type="text" name="first_name<?php echo $i; ?>" /></td>
		<td><input type="text" name="last_name<?php echo $i; ?>" /></td>
		<td><input type="text" name="email<?php echo $i; ?>" /></td>
	</tr>
<?php endfor; ?>
</tbody>

</table>
</form><?php
// !!!!!! END STEP 1 - GET USER LIST !!!!!!!
/**********************************************************************************************/


/**********************************************************************************************/
// !!!!!!STEP 2 - VERIFY INFORMATION !!!!!!!
} else {	
	$msg->addHelp('CREATE_LIST1');
	$msg->printHelps();

	if ($_POST['verify']) {
		for ($i=0; $i<$_POST['count']; $i++) {							
			
			$students[] = checkUserInfo(array('fname' => $_POST['fname'.$i], 'lname' => $_POST['lname'.$i], 'email' => $_POST['email'.$i], 'uname' => $_POST['uname'.$i], 'remove' => $_POST['remove'.$i]));

			if (!empty($students[$i]['err_email']) || !empty($students[$i]['err_uname'])) {
				$still_errors = TRUE;
			}
		}

		/**************************************************************************/
		// !!!!!!STEP 3 - INSERT INTO DB !!!!!!!
		if (!$still_errors && (isset($_POST['submit_unenr']) || isset($_POST['submit_enr']))) {			

			$sql = "SELECT * FROM ".TABLE_PREFIX."theme_settings where theme_id = '4'";
			$result = mysql_query($sql, $db); 	
			if ($row = mysql_fetch_assoc($result)) {
				$start_prefs = $row['preferences'];
			}	

			if (isset($_POST['submit_unenr'])) {
				$unenrolled = 'n';
				$role = "";
			} else {
				$unenrolled = 'y';
				$role = _AT('student1');
			}
			
			require_once(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
			foreach ($students as $student) {
				$name = $student['fname'].' '.$student['lname'];
				if ($name == ' ') {
					$name = '"'.$student['uname'].'"';
				}

				if (!$student['remove']) {
					$add_more_flag = TRUE;
					if ($student['exists'] == '') {
						$student = sql_quote($student);
						$now = date('Y-m-d H:i:s'); // we use this later for the email confirmation.

						$sql = "INSERT INTO ".TABLE_PREFIX."members (member_id, login, password, email, first_name, last_name, gender, preferences, creation_date, confirmed) VALUES (0, '".$student['uname']."', '".$student['uname']."', '".$student['email']."', '".$student['fname']."', '".$student['lname']."', '', '$start_prefs', '$now', 0)";

						if ($result = mysql_query($sql,$db)) {
							$student['exists'] = _AT('import_err_email_exists');
							$m_id = mysql_insert_id($db);

							$sql = "INSERT INTO ".TABLE_PREFIX."course_enrollment (member_id, course_id, approved, last_cid, role) VALUES (LAST_INSERT_ID(), '".$course."', '$unenrolled', 0, '$role')";

							if ($result = mysql_query($sql,$db)) {
								$enrolled_list .= '<li>'.$name.'</li>';

								// send email here.
								$code = substr(md5($student['email'] . $now . $m_id), 0, 10);
								$confirmation_link = $_base_href . 'confirm.php?id='.$m_id.SEP.'m='.$code;

								$subject = SITE_NAME.': '._AT('account_information');
								$body =  _AT('email_confirmation_message', SITE_NAME, $confirmation_link)."\n\n";
								$body .= SITE_NAME.': '._AT('account_information')."\n";
								$body .= _AT('login_name') .' : '.$student['uname'] . "\n";
								$body .= _AT('password') .' : '.$student['uname'] . "\n";

								$mail = new ATutorMailer;
								$mail->From     = EMAIL;
								$mail->AddAddress($student['email']);
								$mail->Subject = $subject;
								$mail->Body    = $body;
								$mail->Send();

								unset($mail);
							} else {
								$already_enrolled .= '<li>'.$name.'</li>';
							}
						} else {
							$msg->addError('LIST_IMPORT_FAILED');	
						}
					} else {
						$sql = "SELECT member_id FROM ".TABLE_PREFIX."members WHERE email='$student[email]'";
						$result = mysql_query($sql, $db);
						if ($row = mysql_fetch_assoc($result)) {
						
							$stud_id = $row['member_id'];
							if (isset($_POST['submit_unenr'])) {
								$sql = "INSERT INTO ".TABLE_PREFIX."course_enrollment (member_id, course_id, approved, last_cid, role) VALUES ('$stud_id', '".$course."', 'n', 0, '')";
							} else {
								$sql = "INSERT INTO ".TABLE_PREFIX."course_enrollment (member_id, course_id, approved, last_cid, role) VALUES ('$stud_id', '".$course."', 'y', 0, '')";
							}

							if($result = mysql_query($sql,$db)) {
								$enrolled_list .= '<li>'.$name.'</li>';
								
							} else {
								$already_enrolled .= '<li>'.$name.'</li>';
							}
						}
					}
				}
			}
			if ($already_enrolled) {
				$feedback = array('ALREADY_ENROLLED', $already_enrolled);
				$msg->addFeedback($feedback);
			}
			if ($enrolled_list) {
				$feedback = array('ENROLLED', $enrolled_list);
				$msg->addFeedback($feedback);
			}			
			$msg->printFeedbacks();

			if ($add_more_flag) { ?>
				<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" name="finalform" />';
				<div class="input-form">
					<div class="row buttons">
						<input type="submit" name="addmore" value="<?php echo _AT('add_more'); ?>" />
						<input type="submit" name="return"  value="<?php echo _AT('done'); ?>" />
					</div>
				</div>
				</form><?php				
			}
		}

	}

	// STEP 2 - INTERNAL VERIFICATION
	if (!$_POST['verify'] || $still_errors || isset($_POST['resubmit'])) { 
		
		$dsbld = '';
		if ($still_errors || $err_count>0) {
			$dsbld = 'disabled="disabled"';
		} ?>

		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<input type="hidden" name="verify" value="1" />
			
		<table class="data static" summary="" rules="cols">
		<thead>
		<tr>
			<th colspan="6"><?php echo _AT('list_import_results'); ?></th>
		</tr>
		<tr>
			<th scope="col"><?php echo _AT('status');     ?></th>
			<th scope="col"><?php echo _AT('first_name'); ?></th>
			<th scope="col"><?php echo _AT('last_name');  ?></th>
			<th scope="col"><?php echo _AT('email');      ?></th>
			<th scope="col"><?php echo _AT('username');   ?></th>
			<th scope="col"><?php echo _AT('remove');     ?></th>
		</tr>
		</thead>
		
		<tfoot>
		<tr>
			<td colspan="6">
				<input type="submit" name="resubmit" value="<?php echo _AT('resubmit'); ?>" />
				<input type="submit" name="submit_unenr" value="<?php echo _AT('list_add_unenrolled_list'); ?>" <?php echo $dsbld; ?> /> 
				<input type="submit" name="submit_enr" value="<?php echo _AT('list_add_enrolled_list'); ?>" <?php echo $dsbld; ?> />
			</td>
		</tr>
		</tfoot><?php

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
					echo ($student['remove'] ? 'checked=checked value="on"' : '');					  
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
					echo ($student['remove'] ? 'checked=checked value="on"' : '');					  
					echo 'name="remove'.$i.'" />';
				}
				$i++;
				echo '</tr>';
			}
			echo '</tbody>';
		}		
		echo '</table>';
		echo '<input type="hidden" name="count" value="'.count($students).'" />';
		echo '</form>';
	} 	
} 

require(AT_INCLUDE_PATH.'footer.inc.php');


function checkUserInfo($record) {
	global $db, $addslashes;

	if(empty($record['remove'])) {
		$record['remove'] == FALSE;			
	}

	//error flags for this record
	$record['err_email'] = FALSE;
	$record['err_uname'] = FALSE;
	$record['exists']    = FALSE;

	$record['email'] = trim($record['email']);

	/* email check */
	if ($record['email'] == '') {
		$record['err_email'] = _AT('import_err_email_missing');
	} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,4}$", $record['email'])) {
		$record['err_email'] = _AT('import_err_email_invalid');
	}

	$record['email'] = $addslashes($record['email']);

	$sql="SELECT * FROM ".TABLE_PREFIX."members WHERE email LIKE '$record[email]'";
	$result = mysql_query($sql,$db);
	if (mysql_num_rows($result) != 0) {
		$row = mysql_fetch_assoc($result);
		$record['exists'] = _AT('import_err_email_exists');
		$record['fname']  = $row['first_name']; 
		$record['lname']  = $row['last_name'];
		$record['email']  = $row['email'];
		$record['uname']  = $row['login'];
	}

	/* username check */
	if (empty($record['uname'])) {
		$record['uname'] = stripslashes (strtolower (substr ($record['fname'], 0, 1).$_POST['sep_choice'].$record['lname']));
	} 		

	$record['uname'] = preg_replace("{[^a-zA-Z0-9._]}","", trim($record['uname']));

	if (!(eregi("^[a-zA-Z0-9._]([a-zA-Z0-9._])*$", $record['uname']))) {
		$record['err_uname'] = _AT('import_err_username_invalid');
	} 

	$record['uname'] = $addslashes($record['uname']);

	$sql = "SELECT member_id FROM ".TABLE_PREFIX."members WHERE login='$record[uname]'";
	$result = mysql_query($sql,$db);
	if ((mysql_num_rows($result) != 0) && !$record['exists']) {
		$record['err_uname'] = _AT('import_err_username_exists');
	} else {
		$result = mysql_query("SELECT * FROM ".TABLE_PREFIX."admins WHERE login='$record[uname]'",$db);
		if (mysql_num_rows($result) != 0) {
			$record['err_uname'] = _AT('import_err_username_exists');
		}
	}	

	/* removed record? */
	if ($record['remove']) {
		//unset errors 
		$record['err_email'] = '';
		$record['err_uname'] = '';
	}

	$record['fname'] = htmlspecialchars(stripslashes(trim($record['fname'])));
	$record['lname'] = htmlspecialchars(stripslashes(trim($record['lname'])));
	$record['email'] = htmlspecialchars(stripslashes(trim($record['email'])));
	$record['uname'] = htmlspecialchars(stripslashes(trim($record['uname'])));

	return $record;
}

?>