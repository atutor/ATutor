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
// $Id: member_stats.php 2734 2004-12-08 20:21:10Z joel $

define('AT_INCLUDE_PATH', './include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'header.inc.php');

if ($_GET['col'] && $_GET['order']) {
	$col   = $addslashes($_GET['col']);
	$order = $addslashes($_GET['order']);
} else {
	//set default sorting order
	$col   = 'total_hits';
	$order = 'desc';
}
?>

<table class="data static" rules="cols" summary="">
<thead>
<tr>
	<th scope="col"><?php echo _AT('page'); ?></th>
	<th scope="col"><?php echo _AT('visits'); ?></th>
	<th scope="col"><?php echo _AT('duration'); ?></th>
	<th scope="col"><?php echo _AT('last_accessed'); ?></th>
</tr>
</thead>
<tbody>
<?php
	$sql = "SELECT MT.counter, C.content_id, MT.last_accessed, SEC_TO_TIME(MT.duration) AS total, C.title 
			FROM ".TABLE_PREFIX."content C LEFT JOIN ".TABLE_PREFIX."member_track MT
			ON MT.content_id=C.content_id AND MT.member_id=$_SESSION[member_id]
			WHERE C.course_id=$_SESSION[course_id] ORDER BY content_id ASC";

$sql = "SELECT content_id, COUNT(*) AS unique_hits, SUM(counter) AS total_hits, SEC_TO_TIME(SUM(duration)/SUM(counter)) AS average_duration, SEC_TO_TIME(SUM(duration)) AS total_duration, last_accessed FROM ".TABLE_PREFIX."member_track WHERE course_id=$_SESSION[course_id] AND member_id=$_SESSION[member_id] GROUP BY content_id ORDER BY $col $order";

	$result = mysql_query($sql, $db);

	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)) {
			if ($row['total'] == '')
				$row['total'] = _AT('na');

			echo '<tr>';
				echo '<td><a href='.$_base_href.'content.php?cid='.$row['content_id']. '>' . AT_print($row['title'], 'content.title') . '</a></td>';
				echo '<td>' . $row['total_hits'] . '</td>';
				echo '<td>' . $row['total_duration'] . '</td>';
				if ($row['last_accessed'] == '') {
					echo '<td>' . _AT('na') . '</td>';
				} else {
					echo '<td>' . AT_date(_AT('forum_date_format'), $row['last_accessed'], AT_DATE_MYSQL_DATETIME) . '</td>';
				}
			echo '</tr>';
		} //end while

		echo '</tbody>';

	} else {
		echo '<tr><td>' . _AT('tracker_data_empty') . '</td></tr>';
		echo '</tbody>';
	}
	?>
</tbody>
</table>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>