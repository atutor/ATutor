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

$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);
if($_GET['cancel']){
	$msg->addFeedback('CANCELLED');
	header("Location:".$_base_href."users/index.php");
}	
	$title = _AT('remove').' '._AT('course');

	$course = intval($_GET['course']);
	
	if (!$_GET['d']) {
		require(AT_INCLUDE_PATH.'header.inc.php');
		$warnings = array('REMOVE_COURSE', $system_courses[$course][title]);
		$msg->printWarnings($warnings);
		?>
		<p align="center">
				<a href="<?php echo $_SERVER['PHP_SELF'].'?course='.$course.SEP.'d=1'; ?>"><?php echo _AT('yes_delete'); ?></a> | <a href="<?php echo $_SERVER['PHP_SELF'].'?course='.$course.SEP.'cancel=1'; ?>"><?php echo _AT('no_cancel'); ?></a>
		<p>
		<?php
	} else {
		$sql	= "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND course_id=$course";

		$result = mysql_query($sql, $db);
		

		if ($result) {
			$msg->addFeedback('COURSE_REMOVED');

		} else {
			$msg->addFeedback('REMOVE_COURSE');
		}
		header("Location: ".$_base_href."users/index.php");
		exit;
	}

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>
