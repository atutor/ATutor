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

$sql = "SELECT C.member_id, M.login FROM %scourse_enrollment C, %smembers M	WHERE C.course_id=%d AND C.member_id=M.member_id AND (C.approved='y' OR C.approved='a') limit %d";
$rows_members = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id'], $record_limit));

if(count($rows_members) > 0){
    foreach($rows_members as $row){
		$list[] = '<a href="'.url_rewrite('profile.php?id='.$row['member_id'], AT_PRETTY_URL_IS_HEADER).'"'.
		          (strlen($row['login']) > SUBLINK_TEXT_LEN ? ' title="'.$row['login'].'"' : '') .'>'. 
		          validate_length($row['login'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>'; 
	}
	return $list;
	
} else {
	return 0;
}


?>