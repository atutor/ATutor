<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

$page = 'courses';
$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);


if ($_POST['logout'] && $_POST['submit']!='') {
	$sql = "SELECT M.member_id, M.login FROM ".TABLE_PREFIX."members M, ".TABLE_PREFIX."courses C WHERE C.course_id=".$_POST['course']." and C.member_id=M.member_id";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$_SESSION['login']      = $row['login'];
		$_SESSION['valid_user'] = true;
		$_SESSION['member_id']	= intval($row['member_id']);

		header('Location: ../bounce.php?course='.$_POST['course']);
		exit;
	}
}
if ($_POST['cancel']) {

	$msg->addFeedback('CANCELLED');
	Header('Location: courses.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 

?>
<h3><?php echo _AT('view').' '; 
	$sql = "SELECT * FROM ".TABLE_PREFIX."courses WHERE course_id=".$_REQUEST['course'];
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_array($result)) {
		echo $row['title'];
	}

?> </h3>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="logout" value="true" />
	<?php
		echo '<input type="hidden" name="course" value="'.$_GET['course'].'" />';
		
		
		$warnings = array('LOGIN_INSTRUCTOR', SITE_NAME);
		$msg->printWarnings($warnings);
	?>
	<p align="center"><input type="submit" name="submit" class="button" value="<?php echo _AT('continue'); ?>"  /> - <input type="submit" name="cancel" class="button" value=" <?php echo _AT('cancel'); ?> " /></p>
	</form>

<?php
require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>