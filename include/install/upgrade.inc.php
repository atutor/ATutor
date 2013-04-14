<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2013                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

/*
 * 
 * Note: required files to use this function: include/classes/sqlutility.class.php
 */
function run_upgrade_sql($upgrade_sql_dir, $current_version, $tb_prefix=TABLE_PREFIX, $in_plain_msg=TRUE) {
	global $progress;
	
	// add the ending slash '/' to the input direc
	$upgrade_sql_dir = (substr($upgrade_sql_dir, -1) == '/') ? $upgrade_sql_dir : $upgrade_sql_dir . '/';
	
	// get a list of all update scripts minus sql extension
	$files = scandir($upgrade_sql_dir);
	 
	foreach ($files as $file) {
		if(count($file = explode('_',$file))==5) {
			$file[4] = substr($file[4],0,-3);
			$update_files[$file[2]] = $file;
		}
	}
	
	ksort($update_files);
	foreach ($update_files as $update_file) {
		if(version_compare($current_version, $update_file[4], '<')) {
			//update_one_ver($update_file, $_POST['tb_prefix']);
			
			$update_file = implode('_',$update_file);
			
			$sqlUtility = new SqlUtility();
			$sqlUtility->queryFromFile($upgrade_sql_dir . $update_file.'sql', $tb_prefix, $in_plain_msg);
		}
	}
}
?>
