<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id: 8.4.managing_forums.php 5080 2005-07-06 15:06:11Z heidi $

require('../common/body_header.inc.php'); ?>

<h3>Update ATutor Marks</h3>


<dl>
	<dt>Update ATutor Marks</dt>
	<dd>Marks are imported from ATutor tests, rather than displaying them live from the Test & Surveys Manager. Therefore, when marks are updated in the Test & Surveys Manager, the Gradebook needs to be updated to reimport the modified marks. You may choose to update all ATutor tests at once, or choose only to update a single test at a time. Or, you may choose to update only marks for a single student, on all test or a single test.<strong> Note that instructors' grades, produced when an instructor takes a test, are not included when marks are updated.</strong></dd>

	<dt>Combine ATutor Tests</dt>
	<dd>Different ATutor generated tests can be combined into a single gradebook entry, if for instance you needed to combine marks from a term test, and marks from a make up test for students who happened to miss the term test. As many tests as required can be combined into a single parent test listed in the <strong>Combine Into</strong>  menu. Select the test to be combined from the <strong>Combined From</strong> select menu, then press the <strong>Combine</strong> button to import the marks from that test.  Be sure to run <strong>Update ATutor Marks</strong> on the <strong>Combined Into</strong> test at least once before combining marks from other tests. When combining marks from multiple tests, should you encounter a conflict such as a mark that already exists for a particular student, you will be given the option to overwrite the old mark with the new one, use the old mark, use the higher mark, or use the lower mark. </dd>
</dl>


<?php require('../common/body_footer.inc.php'); ?>
