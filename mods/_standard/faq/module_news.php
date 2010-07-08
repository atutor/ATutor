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
// $Id: module_news.php 9335 2010-02-11 16:29:01Z hwong $
/*
 * Get the latest updates of this module
 * @return list of news, [timestamp]=>
 */
function faq_news() {
	global $db, $enrolled_courses, $system_courses;
	$news = array();

	if ($enrolled_courses == ''){
		return $news;
	} 

	$sql = "SELECT * FROM ".TABLE_PREFIX."faq_topics T INNER JOIN ".TABLE_PREFIX."faq_entries E ON T.topic_id = E.topic_id WHERE T.course_id IN $enrolled_courses ORDER BY E.revised_date DESC";
	$result = mysql_query($sql, $db);
	if($result){
		while($row = mysql_fetch_assoc($result)){
			$news[] = array('time'=>$row['revised_date'], 
			'alt'=>_AT('faq'),'object'=>$row,
			'course'=>$system_courses[$row['course_id']]['title'], 
			'thumb'=>'images/home-faq_sm.png', 
			'link'=>'<a href="bounce.php?course='.$row['course_id'].'&p='.urlencode('mods/_standard/faq/index.php#'.$row['entry_id']).'"'.
			(strlen($row['question']) > SUBLINK_TEXT_LEN ? ' title="'.$row['question'].'"' : '') .'>'. 
			validate_length($row['question'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>');
		}
	}
	return $news;
}

?>