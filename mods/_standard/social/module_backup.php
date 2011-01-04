<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2009										*/
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: module_backup.php 10055 2010-06-29 20:30:24Z cindy $

/* each table to be backed up. includes the sql entry and fields */
/*
 * TODO: decide how to back up all the social data
 *
$dirs = array();
$dirs['hello_world/'] = AT_CONTENT_DIR . 'hello_world' . DIRECTORY_SEPARATOR;

$sql = array();
$sql['hello_world']  = 'SELECT value FROM '.TABLE_PREFIX.'hello_world WHERE course_id=?';

function hello_world_convert($row, $course_id, $table_id_map, $version) {
	$new_row = array();
	$new_row[0]  = $course_id;
	$new_row[1]  = $row[0];

	return $new_row;
}
*/
?>
