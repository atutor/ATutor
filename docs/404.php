<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', 'include/');

	require (AT_INCLUDE_PATH.'vitals.inc.php');
	require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

	global $savant;
	$msg =& new Message($savant);

	$_section[0][0] = _AT('404');

	require (AT_INCLUDE_PATH.'header.inc.php');
	
	echo '<h2>'._AT('404').'</h2>';
	$_info = array('404_BLURB', $_SERVER['REQUEST_URI']);
	$msg->printInfos($_info);

	$msg->printAll();

	require (AT_INCLUDE_PATH.'footer.inc.php'); 
?>