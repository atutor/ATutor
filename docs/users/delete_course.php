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
define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/filemanager.inc.php');

if ($_SESSION['s_is_super_admin']) {
	require(AT_INCLUDE_PATH.'admin_html/header.inc.php'); 
} else {
	require(AT_INCLUDE_PATH.'cc_html/header.inc.php');
}
$member_id=$_SESSION['member_id'];
?>

<h2><?php echo _AT('delete_course'); ?></h2>

<?php

/* make sure we own this course that we're approving for! */
$course = intval($_GET['course']);
if (!$_SESSION['s_is_super_admin']) {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE course_id=$course AND member_id=$_SESSION[member_id]";
	$result	= mysql_query($sql, $db);
	if (mysql_num_rows($result) != 1) {
		echo _AT('not_your_course');
		require(AT_INCLUDE_PATH.'cc_html/footer.inc.php');
		exit;
	}
}
if (!$_GET['d']) {
	$warnings[]= array(AT_WARNING_SURE_DELETE_COURSE1, $system_courses[$course][title]);
	print_warnings($warnings);
	//phpinfo();
	if(ereg("admin" , $_SERVER[HTTP_REFERER])){
		if($_GET['member_id']){
			echo '<center><a href="'.$PHP_SELF.'?course='.$course.SEP.'d=1'.SEP.'ad=1'.SEP.'member_id='.$_GET['member_id'].'">'._AT('yes_delete').'</a> | <a href="users/admin/courses.php?f='.urlencode_feedback(AT_FEEDBACK_CANCELLED).SEP.'member_id='.$_GET['member_id'].'">'._AT('no_cancel').'</a></center>';
		}else{
			echo '<center><a href="'.$PHP_SELF.'?course='.$course.SEP.'d=1'.SEP.'ad=1">'._AT('yes_delete').'</a> | <a href="users/admin/courses.php?f='.urlencode_feedback(AT_FEEDBACK_CANCELLED).'">'._AT('no_cancel').'</a></center>';
		}
	}else{
		echo	'<center><a href="'.$PHP_SELF.'?course='.$course.SEP.'d=1'.'">'._AT('yes_delete').'</a> | <a href="users/?f='.urlencode_feedback(AT_FEEDBACK_CANCELLED).'">'._AT('no_cancel').'</a></center>';
	}
?>
	<!--center><a href="<?php echo $PHP_SELF.'?course='.$course.SEP.'d=1'; ?>"><?php echo _AT('yes_delete'); ?></a> | <a href="users/?f=<?php echo urlencode_feedback(AT_FEEDBACK_CANCELLED); ?>"><?php echo _AT('no_cancel'); ?></a></center-->
<br />
<?php
	} else if ($_GET['d'] == 1){
		$warnings[]=array(AT_WARNING_SURE_DELETE_COURSE2, $system_courses[$course][title]);
		print_warnings($warnings);
?>
	<?php if($_GET['ad'] == 1){?>
		<?php if($_GET['member_id']){ ?>
			<center><br /><a href="<?php echo $PHP_SELF.'?course='.$course.SEP.'d=2'.SEP.'member_id='.$_GET['member_id'].'"'; ?>"><?php echo _AT('yes_delete'); ?></a> | <a href="users/admin/courses.php?f=<?php echo urlencode_feedback(AT_FEEDBACK_CANCELLED).SEP.'member_id='.$_GET['member_id']; ?>"><?php echo _AT('no_cancel'); ?></a></center>
		<?php }else{ ?>
			<center><br /><a href="<?php echo $PHP_SELF.'?course='.$course.SEP.'d=2'; ?>"><?php echo _AT('yes_delete'); ?></a> | <a href="users/admin/courses.php?f=<?php echo urlencode_feedback(AT_FEEDBACK_CANCELLED); ?>"><?php echo _AT('no_cancel'); ?></a></center>
		<?php } ?>
		<!--center><br /><a href="<?php echo $PHP_SELF.'?course='.$course.SEP.'d=2'; ?>"><?php echo _AT('yes_delete'); ?></a> | <a href="users/admin/courses.php?f=<?php echo urlencode_feedback(AT_FEEDBACK_CANCELLED); ?>"><?php echo _AT('no_cancel'); ?></a></center -->
	<?php }else{ ?>
		<center><br /><a href="<?php echo $PHP_SELF.'?course='.$course.SEP.'d=2'; ?>"><?php echo _AT('yes_delete'); ?></a> | <a href="users/?f=<?php echo urlencode_feedback(AT_FEEDBACK_CANCELLED); ?>"><?php echo _AT('no_cancel'); ?></a></center>
	<?php } ?>


	<!--center><br /><a href="<?php echo $PHP_SELF.'?course='.$course.SEP.'d=2'; ?>"><?php echo _AT('yes_delete'); ?></a> | <a href="users/?f=<?php echo urlencode_feedback(AT_FEEDBACK_CANCELLED); ?>"><?php echo _AT('no_cancel'); ?></a></center> -->

	<?php
	} else if ($_GET['d'] == 2){
		echo '<b>'._AT('deleting_course').'</b><pre>';

		// course_enrollment:
		$sql	= "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('enrolled').': '.mysql_affected_rows($db)."\n";

		// news:
		$sql	= "DELETE FROM ".TABLE_PREFIX."news WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('announcements').': '.mysql_affected_rows($db)."\n";
		//echo $sql;

		// related_content + content:
		$sql	= "SELECT * FROM ".TABLE_PREFIX."content WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_array($result)) {
			$sql	= "DELETE FROM ".TABLE_PREFIX."content_learning_concepts WHERE content_id=$row[0]";
			$result2 = mysql_query($sql, $db);
	
			$sql	= "DELETE FROM ".TABLE_PREFIX."related_content WHERE content_id=$row[0]";
			$result2 = mysql_query($sql, $db);
		}

		$sql = "DELETE FROM ".TABLE_PREFIX."content WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('content').':                            '.mysql_affected_rows($db)."\n";

		$sql = "OPTIMIZE TABLE ".TABLE_PREFIX."content";
		$result = mysql_query($sql, $db);

		/************************************/
		// course stats:
		$sql = "DELETE FROM ".TABLE_PREFIX."course_stats WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('course_stats').':                  '.mysql_affected_rows($db)."\n";

		/************************************/
		// links:
		$sql	= "SELECT * FROM ".TABLE_PREFIX."resource_categories WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		$total_links = 0;
		while ($row = mysql_fetch_array($result)) {
			$sql = "DELETE FROM ".TABLE_PREFIX."resource_links WHERE CatID=$row[0]";
			$result2 = mysql_query($sql, $db);
			$total_links += mysql_affected_rows($db);
		}
		$sql	= "DELETE FROM ".TABLE_PREFIX."resource_categories WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('resource_categories').':                '.mysql_affected_rows($db)."\n";
		echo _AT('resource_links').':                     '.$total_links."\n";

		/************************************/
		// glossary:
		$sql	= "DELETE FROM ".TABLE_PREFIX."glossary WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('glossary_terms').':                     '.mysql_affected_rows($db)."\n";

		/************************************/
		/* forum */
		$sql	= "SELECT post_id FROM ".TABLE_PREFIX."forums_threads WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_array($result)) {
			$sql	 = "DELETE FROM ".TABLE_PREFIX."forums_accessed WHERE post_id=$row[post_id]";
			$result2 = mysql_query($sql, $db);

			$sql	 = "DELETE FROM ".TABLE_PREFIX."forums_subscriptions WHERE post_id=$row[post_id]";
			$result2 = mysql_query($sql, $db);
		}

		/************************************/
		$sql = "DELETE FROM ".TABLE_PREFIX."forums_threads WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('forum_threads').':                      '.mysql_affected_rows($db)."\n";

		$sql = "DELETE FROM ".TABLE_PREFIX."forums WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('forums').':                             '.mysql_affected_rows($db)."\n";

		$sql = "OPTIMIZE TABLE ".TABLE_PREFIX."forums_threads";
		$result = mysql_query($sql, $db);

		$sql = "DELETE FROM ".TABLE_PREFIX."preferences WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo _AT('preferences').':                        '.mysql_affected_rows($db)."\n";

		$sql = "DELETE FROM ".TABLE_PREFIX."g_click_data WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		// no feedback for this item.


		// tests + tests_questions + tests_answers + tests_results:
		$sql	= "SELECT test_id FROM ".TABLE_PREFIX."tests WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_array($result)) {
			$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions WHERE test_id=$row[0]";
			$result2 = mysql_query($sql, $db);
	
			$sql2	= "SELECT result_id FROM ".TABLE_PREFIX."tests_results WHERE test_id=$row[0]";
			$result2 = mysql_query($sql2, $db);
			while ($row2 = mysql_fetch_array($result2)) {
				$sql3	= "DELETE FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$row2[0]";
				$result3 = mysql_query($sql3, $db);
			}

			$sql	= "DELETE FROM ".TABLE_PREFIX."tests_results WHERE test_id=$row[0]";
			$result2 = mysql_query($sql, $db);
		}

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests WHERE course_id=$course";
		$result = mysql_query($sql, $db);

		echo _AT('tests').':                              '.mysql_affected_rows($db)."\n";

		// files:
		$path = '../content/'.$course.'/';
		clr_dir($path);

		// courses:
		$sql = "DELETE FROM ".TABLE_PREFIX."courses WHERE course_id=$course";
		$result = mysql_query($sql, $db);
		echo '<b>'._AT('course').': '.mysql_affected_rows($db).' '._AT('always_one').'</b>'."\n";

		echo '</pre><br />'._AT('return').' ';
		
		if (!$_SESSION['s_is_super_admin']) {
			echo '<a href="users/">'._AT('home').'</a>.';
		} else {
			echo '<a href="users/admin/">'._AT('home').'</a>.';
		}

		// purge the system_courses cache! (if successful)
		cache_purge('system_courses','system_courses');
		$feedback[]=AT_FEEDBACK_COURSE_DELETED;
		print_feedback($feedback);

	}

require (AT_INCLUDE_PATH.'cc_html/footer.inc.php');

?>