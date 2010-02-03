<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

if (!defined('AT_INCLUDE_PATH')) { exit; }

global $db;

$record_limit = 3;	// Number of sublinks to display for this module on course home page -> detail view

$sql = "SELECT G.group_id, G.title, G.modules FROM ".TABLE_PREFIX."groups G INNER JOIN ".TABLE_PREFIX."groups_types T USING (type_id) WHERE T.course_id=$_SESSION[course_id] ORDER BY G.title LIMIT $record_limit";
$result = mysql_query($sql, $db);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		if (strpos($row['modules'], '_standard/blogs') !== FALSE) {
			// retrieve the last posted date/time from this blog
			$sql = "SELECT MAX(date) AS date FROM ".TABLE_PREFIX."blog_posts WHERE owner_type=".BLOGS_GROUP." AND owner_id={$row['group_id']}";
			$date_result = mysql_query($sql, $db);
			if (($date_row = mysql_fetch_assoc($date_result)) && $date_row['date']) {
				$last_updated = ' - ' . _AT('last_updated', AT_date(_AT('forum_date_format'), $date_row['date'], AT_DATE_MYSQL_DATETIME));
			} else {
				$last_updated = '';
			}
	
			$link_title = $row['title'].$last_updated;
			$list[] = '<a href="'.url_rewrite('mods/_standard/blogs/view.php?ot='.BLOGS_GROUP.htmlentities(SEP).'oid='.$row['group_id'], AT_PRETTY_URL_IS_HEADER).'"'.
			          (strlen($link_title) > SUBLINK_TEXT_LEN ? ' title="'.$link_title.'"' : '') .'>'. 
			          validate_length($link_title, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>'; 
		}
	}
	return $list;
	
} else {
	return 0;
}
?>