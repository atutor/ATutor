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
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_JB_INCLUDE.'classes/Job.class.php');
$_custom_css = $_base_path . AT_JB_BASENAME . 'module.css'; // use a custom stylesheet

admin_authenticate(AT_ADMIN_PRIV_JOB_BOARD); 

//init
$job = new Job();
$categories = $job->getCategories();

//handle submit
if (isset($_REQUEST['submit'])){
	if($_POST['submit']=='ajax' && $_POST['action']=='edit'){
		//handle edit.
		$cid = intval($_POST['cid']);		//category id
		$name = $_POST['category_name'];	//category name
		$job->updateCategory($cid, $name);
		exit;	//ajax exit;
	} elseif($_POST['action']=='add'){
		//handle add
		$name = $_POST['category_name'];	//category name
		$job->addCategory($name);
		$categories = $job->getCategories();	//refresh the list.		
	} elseif ($_GET['action']=='delete'){
		//handle delete
		$cid = intval($_GET['cid']);		//category id
		$hidden_vars['cid'] = $cid;
		$this_category_name = $job->getCategoryNameById($cid);
		$msg->addConfirm(array('DELETE', $this_category_name), $hidden_vars);
	}
}

//handle delete confirmation
if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: categories.php');
	exit;
} else if (isset($_POST['submit_yes'])) {
	$cid = intval($_POST['cid']);
	$job->removeCategory($cid);
	$msg->addFeedback('JB_CATEGORY_DELETED');
	header('Location: categories.php');
	exit;
}

include(AT_INCLUDE_PATH.'header.inc.php');
$msg->printConfirm();
$savant->assign('categories', $categories);
$savant->display('admin/jb_categories.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>