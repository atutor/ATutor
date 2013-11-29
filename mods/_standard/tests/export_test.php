<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'../mods/_standard/tests/classes/testQuestions.class.php');

authenticate(AT_PRIV_TESTS);

$tid = intval($_GET['tid']);

/* Retrieve the content_id of this test */
$sql = "SELECT title, random, num_questions, instructions FROM %stests WHERE test_id=%d";
$test_row	= queryDB($sql, array(TABLE_PREFIX, $tid), TRUE); 

if(count($test_row) == 0){
	$msg->addError('ITEM_NOT_FOUND');
	header(url_rewrite('mods/_standard/tests/index.php', AT_PRETTY_URL_IS_HEADER));
	exit;
}

//export
test_qti_export($tid, $test_row['title']);
?>