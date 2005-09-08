<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: $

class csvexport {

	// private
	// quote $line so that it's safe to save as a CSV field
	function quoteCSV($line) {
		// this code below can be replaced with a single str_replace call with two arrays as arguments.
		$line = str_replace('"', '""', $line);

		$line = str_replace("\n", '\n', $line);
		$line = str_replace("\r", '\r', $line);
		$line = str_replace("\x00", '\0', $line);

		return '"'.$line.'"';
	}

	// private
	function exportTable($sql, $course_id, $prefs) { //delimiters(opt array), 
		global $db;
		$sql = str_replace('?', $course_id, $sql);

		$content = '';
		$result = mysql_query($sql, $db);

		$num_fields = mysql_num_fields($result);

		//put in function...
		for ($i=0; $i< $num_fields; $i++) {
			$type  = mysql_field_type($result, $i);
			$types[$i] = $type;
		}

		while ($row = mysql_fetch_row($result)) {
			for ($i=0; $i< $num_fields; $i++) {
				if ($types[$i] == 'int' || $types[$i] == 'real') {
					$content .= $row[$i] . ',';
				} else {
					$content .= csvexport::quoteCSV($row[$i]) . ',';
				}
			}
			$content = substr($content, 0, -1);
			$content .= "\n";
		}
		
		@mysql_free_result($result);

		return $content;
	}

}

/*
define('DB_USER',                      'root');
define('DB_PASSWORD',                  'tydiutor');
define('DB_HOST',                      'localhost');
define('DB_PORT',                      '3306');
define('DB_NAME',                      'atutor_svn2');
define('TABLE_PREFIX',                 'AT_');

$db = @mysql_connect(DB_HOST . ':' . DB_PORT, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME, $db);

$sql = 'SELECT question, created_date, choice1, choice2, choice3, choice4, choice5, choice6, choice7 FROM '.TABLE_PREFIX.'polls WHERE course_id=?';

$prefs = array();
*/
/*
Fields terminated by   	
Fields enclosed by  	
Fields escaped by  	
Lines terminated by  	
Replace NULL by  	
Put fields names at first row
*/
/*
$str = csvexport::exportTable($sql, 4,  $prefs);

echo $str;
*/
?>