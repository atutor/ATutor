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

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/';

$pid = intval($_GET['pid']);
$fid = intval($_GET['fid']);

if ($_GET['us']) {
	$sql	= "DELETE FROM ".TABLE_PREFIX."forums_subscriptions WHERE post_id=$pid AND member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);

} else {
	$sql	= "INSERT INTO ".TABLE_PREFIX."forums_subscriptions VALUES ($pid, $_SESSION[member_id])";
	$result = mysql_query($sql, $db);
}
if ($_GET['us'] == '1'){
	header('Location: '.$_base_href.'forum/view.php?fid='.$fid.SEP.'pid='.$pid.SEP.'f='.urlencode_feedback(AT_FEEDBACK_THREAD_UNSUBCRIBED));
	exit;
}
/* else: */
	header('Location: '.$_base_href.'forum/view.php?fid='.$fid.SEP.'pid='.$pid.SEP.'f='.urlencode_feedback(AT_FEEDBACK_THREAD_SUBCRIBED));
	exit;

?>