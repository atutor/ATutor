<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

	define('AT_INCLUDE_PATH', '../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');

	require(AT_INCLUDE_PATH.'header.inc.php');

?>
<ol>
	<li><a href="tools/content/index.php"><?php echo _AT('content'); ?></a> (add, usage, content packaging, tile search)</li>
	<li><a href="tools/news/index.php"><?php echo _AT('announcements'); ?></a></li>
	<li><a href="tools/forums/index.php"><?php echo _AT('forums'); ?></a></li>
	<li><a href="tools/course_properties.php"><?php echo _AT('properties'); ?></a></li>
	<li><a href="tools/backup/index.php"><?php echo _AT('backups'); ?></a></li>
	<li><a href="tools/enrollment/index.php"><?php echo _AT('enrolment'); ?></a> (send email, enrollment manager)</li>
	<li><a href="tools/polls/index.php"><?php echo _AT('polls'); ?></a></li>
	<li><a href="tools/links/index.php"><?php echo _AT('links'); ?></a></li>
	<li><a href="tools/filemanager/index.php"><?php echo _AT('file_manager'); ?></a></li>
	<li><a href="tools/tests/index.php"><?php echo _AT('test_manager'); ?></a></li>
	<li><a href="tools/course_stats.php"><?php echo _AT('course_stats'); ?></a></li>
	<li><a href="tools/modules.php"><?php echo _AT('sections'); ?></a> (home links, tabs, side menu)</li>
	<li><a href="tools/glossary/index.php"><?php echo _AT('glossary'); ?></a></li>
</ol>
<!--
<a href="acollab/bounce.php?p=<?php echo urlencode('admin/groups_create.php'); ?>"> <?php echo _AT('ac_create'); ?></a><br />
<a href="acollab/bounce.php"><?php echo _AT('ac_access_groups'); ?></a>
<br><br><br>
<a href="tools/banner.php"><?php echo _AT('course_banner'); ?></a>
-->

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>