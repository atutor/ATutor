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
// $Id$

// NOTE: this script should not be altered. its use will soon be deprecated.

if (!defined('AT_INCLUDE_PATH')) { exit; }

//how many content pages are in this course
$sql25 = "SELECT content_id from ".TABLE_PREFIX."content where course_id = $_SESSION[course_id]";
$result29 = mysql_query($sql25, $db);
$num_rows_total = @mysql_num_rows($result29);

//get the title for each content_id
$sql7 = "select
			C.title,
			C.content_id

		from
			".TABLE_PREFIX."content C
		where
			course_id='$_SESSION[course_id]'";

	if(!$result7 = mysql_query($sql7, $db)){
		echo "query failed";
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	$title_refs = array();
	while ($row= mysql_fetch_array($result7)) {
		$title_refs[$row['content_id']] = $row['title'];

	}

$sql2 = "SELECT * FROM ".TABLE_PREFIX."g_click_data WHERE member_id = $_GET[member_id] AND course_id = $_SESSION[course_id]";
$result28 = mysql_query($sql2, $db);
echo '<br /><h3>'._AT('tracker_summary_read', $this_user[$_GET['member_id']]).'</h3>';
echo '<table class="bodyline" width="90%" align="center" cellpadding="0" cellspacing="1"><tr><th scope="col"> '._AT('page').' </th><th scope="col"> '._AT('visits').' </th><th scope="col"> '._AT('duration_sec').'</th></tr>';
while ($row2= @mysql_fetch_assoc($result28)){
	$duration[$row2['to_cid']] = ($duration[$row2['to_cid']] + $row2['duration']);
	$visits[$row2['to_cid']] = ($visits[$row2['to_cid']] +1);
}

$sql= "SELECT DISTINCT to_cid FROM ".TABLE_PREFIX."g_click_data WHERE member_id = $_GET[member_id] AND course_id = $_SESSION[course_id]";
$result27 = mysql_query($sql, $db);

while ($row= @mysql_fetch_array($result27)) {
	if($row['to_cid']){
		$num_rows_read = ($num_rows_read +1);
		echo '<tr><td class="row1">'.$title_refs[$row['to_cid']].'</td><td align="center" class="row1"> '.$visits[$row['to_cid']].' </td><td align="center" class="row1"> '.number_format($duration[$row['to_cid']]).'</td></tr>';
		echo '<tr><td height="1" class="row2" colspan="3"></td></tr>';
	}

}
echo '<tr><td height="1" class="row2" colspan="3"></td></tr>';
echo '<tr><td align="left" class="row1">'._AT('tracker_pages_total', $num_rows_total, $num_rows_read).'</td><td colspan="2" align="right" class="row1">'._AT('tracker_percent_read',@number_format((($num_rows_read/$num_rows_total)*100),1)).'%</td></tr>';
echo '</table>';
?>