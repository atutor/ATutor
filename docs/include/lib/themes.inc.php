<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

if (!defined('AT_INCLUDE_PATH')) { exit; }
$db;

/**
* Gets the version of the theme.
* @access  private
* @param   string $theme_name	the name of the theme
* @return  string				the version of the theme
* @author  Shozub Qureshi
*/
function get_version ($theme_name) {
	global $db;

	//Go to db
	$sql    = "SELECT version FROM ".TABLE_PREFIX."themes WHERE title = '$theme_name'";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_array($result);
	
	return $row['version'];	
}

/**
* Gets the name of the folder where the theme is stored
* @access  private
* @param   string $theme_name	the name of the theme
* @return  string				theme folder
* @author  Shozub Qureshi
*/
function get_folder ($theme_name) {

	$fldrname = str_replace(' ', '_', $theme_name);
	return $fldrname;
}

/**
* Gets the attributes of the theme from the themes database table
* @access  private
* @param   string $theme_name	the name of the theme
* @return  array				theme info
* @author  Shozub Qureshi
*/
function get_themes_info($theme_name) {
	global $db;
	//Go to db
	$sql    = "SELECT * FROM ".TABLE_PREFIX."themes WHERE title = '$theme_name'";
	$result = mysql_query($sql, $db);
	
	$info = mysql_fetch_assoc($result);

	return $info;
}

/**
* Checks the status of the Theme
* @access  private
* @param   string $theme_name	the name of the theme
* @return  int    				theme status (0=diabled, 1=enabled, 2=default)
* @author  Shozub Qureshi
*/
function check_status ($theme_name) {
	global $db;
	//Go to db
	$sql    = "SELECT status FROM ".TABLE_PREFIX."themes WHERE title = '$theme_name'";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	
	return $row['status'];
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
	//Go to db
	$sql    = "SELECT title FROM ".TABLE_PREFIX."themes ORDER BY status DESC";
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
* Sets the status of a theme as a default theme
* @access  private
* @param   string $theme_name	the name of the theme
* @author  Shozub Qureshi
*/
function set_theme_as_default ($theme_name) {
	global $db;
	
	//first check if there is another default theme
	$sql    = "UPDATE ".TABLE_PREFIX."themes SET ".
			   "status = '1' WHERE status = '2'";
	$result = mysql_query($sql, $db);
	
	//Set status to '2' (default)
	$sql1    = "UPDATE ".TABLE_PREFIX."themes SET ".
			  "status = '2' WHERE title = '$theme_name'";
	$result1 = mysql_query($sql1, $db);
}

/**
* Sets the status of a theme as enabled
* @access  private
* @param   string $theme_name	the name of the theme
* @author  Shozub Qureshi
*/
function enable_theme ($theme_name) {
	global $db;

	$sql    = "SELECT status FROM ".TABLE_PREFIX."themes WHERE title = '$theme_name'";
	$result = mysql_query ($sql, $db);
	$row    = mysql_fetch_array($result);

	$status = intval($row['status']);

	//If default theme, then it cannot be deleted
	if ($status == 2) {
		//SHOULD NEVER COME HERE AS DEFAULT THEME CANNOT BE ENABLED
		//echo 'you shouldnt be hee, cant enable a default theme';
		exit;
	}

	//Check if theme is available in db
	$sql1 = "UPDATE ".TABLE_PREFIX."themes SET ".
		   "status = '1' WHERE title = '$theme_name'";
	$result1 = mysql_query($sql1, $db);
}

/**
* Sets the status of a theme as disabled
* @access  private
* @param   string $theme_name	the name of the theme
* @author  Shozub Qureshi
*/
function disable_theme ($theme_name) {
	global $db;

	$sql    = "SELECT status FROM ".TABLE_PREFIX."themes WHERE title = '$theme_name'";
	$result = mysql_query ($sql, $db);
	$row    = mysql_fetch_array($result);

	$status = intval($row['status']);

	//If default theme, then it cannot be deleted
	if ($status == 2) {
		//SHOULD NEVER COME HERE AS DEFAULT THEME CANNOT BE DISABLED
		//echo 'you shouldnt be hee, cant disable a default theme';
		exit;
	}
	
	//Check if theme is available in db
	$sql1    = "UPDATE ".TABLE_PREFIX."themes SET ".
				"status = '0' WHERE title = '$theme_name'";
	$result1 = mysql_query($sql1, $db);
}

/**
* Sets the status of a theme as a default theme
* @access  private
* @param   string $theme_name	the name of the theme
* @return  int					success of deletion (1 if successfull, 0 otherwise
* @author  Shozub Qureshi
*/
function delete_theme ($theme_name) {
	require (AT_INCLUDE_PATH . 'lib/filemanager.inc.php'); /* for clr_dir() */

	global $db;
	//Check if trying to delete default theme

	//Get Dir Name
	$sql    = "SELECT dir_name, status FROM ".TABLE_PREFIX."themes WHERE title = '$theme_name'";
	$result = mysql_query ($sql, $db);
	$row    = mysql_fetch_array($result);

	$status = intval($row['status']);

	$i=0;
	foreach($row as $r) {
		$i++;
	}

	//If default theme, then it cannot be deleted
	if ($status == 2) {
		//SHOULD NEVER COME HERE AS DEFAULT THEME CANNOT BE DELETED
		echo 'you shouldnt be here, cant delete a default theme';
		exit;
	}
	
	//if it is the only theme left
	else if ($i == 1) {
		//SHOULD NEVER COME HERE AS DEFAULT THEME IS ALWAYS LAST
		echo 'you shouldnt be here, as default theme is always last';
		exit;
	}

	//Otherwise Clear Directory and delete theme from db
	else {
		$dir    = '../../themes/' . $row['dir_name'];
		
		//Otherwise Set theme as disabled
		disable_theme ($theme_name);
		
		//Clear theme Directory
		clr_dir($dir);

		//Remove from db
		$sql1    = "DELETE FROM ".TABLE_PREFIX."themes WHERE title = '$theme_name'";
		$result1 = mysql_query ($sql1, $db);

		return 1;
	}
	return 0;
}

/**
* Exports the selected theme to a users desktop
* @access  private
* @param   string $theme_name	the name of the theme
* @author  Shozub Qureshi
*/
function export_theme($theme_title) {
	require(AT_INCLUDE_PATH.'classes/zipfile.class.php');				/* for zipfile */
	require(AT_INCLUDE_PATH.'classes/XML/XML_HTMLSax/XML_HTMLSax.php');	/* for XML_HTMLSax */
	require('theme_template.inc.php');									/* for theme XML templates */ 
	
	global $db;
	
	//identify current theme and then searches db for relavent info
	$sql    = "SELECT * FROM ".TABLE_PREFIX."themes WHERE '$theme_title' = title";
	$result = mysql_query($sql, $db);
	$row    = mysql_fetch_array($result);

	$dir1         = $row['dir_name'];
	$title        = $row['title'];
	$version      = $row['version'];
	$last_updated = $row['last_updated'];
	$extra_info   = $row['extra_info'];

	//generate 'theme_info.xml' file based on info	
	$info_xml = str_replace(array('{TITLE}', '{VERSION}',
							'{LAST_UPDATED}', '{EXTRA_INFO}'), 
							array($title, $version, $last_updated, $extra_info),
           				    $theme_template_xml);

	$dir1 = '../../themes/' . $dir1;

	//zip together all the contents of the folder along with the XML file
	$zipfile = new zipfile();
	$zipfile->create_dir('images/');

	$zipfile->add_file($info_xml, 'theme_info.xml');
	
	/* zip other required files */
	$zipfile->add_file(file_get_contents($dir1 . '/admin_footer.tmpl.php'), 'admin_footer.tmpl.php');
	$zipfile->add_file(file_get_contents($dir1 . '/admin_header.tmpl.php'), 'admin_header.tmpl.php');
	$zipfile->add_file(file_get_contents($dir1 . '/course_footer.tmpl.php'), 'course_footer.tmpl.php');
	$zipfile->add_file(file_get_contents($dir1 . '/course_header.tmpl.php'), 'course_header.tmpl.php');
	$zipfile->add_file(file_get_contents($dir1 . '/dropdown_closed.tmpl.php'), 'dropdown_closed.tmpl.php');
	$zipfile->add_file(file_get_contents($dir1 . '/dropdown_open.tmpl.php'), 'dropdown_open.tmpl.php');
	$zipfile->add_file(file_get_contents($dir1 . '/footer.tmpl.php'), 'footer.tmpl.php');
	$zipfile->add_file(file_get_contents($dir1 . '/header.tmpl.php'), 'header.tmpl.php');
	$zipfile->add_file(file_get_contents($dir1 . '/readme.txt'), 'readme.txt');
	$zipfile->add_file(file_get_contents($dir1 . '/screenshot.jpg'), 'screenshot.jpg');
	$zipfile->add_file(file_get_contents($dir1 . '/styles.css'), 'styles.css');
	$zipfile->add_file(file_get_contents($dir1 . '/theme.cfg.php'), 'theme.cfg.php');


	/*Copying files from the images folder*/
	$zipfile->add_dir($dir1 . '/images/', 'images/');
	
	/*close & send*/
	$zipfile->close();
	//Name the Zip file and sends to user for download
	$zipfile->send_file(str_replace(array(' ', ':'), '_', $title));
}

?>