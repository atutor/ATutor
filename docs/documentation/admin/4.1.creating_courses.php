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
// $Id: 4.5.scorm_packages.php 5063 2005-07-04 20:56:07Z heidi $

require('../common/body_header.inc.php'); ?>

<h2>4.1 Creating Courses</h2>

<p>See <a href="../instructor/0.1.creating_courses.php">Creating Courses</a> documentation for Instructors.</p>

<p>In addition, administrators have access to the following properties:</p>

	<dl>
		<dt>Course Quota</dt>
		<dd>Defines the maximum size of a course.  That is, the amount of space each course's file manager can have.</dd>

		<dt>Max File Size</dt>
		<dd>Defines the maximum size allowed for a file being uploaded to a course's file manager.</dd>
	</dl>

<?php require('../common/body_footer.inc.php'); ?>