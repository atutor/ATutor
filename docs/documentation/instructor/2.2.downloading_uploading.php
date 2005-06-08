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

<h2>2.2 Downloading &amp; Uploading Backups</h2>
	<p>After creating backups they can be downloaded and stored locally by selecting from the list of backups created and using the <kbd>Download</kbd> button. Locally stored backups can be uploaded back into the original course, into a new course, or into another installation of ATutor.</p>

	<p>The backup file itself is a compressed archive in a format specific to ATutor. Backups cannot be used by any other system other than ATutor. Extracting the backup archive to view and change its contents is strongly discouraged as it may currupt the backup, making it impossible to restore.</p>

	<p>Backups are forwards compatible, but are not backwards compatible with older versions of ATutor. That is, backups can be used with all future versions of ATutor, but cannot be used with versions of ATutor older than the version originally used in its creation.</p>

<?php require('../common/body_footer.inc.php'); ?>
