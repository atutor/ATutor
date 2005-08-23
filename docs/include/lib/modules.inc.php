<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$
if (!defined('AT_INCLUDE_PATH')) { exit; }

function get_installed_mods() {
	global $db;

	$installed_mods = array();
	$sql	= "SELECT * FROM ".TABLE_PREFIX."modules ORDER BY dir_name";
	$result = mysql_query($sql, $db);
	while($row = mysql_fetch_assoc($result)) {
		$installed_mods[$row['dir_name']] = $row;
	}
	return $installed_mods;
}

function get_enabled_modules() {
	global $db;

	$modules = array();
	$sql	= "SELECT dir_name FROM ".TABLE_PREFIX."modules WHERE status=".AT_MOD_ENABLED." ORDER BY dir_name";
	$result = mysql_query($sql, $db);
	while($row = mysql_fetch_assoc($result)) {
		$modules[] = $row['dir_name'];
	}
	return $modules;
}

function find_mods($installed_mods = array()) {
	global $db;

	$new_mods = array();
	$dir = opendir(AT_INCLUDE_PATH.'../mods/');
	while (false !== ($file = readdir($dir))) {
		if (($file != '.') && ($file != '..') && ($file != 'readme.txt') && ($file != '.svn') && (!array_key_exists($file, $installed_mods))) { 
			$new_mods[]['dir_name'] = $file;
		}
	}
	closedir($dir);

	return $new_mods;
}

function install($dir_name) {
	global $db;

	// get the module details from the XML file.

	// if use_privilege then set $priv to the next available privilege on the system
	$priv = AT_PRIV_ADMIN; //or function: get next avail priv

	// 

	$sql = 'INSERT INTO '. TABLE_PREFIX . 'modules VALUES ("'.$dir_name.'", '.AT_MOD_DISABLED.', '.$priv.')';
	$result = mysql_query($sql, $db);
}

function enable($dir_name) {
	global $db;

	$sql = 'UPDATE '. TABLE_PREFIX . 'modules SET status='.AT_MOD_ENABLED.' WHERE dir_name="'.$dir_name.'"';
	$result = mysql_query($sql, $db);
}

function disable($dir_name) {
	global $db;

	$sql = 'UPDATE '. TABLE_PREFIX . 'modules SET status='.AT_MOD_DISABLED.' WHERE dir_name="'.$dir_name.'"';
	$result = mysql_query($sql, $db);
}
?>