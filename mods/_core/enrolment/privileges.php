<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$
define('AT_INCLUDE_PATH', '../../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');

if (!authenticate(AT_PRIV_ADMIN, true)) {
	$msg->addError('ACCESS_DENIED');
	header('Location: index.php');
	exit;
}

$course_id = $_SESSION['course_id'];

require(AT_INCLUDE_PATH.'../mods/_core/enrolment/html/privileges.inc.php');
exit;
?>