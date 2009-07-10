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
global $_base_path;

$record_limit = 3;	// Number of sublinks to display for this module on course home page -> detail view

$sql = "SELECT * FROM ".TABLE_PREFIX."glossary WHERE course_id = $_SESSION[course_id] LIMIT $record_limit";
$result = mysql_query($sql, $db);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		$list[] = '<a href="'.AT_BASE_HREF.url_rewrite('glossary/index.php?w='.urlencode($row['word']).'#term').'"'.
		          (strlen($row['word']) > SUBLINK_TEXT_LEN ? ' title="'.$row['word'].'"' : '') .'>'. 
		          validate_length($row['word'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>'; 
	}
	return $list;
	
} else {
	return 0;
}


?>