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

$job = new Job();
$all_job_posts = $job->getAllJobs();

include(AT_INCLUDE_PATH.'header.inc.php');
$savant->assign('all_job_posts', $all_job_posts);
$savant->display('jb_posting_table.tmpl.php');
include(AT_INCLUDE_PATH.'footer.inc.php');
?>