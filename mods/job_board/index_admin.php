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

//init
$job = new Job();
$page = intval($_GET['p']);
$page = ($page==0)?1:$page;
$all_job_posts = $job->getAllJobs($_GET['col'], $_GET['order'], true);

//handle pages
if ($page > 0){
	$offset = ($page - 1) * AT_JB_ROWS_PER_PAGE;
} else {
	$offset = 0;
}
$current_job_posts = array_slice($all_job_posts, $offset, AT_JB_ROWS_PER_PAGE);

//handle order
if ($_GET['order']==''){
	$order = 'DESC';
} else {
	//flip the ordre
	$order = ($_GET['order']=='ASC')?'DESC':'ASC';
	$page_string = 'col='.$_GET['col'].SEP.'order='.$_GET['order'];
}

include(AT_INCLUDE_PATH.'header.inc.php');
echo '<div class="pageinator_box">';
print_paginator($page, sizeof($all_job_posts), $page_string, AT_JB_ROWS_PER_PAGE);
echo '</div>';
$savant->assign('job_obj', $job);
$savant->assign('job_posts', $current_job_posts);
$savant->display('admin/jb_index.tmpl.php');
print_paginator($page, sizeof($all_job_posts), $page_string, AT_JB_ROWS_PER_PAGE);
include(AT_INCLUDE_PATH.'footer.inc.php'); 
?>
