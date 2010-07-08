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

/*
 * Get the latest updates of this module
 * @return list of news, [timestamp]=>
 */
function job_board_news() {
	global $db;
	$news = array();
	$job = new Job();

	$result = $job->getAllJobs('created_date', 'desc');

	if(is_array($result)){
		foreach ($result as $row){
			$title = htmlentities_utf8($row['title']);

			$news[] = array('time'=>$row['revised_date'], 
							'object'=>$row, 
							'thumb'=>AT_JB_BASENAME.'images/jb_icon_tiny.png',
							'link'=>'<span title="'.strip_tags($title).'"><a href="'.AT_JB_BASENAME.'view_post.php?jid='.$row['id'].'">'.$title."</a></span>");
		}
	}
	return $news;
}

?>