<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: index.php 2734 2004-12-08 20:21:10Z joel $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_ADMIN);

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<ul>
	<!-- 1. View page Stats -->
	<li>
		<a href="tools/tracker/page_stats.php"><?php echo _AT('g_show_page_stats'); ?></a><br />
		<?php echo _AT('g_show_page_stats_desc'); ?>
	</li>
	<br />

	<!-- 2. View Member Stats -->
	<li>
		<a href="tools/tracker/member_stats.php"><?php echo _AT('g_show_member_stats'); ?></a><br />
		<?php echo _AT('g_show_member_stats_desc'); ?>
	</li>
	<br />

	<!-- 3. Download Tracking data -->
	<li>
		<a href="tools/tracker/download_stats.php"><?php echo _AT('g_download_tracking_csv'); ?></a><br />
		<?php echo _AT('g_download_tracking_csv_desc'); ?>
	</li>
	<br />

	<!-- 4. Reset Tracker -->
	<li>
		<a href="tools/tracker/reset.php"><?php echo _AT('g_reset_tracking'); ?></a><br />
		<?php echo _AT('g_reset_tracking_desc'); ?>
	</li>
</ul>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>