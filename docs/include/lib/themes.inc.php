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

//This function compares the version of the given theme to the current atutor installation version
function get_version ($theme_name) {
	global $db;

	//Go to db
	$sql    = "SELECT version FROM ".TABLE_PREFIX."themes WHERE title = '$theme_name'";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_array($result);
	
	return $row['version'];	
}

//This function retuns the folder name for the theme
function get_folder ($theme_name) {

	$fldrname = str_replace(' ', '_', $theme_name);
	return $fldrname;
}


//This function returns theme information
function get_themes_info($theme_name) {
	global $db;
	//Go to db
	$sql    = "SELECT * FROM ".TABLE_PREFIX."themes WHERE title = '$theme_name'";
	$result = mysql_query($sql, $db);
	
	$info = mysql_fetch_array($result);

	return $info;
}


//This function checks whether a theme is currently enabled, disabled or default
function check_status ($theme_name) {
	global $db;
	//Go to db
	$sql    = "SELECT status FROM ".TABLE_PREFIX."themes WHERE title = '$theme_name'";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	
	return $row['status'];
}


// Returns an array of information on all available themes found from config.inc.php
function get_available_themes () {

	$theme_list = explode(', ' , AVAILABLE_THEMES);
	foreach ($theme_list as $theme) {
		$theme_info [$theme] = get_theme_info($theme);
		$theme_info [$theme]['filename'] = $theme;
	}
	return $theme_info;
}


//Get list of enabled themes
function get_enabled_themes () {
	global $db;
	//Go to db
	$sql    = "SELECT title FROM ".TABLE_PREFIX."themes WHERE status = '1' OR status = '2'";
	$result = mysql_query($sql, $db);
	
	//Get all theme names into array
	$i = 0;
	while ($row = mysql_fetch_array($result)) {
		$themes[$i] = $row['title'];
		$i++;
	}
	
	return $themes;
}

//Get list of disabled themes
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

//Get all currently installed themes
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

//Set a theme as a default theme (i.e. cannot be deleted)
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

//sets selected theme as "enabled"
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

//sets theme as "disabled"
//PROBLEM: what if theme to be deleted is currently active!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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

//Sets a theme as disabled, deltes it from db and deletes theme's folder.
//PROBLEM: what if theme to be deleted is currently active!!!!!!!!!!!!!!!!!!!!!!!!!!!!
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
		echo 'you shouldnt be hee, cant delete a default theme';
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

}

/*******************************************************************************
1) Send info (theme_title & theme_version) from a different page
2) Prog identifies current theme and then searches db for relavent info
3) generates XML file based on that info
4) zips together all the contents of the folder along with the XML file
5) Names the Zip file and sends to user for download
*******************************************************************************/
function export_theme($theme_title) {
	require(AT_INCLUDE_PATH.'classes/zipfile.class.php');				/* for zipfile */
	require(AT_INCLUDE_PATH.'classes/XML/XML_HTMLSax/XML_HTMLSax.php');	/* for XML_HTMLSax */
	require('theme_template.inc.php');									/* for theme XML templates */ 
	
	global $db;
	
	/*retrieving theme info from db*/
	$sql    = "SELECT * FROM ".TABLE_PREFIX."themes WHERE '$theme_title' = title";

	$result = mysql_query($sql, $db);
	
	$row    = mysql_fetch_array($result);

	$dir1         = $row['dir_name'];
	$title        = $row['title'];
	$version      = $row['version'];
	$last_updated = $row['last_updated'];
	$extra_info   = $row['extra_info'];

	//debug($dir);
	$zipfile = new zipfile();
	$zipfile->create_dir('images/');

	$info_xml = str_replace(array('{TITLE}', '{VERSION}',
							'{LAST_UPDATED}', '{EXTRA_INFO}'), 
							array($title, $version, $last_updated, $extra_info),
           				    $theme_template_xml);

	$dir1 = '../../themes/' . $dir1;

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
	$zipfile->send_file(str_replace(array(' ', ':'), '_', $title));
}

?>