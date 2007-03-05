<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2007 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', './include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'header.inc.php');
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
	$sql = "SELECT content_id, COUNT(*) AS unique_hits, SUM(counter) AS total_hits, SEC_TO_TIME(SUM(duration)/SUM(counter)) AS average_duration, SEC_TO_TIME(SUM(duration)) AS total_duration, last_accessed FROM ".TABLE_PREFIX."member_track WHERE course_id=$_SESSION[course_id] AND member_id=$_SESSION[member_id] GROUP BY content_id ORDER BY total_hits DESC";

	$result = mysql_query($sql, $db);

	if (mysql_num_rows($result) > 0) {
		while ($row = mysql_fetch_assoc($result)) {
			if ($row['total'] == '') {
				$row['total'] = _AT('na');
			}

			echo '<tr>';
			echo '<td><a href='.AT_BASE_HREF.'content.php?cid='.$row['content_id']. '>' . $contentManager->_menu_info[$row['content_id']]['title'] . '</a></td>';
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
		echo '<tr><td colspan="4">' . _AT('none_found') . '</td></tr>';
		echo '</tbody>';
	}
	?>
</tbody>
</table>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>