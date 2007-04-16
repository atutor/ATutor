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
// $Id$

define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_CONTENT);

/* Getting content id from page that reffered */
$content_id = intval($_GET['content_id']);

$_pages['tools/tracker/page_student_stats.php']['title'] = $contentManager->_menu_info[$content_id]['title'];
$_pages['tools/tracker/page_student_stats.php']['parent'] = 'tools/tracker/index.php';

require(AT_INCLUDE_PATH.'header.inc.php');

$sql = "SELECT counter, content_id, member_id, SEC_TO_TIME(duration) AS total, SEC_TO_TIME(duration/counter) AS average FROM ".TABLE_PREFIX."member_track WHERE course_id=$_SESSION[course_id] AND content_id=$content_id ORDER BY total DESC";
$result = mysql_query($sql, $db);

?>
<table class="data" rules="cols" summary="">
<thead>
<tr>
	<th scope="col"><?php echo _AT('login_name'); ?></th>
	<th scope="col"><?php echo _AT('visits'); ?></th>
	<th scope="col"><?php echo _AT('avg_duration'); ?></th>
	<th scope="col"><?php echo _AT('duration'); ?></th>
</tr>
</thead>
<tbody>
<?php if ($row = mysql_fetch_assoc($result)) : ?>
	<?php do { ?>
	<tr onmousedown="document.location='<?php echo AT_BASE_HREF; ?>tools/tracker/student_usage.php?id=<?php echo $row['member_id']; ?>'" title="<?php echo _AT('member_stats'); ?>">
		<td><a href="<?php echo AT_BASE_HREF; ?>tools/tracker/student_usage.php?id=<?php echo $row['member_id']; ?>"><?php echo get_display_name($row['member_id']); ?></a></td>
		<td><?php echo $row['counter']; ?></td>
		<td><?php echo $row['average']; ?></td>
		<td><?php echo $row['total']; ?></td>
	</tr>
	<?php } while ($row = mysql_fetch_assoc($result)); ?>
<?php else: ?>
	<tr>
		<td colspan="4"><?php echo _AT('none_found'); ?></td>
	</tr>
<?php endif; ?>
</tbody>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>