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
$msg->printAll();

$thismember_id = intval($_GET['id']);

$sql	= "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id=$thismember_id";
$result	= mysql_query($sql, $db);

if (!($row = mysql_fetch_array($result))) {
	echo _AT('no_user_found');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

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

<div class="input-form">
	<div class="row">
		<?php echo _AT('username'); ?><br />
		<?php echo AT_print($row['login'], 'members.login'); ?>
	</div>

	<div class="row">
		<?php echo _AT('member_id'); ?><br />
		<?php echo $row['member_id']; ?>
	</div>

	<div class="row">
		<?php echo _AT('password'); ?><br />
		<?php echo AT_print($row['password'], 'members.password'); ?>
	</div>

	<div class="row">
		<?php echo _AT('email_address'); ?><br />
		<a href="mailto:<?php echo $row['email']; ?>"><?php echo AT_print($row['email'], 'members.email'); ?></a>
	</div>

	<div class="row">
		<?php echo _AT('web_site'); ?><br />
		<?php
			if ($row['website']) {
				echo '<a href="'.$row['website'].'">'.AT_print($row['website'], 'members.website').'</a>';
			}
		?>
	</div>
	<div class="row">
		<?php echo _AT('first_name'); ?><br />
		<?php echo AT_print($row['first_name'],'members.first_name'); ?>
	</div>
	<div class="row">
		<?php echo _AT('last_name'); ?><br />
		<?php echo AT_print($row['last_name'],'members.last_name'); ?>
	</div>

	<div class="row">
		<?php echo _AT('gender'); ?><br />
		<?php echo AT_print($row['gender'],'members.gender'); ?>
	</div>
	
	<div class="row">
		<?php echo _AT('street_address'); ?><br />
		<?php echo AT_print($row['address'],'members.address'); ?>
	</div>
	
	<div class="row">
		<?php echo _AT('city'); ?><br />
		<?php echo AT_print($row['city'],'members.city'); ?>
	</div>

	<div class="row">
		<?php echo _AT('province'); ?><br />
		<?php echo AT_print($row['province'],'members.province'); ?>
	</div>

	<div class="row">
		<?php echo _AT('postal_code'); ?><br />
		<?php echo AT_print($row['postal'], 'members.postal'); ?>
	</div>

	<div class="row">
		<?php echo _AT('country'); ?><br />
		<?php echo AT_print($row['country'],'members.country'); ?>
	</div>
	<div class="row">
		<?php echo _AT('phone'); ?><br />
		<?php echo AT_print($row['phone'],'members.phone'); ?>
	</div>

	<div class="row">
		<?php  echo _AT('status'); ?><br />
		<a href="admin/admin_edit.php?id=<?php echo $row['member_id']; ?>"><?php
		if ($row['status']) {
			echo _AT('instructor').'</a>,  <a href="admin/courses.php?id='.$row['member_id'].'">'. _AT('view_courses_taught') .'</a>';
		} else {
			echo _AT('student1').'</a>';
		} ?>
	</div>

	<div class="row">
		<?php  echo _AT('created_date'); ?><br />
		<?php echo $row['creation_date']; ?>
	</div>
</div>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>