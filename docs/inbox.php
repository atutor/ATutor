<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

	define('AT_INCLUDE_PATH', 'include/');
	
	require (AT_INCLUDE_PATH.'vitals.inc.php');
	$_section[0][0] = _AT('inbox');
	$_section[0][1] = 'inbox.php';

	$_GET['view'] = intval($_GET['view']);

	if ($_GET['view']) {
		$result = mysql_query("UPDATE ".TABLE_PREFIX."messages SET new=0 WHERE to_member_id=$_SESSION[member_id] AND message_id=$_GET[view]",$db);
	}

	$current_path = './';

	require (AT_INCLUDE_PATH.'header.inc.php');

	require (AT_INCLUDE_PATH.'lib/inbox.inc.php');

	require (AT_INCLUDE_PATH.'footer.inc.php'); 
?>