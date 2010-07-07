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

define(AT_INCLUDE_PATH, '../../../include/');
include(AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_JB_INCLUDE.'classes/Employer.class.php');
$_custom_css = $_base_path . AT_JB_BASENAME . 'module.css'; // use a custom stylesheet

admin_authenticate(AT_ADMIN_PRIV_JOB_BOARD);

/* 
 * Add the submenu on this page so that user can go back to the listing.
 * Reason why this is not included in module.php is because we don't want the 
 * 'edit_post' submenu to show on job_board/index.php
 */
$_pages[AT_JB_BASENAME.'admin/employers.php']['children'] = array(AT_JB_BASENAME.'admin/edit_employer.php');

//init
$employer = new Employer($_GET['eid']);

//save profile changes
if ($_POST['submit']){
	$name = $_POST['jb_employer_name'];
	$pass = $_POST['jb_employer_password_hidden'];
	$company = $_POST['jb_employer_company'];
	$email = $_POST['jb_employer_email'];
	$email2 = $_POST['jb_employer_email2'];
	$website = $_POST['jb_employer_website'];
	$approval_state = $_POST['jb_employer_approval_state'];

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

	//set approval state
	$employer->setApprovalState($approval_state);

	if ($employer->updateProfile($name, $company, $email, $website)){
		$msg->addFeedback('PROFILE_UPDATED');
	} else {
		$msg->addError('DB_NOT_UPDATED');
	}
	header('Location: employers.php');
	exit;	
}

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('name', $employer->getName());
$savant->assign('company', $employer->getCompany());
$savant->assign('email', $employer->getEmail());
$savant->assign('website', $employer->getWebsite());
$savant->assign('approval_state', $employer->getApprovalState());
$savant->display('admin/jb_edit_employer.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
