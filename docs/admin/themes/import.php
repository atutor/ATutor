<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: theme_import.php 1903 2004-10-15 13:48:59Z shozubq $

$page = 'themes';
$_user_location = 'admin';
// 1. define relative path to `include` directory:
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH . 'vitals.inc.php');
require (AT_INCLUDE_PATH . 'lib/filemanager.inc.php'); /* for clr_dir() and dirsize() */
require (AT_INCLUDE_PATH . 'classes/pclzip.lib.php');
require (AT_INCLUDE_PATH . 'classes/Themes/ThemeParser.class.php');

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

$db;

if(isset($_POST['import'])) {
	import_theme();
	$msg->addFeedback('THEME_IMPORT_SUCCESS');
	header('Location: index.php');
	exit;

}

/**
* Imports a theme from a URL or Zip file to Atutor
* @access  private
* @author  Shozub Qureshi
*/
function import_theme() {
	global $db;
	global $msg;
	
	if (isset($_POST['url']) && ($_POST['url'] != 'http://') ) {
		if ($content = @file_get_contents($_POST['url'])) {
	
			// save file to /themes/
			$filename = pathinfo($_POST['url']);
			$filename = $filename['basename'];
			$full_filename = AT_CONTENT_DIR . '/' . $filename;
			
			if (!$fp = fopen($full_filename, 'w+b')) {
				//Cannot open file ($filename)";
				require(AT_INCLUDE_PATH.'header.inc.php'); 
				$errors = array('CANNOT_OPEN_FILE', $filename);
				$msg->printErrors($errors);
				require(AT_INCLUDE_PATH.'footer.inc.php'); 
				exit;
			}
		
			if (fwrite($fp, $content, strlen($content) ) === FALSE) {
				//"Cannot write to file ($filename)";
				require(AT_INCLUDE_PATH.'header.inc.php'); 
				$errors = array('CANNOT_WRITE_FILE', $filename);
				$msg->printErrors($errors);
				require(AT_INCLUDE_PATH.'footer.inc.php'); 
				exit;
			}
			fclose($fp);
		}	
		$_FILES['file']['name']     = $filename;
		$_FILES['file']['tmp_name'] = $full_filename;
		$_FILES['file']['size']     = strlen($content);
		unset($content);
		$url_parts = pathinfo($_POST['url']);
		$package_base_name_url = $url_parts['basename'];
	}
	$ext = pathinfo($_FILES['file']['name']);
	$ext = $ext['extension'];
	
	//error in the file
	if ($_FILES['file']['error'] == 1) {
		require(AT_INCLUDE_PATH.'header.inc.php'); 
		$errors = array('FILE_MAX_SIZE', ini_get('upload_max_filesize'));
		$msg->printErrors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php'); 
		exit;
	}

	//debug($_FILES);
	
	//If file has no name or no address or if the extension is not .zip
	if (!$_FILES['file']['name'] 
		|| (!is_uploaded_file($_FILES['file']['tmp_name']) && !$_POST['url']) 
		|| ($ext != 'zip')) {

			require(AT_INCLUDE_PATH.'header.inc.php'); 
			$msg->printErrors('FILE_NOT_SELECTED');
			require(AT_INCLUDE_PATH.'footer.inc.php'); 
			exit;
	}

	//check if file size is ZERO	
	if ($_FILES['file']['size'] == 0) {
		require(AT_INCLUDE_PATH.'header.inc.php'); 
		$msg->printErrors('IMPORTFILE_EMPTY');
		require(AT_INCLUDE_PATH.'footer.inc.php'); 
		exit;
	}

	// new directory name is the filename minus the extension
	$fldrname    = substr($_FILES['file']['name'], 0, -4);
	$fldrname   = str_replace(' ', '_', $fldrname);
	$import_path = '../../themes/' . $fldrname;

	//check if Folder by that name already exists
	if (is_dir($import_path)) {
		$i = 1;
		while (is_dir($import_path . '_' . $i)) {
			$i++;
		}
		$fldrname    = $fldrname . '_' . $i; 
		$import_path = $import_path . '_' . $i;
	}
	
	//if folder does not exist previously
	if (!@mkdir($import_path, 0700)) {
		require(AT_INCLUDE_PATH.'header.inc.php'); 
		$msg->printErrors('IMPORTDIR_FAILED');
		require(AT_INCLUDE_PATH.'footer.inc.php'); 
		exit;
	}
	
	// unzip file and save into directory in themes
	$archive = new PclZip($_FILES['file']['tmp_name']);

	//extract contents to importpath/foldrname
	if (!$archive->extract($import_path)) {
		require(AT_INCLUDE_PATH.'header.inc.php'); 
		$errors = array('IMPORT_ERROR_IN_ZIP', $archive->errorInfo(true));
		$msg->printErrors($errors);

		clr_dir($import_path);
		require(AT_INCLUDE_PATH.'footer.inc.php'); 
		exit;
	}

	$handle = opendir($import_path);
	while ($file = readdir($handle)) { 
       if (is_dir($import_path.'/'.$file) && $file != '.' && $file != '..') {
		   $folder = $file;
		   //echo $file .'<br />';
	   }
	}

	//debug($folder);
	//debug($import_path.'/'.$folder);
	//debug($import_path);

	//copy contents from importpath/foldrname to importpath
	copys($import_path.'/'.$folder, $import_path);

	//delete importpath/foldrname
	clr_dir($import_path.'/'.$folder);

	$theme_xml = @file_get_contents($import_path . '/theme_info.xml');

	//debug($theme_xml);
	
	//Check if XML file exists (if it doesnt send error and clear directory
	if ($theme_xml == false) {
		//this should be changed after this version to exit if the user does not provide the theme_info.xml file
		$version = '1.4.x';
		$extra_info = 'unspecified';
	}
	
	else {
		//parse information
		$xml_parser =& new ThemeParser();
		$xml_parser->parse($theme_xml);

		$version      = $xml_parser->theme_rows['version'];
		$extra_info   = $xml_parser->theme_rows['extra_info'];
	}

	$title        = str_replace('_', ' ', $fldrname);
	$last_updated = date('Y-m-d');
	$status       = '1';

	//if version number is not compatible with current Atutor version set theme as disabled
	if ($version != VERSION) {
		$status = '0';
	}

	//save information in database
	$sql = "INSERT INTO ".TABLE_PREFIX."themes VALUES ('$title', '$version', '$fldrname', '$last_updated', '$extra_info', '$status')";
	
	$result = mysql_query($sql, $db);	

	if (!$result) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		$msg->printErrors('IMPORT_FAILED');
		require(AT_INCLUDE_PATH.'footer.inc.php'); 
		exit;
	}

	if (isset($_POST['url'])) {
		@unlink($full_filename);
	}
}

?>