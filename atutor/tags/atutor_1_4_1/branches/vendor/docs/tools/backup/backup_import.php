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

$_include_path = '../../include/';
require($_include_path.'vitals.inc.php');
require($_include_path.'classes/pclzip.lib.php');
require($_include_path.'lib/filemanager.inc.php'); /* for clr_dir() */


if (!isset($_POST['submit'])) {
	$_SESSION['done'] = 1;
	Header('Location: ../index.php?f='.AT_FEEDBACK_IMPORT_CANCELLED);
	exit;
}

if (!$_SESSION['is_admin']) {
	require ($_include_path.'header.inc.php'); 
	$errors[] = AT_ERROR_NOT_OWNER;
	print_errors($errors);
	require ($_include_path.'footer.inc.php'); 
	exit;
}

function translate_whitespace($input) {
	$input = str_replace('\n', "\n", $input);
	$input = str_replace('\r', "\r", $input);
	$input = str_replace('\x00', "\0", $input);

	return $input;
}

/* to avoid timing out on large files */
set_time_limit(0);
$_SESSION['done'] = 1;

	$ext = pathinfo($_FILES['file']['name']);
	$ext = $ext['extension'];

	if (   !$_FILES['file']['name'] 
		|| !is_uploaded_file($_FILES['file']['tmp_name']) 
		|| ($ext != 'zip'))
		{
			require($_include_path.'header.inc.php');
			$errors[] = AT_ERROR_FILE_NOT_SELECTED;
			print_errors($errors);
			require($_include_path.'footer.inc.php');
			exit;
	}

	if ($_FILES['file']['size'] == 0) {
		require($_include_path.'header.inc.php');
		$errors[] = AT_ERROR_IMPORTFILE_EMPTY;
		print_errors($errors);
		require($_include_path.'footer.inc.php');
		exit;
	}
		
	/* check if ../content/import/ exists */
	$import_path = '../../content/import/';
	$content_path = '../../content/';

	if (!is_dir($import_path)) {
		if (!@mkdir($import_path, 0700)) {
			require($_include_path.'header.inc.php');
			$errors[] = AT_ERROR_IMPORTDIR_FAILED;
			print_errors($errors);
			require($_include_path.'footer.inc.php');
			exit;
		}
	}

	$import_path = '../../content/import/'.$_SESSION['course_id'].'/';

	if (!is_dir($import_path)) {
		if (!@mkdir($import_path, 0700)) {
			require($_include_path.'header.inc.php');
			$errors[] = AT_ERROR_IMPORTDIR_FAILED;
			print_errors($errors);
			require($_include_path.'footer.inc.php');
			exit;
		}
	}

	if ($ext != 'zip') {
		/* for versions < 1.1				*/
		/* copy the file in the directory	*/
		move_uploaded_file($_FILES['file']['tmp_name'], $import_path . $_FILES['file']['name']);

		/* unzip and untar the archive */
		$exec	= 'cd '.$import_path.'; gunzip -dc '.escapeshellcmd($_FILES['file']['name']).' | tar -xf -';
		$result = system ( $exec );
	} else {
		/* for versions >= 1.1				*/
		$archive = new PclZip($_FILES['file']['tmp_name']);
		if ($archive->extract(	PCLZIP_OPT_PATH,	$import_path,
								PCLZIP_CB_PRE_EXTRACT,	'preImportCallBack') == 0) {
			die("Error : ".$archive->errorInfo(true));
		}
	}


	$backup_csv_files = array(	'content.csv', 
								'forums.csv',
								'related_content.csv',
								'glossary.csv',
								'resource_categories.csv',
								'resource_links.csv',
								'news.csv',
								'tests.csv',
								'tests_questions.csv');

	$not_valid_backup = false;
	foreach($backup_csv_files as $file_name) {
		if (!file_exists($import_path . $file_name)) {
			$not_valid_backup = true;
			break;
		}
	}

	if ($not_valid_backup) {
		clr_dir($import_path);

		require($_include_path.'header.inc.php');
		$errors[] = AT_ERROR_IMPORTDIR_NOTVALID;
		print_errors($errors);
		require($_include_path.'footer.inc.php');
		exit;
	}

	if (ALLOW_IMPORT_CONTENT) {
		/* get the course's max_quota */
		$sql	= "SELECT max_quota FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);
		$row	= mysql_fetch_array($result);

		if ($row['max_quota'] != -1) {

			$totalBytes   = dirsize($import_path.'content/');
			$course_total = dirsize($content_path.$_SESSION['course_id'].'/');
			$total_after  = $row['max_quota'] - $course_total - $totalBytes + $MaxCourseFloat;

			if ($total_after < 0) {
				/* remove the content dir, since there's no space for it */
				require($_include_path.'header.inc.php');
				$errors[] = array(AT_ERROR_NO_CONTENT_SPACE, number_format(-1*($total_after/AT_KBYTE_SIZE), 2 ) );
				print_errors($errors);
				require($_include_path.'footer.inc.php');
				clr_dir($import_path);
				exit;
			}
		}

		/* move the content to the correct course content directory */
		/* check if this is a 1.2.2 installation, where there is a /content/COURSE_ID/ directory */
		$h = @dir($import_path.'/content/');
		if ($h) {
			$num_files = 0;
			$has_one_file  = true;
			while (@($entry=$h->read()) !== false) {
				if (($entry == '.') || ($entry == '..')) {
					continue;
				}
				$num_files++;
				$this_file = $entry;
				if ($num_files > 1) {
					$has_one_file = false;
					break;
				}
			}
			$h->close();

			if ($has_one_file && is_numeric($this_file)) {
				/* importing from a 1.2.2 installation probably */
				copys($import_path.'/content/'.$this_file.'/', '../../content/'.$_SESSION['course_id']);
			} else {
				copys($import_path.'/content/', '../../content/'.$_SESSION['course_id']);
			}
		}
	}

	$fp = fopen($import_path.'content.csv','rb');

	$lock_sql = 'LOCK TABLES '.TABLE_PREFIX.'content WRITE';
	$result   = mysql_query($lock_sql, $db);

	$sql	  = 'SELECT MAX(content_id) FROM '.TABLE_PREFIX.'content';
	$result   = mysql_query($sql, $db);
	$next_index = mysql_fetch_row($result);
	$next_index = $next_index[0] + 1;

	$sql	  = 'SELECT MAX(ordering) FROM '.TABLE_PREFIX.'content WHERE content_parent_id=0 AND course_id='.$_SESSION['course_id'];
	$result   = mysql_query($sql, $db);
	$next_order = mysql_fetch_row($result);
	$order_offset = $next_order[0];

	$sql = '';
	$index_offset = '';
	$translated_content_ids = array();
	$content_pages = array();
	while ($data = fgetcsv($fp, 100000, ',')) {
		$content_pages[$data[0]] = $data;
	}

	define('CPID', 1);

	function insert_content($content_id, &$content_pages, &$translated_content_ids) {
		global $db;
		global $order_offset;

		if ($content_pages[$content_id] == '') {
			/* should never reach here. */
			debug('CONTENT NOT FOUND! ' . $content_id);
			exit;
		}

		$num_fields = count($content_pages[$content_id]);
		if ($num_fields == 9) {
			$version = '1.2';
		} else if ($num_fields == 11) {
			$version = '1.3';
		} else {
			$version = '1.1';
		}

		if ($content_pages[$content_id][CPID] > 0) {
			if ($translated_content_ids[$content_pages[$content_id][CPID]] == '') {
				$last_id = insert_content(	$content_pages[$content_id][CPID],
											$content_pages,
											$translated_content_ids);
				$translated_content_ids[$content_pages[$content_id][CPID]] = $last_id;
			}
		}

		$sql = 'INSERT INTO '.TABLE_PREFIX.'content VALUES ';
		$sql .= '(0, ';	// content_id
		$sql .= $_SESSION['course_id'] .','; // course_id
		if ($content_pages[$content_id][CPID] == 0) { // content_parent_id
			$sql .= 0;
		} else {
			$sql .= $translated_content_ids[$content_pages[$content_id][CPID]];
		}
		$sql .= ',';

		if ($content_pages[$content_id][CPID] == 0) { // ordering
			$sql .= $content_pages[$content_id][2] + $order_offset;
		} else {
			$sql .= $content_pages[$content_id][2];
		}
		$sql .= ',';

		$sql .= "'".addslashes($content_pages[$content_id][3])."',"; // last_modified
		$sql .= $content_pages[$content_id][4] . ','; // revision
		$sql .= $content_pages[$content_id][5] . ','; // formatting
		$sql .= "'".addslashes($content_pages[$content_id][6])."',"; // release_date

		$i = 7;
		if ($version == '1.3') {
			$sql .= "'".addslashes($content_pages[$content_id][7])."',"; // keywords
			$sql .= "'".addslashes($content_pages[$content_id][8])."',"; // content_path
			$i = 9;
		} else {
			$sql .= "'', '',";
		}
		
		$sql .= "'".addslashes($content_pages[$content_id][$i])."',"; // title
		$i++;

		$content_pages[$content_id][$i] = translate_whitespace($content_pages[$content_id][$i]);

		$sql .= "'".addslashes($content_pages[$content_id][$i])."')"; // text

		$result = mysql_query($sql, $db);
		if (!$result) {
			debug(mysql_error());
			debug($sql);
			exit;
		}
		$last_id = mysql_insert_id( );
		return $last_id;
	}

	$keys = array_keys($content_pages);
	reset($content_pages);
	$num_keys = count($keys);
	for($i=0; $i<$num_keys; $i++) {
		if ($translated_content_ids[$keys[$i]] == '') {
			$last_id = insert_content($keys[$i], $content_pages, $translated_content_ids);
			$translated_content_ids[$keys[$i]] = $last_id;
		}
	}
	fclose($fp);

	$lock_sql = 'UNLOCK TABLES';
	$result   = mysql_query($lock_sql, $db);
	/****************************************************/

	/* related_content.csv */
	$sql = '';
	$fp = fopen($import_path.'related_content.csv','rb');
	while ($data = fgetcsv($fp, 10000000, ',')) {
		if ($sql == '') {
			/* first row stuff */
			$sql = 'INSERT INTO '.TABLE_PREFIX.'related_content VALUES ';
		}
		$sql .= '(';
		$sql .= ($translated_content_ids[$data[0]]) . ',';
		$sql .= ($translated_content_ids[$data[1]]) . '),';
	}
	if ($sql != '') {
		$sql = substr($sql, 0, -1);
		$result = mysql_query($sql, $db);
	}
	fclose($fp);
	unset($translated_content_ids);
	/****************************************************/

	/* forums.csv */
	$sql = '';
	$fp  = fopen($import_path.'forums.csv','rb');
	while ($data = fgetcsv($fp, 10000000, ',')) {
		if ($sql == '') {
			/* first row stuff */
			$sql = 'INSERT INTO '.TABLE_PREFIX.'forums VALUES ';
		}
		$sql .= '(0,'.$_SESSION['course_id'].',';

		$data[0] = translate_whitespace($data[0]);
		$data[1] = translate_whitespace($data[1]);

		$sql .= "'".addslashes($data[0])."',";
		$sql .= "'".addslashes($data[1])."'),";
	}
	if ($sql != '') {
		$sql = substr($sql, 0, -1);
		$result = mysql_query($sql, $db);
	}
	fclose($fp);
	/****************************************************/

	/* glossary.csv */
	/* get the word id offset: */
	$lock_sql = 'LOCK TABLES '.TABLE_PREFIX.'glossary WRITE';
	mysql_query($lock_sql, $db);

	$sql	  = 'SELECT MAX(word_id) FROM '.TABLE_PREFIX.'glossary';
	$result   = mysql_query($sql, $db);
	$next_index = mysql_fetch_row($result);
	$next_index = $next_index[0] + 1;

	/* $glossary_index_map[old_glossary_id] = new_glossary_id; */
	$glossary_index_map = array();

	$sql = '';
	$index_offset = '';
	$fp  = fopen($import_path.'glossary.csv','rb');
	while ($data = fgetcsv($fp, 10000000, ',')) {
		if ($sql == '') {
			/* first row stuff */
			$sql = 'INSERT INTO '.TABLE_PREFIX.'glossary VALUES ';
		}
		$sql .= '(';
		if (!isset($glossary_index_map[$data[0]])) {
			while (in_array($next_index, $glossary_index_map)) {
				$next_index++;
			}
			$glossary_index_map[$data[0]] = $next_index;
		}
	
		$sql .= $glossary_index_map[$data[0]] . ',';
		$sql .= $_SESSION['course_id'] .',';

		/* title */
		$data[1] = translate_whitespace($data[1]);
		$sql .= "'".addslashes($data[1])."',";

		/* definition */
		$data[2] = translate_whitespace($data[2]);
		$sql .= "'".addslashes($data[2])."',";

		/* related_word_id */
		if ($data[3]) {
			if (!isset($glossary_index_map[$data[3]])) {
				while (in_array($next_index, $glossary_index_map)) {
					$next_index++;
				}
				$glossary_index_map[$data[3]] = $next_index;
			}
			
			$sql .= $glossary_index_map[$data[3]];
		} else {
			$sql .= '0';
		}
		$next_index++;
		$sql .= '),';

	}

	if ($sql != '') {
		$sql = substr($sql, 0, -1);
		$result = mysql_query($sql, $db);
	}
	fclose($fp);

	$lock_sql = 'UNLOCK TABLES';
	$result   = mysql_query($lock_sql, $db);
	/****************************************************/

	/* resource_categories.csv */
	/* get the CatID offset:   */
	$lock_sql = 'LOCK TABLES '.TABLE_PREFIX.'resource_categories WRITE';
	$result   = mysql_query($lock_sql, $db);

	$sql = '';
	$link_cat_map = array();
	$fp  = fopen($import_path.'resource_categories.csv','rb');
	while ($data = fgetcsv($fp, 10000000, ',')) {
		$sql = 'INSERT INTO '.TABLE_PREFIX.'resource_categories VALUES ';
		$sql .= '(0,';
		$sql .= $_SESSION['course_id'] .',';

		/* CatName */
		$data[1] = translate_whitespace($data[1]);
		$sql .= "'".addslashes($data[1])."',";

		/* CatParent */
		if ($data[2] == 0) {
			$sql .= 'NULL';
		} else {
			$sql .= $data[2] + $index_offset;
		}
		$sql .= ')';

		$result = mysql_query($sql, $db);

		$link_cat_map[$data[0]] = mysql_insert_id($db);
	}
	fclose($fp);

	$lock_sql = 'UNLOCK TABLES';
	$result   = mysql_query($lock_sql, $db);
	/****************************************************/

	/* resource_links.csv */
	$sql = '';
	$fp  = fopen($import_path.'resource_links.csv','rb');
	while ($data = fgetcsv($fp, 10000000, ',')) {
		if ($sql == '') {
			/* first row stuff */
			$sql = 'INSERT INTO '.TABLE_PREFIX.'resource_links VALUES ';
		}
		$sql .= '(0, ';
		$sql .= $link_cat_map[$data[0]] . ',';

		/* URL */
		$data[1] = translate_whitespace($data[1]);
		$sql .= "'".addslashes($data[1])."',";

		/* LinkName */
		$data[2] = translate_whitespace($data[2]);
		$sql .= "'".addslashes($data[2])."',";

		/* Description */
		$data[3] = translate_whitespace($data[3]);
		$sql .= "'".addslashes($data[3])."',";

		/* Approved */
		$sql .= $data[4].',';

		/* SubmitName */
		$data[5] = translate_whitespace($data[5]);
		$sql .= "'".addslashes($data[5])."',";

		/* SubmitEmail */
		$data[6] = translate_whitespace($data[6]);
		$sql .= "'".addslashes($data[6])."',";

		/* SubmitDate */
		$data[7] = translate_whitespace($data[7]);
		$sql .= "'".addslashes($data[7])."',";

		$sql .= $data[8]. '),';
	}

	if ($sql != '') {
		$sql = substr($sql, 0, -1);
		$result = mysql_query($sql, $db);
	}
	fclose($fp);
	/****************************************************/

	/* news.csv */
	$sql = '';
	$fp  = fopen($import_path.'news.csv','rb');
	while ($data = fgetcsv($fp, 10000000, ',')) {
		if ($sql == '') {
			/* first row stuff */
			$sql = 'INSERT INTO '.TABLE_PREFIX.'news VALUES ';
		}
		$sql .= '(0,'.$_SESSION['course_id'].', '. $_SESSION['member_id'].', ';

		/* date */
		$data[0] = translate_whitespace($data[0]);
		$sql .= "'".addslashes($data[0])."',";

		$i=1;
		if ($_FILES['file']['type'] != 'application/x-gzip-compressed') {
			/* for versions 1.1+	*/
			/* formatting			*/
			$data[$i] = translate_whitespace($data[$i]);
			$sql .= $data[$i].',';
			$i++;
		} else {
			$sql .= '0,';
		}

		/* title */
		$data[$i] = translate_whitespace($data[$i]);
		$sql .= "'".addslashes($data[$i])."',";
		$i++;

		/* body */
		$data[$i] = translate_whitespace($data[$i]);
		$sql .= "'".addslashes($data[$i])."'";

		$sql .= '),';
	}

	if ($sql != '') {
		$sql = substr($sql, 0, -1);
		$result = mysql_query($sql, $db);
	}
	fclose($fp);
	/****************************************************/

	/* tests.csv */
	/* get the test_id offset:   */
	$lock_sql = 'LOCK TABLES '.TABLE_PREFIX.'tests WRITE';
	$result   = mysql_query($lock_sql, $db);

	$sql		= 'SELECT MAX(test_id) FROM '.TABLE_PREFIX.'tests';
	$result		= mysql_query($sql, $db);
	$next_index = mysql_fetch_row($result);
	$next_index = $next_index[0] + 1;

	$sql = '';
	$index_offset = '';
	$fp  = fopen($import_path.'tests.csv','rb');
	while ($data = fgetcsv($fp, 10000000, ',')) {
		if ($sql == '') {
			/* first row stuff */
			$index_offset = $next_index - $data[0];
			$sql = 'INSERT INTO '.TABLE_PREFIX.'tests VALUES ';
		}
		$sql .= '(';
		$sql .= ($data[0] + $index_offset) . ',';
		$sql .= $_SESSION['course_id'] .',';

		/* title */
		$data[1] = translate_whitespace($data[1]);
		$sql .= "'".addslashes($data[1])."',";

		/* format */
		$sql .= $data[2].',';

		/* start date */
		$data[3] = translate_whitespace($data[3]);
		$sql .= "'".addslashes($data[3])."',";
		
		/* end date */
		$data[4] = translate_whitespace($data[4]);
		$sql .= "'".addslashes($data[4])."',";

		/* randomize order */
		$sql .= $data[5].',';

		/* num_questions */
		$sql .= $data[6].',';

		/* instructions */
		$data[7] = translate_whitespace($data[7]);
		$sql .= "'".addslashes($data[7])."'";

		$sql .= '),';
	}
	if ($sql != '') {
		$sql	= substr($sql, 0, -1);
		$result = mysql_query($sql, $db);
	}
	fclose($fp);


	$lock_sql = 'UNLOCK TABLES';
	$result   = mysql_query($lock_sql, $db);
	/****************************************************/

	/* tests_questions.csv */

	$sql = '';
	$fp  = fopen($import_path.'tests_questions.csv','rb');
	while ($data = fgetcsv($fp, 10000000, ',')) {
		if ($sql == '') {
			/* first row stuff */
			$sql = 'INSERT INTO '.TABLE_PREFIX.'tests_questions VALUES ';
		}
		$sql .= '(0, ';
		$sql .= ($data[0] + $index_offset) . ','; // test_id
		$sql .= $_SESSION['course_id'] .',';

		/* ordering */
		$sql .= $data[1].',';

		/* type */
		$sql .= $data[2].',';

		/* weight */
		$sql .= $data[3].',';

		/* required */
		$sql .= $data[4].',';

		/* feedback */
		$data[5] = translate_whitespace($data[5]);
		$sql .= "'".addslashes($data[5])."',";

		/* question */
		$data[6] = translate_whitespace($data[6]);
		$sql .= "'".addslashes($data[6])."',";

		/* choice_0 */
		$data[7] = translate_whitespace($data[7]);
		$sql .= "'".addslashes($data[7])."',";

		/* choice_1 */
		$data[8] = translate_whitespace($data[8]);
		$sql .= "'".addslashes($data[8])."',";

		/* choice_2 */
		$data[9] = translate_whitespace($data[9]);
		$sql .= "'".addslashes($data[9])."',";

		/* choice_3 */
		$data[10] = translate_whitespace($data[10]);
		$sql .= "'".addslashes($data[10])."',";

		/* choice_4 */
		$data[11] = translate_whitespace($data[11]);
		$sql .= "'".addslashes($data[11])."',";

		/* choice_5 */
		$data[12] = translate_whitespace($data[12]);
		$sql .= "'".addslashes($data[12])."',";

		/* choice_6 */
		$data[13] = translate_whitespace($data[13]);
		$sql .= "'".addslashes($data[13])."',";

		/* choice_7 */
		$data[14] = translate_whitespace($data[14]);
		$sql .= "'".addslashes($data[14])."',";

		/* choice_8 */
		$data[15] = translate_whitespace($data[15]);
		$sql .= "'".addslashes($data[15])."',";

		/* choice_9 */
		$data[16] = translate_whitespace($data[16]);
		$sql .= "'".addslashes($data[16])."',";

		/* answer_0 */
		$sql .= $data[17].',';

		/* answer_1 */
		$sql .= $data[18].',';

		/* answer_2 */
		$sql .= $data[19].',';

		/* answer_3 */
		$sql .= $data[20].',';

		/* answer_4 */
		$sql .= $data[21].',';

		/* answer_5 */
		$sql .= $data[22].',';

		/* answer_6 */
		$sql .= $data[23].',';

		/* answer_7 */
		$sql .= $data[24].',';

		/* answer_8 */
		$sql .= $data[25].',';

		/* answer_9 */
		$sql .= $data[26].',';

		/* answer_size */
		$sql .= $data[27];

		$sql .= '),';
	}
	if ($sql != '') {
		$sql	= substr($sql, 0, -1);
		$result = mysql_query($sql, $db);
	}
	fclose($fp);

	$lock_sql = 'UNLOCK TABLES';
	$result   = mysql_query($lock_sql, $db);
	/****************************************************/

	clr_dir($import_path);

	header('Location: index.php?f='.AT_FEEDBACK_IMPORT_SUCCESS);
	exit;
	
?>