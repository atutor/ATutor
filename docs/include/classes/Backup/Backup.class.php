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

		if ($name == 'forums.csv') {
			//debug($fields);
			//exit;
			//debug($sql);
		}
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
		//$content .= "\n\n\n";
		
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
		$row['file_size']		 = $this->zipfile->get_size();

		$this->add($row);

		return TRUE;
	}

	// public
	function upload($_FILES, $description) {
		$ext = pathinfo($_FILES['file']['name']);
		$ext = $ext['extension'];

		if (!$_FILES['file']['name'] || !is_uploaded_file($_FILES['file']['tmp_name']) || ($ext != 'zip')) {
			if ($_FILES['file']['error'] == 1) { // LEQ to UPLOAD_ERR_INI_SIZE
				$errors[] = array(AT_ERROR_FILE_TOO_BIG, ini_get('upload_max_filesize'));
			} else {
				$errors[] = AT_ERROR_FILE_NOT_SELECTED;
			}
		}

		if ($_FILES['file']['size'] == 0) {
			$errors[] = AT_ERROR_IMPORTFILE_EMPTY;
		}

		if(!empty($errors)) {
			return $errors;
		}

		$row = array();
		$row['description'] = $description;
		$row['system_file_name'] =  md5(time());
		$row['contents'] = '';
		$row['file_size'] = $_FILES['file']['size'];

		$backup_path = AT_CONTENT_DIR . 'backups/' . $this->course_id .'/';

		move_uploaded_file($_FILES['file']['tmp_name'], $backup_path . $row['system_file_name'].'.zip');

		$this->add($row);

		return;
	}

	// private
	// adds a backup to the database
	function add($row) {
		$sql = "INSERT INTO ".TABLE_PREFIX."backups VALUES (0, $this->course_id, NOW(), '$row[description]', '$row[file_size]', '$row[system_file_name]', '$row[contents]')";
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

	function getVersion() {
		if ($version = file($this->import_dir.'atutor_backup_version')) {
			return trim($version[0]);
		} else {
			return false;
		}
	}

	function restore($material, $action, $backup_id) {
		require_once(AT_INCLUDE_PATH.'classes/pclzip.lib.php');
		require_once(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
		require_once(AT_INCLUDE_PATH.'classes/Backup/TableBackup.class.php');

		// 1. get backup row/information
		$my_backup = $this->getRow($backup_id);
		debug($my_backup);

		@mkdir(AT_CONTENT_DIR . 'import/' . $this->course_id);
		$this->import_dir = AT_CONTENT_DIR . 'import/' . $this->course_id . '/';


		// 2. extract the backup
		$archive = new PclZip(AT_BACKUP_DIR . $this->course_id . '/' . $my_backup['system_file_name']. '.zip');
		if ($archive->extract(	PCLZIP_OPT_PATH,	$this->import_dir, 
								PCLZIP_CB_PRE_EXTRACT,	'preImportCallBack') == 0) {
			die("Error : ".$archive->errorInfo(true));
		}

		// 3. get the course's max_quota. if backup is too big AND we want to import files then abort/return FALSE
		/* get the course's max_quota */
		// $this->getFilesSize();

		// 4. figure out version number
		$this->version = $this->getVersion();
		debug('version: '.$this->version);
		if (!$this->version) {
			exit('version not found. backups < 1.3 are not supported.');
		}

		// 5. if override is set then delete the content
		if ($action == 'overwrite') {
			debug('deleting content - overwrite');
			//delete_course($_SESSION['course_id'], $entire_course = false, $rel_path = '../../');
			//$_SESSION['s_cid'] = 0;
		} else {
			debug('appending content');
		}

		/*
		if (isset($material['files'])) {
			$return = $this->restore_files();
			if ($return === false) {
				exit('no space for files');
			}
			unset($material['files']);
		}
		*/
		$TableFactory =& new TableFactory($this->version, $this->db, $this->course_id, $this->import_dir);
		debug($TableFactory);

		//$table->restore();
		//print_r($table);

		$material = array('links' => 1);
		// 6. import csv data that we want
		foreach ($material as $name => $garbage) {
			//debug($name .' -> ' . 'convert_'.$name.'()');
			//$this->{'convert_'.$name}();

			debug($name);
			if ($name == 'links') {
				$table  = $TableFactory->createTable('resource_categories');
				$table->restore();

				//$table  = $TableFactory->createTable('resource_links');
				//$table->restore();
			}
		}

		// 7. delete import files
		clr_dir($this->import_path);
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

	$fields[0] = array('title',			TEXT);
	$fields[1] = array('description',	TEXT);
	// three fields added for v1.4:
	$fields[2] = array('num_topics',	NUMBER);
	$fields[3] = array('num_posts',		NUMBER);
	$fields[4] = array('last_post',		TEXT);

	$backup_tables['forums']['sql'] = 'SELECT * FROM '.TABLE_PREFIX.'forums WHERE course_id='.$_SESSION['course_id'].' ORDER BY forum_id';
	$backup_tables['forums']['fields'] = $fields;

/* related_content.csv */
	$fields = array();
	$fields[0] = array('content_id',			NUMBER);
	$fields[1] = array('related_content_id',	NUMBER);

	$backup_tables['related_content']['sql'] = 'SELECT R.content_id, R.related_content_id 
													FROM '.TABLE_PREFIX.'related_content R, '.TABLE_PREFIX.'content C 
													WHERE C.course_id='.$_SESSION['course_id'].' AND R.content_id=C.content_id ORDER BY R.content_id ASC';
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