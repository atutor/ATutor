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

<h2>6.1 Export Content</h2>

<p>The Export Content feature creates a "Content Package" that can be downloaded and viewed offline in the viewer included with each package. If an instructor has turned this feature on, it can be accessed from the home page and/or the main navigation. Choose which section you wish to download as a content package and use the <code>Export</code> button.  <em>Export Content</em> is also linked from top level content pages or all content pages (depending on what the instructor has set) in the Shortcuts box.  Using this link will package the page you are on and all of its sub pages into a single "zip" file, and prompt you to download the file.</p>

<p>The downloaded file can be unpacked with a common archiving application (e.g. WinZip, PKZip, Unzip). Unzip the file into an empty directory then open "index.html" to open the viewer. A list of topics appears in the left frame of the viewer, and the content itself appears on the right. Choose a topic from the list to display its corresponding content.</p>

<?php require('../common/body_footer.inc.php'); ?>
