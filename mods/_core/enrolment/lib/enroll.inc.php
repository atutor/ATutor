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
// $Id$

function checkUserInfo($record) {
	global $db, $addslashes;
	static $email_list;

	if (empty($record['remove'])) {
		$record['remove'] = FALSE;			
	}

	//error flags for this record
	$record['err_email'] = FALSE;
	$record['err_uname'] = FALSE;
	$record['exists']    = FALSE;

	$record['email'] = trim($record['email']);

	/* email check */
	if ($record['email'] == '') {
		$record['err_email'] = _AT('import_err_email_missing');
	} else if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $record['email'])) {
		$record['err_email'] = _AT('import_err_email_invalid');
	} else if (isset($email_list[$record['email']])) {
		$record['err_email'] = _AT('import_err_email_exists');
	} else {
		$record['email'] = $addslashes($record['email']);

		$sql="SELECT * FROM %smembers WHERE email LIKE '%s'";
		$rows_members = queryDB($sql,array(TABLE_PREFIX, $record['email']), TRUE);
		if(count($rows_members) > 0){
			$record['exists'] = _AT('import_err_email_exists');
			$record['fname']  = $rows_members['first_name']; 
			$record['lname']  = $rows_members['last_name'];
			$record['email']  = $rows_members['email'];
			$record['uname']  = $rows_members['login'];
			$record['status'] = $rows_members['status'];
		} else {
			// it's good, add it to the list
			$email_list[$record['email']] = true;
		}
	}

	/* username check */
	if (empty($record['uname'])) {
		$record['uname'] = stripslashes (strtolower (substr ($record['fname'], 0, 1).$_POST['sep_choice'].$record['lname']));
	} 		

	$record['uname'] = preg_replace("{[^a-zA-Z0-9._-]}","", trim($record['uname']));

	if (!(preg_match("/^[a-zA-Z0-9._-]([a-zA-Z0-9._-])*$/i", $record['uname']))) {
		$record['err_uname'] = _AT('import_err_username_invalid');
	} 

	if (isset($record['status']) && $record['status'] == AT_STATUS_DISABLED) {
		$record['err_disabled'] = true;
	} else {
		$record['err_disabled'] = false;
	}

	$record['uname'] = $addslashes($record['uname']);
	$record['fname'] = $addslashes($record['fname']);
	$record['lname'] = $addslashes($record['lname']);

	$sql = "SELECT member_id FROM %smembers WHERE login='%s'";
	$rows_members = queryDB($sql,array(TABLE_PREFIX, $record['uname']),TRUE);
	if(count($rows_members) > 0 && !$record['exists']){
		$record['err_uname'] = _AT('import_err_username_exists');
	} else {
		$rows_admins = queryDB("SELECT * FROM %sadmins WHERE login='%s'", array(TABLE_PREFIX, $record['uname']), TRUE);
		if (count($rows_admins) != 0) {
			$record['err_uname'] = _AT('import_err_username_exists');
		}
	}	

    // This prevent CVS import course list when a person with the same name exists.
    /*******	
	$sql = "SELECT member_id FROM %smembers WHERE first_name='%s' AND last_name='%s' LIMIT 1";
	$rows_members = queryDB($sql, array(TABLE_PREFIX, $record['fname'], $record['lname']), TRUE);
	if(count($rows_members) != 0 && !$record['exists']){
		$record['err_uname'] = _AT('import_err_full_name_exists');
	}
    ******/
	/* removed record? */
	if ($record['remove']) {
		//unset errors 
		$record['err_email'] = '';
		$record['err_uname'] = '';
		$record['err_disabled'] = '';
	}

	$record['fname'] = htmlspecialchars(stripslashes(trim($record['fname'])));
	$record['lname'] = htmlspecialchars(stripslashes(trim($record['lname'])));
	$record['email'] = htmlspecialchars(stripslashes(trim($record['email'])));
	$record['uname'] = htmlspecialchars(stripslashes(trim($record['uname'])));

	return $record;
}

function add_users($user_list, $enroll, $course) {
	global $db;
	global $msg;
	global $_config;
	global $addslashes;

	require_once(AT_INCLUDE_PATH.'classes/phpmailer/atutormailer.class.php');

	if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION) {
		$status = AT_STATUS_UNCONFIRMED;
	} else {
		$status = AT_STATUS_STUDENT;
	}

	foreach ($user_list as $student) {
		if ($student['remove']  == '')  {
				$student['uname'] = $addslashes($student['uname']);
				$student['email'] = $addslashes($student['email']);
				$student['fname'] = $addslashes($student['fname']);
				$student['lname'] = $addslashes($student['lname']);

			if ($student['exists'] == '') {
				$sql = "INSERT INTO %smembers 
				              (login,
				               password,
				               email,
				               first_name,
				               last_name,
				               gender,
				               status,
				               preferences,
				               creation_date,
				               language,
				               inbox_notify,
				               private_email)
				              VALUES 
				              ('$student[uname]',
				               '". sha1($student[uname]). "',
				               '$student[email]',
				               '$student[fname]',
				               '$student[lname]',
				               'n', 
				               $status, 
				               '$_config[pref_defaults]', 
				               NOW(),
				               '$_config[default_language]', 
				               0, 
				               1)";

				$result = queryDB($sql,array(TABLE_PREFIX));
				if ($result == 1) {
                    $m_id = at_insert_id();
					$student['exists'] = _AT('import_err_email_exists');
                    $role = "Student";
			        $sql = "INSERT INTO %scourse_enrollment (member_id, course_id, approved, last_cid, role) VALUES (%d, %d, '%s', 0, '%s')";
                    $result = queryDB($sql, array(TABLE_PREFIX, $m_id, $course, $enroll, $role));
                    if($result > 0){
                    
						$enrolled_list .= '<li>' . $student['uname'] . '</li>';

						if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION) {

							$sql    = "SELECT email, creation_date FROM %smembers WHERE member_id=%d";
							$row    = queryDB($sql, array(TABLE_PREFIX, $m_id), TRUE);

							$code   = substr(md5($row['email'] . $row['creation_date'] . $m_id), 0, 10);

							// send email here.
							$confirmation_link = AT_BASE_HREF . 'confirm.php?id='.$m_id.SEP.'m='.$code;
			
							$subject = $_config['site_name'].': '._AT('email_confirmation_subject');
							$body = _AT(array('new_account_enroll_confirm', $_SESSION['course_title'], $confirmation_link))."\n\n";
						} else {
							$subject = $_config['site_name'].': '._AT('account_information');
							$body = _AT(array('new_account_enroll',AT_BASE_HREF, $_SESSION['course_title']))."\n\n";
						}
						
						//$body .= SITE_NAME.': '._AT('account_information')."\n";
						$body .= _AT('web_site') .' : '.AT_BASE_HREF."\n";
						$body .= _AT('login_name') .' : '.$student['uname'] . "\n";
						$body .= _AT('password') .' : '.$student['uname'] . "\n";

						$mail = new ATutorMailer;
						$mail->From     = $_config['contact_email'];
						$mail->AddAddress($student['email']);
						$mail->Subject = $subject;
						$mail->Body    = $body;
						$mail->Send();

						unset($mail);
					} else {
						$already_enrolled .= '<li>' . $student['uname'] . '</li>';
					}
				} else {
					//$msg->addError('LIST_IMPORT_FAILED');	
				}
			} else if ($student['err_disabled'] == '') {
				$sql = "SELECT member_id FROM %smembers WHERE email='%s'";
				$rows_members = queryDB($sql, array(TABLE_PREFIX, $student['email']), TRUE);
                $role = "Student";
                if(count($rows_members) >0){
				    $row = $rows_members;
					$m_id = $row['member_id'];
					$sql = "SELECT member_id FROM %smembers WHERE member_id =".$m_id;
					$result = queryDB($sql, array(TABLE_PREFIX, $m_id), TRUE);
					
                    if(!is_array($result)){
					    $sql = "INSERT INTO %scourse_enrollment (member_id, course_id, approved, last_cid, role) VALUES (%d, %d, '%s', 0, '%s')";
                        $result = queryDB($sql, array(TABLE_PREFIX, $m_id, $course, $enroll, $role));
						$enrolled_list .= '<li>' . $student['uname'] . '</li>';
					} else {
						$sql = "REPLACE INTO %scourse_enrollment (member_id, course_id, approved, last_cid, role) VALUES (%d, %s, '%s', 0, '%s')";
						$result = queryDB($sql, array(TABLE_PREFIX, $m_id, $course, $enroll, $role));
						$enrolled_list .= '<li>' . $student['uname'] . '</li>';
					}
				$subject = $_config['site_name'].': '._AT('email_confirmation_subject');
				$body = _AT(array('enrol_message_approved',$_SESSION['course_title'],AT_BASE_HREF))."\n\n";
				$body .= _AT('web_site') .' : '.AT_BASE_HREF."\n";
				$body .= _AT('login_name') .' : '.$student['uname'] . "\n";
				$mail = new ATutorMailer;
				$mail->From     = $_config['contact_email'];
				$mail->AddAddress($student['email']);
				$mail->Subject = $subject;
				$mail->Body    = $body;
				$mail->Send();

				unset($mail);

				}

			} else if ($student['err_disabled'] != '') {
				$not_enrolled_list .= '<li>' . $student['uname'] . '</li>';
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
	if ($not_enrolled_list) {
		$feedback = array('NOT_ENROLLED', $not_enrolled_list);
		$msg->addFeedback($feedback);
	}
}

?>