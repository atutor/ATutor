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

require(AT_INCLUDE_PATH.'classes/zipfile.class.php');

define('NUMBER',	1);
define('TEXT',		2);

/**
* Backup
* Class for creating and managing course backups
* @access	public
* @author	Joel Kronenberg
* @package	Backup
*/
class Backup {

	// private
	// number of backups in the backup dir
	var $num_backups;

	// private
	// the current course id
	var $course_id;

	// private
	// where to store the backup
	var $backup_dir;

	// private
	// db handler
	var $db;

	// the backup zipfile Object
	var $zipfile;

	// the timestamp for the zip files
	var $timestamp;

	// constructor
	function Backup(&$db, $course_id = 0) {

		$this->db = $db;

		$this->setCourseID($course_id);
	}

	// public
	// should be used by the admin section
	function setCourseID($course_id) {
		$this->course_id  = $course_id;
		$this->backup_dir = AT_BACKUP_DIR . $course_id . DIRECTORY_SEPARATOR;
	}


	// public
	// call staticly
	function generateFileName($course_id, $timestamp) {
		global $system_courses;
		$title = $system_courses[$course_id]['title'];
		$title = str_replace(' ',  '_', $title);
		$title = str_replace('%',  '',  $title);
		$title = str_replace('\'', '',  $title);
		$title = str_replace('"',  '',  $title);
		$title = str_replace('`',  '',  $title);

		$title .= '_' . date('d_M_y', $timestamp) . '.zip';

		return $title;
	}

	// private
	// quote $line so that it's safe to save as a CSV field
	function quoteCSV($line) {
		$line = str_replace('"', '""', $line);

		$line = str_replace("\n", '\n', $line);
		$line = str_replace("\r", '\r', $line);
		$line = str_replace("\x00", '\0', $line);

		return '"'.$line.'"';
	}
	
	// private
	// add this table to the backup
	// returns the number of rows for that table
	function saveCSV($name, $sql, $fields) {
		$content = '';
		$num_fields = count($fields);
		$counter = 0;

		$result = mysql_query($sql, $this->db);
		while ($row = mysql_fetch_assoc($result)) {
			for ($i=0; $i< $num_fields; $i++) {

				if ($fields[$i][1] == NUMBER) {
					$content .= $row[$fields[$i][0]] . ',';
				} else {
					$content .= $this->quoteCSV($row[$fields[$i][0]]) . ',';
				}
			}
			$content = substr($content, 0, strlen($content)-1);
			$content .= "\n";
			$counter++;
		}
		@mysql_free_result($result); 

		// NOTE: probably want to store time() in a variable so all files get the same time stamp...

		$this->zipfile->add_file($content, $name, $this->timestamp);

		return $counter;
	}

	// public
	// NOTE: should the create() deal with saving it to disk as well? or should it be general to just create it, and not actually
	// responsible for where to save it? (write a diff method to save it after)
	function create($description) {
		global $addslashes, $backup_tables;

		$table_counters = array();

		if ($this->getNumAvailable() >= AT_COURSE_BACKUPS) {
			return FALSE;
		}

		$this->timestamp = time();

		$this->zipfile =& new zipfile();
		if (is_dir(AT_CONTENT_DIR . $this->course_id)) {
			$this->zipfile->add_dir(AT_CONTENT_DIR . $this->course_id . DIRECTORY_SEPARATOR, 'content/');

			require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
			$table_counters['file_manager'] = dirsize(AT_CONTENT_DIR . $this->course_id . DIRECTORY_SEPARATOR, 'content/');
		}

		$package_identifier = VERSION."\n\n\n".'Do not change the first line of this file it contains the ATutor version this backup was created with.';
		$this->zipfile->add_file($package_identifier, 'atutor_backup_version', $this->timestamp);

		// loop through all the tables/fields to save to the zip file:
		foreach ($backup_tables as $name => $info) {
			$table_counters[$name] = $this->saveCSV($name . '.csv', $info['sql'], $info['fields']);
		}

		// if no errors:

		$this->zipfile->close();

		$system_file_name = md5(time());

		$fp = fopen(AT_BACKUP_DIR . $this->course_id . DIRECTORY_SEPARATOR . $system_file_name . '.zip', 'wb+');
		fwrite($fp, $this->zipfile->get_file($backup_course_title));

		$row['description']      = $addslashes($description);
		$row['contents']         = addslashes(serialize($table_counters));
		$row['system_file_name'] = $system_file_name;

		$this->add($row);

		return TRUE;
	}

	// public
	function upload() {

	}

	// private
	// adds a backup to the database
	function add($row) {
		$file_size = $this->zipfile->get_size();

		$sql = "INSERT INTO ".TABLE_PREFIX."backups VALUES (0, $this->course_id, NOW(), '$row[description]', $file_size, '$row[system_file_name]', '$row[contents]')";
		mysql_query($sql, $this->db);
	}

	// public
	// get number of backups
	function getNumAvailable() {
		// use $num_backups, if not set then do a COUNT(*) on the table
		if (isset($this->num_backups)) {
			return $this->num_backups;
		}

		$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."backups WHERE course_id=$this->course_id";
		$result = mysql_query($sql, $this->db);
		$row	= mysql_fetch_assoc($result);

		$this->num_backups = $row['cnt'];
		return $row['cnt'];
	}

	// public
	// get list of backups
	function getAvailableList($course_id) {
		$backup_list = array();

		$sql	= "SELECT *, UNIX_TIMESTAMP(date) AS date_timestamp FROM ".TABLE_PREFIX."backups WHERE course_id=$course_id ORDER BY date DESC";
		$result = mysql_query($sql, $this->db);
		while ($row = mysql_fetch_assoc($result)) {
			$backup_list[$row['backup_id']] = $row;
			$backup_list[$row['backup_id']]['contents'] = unserialize($row['contents']);
		}

		$this->num_backups = count($backup_list);

		return $backup_list;
	}

	// public
	function download($backup_id) { // or fetch()
		$list = $this->getAvailableList($this->course_id);
		if (!isset($list[$backup_id])) {
			// catch the error
			//debug('does not belong to us');
			exit;
		}

		$my_backup = $list[$backup_id];
		$file_name = Backup::generateFileName($this->course_id, $my_backup['date_timestamp']);

		header('Content-Type: application/zip');
		header('Content-transfer-encoding: binary'); 
		header('Content-Disposition: attachment; filename="'.escapeshellcmd(htmlspecialchars($file_name)).'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: '.$my_backup['file_size']);

		readfile(AT_BACKUP_DIR . $this->course_id . DIRECTORY_SEPARATOR . $my_backup['system_file_name']. '.zip');
		exit;
	}

	// public
	function delete($backup_id) {
		$list = $this->getAvailableList($this->course_id);
		if (!isset($list[$backup_id])) {
			// catch the error
			//debug('does not belong to us');
			exit;
		}
		$my_backup = $list[$backup_id];

		// delete the backup file:
		@unlink(AT_BACKUP_DIR . $this->course_id . DIRECTORY_SEPARATOR . $my_backup['system_file_name']. '.zip');

		// delete the row in the table:
		$sql	= "DELETE FROM ".TABLE_PREFIX."backups WHERE backup_id=$backup_id AND course_id=$this->course_id";
		$result = mysql_query($sql, $this->db);
	}

	// public
	function edit($backup_id, $description) {
		// update description in the table:
		$sql	= "UPDATE ".TABLE_PREFIX."backups SET description='$description' WHERE backup_id=$backup_id AND course_id=$this->course_id";
		$result = mysql_query($sql, $this->db);

	}

	// public
	function getRow($backup_id) {
		$sql	= "SELECT *, UNIX_TIMESTAMP(date) AS date_timestamp FROM ".TABLE_PREFIX."backups WHERE backup_id=$backup_id AND course_id=$this->course_id";
		$result = mysql_query($sql, $this->db);

		$row = mysql_fetch_assoc($result);
		if ($row) {
			$row['contents'] = unserialize($row['contents']);
		}
		return $row;
	}

	// public
	function translate_whitespace($input) {
		$input = str_replace('\n', "\n", $input);
		$input = str_replace('\r', "\r", $input);
		$input = str_replace('\x00', "\0", $input);

		return $input;
	}

	// public
	function restore($material, $action, $backup_id) {
		// 1. get backup row/information
		$my_backup = $this->getRow($backup_id);

		$archive = new PclZip(AT_BACKUP_DIR . $this->course_id . DIRECTORY_SEPARATOR . $my_backup['system_file_name']. '.zip');
		if ($archive->extract(	PCLZIP_OPT_PATH,	$import_path, 
								PCLZIP_CB_PRE_EXTRACT,	'preImportCallBack') == 0) {
			die("Error : ".$archive->errorInfo(true));
		}

		// 2. check if backup file is valid (does this have to be done?)

		// 3. get the course's max_quota. if backup is too big AND we want to import files then abort/return FALSE
		/* get the course's max_quota */
		if (isset($material['files'])) {
			debug('want to copy files');
			$sql	= "SELECT max_quota FROM ".TABLE_PREFIX."courses WHERE course_id=$this->course_id";
			$result = mysql_query($sql, $this->db);
			$row	= mysql_fetch_assoc($result);

			if ($row['max_quota'] != AT_COURSESIZE_UNLIMITED) {
				global $MaxCourseSize, $MaxCourseFloat;

				if ($row['max_quota'] == AT_COURSESIZE_DEFAULT) {
					$row['max_quota'] = $MaxCourseSize;
				}

				$totalBytes   = dirsize($import_path.'content/');
				$course_total = dirsize(AT_CONTENT_DIR . $this->course_id . '/');
				$total_after  = $row['max_quota'] - $course_total - $totalBytes + $MaxCourseFloat;

				debug($total_after, 'total_after');
				if ($total_after < 0) {
					debug('not enough space. delete everything');
					/* remove the content dir, since there's no space for it */
					clr_dir($import_path);
					return FALSE;
					/*
					require(AT_INCLUDE_PATH.'header.inc.php');
					$errors[] = array(AT_ERROR_NO_CONTENT_SPACE, number_format(-1*($total_after/AT_KBYTE_SIZE), 2 ) );
					print_errors($errors);
					require(AT_INCLUDE_PATH.'footer.inc.php');
					*/
				}
			}

			copys($import_path.'/content/', AT_CONTENT_DIR . $this->course_id);
		} else {
			debug('skipping files - deleting content/');
			clr_dir($import_path . 'content/');
		}

		// 4. figure out version number
		if ($version = file($import_path.'atutor_backup_version')) {
			$version = $version[0];
		} else {
			$version = null;
		}
		debug('version: '.$version);
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
		$Restore =& new RestoreBackup($this->db, $this->course_id, $import_path, $version );

		//$Restore->restoreContent();
		//$Restore->restoreForums();
		
		// 7. delete import files
		//clr_dir($import_path);
	}
}

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

		//$this->version = $version;
	}

	function restore($material, $action, $backup_id) {
		require_once(AT_INCLUDE_PATH.'classes/pclzip.lib.php');
		require_once(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

	}


	function restoreContent() {
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

	function restoreRelatedContent() { 
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

	function restoreForums() {
		/* forums.csv */
		$sql = '';
		$fp  = fopen($this->import_path.'forums.csv','rb');
		debug($this->import_path.'forums.csv');
		while ($data = fgetcsv($fp, 20000, ',')) {
			debug($data , 'data');

			/**
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

	function restoreGlossary() {
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

	function restoreResourceCategories() {
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

	function restoreResourceLinks() {
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

	function restoreNews() {
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

	function restoreTests() {
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

	function restoreTestsQuestions() {
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

	function restorePolls() {
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
}

/* content.csv */
	$fields = array();
	$fields[0] = array('content_id',		NUMBER);
	$fields[1] = array('content_parent_id', NUMBER);
	$fields[2] = array('ordering',			NUMBER);
	$fields[3] = array('last_modified',		TEXT);
	$fields[4] = array('revision',			NUMBER);
	$fields[5] = array('formatting',		NUMBER);
	$fields[6] = array('release_date',		TEXT);
	$fields[7] = array('keywords',			TEXT);
	$fields[8] = array('content_path',		TEXT);
	$fields[9] = array('title',				TEXT);
	$fields[10] = array('text',				TEXT);

	$backup_tables['content']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'content WHERE course_id='.$_SESSION['course_id'].' ORDER BY content_parent_id, ordering';
	$backup_tables['content']['fields'] = $fields;

/* forums.csv */
	$fields = array();
	$fields[] = array('title',			TEXT);
	$fields[] = array('description',	TEXT);
	// three fields added for v1.4:
	$fields[] = array('num_topics',		NUMBER);
	$fields[] = array('num_posts',		NUMBER);
	$fields[] = array('last_post',		TEXT);

	$backup_tables['forums']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'forums WHERE course_id='.$_SESSION['course_id'].' ORDER BY forum_id ASC';
	$backup_tables['forums']['fields'] = $fields;

/* related_content.csv */
	$fields[0] = array('content_id',			NUMBER);
	$fields[1] = array('related_content_id',	NUMBER);

	$backup_tables['related_content']['sql'] = 'SELECT R.content_id, R.related_content_id 
													FROM '.TABLE_PREFIX.'related_content R, '.TABLE_PREFIX.'content C 
													WHERE C.course_id='.$_SESSION['course_id'].' AND R.content_id=C.content_id ORDER BY R.content_id ASC';
	$fields = array();
	$backup_tables['related_content']['fields'] = $fields;


/* glossary.csv */
	$fields = array();
	$fields[0] = array('word_id',			NUMBER);
	$fields[1] = array('word',				TEXT);
	$fields[2] = array('definition',		TEXT);
	$fields[3] = array('related_word_id',	NUMBER);

	$backup_tables['glossary']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'glossary WHERE course_id='.$_SESSION['course_id'].' ORDER BY word_id ASC';
	$backup_tables['glossary']['fields'] = $fields;

/* resource_categories.csv */
	$fields = array();
	$fields[0] = array('CatID',		NUMBER);
	$fields[1] = array('CatName',	TEXT);
	$fields[2] = array('CatParent', NUMBER);

	$backup_tables['resource_categories']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'resource_categories WHERE course_id='.$_SESSION['course_id'].' ORDER BY CatID ASC';
	$backup_tables['resource_categories']['fields'] = $fields;

/* resource_links.csv */
	$fields = array();
	$fields[0] = array('CatID',			NUMBER);
	$fields[1] = array('Url',			TEXT);
	$fields[2] = array('LinkName',		TEXT);
	$fields[3] = array('Description',	TEXT);
	$fields[4] = array('Approved',		NUMBER);
	$fields[5] = array('SubmitName',	TEXT);
	$fields[6] = array('SubmitEmail',	TEXT);
	$fields[7] = array('SubmitDate',	TEXT);
	$fields[8] = array('hits',			NUMBER);

	$backup_tables['resource_links']['sql'] = 'SELECT L.* FROM '.TABLE_PREFIX.'resource_links L, '.TABLE_PREFIX.'resource_categories C 
													WHERE C.course_id='.$_SESSION['course_id'].' AND L.CatID=C.CatID 
													ORDER BY LinkID ASC';

	$backup_tables['resource_links']['fields'] = $fields;

/* news.csv */
	$fields = array();
	$fields[0] = array('date',		TEXT);
	$fields[1] = array('formatting',NUMBER);
	$fields[2] = array('title',		TEXT);
	$fields[3] = array('body',		TEXT);

	$backup_tables['news']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'news WHERE course_id='.$_SESSION['course_id'].' ORDER BY news_id ASC';
	$backup_tables['news']['fields'] = $fields;
	
/* tests.csv */
	$fields = array();
	$fields[] = array('test_id',			NUMBER);
	$fields[] = array('title',				TEXT);
	$fields[] = array('format',				NUMBER);
	$fields[] = array('start_date',			TEXT);
	$fields[] = array('end_date',			TEXT);
	$fields[] = array('randomize_order',	NUMBER);
	$fields[] = array('num_questions',		NUMBER);
	$fields[] = array('instructions',		TEXT);

	/* four fields added for v1.4 */
	$fields[] = array('content_id',		NUMBER);
	$fields[] = array('automark',		NUMBER);
	$fields[] = array('random',			NUMBER);
	$fields[] = array('difficulty',		NUMBER);

	/* field added for v1.4.2 */
	$fields[] = array('num_takes',		NUMBER);
	$fields[] = array('anonymous',		NUMBER);

	$backup_tables['tests']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'tests WHERE course_id='.$_SESSION['course_id'].' ORDER BY test_id ASC';
	$backup_tables['tests']['fields'] = $fields;

/* tests_questions.csv */
	$fields = array();
	$fields[] = array('test_id',			NUMBER);
	$fields[] = array('ordering',			NUMBER);
	$fields[] = array('type',				NUMBER);
	$fields[] = array('weight',				NUMBER);
	$fields[] = array('required',			NUMBER);
	$fields[] = array('feedback',			TEXT);
	$fields[] = array('question',			TEXT);
	$fields[] = array('choice_0',			TEXT);
	$fields[] = array('choice_1',			TEXT);
	$fields[] = array('choice_2',			TEXT);
	$fields[] = array('choice_3',			TEXT);
	$fields[] = array('choice_4',			TEXT);
	$fields[] = array('choice_5',			TEXT);
	$fields[] = array('choice_6',			TEXT);
	$fields[] = array('choice_7',			TEXT);
	$fields[] = array('choice_8',			TEXT);
	$fields[] = array('choice_9',			TEXT);
	$fields[] = array('answer_0',			NUMBER);
	$fields[] = array('answer_1',			NUMBER);
	$fields[] = array('answer_2',			NUMBER);
	$fields[] = array('answer_3',			NUMBER);
	$fields[] = array('answer_4',			NUMBER);
	$fields[] = array('answer_5',			NUMBER);
	$fields[] = array('answer_6',			NUMBER);
	$fields[] = array('answer_7',			NUMBER);
	$fields[] = array('answer_8',			NUMBER);
	$fields[] = array('answer_9',			NUMBER);
	$fields[] = array('answer_size',		NUMBER);
	$fields[] = array('content_id',			NUMBER);	/* one field added for v1.4 */

	$backup_tables['tests_questions']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'tests_questions WHERE course_id='.$_SESSION['course_id'].' ORDER BY test_id ASC';
	$backup_tables['tests_questions']['fields'] = $fields;

/* polls.csv */
	$fields = array();
	$fields[0] = array('question',		TEXT);
	$fields[1] = array('created_date',	TEXT);
	$fields[2] = array('choice1',		TEXT);
	$fields[3] = array('choice2',		TEXT);
	$fields[4] = array('choice3',		TEXT);
	$fields[5] = array('choice4',		TEXT);
	$fields[6] = array('choice5',		TEXT);
	$fields[7] = array('choice6',		TEXT);
	$fields[8] = array('choice7',		TEXT);

	$backup_tables['polls']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'polls WHERE course_id='.$_SESSION['course_id'];
	$backup_tables['polls']['fields'] = $fields;

/* course_stats.csv */
	$fields = array();
	$fields[0] = array('login_date',	TEXT);
	$fields[1] = array('guests',		NUMBER);
	$fields[2] = array('members',		NUMBER);

	$backup_tables['course_stats']['sql']    = 'SELECT * FROM '.TABLE_PREFIX.'course_stats WHERE course_id='.$_SESSION['course_id'];
	$backup_tables['course_stats']['fields'] = $fields;

	unset($fields);
?>