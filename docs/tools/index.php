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
	$tools_list = array('tools/content/index.php'    => AT_PRIV_CONTENT,
						'tools/news/index.php'       => AT_PRIV_ANNOUNCEMENTS,
						'tools/forums/index.php'     => AT_PRIV_FORUMS,
						'tools/course_properties.php'=> AT_PRIV_ADMIN,
						'tools/backup/index.php'     => AT_PRIV_ADMIN,
						'tools/enrollment/index.php' => AT_PRIV_ENROLLMENT,
						'tools/course_email.php'     => AT_PRIV_COURSE_EMAIL,
						'tools/polls/index.php'      => AT_PRIV_POLLS,
						'tools/links/index.php'      => AT_PRIV_LINKS,
						'tools/filemanager/index.php'=> AT_PRIV_FILES,
						'tools/tests/index.php'      => AT_PRIV_TEST_CREATE + AT_PRIV_TEST_MARK ,
						'tools/course_stats.php'     => AT_PRIV_ADMIN,
						'tools/modules.php'          => AT_PRIV_STYLES,
						'tools/glossary/index.php'   => AT_PRIV_GLOSSARY);

	require(AT_INCLUDE_PATH.'header.inc.php');
	
	echo '<ol>';
	foreach ($tools_list as $location=>$priv) {
		if (authenticate($priv, AT_PRIV_RETURN)) {
			echo '<li>'; 
			echo '<a href="' . $location . '">' . _AT($_pages[$location]['title_var']) . '</a>';
			echo '</li>';
		}
	}
	echo '</ol>';

	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
?>