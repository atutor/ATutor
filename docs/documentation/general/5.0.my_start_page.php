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
// $Id: 4.5.scorm_packages.php 5063 2005-07-04 20:56:07Z heidi $

require('../common/body_header.inc.php'); ?>

<h2>5.0 My Start Page</h2>

5. My Start Page
<p>This section is where one can manage their ATutor account. It allows them to browse existing or create new courses, edit their profile, and preferences.</p>

5.1 My Courses
<p>All enrolled and pending enrollment courses are listed here.</p>

5.1.1 Browse Courses
<p>This section lists all the courses on the system grouped by category.</p>

5.1.2 Create Course
<p>Only Instructors may create courses. Students are given the option of requesting instructor accounts. View the Instructor Documentation on creating courses.</p>

5.2 Profile
<p>This section allows a user to change their password, email address, and other profile details.</p>

5.3 Preferences
<p></p>
<dl>
    <dt>Theme</dt>
    <dd>Themes are used for changing the look and feel of an ATutor installation.</dd>

    <dt>Inbox Notification</dt>
    <dd>If enabled, an email notification message will be sent each time an Inbox message is received.</dd>

    <dt>Topic Numbering</dt>
    <dd>If enabled, numbers will appear prefixed to topic titles.</dd>

    <dt>Direct Jump</dt>
    <dd>If enabled, using the Jump feature will redirect to the current section in ATutor, but of the selected course.</dd>

    <dt>Auto-Login</dt>
    <dd>If enabled, a manual login is no longer required, instead viewing ATutor will automatically log one in.</dd>

    <dt>Form Focus On Page Load</dt>
    <dd>If enabled, the cursor will be placed at the first field in the form.</dd>

    <dt>Default Language</dt>
    <dd>The default language to view ATutor in.</dd>
</dl>

5.4 Inbox
<p>The Inbox is used for privately messaging other ATutor users. Use the <em>Inbox Notification</em> preference to receive emails when a new Inbox message is received.</p>


<p></p>

<?php require('../common/body_footer.inc.php'); ?>
