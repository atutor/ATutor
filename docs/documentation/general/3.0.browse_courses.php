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
// $Id$

require('../common/body_header.inc.php'); ?>

<h2>3.0 Browse Courses</h2>

<p>The Browse Courses page lists all available courses presently on the ATutor system.  The first column of the Browse Courses screen provides a list of course categories, the second displays a list of courses within the selected category, and the third column shows details about the selected course (or 'All' courses) and a link to enter into the course.</p>  

<p>If a course is Public, it may be accessed without logging in first.  Protected and Private courses require that you be logged in and therefore using the <em>Enter Course</em> link will redirect to the Login page.  Protected courses are accessible to all logged in users (with some features not available, such as tests and forums), and Private courses are only available to enrolled users.</p>

<?php require('../common/body_footer.inc.php'); ?>
