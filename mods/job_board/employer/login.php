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
include(AT_JB_INCLUDE.'classes/Job.class.php');
$_custom_css = $_base_path . AT_JB_BASENAME . 'module.css'; // use a custom stylesheet

$job = new Job();
$all_job_posts = $job->getAllJobs();

//Check the form username and pwd
if (isset($_POST['submit']) && $_POST['submit']!=''){
	$job_login		= $addslashes($_POST['form_login']);

	$sql = 'SELECT id, password FROM '.TABLE_PREFIX."jb_employers WHERE username='$job_login'";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	//if enc(a x s) = enc(b x s), then valid
	if (sha1($addslashes($row['password']).$_SESSION['token']) == $_POST['form_password_hidden']){
		$_SESSION['jb_employer_id'] = 1;
		//if succeeded
		$msg->addFeedback('LOGIN_SUCCESS');
		header('Location: home.php');
		exit;
	} else {
		$msg->addError('INVALID_LOGIN');
	}    
}

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->display('employer/jb_login.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
