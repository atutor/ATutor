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

/**
* ConvertBackup
* Class for restoring a course backup
* @access	public
* @author	Joel Kronenberg, Heidi Hazelton
* @package	Backup
*/
class ConvertBackup {
	var $db;

	var $course_id;
	var $dir;

	var $import_path;
	var $version;

	function ConvertBackup($course_id) {
		//$this->dir = AT_CONTENT_DIR . $course_id . '/';
		$this->import_path =  AT_CONTENT_DIR . 'import/'.$course_id . '/';

		$fp = fopen($this->import_path."atutor_backup_version", "r");
		$this->version = fgets($fp);
		fclose($fp);
	}

	function convert() {							
		$tables = array('related_content','tests','tests_questions', 'polls', 'forums', 'glossary', 'resource_categories', 'resource_links', 'news');
		//go through each file in unzipped dir

		//deal with related_content.csv
		$fp = fopen($this->import_path."related_content.csv", "r");
		while ($row = fgetcsv($fp, 10000)) {
			$row = $this->convert_related_content($row);
		}
	}

	function convert_related_content($row) {
		//rows 0,1

		//< 1.4
		if (version_compare($version, '1.4', '<')) {
			$row[2] = 0;
			$row[3] = 0;
			$row[4] = 0;
		}

		return $row;
	}

	function convert_tests($row) {
		//rows 0-7
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

	function convert_tests_questions($row) {
		//rows 0-27
		if (version_compare($version, '1.4', '<')) {
			$row[28] = 0;
		}
		return $row;
	}

	function convert_polls($row) {
		return $row;
	}

	function convert_forums($row) {
		return $row;
	}

	function convert_glossary($row) {
		return $row;
	}

	function convert_resource_categories($row) {
		return $row;
	}

	function convert_resource_links($row) {
		return $row;
	}

	function convert_news($row) {
		return $row;
	}

}
