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


// Returns an array of information on all available themes found from config.inc.php
function get_available_themes () {
	$theme_list = explode(', ' , AVAILABLE_THEMES);
	foreach ($theme_list as $theme) {
		$theme_info [$theme] = get_theme_info($theme);
		$theme_info [$theme]['filename'] = $theme;
	}
	return $theme_info;
}

/*******************************************************************************
1) Send info (theme_title & theme_version) from a different page
2) Prog identifies current theme and then searches db for relavent info
3) generates XML file based on that info
4) zips together all the contents of the folder along with the XML file
5) Names the Zip file and sends to user for download
*******************************************************************************/
function export_theme($theme_title, $theme_version) {

	/*Include relevant parsers/classes*/
	require(AT_INCLUDE_PATH.'classes/zipfile.class.php');				/* for zipfile */
	require(AT_INCLUDE_PATH.'classes/XML/XML_HTMLSax/XML_HTMLSax.php');	/* for XML_HTMLSax */
	require('theme_template.inc.php');									/* for theme XML templates */ 

	
	/*retrieving theme info from db*/
	/*
	$sql    = "SELECT * FROM" . TABLE_PREFIX . "themes WHERE title = $selected_theme AND version = $theme_version";
	
	$result = mysql_query($sql, $db);
	$row    = mysql_fetch_array($result);

	$row['dir_name']     = $dir;
	$row['title']        = $title;
	$row['version']      = $version;
	$row['last_update']  = $last_update;
	$row['extra_info]    = $extra_info;

	$dir = './themes' . $dir;
	*/

	/*create zipfile*/
	$zipfile = new zipfile();
	$zipfile->create_dir('images/');

	$info_xml = str_replace(array('{DIR_NAME}', '{TITLE}', '{VERSION}',
							'{LAST_UPDATED}', '{EXTRA_INFO}'), 
							array($dir, $title, $version, $last_update, $extra_info),
           				    $theme_template_xml);
	$zipfile->add_file($info_xml, 'theme_info.xml');

	/* zip other required files */
	$zipfile->add_file(file_get_contents($dir . '/admin_footer.tmpl.php'), 'admin_footer.tmpl.php');
	$zipfile->add_file(file_get_contents($dir . '/admin_header.tmpl.php'), 'admin_header.tmpl.php');
	$zipfile->add_file(file_get_contents($dir . '/course_footer.tmpl.php'), 'course_header.tmpl.php');
	$zipfile->add_file(file_get_contents($dir . '/course_header.tmpl.php'), 'course_header.tmpl.php');
	$zipfile->add_file(file_get_contents($dir . '/dropdown_closed.tmpl.php'), 'dropdown_closed.tmpl.php');
	$zipfile->add_file(file_get_contents($dir . '/dropdown_open.tmpl.php'), 'dropdown_open.tmpl.php');
	$zipfile->add_file(file_get_contents($dir . '/footer.tmpl.php'), 'footer.tmpl.php');
	$zipfile->add_file(file_get_contents($dir . '/header.tmpl.php'), 'header.tmpl.php');
	$zipfile->add_file(file_get_contents($dir . '/readme.txt'), 'readme.txt');
	$zipfile->add_file(file_get_contents($dir . '/styles.css'), 'styles.css');
	$zipfile->add_file(file_get_contents($dir . '/theme.cfg.php'), 'theme.cfg.php');

	/*Copying files from the images folder*/
	$zipfile->add_dir($dir . '/images/', 'images/');
	
	/*close & send*/
	$zipfile->close();
	$zipfile->send_file(str_replace(array(' ', ':'), '_', $title) . '_theme');
}


?>