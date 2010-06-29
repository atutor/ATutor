<?php
/************************************************************************/
/* ATutor								*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                       */
/* http://atutor.ca							*/
/*									*/
/* This program is free software. You can redistribute it and/or	*/
/* modify it under the terms of the GNU General Public License		*/
/* as published by the Free Software Foundation.			*/
/************************************************************************/
// $Id: index.php 8901 2009-11-11 19:10:19Z cindy $

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CONTENT);

require(AT_INCLUDE_PATH.'header.inc.php');


$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('total_hits' => 1, 'unique_hits' => 1, 'average_duration' => 1, 'total_duration' => 1);

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'total_hits';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'total_hits';
} else {
	// no order set
	$order = 'desc';
	$col   = 'total_hits';
}

$page_string = SEP.$order.'='.$col;

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
$num_pages = max(ceil($num_results / $results_per_page), 1);
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	
$count = (($page-1) * $results_per_page) + 1;

$offset = ($page-1)*$results_per_page;

/*create a table that lists all the content pages and the number of time they were viewed*/
$sql = "SELECT content_id, COUNT(*) AS unique_hits, SUM(counter) AS total_hits, SEC_TO_TIME(SUM(duration)/SUM(counter)) AS average_duration, SEC_TO_TIME(SUM(duration)) AS total_duration FROM ".TABLE_PREFIX."member_track WHERE course_id=$_SESSION[course_id] GROUP BY content_id ORDER BY $col $order LIMIT $offset, $results_per_page";
$result = mysql_query($sql, $db);

?>
<div class="toolcontainer">
<div class="paging">
	<ul>
	<?php for ($i=1; $i<=$num_pages; $i++): ?>
		<li>
			<?php if ($i == $page) : ?>
				<a class="current" href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i.$page_string; ?>"><em><?php echo $i; ?></em></a>
			<?php else: ?>
				<a href="<?php echo $_SERVER['PHP_SELF']; ?>?p=<?php echo $i.$page_string; ?>"><?php echo $i; ?></a>
			<?php endif; ?>
		</li>
	<?php endfor; ?>
	</ul>
</div>

<table class="data" rules="cols" summary="">
<colgroup>
	<?php if ($col == 'total_hits'): ?>
		<col />
		<col class="sort" />
		<col span="4" />
	<?php elseif($col == 'unique_hits'): ?>
		<col span="2" />
		<col class="sort" />
		<col span="3" />
	<?php elseif($col == 'average_duration'): ?>
		<col span="3" />
		<col class="sort" />
		<col span="2" />
	<?php elseif($col == 'total_duration'): ?>
		<col span="4" />
		<col class="sort" />
		<col />
	<?php endif; ?>
</colgroup>
<thead>
<tr>
	<th scope="col"><?php echo _AT('page'); ?></th>
	<th scope="col"><a href="mods/_standard/tracker/tools/index.php?<?php echo $orders[$order]; ?>=total_hits"><?php echo _AT('visits');             ?></a></th>
	<th scope="col"><a href="mods/_standard/tracker/tools/index.php?<?php echo $orders[$order]; ?>=unique_hits"><?php echo _AT('unique_visits');     ?></a></th>
	<th scope="col"><a href="mods/_standard/tracker/tools/index.php?<?php echo $orders[$order]; ?>=average_duration"><?php echo _AT('avg_duration'); ?></a></th>
	<th scope="col"><a href="mods/_standard/tracker/tools/index.php?<?php echo $orders[$order]; ?>=total_duration"><?php echo _AT('duration');       ?></a></th>
	<th scope="col"><?php echo _AT('details');       ?></th>
</tr>
</thead>
<tbody>
<?php if ($row = mysql_fetch_assoc($result)): ?>
	<?php do { ?>
		<tr onmousedown="document.location='<?php echo AT_BASE_HREF; ?>mods/_standard/tracker/tools/page_student_stats.php?content_id=<?php echo $row['content_id']; ?>'" title="<?php echo _AT('details'); ?>">
			<td><?php echo $contentManager->_menu_info[$row['content_id']]['title']; ?></td>
			<td><?php echo $row['total_hits'];       ?></td>
			<td><?php echo $row['unique_hits'];      ?></td>
			<td><?php echo $row['average_duration']; ?></td>
			<td><?php echo $row['total_duration'];   ?></td>
			<td><a href="mods/_standard/tracker/tools/page_student_stats.php?content_id=<?php echo $row['content_id']; ?>"><?php echo _AT('details'); ?></a></td>
		</tr>
	<?php } while ($row = mysql_fetch_assoc($result)); ?>
<?php else: ?>
	<tr>
		<td colspan="6"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>
</div>
<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>