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
	global $enrolled_courses, $system_courses;
	$news = array();

	if ($enrolled_courses == ''){
		return $news;
	} 

	$sql = "SELECT * FROM %spolls WHERE course_id IN %s ORDER BY created_date DESC";
	$rows_polls = queryDB($sql, array(TABLE_PREFIX, $enrolled_courses));

    if(count($rows_polls) > 0){
		    foreach($rows_polls as $row){
			$news[] = array('time'=>$row['created_date'], 
							'object'=>$row,
							'alt'=>_AT('polls'),
							'course'=>$system_courses[$row['course_id']]['title'],
							'thumb'=>'images/home-polls_sm.png',
							'link'=>'<a href="bounce.php?course='.$row['course_id'].SEP.'p='.urlencode('mods/_standard/polls/index.php#'.$row['poll_id']).'"'.
									(strlen($row['question']) > SUBLINK_TEXT_LEN ? ' title="'.AT_print($row['question'], 'polls.question').'"' : '') .'>'. 
									AT_print(validate_length($row['question'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'polls.question') .'</a>');
		}
	}
	return $news;
}

?>