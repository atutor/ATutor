<?php
/************************************************************************/
/* ATutor														        */
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Harris Wong					*/
/* Inclusive Design Institute                                           */
/* http://atutor.ca												        */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.				        */
/************************************************************************/
// $Id: test_question_queries.inc.php 10142 2010-08-17 19:17:26Z hwong $

//Question for multiple choice.
define('AT_SQL_QUESTION_MULTI', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL, %d, %d, 1, '%s', '%s', 
							'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 
							%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
							'', '', '', '', '', '', '', '', '', '', 5, 0)");

//Question for True/False
define('AT_SQL_QUESTION_TRUEFALSE', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL, %d, %d, 2, '%s', '%s', 
							'', '', '', '', '', '', '', '', '', '', 
							%s, 0, 0, 0, 0, 0, 0, 0, 0, 0, 
							'', '', '', '', '', '', '', '', '', '', 5, 0)");


//Question for Open ended
define('AT_SQL_QUESTION_LONG', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL, %d, %d, 3, '%s', '%s', 
							'', '', '', '', '', '', '', '', '', '', 
							0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 
							'', '', '', '', '', '', '', '', '', '', %s, 0)");

//Question for Likert
define('AT_SQL_QUESTION_LIKERT', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL, %d, %d, 4, '%s', '%s', 
							'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 
							%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
							'', '', '', '', '', '', '', '', '', '', 0, 0)");

//Question for Ordering
define('AT_SQL_QUESTION_ORDERING', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL, %d, %d, 6, '%s', '%s', 
							'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 
							%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
							'', '', '', '', '', '', '', '', '', '', 0, 0)");

//Question for MultiAnswer
define('AT_SQL_QUESTION_MULTIANSWER', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL, %d, %d, 7, '%s', '%s', 
							'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 
							%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
							'', '', '', '', '', '', '', '', '', '', 0, 0)");

//Question for Matching Simple
define('AT_SQL_QUESTION_MATCHING', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL, %d, %d, 5, '%s', '%s', 
							'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 
							%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
							'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 0, 0)");

//Question for Matching Graphical
define('AT_SQL_QUESTION_MATCHINGDD', "INSERT INTO ".TABLE_PREFIX."tests_questions VALUES (	NULL, %d, %d, 8, '%s', '%s', 
							'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 
							%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 
							'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 0, 0)");

define('AT_SQL_TEST', "INSERT INTO ".TABLE_PREFIX."tests " .
					   "(test_id,
					 course_id,
					 title,
					 description,
					 `format`,
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
					 display) " .
					   "VALUES 
						(NULL, %d, '%s', '%s', %d, '%s', '%s', %d, %d, '%s', %d, %d, %d, '%s', '%s', %s, %d, %d, %d, %s, '', %d, %d)");

?>