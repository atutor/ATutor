<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institute	   */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$
$_user_location='public';
define(AT_INCLUDE_PATH, '../../../include/');
include(AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_JB_INCLUDE.'classes/Job.class.php');
$_custom_css = $_base_path . AT_JB_BASENAME . 'module.css'; // use a custom stylesheet

$job = new Job();

//handle registration
//todo: handle spam.
if(isset($_POST['submit'])){
	$email = $_POST['jb_registration_email'];
	$username = $_POST['jb_registration_username'];
	$company = $_POST['jb_registration_company'];
	$employer_name = $_POST['jb_registration_employer_name'];
	$website = $_POST['jb_registration_website'];
	$description = $_POST['jb_registration_description'];
	$password = $_POST['jb_registration_password_hidden'];
	$noerror = true;

	if ($_POST['jb_registration_password_error'] != ''){
		$errors = explode(',', $_POST['jb_registration_password_error']);
		if(sizeof($errors) > 0){
			foreach($errors as $err){
				$msg->addError($err);
			}
		}
		$noerror = false;
	}
	
	// these fields cannot be empty
	if($email=='' || $username=='' || $company=='' || $employer_name==''){
		$msg->addError('JB_MISSING_FIELDS');
		$noerror = false;
	} 

	// email, username taken	
	$sql = 'SELECT COUNT(*) FROM '.TABLE_PREFIX."jb_employers WHERE username='$username' OR email='$email'";
	$result = mysql_query($sql, $db);	
	if ($result){
		$row = mysql_fetch_row($result);
		if ($row[0] > 0){
			$msg->addError('JB_EXISTING_INFO');
			$noerror = false;
		}
	}
		
	if ($noerror){
		//no error
		$now = date('Y-m-d H:i:s'); // we use this later for the email confirmation.
		$e_id = $job->addEmployerRequest($username, $password, $employer_name, $email, $company, $description, $now, $website);
		
		//sends out confirmation email.
		$code = substr(md5($email . $now . $e_id), 0, 10);		
		$confirmation_link = $_base_href . AT_JB_BASENAME . 'confirm.php?id='.$e_id.SEP.'m='.$code;

		/* send the email confirmation message: */
		require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
		$mail = new ATutorMailer();

		$mail->From     = $_config['contact_email'];
		$mail->AddAddress($email);
		$mail->Subject = SITE_NAME . ' - ' . _AT('jb_email_confirmation_subject');
		$mail->Body    = _AT('jb_email_confirmation_message', SITE_NAME, $confirmation_link);
		$mail->Send();

		header('Location: ../index.php');
		exit;
	}


}

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('employer/jb_registration.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
