<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$page = 'home';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }


if (AT_DEVEL_TRANSLATE == 1) { 
	$msg->addWarning('TRANSLATE_ON');	
}

if (isset($_GET['remove'])) {
	$sql = 'DELETE FROM '.TABLE_PREFIX.'instructor_approvals WHERE member_id='.intval($_GET['remove']);
	$result = mysql_query($sql, $db);
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

$msg->printAll();

$sql	= "SELECT COUNT(*) FROM ".TABLE_PREFIX."members WHERE status=1";
$result = mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
$total_instructors = $row[0] ? $row[0] : 0;
unset($row);

$sql	= "SELECT COUNT(*) FROM ".TABLE_PREFIX."members WHERE status=0";
$result = mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
$total_students = $row[0] ? $row[0] : 0;
unset($row);

$sql	= "SELECT COUNT(*) FROM ".TABLE_PREFIX."courses";
$result = mysql_query($sql, $db);
$row	= mysql_fetch_array($result);
$total_courses = $row[0] ? $row[0] : 0;

?>
<table class="data static" summary="" rules="cols">
<thead>
<tr>
	<th height="1" colspan="2"><?php echo _AT('general_statistics'); ?></th>
</tr>
</thead>
<tbody>
<tr>
	<td align="right" width="10%"><?php echo _AT('instructors'); ?>:</td>
	<td><?php echo $total_instructors; ?></td>
</tr>
<tr>
	<td align="right"><?php echo _AT('students'); ?>:</td>
	<td><?php echo $total_students; ?></td>
</tr>
<tr>
	<td align="right"><?php echo _AT('courses'); ?>:</td>
	<td><?php echo $total_courses; ?></td>
</tr>
</tbody>
</table>
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
<?php
require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>