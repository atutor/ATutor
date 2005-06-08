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
// $Id: menu_pages.php 4799 2005-06-06 13:19:09Z heidi $

require('../common/body_header.inc.php'); ?>

<h2>4.1.5 Accessibility</h2>
	<p>The Accessibility tab performs analyses of the content for accessibility problems, and recommends corrections. You are given the option to implement or reverse the corrections.</p>
	<p>After opening the Accessibility tab, review the report, and notice the number of <em>known</em> and <em>potential</em> problems</p>
	<p>Correct the known problems by reviewing the report, then returning to the Content tab to modifying the HTML. Return to the Accessibility tab when the known problems are corrected to see a <em>Conditional Pass</em>. Select from the choices available with the potential problems listed, then press <code>Make Decisions</code> to update the report. When all known problems are corrected, and decisions have been made on all potential problems, a <em>Full Pass</em> will be displayed, after which you can be sure the content will be accessible to all your students.

<?php require('../common/body_footer.inc.php'); ?>
