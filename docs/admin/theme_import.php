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
// $Id: ims_import.php 1308 2004-08-05 15:48:59Z joel $

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php'); /* for clr_dir() and preImportCallBack and dirsize() */
require(AT_INCLUDE_PATH.'classes/pclzip.lib.php');

/* make sure we own this course that we're exporting */

/* to avoid timing out on large files */
@set_time_limit(0);
$_SESSION['done'] = 1;



if (!isset($_POST['submit'])) {
	/* just a catch all */
	header('Location: index.php?f='.AT_FEEDBACK_IMPORT_CANCELLED);
	exit;
}


// Destination directory for theme packages
$import_path = '../themes/';



// If Importing from URL
if (isset($_POST['url']) && ($_POST['url'] != 'http://') ) {
	$_POST['url'] = str_replace("\\", "/", $_POST['url']);  
	if ($content = @file_get_contents($_POST['url'])) {

		// save file to themes directory
		$filename = substr($_POST['url'], -(strlen(strrchr($_POST['url'], "/"))-1));
		$full_filename = realpath($import_path).DIRECTORY_SEPARATOR.$filename;

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

$_section[0][0] = _AT('admin');
$_section[0][1] = 'admin/';
$_section[1][0] = _AT('themes');


// If encountered error while uploading file
if ($_FILES['file']['error'] == 1) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$errors[] = array(AT_ERROR_FILE_MAX_SIZE, ini_get('upload_max_filesize'));
	print_errors($errors);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

// If filename is not detected or file is not properly uploaded
if (   !$_FILES['file']['name'] 
	|| (!is_uploaded_file($_FILES['file']['tmp_name']) && !$_POST['url']) 
	|| ($ext != 'zip'))
	{
		require(AT_INCLUDE_PATH.'header.inc.php');
		$errors[] = AT_ERROR_FILE_NOT_SELECTED;
		print_errors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

// If filesize is zero (there is no file)
if ($_FILES['file']['size'] == 0) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$errors[] = AT_ERROR_IMPORTFILE_EMPTY;
	print_errors($errors);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}
			

/* check if destination directory exists */
if (!is_dir($import_path)) {
	if (!@mkdir($import_path, 0700)) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		$errors[] = AT_ERROR_IMPORTDIR_FAILED;
		print_errors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}


// Get Filename without the dot-extension
$dot_ext = strrchr($_FILES['file']['name'], ".".$ext);
$filename = substr($_FILES['file']['name'], 0, -strlen($dot_ext));


// Directory to save package contents to (folder name is the name of the zipped archive)
$import_path .= $filename;

if (!is_dir($import_path)) {
	if (!@mkdir($import_path, 0700)) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		$errors[] = AT_ERROR_IMPORTDIR_FAILED;
		print_errors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
}


/* extract the entire archive into $import_path */
error_reporting(0);
$archive = new PclZip($_FILES['file']['tmp_name']);
if ($archive->extract(	PCLZIP_OPT_PATH,	$import_path,
						PCLZIP_CB_PRE_EXTRACT,	'preImportCallBack') == 0) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	echo 'Error : '.$archive->errorInfo(true);
	require(AT_INCLUDE_PATH.'footer.inc.php');
	clr_dir($import_path);
	exit;
}
error_reporting(E_ALL ^ E_NOTICE);


// If zip file was downloaded from a url, we have no use for it after extracting it so let's delete it
if (isset($_POST['url']) && ($_POST['url'] != 'http://') ) {
	unlink($full_filename);
}


header('Location: themes.php?f='.AT_FEEDBACK_IMPORT_SUCCESS);
exit;

?>