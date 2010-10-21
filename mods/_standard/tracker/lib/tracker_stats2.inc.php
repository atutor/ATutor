<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
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

echo '<table class="data static" rules="cols" summary="">';
echo '<thead>';
echo '<tr>';
echo '<th>' . _AT('page')         . '</th>';
echo '<th>' . _AT('visits')       . '</th>';
echo '<th>' . _AT('duration_sec') . '</th>';
echo '</tr>';
echo '</thead>';

while ($row2= @mysql_fetch_assoc($result28)){
	$duration[$row2['to_cid']] = ($duration[$row2['to_cid']] + $row2['duration']);
	$visits[$row2['to_cid']] = ($visits[$row2['to_cid']] +1);
}

$sql= "SELECT DISTINCT to_cid FROM ".TABLE_PREFIX."g_click_data WHERE member_id = $_GET[member_id] AND course_id = $_SESSION[course_id]";
$result27 = mysql_query($sql, $db);

echo '<tbody>';
while ($row= @mysql_fetch_array($result27)) {
	if($row['to_cid']){
		$num_rows_read = ($num_rows_read +1);
		echo '<tr>';
		echo '<td>' . $title_refs[$row['to_cid']] . '</td>';
		echo '<td>' . $visits[$row['to_cid']] . '</td>';
		echo '<td>' . number_format($duration[$row['to_cid']]) . '</td>';
		echo '</tr>';
	}
}

echo '<tr>';
echo '<td>' . _AT('tracker_pages_total', $num_rows_total, $num_rows_read).'</td>';
echo '<td>' . _AT('tracker_percent_read',@number_format((($num_rows_read/$num_rows_total)*100),1)) . '%</td>';
echo '</tr>';

echo '<tbody>';
echo '</table>';

?>