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
// $Id: member_stats.php 2734 2004-12-08 20:21:10Z joel $

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_ADMIN);

/* Getting content id from page that reffered */
$cid = intval($_GET['content_id']);

require(AT_INCLUDE_PATH.'header.inc.php');

	//Table displays all content pages with no. of hits by user
	echo '<table class="data static" rules="cols" summary="">';
	echo '<thead>';
	echo '<tr>';
		echo '<th scope="col">';
			echo _AT('login');
		echo '</th>';
		echo '<th scope="col">';
			echo _AT('visits');
		echo '</th>';
	echo '<th scope="col">';
		echo _AT('avg_duration');
	echo '</th>';
	echo '<th scope="col">';
		echo _AT('duration_sec');
	echo '</th>';
		echo '<th scope="col">';
			echo _AT('last_accessed');
		echo '</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';


	/* go through member track list looking for all users that have visited that content page*/
	$sql = "SELECT MT.counter, MT.content_id, MT.last_accessed, MT.member_id, C.title,
			SEC_TO_TIME(MT.duration) AS total, SEC_TO_TIME(MT.duration/counter) AS average
			FROM ".TABLE_PREFIX."content C LEFT JOIN ".TABLE_PREFIX."member_track MT
			ON MT.content_id=C.content_id 
			WHERE C.course_id=$_SESSION[course_id] AND MT.content_id=$cid
			ORDER BY counter DESC";
	$result = mysql_query($sql, $db);

	if (mysql_num_rows($result) > 0) {
		while($row = mysql_fetch_assoc($result)) {
			$sql1    = "SELECT login FROM ".TABLE_PREFIX."members WHERE member_id=$row[member_id]";
			$result1 = mysql_query($sql1, $db);
			$row1     = mysql_fetch_assoc($result1);

			echo '<tr>';
				echo '<td>' . AT_print($row1['login'], 'members_name') . '</td>';
				echo '<td>' . intval($row['counter']) . '</td>';

				if ($row['average'] == '')
					$row['average'] = '00:00:00';

				if ($row['total'] == '')
					$row['total'] = '00:00:00';

				echo '<td>' . ($row['average']) . '</td>';
				echo '<td>' . ($row['total']) . '</td>';

				if ($row['last_accessed'] == '') {
					echo '<td>' . _AT('n_a') .'</td>';
				} else {
					echo '<td>' . AT_date(_AT('forum_date_format'), $row['last_accessed'], AT_DATE_MYSQL_DATETIME) . '</td>';
				}
			echo '</tr>';
		}
	} else {
		echo '<tr><td>' . _AT('tracker_data_empty') . '</td></tr>';
		echo '</tbody>';
	}
	echo '</table>';



require(AT_INCLUDE_PATH.'footer.inc.php');

?>