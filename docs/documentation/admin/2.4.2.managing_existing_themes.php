<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg 		*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: 10.1.link_categories.php 4824 2005-06-08 19:27:33Z joel $

require('../common/body_header.inc.php'); ?>

<h2>2.4.2 Managing Existing Themes</h2>
	<p>All the available themes on an ATutor system will listed in the Administrator's Theme Manager.  </p>
	<ol>
	<li><code>Enable/Disable</code>: When a theme is enabled it will be available to student to choose from in their personal preferences access through <em>My Start Page</em> if Category Theme has not been enabled (see below). You might choose to disable a themes while it is being modified. If a theme  chosen by a student is disabled, that student will then see the system's default theme.</li>
	<li><code>Set as Default</code>: If a theme is set as the Default Theme, it will display for students who have not selected a prefered theme, and it will be displayed on public pages, such as the Login screen or Registration screen.</li>
	<li><code>Export</code>: Any theme can be exported from an ATutor installtion to share with others or to copy and import back into an ATutor installation after which it can be modified to create a new theme.</li>
	<li><code>Delete</code>: A theme is removed from the system if the Delete button is used.</li>
	</ol>

	<h3>Category Themes</h3>
	<p>If there are <a href="4.3.categories.php">Course Categories</a> defined and the <a href="2.2.system_preferences.php">Theme Specific Categories</a> system preference has been enabled, themes can be asigned to categories so all courses under a particular category are displayed with the same look and feel. When defining <a href="4.3.categories.php">Course Categories</a> while the Category Themes system preference is enabled, a list of themes will be available to select from and assign to each category.</p>
	<p>Note that when Category Themes has been enabled, users will no longer be able to select themes from their personal preference settings.</p>

<?php require('../common/body_footer.inc.php'); ?>
