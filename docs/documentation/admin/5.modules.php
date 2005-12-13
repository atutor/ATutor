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

<h2>5. Modules</h2>
	<p>Since version 1.5.2 ATutor provides the facility to install, endable, or disable student, instructor, and administrator tools as modules.</p>

	<p>To install a module it must first be extracted into a unique subdirectory within <kbd>./mods</kbd>. It will then be listed on the <em>Install Modules</em> page where more details can be retrieved and the module installed.</p>

	<p>After extracting a module, be sure to see the readme file in the module's top directory for any additional installation instructions or requirements. See the ATutor Module Developer documentation for information about creating ATutor modules, and review the module files in the Hello World demo module (and other modules) for a model that can be duplicated and modified to quickly add new addon features to your ATutor installation.

<?php require('../common/body_footer.inc.php');?>