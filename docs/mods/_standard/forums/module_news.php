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
// $Id: module_news.php 9335 2010-02-11 16:29:01Z hwong $
/*
 * Get the latest updates of this module
 * @return list of news, [timestamp]=>
 */
function forums_news() {
	require_once(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php');
	global $db;
	$news = array();
	$all_forums = get_forums($_SESSION['course_id']);
	if (is_array($all_forums)){
		foreach($all_forums as $forums){
			if (is_array($forums)){
				foreach ($forums as $forum_obj){
					$news[] = array('time'=>$row['last_post'], 'object'=>$row);
				}
			}
		}
	}
	return $news;
}

?>