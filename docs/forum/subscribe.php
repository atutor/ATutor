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

$sql = "SELECT subject from ".TABLE_PREFIX."forums_threads WHERE post_id=".$pid;
$result = mysql_query($sql, $db);
while($row = mysql_fetch_array($result)){
	$thread_name = $row['subject'];
}

if ($_GET['us']) {
	$sql	= "DELETE FROM ".TABLE_PREFIX."forums_thread_subscriptions WHERE post_id=$pid AND member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);

} else {
	$sql	= "INSERT INTO ".TABLE_PREFIX."forums_thread_subscriptions VALUES ($pid, $_SESSION[member_id])";
	$result = mysql_query($sql, $db);
}

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

if($_REQUEST['t']){
	$this_pid = 'index.php?fid='.$fid;
}else{
	$this_pid = 'view.php?fid='.$fid.SEP.'pid='.$pid;
}

if ($_GET['us'] == '1') {
	$msg->addFeedback(array('THREAD_UNSUBSCRIBED', $thread_name));
	header('Location: '.$_base_href.'forum/'.$this_pid);
	exit;
}
/* else: */
	$msg->addFeedback(array('THREAD_SUBSCRIBED', $thread_name ));
	header('Location: '.$_base_href.'forum/'.$this_pid);
	exit;

?>
