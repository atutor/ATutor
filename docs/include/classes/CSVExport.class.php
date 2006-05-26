<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

class CSVExport {
	var $quote_search  = array('"',  "\n", "\r", "\x00");
	var $quote_replace = array('""', '\n', '\r', '\0');

	// constructor
	function CSVExport() { }

	// public
	function export($sql, $course_id) {
		global $db;
		$sql = str_replace('?', $course_id , $sql);

		$content = '';

		$result = mysql_query($sql, $db);

		$field_types = $this->detectFieldTypes($result);
		if (!$field_types) {
			return FALSE;
		}
		$num_fields = count($field_types);

		while ($row = mysql_fetch_row($result)) {
			for ($i=0; $i < $num_fields; $i++) {
				if ($types[$i] == 'int' || $types[$i] == 'real') {
					$content .= $row[$i] . ',';
				} else {
					$content .= $this->quote($row[$i]) . ',';
				}
			}
			$content = substr($content, 0, -1);
			$content .= "\n";
		}
		
		@mysql_free_result($result);

		return $content;
	}

	// public
	// given a query result returns an array of field types.
	// possible field types are int, string, datetime, or blob...
	function detectFieldTypes(&$result) {
		$field_types = array();
		$num_fields = @mysql_num_fields($result);

		if (!$num_fields) {
			return array();
		}

		for ($i=0; $i< $num_fields; $i++) {
			$field_types[] = mysql_field_type($result, $i);
		}
		return $field_types;
	}

	// private
	// quote $line so that it's safe to save as a CSV field
	function quote($line) {
		return '"'.str_replace($this->quote_search, $this->quote_replace, $line).'"';
	}
}

?>