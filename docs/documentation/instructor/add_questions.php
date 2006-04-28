<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: 15.4.managing_test_questions.php 4824 2005-06-08 19:27:33Z joel $

require('../common/body_header.inc.php'); ?>

<h2>15.6 Test Questions</h2>

<p>To manage the questions in a test, choose the test from the Test/Surveys Manager and then use the <code>Questions</code> button. Questions in the <a href="../instructor/15.2.question_database.php">Question Database</a> can be added to your test by using the <em>Add Questions</em> link. Check the questions and/or categories of questions to be added to the test and use the <code>Add to Test/Survey</code> button. After confirming this action, the added questions will appear in the Question Manager.  Beside each question is a box in which to enter a weight or mark for that question.  If this is for a survey, leave the weight box empty.  Note that Likert questions do not get marked and therefore do not require a weight.</p>

<p>It is also possible to <em>Edit</em> or <em>Remove</em> questions by using the links beside each question. Editing a question will alter it in the Question Database, and thus affect all tests and surveys using that question.  Removing the question only removes it from the test and will not delete the question from the Question Database.</p>

<?php require('../common/body_footer.inc.php'); ?>