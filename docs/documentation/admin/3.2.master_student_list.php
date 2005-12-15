<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg 		*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

require('../common/body_header.inc.php'); ?>

<h2>3.2 Master Student List</h2>
	<p>If the <a href="../admin/2.2.system_preferences.php">System Preferences</a> <em>Authenticate Against A Master Student List</em> option is enabled then this page will allow you to manage that list. If enabled, only new registrations that validate against the master list will be successful. The master list is flexible and can be used to validate any two fields, one of which is publicly viewable to Administrators, while the other is hidden. A common use of this feature would be to authenticate students using a previously assigned Student ID &amp; PIN combination.</p>


	<p>Subsequently when a student registers for an ATutor account on your system, they must provide this authenticating information (like their student ID and a PIN). Once an account is authenticated and created, that user will then be associated with the appropriate entry in the Master Student List. If the <em>Require Email Confirmation Upon Registration</em> option is enabled in the <a href="../admin/2.2.system_preferences.php">System Preferences</a>, then the user must confirm their account using that email before the account is activated.</p>

	<p>Viewing the Master Student List shows Student ID-Username pairs. Student IDs in the Master Student List that are not associated with any student account are considered to not have been created.</p>

<?php require('../common/body_footer.inc.php'); ?>