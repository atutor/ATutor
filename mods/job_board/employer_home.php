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
// $Id: index.php 9941 2010-05-21 17:24:29Z hwong $

define(AT_INCLUDE_PATH, '../../include/');
include(AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_JB_INCLUDE.'classes/Job.class.php');
$_custom_css = $_base_path . AT_JB_BASENAME . 'module.css'; // use a custom stylesheet

//TODO: If not authenticated with user login, quit.

$job = new Job();
$all_job_posts = $job->getMyJobs();

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('all_job_posts', $all_job_posts);
$savant->display('jb_employer_home.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
