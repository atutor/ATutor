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

	function ConvertBackup($db, $course_id) {
		$this->dir = AT_CONTENT_DIR . $course_id . '/';
		$this->version = $version;
	}

	function convert_related_content($row) {
		row 0
		row 1

		1.4+
			row 2
			row 3
			row 4
		else
			0
			0
			0
		return $row;
	}

	function convert_tests($row) {
		rows 0-7

		1.4-1.4.2
			rows 8-11
			0
			0
		1.4.2+
			rows 8-13
		else
			0's

		return $row;
	}

	function convert_tests_questions($row) {
		rows 0-27

		1.4+
			row 28
		else
			0
		return $row;
	}

	function convert_polls($row) {
		
		1.4.1+
			rows 0-8
		else 			
			make this file/fill with 0s

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
