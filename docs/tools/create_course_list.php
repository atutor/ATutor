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
// $Id: import_course_list.php 1651 2004-09-21 15:03:59Z joel $

$page = 'enroll_admin';
$_user_location = '';

define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/index.php';
$_section[1][0] = _AT('course_enrolment');
$_section[1][1] = 'tools/enroll_admin.php';
$_section[2][0] = _AT('list_create_course_list');
$_section[2][1] = 'tools/create_course_list.php';

$course = $_SESSION['course_id'];
$title = _AT('course_enrolment');

function checkUserInfo($record) {
	global $db, $addslashes;

	if(empty($record['remove'])) {
		$record['remove'] == FALSE;			
	}

	//error flags for this record
	$record['err_email'] = FALSE;
	$record['err_uname'] = FALSE;
	$record['exists']    = FALSE;

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
		$row = mysql_fetch_array($result);
		$record['exists'] = _AT('import_err_email_exists');
		$record['fname']  = $row['first_name']; 
		$record['lname']  = $row['last_name'];
		$record['email']  = $row['email'];
		$record['uname']  = $row['login'];
	}

	/* username check */
	if (empty($record['uname'])) {
		$record['uname'] = stripslashes(strtolower($record['fname'].$_POST['sep_choice'].$record['lname']));
	} 		

	if (!(eregi("^[a-zA-Z0-9._]([a-zA-Z0-9._])*$", $record['uname']))) {
		$record['err_uname'] = _AT('import_err_username_invalid');
	} 

	$record['uname'] = $addslashes($record['uname']);

	$sql = "SELECT * FROM ".TABLE_PREFIX."members WHERE login='$record[uname]'";
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
if ($_POST['addmore']) {
	header('Location: create_course_list.php?f='.AT_FEEDBACK_ADDMORE);
	exit;
}

if ($_POST['return']) {
	header('Location: enroll_admin.php?f='.AT_FEEDBACK_COMPLETED);
	exit;
}

if(isset($_POST['cancel'])) {
	header('Location: enroll_admin.php?f=' . AT_FEEDBACK_CANCELLED);	
	exit;
}
require(AT_INCLUDE_PATH.'header.inc.php');

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
	if (empty($_POST['first_name1']) && empty($_POST['last_name1']) && empty($_POST['email1'])) {
		$errors[] = AT_ERROR_INCOMPLETE;
	}
	else {
	//debug($_POST['first_name1']);

		$j=1;
		while ($_POST['first_name'.$j] || $_POST['last_name'.$j] || $_POST['email'.$j]) {
			$students[] = checkUserInfo(array('fname' => $_POST['first_name'.$j], 'lname' => $_POST['last_name'.$j], 'email' => $_POST['email'.$j]));
			$j++;
		}
	}
	print_errors($errors);

}

if ($_POST['submit']=='' || !empty($errors)) {
	//step one - upload file
?>
	<p align="center"><strong>
	<a href="tools/import_course_list.php"> <?php echo _AT('import_from_file');  ?></a>	
	</strong></p>
	<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="MAX_FILE_SIZE" value="30000" />
	<input type="hidden" name="course" value="<?php echo $course; ?>" />

	<table align="center" cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="90%">
	<tr><th class="cyan" colspan="4"><?php echo _AT('list_create_course_list');  ?></th></tr>
	
	<tr><td class="row1" colspan="4"><?php echo _AT('list_create_howto'); ?></td></tr>
	
	<tr><td height="1" class="row2" colspan="4"></td></tr>
	<tr><td class="row1" colspan="4" align="left"><?php echo _AT('import_sep_txt'); ?><br /><label><input type="radio" name="sep_choice" class="radio" value="_" 
	<?php		
		if (($_POST['sep_choice'] == '_') || empty($_POST['sep_choice'])) { 
			echo ' checked="checked"'; 
		}

		echo ' />'._AT('underscore').'</label> <label><input type="radio" name="sep_choice" class="radio" value="."';
		if ($_POST['sep_choice'] == '.') { 
			echo ' checked="checked"'; 
		}
		echo ' />'._AT('period').'</label>';
	?>
	</td></tr>
	<tr><td height="1" class="row2" colspan="4"></td></tr>
	
	<tr>
		<td class="cat" align="center" width="4%"></td>
		<td class="cat" align="center" width="32%">
			<?php echo _AT('first_name'); ?>
		</td>
		<td class="cat" align="center" width="32%">
			<?php echo _AT('last_name'); ?>			
		</td>
		<td class="cat" align="center" width="32%">
			<?php echo _AT('email'); ?>
		</td>
	</tr>

	<tr><td height="1" class="row2" colspan="4"></td></tr>
<?php
for ($i=1; $i <= 5; $i++) { ?>
	<tr>
		<td class="row1" align="center">
			<?php echo $i; ?>
		</td>
		<td class="row1" align="center">
			<input type="text" name="first_name<?php echo $i; ?>"  class="formfield" />
		</td>
		<td class="row1" align="center">
			<input type="text" name="last_name<?php echo $i; ?>"  class="formfield" />
		</td>
		<td class="row1" align="center">
			<input type="text" name="email<?php echo $i; ?>"  class="formfield" />
		</td>
	</tr>
	<tr><td height="1" class="row2" colspan="4"></td></tr>

	<?php }	?>
	<tr><td colspan="4" class="row1" align="center">
		<input type="submit" name="submit" value="<?php echo _AT('list_add_course_list');  ?>" class="button" /> |
		<input type="submit" class="button" name="cancel" value="<?php echo _AT('cancel'); ?>" />
	</td></tr>

	<tr><td height="1" class="row2" colspan="4"></td></tr>
	<tr><td class="row1" align="center" colspan="4"></td></tr>
	</table>
	</form>

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
		if (!$still_errors && ($_POST['submit'] == _AT('list_add_enrolled_list') || 
			$_POST['submit'] == _AT('list_add_unenrolled_list'))) {			
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
							$student['exists'] = _AT('import_err_email_exists');

							if ($_POST['submit'] == _AT('list_add_unenrolled_list')) {
								$sql = "INSERT INTO ".TABLE_PREFIX."course_enrollment (member_id, course_id, approved, last_cid, role) VALUES (LAST_INSERT_ID(), '".$course."', 'n', 0, '')";
							} else {
								$sql = "INSERT INTO ".TABLE_PREFIX."course_enrollment (member_id, course_id, approved, last_cid, role) VALUES (LAST_INSERT_ID(), '".$course."', 'y', 0, '')";
							}

							if($result = mysql_query($sql,$db)) {
								$enrolled_list .= '<li>'.$name.'</li>';
							} else {
								$$already_enrolled .= '<li>'.$name.'</li>';
							}

							// send email here.
							$subject = SITE_NAME.': '._AT('account_information');
							$body = SITE_NAME.': '._AT('account_information')."\n\n";
							$body .= _AT('new_account_msg', $_base_href.'login.php'). "\n\n";
							$body .= _AT('login_name') .' : '.$student['uname'] . "\n\n";
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
							if ($_POST['submit'] == _AT('list_add_unenrolled_list')) {
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
				$feedback[] = array(AT_FEEDBACK_ALREADY_ENROLLED, $already_enrolled);
			}
			if ($enrolled_list) {
				$feedback[] = array(AT_FEEDBACK_ENROLLED, $enrolled_list);
			}
			print_feedback($feedback);

			echo '<table align="center" cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="90%">';
			echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'" name="finalform" />';
			echo '<tr><td class="row1" align="center">';
			echo '<input type="submit" name="addmore" value="'._AT('add_more').'" class="button" /> | ';
			echo '<input type="submit" name="return"  value="'._AT('done').'" class="button" />';
			echo '</td></tr></table></form>';
		}

	} 
	if (!$_POST['verify'] || $still_errors || ($_POST['submit'] == _AT('resubmit'))) {
		
		
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
				echo '<td class="row1"><span style="color: red;">';

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
		
		echo '<tr><td class="row1" colspan="6" align="center"><input type="submit" name="submit" value="'._AT('resubmit').'" class="button" /> | ';
		
		if ($still_errors || $err_count>0) {	
			echo '<input type="submit" name="submit" value="'._AT('list_add_course_list').'" class="button" disabled="disabled" />';			
		} else {
			echo '<input type="submit" name="submit" value="'._AT('list_add_unenrolled_list').'" class="button" /> | ';
			echo '<input type="submit" name="submit" value="'._AT('list_add_enrolled_list').'" class="button" />';
		}
		
		echo '</td></tr></table>';
		echo '<input type="hidden" name="count" value="'.count($students).'" /></form>';
	} 	
} 

echo '<br /><br />';
require(AT_INCLUDE_PATH.'footer.inc.php');
?>