<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/

if (!defined('AT_INCLUDE_PATH')) { exit; }
$db;

/**
* Gets the name of the folder where the theme is stored
* @access  private
* @param   string $theme_dir	the name of the theme
* @return  string				theme folder
* @author  Shozub Qureshi
*/
//used by preferences.tmpl.php only
function get_folder ($theme_name) {
	global $db;

	$sql    = "SELECT dir_name FROM ".TABLE_PREFIX."themes WHERE title = '$theme_name'";
	$result = mysql_query($sql, $db);
	$row    = mysql_fetch_assoc($result);

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
	global $db;
	//Go to db
	$sql    = "SELECT * FROM ".TABLE_PREFIX."themes WHERE dir_name = '$theme_dir'";
	$result = mysql_query($sql, $db);
	
	$info = mysql_fetch_assoc($result);

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
	global $db;

	$sql    = "SELECT title FROM ".TABLE_PREFIX."themes WHERE dir_name = '$theme_dir'";
	$result = mysql_query($sql, $db);
	$row    = mysql_fetch_assoc($result);

	return $row['title'];
}

/**
* Gets list of enabled themes
* @access  private
* @return  array				the version of the theme
* @author  Shozub Qureshi
*/
function get_enabled_themes () {
	global $db;
	//Go to db
	$sql    = "SELECT title FROM ".TABLE_PREFIX."themes WHERE status = '1' OR status = '2' ORDER BY title";
	$result = mysql_query($sql, $db);
	
	//Get all theme names into array
	$i = 0;
	while ($row = mysql_fetch_array($result)) {
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
	global $db;
	//Go to db
	$sql    = "SELECT title FROM ".TABLE_PREFIX."themes WHERE status = '1' OR status = '2'";
	$result = mysql_query($sql, $db);
		
	return mysql_num_rows($result);
}

/**
* Gets list of disabled themes
* @access  private
* @return  array				the version of the theme
* @author  Shozub Qureshi
*/
function get_disabled_themes () {
	global $db;
	//Go to db
	$sql    = "SELECT title FROM ".TABLE_PREFIX."themes WHERE status = '0'";
	$result = mysql_query($sql, $db);
	
	//Get all theme names into array
	$i = 0;
	while ($row = mysql_fetch_array($result)) {
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
	global $db;
	
	// The ordering is as follow. The default theme followed by ASC ordering of rest of themes
	
	// Assert, one of them must be a default
	$result = mysql_query('SELECT title FROM ' . TABLE_PREFIX . 'themes WHERE status = 2', $db);
	$row = mysql_fetch_assoc($result);
	$first_one = $row['title'];
	
	$themes[$i] = $first_one;
	
	// Go to db
	$sql    = "SELECT title FROM " . TABLE_PREFIX . "themes WHERE title != '$first_one' ORDER BY title ASC";
	$result = mysql_query($sql, $db);
	
	// Get all theme names into array
	$i = 1;
	while ($row = mysql_fetch_assoc($result)) {
		$themes[$i] = $row['title'];
		$i++;
	}
	
	return $themes;
}

function enable_theme ($theme_dir) {
	global $msg, $db;

	if ($_SESSION['prefs']['PREF_THEME'] != $theme_dir) {
		$sql = "UPDATE ".TABLE_PREFIX."themes SET status = '1' WHERE dir_name = '$theme_dir'";
		$result = mysql_query($sql, $db);
		write_to_log(AT_ADMIN_LOG_UPDATE, 'themes', mysql_affected_rows($db), $sql);
	} 
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
}

function disable_theme ($theme_dir) {
	global $msg, $db;

	$sql    = "SELECT status FROM ".TABLE_PREFIX."themes WHERE dir_name = '$theme_dir'";
	$result = mysql_query ($sql, $db);
	$row    = mysql_fetch_array($result);
	$status = intval($row['status']);

	//If default theme, then it cannot be disabled
	if ($status == 2) {
		$msg->addError('THEME_NOT_DISABLED');
		return;
	} else {
		$sql    = "UPDATE ".TABLE_PREFIX."themes SET status = '0' WHERE dir_name = '$theme_dir'";
		$result = mysql_query($sql, $db);

		$feedback = array('THEME_DISABLED', $theme_dir);
		$msg->addFeedback($feedback);

		write_to_log(AT_ADMIN_LOG_UPDATE, 'themes', mysql_affected_rows($db), $sql);
	}
}

function set_theme_as_default ($theme_dir) {
	global $msg, $db;
	
	//unset current default theme
	$sql    = "UPDATE ".TABLE_PREFIX."themes SET status = '1' WHERE status = '2'";
	$result = mysql_query($sql, $db);
	
	write_to_log(AT_ADMIN_LOG_UPDATE, 'themes', mysql_affected_rows($db), $sql);

	//set to default
	$sql    = "UPDATE ".TABLE_PREFIX."themes SET status = '2' WHERE dir_name = '$theme_dir'";
	$result = mysql_query($sql, $db);

	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	$feedback = array('THEME_DEFAULT', $theme_dir);
	$msg->addFeedback($feedback);
	$_SESSION['prefs']['PREF_THEME'] = $theme_dir;

	write_to_log(AT_ADMIN_LOG_UPDATE, 'themes', mysql_affected_rows($db), $sql);
}

function delete_theme ($theme_dir) {
	global $msg, $db;

	//check status
	$sql    = "SELECT status FROM ".TABLE_PREFIX."themes WHERE dir_name='$theme_dir'";
	$result = mysql_query ($sql, $db);
	$row    = mysql_fetch_assoc($result);
	$status = intval($row['status']);

	//can't delete original default or current default theme
	if (($theme_dir == 'default') || ($status == 2)) {
		$msg->addError('THEME_NOT_DELETED');
		return FALSE;

	} else {	//disable, clear directory and delete theme from db

		require (AT_INCLUDE_PATH . 'lib/filemanager.inc.php'); /* for clr_dir() */
		if ($status != 0) {
			disable_theme($theme_dir);
			$msg->deleteFeedback('THEME_DISABLED');
		}

		$dir = '../../themes/' . $theme_dir;
		//chmod($dir, 0777);
		@clr_dir($dir);

		$sql1    = "DELETE FROM ".TABLE_PREFIX."themes WHERE dir_name = '$theme_dir'";
		$result1 = mysql_query ($sql1, $db);

		write_to_log(AT_ADMIN_LOG_DELETE, 'themes', mysql_affected_rows($db), $sql);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		return TRUE;
	}
}

function export_theme($theme_dir) {
	require(AT_INCLUDE_PATH.'classes/zipfile.class.php');				/* for zipfile */
	require(AT_INCLUDE_PATH.'classes/XML/XML_HTMLSax/XML_HTMLSax.php');	/* for XML_HTMLSax */
	require('theme_template.inc.php');									/* for theme XML templates */ 
	
	global $db;
	
	//identify current theme and then searches db for relavent info
	$sql    = "SELECT * FROM ".TABLE_PREFIX."themes WHERE dir_name = '$theme_dir'";
	$result = mysql_query($sql, $db);
	$row    = mysql_fetch_assoc($result);

	$dir          = $row['dir_name'] . '/';
	$title        = $row['title'];
	$version      = $row['version'];
	$last_updated = $row['last_updated'];
	$extra_info   = $row['extra_info'];



	//generate 'theme_info.xml' file based on info	
	$info_xml = str_replace(array('{TITLE}', '{VERSION}', '{LAST_UPDATED}', '{EXTRA_INFO}'), 
							array($title, $version, $last_updated, $extra_info),
           				    $theme_template_xml);

	//zip together all the contents of the folder along with the XML file
	$zipfile = new zipfile();
	$zipfile->create_dir($dir);

	//update installation folder
	$dir1 = '../../themes/' . $dir;

	$zipfile->add_file($info_xml, $dir . 'theme_info.xml');

	/* zip other required files */
	$zipfile->add_dir($dir1, $dir);

	/*close & send*/
	$zipfile->close();
	//Name the Zip file and sends to user for download
	$zipfile->send_file(str_replace(array(' ', ':'), '_', $title));
}

?>