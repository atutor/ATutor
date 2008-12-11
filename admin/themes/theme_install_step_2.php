<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto          */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: 

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
require(AT_INCLUDE_PATH . 'classes/Themes/ThemeParser.class.php');

// theme content folder
$theme_content_folder = AT_CONTENT_DIR . "theme/";

if (isset($_GET["theme"])) $theme = str_replace(array('.','..'), '', $_GET['theme']);
else if (isset($_POST["theme"])) $theme = $_POST["theme"];

if (isset($_GET["title"])) $title = $_GET['title'];
else if (isset($_POST["title"])) $title = $_POST["title"];

if (isset($_GET["permission_granted"])) $permission_granted = $_GET["permission_granted"];
else if (isset($_POST["permission_granted"])) $permission_granted = $_POST["permission_granted"];

// copy theme from content folder into themes folder
if (isset($_GET["theme"]))
{
	copys($theme_content_folder.$theme, '../../themes/'.$theme);

	$theme_xml = @file_get_contents('../../themes/'.$theme . '/theme_info.xml');

	//Check if XML file exists (if it doesnt send error and clear directory)
	if ($theme_xml == false) 
	{
		$version = '1.4.x';
		$extra_info = 'unspecified';
	} 
	else 
	{
		//parse information
		$xml_parser =& new ThemeParser();
		$xml_parser->parse($theme_xml);

		$version      = $xml_parser->theme_rows['version'];
		$extra_info   = $xml_parser->theme_rows['extra_info'];
	}

	if ($title == '') $title = str_replace('_', ' ', $theme);
	$last_updated = date('Y-m-d');
	$status       = '1';

	//if version number is not compatible with current Atutor version, set theme as disabled
	if ($version != VERSION) $status = '0';
	
	//save information in database
	$sql = "INSERT INTO ".TABLE_PREFIX."themes (title, version, dir_name, last_updated, extra_info, status) ".
				"VALUES ('$title', '$version', '$theme', '$last_updated', '$extra_info', '$status')";
	$result = mysql_query($sql, $db);
	
	write_to_log(AT_ADMIN_LOG_INSERT, 'themes', mysql_affected_rows($db), $sql);
}

if (!$result) // error occurs
{
	clr_dir("../../themes/".$theme);
	
	if ($_GET['permission_granted']==1)
	{
		header('Location: '.AT_BASE_HREF.'admin/themes/theme_install_step_3.php?error=1');
	}
	else
	{
		$msg->addError('IMPORT_FAILED');
		header('Location: '.AT_BASE_HREF.'admin/themes/install_themes.php');
	}
}
else // successful
{
	if ($_GET['permission_granted']==1)
	{
		header('Location: '.AT_BASE_HREF.'admin/themes/theme_install_step_3.php?installed=1');
	}
	else
	{
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.AT_BASE_HREF.'admin/themes/index.php');
	}
}
?>