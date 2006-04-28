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

<h2>15.4 Editing &amp; Deleting Tests</h2>

<p>In the Test/Survey Manager, choose the test who's properties you wish to change and use the <code>Edit</code> button.  This will display a screen like the one for <a href="../instructor/15.1.creating_tests_surveys.php">Creating Tests &amp; Surveys</a>, where the test's properties can be altered and saved.</p>

<p>To delete a Test or Survey, choose it from the Test/Survey Manager and use the <code>Delete</code> button.  Aftering confirming the delete action, the test will be removed.  Note that the questions within the test will not be deleted as they are stored in the <a href="../instructor/15.2.question_database.php">Question Database</a>. </p>

<?php require('../common/body_footer.inc.php'); ?>