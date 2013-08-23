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

$sql = "SELECT * FROM %sglossary WHERE course_id = %d LIMIT %d";
$rows_g = queryDB($sql, array(TABLE_PREFIX, $_SESSION[course_id], $record_limit));

if(count($rows_g)){
    foreach($rows_g as $row){
		$list[] = '<a href="'.url_rewrite('mods/_core/glossary/index.php?w='.urlencode($row['word']).'#term', AT_PRETTY_URL_IS_HEADER).'"'.
		          (strlen($row['word']) > SUBLINK_TEXT_LEN ? ' title="'.AT_print($row['word'], 'glossary.word').'"' : '') .'>'. 
		          AT_print(validate_length($row['word'], SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY), 'glossary.word') .'</a>'; 
	}
	return $list;
	
} else {
	return 0;
}


?>