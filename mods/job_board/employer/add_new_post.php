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
$all_categories = $job->getCategories();

//on submit
if(isset($_POST['submit'])){
	$job->addJob($_POST['jb_title'], $_POST['jb_description'], $_POST['jb_categories'], $_POST['jb_is_public'], $_POST['jb_closing_date']);
	$msg->addFeedback('JOB_POST_ADDED_SUCCESSFULLY');
	header('Location: home.php');
	exit;
}


include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('categories', $all_categories);
$savant->display('employer/jb_add_new_post.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
