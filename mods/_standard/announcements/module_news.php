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
function announcements_news() {
	global $db, $enrolled_courses, $system_courses;
	$news = array();

	if ($enrolled_courses == ''){
		return $news;
	} 

	$sql = 'SELECT * FROM %snews WHERE course_id IN %s ORDER BY date DESC';
	$rows_news = queryDB($sql, array(TABLE_PREFIX, $enrolled_courses));

	if(count($rows_news) > 0){
		foreach($rows_news as $row){
			$news[] = array('time'=>$row['date'], 
							'object'=>$row, 
							'alt'=>_AT('announcements'),
							'course'=>$system_courses[$row['course_id']]['title'],
							'thumb'=>'images/flag_blue.png',
							'link'=>$row['body']);
		}
	}
	return $news;
}

?>