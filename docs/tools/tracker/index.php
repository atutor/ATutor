<?php
/************************************************************************/
/* ATutor								*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto		*/
/* http://atutor.ca							*/
/*									*/
/* This program is free software. You can redistribute it and/or	*/
/* modify it under the terms of the GNU General Public License		*/
/* as published by the Free Software Foundation.			*/
/************************************************************************/
// $Id: page_stats.php 2734 2004-12-08 20:21:10Z joel $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_ADMIN);

$_section[0][0] = _AT('tools');
$_section[0][1] = 'tools/';

require(AT_INCLUDE_PATH.'header.inc.php');

//get sorting order from user input
if ($_GET['col'] && $_GET['order']) {
	$col   = $addslashes($_GET['col']);
	$order = $addslashes($_GET['order']);
}

//set default sorting order
else {
	$col   = "content_id";
	$order = "asc";
}

$sql	= "SELECT COUNT(content_id) FROM ".TABLE_PREFIX."content WHERE course_id=$_SESSION[course_id]";
$result = mysql_query($sql, $db);

if (($row = mysql_fetch_array($result))==0) {
	echo '<tr><td colspan="7" class="row1">'._AT('tracker_data_empty').' <strong>'.$_GET['L'].'</strong></td></tr>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

	$num_results = $row[0];
	$results_per_page = 10;
	$num_pages = ceil($num_results / $results_per_page);
	$page = intval($_GET['p']);
	if (!$page) {
		$page = 1;
	}	
	$count = (($page-1) * $results_per_page) + 1;

	for ($i=1; $i<=$num_pages; $i++) {
		if ($i == 1) {
			echo _AT('page').': | ';
		}
		if ($i == $page) {
			echo '<strong>'.$i.'</strong>';
		} else {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?p='.$i.'#list">'.$i.'</a>';
		}
		echo ' | ';
	}
$offset = ($page-1)*$results_per_page;

/*create a table that lists all the content pages and the number of time they were viewed*/
$sql = "SELECT C.content_id, C.title, COUNT(DISTINCT member_id) AS unique_hits
		FROM ".TABLE_PREFIX."content C LEFT JOIN ".TABLE_PREFIX."member_track MT
		USING (content_id)
		WHERE C.course_id=$_SESSION[course_id]
		GROUP BY content_id
		ORDER BY $col $order
		LIMIT $offset, $results_per_page";
$result = mysql_query($sql, $db);

echo '<table class="data static" rules="cols" summary="">';
echo '<thead>';
echo '<tr>';
	echo '<th scope="col">';
		echo _AT('page');
		echo '<a href="' . $_SERVER['PHP_SELF'] . '?col=title' . SEP . 'order=asc" title="' . _AT('title_ascending') . '"><img src="images/asc.gif" alt="' . _AT('title_ascending') . '" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a>';

		echo '<a href="' . $_SERVER['PHP_SELF'] . '?col=title' . SEP . 'order=desc" title="' . _AT('title_descending') . '"><img src="images/desc.gif" alt="' . _AT('title_descending') . '" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a>';
	echo '</th>';

	echo '<th scope="col">';
		echo _AT('visits');
		echo '<a href="' . $_SERVER['PHP_SELF'] . '?col=counter' . SEP . 'order=asc" title="' . _AT('hits_ascending') . '"><img src="images/asc.gif" alt="' . _AT('hits_ascending') . '" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a>';

		echo '<a href="' . $_SERVER['PHP_SELF'] . '?col=counter' . SEP . 'order=desc" title="' . _AT('hits_descending') . '"><img src="images/desc.gif" alt="' . _AT('hits_descending') . '" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a>';
	echo '</th>';
	echo '<th scope="col">';
		echo _AT('unique_visits');
	echo '</th>';
	echo '<th scope="col">';
		echo _AT('avg_duration');
	echo '</th>';
	echo '<th scope="col">';
		echo _AT('duration');
	echo '</th>';
	echo '<th scope="col">';
		echo _AT('details');
	echo '</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

if (mysql_num_rows($result) > 0) {
	while ($row = mysql_fetch_assoc($result)) {
		$sql2    = "SELECT SUM(counter) AS hits, SEC_TO_TIME(SUM(duration)) AS total, SEC_TO_TIME(SUM(duration)/SUM(counter)) AS average
					FROM ".TABLE_PREFIX."member_track WHERE content_id=$row[content_id]";
		$result2 = mysql_query($sql2, $db);
		$row2    = mysql_fetch_assoc($result2);

		if ($row2['average'] == '')
			$row2['average'] = _AT('na');

		if ($row2['total'] == '') {
			$row2['total'] = _AT('na');
			$data_text = _AT('na');
		} else {
			$data_text = '<a href=tools/tracker/page_student_stats.php?content_id='.$row['content_id']. '>' . _AT('raw_data') . '</a>';
		}

		echo '<tr>';
			echo '<td><a href='.$_base_href.'content.php?cid='.$row['content_id']. '>' . AT_print($row['title'], 'content.title') . '</a></td>';
			echo '<td>' . intval($row2['hits']) . '</td>';
			echo '<td>' . intval($row['unique_hits']) . '</td>';
			echo '<td>' . ($row2['average']) . '</td>';
			echo '<td>' . ($row2['total']) . '</td>';
			echo '<td>' . $data_text . '</td>';
		echo '</tr>';
	} //end while

	echo '</tbody>';
} else {
	echo '<tr><td>' . _AT('tracker_data_empty') . '</td></tr>';
	echo '</tbody>';
}

echo '</table>';

require(AT_INCLUDE_PATH.'footer.inc.php');

?>