<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
/* linked from admin/users.php                                  */
/* deletes a user from the system.                              */
/****************************************************************/

$_user_location = 'admin';

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
if ($_SESSION['course_id'] > -1) { exit; }

require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

global $savant;
$msg =& new Message($savant);

$id = intval($_GET['id']);

if (isset($_POST['submit_yes'])) {
	$id = intval($_POST['id']);

	$sql	= "DELETE FROM ".TABLE_PREFIX."course_enrollment WHERE member_id=$id";
	mysql_query($sql, $db);

	$sql	= "DELETE FROM ".TABLE_PREFIX."forums_accessed WHERE member_id=$id";
	mysql_query($sql, $db);

	$sql	= "DELETE FROM ".TABLE_PREFIX."forums_subscriptions WHERE member_id=$id";
	mysql_query($sql, $db);

	/****/
	/* delete forum threads block: */
		/* delete the thread replies: */
		$sql	= "SELECT COUNT(*) AS cnt, parent_id, forum_id FROM ".TABLE_PREFIX."forums_threads WHERE member_id=$id AND parent_id<>0 GROUP BY parent_id";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			/* update the forum posts counter */
			$sql = "UPDATE ".TABLE_PREFIX."forums SET num_posts=num_posts - $row[cnt] WHERE forum_id=$row[forum_id]";
			mysql_query($sql, $db);
			
			/* update the topics reply counter */
			$sql = "UPDATE ".TABLE_PREFIX."forums_threads SET num_comments=num_comments-$row[cnt] WHERE post_id=$row[parent_id]";
			mysql_query($sql, $db);
		}

		/* delete threads this member started: */
		$sql	= "SELECT post_id, forum_id, num_comments FROM ".TABLE_PREFIX."forums_threads WHERE member_id=$id AND parent_id=0";
		$result = mysql_query($sql, $db);
		while ($row = mysql_fetch_assoc($result)) {
			/* update the forum posts and topics counters */
			$num_posts = $row['num_comments'] + 1;
			$sql = "UPDATE ".TABLE_PREFIX."forums SET num_topics=num_topics-1, num_posts=num_posts - $num_posts WHERE forum_id=$row[forum_id]";
			mysql_query($sql, $db);

			/* delete the replies */
			$sql = "DELETE FROM ".TABLE_PREFIX."forums_threads WHERE parent_id=$row[post_id]";
			mysql_query($sql, $db);
		}
		/* delete the actual threads */
		$sql	= "DELETE FROM ".TABLE_PREFIX."forums_threads WHERE member_id=$id";
		mysql_query($sql, $db);
	/* end delete forum threads block. */
	/****/

	$sql	= "DELETE FROM ".TABLE_PREFIX."instructor_approvals WHERE member_id=$id";
	mysql_query($sql, $db);

	$sql	= "DELETE FROM ".TABLE_PREFIX."messages WHERE from_member_id=$id OR to_member_id=$id";
	mysql_query($sql, $db);

	$sql	= "DELETE FROM ".TABLE_PREFIX."polls_members WHERE member_id=$id";
	mysql_query($sql, $db);

	$sql	= "DELETE FROM ".TABLE_PREFIX."tests_answers WHERE member_id=$id";
	mysql_query($sql, $db);

	$sql	= "DELETE FROM ".TABLE_PREFIX."tests_results WHERE member_id=$id";
	mysql_query($sql, $db);

	$sql	= "DELETE FROM ".TABLE_PREFIX."users_online WHERE member_id=$id";
	mysql_query($sql, $db);

	$sql	= "DELETE FROM ".TABLE_PREFIX."members WHERE member_id=$id";
	mysql_query($sql, $db);

	$msg->addFeedback('USER_DELETED');
	header('Location: users.php');
	exit;
} else if (isset($_GET['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: users.php');
	exit;
}

require(AT_INCLUDE_PATH.'header.inc.php'); 
?>
<h3><?php echo _AT('delete_user') ?></h3>
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
	if(isset($warnings)){ print_warnings($warnings); }
	*/
	$msg->printAll();
	
	$sql	= "SELECT * FROM ".TABLE_PREFIX."members WHERE member_id=$id";
	$result = mysql_query($sql, $db);
	if (!($row = mysql_fetch_assoc($result))) {
		echo _AT('no_user_found');
	} else {
		$sql	= "SELECT * FROM ".TABLE_PREFIX."courses WHERE member_id=$id";
		$result = mysql_query($sql, $db);
		if (($row2 = mysql_fetch_assoc($result))) {
			$msg->printErrors('NODELETE_USER');
			
			echo '<p><a href="'.$_SERVER['PHP_SELF'].'?cancel=1">'._AT('cancel').'</a></p>';

		} else {
			$hidden_vars['id'] = $id;
			$confirm = array('DELETE_USER', AT_print($row['login'], 'members.login'));
			$msg->addConfirm($confirm, $hidden_vars);
			$msg->printConfirm();
		}
	}

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>