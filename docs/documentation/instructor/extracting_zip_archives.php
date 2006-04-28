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

<h2>7.7 Extracting Zip Archives</h2>
	<p>After uploading a ZIP file to the File Manager, select the <em>Extract Archive</em> icon next to the file name. This will display the contents of the zip file and suggest a directory name in which to unzip the archive. Use the <code>Extract</code> button in the ZIP file viewer to unzip the file into the specified directory.</p>

	<p><strong>Illegal file types</strong> will not be extracted, and file names containing illegal characters will be renamed. The viewer will show illegal file types <span style="text-decoration: line-through;">crossed out</span>, and files with illegal characters pointing ( => ) to the renamed file that will be extracted.</p>


<?php require('../common/body_footer.inc.php'); ?>
