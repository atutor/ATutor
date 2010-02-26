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
function tests_news() {
	global $db;
	$news = array();

	$sql = "SELECT T.test_id, T.title, T.end_date as end_date, UNIX_TIMESTAMP(T.start_date) AS sd, UNIX_TIMESTAMP(T.end_date) AS ed 
          FROM ".TABLE_PREFIX."tests T, ".TABLE_PREFIX."tests_questions_assoc Q 
         WHERE Q.test_id=T.test_id 
           AND T.course_id=$_SESSION[course_id] 
         GROUP BY T.test_id 
         ORDER BY T.end_date DESC";
	$result = mysql_query($sql, $db);
	if($result){
		while($row = mysql_fetch_assoc($result)){
			//show only the visible tests
			if ( ($row['sd'] <= time()) && ($row['ed'] >= time())){
				$news[] = array('time'=>$row['end_date'], 'object'=>$row);
			}
		}
	}
	return $news;
}

?>