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
 * 'edit_post' submenu to show on job_board/index.php
 */
$_pages[AT_JB_BASENAME.'index_admin.php']['children'] = array(AT_JB_BASENAME.'admin/edit_post.php');

$jid = intval($_GET['jid']);
$job = new Job();
$job_post = $job->getJob($jid);

//handle edit
if(isset($_POST['submit'])){
	$job->updateJob($jid, $_POST['jb_title'], $_POST['jb_description'], $_POST['jb_categories'], $_POST['jb_is_public'], $_POST['jb_closing_date'], $_POST['jb_approval_state']);
	$msg->addFeedback('UPDATED_SUCCESS');
	header('Location: home.php');
	exit;
}

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('job', $job);
$savant->assign('categories', $job->getCategories());
$savant->assign('job_post', $job_post);
$savant->display('employer/jb_edit_post.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
