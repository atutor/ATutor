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
function tests_news() {
	global $enrolled_courses, $system_courses;
	$news = array();

	if ($enrolled_courses == ''){
		return $news;
	} 

	$sql = "SELECT T.test_id, T.course_id, T.title, T.start_date as start_date, UNIX_TIMESTAMP(T.start_date) AS sd, UNIX_TIMESTAMP(T.end_date) AS ed 
          FROM %stests T, %stests_questions_assoc Q 
         WHERE Q.test_id=T.test_id 
           AND T.course_id IN %s 
         GROUP BY T.test_id 
         ORDER BY T.start_date DESC";
	$rows_tests = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $enrolled_courses));

	if(count($rows_tests) > 0){
	    foreach($rows_tests as $row){
			//show only the visible tests
			if ( ($row['sd'] <= time()) && ($row['ed'] >= time())){
				$news[] = array('time'=>$row['start_date'], 
								'object'=>$row,
								'alt'=>_AT('tests'),
								'course'=>$system_courses[$row['course_id']]['title'],
								'thumb'=>'images/home-tests_sm.png',
								'link'=>'<a href="bounce.php?course='.$row['course_id'].SEP.'p='.urlencode('mods/_standard/tests/test_intro.php?tid='.$row['test_id']).'" '
										.(strlen($row['title']) > SUBLINK_TEXT_LEN ? ' title="'.$row['title'].'"' : '') .'>'
										.validate_length($row['title'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a> <small>('._AT('start_date').':'.AT_DATE('%F %j, %g:%i',$row['start_date']).')</small>');
			}
		}
	}
	return $news;
}

?>