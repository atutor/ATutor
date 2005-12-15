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

<h2>2.0 Register</h2>

<p>In order for a user to login to the ATutor system, a unique system account needs to be created.  Use the <em>Register</em> link in the main navigation to access the registration form.  Enter all the required feels, and optional data if desired, and use the <code>Submit</code> button.  The login and password entered during registration can now be entered into the <a href="../general/1.0.login.php">Login</a> screen.</p>

<p>Note that if a system administrator has specified users to be checked against a list of allowed Student IDs and PINs (for example), these must also be entered during registration.</p>

<p>Addtionaly, confirmation of the account's email address may be required before logging in.</p>

<?php require('../common/body_footer.inc.php'); ?>