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

if (isset($_GET['remove'])) {
	$sql = 'DELETE FROM '.TABLE_PREFIX.'instructor_approvals WHERE member_id='.intval($_GET['remove']);
	$result = mysql_query($sql, $db);
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

echo '<h3>'._AT('home').'</h3>';

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

$msg->printAll();
/*
if (isset($_GET['f'])) { 
	$f = intval($_GET['f']);
	if ($f <= 0) {
		/* it's probably an array *
		$f = unserialize(urldecode($_GET['f']));
	}
	print_feedback($f);
}
if (isset($errors)) { print_errors($errors); }
if(isset($warnings)){ print_warnings($warnings); }
*/

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
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="95%" align="center">
<tr><th height="1" class="cyan" colspan="2"><?php echo _AT('general_statistics'); ?></th></tr>
<tr>
	<td class="row1" align="right" width="10%"><small><?php echo _AT('instructors'); ?>:</small></td>
	<td class="row1"><small><?php echo $total_instructors; ?></small></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><small><?php echo _AT('students'); ?>:</small></td>
	<td class="row1"><small><?php echo $total_students; ?></small></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1" align="right"><small><?php echo _AT('courses'); ?>:</small></td>
	<td class="row1"><small><?php echo $total_courses; ?></small></td>
</tr>
</table>
<?php


$sql	= "SELECT M.login, M.member_id, A.* FROM ".TABLE_PREFIX."members M, ".TABLE_PREFIX."instructor_approvals A WHERE A.member_id=M.member_id ORDER BY M.login";
$result = mysql_query($sql, $db);
$num_pending = mysql_num_rows($result);
?>
<br />
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" width="95%" align="center">
<tr><th height="1" class="cyan" colspan="5"><?php echo _AT('instructor_requests'); ?></th></tr>
<tr>
	<th scope="col" class="cat"><small><?php echo _AT('username'); ?></small></th>
	<th scope="col" class="cat"><small><?php echo _AT('notes'); ?></small></th>
	<th scope="col" class="cat"><small><?php echo _AT('request_date'); ?></small></th>
	<th scope="col" class="cat"><small><?php echo _AT('remove'); ?></small></th>
	<th scope="col" class="cat"><small><?php echo _AT('approve'); ?></small></th>
</tr>
<?php
	if ($row = mysql_fetch_assoc($result)) {
		do {
			$counter++;
			echo '<tr>';
			echo '<td class="row1"><small><a href="admin/profile.php?member_id='.$row['member_id'].'">'.AT_print($row['login'], 'members.login').'</a></small></td>';
			
			echo '<td class="row1"><small>'.AT_print($row['notes'], 'instructor_approvals.notes').'</small></td>';
			echo '<td class="row1"><small>'.substr($row['request_date'], 0, -3).'</small></td>';
			echo '<td class="row1"><small><a href="admin/admin_deny.php?id='.$row['member_id'].'">'._AT('remove').'</a></small></td>';
			echo '<td class="row1"><small><a href="admin/admin_edit.php?id='.$row['member_id'].'">'._AT('approve').'</a></small></td>';

			echo '</tr>';
			if ($counter < $num_pending) {
				echo '<tr><td height="1" class="row2" colspan="5"></td></tr>';
			}
		} while ($row = mysql_fetch_assoc($result));
	} else {
		echo '<tr>
			<td class="row1" colspan="5"><small><em>'._AT('none').'</em></small></td>
		</tr>';
	}
?>

</table>
<?php
require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>