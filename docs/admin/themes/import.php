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
// $Id: theme_import.php 1903 2004-10-15 13:48:59Z shozubq $

$page = 'themes';
$_user_location = 'admin';
// 1. define relative path to `include` directory:
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH . 'vitals.inc.php');

if(isset($_POST['import'])) {
	import_theme();
	header('Location: index.php?f='.urlencode_feedback(AT_FEEDBACK_LANG_IMPORTED));
	exit;

}

/* 	1. Go to XML file, 
**  2. Check attributes of theme (are they complete?)
**  3. Send error Message if required info (or file) is not present/complete
**  4. Make new directory
**  5. Copy files into Directory from zip folder
**  6, Enter Information into Db Table
*/
function import_theme(/*$import_path*/) {
	require (AT_INCLUDE_PATH . 'lib/filemanager.inc.php'); /* for clr_dir() and preImportCallBack and dirsize() */
	require (AT_INCLUDE_PATH . 'classes/pclzip.lib.php');
	require (AT_INCLUDE_PATH . 'classes/Themes/ThemeParser.class.php');

	global $db;
	/*global $result;
	global $sql;*/
	
	if (isset($_POST['url']) && ($_POST['url'] != 'http://') ) {
		if ($content = @file_get_contents($_POST['url'])) {
	
			// save file to /themes/
			$filename = pathinfo($_POST['url']);
			$filename = $filename['basename'];
			$full_filename = AT_CONTENT_DIR . '/' . $filename;

			if (!$fp = fopen($full_filename, 'w+b')) {
				echo "Cannot open file ($filename)";
				exit;
			}
	
	
			if (fwrite($fp, $content, strlen($content) ) === FALSE) {
				echo "Cannot write to file ($filename)";
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
		$errors[] = array(AT_ERROR_FILE_MAX_SIZE, ini_get('upload_max_filesize'));
		print_errors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	
	//If file has no name or no address or if the extension is not .zip
	if (!$_FILES['file']['name'] 
		|| (!is_uploaded_file($_FILES['file']['tmp_name']) && !$_POST['url']) 
		|| ($ext != 'zip')) {
			require(AT_INCLUDE_PATH.'header.inc.php');
			$errors[] = AT_ERROR_FILE_NOT_SELECTED;
			print_errors($errors);
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
	}


	//check if file size is ZERO	
	if ($_FILES['file']['size'] == 0) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		$errors[] = AT_ERROR_IMPORTFILE_EMPTY;
		print_errors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	// new directory name is the filename minus the extension
	$fldrname = substr($_FILES['file']['name'], 0, -4);
	$import_path = '../themes/' . $fldrname;

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
		$errors[] = AT_ERROR_IMPORTDIR_FAILED;
		print_errors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	
	// unzip file and save into directory in themes
	$archive = new PclZip($_FILES['file']['tmp_name']);

	if (!$archive->extract($import_path)) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		echo 'Error : '.$archive->errorInfo(true);
		//Error - Must be Valid Zip File
		require(AT_INCLUDE_PATH.'footer.inc.php');
		clr_dir($import_path);
		exit;
	}


	$theme_xml = @file_get_contents($import_path . '/theme_info.xml');
	
	//Check if XML file exists (if it doesnt send error and clear directory
	if ($theme_xml === false) {
		echo 'ERROR - No theme_info.xml present';
		clr_dir($import_path);
		exit;
	}
	
	//parse information
	$xml_parser =& new ThemeParser();
	$xml_parser->parse($theme_xml);

	$fldrname = str_replace('_', ' ', $fldrname);

	$title        = $fldrname;
	$version      = $xml_parser->theme_rows['version'];
	$last_updated = $xml_parser->theme_rows['last_updated'];
	$extra_info   = $xml_parser->theme_rows['extra_info'];
	$status       = '1';

	//if version number is not compatible with current Atutor version display warning message
	/*if ($version != $atutor_version) {
		warnings[] = array(AT_WARNING_INCOPMATIBLE_THEME, $version);
	}*/

	//save information in database
	$sql = "INSERT INTO ".TABLE_PREFIX."themes VALUES ('$title', '$version', '$fldr_name', '$last_updated', '$extra_info', '$status')";
	
	$result = mysql_query($sql, $db);	

	if (!$result) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		$errors[] = AT_ERROR_IMPORT_FAILED;
		print_errors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	if (isset($_POST['url'])) {
		@unlink($full_filename);
	}
}

?>