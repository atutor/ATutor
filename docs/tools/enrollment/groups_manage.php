<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

$page = 'tests';
define('AT_INCLUDE_PATH', '../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

authenticate(AT_PRIV_TEST_CREATE);

if ($_REQUEST['gid']) {
	$_section[3][0] = _AT('edit');
} else {
	$_section[3][0] = _AT('add');
}

if (isset($_POST['cancel'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: groups.php');
	exit;
} else if (isset($_POST['submit'])) {

	$_POST['title'] = trim($_POST['title']);

	if (!empty($_POST['title']) && !isset($_POST['gid'])) {
		$_POST['title'] = $addslashes($_POST['title']);
		$sql	= "INSERT INTO ".TABLE_PREFIX."groups VALUES (0, $_SESSION[course_id], '$_POST[title]')";
		$result = mysql_query($sql, $db);
		$msg->addFeedback('GROUP_ADDED');
		header('Location: groups.php');
		exit;
	} else if (!empty($_POST['title']) && isset($_POST['gid']))  {
		$_POST['title'] = $addslashes($_POST['title']);
		$sql	= "REPLACE INTO ".TABLE_PREFIX."groups VALUES ($_POST[gid], $_SESSION[course_id], '$_POST[title]')";
		$result = mysql_query($sql, $db);
		$msg->addFeedback('GROUP_UPDATED');
		header('Location: groups.php');
		exit;
	} else {
		$msg->addError('NO_TITLE');
	}
}

if (isset($_GET['gid'])) {
	$sql = "SELECT title FROM ".TABLE_PREFIX."groups WHERE group_id=$_GET[gid]";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	$_POST['title'] = $row['title'];
}

require(AT_INCLUDE_PATH.'header.inc.php');

$msg->printErrors();

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="form">
<?php 
	if (isset($_REQUEST['gid'])) {
		echo '<input type="hidden" value="'.$_REQUEST['gid'].'" name="gid" />';
	}
?>
<div class="input-form" style="width:50%;">
	<div class="row">
		<label for="cat"><div class="required" title="<?php echo _AT('required_field'); ?>">*</div><?php echo _AT('title'); ?></label><br />
		<input type="text" name="title" id="cat" value="<?php echo $_POST['title']; ?>" />
	</div>

	<div class="row buttons">
		<input type="submit" value="<?php echo _AT('save'); ?>"   name="submit" accesskey="s" />
		<input type="submit" value="<?php echo _AT('cancel'); ?>" name="cancel" />
	</div>
</div>
</form>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>