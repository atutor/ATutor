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

	// the zip filename
	var $file_name;

	// the backup zipfile Object
	var $zipfile;

	// constructor
	function Backup($course_id, &$db) {

		$this->db = $db;

		$this->setCourseID($course_id);
	}

	function setCourseID($course_id) {
		$this->course_id  = $course_id;
		$this->backup_dir = AT_BACKUP_DIR . $course_id . DIRECTORY_SEPARATOR;
		$this->file_name  = Backup::generateFileName($_SESSION['course_title']);
	}

	function generateFileName($title) {
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
	function quote_csv($line) {
		$line = str_replace('"', '""', $line);

		$line = str_replace("\n", '\n', $line);
		$line = str_replace("\r", '\r', $line);
		$line = str_replace("\x00", '\0', $line);

		return '"'.$line.'"';
	}
	
	// private
	// add this table to the backup
	function save_csv($name, $sql, $fields) {
		$content = '';
		$num_fields = count($fields);

		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			for ($i=0; $i< $num_fields; $i++) {
				if ($fields[$i][1] == NUMBER) {
					$content .= $row[$fields[$i][0]] . ',';
				} else {
					$content .= $this->quote_csv($row[$fields[$i][0]]) . ',';
				}
			}
			$content = substr($content, 0, strlen($content)-1);
			$content .= "\n";
		}
		@mysql_free_result($result); 

		// NOTE: probably want to store time() in a variable so all files get the same time stamp...

		$this->zipfile->add_file($content, $name.'.csv', time());
	}

	// public
	// NOTE: should the create() deal with saving it to disk as well? or should it be general to just create it, and not actually
	// responsible for where to save it? (write a diff method to save it after)
	function create($row) {
		/*
		$this->zipfile = new zipfile();
		if (is_dir(AT_CONTENT_DIR . $_SESSION['course_id'])) {
			$zipfile->add_dir(AT_CONTENT_DIR . $_SESSION['course_id'].'/', 'content/');
		}

		$package_identifier = VERSION."\n\n\n".'Do not change the first line of this file it contains the ATutor version this backup was created with.';
		$this->zipfile->add_file($package_identifier, 'atutor_backup_version', time());

		define('NUMBER',	1);
		define('TEXT',		2);
		*/

		// loop through all the tables/fields to save to the zip file:
		// ....

		// if no errors:

		// $this->addBackup($row);

		// if no errors:

		// $this->saveBackup();
	}

	// private
	// saves the $zipfile backup to disk
	function save() {

	}

	// public
	function upload() {

	}

	// private
	// adds a backup to the database
	function add($row) {
		// call getNumBackups() first
		$sql = "INSERT INTO ".TABLE_PREFIX."backups VALUES(0, $this->course_id, NOW(), '$row[description]', $row[file_size], '$row[file_name]', '$row[contents]')";
	}

	// public
	// get number of backups
	function getNumAvailable() {
		// use $num_backups, if not set then do a COUNT(*) on the table
	}

	// public
	// get list of backups
	function getAvailableList() {

	}

	// public
	function fetch() { // or download()

	}
}

class RestoreBackup {

	
}

?>