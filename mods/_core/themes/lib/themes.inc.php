<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/

if (!defined('AT_INCLUDE_PATH')) { exit; }

/**
* Gets the name of the folder where the theme is stored. Used by preferences.tmpl.php only
* @access  private
* @param   string $theme_dir	the name of the theme
* @return  string				theme folder
* @author  Shozub Qureshi
*/
function get_folder ($theme_name) {

	$sql    = "SELECT dir_name FROM %sthemes WHERE title = '%s'";
	$row = queryDB($sql,array(TABLE_PREFIX, $theme_name), TRUE);
	return $row['dir_name'];
}

/**
* Gets the attributes of the theme from the themes database table
* @access  private
* @param   string $theme_dir	the name of the theme
* @return  array				theme info
* @author  Shozub Qureshi
*/
function get_themes_info($theme_dir) {

	$sql    = "SELECT * FROM %sthemes WHERE dir_name = '%s'";
	$info = queryDB($sql, array(TABLE_PREFIX, $theme_dir), TRUE);

	return $info;
}

/**
* Gets the name of the theme
* @access  private
* @param   string $theme_dir	theme folder
* @return  string				theme name
* @author  heidi hazelton
*/
function get_theme_name ($theme_dir) {

	$sql    = "SELECT title FROM %sthemes WHERE dir_name = '%s'";
	$row = queryDB($sql,array(TABLE_PREFIX, $theme_dir), TRUE);

	return $row['title'];
}

/**
* Gets list of enabled themes
* @access  private
* @return  array				the version of the theme
* @author  Shozub Qureshi
*/
function get_enabled_themes ($type = "all") {	
	if ($type == MOBILE_DEVICE) {
		$where_clause = " AND type='".MOBILE_DEVICE."' ";
	} else if ($type == DESKTOP_DEVICE) {
		$where_clause = " AND type='".DESKTOP_DEVICE."' ";
	}
	$sql    = "SELECT title FROM %sthemes WHERE (status = '1' OR status = '2' OR status = '3') ".$where_clause." ORDER BY title";
	$rows_themes = queryDB($sql, array(TABLE_PREFIX));
	//Get all theme names into array
	$i = 0;
	foreach($rows_themes as $row){
		$themes[$i] = $row['title'];
		$i++;
	}
	
	return $themes;
}

/**
* Gets number of enabled themes
* @access  private
* @return  int				the number of enabled themes
* @author  Shozub Qureshi
*/
function num_enabled_themes () {
	//Go to db
	$sql    = "SELECT title FROM %sthemes WHERE status = '1' OR status = '2'";
	$result = queryDB($sql, array(TABLE_PREFIX));	
	return count($result);
}

/**
* Gets list of disabled themes
* @access  private
* @return  array				the version of the theme
* @author  Shozub Qureshi
*/
function get_disabled_themes () {
	//Go to db
	$sql    = "SELECT title FROM %sthemes WHERE status = '0'";
	$rows_themes = queryDB($sql, array(TABLE_PREFIX));	
	//Get all theme names into array
	$i = 0;
	foreach($rows_themes as $row){
		$themes[$i] = $row['title'];
		$i++;
	}
	
	return $themes;
}

/**
* Gets list of all currently installed themes
* @access  private
* @return  array				the version of the theme
* @author  Shozub Qureshi
*/
function get_all_themes () {
	
	// The ordering is as follow. The default theme followed by ASC ordering of rest of themes
	
	// Assert, one of them must be a default
	$row = queryDB('SELECT title FROM %sthemes WHERE status = 2', array(TABLE_PREFIX), TRUE);

	$first_one = $row['title'];
	
	$themes[$i] = $first_one;
	
	// Go to db
	$sql    = "SELECT title FROM %sthemes WHERE title != '%s' ORDER BY title ASC";
	$rows_titles = queryDB($sql, array(TABLE_PREFIX, $first_one));	
	// Get all theme names into array
	$i = 1;
	foreach($rows_titles as $row){
		$themes[$i] = $row['title'];
		$i++;
	}
	
	return $themes;
}

function enable_theme ($theme_dir) {
	global $msg;

	if ($_SESSION['prefs']['PREF_THEME'] != $theme_dir) {
		$sql = "UPDATE %sthemes SET status = '1' WHERE dir_name = '%s'";
		$result = queryDB($sql, array(TABLE_PREFIX, $theme_dir));
		global $sqlout;
		write_to_log(AT_ADMIN_LOG_UPDATE, 'themes', $result, $sqlout);

	} 
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
}

function disable_theme ($theme_dir) {
	global $msg;

	$sql    = "SELECT status FROM %sthemes WHERE dir_name = '%s'";
	$row    = queryDB($sql, array(TABLE_PREFIX, $theme_dir), TRUE);

	$status = intval($row['status']);

	//If default theme, then it cannot be disabled
	if ($status == 2) {
		$msg->addError('THEME_NOT_DISABLED');
		return;
	} else {
		$sql    = "UPDATE %sthemes SET status = '0' WHERE dir_name = '%s'";
		$result = queryDB($sql, array(TABLE_PREFIX, $theme_dir));
		
		$feedback = array('THEME_DISABLED', $theme_dir);
		$msg->addFeedback($feedback);
		global $sqlout;
		write_to_log(AT_ADMIN_LOG_UPDATE, 'themes', $result, $sqlout);
	}
}

function set_theme_as_default ($theme_dir, $type) {
	global $msg;
	
	//unset current default theme
	if ($type == MOBILE_DEVICE) {
		$default_status = 3;
	} else {
		$default_status = 2;
	}
	$sql    = "UPDATE %sthemes SET status = 1 WHERE status = %d";
	$result = queryDB($sql, array(TABLE_PREFIX, $default_status));
	global $sqlout;
	write_to_log(AT_ADMIN_LOG_UPDATE, 'themes', $result, $sqlout);

	//set to default
	$sql    = "UPDATE %sthemes SET status = %d WHERE dir_name = '%s'";
	$result = queryDB($sql, array(TABLE_PREFIX, $default_status, $theme_dir));
	
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	$feedback = array('THEME_DEFAULT', $theme_dir);
	$msg->addFeedback($feedback);

	//only over-ride the current theme if it's not mobile themes.
	if($type != MOBILE_DEVICE){
		$_SESSION['prefs']['PREF_THEME'] = $theme_dir;
	}
	global $sqlout;
	write_to_log(AT_ADMIN_LOG_UPDATE, 'themes', $result, $sqlout);
}

function delete_theme ($theme_dir) {
	global $msg;

	$theme_dir = addslashes($theme_dir);
	
	//check status
	$sql    = "SELECT status, customized FROM %sthemes WHERE dir_name='%s'";
	$row = queryDB($sql, array(TABLE_PREFIX, $theme_dir), TRUE);

	$status = intval($row['status']);
	$customized = intval($row['customized']);
	
	//can't delete if
	// 1. a system default 
	// 2. current default theme
	// 3. a system level theme
	if (($theme_dir == 'default') || ($status == 2) || !$customized && defined('IS_SUBSITE') && IS_SUBSITE) {
		$msg->addError('THEME_NOT_DELETED');
		return FALSE;
	} else {	//disable, clear directory and delete theme from db
		require_once(AT_INCLUDE_PATH.'../mods/_core/file_manager/filemanager.inc.php'); /* for clr_dir() */
		if ($status != 0) {
			disable_theme($theme_dir);
			$msg->deleteFeedback('THEME_DISABLED');
		}

		
		$dir = get_main_theme_dir($customized) . $theme_dir;
		//chmod($dir, 0777);
		@clr_dir($dir);

		$sql1    = "DELETE FROM %sthemes WHERE dir_name = '%s'";
		$result1 = queryDB($sql1, array(TABLE_PREFIX, $theme_dir));
		global $sqlout;
        write_to_log(AT_ADMIN_LOG_DELETE, 'themes', $result1, $sqlout);
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		return TRUE;
	}
}

function export_theme($theme_dir) {
	require(AT_INCLUDE_PATH.'classes/zipfile.class.php');				/* for zipfile */
	require(AT_INCLUDE_PATH.'classes/XML/XML_HTMLSax/XML_HTMLSax.php');	/* for XML_HTMLSax */
	require('theme_template.inc.php');									/* for theme XML templates */ 
	
	
	//identify current theme and then searches db for relavent info
	$sql    = "SELECT * FROM %sthemes WHERE dir_name = '%s'";
	$row = queryDB($sql, array(TABLE_PREFIX, $theme_dir), TRUE);
	
	$dir          = $row['dir_name'] . '/';
	$title        = $row['title'];
	$version      = $row['version'];
	$type         = $row['type'];
	$last_updated = $row['last_updated'];
	$extra_info   = $row['extra_info'];

	//generate 'theme_info.xml' file based on info	
	$info_xml = str_replace(array('{TITLE}', '{VERSION}', '{TYPE}', '{LAST_UPDATED}', '{EXTRA_INFO}'), 
							array($title, $version, $type, $last_updated, $extra_info),
           				    $theme_template_xml);

	//zip together all the contents of the folder along with the XML file
	$zipfile = new zipfile();
	$zipfile->create_dir($dir);

	//update installation folder
	$dir1 = get_main_theme_dir(intval($row["customized"])) . $dir;

	$zipfile->add_file($info_xml, $dir . 'theme_info.xml');

	/* zip other required files */
	$zipfile->add_dir($dir1, $dir);

	/*close & send*/
	$zipfile->close();
	//Name the Zip file and sends to user for download
	$zipfile->send_file(str_replace(array(' ', ':'), '_', $title));
}

?>