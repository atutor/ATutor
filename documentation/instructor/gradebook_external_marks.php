<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: 8.4.managing_forums.php 5080 2005-07-06 15:06:11Z heidi $

require('../common/body_header.inc.php'); ?>

<h3>External Marks</h3>

<dl>
	<dt>Export</dt>
	<dd>The Export tool is used to export a course list in a CSV form into which marks can be entered manually, then reimported back into the Gradebook. It can also be used to export marks from the Gradebook to import those marks back into another application such as a spreadsheet, or another database.</dd>

	<dt>Import</dt>
	<dd>Marks from an external assignment or test can be imported in a Comma Separated Values (CSV) file in the form <em>"firstname", "lastname", "email", "grade"</em> with one student per line. The mark can either be a scale mark such <em>"A"</em> or <em>"Pass"</em>, or a percentage mark such as <em>78%</em>. Select the test or assignment previously defined through <a href="gradebook_add.php"><strong>Add Tests/Assignment</strong></a> The first line of the imported file should contain the field names <em>"First Name, Last Name, Email, Grade" </em> If it is not included the first line will be removed when the marks are imported.</dd>
</dl>


<?php require('../common/body_footer.inc.php'); ?>
