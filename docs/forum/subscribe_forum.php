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
// $Id: index.php 2526 2004-11-25 18:54:16Z greg$


define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
//authenticate(USER_CLIENT, USER_TRANS, USER_ADMIN);

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);


$fid = intval($_REQUEST['fid']);
$sql = "SELECT title FROM ".TABLE_PREFIX."forums WHERE forum_id = $fid";
$result = mysql_query($sql, $db);
while($row = mysql_fetch_row($result)){
	$forum_title = $row['0'];
}
 if ($_GET['us']) {

	$sql = "DELETE from ".TABLE_PREFIX."forums_subscriptions WHERE forum_id = $fid AND member_id = $_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	$msg->addFeedback(array(FORUM_UNSUBSCRIBED, $forum_title));
	Header('Location: list.php');
	exit;

} else {
	
	$sql = "INSERT into ".TABLE_PREFIX."forums_subscriptions VALUES($fid, '$_SESSION[member_id]')";
	mysql_query($sql, $db);

	$msg->addFeedback(array(FORUM_SUBSCRIBED,$forum_title));
	Header('Location: list.php');
	exit;
}

?>
