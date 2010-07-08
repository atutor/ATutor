<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: testQuestions.class.php 8983 2009-11-30 22:37:20Z hwong $
require(AT_INCLUDE_PATH.'../mods/_standard/tests/lib/test_question_queries.inc.php');

/*
 * Steps to follow when adding a new question type:
 *
 * 1 - Create a class extending AbstractQuestion or extend an 
 *     existing question class.
 *     Define $sPrefix and $sNameVar appropriately.
 *     Implement the following methods, which set template variables:
 *
 *        assignQTIVariables()
 *        assignDisplayResultVariables()
 *        assignDisplayVariables()
 *        assignDisplayStatisticsVariables()
 *		  importQti()
 *     
 *     And implement mark() which is used for marking the result.
 *
 * 2 - Add the new class name to $question_classes in test_question_factory()
 *
 * 3 - Add $sNameVar to the language database.
 *
 * 4 - Create the following files for creating and editing the question,
 *     where "{PREFIX}" is the value defined by $sPrefix:
 *
 *     /tools/tests/create_question_{PREFIX}.php
 *     /tools/tests/edit_question_{PREFIX}.php
 *
 * 5 - Add those two newly created pages to 
 *     /mods/_standard/tests/module.php
 *
 * 6 - Create the following template files:
 *
 *     /themes/default/test_questions/{PREFIX}.tmpl.php
 *     /themes/default/test_questions/{PREFIX}_qti_2p1.tmpl.php
 *     /themes/default/test_questions/{PREFIX}_result.tmpl.php
 *     /themes/default/test_questions/{PREFIX}_stats.tmpl.php
 *
 * 7 - Add the new question type to qti import/export tools,
 *     Implement the following methods, which set template variables:
 *
 *     include/classes/QTI/QTIParser.class.php
 *	   getQuestionType()
 *
 * 8 - Done!
 **/
class TestQuestions {
	// returns array of prefix => name, sorted!
	/*static */function getQuestionPrefixNames() {
		$question_prefix_names = array(); // prefix => name
		$questions = TestQuestions::getQuestionClasses();
		foreach ($questions as $type => $question) {
			$o = TestQuestions::getQuestion($type);
			$question_prefix_names[$o->getPrefix()] = $o->getName();
		}
		asort($question_prefix_names);
		return $question_prefix_names;
	}

	/*static */function getQuestionClasses() {
		/** NOTE: The indices are CONSTANTS. Do NOT change!! **/
		$question_classes = array(); // type ID => class name
		$question_classes[1] = 'MultichoiceQuestion';
		$question_classes[2] = 'TruefalseQuestion';
		$question_classes[3] = 'LongQuestion';
		$question_classes[4] = 'LikertQuestion';
		$question_classes[5] = 'MatchingQuestion';
		$question_classes[6] = 'OrderingQuestion';
		$question_classes[7] = 'MultianswerQuestion';
		$question_classes[8] = 'MatchingddQuestion';

		return $question_classes;
	}

	/**
	 * Used to create question objects based on $question_type.
	 * A singleton that creates one obj per question since
	 * questions are all stateless.
	 * Returns a reference to the question object.
	 */
	/*static */function & getQuestion($question_type) {
		static $objs, $question_classes;

		if (isset($objs[$question_type])) {
			return $objs[$question_type];
		}

		$question_classes = TestQuestions::getQuestionClasses();

		if (isset($question_classes[$question_type])) {
			global $savant;
			$objs[$question_type] = new $question_classes[$question_type]($savant);
		} else {
			return FALSE;
		}

		return $objs[$question_type];
	}
}

/** 
 * Export test questions
 * @param	array	an array consist of all the ids of the questions in which we desired to export.
 */
function test_question_qti_export_v2p1($question_ids) {
	require(AT_INCLUDE_PATH.'classes/zipfile.class.php'); // for zipfile
	require(AT_INCLUDE_PATH.'lib/html_resource_parser.inc.php'); // for get_html_resources()
	require(AT_INCLUDE_PATH.'classes/XML/XML_HTMLSax/XML_HTMLSax.php');	// for XML_HTMLSax

	global $savant, $db, $system_courses, $languageManager;

	$course_language = $system_courses[$_SESSION['course_id']]['primary_language'];
	$courseLanguage =& $languageManager->getLanguage($course_language);
	$course_language_charset = $courseLanguage->getCharacterSet();

	$zipfile = new zipfile();
	$zipfile->create_dir('resources/'); // for all the dependency files
	$resources    = array();
	$dependencies = array();

	asort($question_ids);

	$question_ids_delim = implode(',',$question_ids);

	$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND question_id IN($question_ids_delim)";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$obj = TestQuestions::getQuestion($row['type']);
		$xml = $obj->exportQTI($row, $course_language_charset, '2.1');
		$local_dependencies = array();

		$text_blob = implode(' ', $row);
		$local_dependencies = get_html_resources($text_blob);
		$dependencies = array_merge($dependencies, $local_dependencies);

		$resources[] = array('href'         => 'question_'.$row['question_id'].'.xml',
							 'dependencies' => array_keys($local_dependencies));

		//TODO
		$savant->assign('xml_content', $xml);
		$savant->assign('title', $row['question']);
		$xml = $savant->fetch('test_questions/wrapper.tmpl.php');

		$zipfile->add_file($xml, 'question_'.$row['question_id'].'.xml');
	}

	// add any dependency files:
	foreach ($dependencies as $resource => $resource_server_path) {
		$zipfile->add_file(@file_get_contents($resource_server_path), 'resources/' . $resource, filemtime($resource_server_path));
	}

	// construct the manifest xml
	$savant->assign('resources', $resources);
	$savant->assign('dependencies', array_keys($dependencies));
	$savant->assign('encoding', $course_language_charset);
	$manifest_xml = $savant->fetch('test_questions/manifest_qti_2p1.tmpl.php');

	$zipfile->add_file($manifest_xml, 'imsmanifest.xml');

	$zipfile->close();

	$filename = str_replace(array(' ', ':'), '_', $_SESSION['course_title'].'-'._AT('question_database').'-'.date('Ymd'));
	$zipfile->send_file($filename);
	exit;
}


/** 
 * Export test questions
 * @param	array	an array consist of all the ids of the questions in which we desired to export.
 */
function test_question_qti_export($question_ids) {
	require(AT_INCLUDE_PATH.'classes/zipfile.class.php'); // for zipfile
	require(AT_INCLUDE_PATH.'lib/html_resource_parser.inc.php'); // for get_html_resources()
	require(AT_INCLUDE_PATH.'classes/XML/XML_HTMLSax/XML_HTMLSax.php');	// for XML_HTMLSax

	global $savant, $db, $system_courses, $languageManager;

	$course_language = $system_courses[$_SESSION['course_id']]['primary_language'];
	$courseLanguage =& $languageManager->getLanguage($course_language);
	$course_language_charset = $courseLanguage->getCharacterSet();

	$zipfile = new zipfile();
	$zipfile->create_dir('resources/'); // for all the dependency files
	$resources    = array();
	$dependencies = array();

	asort($question_ids);

	$question_ids_delim = implode(',',$question_ids);

	$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND question_id IN($question_ids_delim)";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$obj = TestQuestions::getQuestion($row['type']);
		$local_xml = '';
		$local_xml = $obj->exportQTI($row, $course_language_charset, '1.2.1');
		$local_dependencies = array();

		$text_blob = implode(' ', $row);
		$local_dependencies = get_html_resources($text_blob);
		$dependencies = array_merge($dependencies, $local_dependencies);

//		$resources[] = array('href'         => 'question_'.$row['question_id'].'.xml',
//							 'dependencies' => array_keys($local_dependencies));

		$xml = $xml . "\n\n" . $local_xml;		
	}
	$xml = trim($xml);

	//TODO
	$savant->assign('xml_content', $xml);
	$savant->assign('title', $row['question']);
	$xml = $savant->fetch('test_questions/wrapper.tmpl.php');

	$xml_filename = 'atutor_questions.xml';
	$zipfile->add_file($xml, $xml_filename);

	// add any dependency files:
	foreach ($dependencies as $resource => $resource_server_path) {
		$zipfile->add_file(@file_get_contents($resource_server_path), 'resources/' . $resource, filemtime($resource_server_path));
	}

	// construct the manifest xml
//	$savant->assign('resources', $resources);
	$savant->assign('dependencies', array_keys($dependencies));
	$savant->assign('encoding', $course_language_charset);
	$savant->assign('xml_filename', $xml_filename);
//	$manifest_xml = $savant->fetch('test_questions/manifest_qti_2p1.tmpl.php');
	$manifest_xml = $savant->fetch('test_questions/manifest_qti_1p2.tmpl.php');

	$zipfile->add_file($manifest_xml, 'imsmanifest.xml');

	$zipfile->close();

	$filename = str_replace(array(' ', ':'), '_', $_SESSION['course_title'].'-'._AT('question_database').'-'.date('Ymd'));
	$zipfile->send_file($filename);
	exit;
}


/** 
 * Export test 
 * @param	int		test id
 * @param	string	the test title
 * @param	ref		[OPTIONAL] zip object reference
 * @param	array	[OPTIONAL] list of already added files.
 */
function test_qti_export($tid, $test_title='', $zipfile = null){
	require_once(AT_INCLUDE_PATH.'classes/zipfile.class.php'); // for zipfile
	require_once(AT_INCLUDE_PATH.'classes/XML/XML_HTMLSax/XML_HTMLSax.php');	// for XML_HTMLSax
	require_once(AT_INCLUDE_PATH.'lib/html_resource_parser.inc.php'); // for get_html_resources()
	global $savant, $db, $system_courses, $languageManager, $test_zipped_files, $test_files, $use_cc;
	global $course_id;

	$course_id = $_SESSION['course_id'];
	$course_title = $_SESSION['course_title'];
	$course_language = $system_courses[$_SESSION['course_id']]['primary_language'];
	
	if ($course_language == '') {  // when oauth export into Transformable
		$sql = "SELECT course_id, title, primary_language FROM ".TABLE_PREFIX."courses
		         WHERE course_id = (SELECT course_id FROM ".TABLE_PREFIX."tests
		                             WHERE test_id=".$tid.")";
		$result	= mysql_query($sql, $db);
		$course_row = mysql_fetch_assoc($result);
		
		$course_language = $course_row['primary_language'];
		$course_id = $course_row['course_id'];
		$course_title = $course_row['title'];
	}
	$courseLanguage =& $languageManager->getLanguage($course_language);
	$course_language_charset = $courseLanguage->getCharacterSet();
	$imported_files;
	$zipflag = false;

	if ($zipfile==null){
		$zipflag = true;
	}

	if ($test_zipped_files == null){
		$test_zipped_files = array();
	}

	if ($zipflag){
		$zipfile = new zipfile();
		$zipfile->create_dir('resources/'); // for all the dependency files
	}
	$resources    = array();
	$dependencies = array();

//	don't want to sort it, i want the same order out.
//	asort($question_ids);

	//TODO: Merge the following 2 sqls together.
	//Randomized or not, export all the questions that are associated with it.
	$sql	= "SELECT TQ.question_id, TQA.weight FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQ.course_id=$course_id AND TQA.test_id=$tid ORDER BY TQA.ordering, TQA.question_id";
	$result	= mysql_query($sql, $db);
	$question_ids = array();

	while (($question_row = mysql_fetch_assoc($result)) != false){
		$question_ids[] = $question_row['question_id'];
	}

	//No questions in the test
	if (sizeof($question_ids)==0){
		return;
	}

	$question_ids_delim = implode(',',$question_ids);	

	//$sql = "SELECT * FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$_SESSION[course_id] AND question_id IN($question_ids_delim)";
	$sql = "SELECT TQ.*, TQA.weight, TQA.test_id FROM ".TABLE_PREFIX."tests_questions TQ INNER JOIN ".TABLE_PREFIX."tests_questions_assoc TQA USING (question_id) WHERE TQA.test_id=$tid AND TQ.question_id IN($question_ids_delim) ORDER BY TQA.ordering, TQA.question_id";

	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$obj = TestQuestions::getQuestion($row['type']);
		$local_xml = '';
		$local_xml = $obj->exportQTI($row, $course_language_charset, '1.2.1');
		$local_dependencies = array();

		$text_blob = implode(' ', $row);
		$local_dependencies = get_html_resources($text_blob);
		$dependencies = array_merge($dependencies, $local_dependencies);

		$xml = $xml . "\n\n" . $local_xml;
	}

	//files that are found inside the test; used by print_organization(), to add all test files into QTI/ folder.
	$test_files = $dependencies;

	$resources[] = array('href'         => 'tests_'.$tid.'.xml',
						 'dependencies' => array_keys($dependencies));

	$xml = trim($xml);

	//get test title
	$sql = "SELECT title FROM ".TABLE_PREFIX."tests WHERE test_id = $tid";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_array($result);

	//TODO: wrap around xml now
	$savant->assign('xml_content', $xml);
	$savant->assign('title', htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'));
	$xml = $savant->fetch('test_questions/wrapper.tmpl.php');

	$xml_filename = 'tests_'.$tid.'.xml';
	if (!$use_cc){		
		$zipfile->add_file($xml, $xml_filename);
	} else {
		$zipfile->add_file($xml, 'QTI/'.$xml_filename);
	}

	// add any dependency files:
	if (!$use_cc){
		foreach ($dependencies as $resource => $resource_server_path) {
			//add this file in if it's not already in the zip package
			if (!in_array($resource_server_path, $test_zipped_files)){
				$zipfile->add_file(@file_get_contents($resource_server_path), 'resources/'.$resource, filemtime($resource_server_path));
				$test_zipped_files[] = $resource_server_path;
			}
		}
	}

	if ($zipflag){
		// construct the manifest xml
		$savant->assign('resources', $resources);
		$savant->assign('dependencies', array_keys($dependencies));
		$savant->assign('encoding', $course_language_charset);
		$savant->assign('title', $test_title);
		$savant->assign('xml_filename', $xml_filename);
		
		$manifest_xml = $savant->fetch('test_questions/manifest_qti_1p2.tmpl.php');
		$zipfile->add_file($manifest_xml, 'imsmanifest.xml');

		$zipfile->close();

		$filename = str_replace(array(' ', ':'), '_', $course_title.'-'.$test_title.'-'.date('Ymd'));
		$zipfile->send_file($filename);
		exit;
	}
	
	$return_array[$xml_filename] = array_keys($dependencies);
	return $return_array;
	//done
}


/* 
 * Recursively create folders
 * For the purpose of this webapp only.  All the paths are seperated by a /
 * And thus this function will loop through each directory and create them on the way
 * if it doesn't exist.
 * @author harris
 */
function recursive_mkdir($path, $mode = 0700) {
    $dirs = explode(DIRECTORY_SEPARATOR , $path);
    $count = count($dirs);
    $path = '';
    for ($i = 0; $i < $count; ++$i) {
        $path .= $dirs[$i].DIRECTORY_SEPARATOR;
		//If the directory has not been created, create it and return error on failure
        if (!is_dir($path) && !mkdir($path, $mode)) {
            return false;
        }
    }
    return true;
}


/**
* keeps count of the question number (when displaying the question)
* need this function because PHP 4 doesn't support static members
*/
function TestQuestionCounter($increment = FALSE) {
	static $count;

	if (!isset($count)) { 
		$count = 0;
	}
	if ($increment) {
		$count++;
	}

	return $count;
}


/**
 * testQuestion
 *
 * Note that all PHP 5 OO declarations and signatures are commented out to be
 * backwards compatible with PHP 4.
 *
 */
/*abstract */ class AbstractTestQuestion  {
	/**
	* Savant2 $savant - refrence to the savant obj
	*/
	/*protected */ var $savant;

	/**
	* Constructor method.  Initialises variables.
	*/
	function AbstractTestQuestion(&$savant) { $this->savant =& $savant; }

	/**
	* Public
	*/
	/*final public */function seed($salt) {
		/**
		* by controlling the seed before calling array_rand() we insure that
		* we can un-randomize the order for marking.
		* used with ordering type questions only.
		*/
		srand($salt + $_SESSION['member_id']);
	}

	/**
	* Public
	*/
	/*final public */function unseed() {
		// To fix http://www.atutor.ca/atutor/mantis/view.php?id=3167
		// Disturb the seed for ordering questions after mark to avoid the deterioration  
		// of the random distribution due to a repeated initialization of the same random seed
		list($usec, $sec) = explode(" ", microtime());
		srand((int)($usec*10));
	}

	/**
	* Public
	* Prints the name of this question
	*/
	/*final public */function printName() { echo $this->getName(); }

	/**
	* Public
	* Prints the name of this question
	*/
	/*final public */function getName() { return _AT($this->sNameVar); }

	/**
	* Public
	* Returns the prefix string (used for file names)
	*/
	/*final public */function getPrefix() { return $this->sPrefix; }

	/**
	* Display the current question (for taking or previewing a test/question)
	*/
	/*final public */function display($row, $response = '') {
		// print the generic question header
		$this->displayHeader($row['weight']);

		// print the question specific template
		$row['question'] = format_content($row['question'], 1, '');
		$this->assignDisplayVariables($row, $response);
		$this->savant->display('test_questions/' . $this->sPrefix . '.tmpl.php');
		
		// print the generic question footer
		$this->displayFooter();
	}

	/**
	* Display the result for the current question
	*/
	/*final public */function displayResult($row, $answer_row, $editable = FALSE) {
		// print the generic question header
		$this->displayHeader($row['weight'], $answer_row['score'], $editable ? $row['question_id'] : FALSE);

		// print the question specific template
		$this->assignDisplayResultVariables($row, $answer_row);
		$this->savant->display('test_questions/' . $this->sPrefix . '_result.tmpl.php');
		
		// print the generic question footer
		$this->displayFooter();
	}


	/**
	* print the question template header
	*/
	/*final public */function displayResultStatistics($row, $answers) {
		TestQuestionCounter(TRUE);
		$this->assignDisplayStatisticsVariables($row, $answers);
		$this->savant->display('test_questions/' . $this->sPrefix . '_stats.tmpl.php');
	}

	/*final public */function exportQTI($row, $encoding, $version) {
		$this->savant->assign('encoding', $encoding);
		//Convert all row values to html entities
		foreach ($row as $k=>$v){
			$row[$k] = htmlspecialchars($v, ENT_QUOTES, 'UTF-8');	//not using htmlentities cause it changes some languages falsely.
		}
		$this->assignQTIVariables($row);
		if ($version=='2.1') {
			$xml = $this->savant->fetch('test_questions/'. $this->sPrefix . '_qti_2p1.tmpl.php');
		} else {	
			$xml = $this->savant->fetch('test_questions/'. $this->sPrefix . '_qti_1p2.tmpl.php');
		}
		return $xml;
	}

	/**
	* print the question template header
	*/
	/*final private */function displayHeader($weight, $score = FALSE, $question_id = FALSE) {
		TestQuestionCounter(TRUE);
		
		if ($score) $score = intval($score);
		$this->savant->assign('question_id', $question_id);
		$this->savant->assign('score', $score);
		$this->savant->assign('weight', $weight);
		$this->savant->assign('type',   _AT($this->sNameVar));
		$this->savant->assign('number', TestQuestionCounter());
		$this->savant->display('test_questions/header.tmpl.php');
	}

	/**
	* print the question template footer
	*/
	/*final private */function displayFooter() {
		$this->savant->display('test_questions/footer.tmpl.php');
	}

	/**
	* return only the non-empty choices from $row.
	* assumes choices are sequential.
	*/
	/*protected */function getChoices($row) {
		$choices = array();
		for ($i=0; $i < 10; $i++) {
			if ($row['choice_'.$i] != '') {
				$num_choices++;
				$choices[] = $row['choice_'.$i];
			} else {
				break;
			}
		}
		return $choices;
	}
}

/**
* orderingQuestion
*
*/
class OrderingQuestion extends AbstractTestQuestion {
	/*protected */ var $sNameVar = 'test_ordering';
	/*protected */ var $sPrefix = 'ordering';
	
	/*protected */function assignDisplayResultVariables($row, $answer_row) {
		$answers = explode('|', $answer_row['answer']);

		$num_choices = count($this->getChoices($row));

		$this->savant->assign('base_href', AT_BASE_HREF);
		$this->savant->assign('num_choices', $num_choices);
		$this->savant->assign('answers', $answers);
		$this->savant->assign('row', $row);
	}

	/*protected */function assignQTIVariables($row) {
		$choices = $this->getChoices($row);
		$num_choices = count($choices);

		$this->savant->assign('num_choices', $num_choices);
		$this->savant->assign('row', $row);
	}

	/*protected */function assignDisplayVariables($row, $response) {
		// determine the number of choices this question has
		// and save those choices to be re-assigned back to $row
		// in the randomized order.
		$choices = $this->getChoices($row);
		$num_choices = count($choices);

		// response from the test_answers table is in the correct order
		// so, they have to be re-randomized in the same order as the
		// choices are. this is only possible because of the seed() method.
		$response = explode('|', $response);
		$new_response = array();

		// randomize the order of choices and re-assign to $row
		$this->seed($row['question_id']);
		$rand = array_rand($choices, $num_choices);
		for ($i=0; $i < 10; $i++) {
			$row['choice_'.$i] = $choices[$rand[$i]];
			$new_response[$i]  = $response[$rand[$i]];
		}

		$this->savant->assign('num_choices', $num_choices);
		$this->savant->assign('row', $row);

		$this->savant->assign('response', $new_response);
	}

	/*protected */function assignDisplayStatisticsVariables($row, $answers) {
		$num_results = 0;		
		foreach ($answers as $answer) {
			$num_results += $answer['count'];
		}

		$choices = $this->getChoices($row);
		$num_choices = count($choices);

		$final_answers = array(); // assoc array of # of times that key was used correctly 0, 1, ...  $num -1
		foreach ($answers as $key => $value) {
			$values = explode('|', $key);
			// we assume $values is never empty and contains $num number of answers
			for ($i=0; $i<=$num_choices; $i++) {
				if ($values[$i] == $i) {
					$final_answers[$i] += $answers[$key]['count'];
				}
			}
		}

		$this->savant->assign('num_results', $num_results);
		$this->savant->assign('num_choices', $num_choices);
		$this->savant->assign('answers', $final_answers);
		$this->savant->assign('row', $row);
	}

	/*public */function mark($row) { 
		$this->seed($row['question_id']);
		$num_choices = count($_POST['answers'][$row['question_id']]);
		$answers = range(0, $num_choices-1);
		$answers = array_rand($answers, $num_choices);
		
		// Disturb the seed for ordering questions after mark to avoid the deterioration  
		// of the random distribution due to a repeated initialization of the same random seed
		$this->unseed();

		$num_answer_correct = 0;

		$ordered_answers = array();

		for ($i = 0; $i < $num_choices ; $i++) {
			$_POST['answers'][$row['question_id']][$i] = intval($_POST['answers'][$row['question_id']][$i]);

			if ($_POST['answers'][$row['question_id']][$i] == -1) {
				// nothing to do. it was left blank
			} else if ($_POST['answers'][$row['question_id']][$i] == $answers[$i]) {
				$num_answer_correct++;
			}
			$ordered_answers[$answers[$i]] = $_POST['answers'][$row['question_id']][$i];
		}
		ksort($ordered_answers);

		$score = 0;

		// to avoid roundoff errors:
		if ($num_answer_correct == $num_choices) {
			$score = $row['weight'];
		} else if ($num_answer_correct > 0) {
			$score = number_format($row['weight'] / $num_choices * $num_answer_correct, 2);
			if ( (float) (int) $score == $score) {
				$score = (int) $score; // a whole number with decimals, eg. "2.00"
			} else {
				$score = trim($score, '0'); // remove trailing zeros, if any, eg. "2.50"
			}
		}

		$_POST['answers'][$row['question_id']] = implode('|', $ordered_answers);

		return $score;
	}

	//QTI Import Ordering Question
	function importQTI($_POST){
		global $msg, $db;

		if ($_POST['question'] == ''){
			$missing_fields[] = _AT('question');
		}

		if (trim($_POST['choice'][0]) == '') {
			$missing_fields[] = _AT('item').' 1';
		}
		if (trim($_POST['choice'][1]) == '') {
			$missing_fields[] = _AT('item').' 2';
		}

		if ($missing_fields) {
			$missing_fields = implode(', ', $missing_fields);
			$msg->addError(array('EMPTY_FIELDS', $missing_fields));
		}

		if (!$msg->containsErrors()) {
			$choice_new = array(); // stores the non-blank choices
			$answer_new = array(); // stores the non-blank answers
			$order = 0; // order count
			for ($i=0; $i<10; $i++) {
				/**
				 * Db defined it to be 255 length, chop strings off it it's less than that
				 * @harris
				 */
				$_POST['choice'][$i] = validate_length($_POST['choice'][$i], 255);
				$_POST['choice'][$i] = trim($_POST['choice'][$i]);

				if ($_POST['choice'][$i] != '') {
					/* filter out empty choices/ remove gaps */
					$choice_new[] = $_POST['choice'][$i];
					$answer_new[] = $order++;
				}
			}

			$_POST['choice']   = array_pad($choice_new, 10, '');
			$answer_new        = array_pad($answer_new, 10, 0);
//			$_POST['feedback'] = $addslashes($_POST['feedback']);
//			$_POST['question'] = $addslashes($_POST['question']);
		
			$sql_params = array(	$_POST['category_id'], 
									$_SESSION['course_id'],
									$_POST['feedback'], 
									$_POST['question'], 
									$_POST['choice'][0], 
									$_POST['choice'][1], 
									$_POST['choice'][2], 
									$_POST['choice'][3], 
									$_POST['choice'][4], 
									$_POST['choice'][5], 
									$_POST['choice'][6], 
									$_POST['choice'][7], 
									$_POST['choice'][8], 
									$_POST['choice'][9], 
									$answer_new[0], 
									$answer_new[1], 
									$answer_new[2], 
									$answer_new[3], 
									$answer_new[4], 
									$answer_new[5], 
									$answer_new[6], 
									$answer_new[7], 
									$answer_new[8], 
									$answer_new[9]);

			$sql = vsprintf(AT_SQL_QUESTION_ORDERING, $sql_params);

			$result	= mysql_query($sql, $db);
			if ($result==true){
				return mysql_insert_id();
			}			
		}
	}
}

/**
* truefalseQuestion
*
*/
class TruefalseQuestion extends AbstracttestQuestion {
	/*protected */ var $sPrefix = 'truefalse';
	/*protected */ var $sNameVar   = 'test_tf';

	/*protected */function assignQTIVariables($row) {
		$this->savant->assign('row', $row);
	}

	/*protected */function assignDisplayResultVariables($row, $answer_row) {

		$this->savant->assign('base_href', AT_BASE_HREF);
		$this->savant->assign('answers', $answer_row['answer']);
		$this->savant->assign('row', $row);
	}

	/*protected */function assignDisplayVariables($row, $response) {
		$this->savant->assign('row', $row);
		$this->savant->assign('response', $response);
	}

	/*protected */function assignDisplayStatisticsVariables($row, $answers) {
		$num_results = 0;		
		foreach ($answers as $answer) {
			$num_results += $answer['count'];
		}

		$this->savant->assign('num_results', $num_results);
		$this->savant->assign('num_blanks', (int) $answers['-1']['count']);
		$this->savant->assign('num_true', (int) $answers['1']['count']);
		$this->savant->assign('num_false', (int) $answers['2']['count']);
		$this->savant->assign('row', $row);
	}

	/*public */function mark($row) { 
		$_POST['answers'][$row['question_id']] = intval($_POST['answers'][$row['question_id']]);

		if ($row['answer_0'] == $_POST['answers'][$row['question_id']]) {
			return (int) $row['weight'];
		} // else:
		return 0;
	}

	//QTI Import True/False Question
	function importQTI($_POST){
		global $msg, $db;

		if ($_POST['question'] == ''){
			$msg->addError(array('EMPTY_FIELDS', _AT('statement')));
		}

		//assign true answer to 1, false answer to 2, idk to 3, for ATutor
		if  ($_POST['answer'] == 'ChoiceT'){
			$_POST['answer'] = 1;
		} else {
			$_POST['answer'] = 2;	
		}

		if (!$msg->containsErrors()) {
//			$_POST['feedback'] = $addslashes($_POST['feedback']);
//			$_POST['question'] = $addslashes($_POST['question']);


			$sql_params = array(	$_POST['category_id'], 
									$_SESSION['course_id'],
									$_POST['feedback'], 
									$_POST['question'], 
									$_POST['answer']);

			$sql = vsprintf(AT_SQL_QUESTION_TRUEFALSE, $sql_params);
			$result	= mysql_query($sql, $db);
			if ($result==true){
				return mysql_insert_id();
			}			
		}
	}
}

/**
* likertQuestion
*
*/
class LikertQuestion extends AbstracttestQuestion {
	/*protected */ var $sPrefix = 'likert';
	/*protected */ var $sNameVar   = 'test_lk';

	/*protected */function assignQTIVariables($row) {
		$choices = $this->getChoices($row);
		$num_choices = count($choices);

		$this->savant->assign('num_choices', $num_choices);
		$this->savant->assign('row', $row);
	}

	/*protected */function assignDisplayResultVariables($row, $answer_row) {
		$this->savant->assign('answer', $answer_row['answer']);
		$this->savant->assign('row', $row);
	}

	/*protected */function assignDisplayVariables($row, $response) {
		$choices = $this->getChoices($row);
		$num_choices = count($choices);

		$this->savant->assign('num_choices', $num_choices);
		$this->savant->assign('row', $row);

		if (empty($response)) {
			$response = -1;
		}
		$this->savant->assign('response', $response);
	}

	/*protected */function assignDisplayStatisticsVariables($row, $answers) {
		$num_results = 0;		
		foreach ($answers as $answer) {
			$num_results += $answer['count'];
		}
		
		$choices = $this->getChoices($row);
		$num_choices = count($choices);

		$sum = 0;
		for ($i=0; $i<$num_choices; $i++) {
			$sum += ($i+1) * $answers[$i]['count'];
		}
		$average = round($sum/$num_results, 1);

		$this->savant->assign('num_results', $num_results);
		$this->savant->assign('average', $average);
		$this->savant->assign('num_choices', $num_choices);
		$this->savant->assign('num_blanks', (int) $answers['-1']['count']);
		$this->savant->assign('answers', $answers);
		$this->savant->assign('row', $row);
	}

	/*public */function mark($row) { 
		$_POST['answers'][$row['question_id']] = intval($_POST['answers'][$row['question_id']]);
		return 0;
	}

	//QTI Import Likert Question
	function importQTI($_POST){
		global $msg, $db;
//		$_POST = $this->_POST; 

		$empty_fields = array();
		if ($_POST['question'] == ''){
			$empty_fields[] = _AT('question');
		}
		if ($_POST['choice'][0] == '') {
			$empty_fields[] = _AT('choice').' 1';
		}

		if ($_POST['choice'][1] == '') {
			$empty_fields[] = _AT('choice').' 2';
		}

		if (!empty($empty_fields)) {
//			$msg->addError(array('EMPTY_FIELDS', implode(', ', $empty_fields)));
		}

		if (!$msg->containsErrors()) {
			$_POST['feedback']   = '';
//			$_POST['question']   = $addslashes($_POST['question']);

			for ($i=0; $i<10; $i++) {
				$_POST['choice'][$i] = trim($_POST['choice'][$i]);
				$_POST['answer'][$i] = intval($_POST['answer'][$i]);

				if ($_POST['choice'][$i] == '') {
					/* an empty option can't be correct */
					$_POST['answer'][$i] = 0;
				}
			}

			$sql_params = array(	$_POST['category_id'], 
									$_SESSION['course_id'],
									$_POST['feedback'], 
									$_POST['question'], 
									$_POST['choice'][0], 
									$_POST['choice'][1], 
									$_POST['choice'][2], 
									$_POST['choice'][3], 
									$_POST['choice'][4], 
									$_POST['choice'][5], 
									$_POST['choice'][6], 
									$_POST['choice'][7], 
									$_POST['choice'][8], 
									$_POST['choice'][9], 
									$_POST['answer'][0], 
									$_POST['answer'][1], 
									$_POST['answer'][2], 
									$_POST['answer'][3], 
									$_POST['answer'][4], 
									$_POST['answer'][5], 
									$_POST['answer'][6], 
									$_POST['answer'][7], 
									$_POST['answer'][8], 
									$_POST['answer'][9]);

			$sql = vsprintf(AT_SQL_QUESTION_LIKERT, $sql_params);
			$result	= mysql_query($sql, $db);
			if ($result==true){
				return mysql_insert_id();
			}
		}
	}
}

/**
* longQuestion
*
*/
class LongQuestion extends AbstracttestQuestion {
	/*protected */ var $sPrefix = 'long';
	/*protected */ var $sNameVar = 'test_open';

	/*protected */function assignQTIVariables($row) {
		$this->savant->assign('row', $row);
	}

	/*protected */function assignDisplayResultVariables($row, $answer_row) {
		$this->savant->assign('answer', $answer_row['answer']);
		$this->savant->assign('row', $row);
	}

	/*protected */function assignDisplayVariables($row, $response) {
		$this->savant->assign('row', $row);
		$this->savant->assign('response', $response);
	}

	/*protected */function assignDisplayStatisticsVariables($row, $answers) {
		$num_results = 0;		
		foreach ($answers as $answer) {
			$num_results += $answer['count'];
		}
		
		$this->savant->assign('num_results', $num_results);
		$this->savant->assign('num_blanks', (int) $answers['']['count']);
		$this->savant->assign('answers', $answers);
		$this->savant->assign('row', $row);
	}

	/*public */function mark($row) { 
		global $addslashes;
		$_POST['answers'][$row['question_id']] = $addslashes($_POST['answers'][$row['question_id']]);
		return NULL;
	}

	//QTI Import Open end/long Question
	function importQTI($_POST){
		global $msg, $db;
//		$_POST = $this->_POST; 

		if ($_POST['question'] == ''){
//			$msg->addError(array('EMPTY_FIELDS', _AT('question')));
		}

		if (!$msg->containsErrors()) {
//			$_POST['feedback'] = $addslashes($_POST['feedback']);
//			$_POST['question'] = $addslashes($_POST['question']);

			if ($_POST['property']==''){
				$_POST['property'] = 4;	//essay
			}

			$sql_params = array(	$_POST['category_id'], 
									$_SESSION['course_id'],
									$_POST['feedback'], 
									$_POST['question'], 
									$_POST['property']);

			$sql = vsprintf(AT_SQL_QUESTION_LONG, $sql_params);

			$result	= mysql_query($sql, $db);
			if ($result==true){
				return mysql_insert_id();
			}
		}
	}
}

/**
* matchingQuestion
*
*/
class MatchingQuestion extends AbstracttestQuestion {
	/*protected */ var $sPrefix = 'matching';
	/*protected */ var $sNameVar   = 'test_matching';

	/*protected */function assignQTIVariables($row) {
		$choices = $this->getChoices($row);
		$num_choices = count($choices);

		$num_options = 0;
		for ($i=0; $i < 10; $i++) {
			if ($row['option_'. $i] != '') {
				$num_options++;
			}
		}

		$this->savant->assign('num_choices', $num_choices);
		$this->savant->assign('num_options', $num_options);
		$this->savant->assign('row', $row);
	}

	/*protected */function assignDisplayResultVariables($row, $answer_row) {
		$num_options = 0;
		for ($i=0; $i < 10; $i++) {
			if ($row['option_'. $i] != '') {
				$num_options++;
			}
		}

		$answer_row['answer'] = explode('|', $answer_row['answer']);

		global $_letters;

		$this->savant->assign('base_href', AT_BASE_HREF);
		$this->savant->assign('answers', $answer_row['answer']);
		$this->savant->assign('letters', $_letters);
		$this->savant->assign('num_options', $num_options);
		$this->savant->assign('row', $row);
	}

	/*protected */function assignDisplayVariables($row, $response) {
		$choices = $this->getChoices($row);
		$num_choices = count($choices);

		if (empty($response)) {
			$response = array_fill(0, $num_choices, -1);
		} else {
			$response = explode('|', $response);
		}

		$num_options = 0;
		for ($i=0; $i < 10; $i++) {
			if ($row['option_'. $i] != '') {
				$num_options++;
			}
		}

		global $_letters;

		$this->savant->assign('num_choices', $num_choices);
		$this->savant->assign('base_href', AT_BASE_HREF);
		$this->savant->assign('letters', $_letters);
		$this->savant->assign('num_options', $num_options);
		$this->savant->assign('row', $row);

		$this->savant->assign('response', $response);
	}

	/*protected */function assignDisplayStatisticsVariables($row, $answers) {
		$choices = $this->getChoices($row);
		$num_choices = count($choices);

		$num_results = 0;
		foreach ($answers as $answer) {
			$num_results += $answer['count'];
		}
					
		foreach ($answers as $key => $value) {
			$values = explode('|', $key);
			if (count($values) > 1) {
				for ($i=0; $i<count($values); $i++) {
					$answers[$values[$i]]['count']++;
				}
			}
		}

		$this->savant->assign('num_choices', $num_choices);
		$this->savant->assign('num_results', $num_results);
		$this->savant->assign('answers', $answers);
		$this->savant->assign('row', $row);
	}

	/*public */function mark($row) { 
		$num_choices = count($_POST['answers'][$row['question_id']]);
		$num_answer_correct = 0;
		foreach ($_POST['answers'][$row['question_id']] as $item_id => $response) {
			if ($row['answer_' . $item_id] == $response) {
				$num_answer_correct++;
			}
			$_POST['answers'][$row['question_id']][$item_id] = intval($_POST['answers'][$row['question_id']][$item_id]);
		}

		$score = 0;
		// to avoid roundoff errors:
		if ($num_answer_correct == $num_choices) {
			$score = $row['weight'];
		} else if ($num_answer_correct > 0) {
			$score = number_format($row['weight'] / $num_choices * $num_answer_correct, 2);
			if ( (float) (int) $score == $score) {
				$score = (int) $score; // a whole number with decimals, eg. "2.00"
			} else {
				$score = trim($score, '0'); // remove trailing zeros, if any
			}
		}

		$_POST['answers'][$row['question_id']] = implode('|', $_POST['answers'][$row['question_id']]);

		return $score;
	}

	//QTI Import Matching Question
	function importQTI($_POST){
		global $msg, $db;
//		$_POST = $this->_POST; 

		if (!is_array($_POST['answer'])){
			$temp = $_POST['answer'];
			$_POST['answer'] = array();
			$_POST['answer'][0] = $temp;
		}
		ksort($_POST['answer']);	//array_pad returns an array disregard of the array keys
		//default for matching is '-'
		$_POST['answer']= array_pad($_POST['answer'], 10, -1);

		for ($i = 0 ; $i < 10; $i++) {
			$_POST['groups'][$i]        = trim($_POST['groups'][$i]);
			$_POST['answer'][$i] = (int) $_POST['answer'][$i];
			$_POST['choice'][$i]          = trim($_POST['choice'][$i]);
		}

		if (!$_POST['groups'][0] 
			|| !$_POST['groups'][1] 
			|| !$_POST['choice'][0] 
			|| !$_POST['choice'][1]) {
//			$msg->addError('QUESTION_EMPTY');
		}

		if (!$msg->containsErrors()) {
//			$_POST['feedback']     = $addslashes($_POST['feedback']);
//			$_POST['instructions'] = $addslashes($_POST['instructions']);
		
			$sql_params = array(	$_POST['category_id'], 
									$_SESSION['course_id'],
									$_POST['feedback'], 
									$_POST['question'], 
									$_POST['groups'][0], 
									$_POST['groups'][1], 
									$_POST['groups'][2], 
									$_POST['groups'][3], 
									$_POST['groups'][4], 
									$_POST['groups'][5], 
									$_POST['groups'][6], 
									$_POST['groups'][7], 
									$_POST['groups'][8], 
									$_POST['groups'][9], 
									$_POST['answer'][0], 
									$_POST['answer'][1], 
									$_POST['answer'][2], 
									$_POST['answer'][3], 
									$_POST['answer'][4], 
									$_POST['answer'][5], 
									$_POST['answer'][6], 
									$_POST['answer'][7], 
									$_POST['answer'][8], 
									$_POST['answer'][9],
									$_POST['choice'][0], 
									$_POST['choice'][1], 
									$_POST['choice'][2], 
									$_POST['choice'][3], 
									$_POST['choice'][4], 
									$_POST['choice'][5], 
									$_POST['choice'][6], 
									$_POST['choice'][7], 
									$_POST['choice'][8], 
									$_POST['choice'][9]);

			$sql = vsprintf(AT_SQL_QUESTION_MATCHINGDD, $sql_params);

			$result	= mysql_query($sql, $db);
			if ($result==true){
				return mysql_insert_id();
			}
		}
	}
}

/**
* matchingddQuestion
*
*/
class MatchingddQuestion extends MatchingQuestion {
	/*protected */ var $sPrefix = 'matchingdd';
	/*protected */ var $sNameVar   = 'test_matchingdd';
}

/**
* multichoiceQuestion
*
*/
class MultichoiceQuestion extends AbstracttestQuestion {
	/*protected */ var $sPrefix = 'multichoice';
	/*protected */var $sNameVar = 'test_mc';

	/*protected */function assignQTIVariables($row) {
		$choices = $this->getChoices($row);
		$num_choices = count($choices);

		$this->savant->assign('num_choices', $num_choices);
		$this->savant->assign('row', $row);
	}

	/*protected */function assignDisplayResultVariables($row, $answer_row) {
		if (strpos($answer_row['answer'], '|') !== false) {
			$answer_row['answer'] = explode('|', $answer_row['answer']);
		} else {
			$answer_row['answer'] = array($answer_row['answer']);
		}

		$this->savant->assign('base_href', AT_BASE_HREF);
		$this->savant->assign('answers', $answer_row['answer']);
		$this->savant->assign('row', $row);
	}

	/*protected */function assignDisplayVariables($row, $response) {
		$choices = $this->getChoices($row);
		$num_choices = count($choices);

		if ($response == '') {
			$response = -1;
		}
		$response = explode('|', $response);
		$this->savant->assign('response', $response);

		$this->savant->assign('num_choices', $num_choices);
		$this->savant->assign('row', $row);
	}

	/*protected */function assignDisplayStatisticsVariables($row, $answers) {
		$choices = $this->getChoices($row);
		$num_choices = count($choices);

		$num_results = 0;
		foreach ($answers as $answer) {
			$num_results += $answer['count'];
		}
					
		foreach ($answers as $key => $value) {
			$values = explode('|', $key);
			if (count($values) > 1) {
				for ($i=0; $i<count($values); $i++) {
					$answers[$values[$i]]['count']++;
				}
			}
		}

		$this->savant->assign('num_choices', $num_choices);
		$this->savant->assign('num_results', $num_results);
		$this->savant->assign('num_blanks', (int) $answers['-1']['count']);
		$this->savant->assign('answers', $answers);
		$this->savant->assign('row', $row);
	}

	/*public */function mark($row) { 
		$score = 0;
		$_POST['answers'][$row['question_id']] = intval($_POST['answers'][$row['question_id']]);
		if ($row['answer_' . $_POST['answers'][$row['question_id']]]) {
			$score = $row['weight'];
		} else if ($_POST['answers'][$row['question_id']] == -1) {
			$has_answer = 0;
			for($i=0; $i<10; $i++) {
				$has_answer += $row['answer_'.$i];
			}
			if (!$has_answer && $row['weight']) {
				// If MC has no answer and user answered "leave blank"
				$score = $row['weight'];
			}
		}
		return $score;
	}

	//QTI Import Multiple Choice Question
	function importQTI($_POST){
		global $msg, $db;
//		$_POST = $this->_POST; 
		if ($_POST['question'] == ''){
			$msg->addError(array('EMPTY_FIELDS', _AT('question')));
		}
		
		if (!$msg->containsErrors()) {
//			$_POST['question']   = $addslashes($_POST['question']);

			for ($i=0; $i<10; $i++) {
				$_POST['choice'][$i] = trim($_POST['choice'][$i]);
			}

			$answers = array_fill(0, 10, 0);
			if (is_array($_POST['answer'])){
				$answers[0] = 1;	//default the first to be the right answer. TODO, use summation of points.
			} else {
				$answers[$_POST['answer']] = 1;
			}
		
			$sql_params = array(	$_POST['category_id'], 
									$_SESSION['course_id'],
									$_POST['feedback'], 
									$_POST['question'], 
									$_POST['choice'][0], 
									$_POST['choice'][1], 
									$_POST['choice'][2], 
									$_POST['choice'][3], 
									$_POST['choice'][4], 
									$_POST['choice'][5], 
									$_POST['choice'][6], 
									$_POST['choice'][7], 
									$_POST['choice'][8], 
									$_POST['choice'][9], 
									$answers[0], 
									$answers[1], 
									$answers[2], 
									$answers[3], 
									$answers[4], 
									$answers[5], 
									$answers[6], 
									$answers[7], 
									$answers[8], 
									$answers[9]);

			$sql = vsprintf(AT_SQL_QUESTION_MULTI, $sql_params);
			$result	= mysql_query($sql, $db);
			if ($result==true){
				return mysql_insert_id();
			}
		}
	}
}

/**
* multianswerQuestion
*
*/
class MultianswerQuestion extends MultichoiceQuestion {
	/*protected */ var $sPrefix  = 'multianswer';
	/*protected */ var $sNameVar = 'test_ma';

	/*public */function mark($row) { 
		$num_correct = array_sum(array_slice($row, 3));

		if (is_array($_POST['answers'][$row['question_id']]) && count($_POST['answers'][$row['question_id']]) > 1) {
			if (($i = array_search('-1', $_POST['answers'][$row['question_id']])) !== FALSE) {
				unset($_POST['answers'][$row['question_id']][$i]);
			}
			$num_answer_correct = 0;
			foreach ($_POST['answers'][$row['question_id']] as $item_id => $answer) {
				if ($row['answer_' . $answer]) {
					// correct answer
					$num_answer_correct++;
				} else {
					// wrong answer
					$num_answer_correct--;
				}
				$_POST['answers'][$row['question_id']][$item_id] = intval($_POST['answers'][$row['question_id']][$item_id]);
			}
			if ($num_answer_correct == $num_correct) {
				$score = $row['weight'];
			} else {
				$score = 0;
			}
			$_POST['answers'][$row['question_id']] = implode('|', $_POST['answers'][$row['question_id']]);
		} else {
			// no answer given
			$_POST['answers'][$row['question_id']] = '-1'; // left blank
			$score = 0;
		}
		return $score;
	}

	//QTI Import multianswer Question
	function importQTI($_POST){
		global $msg, $db;
//		$_POST = $this->_POST; 

		if ($_POST['question'] == ''){
			$msg->addError(array('EMPTY_FIELDS', _AT('question')));
		}

		//Multiple answer can have 0+ answers, in the QTIImport.class, if size(answer) < 2, answer will be came a scalar.
		//The following code will change $_POST[answer] back to a vector.
		$_POST['answer'] = $_POST['answers'];

		if (!$msg->containsErrors()) {
			$choice_new = array(); // stores the non-blank choices
			$answer_new = array(); // stores the associated "answer" for the choices

			foreach ($_POST['choice'] as $choiceNum=>$choiceOpt) {
				$choiceOpt = validate_length($choiceOpt, 255);
				$choiceOpt = trim($choiceOpt);
				$_POST['answer'][$choiceNum] = intval($_POST['answer'][$choiceNum]);
				if ($choiceOpt == '') {
					/* an empty option can't be correct */
					$_POST['answer'][$choiceNum] = 0;
				} else {
					/* filter out empty choices/ remove gaps */
					$choice_new[] = $choiceOpt;
					if (in_array($choiceNum, $_POST['answer'])){
						$answer_new[] = 1;
					} else {
						$answer_new[] = 0;
					}

					if ($_POST['answer'][$choiceNum] != 0)
						$has_answer = TRUE;
				}
			}

			if ($has_answer != TRUE) {
		
				$hidden_vars['required']    = htmlspecialchars($_POST['required']);
				$hidden_vars['feedback']    = htmlspecialchars($_POST['feedback']);
				$hidden_vars['question']    = htmlspecialchars($_POST['question']);
				$hidden_vars['category_id'] = htmlspecialchars($_POST['category_id']);

				for ($i = 0; $i < count($choice_new); $i++) {
					$hidden_vars['answer['.$i.']'] = htmlspecialchars($answer_new[$i]);
					$hidden_vars['choice['.$i.']'] = htmlspecialchars($choice_new[$i]);
				}

				$msg->addConfirm('NO_ANSWER', $hidden_vars);
			} else {			
				//add slahes throughout - does that fix it?
				$_POST['answer'] = $answer_new;
				$_POST['choice'] = $choice_new;
				$_POST['answer'] = array_pad($_POST['answer'], 10, 0);
				$_POST['choice'] = array_pad($_POST['choice'], 10, '');
			
//				$_POST['feedback'] = $addslashes($_POST['feedback']);
//				$_POST['question'] = $addslashes($_POST['question']);

				$sql_params = array(	$_POST['category_id'], 
										$_SESSION['course_id'],
										$_POST['feedback'], 
										$_POST['question'], 
										$_POST['choice'][0], 
										$_POST['choice'][1], 
										$_POST['choice'][2], 
										$_POST['choice'][3], 
										$_POST['choice'][4], 
										$_POST['choice'][5], 
										$_POST['choice'][6], 
										$_POST['choice'][7], 
										$_POST['choice'][8], 
										$_POST['choice'][9], 
										$_POST['answer'][0], 
										$_POST['answer'][1], 
										$_POST['answer'][2], 
										$_POST['answer'][3], 
										$_POST['answer'][4], 
										$_POST['answer'][5], 
										$_POST['answer'][6], 
										$_POST['answer'][7], 
										$_POST['answer'][8], 
										$_POST['answer'][9]);

				$sql = vsprintf(AT_SQL_QUESTION_MULTIANSWER, $sql_params);

				$result	= mysql_query($sql, $db);
				if ($result==true){
					return mysql_insert_id();
				}
			}
		}
	}

}
?>