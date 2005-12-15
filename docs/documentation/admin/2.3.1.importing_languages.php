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

<h2>2.3.1 Importing Languages</h2>
	<p>Language packs can be imported either manually by retreiving the package and then importing it into ATutor, or automatically by having ATutor connect to the atutor.ca language repository directly.</p>

	<p>To <em>manually</em> import a new language pack:</p>
	<ol>
		<li>Visit <a href="http://atutor.ca/atutor/translate/index.php" target="_new">atutor.ca/atutor/translate/</a> to download one of the available language packs for your version.</li>
		<li>Use the <code>Browse...</code> button to find the downloaded language pack.</li>
		<li>Use the <code>Import</code> button to import the language.</li>
	</ol>

	<p>If your ATutor installation is connected to the Internet and can contact the atutor.ca website then it will try to retrieve the list remotely. To <em>automatically</em> import a new language pack from within ATutor:</p>

	<ol>
		<li>Select the language you want to import from the drop down.</li>
		<li>Use the <code>Import</code> button to import the selected language.</li>
	</ol>

	<p>If your installation cannot retrieve the language list from atutor.ca, a message indicating so will be presented rather than a drop down list. In this case you will have to use the manual method described above.</p>

<?php require('../common/body_footer.inc.php'); ?>
