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

<h2>2.1 Creating &amp; Restoring Backups</h2>
	<p>To create a backup of the current course, use the <em>Create</em> link found on the Backups page. All created backups are stored securely on the ATutor server. The space required for the backups does not affect the course's size quota. Once a backup is created, it will be listed on the main Backups page where it can be managed.</p>

	<p>Backups can be restored by selecting a backup and using the <kbd>Restore</kbd> button. The restoration process will present details on what is stored in the backup and allow instructors to select which course material they wish to restore.</p>

<?php require('../common/body_footer.inc.php'); ?>
