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

/* 
 * Add the submenu on this page so that user can go back to the listing.
 * Reason why this is not included in module.php is because we don't want the 
 * 'view_post' submenu to show on job_board/index.php
 */
$_pages[AT_JB_BASENAME.'index.php']['children'] = array(AT_JB_BASENAME.'view_post.php');

$jid = intval($_REQUEST['jid']);
$job = new Job();
$job_post = $job->getJob($jid);

//handle add to cart event
if ($_GET['action']=='add_to_cart'){
	$job->addToJobCart($_SESSION['member_id'], $jid);
} elseif ($_GET['action']=='remove_from_cart'){
	$job->removeFromJobCart($_SESSION['member_id'], $jid);
}

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('job_obj', $job);
$savant->assign('job_post', $job_post);
$savant->display('jb_view_post.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
