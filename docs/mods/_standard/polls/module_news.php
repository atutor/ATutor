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
/*
 * Get the latest updates of this module
 * @return list of news, [timestamp]=>
 */
function polls_news() {
	global $db, $enrolled_courses, $system_courses;
	$news = array();

	if ($enrolled_courses == ''){
		return $news;
	} 

	$sql = 'SELECT * FROM '.TABLE_PREFIX.'polls WHERE course_id IN'.$enrolled_courses.' ORDER BY created_date DESC';
	$result = mysql_query($sql, $db);
	if($result){
		while($row = mysql_fetch_assoc($result)){
			$news[] = array('time'=>$row['created_date'], 
							'object'=>$row,
							'alt'=>_AT('polls'),
							'course'=>$system_courses[$row['course_id']]['title'],
							'thumb'=>'images/home-polls_sm.png',
							'link'=>'<a href="bounce.php?course='.$row['course_id'].'&p='.urlencode('mods/_standard/polls/index.php#'.$row['poll_id']).'"'.
									(strlen($row['question']) > SUBLINK_TEXT_LEN ? ' title="'.$row['question'].'"' : '') .'>'. 
									validate_length($row['question'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>');
		}
	}
	return $news;
}

?>