<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg GayJoel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');
require(AT_INCLUDE_PATH.'header.inc.php'); 
require(AT_INCLUDE_PATH.'lib/delete_course.inc.php');

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

$course = intval($_GET['course']);
?>

<h2><?php echo _AT('delete_course'); ?></h2>

<?php
/*if (isset($_GET['f'])) { 
	$f = intval($_GET['f']);
	if ($f <= 0) {
		/* it's probably an array *
		$f = unserialize(urldecode($_GET['f']));
	}
	print_feedback($f);
}
if (isset($errors)) { print_errors($errors); }
*/
$msg->printFeedbacks();
$msg->printErrors();

if (!$_GET['d']) {
	/* We must ensure that any previous feedback is flushed, since AT_FEEDBACK_CANCELLED might be present
		 * if Yes/Delete was chosen somewhere
		 */
	$msg->deleteFeedback('CANCELLED');
	
	$warnings = array('SURE_DELETE_COURSE1', AT_print($system_courses[$course]['title'], 'courses.title'));
	$msg->printWarnings($warnings);
	
	/* Since we do not know which choice will be taken, assume it No/Cancel, addFeedback('CANCELLED)
	 * If sent to courses.php then OK, else if sent back here & if $_GET['d']=1 then assumed choice was not taken
	 * ensure that addFeeback('CANCELLED') is properly cleaned up, see above
	 */
	$msg->addFeedback('CANCELLED');
	echo '<div align="center"><a href="'.$_SERVER['PHP_SELF'].'?course='.$course.SEP.'d=1'.'">'._AT('yes_delete').'</a> | <a href="admin/courses.php">'._AT('no_cancel').'</a></div>';

} else if ($_GET['d'] == 1){
	/* We must ensure that any previous feedback is flushed, since AT_FEEDBACK_CANCELLED might be present
		 * if Yes/Delete was chosen above
		 */
	$msg->deleteFeedback('CANCELLED');

	$warnings = array('SURE_DELETE_COURSE2', AT_print($system_courses[$course]['title'], 'courses.title'));
	$msg->printWarnings($warnings);
	
	/* Since we do not know which choice will be taken, assume it No/Cancel, addFeedback('CANCELLED)
	 * If sent to courses.php then OK, else if sent back here & if $_GET['d']=2 then assumed choice was not taken
	 * ensure that addFeeback('CANCELLED') is properly cleaned up, see above
	 */
	$msg->addFeedback('CANCELLED'); ?>
	<div align="center"><a href="<?php echo $_SERVER['PHP_SELF'].'?course='.$course.SEP.'d=2'; ?>"><?php echo _AT('yes_delete'); ?></a> | <a href="admin/courses.php"><?php echo _AT('no_cancel'); ?></a></div>
<?php
	} else if ($_GET['d'] == 2){
		/* We must ensure that any previous feedback is flushed, since AT_FEEDBACK_CANCELLED might be present
		 * if Yes/Delete was chosen above
		 */
		$msg->deleteFeedback('CANCELLED');

		/* delete this course */
		/* @See: lib/delete_course.inc.php */
		delete_course($course, $entire_course = true, $rel_path = '../');

		echo '</pre><br />';

		// purge the system_courses cache! (if successful)
		cache_purge('system_courses','system_courses');
		
		$msg->printFeedbacks('COURSE_DELETED');
		
		echo _AT('return').' <a href="admin/courses.php">'._AT('home').'</a>.<br />';
	}

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>