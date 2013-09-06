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
function assignments_news() {
	global $db, $enrolled_courses, $system_courses;
	$news = array();

	if ($enrolled_courses == ''){
		return $news;
	} 

	$sql = 'SELECT * FROM %sassignments WHERE course_id IN %s ORDER BY date_due DESC';
	$rows_assignments = queryDB($sql, array(TABLE_PREFIX, $enrolled_courses));

	if(count($rows_assignments) > 0){
	    foreach($rows_assignments as $row){
			$news[] = array('time'=>$row['date_due'], 
							'object'=>$row, 
							'course'=>$system_courses[$row['course_id']]['title'],
							'alt'=>_AT('assignment'),
							'thumb'=>'images/home-forums_sm.png',
							'link'=>_AT('assignment_due', $row['title'], '<small>'.AT_DATE('%F %j, %g:%i',$row['date_due']).'</small>'));
		}
	}
	return $news;
}

?>
