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
include(AT_JB_INCLUDE.'classes/Employer.class.php');
$_custom_css = $_base_path . AT_JB_BASENAME . 'module.css'; // use a custom stylesheet

if (!Employer::authenticate()){
	$msg->addError('ACCESS_DENIED');
	header('Location: ../index.php');
	exit;
}

//init
$employer = new Employer($_SESSION['jb_employer_id']);

//save profile changes
if ($_POST['submit']){
	$name = $_POST['jb_employer_name'];
	$pass = $_POST['jb_employer_password_hidden'];
	$company = $_POST['jb_employer_company'];
	$email = $_POST['jb_employer_email'];
	$email2 = $_POST['jb_employer_email2'];
	$website = $_POST['jb_employer_website'];
    $description = $_POST['jb_employer_description'];

	//check if email has been changed.  If it's been changed, check the 2 emails.
	if ($email!=$employer->getEmail()){
		if ($email!=$email2){
			$msg->addError('EMAIL_MISMATCH');
			header('Location: profile.php');
			exit;
		}
	}
	
	//check js errors
	if ($_POST['jb_employer_password_error'] != ''){
		$errors = explode(',', $_POST['jb_employer_password_error']);
		if(sizeof($errors) > 0){
			foreach($errors as $err){
				$msg->addError($err);
			}
		}
		header('Location: profile.php');
		exit;
	}

	//update password
	if ($pass!='' && strlen($pass)==40){
	    $employer->updatePassword($pass);
	} 

	if ($employer->updateProfile($name, $company, $email, $website, $description)){
		$msg->addFeedback('JB_PROFILE_UPDATED');
	} else {
		$msg->addFeedback('DB_NOT_UPDATED');
	}
	header('Location: profile.php');
	exit;	
}

include(AT_INCLUDE_PATH.'header.inc.php');
$msg->printConfirm();
echo '<div class="pageinator_box">';
$savant->display('employer/jb_employer_header.tmpl.php');
echo '</div>';
$savant->assign('name', $employer->getName());
$savant->assign('company', $employer->getCompany());
$savant->assign('email', $employer->getEmail());
$savant->assign('website', $employer->getWebsite());
$savant->assign('description', $employer->getDescription());
$savant->display('employer/jb_profile.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
