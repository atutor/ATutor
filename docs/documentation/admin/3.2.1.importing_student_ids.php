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

<h2>3.2.1 Importing Student IDs</h2>
	<p>Importing Student IDs into the Master Student List requires a specifically formatted file:</p>

	<p>The master list must be a plain text file, where each row in the file contains two fields seperated by a single comma. The first field will be used as the Student ID. The second field will be the PIN or Password which will be encoded by the ATutor system, once the list is uploaded, so that it cannot be viewed and read by anyone. Those two fields together will be used to authenticate students when creating new accounts. The fields may optionally be enclosed by double quotes. Such a file is known as a <acronym title="Comma Separated Values">CSV</acronym> file and can be generated manually using a text editor, or by any spreadsheet application (such as MS Excel).</p>

	<p>In the example below, a student number and a birth date are used to construct a master list:</p>
	<pre>"12345", "10/07/54"
"12346", "23/04/76"
"12347", "30/05/68"</pre>

<?php require('../common/body_footer.inc.php'); ?>