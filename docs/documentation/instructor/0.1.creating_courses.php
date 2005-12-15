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

<h2>0.1 Creating Courses</h2>

<p>After logging in, use the <em>Create Course</em> link from My Start Page.</p>

<p>Some course properties include:</p>

	<dl>
		<dt>Description</dt>
		<dd>Enter a meaningful but brief paragraph describing the course.  This will be displayed under the course name in <em>Browse Courses</em> as well as on the My Start Page for those enrolled.</dd>

		<dt>Export Content</dt>
		<dd>Choose the availability of the "Export Content" link on course content pages.</dd>

		<dt>Syndicate Announcements</dt>
		<dd>Enable this setting if you wish to make an RSS feed of the course announcements available for display on another website.</dd>

		<dt>Access</dt>
		<dd>determines who can have access to the course content - any user, only logged in users, or logged in and enrolled users.</dd>

		<dt>Initial Content</dt>
		<dd>initialise the course content to be either empty, basic place-holder content, or a restored backup from other courses you teach.</dd>
	</dl>

<p>Enter the necessary information and use the <code>Save</code> button to proceed into the newly created course.</p>


<?php require('../common/body_footer.inc.php'); ?>