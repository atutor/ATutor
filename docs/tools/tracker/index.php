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
authenticate(AT_PRIV_CONTENT);

require(AT_INCLUDE_PATH.'header.inc.php');

//get sorting order from user input
if ($_GET['col'] && $_GET['order']) {
	$col   = $addslashes($_GET['col']);
	$order = $addslashes($_GET['order']);
} else {
	//set default sorting order
	$col   = 'total_hits';
	$order = 'desc';
}

if (!isset($_GET['cnt'])) {
	$sql	= "SELECT COUNT(DISTINCT content_id) AS cnt FROM ".TABLE_PREFIX."member_track WHERE course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	$cnt = $row['cnt'];
} else {
	$cnt = intval($_GET['cnt']);
}

$num_results = $cnt;
$results_per_page = 15;
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
		echo '<a href="'.$_SERVER['PHP_SELF'].'?p='.$i.SEP.'col='.$col.SEP.'order='.$order.SEP.'cnt='.$cnt.'">'.$i.'</a>';
	}
	echo ' | ';
}
$offset = ($page-1)*$results_per_page;

/*create a table that lists all the content pages and the number of time they were viewed*/
$sql = "SELECT content_id, COUNT(*) AS unique_hits, SUM(counter) AS total_hits, SEC_TO_TIME(SUM(duration)/SUM(counter)) AS average_duration, SEC_TO_TIME(SUM(duration)) AS total_duration FROM ".TABLE_PREFIX."member_track WHERE course_id=$_SESSION[course_id] GROUP BY content_id ORDER BY $col $order LIMIT $offset, $results_per_page";
$result = mysql_query($sql, $db);

?>
<table class="data" rules="cols" summary="">
<thead>
<tr>
	<th scope="col"><?php echo _AT('page'); ?></th>
	
	<th scope="col"><?php echo _AT('visits'). ' <a href="' . $_SERVER['PHP_SELF'] . '?col=total_hits' . SEP . 'order=asc" title="' . _AT('hits_ascending') . '"><img src="images/asc.gif" alt="' . _AT('hits_ascending') . '" border="0" height="7" width="11" /></a> <a href="' . $_SERVER['PHP_SELF'] . '?col=total_hits' . SEP . 'order=desc" title="' . _AT('hits_descending') . '"><img src="images/desc.gif" alt="' . _AT('hits_descending') . '" border="0" height="7" width="11" /></a>'; ?></th>
	
	<th scope="col"><?php echo _AT('unique_visits'). ' <a href="' . $_SERVER['PHP_SELF'] . '?col=unique_hits' . SEP . 'order=asc" title="' . _AT('hits_ascending') . '"><img src="images/asc.gif" alt="' . _AT('hits_ascending') . '" border="0" height="7" width="11" /></a> <a href="' . $_SERVER['PHP_SELF'] . '?col=unique_hits' . SEP . 'order=desc" title="' . _AT('hits_descending') . '"><img src="images/desc.gif" alt="' . _AT('hits_descending') . '" border="0" height="7" width="11" /></a>'; ?></th>

	<th scope="col"><?php echo _AT('avg_duration'). ' <a href="' . $_SERVER['PHP_SELF'] . '?col=average_duration' . SEP . 'order=asc" title="' . _AT('hits_ascending') . '"><img src="images/asc.gif" alt="' . _AT('hits_ascending') . '" border="0" height="7" width="11" /></a> <a href="' . $_SERVER['PHP_SELF'] . '?col=average_duration' . SEP . 'order=desc" title="' . _AT('hits_descending') . '"><img src="images/desc.gif" alt="' . _AT('hits_descending') . '" border="0" height="7" width="11" /></a>'; ?></th>

	<th scope="col"><?php echo _AT('duration'). ' <a href="' . $_SERVER['PHP_SELF'] . '?col=total_duration' . SEP . 'order=asc" title="' . _AT('hits_ascending') . '"><img src="images/asc.gif" alt="' . _AT('hits_ascending') . '" border="0" height="7" width="11" /></a> <a href="' . $_SERVER['PHP_SELF'] . '?col=total_duration' . SEP . 'order=desc" title="' . _AT('hits_descending') . '"><img src="images/desc.gif" alt="' . _AT('hits_descending') . '" border="0" height="7" width="11" /></a>'; ?></th>

	<th scope="col"><?php echo _AT('details');       ?></th>
</tr>
</thead>
<tbody>
<?php while ($row = mysql_fetch_assoc($result)) : ?>
	<tr onmousedown="document.location='tools/tracker/page_student_stats.php?content_id=<?php echo $row['content_id']; ?>'" title="<?php echo _AT('details'); ?>">
		<td><?php echo $contentManager->_menu_info[$row['content_id']]['title']; ?></td>
		<td><?php echo $row['total_hits'];       ?></td>
		<td><?php echo $row['unique_hits'];      ?></td>
		<td><?php echo $row['average_duration']; ?></td>
		<td><?php echo $row['total_duration'];   ?></td>
		<td><a href="tools/tracker/page_student_stats.php?content_id=<?php echo $row['content_id']; ?>"><?php echo _AT('details'); ?></a></td>
	</tr>
	<?php endwhile; ?>
</tbody>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>