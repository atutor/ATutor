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
admin_authenticate(AT_ADMIN_PRIV_USERS);


$thismember_id = intval($_GET['id']);

$sql		 = "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id=$thismember_id";
$result_info = mysql_query($sql, $db);

if (!$result) {
	$msg->addError('USER_NOT_FOUND');
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printAll();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

//else
$row_info = mysql_fetch_assoc($result_info);

require(AT_INCLUDE_PATH.'header.inc.php'); 

?>

<div class="input-form">
	<div class="row">
		<?php echo _AT('username'); ?><br />
		<?php echo AT_print($row_info['login'], 'members.login'); ?>
	</div>

	<div class="row">
		<?php echo _AT('member_id'); ?><br />
		<?php echo $row_info['member_id']; ?>
	</div>

	<div class="row">
		<?php echo _AT('password'); ?><br />
		<?php echo AT_print($row_info['password'], 'members.password'); ?>
	</div>

	<div class="row">
		<?php echo _AT('email_address'); ?><br />
		<a href="mailto:<?php echo $row_info['email']; ?>"><?php echo AT_print($row_info['email'], 'members.email'); ?></a>
	</div>

	<div class="row">
		<?php echo _AT('web_site'); ?><br />
		<?php
			if ($row_info['website']) {
				echo '<a href="'.$row_info['website'].'">'.AT_print($row_info['website'], 'members.website').'</a>';
			}
		?>
	</div>
	<div class="row">
		<?php echo _AT('first_name'); ?><br />
		<?php echo AT_print($row_info['first_name'],'members.first_name'); ?>
	</div>
	<div class="row">
		<?php echo _AT('last_name'); ?><br />
		<?php echo AT_print($row_info['last_name'],'members.last_name'); ?>
	</div>

	<div class="row">
		<?php echo _AT('gender'); ?><br />
		<?php echo AT_print($row_info['gender'],'members.gender'); ?>
	</div>
	
	<div class="row">
		<?php echo _AT('street_address'); ?><br />
		<?php echo AT_print($row_info['address'],'members.address'); ?>
	</div>
	
	<div class="row">
		<?php echo _AT('city'); ?><br />
		<?php echo AT_print($row_info['city'],'members.city'); ?>
	</div>

	<div class="row">
		<?php echo _AT('province'); ?><br />
		<?php echo AT_print($row_info['province'],'members.province'); ?>
	</div>

	<div class="row">
		<?php echo _AT('postal_code'); ?><br />
		<?php echo AT_print($row_info['postal'], 'members.postal'); ?>
	</div>

	<div class="row">
		<?php echo _AT('country'); ?><br />
		<?php echo AT_print($row_info['country'],'members.country'); ?>
	</div>
	<div class="row">
		<?php echo _AT('phone'); ?><br />
		<?php echo AT_print($row_info['phone'],'members.phone'); ?>
	</div>

	<div class="row">
		<?php  echo _AT('status'); ?><br />
		<a href="admin/admin_edit.php?id=<?php echo $row_info['member_id']; ?>"><?php
		if ($row_info['status']) {
			echo _AT('instructor').'</a>,  <a href="admin/courses.php?id='.$row_info['member_id'].'">'. _AT('view_courses_taught') .'</a>';
		} else {
			echo _AT('student1').'</a>';
		} ?>
	</div>

	<div class="row">
		<?php  echo _AT('created_date'); ?><br />
		<?php echo $row_info['creation_date']; ?>
	</div>
</div>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>