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
// $Id: Backup.class.php 1783 2004-10-06 17:31:05Z heidi $

class table {
var $db
var $table_fp;
var $version;

	function open_table() {
		$this->table_fp = fopen($this->table_name);
	}

	function get_row() {
		$row = fgetcsv($this->table_fp, 10000);
		if count($row) < 2)
			return false;
		return $row;
	}
}

class links extends table {
	var $table_name = 'links';

	function convert() {

	}

	function restore() {
		$this->open_table(); // lock table
		while ($row = $this->get_row()) {
			$this->convert($row);
			$this->insert_row($row);
		}
	}

	function convert($row) {

	}

}

class news extends table {
	function convert() {

	}

	function insert() {
		$this->open_table();
		$this->convert();
	}
}

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

foreach ($tables as $table_name) {
	$table = $factory->get_table($table_name);

	$table->insert();
}
?>