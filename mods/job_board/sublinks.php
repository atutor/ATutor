<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2010                                             */
/* Inclusive Design Institute	                                       */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }
include(AT_JB_INCLUDE.'classes/Job.class.php');

global $db;

//init
$link_limit = 3;
$cnt = 0;
$job = new Job();

$result = $job->getAllJobs('created_date', 'desc');

if (is_array($result)) {
	foreach($result as $row){
		if ($cnt >= $link_limit) break;
		$cnt++;

		$title = htmlentities_utf8($row['title']);
		$list[] = '<span title="'.strip_tags($title).'">'.'<a href="'.$_base_href.'job_board/view_post.php?jid='.$row['id'].'">'.$title.'</a></span>';
	}
	return $list;
} else {
	return 0;
}

?>