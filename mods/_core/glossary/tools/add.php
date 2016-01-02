<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_GLOSSARY);
tool_origin();

if ($_POST['cancel']) {	
	$msg->addFeedback('CANCELLED');
        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
		exit;
}

if (isset($_POST['submit'])) {
	$num_terms = intval($_POST['num_terms']);
	$missing_fields = array();

	for ($i=0; $i<$num_terms; $i++) {

		if ($_POST['ignore'][$i] == '') {
			$_POST['word'][$i] = trim($_POST['word'][$i]);
			$_POST['definition'][$i] = trim($_POST['definition'][$i]);

			if ($_POST['word'][$i] == '') {
				$missing_fields[] = _AT('glossary_term');
			} else{
				//60 is defined by the sql
				$_POST['word'][$i] = validate_length($_POST['word'][$i], 60);
			}
			

			if ($_POST['definition'][$i] == '') {
				$missing_fields[] = _AT('glossary_definition');
			}

			if ($terms_sql != '') {
				$terms_sql .= ', ';
			}

			$_POST['related_term'][$i] = intval($_POST['related_term'][$i]);

			/* for each item check if it exists: */

			if ($glossary[urlencode($_POST['word'][$i])] != '' ) {
				$errors = array('TERM_EXISTS', $_POST['word'][$i]);
				$msg->addError($errors);
			} else {
				$_POST['word'][$i]         = $addslashes(preg_replace('#\'#','\\\'',$_POST['word'][$i]));
				$_POST['definition'][$i]   = $addslashes($_POST['definition'][$i]);
				$_POST['related_term'][$i] = $addslashes($_POST['related_term'][$i]);
				
				$terms_sql .= "(NULL, $_SESSION[course_id], '{$_POST[word][$i]}', '{$_POST[definition][$i]}', {$_POST[related_term][$i]})";
			}
		}
	}

	if ($missing_fields) {
		$missing_fields = implode(', ', $missing_fields);
		$msg->addError(array('EMPTY_FIELDS', $missing_fields));
	}

	if (!$msg->containsErrors()) {

		$sql = "INSERT INTO %sglossary VALUES $terms_sql";
		$result = queryDB($sql, array(TABLE_PREFIX), false, false);

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
		exit;
	}
	$_GET['pcid'] = $_POST['pcid'];
}

$onload = 'document.form.title0.focus();';

unset($word);

$num_terms = 1;

$sql = "SELECT * FROM %sglossary WHERE course_id=%d ORDER BY word";
$rows_g = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id']));

require(AT_INCLUDE_PATH.'header.inc.php');

$savant->assign('rows_g', $rows_g);
$savant->assign('num_terms', $num_terms);
$savant->display('instructor/glossary/add.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); 


?>