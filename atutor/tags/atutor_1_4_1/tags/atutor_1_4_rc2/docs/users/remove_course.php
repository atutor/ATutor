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

$title = _AT('remove').' '._AT('course');
	require(AT_INCLUDE_PATH.'header.inc.php');


	$course = intval($_GET['course']);
	
	if (!$_GET['d']) {
	$warnings[]=array(AT_WARNING_REMOVE_COURSE,$system_courses[$course][title]);
	print_warnings($warnings);

?>
<p align="center">
		<a href="<?php echo $_SERVER['PHP_SELF'].'?course='.$course.SEP.'d=1'; ?>"><?php echo _AT('yes_delete'); ?></a> | <a href="users/index.php?f=<?php echo urlencode_feedback(AT_FEEDBACK_CANCELLED); ?>"><?php echo _AT('no_cancel'); ?></a>
<p>
<?php
	} else {
		$sql	= "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$_SESSION[member_id] AND course_id=$course";

		$result = mysql_query($sql, $db);
		

		if ($result) {
			$feedback[]=AT_FEEDBACK_COURSE_REMOVED;
			print_feedback($feedback);
		} else {
			$errors[]=AT_ERROR_REMOVE_COURSE;
			print_errors($errors);
		}
		echo '<br />'._AT('return').' <a href="users/">'._AT('home').'</a>.';
	}

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>