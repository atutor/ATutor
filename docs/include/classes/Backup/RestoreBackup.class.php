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

/**
* RestoreBackup
* Class for restoring a course backup
* @access	public
* @author	Joel Kronenberg
* @package	Backup
*/
class RestoreBackup {
	var $db;

	var $course_id;
	var $dir;

	var $import_path;
	var $version;

	function RestoreBackup($db, $course_id) {
		$this->db =& $db;
		$this->course_id = $course_id;

		$this->dir = AT_CONTENT_DIR . $course_id . '/';

		@mkdir(AT_CONTENT_DIR . 'import/' . $this->course_id);
		$this->import_path = AT_CONTENT_DIR . 'import/' . $this->course_id . '/';

		debug($this->import_path);
		//$this->version = $version;
	}

	function restore($material, $action, $backup_id) {
		require_once(AT_INCLUDE_PATH.'classes/pclzip.lib.php');
		require_once(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

		// 1. get backup row/information
		$Backup =& new Backup($this->db, $this->course_id);
		$my_backup = $Backup->getRow($backup_id);
		unset($Backup);

		// 2. extract the backup
		$archive = new PclZip(AT_BACKUP_DIR . $this->course_id . DIRECTORY_SEPARATOR . $my_backup['system_file_name']. '.zip');
		if ($archive->extract(	PCLZIP_OPT_PATH,	$this->import_path, 
								PCLZIP_CB_PRE_EXTRACT,	'preImportCallBack') == 0) {
			die("Error : ".$archive->errorInfo(true));
		}

		// 3. get the course's max_quota. if backup is too big AND we want to import files then abort/return FALSE
		/* get the course's max_quota */
		// $this->getFilesSize();

		// 4. figure out version number
		$this->setVersion();
		debug('version: '.$this->version);
		// what to do if version is null?

		// 5. if override is set then delete the content
		if ($action == 'overwrite') {
			debug('deleting content - overwrite');
			//delete_course($_SESSION['course_id'], $entire_course = false, $rel_path = '../../');
			//$_SESSION['s_cid'] = 0;
		} else {
			debug('appending content');
		}

		// 6. import csv data that we want
		foreach ($material as $name => $garbage) {
			debug($name .' -> ' . 'convert_'.$name.'()');
			$this->{'convert_'.$name}();

			debug($name .' -> ' . 'restore_'.$name.'()');
			$this->{'restore_'.$name}();
		}
		
		// 7. delete import files
		//clr_dir($import_path);
	}

	function setVersion() {
		if ($version = file($this->import_path.'atutor_backup_version')) {
			$this->version = $version[0];
		} else {
			$this->version = null;
		}
	}

	// the following private methods are using for converting the given file
	// to the current atutor version. they must be exactly named that:
	function convert_content()	{ }
	function convert_news()		{ }
	function convert_links()	{ }
	function convert_forums()	{ }
	function convert_tests()	{ }
	function convert_polls()	{ }
	function convert_glossary() { }
	function convert_files()	{ }
	function convert_stats()	{ }

	// private
	function restore_files() {
		debug('want to copy files');
		/*
		$sql	= "SELECT max_quota FROM ".TABLE_PREFIX."courses WHERE course_id=$this->course_id";
		$result = mysql_query($sql, $this->db);
		$row	= mysql_fetch_assoc($result);

		if ($row['max_quota'] != AT_COURSESIZE_UNLIMITED) {
			global $MaxCourseSize, $MaxCourseFloat;

			if ($row['max_quota'] == AT_COURSESIZE_DEFAULT) {
				$row['max_quota'] = $MaxCourseSize;
			}

			$totalBytes   = dirsize($import_path.'content/'); // use size of $my_backup['contents']['files'] 
			$course_total = dirsize(AT_CONTENT_DIR . $this->course_id . '/');
			$total_after  = $row['max_quota'] - $course_total - $totalBytes + $MaxCourseFloat;

			debug($total_after, 'total_after');
			if ($total_after < 0) {
				debug('not enough space. delete everything');
				// remove the content dir, since there's no space for it
				clr_dir($import_path);
				return FALSE;
					
				//require(AT_INCLUDE_PATH.'header.inc.php');
				//$errors[] = array(AT_ERROR_NO_CONTENT_SPACE, number_format(-1*($total_after/AT_KBYTE_SIZE), 2 ) );
				//print_errors($errors);
				//require(AT_INCLUDE_PATH.'footer.inc.php');
			}
		}

		copys($import_path.'/content/', AT_CONTENT_DIR . $this->course_id);
		*/
	}

	// private
	function restore_content() {
		/*
		$keys = array_keys($content_pages);
		reset($content_pages);
		$num_keys = count($keys);
		for($i=0; $i<$num_keys; $i++) {
			if ($translated_content_ids[$keys[$i]] == '') {
				$last_id = insert_content($keys[$i], $content_pages, $translated_content_ids);
				$translated_content_ids[$keys[$i]] = $last_id;
			}
		}
		*/
	}

	// private
	function _insertContent($content_id, &$content_pages, &$translated_content_ids) {
		/*
		global $order_offset;

		if ($content_pages[$content_id] == '') {
			// should never reach here.
			debug('CONTENT NOT FOUND! ' . $content_id);
			exit;
		}

		$num_fields = count($content_pages[$content_id]);
		if (!$version) {
			if ($num_fields == 9) {
				$version = '1.2';
			} else if ($num_fields == 11) {
				$version = '1.3';
			} else {
				$version = '1.1';
			}
		}

		if ($content_pages[$content_id][CPID] > 0) {
			if ($translated_content_ids[$content_pages[$content_id][CPID]] == '') {
				$last_id = insert_content(	$content_pages[$content_id][CPID],
											$content_pages,
											$translated_content_ids);
				$translated_content_ids[$content_pages[$content_id][CPID]] = $last_id;
			}
		}

		$sql = 'INSERT INTO '.TABLE_PREFIX.'content VALUES ';
		$sql .= '(0, ';	// content_id
		$sql .= $_SESSION['course_id'] .','; // course_id
		if ($content_pages[$content_id][CPID] == 0) { // content_parent_id
			$sql .= 0;
		} else {
			$sql .= $translated_content_ids[$content_pages[$content_id][CPID]];
		}
		$sql .= ',';

		if ($content_pages[$content_id][CPID] == 0) { // ordering
			$sql .= $content_pages[$content_id][2] + $order_offset;
		} else {
			$sql .= $content_pages[$content_id][2];
		}
		$sql .= ',';

		$sql .= "'".addslashes($content_pages[$content_id][3])."',"; // last_modified
		$sql .= $content_pages[$content_id][4] . ','; // revision
		$sql .= $content_pages[$content_id][5] . ','; // formatting
		$sql .= "'".addslashes($content_pages[$content_id][6])."',"; // release_date

		$i = 7;
		if (version_compare($version, '1.3', '>=')) {
			$sql .= "'".addslashes($content_pages[$content_id][7])."',"; // keywords
			$sql .= "'".addslashes($content_pages[$content_id][8])."',"; // content_path
			$i = 9;
		} else {
			$sql .= "'', '',";
		}
		
		$sql .= "'".addslashes($content_pages[$content_id][$i])."',"; // title
		$i++;

		$content_pages[$content_id][$i] = translate_whitespace($content_pages[$content_id][$i]);

		$sql .= "'".addslashes($content_pages[$content_id][$i])."',0)"; // text

		$result = mysql_query($sql, $db);
		if (!$result) {
			debug(mysql_error());
			debug($sql);
			exit;
		}
		$last_id = mysql_insert_id($db);
		return $last_id;
		*/
	}

	// private
	function restore_related_content() { 
		/* related_content.csv
		$sql = '';
		$fp = fopen($import_path.'related_content.csv','rb');
		while ($data = fgetcsv($fp, 100000, ',')) {
			if ($sql == '') {
				// first row stuff
				$sql = 'INSERT INTO '.TABLE_PREFIX.'related_content VALUES ';
			}
			$sql .= '(';
			$sql .= ($translated_content_ids[$data[0]]) . ',';
			$sql .= ($translated_content_ids[$data[1]]) . '),';
		}
		if ($sql != '') {
			$sql = substr($sql, 0, -1);
			$result = mysql_query($sql, $db);
		}
		*/
	}

	// private
	function restore_forums() {
		/* forums.csv */

		$sql = '';
		debug($this->import_path);
		$lines = file($this->import_path.'forums.csv');
		debug($lines);

		$fp  = fopen($this->import_path.'forums.csv',"r");
		debug($this->import_path.'forums.csv');
		while ($data = fgetcsv($fp)) {
			debug($data , 'data');

			/*
			if ($sql == '') {
				// first row stuff
				$sql = 'INSERT INTO '.TABLE_PREFIX.'forums VALUES ';
			}
			$sql .= '(0,'.$_SESSION['course_id'].',';

			$data[0] = translate_whitespace($data[0]);
			$data[1] = translate_whitespace($data[1]);

			$sql .= "'".addslashes($data[0])."',";
			$sql .= "'".addslashes($data[1])."',";

			if (version_compare($version, '1.4', '>=')) {
				$sql .= $data[2] . ',';
				$sql .= $data[3] . ',';
				$sql .= "'".addslashes($data[4])."'";
			} else {
				$sql .= '0,0,0';
			}
			$sql .= '),';
			*/
		
		}
		/*
		if ($sql != '') {
			$sql = substr($sql, 0, -1);
			$result = mysql_query($sql, $db);
		}
		*/
		fclose($fp);

	}

	// private
	function restore_glossary() {
		/* glossary.csv */
		/* get the word id offset: *
		$lock_sql = 'LOCK TABLES '.TABLE_PREFIX.'glossary WRITE';
		mysql_query($lock_sql, $db);

		$sql	  = 'SELECT MAX(word_id) FROM '.TABLE_PREFIX.'glossary';
		$result   = mysql_query($sql, $db);
		$next_index = mysql_fetch_row($result);
		$next_index = $next_index[0] + 1;

		// $glossary_index_map[old_glossary_id] = new_glossary_id;
		$glossary_index_map = array();

		$sql = '';
		$index_offset = '';
		$fp  = fopen($import_path.'glossary.csv','rb');
		while ($data = fgetcsv($fp, 100000, ',')) {
			if ($sql == '') {
				// first row stuff
				$sql = 'INSERT INTO '.TABLE_PREFIX.'glossary VALUES ';
			}
			$sql .= '(';
			if (!isset($glossary_index_map[$data[0]])) {
				while (in_array($next_index, $glossary_index_map)) {
					$next_index++;
				}
				$glossary_index_map[$data[0]] = $next_index;
			}
		
			$sql .= $glossary_index_map[$data[0]] . ',';
			$sql .= $_SESSION['course_id'] .',';

			// title
			$data[1] = translate_whitespace($data[1]);
			$sql .= "'".addslashes($data[1])."',";

			// definition
			$data[2] = translate_whitespace($data[2]);
			$sql .= "'".addslashes($data[2])."',";

			// related_word_id
			if ($data[3]) {
				if (!isset($glossary_index_map[$data[3]])) {
					while (in_array($next_index, $glossary_index_map)) {
						$next_index++;
					}
					$glossary_index_map[$data[3]] = $next_index;
				}
				
				$sql .= $glossary_index_map[$data[3]];
			} else {
				$sql .= '0';
			}
			$next_index++;
			$sql .= '),';
		}
		*/
	}

	// private: resource_categories
	function restore_links() {
		/* resource_categories.csv */
		/* get the CatID offset:
		$lock_sql = 'LOCK TABLES '.TABLE_PREFIX.'resource_categories WRITE';
		$result   = mysql_query($lock_sql, $db);

		$sql = '';
		$link_cat_map = array();
		$fp  = fopen($import_path.'resource_categories.csv','rb');
		while ($data = fgetcsv($fp, 100000, ',')) {
			$sql = 'INSERT INTO '.TABLE_PREFIX.'resource_categories VALUES ';
			$sql .= '(0,';
			$sql .= $_SESSION['course_id'] .',';

			// CatName
			$data[1] = translate_whitespace($data[1]);
			$sql .= "'".addslashes($data[1])."',";

			// CatParent
			if ($data[2] == 0) {
				$sql .= 'NULL';
			} else {
				$sql .= $data[2] + $index_offset;
			}
			$sql .= ')';

			$result = mysql_query($sql, $db);

			$link_cat_map[$data[0]] = mysql_insert_id($db);
		}
		fclose($fp);
		*/
	}

	// private
	function restore_resource_links() {
		/*
		$sql = '';
		$fp  = fopen($import_path.'resource_links.csv','rb');
		while ($data = fgetcsv($fp, 100000, ',')) {
			if ($sql == '') {
				// first row stuff
				$sql = 'INSERT INTO '.TABLE_PREFIX.'resource_links VALUES ';
			}
			$sql .= '(0, ';
			$sql .= $link_cat_map[$data[0]] . ',';

			// URL
			$data[1] = translate_whitespace($data[1]);
			$sql .= "'".addslashes($data[1])."',";

			// LinkName
			$data[2] = translate_whitespace($data[2]);
			$sql .= "'".addslashes($data[2])."',";

			// Description
			$data[3] = translate_whitespace($data[3]);
			$sql .= "'".addslashes($data[3])."',";

			// Approved
			$sql .= $data[4].',';

			// SubmitName
			$data[5] = translate_whitespace($data[5]);
			$sql .= "'".addslashes($data[5])."',";

			// SubmitEmail
			$data[6] = translate_whitespace($data[6]);
			$sql .= "'".addslashes($data[6])."',";

			// SubmitDate
			$data[7] = translate_whitespace($data[7]);
			$sql .= "'".addslashes($data[7])."',";

			$sql .= $data[8]. '),';
		}
		*/
	}

	// private
	function restore_news() {
		/* news.csv */
		/*
		$sql = '';
		$fp  = fopen($import_path.'news.csv','rb');
		while ($data = fgetcsv($fp, 100000, ',')) {
			if ($sql == '') {
				// first row stuff
				$sql = 'INSERT INTO '.TABLE_PREFIX.'news VALUES ';
			}
			$sql .= '(0,'.$_SESSION['course_id'].', '. $_SESSION['member_id'].', ';

			// date
			$data[0] = translate_whitespace($data[0]);
			$sql .= "'".addslashes($data[0])."',";

			$i=1;
			if ($_FILES['file']['type'] != 'application/x-gzip-compressed') {
				// for versions 1.1+
				// formatting
				$data[$i] = translate_whitespace($data[$i]);
				$sql .= $data[$i].',';
				$i++;
			} else {
				$sql .= '0,';
			}

			// title
			$data[$i] = translate_whitespace($data[$i]);
			$sql .= "'".addslashes($data[$i])."',";
			$i++;

			// body
			$data[$i] = translate_whitespace($data[$i]);
			$sql .= "'".addslashes($data[$i])."'";

			$sql .= '),';
		}
		*/
	}

	// private
	function restore_tests() {
		/* tests.csv */
		/* get the test_id offset:
		$lock_sql = 'LOCK TABLES '.TABLE_PREFIX.'tests WRITE';
		$result   = mysql_query($lock_sql, $db);

		$sql		= 'SELECT MAX(test_id) AS max_test_id FROM '.TABLE_PREFIX.'tests';
		$result		= mysql_query($sql, $db);
		$next_index = mysql_fetch_assoc($result);
		$next_index = $next_index['max_test_id'] + 1;

		$sql = '';
		$index_offset = '';
		$fp  = fopen($import_path.'tests.csv','rb');
		while ($data = fgetcsv($fp, 100000, ',')) {
			if ($sql == '') {
				// first row stuff
				$index_offset = $next_index - $data[0];
				$sql = 'INSERT INTO '.TABLE_PREFIX.'tests VALUES ';
			}
			$sql .= '(';
			$sql .= ($data[0] + $index_offset) . ',';
			$sql .= $_SESSION['course_id'] .',';

			// title
			$data[1] = translate_whitespace($data[1]);
			$sql .= "'".addslashes($data[1])."',";

			// format
			$sql .= $data[2].',';

			// start date
			$data[3] = translate_whitespace($data[3]);
			$sql .= "'".addslashes($data[3])."',";
			
			// end date
			$data[4] = translate_whitespace($data[4]);
			$sql .= "'".addslashes($data[4])."',";

			// randomize order
			$sql .= $data[5].',';

			// num_questions
			$sql .= $data[6].',';

			// instructions
			$data[7] = translate_whitespace($data[7]);
			$sql .= "'".addslashes($data[7])."'";

			if (version_compare($version, '1.4', '>=')) {
				$sql .= ',' . ($translated_content_ids[$data[8]] ? $translated_content_ids[$data[8]] : 0). ',';
				$sql .= $data[9] . ',';
				$sql .= $data[10] . ',';
				$sql .= $data[11];
			} else {
				$sql .= ',0,0,0,0';
			}

			// v1.4.2 added `num_taken`, `anonymouse`
			if (version_compare($version, '1.4.2', '>=')) {
				$sql .= ',' . $data[12] .',' .$data[13] ;
			} else {
				$sql .= ',0,0';
			}

			$sql .= '),';
		}
		*/
	}

	// private
	function restore_tests_questions() {
		/* tests_questions.csv */

		/*
		$sql = '';
		$fp  = fopen($import_path.'tests_questions.csv','rb');
		while ($data = fgetcsv($fp, 100000, ',')) {
			if ($sql == '') {
				// first row stuff
				$sql = 'INSERT INTO '.TABLE_PREFIX.'tests_questions VALUES ';
			}
			$sql .= '(0, ';
			$sql .= ($data[0] + $index_offset) . ','; // test_id
			$sql .= $_SESSION['course_id'] .',';

			// ordering
			$sql .= $data[1].',';

			// type
			$sql .= $data[2].',';

			// weight
			$sql .= $data[3].',';

			// required
			$sql .= $data[4].',';

			// feedback
			$data[5] = translate_whitespace($data[5]);
			$sql .= "'".addslashes($data[5])."',";

			// question
			$data[6] = translate_whitespace($data[6]);
			$sql .= "'".addslashes($data[6])."',";

			// choice_0
			$data[7] = translate_whitespace($data[7]);
			$sql .= "'".addslashes($data[7])."',";

			// choice_1
			$data[8] = translate_whitespace($data[8]);
			$sql .= "'".addslashes($data[8])."',";

			// choice_2
			$data[9] = translate_whitespace($data[9]);
			$sql .= "'".addslashes($data[9])."',";

			// choice_3
			$data[10] = translate_whitespace($data[10]);
			$sql .= "'".addslashes($data[10])."',";

			// choice_4
			$data[11] = translate_whitespace($data[11]);
			$sql .= "'".addslashes($data[11])."',";

			// choice_5
			$data[12] = translate_whitespace($data[12]);
			$sql .= "'".addslashes($data[12])."',";

			// choice_6
			$data[13] = translate_whitespace($data[13]);
			$sql .= "'".addslashes($data[13])."',";

			// choice_7
			$data[14] = translate_whitespace($data[14]);
			$sql .= "'".addslashes($data[14])."',";

			// choice_8
			$data[15] = translate_whitespace($data[15]);
			$sql .= "'".addslashes($data[15])."',";

			// choice_9
			$data[16] = translate_whitespace($data[16]);
			$sql .= "'".addslashes($data[16])."',";

			// answer_0
			$sql .= $data[17].',';

			// answer_1
			$sql .= $data[18].',';

			// answer_2
			$sql .= $data[19].',';

			// answer_3/
			$sql .= $data[20].',';

			// answer_4
			$sql .= $data[21].',';

			// answer_5
			$sql .= $data[22].',';

			// answer_6
			$sql .= $data[23].',';

			// answer_7
			$sql .= $data[24].',';

			// answer_8
			$sql .= $data[25].',';

			// answer_9
			$sql .= $data[26].',';

			// answer_size
			$sql .= $data[27];

			if (version_compare($version, '1.4', '>=')) {
				$sql .= ',' . ($translated_content_ids[$data[28]] ? $translated_content_ids[$data[28]] : 0) ;
			} else {
				$sql .= ',0';
			}

			$sql .= '),';
		}
		*/
	}

	// private
	function restore_polls() {
		/* polls.csv */
		/*
		$sql = '';
		$fp = fopen($import_path.'polls.csv','rb');
		while ($data = fgetcsv($fp, 100000, ',')) {
			if ($sql == '') {
				// first row stuff
				$sql = 'INSERT INTO '.TABLE_PREFIX.'polls VALUES ';
			}
			$sql .= '(0, ' . $_SESSION['course_id'] . ', ';

			// question
			$data[0] = translate_whitespace($data[0]);
			$sql .= "'".addslashes($data[0])."',";

			// date
			$data[1] = translate_whitespace($data[1]);
			$sql .= "'".addslashes($data[1])."',0,";

			// choice 1
			$data[2] = translate_whitespace($data[2]);
			$sql .= "'".addslashes($data[2])."',0,";

			// choice 2
			$data[3] = translate_whitespace($data[3]);
			$sql .= "'".addslashes($data[3])."',0,";

			// choice 3
			$data[4] = translate_whitespace($data[4]);
			$sql .= "'".addslashes($data[4])."',0,";

			// choice 4
			$data[5] = translate_whitespace($data[5]);
			$sql .= "'".addslashes($data[5])."',0,";

			// choice 5
			$data[6] = translate_whitespace($data[6]);
			$sql .= "'".addslashes($data[6])."',0,";

			// choice 6
			$data[7] = translate_whitespace($data[7]);
			$sql .= "'".addslashes($data[7])."',0,";

			// choice 7
			$data[8] = translate_whitespace($data[8]);
			$sql .= "'".addslashes($data[8])."',0";

			$sql .= '),';
		}
		*/
	}

	function restore_stats() {

	}
}

?>