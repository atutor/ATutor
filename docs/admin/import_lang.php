<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/


$section = 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/pclzip.lib.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

/* to avoid timing out on large files */
set_time_limit(0);


if ($_POST['cancel']) {
	Header('Location: language.php?f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
	exit;
}

$_SESSION['done'] = 1;

if ($_POST['submit']) {
	if (!$_FILES['file']['name']) {
		Header('Location: language.php?file_missing=1');
		exit;
	}
	if ($_FILES['file']['name'] && is_uploaded_file($_FILES['file']['tmp_name']) && ($_FILES['file']['size'] > 0))
		{
		$new_lang = substr($_FILES['file']['name'], -2);
		//check to see if the language is already installed
		if (isset($available_languages[$new_lang])){
			Header('Location: language.php?lang_exists=1'.SEP.'upload_filename='.urlencode($_FILES['file']['name']));
			exit;
		}

		/* check if ../content/import/ exists */
		$import_path = '../content/import/';

		if (!is_dir($import_path)) {
			if (!@mkdir($import_path, 0700)) {
				$errors[] = AT_ERROR_IMPORTDIR_FAILED;
				print_errors($errors);
				exit;
			}
		}

		$import_path = '../content/import/';
		$archive = new PclZip($_FILES['file']['tmp_name']);
		if ($archive->extract(	PCLZIP_OPT_PATH,	$import_path,
								PCLZIP_CB_PRE_EXTRACT,	'preImportLangCallBack') == 0) {
			dir ("Error : ".$archive->errorInfo(true));
			exit;
		}

		$sql	= "LOAD DATA LOCAL INFILE '".$import_path."language.csv' INTO TABLE ".TABLE_PREFIX."lang2 FIELDS TERMINATED BY ',' ENCLOSED BY '\"'";

		if (mysql_query($sql, $db)) {
			@unlink($import_path . 'language.csv');

			cache_purge('system_langs', 'system_langs');

			$_SESSION['done'] = 1;
			Header('Location: language.php?f='.urlencode_feedback(AT_FEEDBACK_IMPORT_LANG_SUCCESS));
			exit;
		} else {
			/* language.csv */
			$sql = '';
			$fp  = fopen($import_path.'language.csv','r');

			while ($data = fgetcsv($fp, 10000000, ',')) {
				if ($sql == '') {
					/* first row stuff */
					$sql = 'INSERT INTO '.TABLE_PREFIX.'lang2 VALUES ';
				}
				$sql .="('".$data[0]."', ";
				$sql .="'".$data[1]."', ";
				$sql .="'".$data[2]."', ";
				$sql .="'".addslashes($data[3])."', ";
				$sql .="'".$data[4]."'),";
			}
			if ($sql != '') {
				$sql = substr($sql, 0, -1);
				if(!mysql_query($sql, $db)){
					require(AT_INCLUDE_PATH.'admin_html/header.inc.php');
					$errors[]  = AT_ERROR_LANG_IMPORT_FAILED;
					@unlink($import_path . 'language.csv');
					print_errors($errors);
					$_SESSION['done'] = 1;
					require(AT_INCLUDE_PATH.'cc_html/footer.inc.php');
					exit;
				}
			}
			@fclose($fp);

			@unlink($import_path . 'language.csv');

			$_SESSION['done'] = 1;

			cache_purge('system_langs', 'system_langs');
			Header('Location: language.php?f='.urlencode_feedback(AT_FEEDBACK_IMPORT_LANG_SUCCESS));
			exit;
		}

	} else {
		require(AT_INCLUDE_PATH.'admin_html/header.inc.php');
		$errors[]  = AT_ERROR_LANG_IMPORT_FAILED;
		@unlink($import_path . 'language.csv');
		print_errors($errors);
		$_SESSION['done'] = 1;
		require(AT_INCLUDE_PATH.'cc_html/footer.inc.php');
		exit;
	}
}
?>