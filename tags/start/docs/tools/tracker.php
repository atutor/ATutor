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

$_include_path = '../include/';
require($_include_path.'vitals.inc.php');

$_section[0][0] = _AT('tools');
$member_id=$_SESSION['member_id'];
require($_include_path.'header.inc.php');
/* It's still experimental, but that doesn't have to be advertised */
//$warnings[]=AT_WARNING_EXPERIMENTAL11;
print_warnings($warnings);

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
	echo '<a href="tools/?g=11"><img src="images/icons/default/square-large-tools.gif" class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
}
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
	echo ' <a href="tools/?g=11">'._AT('tools').'</a>';
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
		if($_SESSION['is_admin']){
			$infos[]=AT_INFOS_TRACKING_OFFIN;
		}else{
			$infos[]=AT_INFOS_TRACKING_OFFST;
		}
	print_infos(AT_INFOS_TRACKING_OFFIN);
	require($_include_path.'footer.inc.php');
	exit;
	}
}
if($_GET['coverage'] == "raw"){
	echo '&nbsp;&nbsp;<a href="'.$PHP_SELF.'">Show summary tracking</a><br /><br />';
}else{
	echo '&nbsp;&nbsp;<a href="'.$PHP_SELF.'?coverage=raw">Show raw tracking</a><br /><br />';
}

if($_SESSION['is_admin']) {
	print_infos(AT_INFOS_TRACKING_NO_INST1);
} else if ($_GET['coverage'] == "raw") {
	require($_include_path.'lib/tracker.inc.php');
} else{
	require($_include_path.'lib/tracker2.inc.php');
}
//echo array_values($this_data);

	require($_include_path.'footer.inc.php');
?>