<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

$_section[0][0] = _AT('tools');
$member_id=$_SESSION['member_id'];
require(AT_INCLUDE_PATH.'header.inc.php');

global $savant;
$msg =& new Message($savant);

//get names for member_ids
$sql14 = "select member_id, login, first_name, last_name from ".TABLE_PREFIX."members";
$result14=mysql_query($sql14, $db);
while($row=mysql_fetch_array($result14)){
	if($row['first_name'] && $row['last_name']){
		$this_user[$row['member_id']]= $row['first_name'].' '. $row['last_name'];
	}else{
		$this_user[$row['member_id']]= $row['login'];
	}
}

/////////////////////////////
// Top of the page

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<a href="tools/index.php?g=11"><img src="images/icons/default/square-large-tools.gif" class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/index.php?g=11">'._AT('tools').'</a>';
}
echo '</h2>';
echo '<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) { 
	echo '&nbsp;<img src="images/icons/default/course-tracker-large.gif" class="menuimageh3" width="42" vspace="2" height="38" alt="" /> ';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo _AT('my_tracker');
}
echo '</h3>';

//see if tracking is turned on
$sql="SELECT tracking FROM ".TABLE_PREFIX."courses where course_id=$_SESSION[course_id]";
$result=mysql_query($sql, $db);
while($row= mysql_fetch_array($result)){
	if($row['tracking']== "off"){
		if(authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)){
			$msg->printInfos('TRACKING_OFFIN');
		} else {
			$msg->printInfos('TRACKING_OFFST');
		}
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
	}
}
if ($_GET['coverage'] == 'raw'){
	echo '&nbsp;&nbsp;<a href="'.$_SERVER['PHP_SELF'].'">'._AT('show_summary_tracking').'</a><br /><br />';
} else {
	echo '&nbsp;&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?coverage=raw">'._AT('show_raw_tracking').'</a><br /><br />';
}

if (authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
	$msg->printInfos('TRACKING_NO_INST1');
} else if ($_GET['coverage'] == 'raw') {
	require(AT_INCLUDE_PATH.'lib/tracker.inc.php');
} else{
	require(AT_INCLUDE_PATH.'lib/tracker2.inc.php');
}
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>