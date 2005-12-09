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
// $Id$

require('../common/body_header.inc.php'); ?>

<h2>2.4.1 Importing/Exporting Themes</h2>
	<p>Themes can be imported into, or exported from, ATutor using the Themes manager in the ATutor administrators' configuration tools. A theme might be downloaded from the atutor.ca Web site, or from another site, then imported into ATutor to give it a new look. Alternatively, a URL can be used to import the theme instead of having to download it first. An existing theme can be exported, then imported back into an ATutor installation to create a copy, after which the copy can be modified to create a new theme. Themes can be exported to share with others. See the <a href="http://www.atutor.ca/atutor/themes/index.php" target="_new">Themes page on atutor.ca</a> for a list of available themes, and for a place to share your themes.</p>
<p>To import a theme  the themes/ directory must be writable, otherwise an error message will appear. On Unix based systems use <code>chmod a+rw themes/</code> to make the directory writable. Once you have confirmed the installation of the new theme, remove the write permission using <code>chmod a-w themes/</code>. Windows user should not need to set permissions, unless they have previously set the directory to read only.</p>


<?php require('../common/body_footer.inc.php'); ?>
