<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$
/*
 * Get the latest updates of this module
 * @return list of news, [timestamp]=>
 */
 
 // 
 //exit;
 
function reading_list_news() {
global $enrolled_courses, $system_courses;
	$news = array();
    if(isset($enrolled_courses)){
	$sql = "SELECT * FROM %sreading_list R INNER JOIN %sexternal_resources E ON E.resource_id = R.resource_id WHERE R.course_id in %s ORDER BY R.reading_id DESC";
	$rows_resources = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $enrolled_courses));

	if(count($rows_resources) > 0){
	    foreach($rows_resources as $row){
			$news[] = array('time'=>$row['date_end'], 
							'object'=>$row,
							'alt'=>_AT('reading_list'),
							'course'=>$system_courses[$row['course_id']]['title'],
							'thumb'=>'images/home-reading_list_sm.png',
							'link'=>'<a href="bounce.php?course='.$row['course_id'].SEP.'p=mods/_standard/reading_list/display_resource.php?id=' . $row['resource_id']
							.'"'.(strlen($row['title']) > SUBLINK_TEXT_LEN ? ' title="'.$row['title'].'"' : '') .'>'. 
									validate_length($row['title'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>');
		}	
	}
	return $news;
	}
}
?>