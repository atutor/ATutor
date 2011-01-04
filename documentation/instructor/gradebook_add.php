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

<h3>Add ATutor Test/Assignment to Gradebook</h3>

<dl>
	<dt>Add ATutor Assignment</dt>
	<dd>Assignments that have been created using the ATutor Assignment Manager can be added to the gradebook. Select the <strong>Title</strong> of the assignment from those available, then select the scale to be used. Once an assignment has been added to the gradebook, marks are entered as <strong>External Marks</strong>. Marks may be entered either as a percentage mark or a scale mark. If percentage is used, the gradebook will attempt to convert those percentage marks to a scale mark</dd>

	<dt>Add ATutor Test</dt>
	<dd>Tests that are created using the ATutor Test &amp; Survey Manager can be added to the gradebook if the test's <strong>Attempts Allowed</strong> property has been set to 1 attempt.   Select the test <strong>Title</strong> from those available, then select the Grade Scale to be used for the test. Additional scales can be created by using the Grade Scales tool. Note that Surveys will not be available to add to the Gradebook. Surveys are technically  tests in the Test &amp; Survey Manager that do not have any "weight" or mark assigned to questions.</dd>
</dl>


<?php require('../common/body_footer.inc.php'); ?>
