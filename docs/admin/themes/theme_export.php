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
// $Id$

$_user_location = 'admin';

// 1. define relative path to `include` directory:
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH . 'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/themes.inc.php');


/*******************************************************************************
1) user clicks Export Theme on either
	a) A form that has two buttons (Export/Cancel)
	b) Sends info (theme_title & theme_version) from a different page
2) prog identifies current theme and then searches db for relavent info
3) generates XML file based on that info
4) zips together all the contents of the folder along with the XML file
5) Names the Zip file and sends to user for download
*******************************************************************************/

/*for now assume information is provided*/
if(isset($_POST['export'])) {
	export_theme($_POST['select_export']);
}

if(isset($_POST['enable'])) {
	enable_theme ($_POST['select_enable]');
}

if(isset($_POST['disable'])) {
	disable_theme ($_POST['select_disable]');
}

if(isset($_POST['delete'])) {
	delete_theme ($_POST['select_delete]');
}

if(isset($_POST['default'])) {
	set_theme_as_default ($_POST['select_default]');
}

function export_theme($theme_title) {
	require(AT_INCLUDE_PATH.'classes/zipfile.class.php');				/* for zipfile */
	require(AT_INCLUDE_PATH.'classes/XML/XML_HTMLSax/XML_HTMLSax.php');	/* for XML_HTMLSax */
	require('theme_template.inc.php');									/* for theme XML templates */ 
	
	global $db;
	
	/*retrieving theme info from db*/
	$sql    = "SELECT * FROM ".TABLE_PREFIX."themes WHERE '$theme_title' = title";

	$result = mysql_query($sql, $db);
	
	$row    = mysql_fetch_array($result);

	$dir         = $row['dir_name'];
	$title       = $row['title'];
	$version     = $row['version'];
	$last_update = $row['last_update'];
	$extra_info  = $row['extra_info'];
	$status      = $row['status'];
	
	$zipfile = new zipfile();
	$zipfile->create_dir('images/');

	$info_xml = str_replace(array('{DIR_NAME}', '{TITLE}', '{VERSION}',
							'{LAST_UPDATED}', '{EXTRA_INFO}', '{STATUS}'), 
							array($dir, $title, $version, $last_update, $extra_info, $status),
           				    $theme_template_xml);

	$zipfile->add_file($info_xml, 'theme_info.xml');

	/* zip other required files */
	$zipfile->add_file(file_get_contents($dir . '/admin_footer.tmpl.php'), 'admin_footer.tmpl.php');
	$zipfile->add_file(file_get_contents($dir . '/admin_header.tmpl.php'), 'admin_header.tmpl.php');
	$zipfile->add_file(file_get_contents($dir . '/course_footer.tmpl.php'), 'course_footer.tmpl.php');
	$zipfile->add_file(file_get_contents($dir . '/course_header.tmpl.php'), 'course_header.tmpl.php');
	$zipfile->add_file(file_get_contents($dir . '/dropdown_closed.tmpl.php'), 'dropdown_closed.tmpl.php');
	$zipfile->add_file(file_get_contents($dir . '/dropdown_open.tmpl.php'), 'dropdown_open.tmpl.php');
	$zipfile->add_file(file_get_contents($dir . '/footer.tmpl.php'), 'footer.tmpl.php');
	$zipfile->add_file(file_get_contents($dir . '/header.tmpl.php'), 'header.tmpl.php');
	$zipfile->add_file(file_get_contents($dir . '/readme.txt'), 'readme.txt');
	$zipfile->add_file(file_get_contents($dir . '/styles.css'), 'styles.css');
	$zipfile->add_file(file_get_contents($dir . '/theme.cfg.php'), 'theme.cfg.php');

	//echo 'got past here';

	/*Copying files from the images folder*/
	$zipfile->add_dir($dir . '/images/', 'images/');
	
	/*close & send*/
	$zipfile->close();
	$zipfile->send_file(str_replace(array(' ', ':'), '_', $title));
}

?>