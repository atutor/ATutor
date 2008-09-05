<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Harris Wong						*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: question_import.php 7482 2008-05-06 17:44:49Z harris $
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php'); /* for clr_dir() and preImportCallBack and dirsize() */
require(AT_INCLUDE_PATH.'lib/qti.inc.php'); 
require(AT_INCLUDE_PATH.'classes/pclzip.lib.php');
require(AT_INCLUDE_PATH.'classes/QTI/QTIParser.class.php');	
require(AT_INCLUDE_PATH.'classes/QTI/QTIImport.class.php');

/* to avoid timing out on large files */
@set_time_limit(0);
$_SESSION['done'] = 1;

$element_path = array();
$character_data = '';
$test_title = '';
$resource_num = 0;
$qids = array();	//store all the question ids that's being inserted into the db by this import
$overwrite = false;	//files will not be overwrite and prompt

/* handle get */
if (isset($_POST['submit_yes'])){
	$overwrite = true;
} elseif (isset($_POST['submit_no'])){
	$msg->addFeedback('IMPORT_CANCELLED');
	header('Location: index.php');
	exit;
}

/* functions */
/* called at the start of en element */
/* builds the $path array which is the path from the root to the current element */
function startElement($parser, $name, $attrs) {
	global $attributes, $element_path, $resource_num;
	//save attributes.
	switch($name) {
		case 'resource':
			$attributes[$name.$resource_num]['identifier'] = $attrs['identifier'];
			$attributes[$name.$resource_num]['href'] = $attrs['href'];
			$attributes[$name.$resource_num]['type'] = $attrs['type'];
			$resource_num++;
			break;
		case 'file':
			if(in_array('resource', $element_path)){
				$attributes['resource'.($resource_num-1)]['file'][] = $attrs['href'];
			}
			break;
		case 'dependency':
			if(in_array('resource', $element_path)){
				$attributes['resource'.($resource_num-1)]['dependency'][] = $attrs['identifierref'];
			}
			break;

	}
	array_push($element_path, $name);		
}

/* called when an element ends */
/* removed the current element from the $path */
function endElement($parser, $name) {
	global $element_path, $test_title, $character_data;
	switch($name) {
		case 'title':
			if (in_array('organization', $element_path)){
				$test_title = $character_data;
			}
	}
	$character_data = '';
	array_pop($element_path);
}

/* called when there is character data within elements */
/* constructs the $items array using the last entry in $path as the parent element */
function characterData($parser, $data){
	global $character_data;
	if (trim($data)!=''){
		$character_data .= preg_replace('/[\t\0\x0B]*/', '', $data);
	}
}

//If overwrite hasn't been set to true, then the file has not been exported and still in the cache.
//otherwise, the zip file is extracted but has not been deleted (due to the confirmation).
if (!$overwrite){
	if (!isset($_POST['submit_import'])) {
		/* just a catch all */
		
		$errors = array('FILE_MAX_SIZE', ini_get('post_max_size'));
		$msg->addError($errors);

		header('Location: ./index.php');
		exit;
	} 


	//Handles import
	/*
	if (isset($_POST['url']) && ($_POST['url'] != 'http://') ) {
		if ($content = @file_get_contents($_POST['url'])) {

			// save file to /content/
			$filename = substr(time(), -6). '.zip';
			$full_filename = AT_CONTENT_DIR . $filename;

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
	*/
	$ext = pathinfo($_FILES['file']['name']);
	$ext = $ext['extension'];

	if ($ext != 'zip') {
		$msg->addError('IMPORTDIR_IMS_NOTVALID');
	} else if ($_FILES['file']['error'] == 1) {
		$errors = array('FILE_MAX_SIZE', ini_get('upload_max_filesize'));
		$msg->addError($errors);
	} else if ( !$_FILES['file']['name'] || (!is_uploaded_file($_FILES['file']['tmp_name']) && !$_POST['url'])) {
		$msg->addError('FILE_NOT_SELECTED');
	} else if ($_FILES['file']['size'] == 0) {
		$msg->addError('IMPORTFILE_EMPTY');
	} 
}

if ($msg->containsErrors()) {
	if (isset($_GET['tile'])) {
		header('Location: '.$_base_path.'tools/tile/index.php');
	} else {
		header('Location: index.php');
	}
	exit;
}

/* check if ../content/import/ exists */
$import_path = AT_CONTENT_DIR . 'import/';
$content_path = AT_CONTENT_DIR;

if (!is_dir($import_path)) {
	if (!@mkdir($import_path, 0700)) {
		$msg->addError('IMPORTDIR_FAILED');
	}
}

$import_path .= $_SESSION['course_id'].'/';
if (!$overwrite){
	if (is_dir($import_path)) {
		clr_dir($import_path);
	}

	if (!@mkdir($import_path, 0700)) {
		$msg->addError('IMPORTDIR_FAILED');
	}

	/* extract the entire archive into AT_COURSE_CONTENT . import/$course using the call back function to filter out php files */
	error_reporting(0);
	$archive = new PclZip($_FILES['file']['tmp_name']);
	if ($archive->extract(	PCLZIP_OPT_PATH,	$import_path,
							PCLZIP_CB_PRE_EXTRACT,	'preImportCallBack') == 0) {
		$msg->addError('IMPORT_FAILED');
		echo 'Error : '.$archive->errorInfo(true);
		clr_dir($import_path);
		header('Location: questin_db.php');
		exit;
	}
	error_reporting(AT_ERROR_REPORTING);
}
/* get the course's max_quota */
$sql	= "SELECT max_quota FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
$result = mysql_query($sql, $db);
$q_row	= mysql_fetch_assoc($result);

if ($q_row['max_quota'] != AT_COURSESIZE_UNLIMITED) {

	if ($q_row['max_quota'] == AT_COURSESIZE_DEFAULT) {
		$q_row['max_quota'] = $MaxCourseSize;
	}
	$totalBytes   = dirsize($import_path);
	$course_total = dirsize(AT_CONTENT_DIR . $_SESSION['course_id'].'/');
	$total_after  = $q_row['max_quota'] - $course_total - $totalBytes + $MaxCourseFloat;

	if ($total_after < 0) {
		/* remove the content dir, since there's no space for it */
		$errors = array('NO_CONTENT_SPACE', number_format(-1*($total_after/AT_KBYTE_SIZE), 2 ) );
		$msg->addError($errors);
		
		clr_dir($import_path);

		if (isset($_GET['tile'])) {
			header('Location: '.$_base_path.'tools/tile/index.php');
		} else {
			header('Location: index.php');
		}
		exit;
	}
}

$ims_manifest_xml = @file_get_contents($import_path.'imsmanifest.xml');

if ($ims_manifest_xml === false) {
	$msg->addError('NO_IMSMANIFEST');

	if (file_exists($import_path . 'atutor_backup_version')) {
		$msg->addError('NO_IMS_BACKUP');
	}

	clr_dir($import_path);

	if (isset($_GET['tile'])) {
		header('Location: '.$_base_path.'tools/tile/index.php');
	} else {
		header('Location: index.php');
	}
	exit;
}

$xml_parser = xml_parser_create();

xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
xml_set_element_handler($xml_parser, 'startElement', 'endElement');
xml_set_character_data_handler($xml_parser, 'characterData');

if (!xml_parse($xml_parser, $ims_manifest_xml, true)) {
	die(sprintf("XML error: %s at line %d",
				xml_error_string(xml_get_error_code($xml_parser)),
				xml_get_current_line_number($xml_parser)));
}

xml_parser_free($xml_parser);

//assign folder names
//if (!$package_base_name){
//	$package_base_name = substr($_FILES['file']['name'], 0, -4);
//}

//$package_base_name = strtolower($package_base_name);
//$package_base_name = str_replace(array('\'', '"', ' ', '|', '\\', '/', '<', '>', ':'), '_' , $package_base_name);
//$package_base_name = preg_replace("/[^A-Za-z0-9._\-]/", '', $package_base_name);

//if (is_dir(AT_CONTENT_DIR . $_SESSION['course_id'].'/'.$package_base_name)) {
//	echo 'Already exist: Quitting.  (Need better msg here)';
//	exit;
//	$package_base_name .= '_'.date('ymdHis');
//}

if ($package_base_path) {
	$package_base_path = implode('/', $package_base_path);
}

//debug($attributes);
//Dependency handling
//$media_items = array();
$xml_items = array();
//foreach($attributes as $resource=>$attrs){
//	if ($attrs['type'] != 'webcontent'){
//		$media_items[$attrs['identifier']] = $attrs['file'];
//	}
//}

//Check if the files exist, if so, warn the user.
$existing_files = isQTIFileExist($attributes);
//debug($existing_files);
if (!$overwrite && !empty($existing_files)){
	$existing_files = implode('<br/>', $existing_files);
	require(AT_INCLUDE_PATH.'header.inc.php');
//	$msg->addConfirm(array('MEDIA_FILE_EXISTED', $existing_files));
//	$msg->printConfirm();
	echo '<form action="" method="POST">';
	echo '<div class="input-form">';
	echo '<div class="row">';
	$msg->printInfos(array('MEDIA_FILE_EXISTED', $existing_files));
	echo '</div>';
	echo '<div class="row buttons">';
	echo '<input type="submit" class="" name="submit_yes" value="'._AT('yes').'"/>';
	echo '<input type="submit" class="" name="submit_no" value="'._AT('no').'"/>';
	echo '<input type="hidden" name="submit_import" value="submit_import" />';
	ECHO '<input type="hidden" name="url" value="'.$_POST['url'].'" />';
	echo '</div></div>';
	echo '</form>';
	require (AT_INCLUDE_PATH.'footer.inc.php');

	exit;
}

//Get the XML file out and start importing them into our database.
//TODO: See question_import.php 287-289.
foreach($attributes as $resource=>$attrs){
	if ($attrs['type'] != 'webcontent'){
		//Instantiate class obj
		$xml =& new QTIParser();
		$xml_content = @file_get_contents($import_path . $attrs['href']);		
		$xml->setRelativePath($package_base_name);
		if (!$xml->parse($xml_content)){
			$msg->addError('QTI_WRONG_PACKAGE');
			break;
		}

		//import file, should we use file href? or jsut this href?
		//Aug 25, use both, so then it can check for respondus media as well.
		foreach($attrs['file'] as $file_id => $file_name){
			$file_pathinfo = pathinfo($file_name);
			if ($file_pathinfo['basename'] == $attrs['href']){
				//This file will be parsed later
				continue;
			} 
//debug($file_pathinfo);
			if (in_array($file_pathinfo['extension'], $supported_media_type)){
				//copy medias over.
				copyMedia(array($file_name), $xml_items);
			}
		}		

		for ($loopcounter=0; $loopcounter<$xml->item_num; $loopcounter++){
			//Create POST values.
			unset($_POST);		//clear cache
			$_POST['required']		= 1;
			$_POST['preset_num']	= 0;
			$_POST['category_id']	= 0;
			$_POST['question']		= $xml->question[$loopcounter];
			$_POST['feedback']		= $xml->feedback[$loopcounter];
			$_POST['groups']		= $xml->groups[$loopcounter];
			$_POST['property']		= $xml->attributes[$loopcounter]['render_fib']['property'];
			$_POST['choice']		= array();
			$_POST['answers']		= array();

			//assign choices
			$i = 0;

			//trim values
			if (is_array($xml->answers[$loopcounter])){
				array_walk($xml->answers[$loopcounter], 'trim_value');
			}
			//TODO: The groups is 1-0+ choices.  So we should loop thru groups, not choices.
			if (is_array($xml->choices[$loopcounter])){		
				foreach ($xml->choices[$loopcounter] as $choiceNum=>$choiceOpt){
					if (sizeof($_POST['groups'] )>0) {
						foreach ($xml->answers[$loopcounter] as $ansNum=>$ansOpt){
							if ($choiceNum == $ansOpt){
								//Not exactly efficient, worst case N^2
								$_POST['answers'][$ansNum] = $i;
							}			
						}		
					} else {
						//save answer(s)
						if (is_array($xml->answers[$loopcounter]) && in_array($choiceNum, $xml->answers[$loopcounter])){
							$_POST['answers'][] = $i;
						}		
					}
					$_POST['choice'][] = $choiceOpt;
					$i++;
				}
			}
			unset($qti_import);
			$qti_import =& new QTIImport($_POST);
				
			//Create questions
			$qti_import->importQuestionType($xml->getQuestionType($loopcounter));			

			//save question id 
			$qids[] = $qti_import->qid;

			//Dependency handling
			if (!empty($attrs['dependency'])){
				$xml_items = array_merge($xml_items, $xml->items);
			}
		}
		$xml->close();
	} else {
		//webcontent, copy it over.
		copyMedia($attrs['file'], $xml_items);
/*
		foreach($attrs['file'] as $file_num => $file_loc){
			$new_file_loc ='';
			foreach ($xml_items as $xk=>$xv){
				if (strpos($file_loc, $xv)!==false){
					$new_file_loc = $xv;
					break;
				} 
			}
			if ($new_file_loc==''){
				$new_file_loc = $file_loc;
			}
debug($new_file_loc, 'NEW FILE LOC');
			//check if new folder is there, if not, create it.
//			createDir(AT_CONTENT_DIR .$_SESSION['course_id'].'/'.$package_base_name.'/'.$new_file_loc );
			createDir(AT_CONTENT_DIR .$_SESSION['course_id'].'/'.$new_file_loc );
			
			//copy files over
//			if (rename(AT_CONTENT_DIR . 'import/'.$_SESSION['course_id'].'/'.$file_loc, 
//				AT_CONTENT_DIR .$_SESSION['course_id'].'/'.$package_base_name.'/'.$new_file_loc) === false) {
			if (rename(AT_CONTENT_DIR . 'import/'.$_SESSION['course_id'].'/'.$file_loc, 
				AT_CONTENT_DIR .$_SESSION['course_id'].'/'.$new_file_loc) === false) {
				//TODO: Print out file already exist error.
				if (!$msg->containsErrors()) {
					$msg->addError('IMPORT_FAILED');
				}
			} 
		}
		*/
	}
}

//debug('done creating questions');
clr_dir(AT_CONTENT_DIR . 'import/'.$_SESSION['course_id']);
//debug($qids);
//debug('creating test template');
//------------------------------------------------------------------------ test ---
$missing_fields			= array();
$_POST['title']			= $test_title;
$_POST['description']	= '';
$_POST['num_questions']	= intval($_POST['num_questions']);
$_POST['num_takes']		= intval($_POST['num_takes']);
$_POST['content_id']	= intval($_POST['content_id']);
$_POST['passpercent']	= intval($_POST['passpercent']);
$_POST['passscore']		= intval($_POST['passscore']);
$_POST['passfeedback']  = $addslashes(trim($_POST['passfeedback']));
$_POST['failfeedback']  = $addslashes(trim($_POST['failfeedback']));
$_POST['num_takes']		= intval($_POST['num_takes']);
$_POST['anonymous']		= intval($_POST['anonymous']);
$_POST['allow_guests']	= $_POST['allow_guests'] ? 1 : 0;
$_POST['instructions']	= $addslashes($_POST['instructions']);
$_POST['display']		= intval($_POST['display']);
$_POST['result_release']= 0;
$_POST['random']		= 0;

// currently these options are ignored for tests:
$_POST['format']       = intval($_POST['format']);
$_POST['order']	       = 1;  //intval($_POST['order']);
$_POST['difficulty']   = 0;  //intval($_POST['difficulty']); 	/* avman */
	
//Title of the test is empty, could be from question database export or some other system's export.
//Either prompt for a title, or generate a random title
if ($_POST['title'] == '') {
	$_POST['title'] = '[' . _AT('tests') . ' ' . _AT('title') . ']';
	
	//set marks to 0 if no title? 
	$xml->weights = array();
}

/*
if ($_POST['random'] && !$_POST['num_questions']) {
	$missing_fields[] = _AT('num_questions_per_test');
}

if ($_POST['pass_score']==1 && !$_POST['passpercent']) {
	$missing_fields[] = _AT('percentage_score');
}

if ($_POST['pass_score']==2 && !$_POST['passscore']) {
	$missing_fields[] = _AT('points_score');
}

if ($missing_fields) {
	$missing_fields = implode(', ', $missing_fields);
	$msg->addError(array('EMPTY_FIELDS', $missing_fields));
}
*/

$day_start	= intval(date('j'));
$month_start= intval(date('n'));
$year_start	= intval(date('Y'));
$hour_start	= intval(date('G'));
$min_start	= intval(date('i'));

$day_end	= $day_start;
$month_end	= $month_start;
$year_end	= $year_start + 1;
$hour_end	= $hour_start;
$min_end	= $min_start;

if (!checkdate($month_start, $day_start, $year_start)) {
	$msg->addError('START_DATE_INVALID');
}

if (!checkdate($month_end, $day_end, $year_end)) {
	$msg->addError('END_DATE_INVALID');
}

if (mktime($hour_end,   $min_end,   0, $month_end,   $day_end,   $year_end) < 
	mktime($hour_start, $min_start, 0, $month_start, $day_start, $year_start)) {
		$msg->addError('END_DATE_INVALID');
}

if (!$msg->containsErrors()) {
	if (strlen($month_start) == 1){
		$month_start = "0$month_start";
	}
	if (strlen($day_start) == 1){
		$day_start = "0$day_start";
	}
	if (strlen($hour_start) == 1){
		$hour_start = "0$hour_start";
	}
	if (strlen($min_start) == 1){
		$min_start = "0$min_start";
	}

	if (strlen($month_end) == 1){
		$month_end = "0$month_end";
	}
	if (strlen($day_end) == 1){
		$day_end = "0$day_end";
	}
	if (strlen($hour_end) == 1){
		$hour_end = "0$hour_end";
	}
	if (strlen($min_end) == 1){
		$min_end = "0$min_end";
	}

	$start_date = "$year_start-$month_start-$day_start $hour_start:$min_start:00";
	$end_date	= "$year_end-$month_end-$day_end $hour_end:$min_end:00";

	//If title exceeded database defined length, truncate it.
	$_POST['title'] = validate_length($_POST['title'], 100);

	$sql = "INSERT INTO ".TABLE_PREFIX."tests " .
		   "(test_id,
		 course_id,
		 title,
		 description,
		 format,
		 start_date,
		 end_date,
		 randomize_order,
		 num_questions,
		 instructions,
		 content_id,
		 passscore,
		 passpercent,
		 passfeedback,
		 failfeedback,
		 result_release,
		 random,
		 difficulty,
		 num_takes,
		 anonymous,
		 out_of,
		 guests,
		 display)" .
		   "VALUES 
			(NULL, 
			 $_SESSION[course_id], 
			 '$_POST[title]', 
			 '$_POST[description]', 
			 $_POST[format], 
			 '$start_date', 
			 '$end_date', 
			 $_POST[order], 
			 $_POST[num_questions], 
			 '$_POST[instructions]', 
			 $_POST[content_id], 
			 $_POST[passscore], 
			 $_POST[passpercent], 
			 '$_POST[passfeedback]', 
			 '$_POST[failfeedback]', 
			 $_POST[result_release], 
			 $_POST[random], 
			 $_POST[difficulty], 
			 $_POST[num_takes], 
			 $_POST[anonymous], 
			 '', 
			 $_POST[allow_guests], 
			 $_POST[display])";

	$result = mysql_query($sql, $db);
	$tid = mysql_insert_id($db);

//debug($xml->weights, 'weights');

	//associate question and tests
	foreach ($qids as $order=>$qid){
		if (isset($xml->weights[$order])){
			$weight = round($xml->weights[$order]);
		} else {
			$weight = 0;
		}
		$new_order = $order + 1;
		$sql = "INSERT INTO " . TABLE_PREFIX . "tests_questions_assoc" . 
				"(test_id, question_id, weight, ordering, required) " .
				"VALUES ($tid, $qid, $weight, $new_order, 0)";
		$result = mysql_query($sql, $db);
	}
}
//debug('imported test');
if (!$msg->containsErrors()) {
	$msg->addFeedback('IMPORT_SUCCEEDED');
}
header('Location: index.php');
exit;
?>