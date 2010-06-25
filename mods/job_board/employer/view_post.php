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

/* 
 * Add the submenu on this page so that user can go back to the listing.
 * Reason why this is not included in module.php is because we don't want the 
 * 'view_post' submenu to show on job_board/index.php
 */
$_pages[AT_JB_BASENAME.'employer/home.php']['children'] = array(AT_JB_BASENAME.'employer/view_post.php');

$jid = intval($_REQUEST['jid']);
$job = new Job();
$job_post = $job->getJob($jid);

if($_GET['action']=='delete'){
	$hidden_vars['jid'] = $jid;
	$job_post = $job->getJob($jid);
	$msg->addConfirm(array('DELETE', $job_post['title']), $hidden_vars);
}
//handle delete 
if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: home.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$job->removeJob($jid);
	$msg->addFeedback('JB_POST_DELETED');
	header('Location: home.php');
	exit;
}

include(AT_INCLUDE_PATH.'header.inc.php');
$msg->printConfirm();
$savant->assign('job_obj', $job);
$savant->assign('job_post', $job_post);
$savant->display('jb_view_post.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
