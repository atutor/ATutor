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
// $Id: theme_export.php 1533 9/8/0411:03:45Z Shozub $


$_user_location = 'public';
// 1. define relative path to `include` directory:
define('AT_INCLUDE_PATH', '../include/');
require (AT_INCLUDE_PATH . 'vitals.inc.php');



if(isset($_POST['import'])) {
	import_theme();

}
if(isset($_POST['cancel'])) {
	echo 'action cancelled!';
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
	
	//check if file size is ZERO	
	if ($_FILES['file']['size'] == 0) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		$errors[] = AT_ERROR_IMPORTFILE_EMPTY;
		print_errors($errors);
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	
	// new directory name is the filename minus the extension
	$import_path = '../themes/' . substr($_FILES['file']['name'], 0, -3) . '/';
	if (!is_dir($import_path)) {
		if (!@mkdir($import_path, 0700)) {
			require(AT_INCLUDE_PATH.'header.inc.php');
			$errors[] = AT_ERROR_IMPORTDIR_FAILED;
			print_errors($errors);
			require(AT_INCLUDE_PATH.'footer.inc.php');
			exit;
		}
	}
	
	// unzip file and save into directory in themes
	$archive = new PclZip($_FILES['file']['tmp_name']);

	if ($archive->extract(	PCLZIP_OPT_PATH,	$import_path,
							PCLZIP_CB_PRE_EXTRACT,	'preImportCallBack') == 0) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		echo 'Error : '.$archive->errorInfo(true);
		//Error - Must be Valid Zip File
		require(AT_INCLUDE_PATH.'footer.inc.php');
		clr_dir($import_path);
		exit;
	}

	$theme_xml = @file_get_contents($import_path . 'theme_info.xml');
	
	//Check if XML file exists (if it doesnt send error and clear directory
	if ($theme_xml === false) {
		echo 'ERROR - No theme_info.xml present';
		clr_dir($import_path);
		exit;
	}
	
	//parse information
	$xml_parser =& new ThemeParser();
	$xml_parser->parse($theme_xml);

	//save information in database
	/*foreach ($xml_parser->theme_rows as $field => $detail) {
		$sql = "INSERT INTO ".TABLE_PREFIX."themes VALUES ('$field', '$detail')";
		mysql_query($sql, $db);	
	}*/

	debug($xml_parser->theme_rows);
	foreach ($xml_parser->theme_rows as $field => $detail)
		echo $field . ' , ' . $detail . '<br>';
	
		

}

?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>"  enctype="multipart/form-data" >
	<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="95%" summary="" align="center">	
	<tr>
		<td class="row1"><?php echo 'Import external Theme' ; ?></td>
	</tr>	
	<tr>
		<td class="row1"><strong><?php echo 'Upload Theme Package'; ?>:</strong> <input type="file" name="file" class="formfield" /><br /><br /></td>
	</tr>	
	<tr>
		<td class="row1" align="center">
		<input type= "submit" name="import" value="Import Theme"> <br><br>
		<input type= "submit" name="cancel" value="Cancel"></td>
	</tr>
	</table>
</form>