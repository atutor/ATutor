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

$jid = intval($_GET['jid']);
$job = new Job();
$job_post = $job->getJob($jid);


include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('job_obj', $job);
$savant->assign('job_post', $job_post);
$savant->display('jb_view_post.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
