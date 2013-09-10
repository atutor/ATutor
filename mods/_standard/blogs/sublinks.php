<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

if (!defined('AT_INCLUDE_PATH')) { exit; }

global $db;

$record_limit = 3;	// Number of sublinks to display for this module on course home page -> detail view

$sql = "SELECT G.group_id, G.title, G.modules FROM %sgroups G INNER JOIN %sgroups_types T USING (type_id) WHERE T.course_id=%d ORDER BY G.title LIMIT %d";
$rows_groups = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id'], $record_limit));

if(count($rows_groups)){
    foreach($rows_groups as $row){
		if (strpos($row['modules'], '_standard/blogs') !== FALSE) {
			// retrieve the last posted date/time from this blog
			$sql = "SELECT MAX(date) AS date FROM %sblog_posts WHERE owner_type=%d AND owner_id=%d";
			$date_row = queryDB($sql, array(TABLE_PREFIX, BLOGS_GROUP, $row['group_id']), TRUE);
			
			if(count($date_row) > 0){
				$last_updated = ' - ' . _AT('last_updated', AT_date(_AT('forum_date_format'), $date_row['date'], AT_DATE_MYSQL_DATETIME));
			} else {
				$last_updated = '';
			}
	
			$link_title = $row['title'].$last_updated;
			$list[] = '<a href="'.url_rewrite('mods/_standard/blogs/view.php?ot='.BLOGS_GROUP. SEP .'oid='.$row['group_id'], AT_PRETTY_URL_IS_HEADER).'"'.
			          (strlen($link_title) > SUBLINK_TEXT_LEN ? ' title="'.AT_print($link_title, 'blog_posts.title').'"' : '') .'>'. 
			          AT_print(validate_length($link_title, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'blog_posts.title') .'</a>'; 
		}
	}
	return $list;
	
} else {
	return 0;
}
?>