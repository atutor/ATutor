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
	global $db, $enrolled_courses;
	$news = array();

	if ($enrolled_courses == ''){
		return $news;
	} 

	$sql = 'SELECT E.approved, E.last_cid, C.* FROM AT_course_enrollment E, AT_courses C WHERE E.member_id=1 AND E.course_id=C.course_id ORDER BY C.title';
	$result = mysql_query($sql, $db);
	if ($result) {
		while($row = mysql_fetch_assoc($result)){
			$all_forums = get_forums($row['course_id']);
			if (is_array($all_forums)){
				foreach($all_forums as $forums){
					if (is_array($forums)){
						foreach ($forums as $forum_obj){
							$link_title = $forum_obj['title'].' ('.AT_DATE('%F %j, %g:%i',$forum_obj['last_post'],AT_DATE_MYSQL_DATETIME).')';
							$news[] = array('time'=>$forum_obj['last_post'], 'object'=>$forum_obj, 'thumb'=>'images/home-forums_sm.png', 'link'=>'<a href="bounce.php?course='.$row['course_id'].'&p='.urlencode('mods/_standard/forums/forum/index.php?fid='.$forum_obj['forum_id']).'"'.
							  (strlen($link_title) > SUBLINK_TEXT_LEN ? ' title="'.$link_title.'"' : '') .'>'. 
							  validate_length($link_title, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>');
						}
					}
				}
			}
		}
	}
	return $news;
}

?>