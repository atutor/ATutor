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

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/zipfile.class.php'); /* for zipfile */

if (!isset($_POST['submit'])) {
	header('Location: ../index.php?f='.AT_FEEDBACK_EXPORT_CANCELLED);
	exit;
}

@set_time_limit(0);

	if (!authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
		require (AT_INCLUDE_PATH.'header.inc.php'); 
		$errors[] = AT_ERROR_NOT_OWNER;
		print_errors($errors);
		require (AT_INCLUDE_PATH.'footer.inc.php'); 
		exit;
	}

function quote_csv($line) {
	$line = str_replace('"', '""', $line);

	$line = str_replace("\n", '\n', $line);
	$line = str_replace("\r", '\r', $line);
	$line = str_replace("\x00", '\0', $line);

	return '"'.$line.'"';
}

function save_csv($name, $sql, $fields) {
	global $db, $zipfile;

	$content = '';
	$num_fields = count($fields);

	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		for ($i=0; $i< $num_fields; $i++) {
			if ($fields[$i][1] == NUMBER) {
				$content .= $row[$fields[$i][0]] . ',';
			} else {
				$content .= quote_csv($row[$fields[$i][0]]) . ',';
			}
		}
		$content = substr($content, 0, strlen($content)-1);
		$content .= "\n";
	}
	@mysql_free_result($result); 

	$zipfile -> add_file($content, $name.'.csv', time());
}

	$backup_course_title = str_replace(' ',  '_', $_SESSION['course_title']);
	$backup_course_title = str_replace('%',  '',  $backup_course_title);
	$backup_course_title = str_replace('\'', '',  $backup_course_title);
	$backup_course_title = str_replace('"',  '',  $backup_course_title);
	$backup_course_title = str_replace('`',  '',  $backup_course_title);

	$zipfile = new zipfile();
	if (is_dir(AT_CONTENT_DIR . $_SESSION['course_id'])) {
		$zipfile->add_dir(AT_CONTENT_DIR . $_SESSION['course_id'].'/', 'content/');
	}

	$package_identifier = VERSION."\n\n\n".'Do not change the first line of this file it contains the ATutor version this backup was created with.';
	$zipfile -> add_file($package_identifier, 'atutor_backup_version', time());

	define('NUMBER',	1);
	define('TEXT',		2);

	/* content.csv */
	$sql	= 'SELECT * FROM '.TABLE_PREFIX.'content WHERE course_id='.$_SESSION['course_id'].' ORDER BY content_parent_id, ordering';

	$fields = array();
	$fields[0] = array('content_id',		NUMBER);
	$fields[1] = array('content_parent_id', NUMBER);
	$fields[2] = array('ordering',			NUMBER);
	$fields[3] = array('last_modified',		TEXT);
	$fields[4] = array('revision',			NUMBER);
	$fields[5] = array('formatting',		NUMBER);
	$fields[6] = array('release_date',		TEXT);
	$fields[7] = array('keywords',			TEXT);
	$fields[8] = array('content_path',		TEXT);
	$fields[9] = array('title',				TEXT);
	$fields[10] = array('text',				TEXT);
	//$fields[11] = array('inherit_release_date',	NUMBER);

	save_csv('content', $sql, $fields);
	/****************************************************/

	/* forums.csv */
	$sql	= 'SELECT * FROM '.TABLE_PREFIX.'forums WHERE course_id='.$_SESSION['course_id'].' ORDER BY forum_id ASC';
	$fields = array();
	$fields[] = array('title',			TEXT);
	$fields[] = array('description',	TEXT);

	/* three fields added for v1.4 */
	$fields[] = array('num_topics',		NUMBER);
	$fields[] = array('num_posts',		NUMBER);
	$fields[] = array('last_post',		NUMBER);

	save_csv('forums', $sql, $fields);
	/****************************************************/

	/* related_content.csv */
	$sql	= 'SELECT R.content_id, R.related_content_id FROM '.TABLE_PREFIX.'related_content R, '.TABLE_PREFIX.'content C WHERE C.course_id='.$_SESSION['course_id'].' AND R.content_id=C.content_id ORDER BY R.content_id ASC';
	$fields = array();
	$fields[0] = array('content_id',			NUMBER);
	$fields[1] = array('related_content_id',	NUMBER);

	save_csv('related_content', $sql, $fields);
	/****************************************************/

	/* glossary.csv */
	$sql	= 'SELECT * FROM '.TABLE_PREFIX.'glossary WHERE course_id='.$_SESSION['course_id'].' ORDER BY word_id ASC';
	$fields = array();
	$fields[0] = array('word_id',			NUMBER);
	$fields[1] = array('word',				TEXT);
	$fields[2] = array('definition',		TEXT);
	$fields[3] = array('related_word_id',	NUMBER);

	save_csv('glossary', $sql, $fields);
	/****************************************************/

	/* resource_categories.csv */
	$sql	= 'SELECT * FROM '.TABLE_PREFIX.'resource_categories WHERE course_id='.$_SESSION['course_id'].' ORDER BY CatID ASC';
	$fields = array();
	$fields[0] = array('CatID',		NUMBER);
	$fields[1] = array('CatName',	TEXT);
	$fields[2] = array('CatParent', NUMBER);

	save_csv('resource_categories', $sql, $fields);
	/****************************************************/

	/* resource_links.csv */
	$sql	= 'SELECT L.* FROM '.TABLE_PREFIX.'resource_links L, '.TABLE_PREFIX.'resource_categories C WHERE C.course_id='.$_SESSION['course_id'].' AND L.CatID=C.CatID ORDER BY LinkID ASC';
	$fields = array();
	$fields[0] = array('CatID',			NUMBER);
	$fields[1] = array('Url',			TEXT);
	$fields[2] = array('LinkName',		TEXT);
	$fields[3] = array('Description',	TEXT);
	$fields[4] = array('Approved',		NUMBER);
	$fields[5] = array('SubmitName',	TEXT);
	$fields[6] = array('SubmitEmail',	TEXT);
	$fields[7] = array('SubmitDate',	TEXT);
	$fields[8] = array('hits',			NUMBER);

	save_csv('resource_links', $sql, $fields);
	/****************************************************/

	/* news.csv */
	$sql	= 'SELECT * FROM '.TABLE_PREFIX.'news WHERE course_id='.$_SESSION['course_id'].' ORDER BY news_id ASC';
	$fields = array();
	$fields[0] = array('date',		TEXT);
	$fields[1] = array('formatting',NUMBER);
	$fields[2] = array('title',		TEXT);
	$fields[3] = array('body',		TEXT);
	
	save_csv('news', $sql, $fields);
	/****************************************************/

	/* tests.csv */
	$sql	= 'SELECT * FROM '.TABLE_PREFIX.'tests WHERE course_id='.$_SESSION['course_id'].' ORDER BY test_id ASC';
	$fields = array();
	$fields[] = array('test_id',			NUMBER);
	$fields[] = array('title',				TEXT);
	$fields[] = array('format',				NUMBER);
	$fields[] = array('start_date',			TEXT);
	$fields[] = array('end_date',			TEXT);
	$fields[] = array('randomize_order',	NUMBER);
	$fields[] = array('num_questions',		NUMBER);
	$fields[] = array('instructions',		TEXT);

	/* four fields added for v1.4 */
	$fields[] = array('content_id',		NUMBER);
	$fields[] = array('automark',		NUMBER);
	$fields[] = array('random',			NUMBER);
	$fields[] = array('difficulty',		NUMBER);

	save_csv('tests', $sql, $fields);
	/****************************************************/

	/* tests_questions.csv */
	$sql	= 'SELECT * FROM '.TABLE_PREFIX.'tests_questions WHERE course_id='.$_SESSION['course_id'].' ORDER BY test_id ASC';
	$fields = array();
	//$fields[0] = array('question_id',		NUMBER);
	$fields[] = array('test_id',			NUMBER);
	$fields[] = array('ordering',			NUMBER);
	$fields[] = array('type',				NUMBER);
	$fields[] = array('weight',				NUMBER);
	$fields[] = array('required',			NUMBER);
	$fields[] = array('feedback',			TEXT);
	$fields[] = array('question',			TEXT);
	$fields[] = array('choice_0',			TEXT);
	$fields[] = array('choice_1',			TEXT);
	$fields[] = array('choice_2',			TEXT);
	$fields[] = array('choice_3',			TEXT);
	$fields[] = array('choice_4',			TEXT);
	$fields[] = array('choice_5',			TEXT);
	$fields[] = array('choice_6',			TEXT);
	$fields[] = array('choice_7',			TEXT);
	$fields[] = array('choice_8',			TEXT);
	$fields[] = array('choice_9',			TEXT);
	$fields[] = array('answer_0',			NUMBER);
	$fields[] = array('answer_1',			NUMBER);
	$fields[] = array('answer_2',			NUMBER);
	$fields[] = array('answer_3',			NUMBER);
	$fields[] = array('answer_4',			NUMBER);
	$fields[] = array('answer_5',			NUMBER);
	$fields[] = array('answer_6',			NUMBER);
	$fields[] = array('answer_7',			NUMBER);
	$fields[] = array('answer_8',			NUMBER);
	$fields[] = array('answer_9',			NUMBER);
	$fields[] = array('answer_size',		NUMBER);
	$fields[] = array('content_id',			NUMBER);	/* one field added for v1.4 */

	save_csv('tests_questions', $sql, $fields);

	/****************************************************/
	/* news.csv */
	$sql	= 'SELECT * FROM '.TABLE_PREFIX.'polls WHERE course_id='.$_SESSION['course_id'];
	$fields = array();
	$fields[0] = array('question',		TEXT);
	$fields[1] = array('created_date',	TEXT);
	$fields[2] = array('choice1',		TEXT);
	$fields[3] = array('choice2',		TEXT);
	$fields[4] = array('choice3',		TEXT);
	$fields[5] = array('choice4',		TEXT);
	$fields[6] = array('choice5',		TEXT);
	$fields[7] = array('choice6',		TEXT);
	$fields[8] = array('choice7',		TEXT);

	save_csv('polls', $sql, $fields);
	/****************************************************/

	$zipfile->close();
	$zipfile->send_file($backup_course_title);

?>