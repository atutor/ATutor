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

			case 'related_content':
				debug($content_id_map);
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

	function translateText($row) {
		global $backup_tables;
		$count = 0;
		foreach ($backup_tables['resource_links']['fields'] as $field) {
			if ($field[1] == TEXT) {
				$row[$count] = $this->translateWhitespace($row[$count]);
			}
			$count++;
		}
		return $row;
	}

}
//---------------------------------------------------------------------
class ForumsTable extends Table {
	var $tableName = 'forums';

	function getOldID($row) {
		return FALSE;
	}

	function getParentID($row) {
		return FALSE;
	}

	// private
	function convert($row) {
		// handle the white space issue as well
		$row = $this->translateText($row);
		return $row;
	}

	// private
	function generateSQL($row) {
		// insert row
		$sql = 'INSERT INTO '.TABLE_PREFIX.'forums VALUES ';
		$sql .= '(0,'.$this->course_id.',';

		$sql .= "'".$row[0]."',"; // title
		$sql .= "'".$row[1]."',"; // description
		$sql .= "'".$row[2]."',"; // num_topics
		$sql .= "'".$row[3]."',"; // num_posts
		$sql .= $row[4]. '),';	  // last_post

		return $sql;
	}
}
//---------------------------------------------------------------------
class GlossaryTable extends Table {
	var $tableName = 'glossary';

	function getOldID($row) {
		return FALSE;
	}

	function getParentID($row) {
		return FALSE;
	}

	// private
	function convert($row) {
		// handle the white space issue as well
		$row = $this->translateText($row);
		return $row;
	}

	// private
	function generateSQL($row) {
		// insert row
		$sql = 'INSERT INTO '.TABLE_PREFIX.'glossary VALUES ';
		$sql .= "('".$row[0]."',";			// word_id  
		$sql .= "('".$this->course_id."',";	// course_id 
		$sql .= "'".$row[1]."',";			// word
		$sql .= "'".$row[2]."',";			// definition
		$sql .= "'".$row[3]."')";			// related word

		return $sql;
	}
}
//---------------------------------------------------------------------
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
		$row = $this->translateText($row);
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

//---------------------------------------------------------------------
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
		$row = $this->translateText($row);
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
//---------------------------------------------------------------------
class NewsTable extends Table {
	var $tableName = 'news';

	function getOldID($row) {
		return FALSE;
	}

	function getParentID($row) {
		return FALSE;
	}

	// private
	function convert($row) {
		// handle the white space issue as well
		$row = $this->translateText($row);
		return $row;
	}

	// private
	function generateSQL($row) {
		// insert row
		$sql = 'INSERT INTO '.TABLE_PREFIX.'news VALUES ';
		$sql .= '(0,'.$this->course_id.', '. $_SESSION['member_id'].', ';
		$sql .= "'".$row[0]."',"; // date
		$sql .= "'".$row[1]."',"; // formatting
		$sql .= "'".$row[2]."',"; // title
		$sql .= "'".$row[3]."')"; // body

		return $sql;
	}
}
//---------------------------------------------------------------------
class TestsTable extends Table {
	var $tableName = 'tests';

	function getOldID($row) {
		return FALSE;
	}

	function getParentID($row) {
		return FALSE;
	}

	// private
	function convert($row) {
		// handle the white space issue as well
		$row = $this->translateText($row);
		if (version_compare($version, '1.4', '<')) {
			$row[8] = 0;
			$row[9] = 0;
			$row[10] = 0;
			$row[11] = 0;
		} 
		
		if (version_compare($version, '1.4.2', '<')) {
			$row[12] = 0;
			$row[13] = 0;
		}
		return $row;
	}

	// private
	function generateSQL($row) {
		// insert row

		$sql		= 'SELECT MAX(test_id) AS max_test_id FROM '.TABLE_PREFIX.'tests';
		$result		= mysql_query($sql, $db);
		$next_index = mysql_fetch_assoc($result);
		$next_index = $next_index['max_test_id'] + 1;

		$sql = '';
		$index_offset = '';
		if ($sql == '') {
			$index_offset = $next_index - $row[0];
			$sql = 'INSERT INTO '.TABLE_PREFIX.'tests VALUES ';
		}
		$sql .= '(';
		$sql .= ($row[0] + $index_offset) . ',';
		$sql .= $this->course_id.',';

		$sql .= "'".$row[1]."',";	//title
		$sql .= "'".$row[2]."',";	//format
		$sql .= "'".$row[3]."',";	//start_date
		$sql .= "'".$row[4]."',";	//end_date
		$sql .= "'".$row[5]."',";	//randomize_order
		$sql .= "'".$row[6]."',";	//num_questions
		$sql .= "'".$row[7]."',";	//instructions
		$sql .= ',' . ($translated_content_ids[$row[8]] ? $translated_content_ids[$row[8]] : 0). ','; //content_id
		$sql .= $row[9] . ',';		//automark
		$sql .= $row[10] . ',';		//random
		$sql .= $row[11] . ',';		//difficulty
		$sql .= $row[12] . ',';		//num_takes
		$sql .= $row[13] ;			//anonymous
		$sql .= ')';

		return $sql;
	}
}
//---------------------------------------------------------------------
class TestsQuestionsTable extends Table {
	var $tableName = 'tests_questions';

	function getOldID($row) {
		return FALSE;
	}

	function getParentID($row) {
		return FALSE;
	}

	// private
	function convert($row) {
		$row = $this->translateText($row);
		if (version_compare($version, '1.4', '<')) {
			$row[28] = 0;
		}	
		return $row;
	}

	// private
	function generateSQL($row) {
		// insert row

		$sql		= 'SELECT MAX(test_id) AS max_test_id FROM '.TABLE_PREFIX.'tests';
		$result		= mysql_query($sql, $db);
		$next_index = mysql_fetch_assoc($result);
		$next_index = $next_index['max_test_id'] + 1;

		$sql = '';
		$index_offset = $next_index - $row[0];

		$sql = 'INSERT INTO '.TABLE_PREFIX.'tests_questions VALUES ';
		$sql .= '(';
		$sql .= ($row[0] + $index_offset) . ',';
		$sql .= $this->course_id.',';

		for ($i=1; $i<=28; $i++) {
			$sql .= "'".$row[$i]."',";
		}

		$sql  = substr($sql, 0, -1);
		$sql .= ')';

		return $sql;
	}
}
//---------------------------------------------------------------------
class PollsTable extends Table {
	var $tableName = 'polls';

	function getOldID($row) {
		return FALSE;
	}

	function getParentID($row) {
		return FALSE;
	}

	// private
	function convert($row) {
		$row = $this->translateText($row);
		return $row;
	}

	// private
	function generateSQL($row) {
		// insert row
		$sql = 'INSERT INTO '.TABLE_PREFIX.'polls VALUES ';
		$sql .= '(0,';
		$sql .= $this->course_id.',';

		for ($i=0; $i<=8; $i++) {
			$sql .= "'".$row[$i]."',";
		}

		$sql  = substr($sql, 0, -1);
		$sql .= ')';

		return $sql;
	}
}
//---------------------------------------------------------------------
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
		$row = $this->translateText($row);
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