<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_CONTENT);

	function quote_csv($line) {
		$line = str_replace('"', '""', $line);

		$line = str_replace("\n", '\n', $line);
		$line = str_replace("\r", '\r', $line);
		$line = str_replace("\x00", '\0', $line);

		return '"'.$line.'"';
	}

	$name = str_replace(" ", "_", $_SESSION['course_title']);
	$name = str_replace("'", "", $name);

	header('Content-Type: text/csv');
	header('Content-Disposition: inline; filename="'.$name.'_tracking.csv"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	
	$file_row = "page,member,visits,duration,timestamp";
	$file_row .= "\n";

	$sql = "SELECT C.title, M.login, MT.counter, SEC_TO_TIME(MT.duration) AS time, MT.last_accessed
			FROM %scontent C, %smembers M, %smember_track MT
			WHERE M.member_id=MT.member_id AND C.content_id=MT.content_id AND C.course_id=%d
			ORDER BY C.title, M.login ASC";

	$rows_tracking = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, TABLE_PREFIX, $_SESSION['course_id']));	
	
	foreach($rows_tracking as $row){
		$file_row .= quote_csv($row['title'])   .",";
		$file_row .= quote_csv($row['login'])  .",";
		$file_row .= quote_csv($row['counter']) .",";
		$file_row .= quote_csv($row['time']) .",";
		$file_row .= AT_date(_AT('forum_date_format'), $row['last_accessed']).",";
		$file_row .= "\n";
	}


	echo $file_row;
	exit;
?>