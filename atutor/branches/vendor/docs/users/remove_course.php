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
$_include_path = '../include/';
require($_include_path.'vitals.inc.php');
require($_include_path.'cc_html/header.inc.php');
?>
<h2>Remove Course</h2>

<?php

	$course = intval($_GET['course']);
	
	if (!$_GET['d']) {
	$warnings[]=array(AT_WARNING_REMOVE_COURSE,$system_courses[$course][title]);
	print_warnings($warnings);

?>

		<a href="<?php echo $PHP_SELF.'?course='.$course.SEP.'d=1'; ?>">Yes, Delete</a> | <a href="users/?f=<?php echo urlencode_feedback(AT_FEEDBACK_CANCELLED); ?>">No, Cancel</a>
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
		echo '<br />Return <a href="users/">home</a>.';
	}

require ($_include_path.'cc_html/footer.inc.php'); 
?>
