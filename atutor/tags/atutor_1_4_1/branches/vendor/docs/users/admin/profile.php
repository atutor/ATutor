<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

$section = 'users';
$_include_path = '../../include/';
require($_include_path.'vitals.inc.php');
if (!$_SESSION['s_is_super_admin']) {
	exit;
}

require($_include_path.'admin_html/header.inc.php');

$thismember_id = intval($_GET['member_id']);

$sql	= "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id=$thismember_id";
$result	= mysql_query($sql, $db);

if (!($row = mysql_fetch_array($result))) {
	echo _AT('no_user_found');
	require($_include_path.'cc_html/footer.inc.php');
	exit;
}
?>
<table cellspacing="1" cellpadding="0" border="0" class="bodyline" summary="">
<tr>
	<th colspan="2" class="left"><?php 
		echo _AT('profile_for').' '. $row['login'];
	?></th>
</tr>
<tr>
	<td class="row1"><?php echo _AT('username'); ?>:</td>
	<td class="row1"><?php echo $row['login']; ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('member_id'); ?>:</td>
	<td class="row1"><?php echo $row['member_id']; ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('password'); ?>:</td>
	<td class="row1"><?php echo $row['password']; ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('email_address'); ?>:</td>
	<td class="row1"><a href="mailto:<?php echo $row['email']; ?>"><?php echo $row['email']; ?></a></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('web_site'); ?>:</td>
	<td class="row1"><?php
		if ($row['website']) {
			echo '<a href="'.$row['website'].'">'.$row['website'].'</a>';
		}
	?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('first_name'); ?>:</td>
	<td class="row1"><?php echo $row['first_name']; ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('last_name'); ?>:</td>
	<td class="row1"><?php echo $row['last_name']; ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('gender'); ?>:</td>
	<td class="row1"><?php echo $row['gender']; ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('street_address'); ?>:</td>
	<td class="row1"><?php echo $row['address']; ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('city'); ?>:</td>
	<td class="row1"><?php echo $row['city']; ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('province'); ?>:</td>
	<td class="row1"><?php echo $row['province']; ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('postal_code'); ?>:</td>
	<td class="row1"><?php echo $row['postal']; ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('country'); ?>:</td>
	<td class="row1"><?php echo $row['country']; ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php echo _AT('phone'); ?>:</td>
	<td class="row1"><?php echo $row['phone']; ?></td>
</tr>
<tr><td height="1" class="row2" colspan="2"></td></tr>
<tr>
	<td class="row1"><?php  echo _AT('status'); ?>:</td>
	<td class="row1"><a href="users/admin/admin_edit.php?id=<?php echo $row['member_id']; ?>"><?php
		if ($row['status']) {
			echo _AT('instructor').'</a>,  <a href="users/admin/courses.php?member_id='.$row['member_id'].'">'. _AT('view_courses_taught') .'</a>';
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
	require($_include_path.'cc_html/footer.inc.php');
?>
