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

function TableFactory ($version, $db, $course_id, $table) {
	switch ($table) {
		case 'links': {
			return new ResourceLinksTable($version, $db, $course_id);
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

	// constructor
	function Table($version, $db, $course_id) {
		$this->db =& $db;
		$this->course_id = $course_id;
		$this->version = $version;
		//$this->importDir = 
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
		//$result   = mysql_query($lock_sql, $this->db);
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

	// protected
	function insertRow() {

	}
}

class ResourceLinksTable extends Table {
	var $tableName = 'links';

	// constructor
	/*
	function ResourceLinksTable($db, $course_id) {
		parent::Table($db, $course_id); // call the parent constructor
	}
	*/

	// private
	function convert() {
		// handle the white space issue as well
	}

	// private
	function insertRow($row) {
		return $row;
	}

	// public
	function restore() {
		$this->openTable();

		while ($this->getRow()) {
			$this->convert();
			//$this->insertRow();
		}
	}
}

class NewsTable extends Table {
	/*
	function convert() {

	}

	function insert() {
		$this->open_table();
		$this->convert();
	}
	*/
}

/*
class table_factory() {
	function get_table($name) {
		return new $name();
	}
}


function restore() {
	unzip
	get the version
	$factory = new table($version);
}
*/

/*
foreach ($tables as $table_name) {
	$table = $factory->get_table($table_name);

	$table->insert();
}
*/

$table  = TableFactory('1.4.3', $db, $course_id, 'links');
$table->restore();
echo '<pre>';
print_r($table);
?>