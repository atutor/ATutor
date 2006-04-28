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
// $Id: 15.5.student_submissions.php 4824 2005-06-08 19:27:33Z joel $

require('../common/body_header.inc.php'); ?>

<h2>15.7 Test Submissions</h2>
	<p>To view the submissions of a test, choose a test from the Test/Survey Manager and use the <code>Submissions</code> button.  The list of student submissions will be listed, and can be filtered to show all, marked or unmarked tests.</p>
	
	<p>Unmarked tests are those requiring instructor input, or those with open-ended questions. Multiple-choice and true-false questions are automatically marked by the Atutor system and Likert questions do not require marking.</p>
	
	<p>To view and/or mark test submissions, choose a submission from the list and use the <kbd>View &amp; Mark Test</kbd> button. The test will be displayed with a box beside each question for entering or editing the mark.  Multiple-choice and true-false answers show a red "X" icon beside an answer if the student answered incorrectly, or a green checkmark if he/she was right.  If an answer is incorrect, the correct answer will be shown with a green checkmark after it in brackets. Use <code>Save</code> to enter the marks into the system and return to the submission manager.</p>

<?php require('../common/body_footer.inc.php'); ?>