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
	<li><em>Link from My Courses</em> allows you to link a test to the course listing in My Courses so students are made aware that a current test is available before they enter the course. </li>
	<li><em>Release Result</em> properties allows you to make results available to students as soon as they have completed the test, to hold results until the instructor has reviewed all answers submitted by a test taker, or to hold result until all students have completed the test. In the latter case, reset the Release Result property to <em>Once quiz has been submitted</em> to make results available to student after all submission have been marked.
	<li><em>Randomized questions</em> can be presented, of which some questions can be required, and others left optionally.</li>
	<li><em>Start & End Dates</em> can be set, between which students can access a test or survey.
	<li><em>Test Groups</em> groups defined using the <a href="./6.0.enrollment.php">Enrollment Manager</a> will appear on the test properties, so tests are available only to those in the group. Otherwise by default tests are available to Everyone in the course. Once the initial properties have been saved, the test will be listed in the Test/Survey Manager, after which you may add questions.</li>
	</ul>
	</p>
	
	<p><strong>Surveys</strong> are created much like regular tests are created, except <em>No Marks</em> are assigned to questions when they are added to the survey, and in some cases submitters are left anonymous by choosing Yes from the <em>Anonymous</em> property setting.</p>


<?php require('../common/body_footer.inc.php'); ?>
