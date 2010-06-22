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

define(AT_INCLUDE_PATH, '../../include/');
include(AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_JB_INCLUDE.'classes/Job.class.php');
$_custom_css = $_base_path . AT_JB_BASENAME . 'module.css'; // use a custom stylesheet

//init
$job = new Job();
$categories = $job->getCategories();
$subscribed = $job->getSubscribedCategories($_SESSION['member_id']);

//handle save
if (isset($_POST['submit'])){
	$token = sha1($_SESSION['member_id'].$_SESSION['token']);
	//validate if this is a post from the user himself but not anyone else.
	if ($_POST['token'] != $token){
		$msg->addError();
		header('Location: index.php');
		exit;
	}
	$job->subscribeCategories($_SESSION['member_id'], $_POST['jb_subscribe_categories']);
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: subscribe.php');
	exit;
}


include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('categories', $categories);
$savant->assign('job_obj', $job);
$savant->assign('subscribed', $subscribed);
$savant->display('jb_subscribe.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>