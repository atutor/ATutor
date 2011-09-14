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
if (!defined('AT_INCLUDE_PATH')) { exit; }

// NOTE: this script should not be altered. its use will soon be deprecated.


//how many content pages are in this course
$sql25 = "SELECT content_id from ".TABLE_PREFIX."content where course_id = $_SESSION[course_id]";
$result29 = mysql_query($sql25, $db);
$num_rows_total = mysql_num_rows($result29);
//get the title for each content_id
$sql7 = "select
			C.title,
			C.content_id

		from
			".TABLE_PREFIX."content C
		where
			course_id='$_SESSION[course_id]'";
	if(!$result7 = mysql_query($sql7, $db)){
		echo AT_ERRORS_GENERAL;
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}
	$title_refs = array();
	while ($row= mysql_fetch_array($result7)) {
		$title_refs[$row['content_id']] = $row['title'];

	}

$sql2 = "SELECT * from ".TABLE_PREFIX."g_click_data where member_id = $_SESSION[member_id] AND course_id = $_SESSION[course_id]";
$result28 = mysql_query($sql2, $db);

echo '<br /><h3>'._AT('tracker_summary_read', $this_user[$_SESSION["member_id"]]).'</h3>';
echo '<a href="'.$_SERVER['PHP_SELF'].'#not_viewed"><img src="images/clr.gif" border="0" alt="'._AT('tracker_not_viewed').'"/></a>';

echo '<table class="data static" rules="cols" summary="">';
echo '<thead>';
echo '<tr>';

echo '<th>' . _AT('page')         . '</th>';
echo '<th>' . _AT('visits')       . '</th>';
echo '<th>' . _AT('duration_sec') . '</th>';
echo '</tr>';
echo '</thead>';


while ($row2= mysql_fetch_array($result28)){
	$duration[$row2['to_cid']] = ($duration[$row2['to_cid']] + $row2['duration']);
	$visits[$row2['to_cid']] = ($visits[$row2['to_cid']] +1);
}

$sql= "SELECT DISTINCT to_cid from ".TABLE_PREFIX."g_click_data where member_id = $_SESSION[member_id] AND course_id = $_SESSION[course_id]";
$result27 = mysql_query($sql, $db);
$viewed_page = array();


echo '<tbody>';
while ($row = mysql_fetch_array($result27)) {
	if ($row['to_cid']) {
		$viewed_pages[$row['to_cid']] = $title_refs[$row['to_cid']];
		$num_rows_read = ($num_rows_read +1);
		
		if ($title_refs[$row['to_cid']] !='') {
			echo '<tr>';
			echo '<td>';
				echo '<a href="./index.php?cid='.$row['to_cid'].'">';
				echo $title_refs[$row['to_cid']].'</a></td><td align="center" class="row1"> '.$visits[$row['to_cid']].' </td><td align="center" class="row1"> '.number_format($duration[$row['to_cid']]);
			echo '</td>';
			echo '</tr>';
		}
	}
}

if(count($viewed_pages) > 0){
	foreach($viewed_pages as $key1 => $refs1){
		$viewed_page_keys[$key1] = $key1;
	}
	foreach($title_refs as $key => $ref){
		if(!in_array($key, $viewed_page_keys)){
				$missed_pages .= ' <li><a href="./index.php?cid='.$key.SEP.'g=36">'.$ref.'</a></li>';
		}
	}
}else{
	echo '<tr><td>'._AT('tracker_none_viewed').'</td></tr>';
}

if ($num_rows_read < 1) {
	$num_rows_read = 0;
}

echo '<tr>';
echo '<td>'._AT('tracker_pages_total', $num_rows_total, $num_rows_read).'</td>';

$per_cent = 0;
if ($num_rows_total) {
	$per_cent = number_format((($num_rows_read/$num_rows_total)*100),1);

}
echo '<td>' . _AT('tracker_percent_read',$per_cent) . '%</td>';
echo '</tr>';
echo '</tbody>';
echo '</table>';

echo '<a name="not_viewed"></a>';

echo '<br /><hr /><br /><h3>'._AT('unvisited_pages').'</h3>';
echo '<div class="results">';

// show which pages have not been viewed yet
if ($missed_pages) {
	echo '<ul>';
	echo $missed_pages;
	echo '</ul>';
} else {
	echo _AT('tracking_all_page_viewed');
}

echo '</div>';
?>