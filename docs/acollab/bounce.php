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
// $Id

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

	$_SESSION['member_id']	= $_SESSION['member_id'];
	$_SESSION['username']	= $_SESSION['username'];
	$_SESSION['lang']		= $_SESSION['lang'];
	$_SESSION['courtyard_id'] = $_SESSION['course_id'];
	$_SESSION['house_id']   = 0;

	if (authenticate(AT_PRIV_AC_CREATE, AT_PRIV_RETURN)) {
		$_SESSION['status'] = 3;
	} else if (authenticate(AT_PRIV_AC_ACCESS_ALL, AT_PRIV_RETURN)) {
		$_SESSION['status'] = 5;
	} else {
		$_SESSION['status'] = 1;
	}

if($_GET['p']) {
	$page = urldecode($_GET['p']);
} else {
	$page = 'index.php';
}

	header('Location: ../../../acollab/docs/' . $page);
	exit;
?>