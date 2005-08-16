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

	/* The array containig all tool page names and the associated privilege */
	/*
	$tools_list['tools/news/index.php']        = AT_PRIV_ANNOUNCEMENTS;
	$tools_list['tools/backup/index.php']      = AT_PRIV_ADMIN;
	$tools_list['tools/chat/index.php']		   = AT_PRIV_FORUMS;
	$tools_list['tools/content/index.php']     = AT_PRIV_CONTENT;
	$tools_list['tools/course_email.php']      = AT_PRIV_COURSE_EMAIL;
	$tools_list['tools/enrollment/index.php']  = AT_PRIV_ENROLLMENT;
	$tools_list['tools/filemanager/index.php'] = AT_PRIV_FILES;
	$tools_list['tools/forums/index.php']      = AT_PRIV_FORUMS;
	$tools_list['tools/glossary/index.php']    = AT_PRIV_GLOSSARY;
	$tools_list['tools/links/index.php']       = AT_PRIV_LINKS;
	$tools_list['tools/packages/index.php']    = AT_PRIV_CONTENT;
	$tools_list['tools/polls/index.php']       = AT_PRIV_POLLS;
	$tools_list['tools/course_properties.php'] = AT_PRIV_ADMIN;
	$tools_list['tools/course_stats.php']      = AT_PRIV_ADMIN;
	$tools_list['tools/modules.php']           = AT_PRIV_STYLES;
	$tools_list['tools/tests/index.php']       = AT_PRIV_TEST_CREATE + AT_PRIV_TEST_MARK;
	*/

	require(AT_INCLUDE_PATH.'header.inc.php');

	/* there's no real need to loop twice, other than to cache this first loop */
	$tools_list = array();
	foreach ($_pages as $page => $info) {
		if (isset($info['privilege'])) {
			if (isset($info['title'])) {
				$tools_list[$page] = $info['title'];
			} else {
				$tools_list[$page] = _AT($info['title_var']);
			}
		}
	}

	natsort($tools_list);

	/* would be nice if we could sort the $tools_list array by the title */

	echo '<ol>';
	foreach ($tools_list as $location => $title) {
		if (authenticate($_page[$location]['privilege'], AT_PRIV_RETURN)) {
			echo '<li><a href="' . $location . '">' . $title . '</a></li>';
		}
	}
	echo '</ol>';

	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
?>