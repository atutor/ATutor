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

define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
include(AT_JB_INCLUDE.'classes/Job.class.php');
include(AT_JB_INCLUDE.'classes/Employer.class.php');

admin_authenticate(AT_ADMIN_PRIV_JOB_BOARD); 

$job = new Job();
$page = intval($_GET['p']);
$page = ($page==0)?1:$page;
$all_job_posts = $job->getAllJobs(true);

if ($page > 0){
	$offset = ($page - 1) * AT_JB_ROWS_PER_PAGE;
} else {
	$offset = 0;
}
$current_job_posts = array_slice($all_job_posts, $offset, AT_JB_ROWS_PER_PAGE);

include(AT_INCLUDE_PATH.'header.inc.php');
print_paginator($page, sizeof($all_job_posts), $_SERVER['QUERY_STRING'], AT_JB_ROWS_PER_PAGE);
$savant->assign('job_obj', $job);
$savant->assign('job_posts', $current_job_posts);
$savant->display('admin/jb_index.tmpl.php');
print_paginator($page, sizeof($all_job_posts), $_SERVER['QUERY_STRING'], AT_JB_ROWS_PER_PAGE);
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
