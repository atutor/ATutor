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

	// constructor
	function TableFactory ($version, $db, $course_id) {
		$this->version = $version;
		$this->db = $db;
		$this->course_id = $course_id;
	}

	function createTable($table_name) {
		static $resource_categories_id_map; // old -> new ID's
		static $content_id_map; // old -> new ID's

		switch ($table_name) {
			case 'resource_links':
				return new ResourceLinksTable($this->version, $this->db, $this->course_id, $garbage, $resource_categories_id_map);
				break;

			case 'resource_categories':
				return new ResourceCategoriesTable($this->version, $this->db, $this->course_id, $resource_categories_id_map, $garbage);
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

	// constructor
	function Table($version, $db, $course_id, &$old_id_to_new_id, $parent_id_to_new_id) {
		$this->db =& $db;
		$this->course_id = $course_id;
		$this->version = $version;
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
		$this->fp = fopen($this->table_name, 'rb');
	}

	// protected
	function closeTable() {
		$this->unlockTable();
		fclose($this->fp);
	}

	// protected
	function getRow() {
		return $row;
		$row = fgetcsv($this->fp, 10000);
		if (count($row) < 2) {
			return false;
		}
		return $row;
	}

	// public
	function restore() {
		$this->openTable();

		while ($this->getRow()) {
			$this->convert();
			$new_id = $this->insertRow();
			$this->old_id_to_new_id[$old_id] = $new_id;
		}

		$this->closeTable();
	}

	// protected
	function insertSQL($sql) {
		mysql_query($sql, $this->db);
		return mysql_insert_id($this->db);
	}
}

class ResourceLinksTable extends Table {
	var $tableName = 'resource_links';

	// private
	function convert() {
		// handle the white space issue as well
	}

	// private
	function insertRow($row) {
		// insert row
	}

}

class ResourceCategoriesTable extends Table {
	var $tableName = 'resource_categories';

	// private
	function convert() {
		// handle the white space issue as well
	}

	// private
	function insertRow($row) {
		//$sql = "INSERT ..";
		///return $this->insertSQL($sql);
		//$result = mysql_query($sql, $this->db);
	}
}

echo '<pre>';

$TableFactory =& new TableFactory('1.4.3', $db, $course_id);

$table  = $TableFactory->createTable('resource_categories');
$table->restore();
print_r($table);

$table  = $TableFactory->createTable('resource_links');
$table->restore();

print_r($table);
?>