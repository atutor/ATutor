<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: import_course_list.php,v 1.10 2004/05/26 20:00:20 joel Exp $

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

$course = $_SESSION['course_id'];
$title = _AT('course_enrolment');

function checkUserInfo($record) {
	global $db;

	if(empty($record['remove'])) {
		$record['remove'] == FALSE;			
	}

	//error flags for this record
	$record['err_email'] = FALSE;
	$record['err_uname'] = FALSE;
	$record['exists'] = FALSE;

	/* email check */
	if ($record['email'] == '') {
		$record['err_email'] = _AT('import_err_email_missing');
	} else if (!eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,4}$", $record['email'])) {
		$record['err_email'] = _AT('import_err_email_invalid');
	}
	$sql="SELECT * FROM ".TABLE_PREFIX."members WHERE email LIKE '".$record['email']."'";
	$result = mysql_query($sql,$db);
	if (mysql_num_rows($result) != 0) {
		$row = mysql_fetch_array($result);
		$record['exists'] = _AT('import_err_email_exists');
		$record['fname'] = $row['first_name']; 
		$record['lname'] = $row['last_name'];
		$record['email'] = $row['email'];
		$record['uname'] = $row['login'];
	}

	/* username check */
	if (empty($record['uname'])) {
		$record['uname'] = stripslashes(strtolower($record['fname'].$_POST['sep_choice'].$record['lname']));
	} 		

	if (!(eregi("^[a-zA-Z0-9._]([a-zA-Z0-9._])*$", $record['uname']))) {
		$record['err_uname'] = _AT('import_err_username_invalid');
	} 
	$sql = "SELECT * FROM ".TABLE_PREFIX."members WHERE login='".sql_quote($record['uname'])."'";
	$result = mysql_query($sql,$db);
	if ((mysql_num_rows($result) != 0) && !$record['exists']) {
		$record['err_uname'] = _AT('import_err_username_exists');
	} else if ($_POST['login'] == ADMIN_USERNAME) {
		$record['err_uname'] = _AT('import_err_username_exists');
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

require(AT_INCLUDE_PATH.'header.inc.php');

echo '<a name="content"></a>';

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-tools.gif" border="0" vspace="2"  class="menuimageh2" width="42" height="40" alt="" />';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/" class="hide" >'._AT('tools').'</a>';
}
echo '</h2>'."\n";

echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '&nbsp;<img src="images/icons/default/enrol_mng-large.gif"  class="menuimageh3" width="42" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo '<a href="tools/enroll_admin.php?course='.$_SESSION['course_id'].'">'._AT('course_enrolment').'</a>';
}
echo '</h3><br />'."\n";


if ($_POST['submit'] && !$_POST['verify']) {
	if ($_FILES['file']['size'] < 1) {
		$errors[] = AT_ERROR_FILE_NOT_SELECTED;		
	} else {
		$fp = fopen($_FILES['file']['tmp_name'],'r');
		$line_number=0;
		while ($data = fgetcsv($fp, 100000, ',')) {
			$line_number++;
			$num_fields = count($data);
			if ($num_fields == 3) {
				$students[] = checkUserInfo(array('fname' => $data[0], 'lname' => $data[1], 'email' => $data[2]));
			} else if ($num_fields != 1) {
				$errors[] = array(AT_ERROR_INCORRECT_FILE_FORMAT, $line_number);
				break;
			} else if (($num_fields == 1) && (trim($data[0]) != '')) {
				$errors[] = array(AT_ERROR_INCORRECT_FILE_FORMAT, $line_number);
				break;
			}
		}
	}
	print_errors($errors);
}

if ($_POST['submit']=='' || !empty($errors)) {
	//step one - upload file
?>
	<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
	<input type="hidden" name="course" value="<?php echo $course; ?>" />

	<table align="center" cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="90%">
	<tr><th class="cyan"><?php echo _AT('list_import_course_list');  ?></th></tr>
	<tr><td class="row1"><?php echo _AT('list_import_howto'); ?></td></tr>
	<tr><td height="1" class="row2"></td></tr>

	<tr><td class="row1" colspan="6" align="left">For auto-generated usernames, separate first and last names with: <input type="radio" name="sep_choice" class="radio" value="_"
	<?php		
		if ($_POST['sep_choice'] == '_' || empty($_POST['sep_choice'])) { echo 'checked="checked"'; }
		echo ' />Underscore <input type="radio" name="sep_choice" class="radio" value="."';
		if ($_POST['sep_choice'] == '.') { echo 'checked="checked"'; }
		echo '/>Period';
	?>
	</tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr><td class="row1" align="center">

	
	<label for="course_list"><?php echo _AT('import_course_list'); ?>: </label>
	<input type="file" name="file" id="course_list" class="formfield" />
	<input type="submit" name="submit" value="<?php echo _AT('list_import_course_list');  ?>" class="button" />
	</form>

	</td></tr>
	<tr><td height="1" class="row2"></td></tr>
	<tr><td class="row1" align="center">
	</table>

<?php

} else {	
	//step two - verify information

	if ($_POST['verify']) {
		for ($i=0; $i<$_POST['count']; $i++) {							
			
			$students[] = checkUserInfo(array('fname' => $_POST['fname'.$i], 'lname' => $_POST['lname'.$i], 'email' => $_POST['email'.$i], 'uname' => $_POST['uname'.$i], 'remove' => $_POST['remove'.$i]));

			if (!empty($students[$i]['err_email']) || !empty($students[$i]['err_uname'])) {
				$still_errors = TRUE;
			}
		}
		if (!$still_errors && ($_POST['submit']==_AT('import_course_list'))) {			
			//step three - make new users in DB, enroll all		
			$sql = "SELECT * FROM ".TABLE_PREFIX."theme_settings where theme_id = '4'";
			$result = mysql_query($sql, $db); 	
			if ($row = mysql_fetch_array($result)) {
				$start_prefs = $row['preferences'];
			}	

			//send new member email
			$result = mysql_query("SELECT email FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]", $db);
			if ($row = mysql_fetch_assoc($result)) {
				$email_from = $row['email'];
			} else {
				$email_from = ADMIN_EMAIL;
			}

			
			foreach ($students as $student) {
				$name = $student['fname'].' '.$student['lname'];
				if ($name == ' ') {
					$name = '"'.$student['uname'].'"';
				}

				if (!$student['remove']) {
					if ($student['exists']=='') {
						//make new user
						$student = sql_quote($student);

						$sql = "INSERT INTO ".TABLE_PREFIX."members (member_id, login, password, email, first_name, last_name, gender, preferences, creation_date) VALUES (0, '".$student['uname']."', '".$student['uname']."', '".$student['email']."', '".$student['fname']."', '".$student['lname']."', '', '$start_prefs', NOW())";
						if($result = mysql_query($sql,$db)) {
							echo _AT('list_new_member_created', $name);
							$stud_id = mysql_insert_id($db);
							$student['exists'] = _AT('import_err_email_exists');

							$sql = "INSERT INTO ".TABLE_PREFIX."course_enrollment (member_id, course_id, approved, last_cid, role) VALUES ('$stud_id', '".$course."', 'y', 0, '')";

							if($result = mysql_query($sql,$db)) {
								echo _AT('list_member_enrolled', $name).'<br />';
							} else {
								echo _AT('list_member_already_enrolled', $name).'<br />';
							}

							// send email here.
							$subject = SITE_NAME.': '._AT('account_information');
							$body = SITE_NAME.': '._AT('account_information')."\n\n";
							$body .= _AT('new_account_msg', $_base_href.'login.php'). "\n\n";
							$body .= _AT('login') .' : '.$student['uname'] . "\n\n";
							$body .= _AT('password') .' : '.$student['uname'] . "\n\n";

							$mail = new ATutorMailer;

							$mail->From     = $email_from;
							$mail->AddAddress($student['email']);
							$mail->Subject = $subject;
							$mail->Body    = $body;

							$mail->Send();

							unset($mail);

						} else {
							$errors[] = AT_ERROR_LIST_IMPORT_FAILED;	
						}
					} else {
						$sql = "SELECT member_id FROM ".TABLE_PREFIX."members WHERE email='$student[email]'";
						$result = mysql_query($sql, $db);
						if ($row = mysql_fetch_assoc($result)) {
						
							$stud_id = $row['member_id'];

							$sql = "INSERT INTO ".TABLE_PREFIX."course_enrollment (member_id, course_id, approved, last_cid, role) VALUES ('$stud_id', '".$course."', 'y', 0, '')";

							if($result = mysql_query($sql,$db)) {
								echo _AT('list_member_enrolled', $name).'<br />';
							} else {
								echo _AT('list_member_already_enrolled', $name).'<br />';
							}
						}
					}
				}
			}	
		}
	} 
	if (!$_POST['verify'] || $still_errors || ($_POST['submit']==_AT('resubmit'))) {
		
		//output results table		
		echo _AT('import_course_list_verify');

		echo '<br /><br /><form enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'" method="post">';
		echo '<input type="hidden" name="verify" value="1" />';	
		echo'<input type="hidden" name="course" value="'.$course.'" />';
		
		echo '<table align="center" width="100%" cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="">';
		echo '<tr><th  colspan="6">'._AT('list_import_results').'</th></tr>';
		echo '<tr><th class="cat" scope="col">'._AT('status').'</th><th class="cat" scope="col">'._AT('first_name').'</th><th class="cat" scope="col">'._AT('last_name').'</th><th class="cat" scope="col">'._AT('email').'</th><th class="cat" scope="col">'._AT('username').'</th><th class="cat" scope="col">'._AT('remove').'</th></tr>';

		$err_count = 0;
		$i=0;
		if (is_array($students)) {
			foreach ($students as $student) {
				echo '<tr><small>';
				echo '<td class="row1"><font color="red">';

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
						echo '</font><font color="purple">'._AT('removed');
					} else if (!empty($student['exists'])) {
						echo '</font><font color="green">'._AT('ok').' - '.$student['exists'];
					} else {
						echo '</font><font color="green">'._AT('ok');								
					}
					
				} else {
					$err_count++;
				}
				echo '</font></td>';	

				if (empty($student['exists'])) {
					echo '<td class="row1"><input type="text" name="fname'.$i.'" class="formfield" value="'.$student['fname'].'" size="10" /></td>';
					echo '<td class="row1"><input type="text" name="lname'.$i.'" class="formfield" value="'.$student['lname'].'" size="10" /></td>';
					echo '<td class="row1"><input type="text" name="email'.$i.'" class="formfield" value="'.$student['email'].'" size="14" /></td>';				
					echo '<td class="row1"><input type="text" name="uname'.$i.'" class="formfield" value="'.$student['uname'].'" size="10" />';	
					echo '<td class="row1" align="center"><input type="checkbox" ';					
					echo ($student['remove'] ? 'checked=checked value="on"' : '');					  
					echo 'name="remove'.$i.'" />';
				} else {
					echo '<input type="hidden" name="fname'.$i.'" value="'.$student['fname'].'" />';		
					echo '<input type="hidden" name="lname'.$i.'" value="'.$student['lname'].'" />';		
					echo '<input type="hidden" name="email'.$i.'" value="'.$student['email'].'" />';		
					echo '<input type="hidden" name="uname'.$i.'" value="'.$student['uname'].'" />';		

					echo '<td class="row1">'.AT_print($student['fname'], 'members.first_name').'</td>';
					echo '<td class="row1">'.AT_print($student['lname'], 'members.last_name').'</td>';
					echo '<td class="row1">'.AT_print($student['email'], 'members.email').'</td>';
					echo '<td class="row1">'.AT_print($student['uname'], 'members.login').'</td>';
					echo '<td class="row1" align="center"><input type="checkbox" ';					
					echo ($student['remove'] ? 'checked=checked value="on"' : '');					  
					echo 'name="remove'.$i.'" />';		
				}
				$i++;
				echo '</tr>';
			}
		}		

		echo '<tr><td class="row1" colspan="6" align="center"><input type="submit" name="submit" value="'._AT('resubmit').'" class="button" /> ';
		
		if ($still_errors || $err_count>0) {	
			echo '<input type="submit" name="submit" value="'._AT('import_course_list').'" class="button" disabled="disabled" />';			
		} else {
			echo '<input type="submit" name="submit" value="'._AT('import_course_list').'" class="button" />';
		}
		
		echo '</td></tr></table>';
		echo '<input type="hidden" name="count" value="'.count($students).'" /></form>';
	} 	
} 

echo '<br /><br />';
require(AT_INCLUDE_PATH.'footer.inc.php');
?>
