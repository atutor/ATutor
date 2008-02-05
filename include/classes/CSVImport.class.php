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
// $Id$

class CSVImport {
	var $quote_replace = array('"',  "\n", "\r", "\x00");
	var $quote_search  = array('""', '\n', '\r', '\0');

	// constructor
	function CSVImport() { }

	// public
	// returns the primary_key, or false if there is none, or null if more than 1
	function getPrimaryFieldName($table_name) {
		global $db;

		$field = false;

		$sql = "SELECT * FROM ".TABLE_PREFIX.$table_name .' WHERE 0';
		$result = mysql_query($sql, $db);
		$num_fields = mysql_num_fields($result);
		for ($i= 0; $i<$num_fields; $i++) {
			$flags = explode(' ', mysql_field_flags($result, $i));
			if (in_array('primary_key', $flags)) {
				if ($field == false) {
					$field = mysql_field_name($result, $i);
				} else {
					// there is more than one primary_key
					return NULL;
				}
			}
		}
		return $field;
	}


	// public
	// given a query result returns an array of field types.
	// possible field types are int, string, datetime, or blob...
	function detectFieldTypes($table_name) {
		global $db;

		$field_types = array();

		$sql = "SELECT * FROM ".TABLE_PREFIX.$table_name .' WHERE 0';
		$result = @mysql_query($sql, $db);
		if (!$result) {
			return array();
		}
		$num_fields = mysql_num_fields($result);

		for ($i=0; $i< $num_fields; $i++) {
			$field_types[] = mysql_field_type($result, $i);
		}

		return $field_types;
	}

	function translateWhitespace($input) {
		$input = str_replace($this->quote_search, $this->quote_replace, $input);

		$input = addslashes($input);
		return $input;
	}

	// public
	function import($tableName, $path, $course_id, $version) {
		global $db;
		static $table_id_map;

		$fn_name = $tableName.'_convert';

		// lock the tables
		$lock_sql = 'LOCK TABLES ' . TABLE_PREFIX . $tableName. ', ' . TABLE_PREFIX . 'courses WRITE';
		$result   = mysql_query($lock_sql, $db);

		// get the field types
		$field_types = $this->detectFieldTypes($tableName);
		if (!$field_types) {
			return FALSE;
		}

		// get the name of the primary field
		$primary_key_field_name = $this->getPrimaryFieldName($tableName);
		// read the rows into an array
		$fp = @fopen($path . $tableName . '.csv', 'rb');
		$i = 0;

		// get the name of the primary ID field and the next index
		$next_id = 0;
		if ($primary_key_field_name) {
			// get the next primary ID
			$sql     = 'SELECT MAX(' . $primary_key_field_name . ') AS next_id FROM ' . TABLE_PREFIX . $tableName;
			$result  = mysql_query($sql, $db);
			$next_id = mysql_fetch_assoc($result);
			$next_id = $next_id['next_id']+1;
		}

		$rows = array();
		while ($row = @fgetcsv($fp, 70000)) {
			if (count($row) && (trim($row[0]) == '')) {
				continue;
			}

			if (function_exists($fn_name)) {
				$row = $fn_name($row, $course_id, $table_id_map, $version);
			}
			if (!$row) {
				continue;
			}
			if ($row[0] == 0) {
				$row[0] = $i;
			}

			$table_id_map[$tableName][$row[0]] = $next_id;
			if ($primary_key_field_name != NULL) {
				$row[0] = $next_id;
			}

			$sql = 'REPLACE INTO '.TABLE_PREFIX.$tableName.' VALUES (';

			foreach($row as $id => $field) {
				if (($field_types[$id] != 'int') && ($field_types[$id] != 'real')) {
					$field = $this->translateWhitespace($field);
				} else if ($field_types[$id] == 'int') {
					$field = intval($field);
				}
				$sql .= '"' . $field.'",';
			}
			$sql = substr($sql, 0, -1);
			$sql .= ')';
			$result = mysql_query($sql, $db);
			$i++;
			$next_id++;
		}

		// close the file
		@fclose($fp);

		// unlock the tables
		$lock_sql = 'UNLOCK TABLES';
		$result   = mysql_query($lock_sql, $db);
	}

}



?>