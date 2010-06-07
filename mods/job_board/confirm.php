<?php
/***********************************************************************/
/* ATutor															   */
/***********************************************************************/
/* Copyright (c) 2002-2009											   */
/* Adaptive Technology Resource Centre / Inclusive Design Institute	   */
/* http://atutor.ca													   */
/*																	   */
/* This program is free software. You can redistribute it and/or	   */
/* modify it under the terms of the GNU General Public License		   */
/* as published by the Free Software Foundation.					   */
/***********************************************************************/
// $Id$
$_user_location = 'public';

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

if (isset($_GET['id'], $_GET['m'])) {
	$id = intval($_GET['id']);
	$m  = $_GET['m'];

	$sql = "SELECT email, requested_date FROM ".TABLE_PREFIX."jb_employers WHERE id=$id AND approval_state=".AT_JB_STATUS_UNCONFIRMED;
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$code = substr(md5($row['email'] . $row['requested_date'] . $id), 0, 10);
		if ($code==$m){
			//confirmed
			$sql = "UPDATE ".TABLE_PREFIX."jb_employers SET approval_state=".AT_JB_STATUS_CONFIRMED.", last_login=NOW() WHERE id=$id";
			mysql_query($sql, $db);
			$msg->addFeedback('CONFIRM_GOOD');

		} else {
			//not confirmed
			$msg->addError('CONFIRM_BAD');			
		}
	}
}

header('Location: index.php');
exit;
?>