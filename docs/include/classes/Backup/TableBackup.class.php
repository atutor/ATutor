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
				return new ResourceLinksTable($this->version, $this->db, $this->course_id, $this->import_dir, $resource_categories_id_map);
				break;

			case 'resource_categories':
				return new ResourceCategoriesTable($this->version, $this->db, $this->course_id, $this->import_dir, $resource_categories_id_map);
				break;

			case 'content':
				return new ContentTable($this->version, $this->db, $this->course_id, $this->import_dir, $content_id_map);
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
	var $new_parent_ids;
	var $import_dir;

	// constructor
	function Table($version, $db, $course_id, $import_dir, &$old_id_to_new_id) {
		$this->db =& $db;
		$this->course_id = $course_id;
		$this->version = $version;
		$this->import_dir = $import_dir;

		//$this->importDir = 
		if (empty($old_id_to_new_id)) {
			$this->old_id_to_new_id =& $old_id_to_new_id;
		} else {
			$this->new_parent_ids = $old_id_to_new_id;
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
		//debug($this->import_dir . $this->tableName . '.csv');
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
			if ($this->getOldID($row) === FALSE) {
				$this->rows[] = $row;
			} else {
				$this->rows[$this->getOldID($row)] = $row;
			}
		}

		$this->closeTable();
	}

	// public
	function restore() {
		$this->getRows();

		foreach ($this->rows as $row) {
			$this->insertRow($row);	
		}
	}


	function insertRow($row) {
		$row = $this->convert($row);
		//debug($row);
		//debug($this->old_id_to_new_id);
		$old_id = $this->getOldID($row);
		$new_id = $this->getNewID($old_id);

		if (!$new_id) {
			$parent_id = $this->getParentID($row);

			if ($parent_id && !$this->getNewID($parent_id)) {
				$this->insertRow($this->rows[$parent_id]);
			}
			debug($this->generateSQL($row));
			mysql_query($this->generateSQL($row), $this->db);

			$new_id = mysql_insert_id($this->db);

			$this->setNewID($old_id, $new_id);
		} // else: already inserted
	}

	// private
	function setNewID($old_id, $new_id) {
		$this->old_id_to_new_id[$old_id] = $new_id;
	}

	// protected
	function getNewID($id) {
		if (isset($this->old_id_to_new_id[$id])) {
			return $this->old_id_to_new_id[$id];
		}
		return FALSE;
	}

}

class ResourceLinksTable extends Table {
	var $tableName = 'resource_links';

	function getOldID($row) {
		return FALSE;
	}

	function getParentID($row) {
		return FALSE;
	}

	// private
	function convert($row) {
		// handle the white space issue as well
		return $row;
	}

	// private
	function generateSQL($row) {
		// insert row
		$sql = 'INSERT INTO '.TABLE_PREFIX.'resource_links VALUES ';
		$sql .= '(0, ';
		$sql .= $this->new_parent_ids[$row[0]] . ',';

		$sql .= "'".$row[1]."',"; // URL
		$sql .= "'".$row[2]."',"; // LinkName
		$sql .= "'".$row[3]."',"; // Description
		$sql .= $row[4].',';      // Approved
		$sql .= "'".$row[5]."',"; // SubmitName
		$sql .= "'".$row[6]."',"; // SubmitEmail
		$sql .= "'".$row[7]."',"; // SubmitDate
		$sql .= $row[8]. '),';

		return $sql;
	}
}

class ResourceCategoriesTable extends Table {
	var $tableName = 'resource_categories';

	function getParentID($row) {
		return $row[2];
	}

	function getOldID($row) {
		return $row[0];
	}

	// private
	function convert($row) {
		// handle the white space issue as well
		$row[1] = $this->translateWhitespace($row[1]);

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
			$sql .= $this->getNewID($row[2]); // need the real way of getting the cat parent ID
		}
		$sql .= ')';

		return $sql;
	}
}


class ContentTable extends Table {
	var $tableName = 'content';

	function getParentID($row) {
		return $row[1];
	}

	function getOldID($row) {
		return $row[0];
	}

	// private
	function convert($row) {
		// handle the white space issue as well
		$row[3] = $this->translateWhitespace($row[3]);
		$row[6] = $this->translateWhitespace($row[6]);
		$row[7] = $this->translateWhitespace($row[7]);
		$row[8] = $this->translateWhitespace($row[8]);
		$row[9] = $this->translateWhitespace($row[9]);
		$row[10] = $this->translateWhitespace($row[10]);

		return $row;
	}

	// private
	function generateSQL($row) {
		$sql = 'INSERT INTO '.TABLE_PREFIX.'content VALUES ';
		$sql .= '(0,';	// content_id
		$sql .= $this->course_id .',';
		if ($row[1] == 0) { // content_parent_id
			$sql .= 0;
		} else {
			$sql .= $this->getNewID($row[1]);
		}
		$sql .= ',';

		//if ($this->content_pages[$content_id][1] == 0) { // ordering
		//	$sql .= $this->content_pages[$content_id][2] + $this->order_offset;
		//} else {
	//		$sql .= $this->content_pages[$content_id][2];
	//	}
		$sql .= '1,';

		$sql .= "'".$row[3]."',"; // last_modified
		$sql .= $row[4] . ','; // revision
		$sql .= $row[5] . ','; // formatting
		$sql .= "'".$row[6]."',"; // release_date
		$sql .= "'".$row[7]."',"; // keywords
		$sql .= "'".$row[8]."',"; // content_path
		$sql .= "'".$row[9]."',"; // title
		$sql .= "'".$row[10]."',0)"; // text

		return $sql;
	}
}
?>