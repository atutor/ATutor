<?php
/************************************************************************/
/* ATutor														        */
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Harris Wong					*/
/* Adaptive Technology Resource Centre / University of Toronto          */
/* http://atutor.ca												        */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.				        */
/************************************************************************/
// $Id: test_question_queries.inc.php 7653 2008-06-25 15:43:31Z hwong $

//Question for multiple choice.
define('AT_SQL_QUESTION_MULTI', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	0, %d, %d, 1, '%s', '%s', 
							'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 
							%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
							'', '', '', '', '', '', '', '', '', '', 5, 0)");

//Question for True/False
define('AT_SQL_QUESTION_TRUEFALSE', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	0, %d, %d, 2, '%s', '%s', 
							'', '', '', '', '', '', '', '', '', '', 
							%s, 0, 0, 0, 0, 0, 0, 0, 0, 0, 
							'', '', '', '', '', '', '', '', '', '', 5, 0)");


//Question for Open ended
define('AT_SQL_QUESTION_LONG', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	0, %d, %d, 3, '%s', '%s', 
							'', '', '', '', '', '', '', '', '', '', 
							0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 
							'', '', '', '', '', '', '', '', '', '', %s, 0)");

//Question for Likert
define('AT_SQL_QUESTION_LIKERT', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	0, %d, %d, 4, '%s', '%s', 
							'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 
							%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
							'', '', '', '', '', '', '', '', '', '', 0, 0)");

//Question for Ordering
define('AT_SQL_QUESTION_ORDERING', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	0, %d, %d, 6, '%s', '%s', 
							'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 
							%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
							'', '', '', '', '', '', '', '', '', '', 0, 0)");

//Question for MultiAnswer
define('AT_SQL_QUESTION_MULTIANSWER', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	0, %d, %d, 7, '%s', '%s', 
							'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 
							%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
							'', '', '', '', '', '', '', '', '', '', 0, 0)");

//Question for Matching
define('AT_SQL_QUESTION_MATCHING', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	0, %d, %d, 8, '%s', '%s', 
							'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 
							%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
							'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 0, 0)");
?>