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

$page = 'users';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

require(AT_INCLUDE_PATH.'header.inc.php'); 

$thismember_id = intval($_GET['member_id']);

$sql	= "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id=$thismember_id";
$result	= mysql_query($sql, $db);

if (!($row = mysql_fetch_array($result))) {
	echo _AT('no_user_found');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);
$msg->printAll();
/*
if (isset($_GET['f'])) { 
	$f = intval($_GET['f']);
	if ($f <= 0) {
		/* it's probably an array 
		$f = unserialize(urldecode($_GET['f']));
	}
	print_feedback($f);
}
if (isset($errors)) { print_errors($errors); }
if(isset($warnings)){ print_warnings($warnings); }
*/
?>
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="" align="center">
<tr>
	<th colspan="2" class="cyan"><?php 
		echo _AT('profile_for').' '. AT_print($row['login'], 'members.login');
	?></th>
</tr>
<tr>
	<td class="row1"><?php echo _AT('username'); ?>:</td>
	<td class="row1"><?php echo AT_print($row['login'], 'members.login'); ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('member_id'); ?>:</td>
	<td class="row1"><?php echo $row['member_id']; ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('password'); ?>:</td>
	<td class="row1"><?php echo AT_print($row['password'], 'members.password'); ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('email_address'); ?>:</td>
	<td class="row1"><a href="mailto:<?php echo $row['email']; ?>"><?php echo AT_print($row['email'], 'members.email'); ?></a></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('web_site'); ?>:</td>
	<td class="row1"><?php
		if ($row['website']) {
			echo '<a href="'.$row['website'].'">'.AT_print($row['website'], 'members.website').'</a>';
		}
	?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('first_name'); ?>:</td>
	<td class="row1"><?php echo AT_print($row['first_name'],'members.first_name'); ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('last_name'); ?>:</td>
	<td class="row1"><?php echo AT_print($row['last_name'],'members.last_name'); ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('gender'); ?>:</td>
	<td class="row1"><?php echo AT_print($row['gender'],'members.gender'); ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('street_address'); ?>:</td>
	<td class="row1"><?php echo AT_print($row['address'],'members.address'); ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('city'); ?>:</td>
	<td class="row1"><?php echo AT_print($row['city'],'members.city'); ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('province'); ?>:</td>
	<td class="row1"><?php echo AT_print($row['province'],'members.province'); ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('postal_code'); ?>:</td>
	<td class="row1"><?php echo AT_print($row['postal'], 'members.postal'); ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('country'); ?>:</td>
	<td class="row1"><?php echo AT_print($row['country'],'members.country'); ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('phone'); ?>:</td>
	<td class="row1"><?php echo AT_print($row['phone'],'members.phone'); ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php  echo _AT('status'); ?>:</td>
	<td class="row1"><a href="admin/admin_edit.php?id=<?php echo $row['member_id']; ?>"><?php
		if ($row['status']) {
			echo _AT('instructor').'</a>,  <a href="admin/courses.php?member_id='.$row['member_id'].'">'. _AT('view_courses_taught') .'</a>';
		} else {
			echo _AT('student1').'</a>';
		}
	?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php  echo _AT('created_date'); ?></td>
	<td class="row1"><?php echo $row['creation_date']; ?></td>
</tr>
</table>
<?php
	require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>