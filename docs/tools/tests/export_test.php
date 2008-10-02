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
// $Id$
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/testQuestions.class.php');

authenticate(AT_PRIV_TESTS);

$tid = intval($_GET['tid']);

/* Retrieve the content_id of this test */
$sql = "SELECT title, random, num_questions, instructions FROM ".TABLE_PREFIX."tests WHERE test_id=$tid";
$result	= mysql_query($sql, $db); 
if (!($test_row = mysql_fetch_assoc($result))) {
	$msg->addError('ITEM_NOT_FOUND');
	header(url_rewrite('tools/tests/index.php', AT_PRETTY_URL_IS_HEADER));
	exit;
}

//export
test_qti_export($tid, $test_row['title']);
?>