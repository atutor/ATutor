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
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_FAQ);
tool_origin();
if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
		exit;
} else if (isset($_POST['submit'])) {
	if (trim($_POST['name']) == '') {
		$msg->addError('NAME_EMPTY');
	}

	if (!$msg->containsErrors()) {
		$_POST['name'] = $addslashes($_POST['name']);
		//This will truncate the content of the length to 240 as defined in the db.
		$_POST['name'] = validate_length($_POST['name'], 250);

		$sql	= "INSERT INTO %sfaq_topics VALUES (NULL, %d, '%s')";
		$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $_POST['name']));
				
		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
        $return_url = $_SESSION['tool_origin']['url'];
        tool_origin('off');
		header('Location: '.$return_url);
		exit;
	}
}

$onload = 'document.form.name.focus();';

require(AT_INCLUDE_PATH.'header.inc.php');


$savant->display('instructor/faq/add_topic.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>