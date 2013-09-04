<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

/**
 * This script handles the ajax post submit from "content editor" =? "adpated content"
 * to save the selected alternative into database
 * @see mods/_core/file_manager/filemanager_display.inc.php
 * @var $_POST values: 
 *      pid: primary resource id
 *      a_type: alternative type, must be one of the values in resource_types.type_id
 *      alternative: the location and name of the selected alternative
 */

define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

$pid = intval($_POST['pid']);
$type_id = intval($_POST['a_type']);
$secondary_resource = trim($_POST['alternative']);

// check post vars
if ($pid == 0 || $type_id == 0 || $secondary_resource == '') exit;

global $db;
// delete the existing alternative for this (pid, a_type)
$sql = "SELECT sr.secondary_resource_id 
          FROM %ssecondary_resources sr, %ssecondary_resources_types srt
         WHERE sr.secondary_resource_id = srt.secondary_resource_id
           AND sr.primary_resource_id = %d
           AND sr.language_code = '%s'
           AND srt.type_id=%d";
$rows_existing_secondary = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $pid, $_SESSION['lang'], $type_id));

foreach($rows_existing_secondary as $existing_secondary){

	$sql = "DELETE FROM %ssecondary_resources WHERE secondary_resource_id = %d";
	$result = queryDB($sql, array(TABLE_PREFIX, $existing_secondary['secondary_resource_id']));

	$sql = "DELETE FROM %ssecondary_resources_types WHERE secondary_resource_id = %d AND type_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $existing_secondary['secondary_resource_id'], $type_id));
}

// insert new alternative
$sql = "INSERT INTO %ssecondary_resources (primary_resource_id, secondary_resource, language_code) VALUES (%d, '%s', '%s')";
$result = queryDB($sql, array(TABLE_PREFIX, $pid, $secondary_resource, $_SESSION['lang']));
$secondary_resource_id = at_insert_id();

$sql = "INSERT INTO %ssecondary_resources_types (secondary_resource_id, type_id) VALUES (%d, %d)";
$result = queryDB($sql, array(TABLE_PREFIX, $secondary_resource_id, $type_id));
exit;

?>