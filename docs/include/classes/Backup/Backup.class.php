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

require_once(AT_INCLUDE_PATH.'classes/zipfile.class.php');
require_once(AT_INCLUDE_PATH.'lib/backup_table_defns.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

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
	function generateFileName( ) {
		global $system_courses;
		$title = $system_courses[$this->course_id]['title'];
		$title = str_replace(' ',  '_', $title);
		$title = str_replace('%',  '',  $title);
		$title = str_replace('\'', '',  $title);
		$title = str_replace('"',  '',  $title);
		$title = str_replace('`',  '',  $title);

		$title .= '_' . date('d_M_y') . '.zip';

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
			$content = substr($content, 0, -1);
			$content .= "\n";
			$counter++;
		}
		
		@mysql_free_result($result); 

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

			require_once(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
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
		$row['file_size']		 = $this->zipfile->get_size();
		$row['file_name']        = $this->generateFileName();

		$this->add($row);

		return TRUE;
	}

	// public
	function upload($_FILES, $description) {
		global $savant;
		$msg =& new Message($savant);
	
		$ext = pathinfo($_FILES['file']['name']);
		$ext = $ext['extension'];

		if (!$_FILES['file']['name'] || !is_uploaded_file($_FILES['file']['tmp_name']) || ($ext != 'zip')) {
			if ($_FILES['file']['error'] == 1) { // LEQ to UPLOAD_ERR_INI_SIZE
				$errors = array('FILE_TOO_BIG', ini_get('upload_max_filesize'));
				$msg->addError($errors); 
			} else {
				$msg->addError('FILE_NOT_SELECTED');
			}
		}

		if ($_FILES['file']['size'] == 0) {
			$msg->addError('IMPORTFILE_EMPTY');
		}

		if($msg->containsErrors()) {
			return;
		}

		$row = array();
		$row['description'] = $description;
		$row['system_file_name'] =  md5(time());
		$row['contents'] = '';
		$row['file_size'] = $_FILES['file']['size'];
		$row['file_name'] = $_FILES['file']['name'];

		$backup_path = AT_CONTENT_DIR . 'backups/' . $this->course_id .'/';

		move_uploaded_file($_FILES['file']['tmp_name'], $backup_path . $row['system_file_name'].'.zip');

		$this->add($row);

		return;
	}

	// private
	// adds a backup to the database
	function add($row) {
		$sql = "INSERT INTO ".TABLE_PREFIX."backups VALUES (0, $this->course_id, NOW(), '$row[description]', '$row[file_size]', '$row[system_file_name]', '$row[file_name]', '$row[contents]')";
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
	function getAvailableList() {
		$backup_list = array();

		$sql	= "SELECT *, UNIX_TIMESTAMP(date) AS date_timestamp FROM ".TABLE_PREFIX."backups WHERE course_id=$this->course_id ORDER BY date DESC";
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
		$file_name = $my_backup['file_name'];

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
	function getRow($backup_id, $course_id = 0) {
		if ($course_id) {
			$sql	= "SELECT *, UNIX_TIMESTAMP(date) AS date_timestamp FROM ".TABLE_PREFIX."backups WHERE backup_id=$backup_id AND course_id=$course_id";
		} else {
			$sql	= "SELECT *, UNIX_TIMESTAMP(date) AS date_timestamp FROM ".TABLE_PREFIX."backups WHERE backup_id=$backup_id AND course_id=$this->course_id";
		}

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

	function getVersion() {
		if ($version = file($this->import_dir.'atutor_backup_version')) {
			return trim($version[0]);
		} else {
			return false;
		}
	}

	function restore($material, $action, $backup_id, $from_course_id = 0) {
		require_once(AT_INCLUDE_PATH.'classes/pclzip.lib.php');
		require_once(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
		require_once(AT_INCLUDE_PATH.'classes/Backup/TableBackup.class.php');

		if (!$from_course_id) {
			$from_course_id = $this->course_id;
		}

		// 1. get backup row/information
		$my_backup = $this->getRow($backup_id, $from_course_id);

		@mkdir(AT_CONTENT_DIR . 'import/' . $this->course_id);
		$this->import_dir = AT_CONTENT_DIR . 'import/' . $this->course_id . '/';

		// 2. extract the backup
		$archive = new PclZip(AT_BACKUP_DIR . $from_course_id. '/' . $my_backup['system_file_name']. '.zip');
		if ($archive->extract(	PCLZIP_OPT_PATH,	$this->import_dir, 
								PCLZIP_CB_PRE_EXTRACT,	'preImportCallBack') == 0) {
			die("Error : ".$archive->errorInfo(true));
		}

		// 3. get the course's max_quota. if backup is too big AND we want to import files then abort/return FALSE
		/* get the course's max_quota */
		// $this->getFilesSize();

		// 4. figure out version number
		$this->version = $this->getVersion();
		//debug('version: '.$this->version);
		if (!$this->version) {
			exit('version not found. backups < 1.3 are not supported.');
		}

		// 5. if override is set then delete the content
		if ($action == 'overwrite') {
			//debug('deleting content - overwrite');
			require_once(AT_INCLUDE_PATH.'lib/delete_course.inc.php'); /* for delete_course() */
			delete_course($this->course_id, $material, $rel_path = '../../');
			$_SESSION['s_cid'] = 0;
		} else {
			//debug('appending content');
		}

		if (($material === TRUE) || isset($material['files'])) {
			$return = $this->restore_files();
			if ($return === false) {
				exit('no space for files');
			}
			unset($material['files']);

		}

		$TableFactory =& new TableFactory($this->version, $this->db, $this->course_id, $this->import_dir);

		// 6. import csv data that we want

		if (($material === TRUE) || isset($material['links'])) {
			$table  = $TableFactory->createTable('resource_categories');
			$table->restore();

			$table  = $TableFactory->createTable('resource_links');
			$table->restore();
		} 
		if (($material === TRUE) || isset($material['content'])) {
			$table  = $TableFactory->createTable('content');
			$table->restore();

			$table  = $TableFactory->createTable('related_content');
			$table->restore();
		}
		if (($material === TRUE) || isset($material['groups'])) {
			$table  = $TableFactory->createTable('groups');
			$table->restore();
		}
		if (($material === TRUE) || isset($material['tests'])) {
			$table  = $TableFactory->createTable('tests');
			$table->restore();

			$table  = $TableFactory->createTable('tests_questions_categories');
			$table->restore();

			$table  = $TableFactory->createTable('tests_questions');
			$table->restore();

			$table  = $TableFactory->createTable('tests_questions_assoc');
			$table->restore();
		}
		if (($material === TRUE) || isset($material['stats'])) {
			$table  = $TableFactory->createTable('stats');
			$table->restore();
		}
		if (($material === TRUE) || isset($material['glossary'])) {
			$table  = $TableFactory->createTable('glossary');
			$table->restore();
		}
		if (($material === TRUE) || isset($material['news'])) {
			$table  = $TableFactory->createTable('news');
			$table->restore();
		}
		if (($material === TRUE) || isset($material['forums'])) {
			$table  = $TableFactory->createTable('forums');
			$table->restore();

			$table  = $TableFactory->createTable('forums_courses');
			$table->restore();
		}
		if (($material === TRUE) || isset($material['polls'])) {
			$table  = $TableFactory->createTable('polls');
			$table->restore();
		}

		// 7. delete import files
		clr_dir($this->import_dir);
	}

	// private
	function restore_files() {
		$sql	= "SELECT max_quota FROM ".TABLE_PREFIX."courses WHERE course_id=$this->course_id";
		$result = mysql_query($sql, $this->db);
		$row	= mysql_fetch_assoc($result);

		if ($row['max_quota'] != AT_COURSESIZE_UNLIMITED) {
			global $MaxCourseSize, $MaxCourseFloat;

			if ($row['max_quota'] == AT_COURSESIZE_DEFAULT) {
				$row['max_quota'] = $MaxCourseSize;
			}
			
			$totalBytes   = dirsize($this->import_dir . 'content/');
			
			$course_total = dirsize(AT_CONTENT_DIR . $this->course_id . '/');
		
			$total_after  = $row['max_quota'] - $course_total - $totalBytes + $MaxCourseFloat;

			if ($total_after < 0) {
				//debug('not enough space. delete everything');
				// remove the content dir, since there's no space for it
				clr_dir($this->import_dir);
				return FALSE;
					
				//require(AT_INCLUDE_PATH.'header.inc.php');
				//$errors[] = array(AT_ERROR_NO_CONTENT_SPACE, number_format(-1*($total_after/AT_KBYTE_SIZE), 2 ) );
				//print_errors($errors);
				//require(AT_INCLUDE_PATH.'footer.inc.php');
			}
		}

		copys($this->import_dir.'content/', AT_CONTENT_DIR . $this->course_id);
	}
}


?>