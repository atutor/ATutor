<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg GayJoel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

$section = 'users';
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
require(AT_INCLUDE_PATH.'admin_html/header.inc.php');
require(AT_INCLUDE_PATH.'lib/delete_course.inc.php');

$course = intval($_GET['course']);
?>

<h2><?php echo _AT('delete_course'); ?></h2>

<?php
if (isset($_GET['f'])) { 
	$f = intval($_GET['f']);
	if ($f <= 0) {
		/* it's probably an array */
		$f = unserialize(urldecode($_GET['f']));
	}
	print_feedback($f);
}
if (isset($errors)) { print_errors($errors); }

if (!$_GET['d']) {
	$warnings[]= array(AT_WARNING_SURE_DELETE_COURSE1, $system_courses[$course]['title']);
	print_warnings($warnings);
	echo '<a href="'.$_SERVER['PHP_SELF'].'?course='.$course.SEP.'d=1'.'">'._AT('yes_delete').'</a> | <a href="admin/courses.php?f='.urlencode_feedback(AT_FEEDBACK_CANCELLED).'">'._AT('no_cancel').'</a>';

} else if ($_GET['d'] == 1){
		$warnings[]=array(AT_WARNING_SURE_DELETE_COURSE2, $system_courses[$course][title]);
		print_warnings($warnings);
?>
	<br /><a href="<?php echo $_SERVER['PHP_SELF'].'?course='.$course.SEP.'d=2'; ?>"><?php echo _AT('yes_delete'); ?></a> | <a href="admin/courses.php?f=<?php echo urlencode_feedback(AT_FEEDBACK_CANCELLED); ?>"><?php echo _AT('no_cancel'); ?></a>
<?php
	} else if ($_GET['d'] == 2){

		/* delete this course */
		delete_course($course);

		echo '</pre><br />'._AT('return').' ';
		
		echo '<a href="admin/courses.php">'._AT('home').'</a>.';

		// purge the system_courses cache! (if successful)
		cache_purge('system_courses','system_courses');
		$feedback[]=AT_FEEDBACK_COURSE_DELETED;
		print_feedback($feedback);
		
		echo _AT('return').' <a href="admin/courses.php">'._AT('home').'</a>.';
	}

require (AT_INCLUDE_PATH.'admin_html/footer.inc.php');

?>