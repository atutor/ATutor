<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
define('AT_INCLUDE_PATH', 'include/');

	$_ignore_page = true; /* used for the close the page option */
	require (AT_INCLUDE_PATH.'vitals.inc.php');
	require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

	global $savant;
	$msg =& new Message($savant);

	require(AT_INCLUDE_PATH.'html/frameset/header.inc.php');	
?>
[<a href="javascript:window.close()"><?php echo _AT('close_help_window'); ?></a>]
<?php

	/**
	 * Modified Jacek Materna
	 * Using Message.class.php layer to print
	 */
	if ($_GET['h']) {
		/*$h = intval($_GET['h']);
		if ($h > 0) {
			//print_help($h);
		} else { */
		$h = $_GET['h'];
		
		if (is_string($_GET['h'])) { // just a AT_HELP code with no prefix
			$msg->printHelps($h);
		} else {
			/* it's probably an array */
			$h = unserialize(urldecode(stripslashes($_GET['h'])));
			$msg->printHelps($h);
		}
	}
?>
</body>
</html>