<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$_SESSION['member_id']	  = $_SESSION['member_id'];
$_SESSION['lang']		  = $_SESSION['lang'];
$_SESSION['courtyard_id'] = $_SESSION['course_id'];
$_SESSION['house_id']     = 0;

if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
	$_SESSION['courtyard_priv'] = 5;
	$_SESSION['status'] = 1;
} else if (authenticate(AT_PRIV_AC_CREATE, AT_PRIV_RETURN) && authenticate(AT_PRIV_AC_ACCESS_ALL, AT_PRIV_RETURN)) {
	$_SESSION['courtyard_priv'] = 5;
	$_SESSION['status'] = 1;
} else if (authenticate(AT_PRIV_AC_CREATE, AT_PRIV_RETURN)) {
	$_SESSION['courtyard_priv'] = 2;
	$_SESSION['status'] = 1;
} else if (authenticate(AT_PRIV_AC_ACCESS_ALL, AT_PRIV_RETURN)) {
	$_SESSION['courtyard_priv'] = 3;
	$_SESSION['status'] = 1;
} else {
	$_SESSION['courtyard_priv'] = 1;
	$_SESSION['status'] = 1;
}

session_write_close();

//$page = 'index.php?p='.$_GET['p'];

$page = $_config['ac_path'];

header('Location: '. $page);
exit;
?>