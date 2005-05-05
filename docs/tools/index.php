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
	$tools_list = array('tools/news/index.php'       => AT_PRIV_ANNOUNCEMENTS,
						'tools/backup/index.php'     => AT_PRIV_ADMIN,	
						'tools/chat/index.php'		 => AT_PRIV_FORUMS,
						'tools/content/index.php'    => AT_PRIV_CONTENT,
						'tools/course_email.php'     => AT_PRIV_COURSE_EMAIL,
						'tools/enrollment/index.php' => AT_PRIV_ENROLLMENT,
						'tools/filemanager/index.php'=> AT_PRIV_FILES,
						'tools/forums/index.php'     => AT_PRIV_FORUMS,
						'tools/glossary/index.php'   => AT_PRIV_GLOSSARY,
						'tools/links/index.php'      => AT_PRIV_LINKS,
						'tools/polls/index.php'      => AT_PRIV_POLLS,
						'tools/course_properties.php'=> AT_PRIV_ADMIN,
						'tools/course_stats.php'     => AT_PRIV_ADMIN,
						'tools/modules.php'          => AT_PRIV_STYLES,
						'tools/tests/index.php'      => AT_PRIV_TEST_CREATE + AT_PRIV_TEST_MARK,
						);

	require(AT_INCLUDE_PATH.'header.inc.php');
	echo '<ol>';
	foreach ($tools_list as $location=>$priv) {
		if (authenticate($priv, AT_PRIV_RETURN)) {
			echo '<li>'; 
			echo '<a href="' . $location . '">' . _AT($_pages[$location]['title_var']) . '</a>';
			echo '</li>';
		}
	}
	/*
	if (defined('AC_PATH') && AC_PATH) {
		echo '<li>'; 
		echo '<a href="acollab/bounce.php">' . _AT($_pages['acollab/bounce.php']['title_var']) . '</a>';
		echo '</li>';
	}
	*/

	echo '</ol>';

	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
?>