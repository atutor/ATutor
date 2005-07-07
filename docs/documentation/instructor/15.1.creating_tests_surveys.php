<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$


require('../common/body_header.inc.php'); ?>

<h2>15.1 Creating a Test or Survey</h2>
	<p>To begin creating a test, use the <em>Create Test/Survey</em> link. Filling out the information on the Create Test/Survey page will address all the administrative options for a test. Actual questions are added to the test in a separate step.</p>

	<p>Some properties that may require explanation when creating a <strong>Test/Quiz</strong>:
	<ul>
	<li><em>Link from My Courses</em> will display a link to the test on the My Courses page, in the course listing. Students will be made aware that the current test is available before they enter the course. </li>
	
	<li><em>Release Results</em> defines the availability of test results to students, either once the test has been submitted, once submitted and completely marked, or not at all. In the latter case, the Release Results property can be changed to <em>Once quiz has been submitted</em> to make results available to students once all submissions have been marked.

	<li><em>Randomized Questions</em> will display the number of questions specified, chosen randomly from the pool of available questions for that test.</li>
	
	<li><em>Start & End Dates</em> define the window of time in which the test will be available for taking.  It is possible to define the start date to be in the future, meaning the test will not be available until that date.</li>
	
	<li><em>Assign to Groups</em> specifies the groups (as defined in the <a href="./6.0.enrollment.php">Enrollment Manager</a>) permitted to take this test. By default, tests are available to Everyone in the course.</li>
	</ul>
	</p>
	
	<p><strong>Surveys</strong> are the same as regular tests, with the exception that no marks be assigned to questions (and no results be released), and in some cases it might be preferable to treat submissions as <em>Anonymous</em>.  This can be done by choosing Yes from the <em>Anonymous</em> property setting.</p>

	<p>Once the initial properties have been saved, the test or survey will be listed in the Test/Survey Manager.  From here, one can <em>Edit</em> the test properties, <em>Preview</em> the test questions, add <em>Questions</em> to a test, view the <em>Submissions</em> received so far, view the test <em>Statistics</em>, or <em>Delete</em> the test.</p>

<?php require('../common/body_footer.inc.php'); ?>
