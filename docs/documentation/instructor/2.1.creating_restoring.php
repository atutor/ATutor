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

<h2>2.1 Creating &amp; Restoring Backups</h2>
	<p>To create a backup of the current course, use the <em>Create</em> link found on the Backups page. All created backups are stored securely on the ATutor server. The space required for the backups does not affect the course's size quota. Once a backup is created it will be listed on the main Backups page, where it can be managed.</p>

	<p>Backups can restored by selecting the backup and using the <kbd>Restore</kbd> button. The restoration process will presents details on what is stored in the backup and provides instructors with the ability to restore any or all of the available material.</p>

<?php require('../common/body_footer.inc.php'); ?>
