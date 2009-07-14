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

$sql = "SELECT C.member_id, M.login FROM ".TABLE_PREFIX."course_enrollment C, ".TABLE_PREFIX."members M	WHERE C.course_id=$_SESSION[course_id] AND C.member_id=M.member_id AND (C.approved='y' OR C.approved='a') limit ".$record_limit;
$result = mysql_query($sql, $db);

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		$list[] = '<a href="'.url_rewrite('profile.php?id='.$row['member_id'], AT_PRETTY_URL_IS_HEADER).'"'.
		          (strlen($row['login']) > SUBLINK_TEXT_LEN ? ' title="'.$row['login'].'"' : '') .'>'. 
		          validate_length($row['login'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>'; 
	}
	return $list;
	
} else {
	return 0;
}


?>