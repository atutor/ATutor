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
// $Id$

class TableFactory {
	var $db;
	var $version;
	var $course_id;
	var $import_dir;

	// constructor
	function TableFactory ($version, $db, $course_id, $import_dir) {
		$this->version = $version;
		$this->db = $db;
		$this->course_id = $course_id;
		$this->import_dir = $import_dir;
	}

	function createTable($table_name) {
		static $resource_categories_id_map; // old -> new ID's
		static $content_id_map; // old -> new ID's

		switch ($table_name) {
			case 'resource_links':
				return new ResourceLinksTable($this->version, $this->db, $this->course_id, $this->import_dir, $garbage, $resource_categories_id_map);
				break;

			case 'resource_categories':
				return new ResourceCategoriesTable($this->version, $this->db, $this->course_id, $this->import_dir, $resource_categories_id_map, $garbage);
				break;

			default:
				return NULL;
		}
	}
}

class Table {
	var $db; // private
	var $fp; // protected
	var $version; // protected
	var $course_id; // protected?
	var $importDir; // private
	var $old_id_to_new_id; // ? array
	var $row; // protected
	var $parent_ids;
	var $import_dir;

	// constructor
	function Table($version, $db, $course_id, $import_dir, &$old_id_to_new_id, $parent_id_to_new_id) {
		$this->db =& $db;
		$this->course_id = $course_id;
		$this->version = $version;
		$this->import_dir = $import_dir;

		//$this->importDir = 
		if (!isset($this->old_id_to_new_id)) {
			$this->old_id_to_new_id = $old_id_to_new_id;
		}

		if (isset($parent_id_to_new_id)) {
			$this->parent_ids = $parent_id_to_new_id;
		}
	}

	// public
	function translateWhitespace($input) {
		$input = str_replace('\n', "\n", $input);
		$input = str_replace('\r', "\r", $input);
		$input = str_replace('\x00', "\0", $input);

		$input = addslashes($input);
		return $input;
	}

	// private
	function lockTable() {
		$lock_sql = 'LOCK TABLES ' . TABLE_PREFIX . $this->tableName. ' WRITE';
		$result   = mysql_query($lock_sql, $this->db);
	}

	// private
	function unlockTable() {
		$lock_sql = 'UNLOCK TABLES';
		$result   = mysql_query($lock_sql, $this->db);
	}

	// protected
	function openTable() {
		$this->lockTable();
		debug($this->import_dir . $this->tableName . '.csv');
		$this->fp = fopen($this->import_dir . $this->tableName . '.csv', 'rb');
	}

	// protected
	function closeTable() {
		$this->unlockTable();
		fclose($this->fp);
	}

	// protected
	function getRows() {
		$this->openTable();

		while ($row = fgetcsv($this->fp, 10000)) {
			if (count($row) < 2) {
				continue;
			}

			$this->rows[$this->getID($row)] = $row;
		}

		$this->closeTable();
	}

	// public
	function restore() {
		$this->getRows();

		debug($this->rows);
		foreach ($this->rows as $row) {
			$this->insertRow($row);	
		}
		debug($this->old_id_to_new_id);
	}


	function insertRow($row) {
		$row = $this->convert($row);
		//debug($row);
		//debug($this->old_id_to_new_id);
		if (!isset($this->old_id_to_new_id[$this->getID($row)])) {
			$parent = $this->getParentID($row);
			//debug($parent);

			if ($parent && !isset($this->old_id_to_new_id[$parent])) {
				$this->insertRow($this->rows[$parent]);
			}
			debug($this->generateSQL($row));
			mysql_query($this->generateSQL($row), $this->db);

			$new_id = mysql_insert_id($this->db);
			$this->old_id_to_new_id[$this->getID($row)] = $new_id;
		} // else: already inserted
	}

}

class ResourceLinksTable extends Table {
	var $tableName = 'resource_links';

	function getID($row) {
		static $i;
		$i++;

		return $i;
	}

	// private
	function convert() {
		// handle the white space issue as well
	}

	// private
	function generateSQL() {
		// insert row
		return $sql;
	}
}

class ResourceCategoriesTable extends Table {
	var $tableName = 'resource_categories';


	function getParentID($row) {
		return $row[2];
	}

	function getID($row) {
		return $row[0];
	}

	// private
	function convert($row) {
		// handle the white space issue as well
		$row[1] = $this->translateWhitespace($row[1]);
		//unset($this->row[2]);

		return $row;
	}

	// private
	function generateSQL($row) {
		$sql = 'INSERT INTO '.TABLE_PREFIX.'resource_categories VALUES ';
		$sql .= '(0,';
		$sql .= $this->course_id .',';

		// CatName
		$sql .= "'".$row[1]."',";

		// CatParent
		if ($row[2] == 0) {
			$sql .= 'NULL';
		} else {
			$sql .= $this->old_id_to_new_id[$row[2]]; // need the real way of getting the cat parent ID
		}
		$sql .= ')';

		return $sql;
	}
}

/*
echo '<pre>';

$TableFactory =& new TableFactory('1.4.3', $db, $course_id, $import_dir);

$table  = $TableFactory->createTable('resource_categories');
$table->restore();
print_r($table);

$table  = $TableFactory->createTable('resource_links');
$table->restore();

print_r($table);
*/
?>