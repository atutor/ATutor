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

define('AT_INCLUDE_PATH', 'include/');
	$_ignore_page = true; /* without this we wouldn't know where we're supposed to go */
	require(AT_INCLUDE_PATH.'vitals.inc.php');

	$_section = _AT('home');
	header('Location: '.$_SESSION['my_referer']);
	exit;
?>