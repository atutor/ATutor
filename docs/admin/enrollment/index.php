<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: index.php 6751 2007-02-12 18:56:44Z joel $
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

admin_authenticate(AT_ADMIN_PRIV_ENROLLMENT);

if (!isset($_GET['course_id'])) {
	$sql = "SELECT course_id FROM ".TABLE_PREFIX."courses ORDER BY title LIMIT 1";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	$course_id = intval($row['course_id']);
} else {
	$course_id = intval($_REQUEST['course_id']);
}

require(AT_INCLUDE_PATH.'html/enrollment.inc.php');
exit;
?>