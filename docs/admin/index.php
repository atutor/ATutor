<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate();

if (isset($_GET['remove'])) {
	$sql = 'DELETE FROM '.TABLE_PREFIX.'instructor_approvals WHERE member_id='.intval($_GET['remove']);
	$result = mysql_query($sql, $db);
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

echo '<p>ATutor '._AT('version').': <strong>'.VERSION.'</strong> - <a href="http://atutor.ca/check_atutor_version.php?v='.urlencode(VERSION).'">'._AT('check_latest_version').'</a></p>';

echo '<h3>'._AT('fix_content_ordering').'</h3>';
echo '<p>'._AT('fix_content_ordering_text').'</p>';

if (AT_DEVEL_TRANSLATE == 1) { 
	$msg->addWarning('TRANSLATE_ON');	
}


$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."members GROUP BY status ORDER BY status";
$result = mysql_query($sql, $db);
$students_row = mysql_fetch_assoc($result);
$instructor_row = mysql_fetch_assoc($result);

$sql	= "SELECT COUNT(*) FROM ".TABLE_PREFIX."courses";
$result = mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
$total_courses = $row[0] ? $row[0] : 0;

?>
<h3><?php echo _AT('general_statistics'); ?></h3>
	<p><?php echo _AT('instructors'); ?>: <?php echo $instructor_row['cnt']; ?></p>
	<p><?php echo _AT('students'); ?>: <?php echo $students_row['cnt']; ?></p>
	<p><?php echo _AT('courses'); ?>: <?php echo $total_courses; ?></p>

<?php
$sql	= "SELECT M.login, M.member_id, A.* FROM ".TABLE_PREFIX."members M, ".TABLE_PREFIX."instructor_approvals A WHERE A.member_id=M.member_id ORDER BY M.login";
$result = mysql_query($sql, $db);
$num_pending = mysql_num_rows($result);
?>
<br />
<h3><?php echo _AT('instructor_requests'); ?></h3>
<table class="data static" summary="" rules="cols">
<thead>
<tr>
	<th scope="col"><?php echo _AT('username');     ?></th>
	<th scope="col"><?php echo _AT('notes');        ?></th>
	<th scope="col"><?php echo _AT('request_date'); ?></th>
	<th scope="col"><?php echo _AT('remove');       ?></th>
	<th scope="col"><?php echo _AT('approve');      ?></th>
</tr>
</thead>
<tbody>
<?php
	if ($row = mysql_fetch_assoc($result)) {
		do {
			$counter++;
			echo '<tr>';
			echo '<td><a href="admin/profile.php?member_id='.$row['member_id'].'">'.AT_print($row['login'], 'members.login').'</a></td>';
			
			echo '<td>'.AT_print($row['notes'], 'instructor_approvals.notes').'</td>';
			echo '<td>'.substr($row['request_date'], 0, -3).'</td>';
			echo '<td><a href="admin/admin_deny.php?id='.$row['member_id'].'">'._AT('remove').'</a></td>';
			echo '<td><a href="admin/admin_edit.php?id='.$row['member_id'].SEP.'from_approve=1">'._AT('approve').'</a></td>';

			echo '</tr>';
		} while ($row = mysql_fetch_assoc($result));
	} else {
		echo '<tr>
			<td colspan="5"><em>'._AT('none').'</em></td>
		</tr>';
	}
?>
</tbody>
</table>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>