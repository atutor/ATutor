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

$section = 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if (!$_SESSION['s_is_super_admin']) {
	exit;
}

if($_POST['logout'] && $_POST['submit']!='') {
	$sql = "SELECT m.member_id, m.login, m.preferences, PASSWORD(m.password) AS pass, m.language FROM ".TABLE_PREFIX."members m, ".TABLE_PREFIX."courses c WHERE c.course_id=".$_POST['course']." and c.member_id=m.member_id";
	$result = mysql_query($sql);
	if ($row = mysql_fetch_array($result)) {
		$_SESSION['login'] = $row['login'];
		$_SESSION['valid_user'] = true;
		$_SESSION['member_id']	= intval($row['member_id']);
		assign_session_prefs(unserialize(stripslashes($row['preferences'])));
		$_SESSION['is_guest']	= 0;
		$_SESSION['lang']		= $row['language'];

		Header('Location: ../bounce.php?course='.$_POST['course']);
		exit;
	}
}
if ($_POST['cancel']) {
	Header('Location: courses.php?f='.urlencode_feedback(AT_FEEDBACK_CANCELLED));
	exit;
}

require(AT_INCLUDE_PATH.'admin_html/header.inc.php');

?>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	<input type="hidden" name="logout" value="true">
	<?php
		echo '<input type="hidden" name="course" value="'.$_GET['course'].'">';
		print_warnings("You will be logged in as the instructor for this course.  To come back to your admin account, you will need to log out as the instructor and then log in again as administrator.");
	?>
	<p align="center"><input type="submit" name="submit" value="<?php echo _AT('continue'); ?>" class="button"> - <input type="submit" name="cancel" class="button" value=" <?php echo _AT('cancel'); ?> " /></p>
	</form>
<?php
require(AT_INCLUDE_PATH.'admin_html/footer.inc.php');
?>