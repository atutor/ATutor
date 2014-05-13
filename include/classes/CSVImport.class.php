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
// $Id$

class CSVImport {
	var $quote_search  = array('""', '\\\n', '\\\r');
	var $quote_replace = array('"', '\n', '\r');

	// constructor
	function CSVImport() { }

	// public
	// returns the primary_key, or false if there is none, or null if more than 1
	function getPrimaryFieldName($table_name) {
		$field = false;

		$sql = "SELECT * FROM %s%s WHERE 0";
		$result = queryDBresult($sql, array(TABLE_PREFIX, $table_name));
		$num_fields = at_num_fields($result);
		
        for ($i= 0; $i<$num_fields; $i++) {
        	if (at_is_field_a_primary_key($result, $i)) {
                if ($field == false) {
                    $field = at_field_name($result, $i);
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

		$field_types = array();

		$sql = "SELECT * FROM %s%s WHERE 0";
		$result = queryDBresult($sql, array(TABLE_PREFIX, $table_name));
		if (!$result) {
			return array();
		}

		$num_fields = at_num_fields($result);

		for ($i=0; $i< $num_fields; $i++) {

			$field_types[] = at_field_type($result, $i);

		}

		return $field_types;
	}

	function translateWhitespace($input) {
		$input = addslashes($input);
		$input = str_replace($this->quote_search, $this->quote_replace, $input);

		return $input;
	}

	// public
	function import($tableName, $path, $course_id, $version) {
		static $table_id_map;

		$fn_name = $tableName.'_convert';

		// lock the tables
		$lock_sql = 'LOCK TABLES ' . TABLE_PREFIX . $tableName. ' WRITE, ' . TABLE_PREFIX . 'courses WRITE';
		$result   = queryDB($lock_sql, array());

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
			$sql     = 'SELECT MAX(%s) AS next_id FROM %s%s';
			$next_id  = queryDB($sql, array($primary_key_field_name, TABLE_PREFIX, $tableName), TRUE);
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
                    $sql .= "'" . $field."',";
                }

			$sql = substr($sql, 0, -1);
			$sql .= ')';

            //Escape % for compatibility with queryDB()
            $sql = str_replace("%", "%%", $sql );
			$result = queryDB($sql, array());
			$i++;
			$next_id++;
		}

		// close the file
		@fclose($fp);

		// unlock the tables
		$lock_sql = 'UNLOCK TABLES';
		$result   = queryDB($lock_sql, array());
	}
}

?>