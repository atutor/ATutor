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
// $Id: module_news.php 10142 2010-08-17 19:17:26Z hwong $
if (!defined('AT_INCLUDE_PATH')) { exit; }
include_once(AT_INCLUDE_PATH.'../mods/_standard/social/lib/friends.inc.php');

/*
 * Get the latest updates of this module
 * @return list of news, [timestamp]=>
 */
function social_news() {
	global $db;
	$news = array();

	$actvity_obj = new Activity();
	$activities = $actvity_obj->getFriendsActivities($_SESSION['member_id']);
	foreach($activities as $row){
		$link_title = printSocialName($row['member_id']).' '. $row['title'];
		$news[] = array('time'=>$row['created_date'], 
						'object'=>$row, 
						'thumb'=>'images/home-directory_sm.png',
						'link'=>'<span title="'.strip_tags($link_title).'">'.$link_title."</span>");
	}
	return $news;
}

?>