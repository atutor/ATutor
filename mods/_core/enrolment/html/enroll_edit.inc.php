<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

/**
* Generates the list of login ids of the selected user
* @access  private
* @param   string $member_ids	the list of members to be checked
* @return  string				The list of login IDs
* @author  Shozub Qureshi
*/
function get_usernames ($member_ids) {
	$sql    = "SELECT login FROM %smembers WHERE `member_id` IN (%s)";
	$rows_logins = queryDB($sql, array(TABLE_PREFIX, $member_ids));
	
	foreach($rows_logins as $row){
		$str .= '<li>' . $row['login'] . '</li>';
	}
	return $str;
}

/**
* Checks if any of the selected users have non-zero roles or privileges
* @access  private
* @param   string $member_ids	the list of members to be checked
* @return  int					whether the role/priv is empty or not (0 = if empty, 1 = if ok)
* @author  Shozub Qureshi
*/
function check_roles ($member_ids) {
	$sql    = "SELECT * FROM %scourse_enrollment WHERE `member_id` IN (%s)";
	$rows_roles = queryDB($sql, array(TABLE_PREFIX, $member_ids));
	
	foreach($rows_roles as $row){
		if ($row['role'] != 'Student' || $row['privileges'] != 0) {
			return 1;
		}
	}
	return 0;
}


/**
* Unenrolls students from course enrollement
* @access  private
* @param   array $list			the IDs of the members to be removed
* @author  Shozub Qureshi
* @author  Greg Gay  added Unsubscribe when unenrolling
*/
function unenroll ($list) {
	global $system_courses, $course_id;
    $members =implode(',', array_map('intval', $list));
	if (isset($members)) {
		$sql    = "DELETE FROM %scourse_enrollment WHERE course_id=%d AND member_id IN (%s)";
		$result = queryDB($sql, array(TABLE_PREFIX, $course_id, $members));

		$sql    = "DELETE FROM %sgroups_members 
		            WHERE member_id IN (%s) 
		              AND group_id IN (SELECT group_id from %sgroups G, %sgroups_types GT
		                                WHERE G.type_id = GT.type_id AND GT.course_id = %d)";
		$result = queryDB($sql, array(TABLE_PREFIX, $members, TABLE_PREFIX, TABLE_PREFIX, $course_id));
		
		// remove forum subscriptions as admin else instructor 
		if($_SESSION['course_id'] == "-1"){
			$this_course_id = $_REQUEST['course_id'];
		} else {
			$this_course_id = $_SESSION['course_id'];
		}
		
		// get a list for forums in this course

		$sql = "SELECT forum_id from %sforums_courses WHERE course_id = %d";
		$rows_forums = queryDB($sql, array(TABLE_PREFIX, $this_course_id));

		if(count($rows_forums) > 0){
                foreach($rows_forums as $row){
                    $this_course_forums[] = $row['forum_id'];
                }
                $this_forum_list = implode(',', $this_course_forums);

                // delete from forum_subscription any member in $members (being unenrolled)
                // with posts to forums in this course. 
                foreach ($this_course_forums as $this_course_forum){
                    $sql1 = "DELETE FROM %sforums_subscriptions WHERE forum_id = %d AND member_id IN (%s)";
                    $result_unsub = queryDB($sql1, array(TABLE_PREFIX, $this_course_forum, $members));
                }

            // get a list of posts for forums in the current course
            $sql = "SELECT post_id FROM %sforums_threads WHERE forum_id IN (%s)";
            $rows_posts = queryDB($sql, array(TABLE_PREFIX, $this_forum_list));
            if(count($rows_posts) > 0){
                foreach($rows_posts as $row){
                    $this_course_posts[] = $row['post_id'];
                }
                $this_post_list = implode(',', $this_course_posts);

                // delete from forums_accessed any post with member_id in $members being unenrolled, 
                // and post_id in 
                foreach($this_course_posts as $this_course_post){
                    $sql2	= "DELETE FROM %sforums_accessed WHERE post_id = %d AND member_id IN (%s)";
                    $result_unsub2 = queryDB($sql2, array(TABLE_PREFIX, $this_course_post, $members));
                }
            }
		}
	}
}

/**
* Enrolls students into course enrollement
* @access  private
* @param   array $list			the IDs of the members to be added
* @author  Shozub Qureshi
*/
function enroll ($list) {
	global $msg, $_config, $course_id, $owner;
	require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
    
	$num_list = count($list);
	$members = '(member_id='.intval($list[0]).')';
	for ($i=0; $i < $num_list; $i++)	{
		$id = intval($list[$i]);
		$members .= ' OR (member_id='.$id.')';
		$sql = "REPLACE INTO %scourse_enrollment VALUES (%d, %d, 'y', 0, 'Student', 0)";
		$result_enrolled = queryDB($sql, array(TABLE_PREFIX, $id, $course_id));
		if($result_enrolled != 1){
			$sql = "UPDATE %scourse_enrollment SET approved='y' WHERE course_id=%d AND member_id=%d";
			$result = queryDB($sql, array(TABLE_PREFIX, $course_id, $id));
		}
	}

	//get First_name, Last_name of course Instructor
	$sql_from    = "SELECT first_name, last_name, email FROM %smembers WHERE member_id = %d";
	$row_from = queryDB($sql_from, array(TABLE_PREFIX, $owner), TRUE);

	$email_from_name  = $row_from['first_name'] . ' ' . $row_from['last_name'];
	$email_from = $row_from['email'];

	//get email addresses of users:
	$sql_to    = "SELECT email FROM %smembers WHERE (%s)";
	$rows_to = queryDB($sql_to, array(TABLE_PREFIX, $members));
	foreach($rows_to as $row_to){
		// send email here.
		$login_link = AT_BASE_HREF . 'login.php?course=' . $course_id;
		$subject = SITE_NAME.': '._AT('enrol_message_subject');
		$body = SITE_NAME.': '._AT('enrol_message_approved', $_SESSION['course_title'], $login_link)."\n\n";

		$mail = new ATutorMailer;
		$mail->From     = $_config['contact_email'];
		$mail->FromName = $_config['site_name'];
		$mail->AddAddress($row_to['email']);
		$mail->Subject  = $subject;
		$mail->Body     = $body;
			
		if (!$mail->Send()) {
			$msg->addError('SENDING_ERROR');
		}

		unset($mail);
	}

}


function group ($list, $gid) {
	global $msg;

	$sql = "REPLACE INTO %sgroups_members VALUES ";
	$gid=intval($gid);
	for ($i=0; $i < count($list); $i++)	{
		$student_id = intval($list[$i]);
		$sql .= "($gid, $student_id),";
	}
	$sql = substr($sql, 0, -1);
	$result = queryDB($sql, array(TABLE_PREFIX));
    if($result > 0){
	    $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	}
	header('Location: index.php');
	exit;
}

function group_remove ($ids, $gid) {
	global $msg;
	$gid=intval($gid);
    $ids =implode(',', array_map('intval', $ids));

	if ($ids) {
		$sql = "DELETE FROM %sgroups_members WHERE group_id=%d AND member_id IN (%s)";
		$result = queryDB($sql, array(TABLE_PREFIX, $gid, $ids));
		if($result > 0){
		    $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		}
	}

	header('Location: index.php');
	exit;
}

/**
* Marks a student as an alumni of the course (not enrolled, but can view course material and participate in forums)
* @access  private
* @param   array $list			the IDs of the members to be alumni
* @author  Heidi Hazelton
*/
function alumni ($list) {
	global $course_id;
	$members = '(member_id='.intval($list[0]).')';
	for ($i=1; $i < count($list); $i++)	{
		$members .= ' OR (member_id='.intval($list[$i]).')';
	}
	
	$sql    = "UPDATE %scourse_enrollment SET approved = 'a' WHERE course_id=%d AND (%s)";
	$result = queryDB($sql, array(TABLE_PREFIX, $course_id, $members));
}


//course_owner
$owner = $system_courses[$course_id]['member_id'];

if (isset($_POST['submit_no'])) {
	//if user decides to forgo option
	$msg->addFeedback('CANCELLED');
	header('Location: index.php?current_tab='.$_POST['curr_tab'].SEP.'course_id='.$course_id);
	exit;
}
else if (isset($_POST['submit_yes']) && $_POST['func'] =='unenroll' ) {
	check_csrf_token();

	//Unenroll student from course
	unenroll($_POST['id']);

	$msg->addFeedback('MEMBERS_REMOVED');
	header('Location: index.php?current_tab=4'.SEP.'course_id='.$course_id);
	exit;
} else if (isset($_POST['submit_yes']) && $_POST['func'] =='enroll' ) {
	check_csrf_token();

	//Enroll student in course
	enroll($_POST['id']);

	$msg->addFeedback('MEMBERS_ENROLLED');
	header('Location: index.php?current_tab=0'.SEP.'course_id='.$course_id);
	exit;
} else if (isset($_POST['submit_yes']) && $_POST['func'] =='alumni' ) {
	check_csrf_token();

	//Mark student as course alumnus
	alumni($_POST['id']);
	
	$msg->addFeedback('MEMBERS_ALUMNI');
	header('Location: index.php?current_tab=2'.SEP.'course_id='.$course_id);
	exit;
} else if (isset($_POST['submit_yes']) && $_POST['func'] =='group' ) {
	//Mark student as a member of the group
	group($_POST['id'],$_POST['gid']);
	
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php?current_tab='.$_POST['current_tab'].SEP.'course_id='.$course_id);
	exit;
} else if (isset($_POST['submit_yes']) && $_POST['func'] =='group_remove' ) {
	// Remove student as a member of the group
	group_remove($_POST['id'],$_POST['gid']);
	
	$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
	header('Location: index.php?current_tab='.$_POST['current_tab'].SEP.'course_id='.$course_id);
	exit;
}
require(AT_INCLUDE_PATH.'header.inc.php');

//Store id's into a hidden element for use by functions
$j = 0;
while ($_GET['id'.$j]) {
	$_GET['id'.$j] = abs($_GET['id'.$j]);
	if ($_GET['id'.$j] == $owner) {
		//do nothing
	} else {
		$hidden_vars['id['.$j.']'] = $_GET['id'.$j];
		$member_ids .= $_GET['id'.$j].', ';
	}	
	$j++;
}
$member_ids = substr($member_ids, 0, -2);

$hidden_vars['func']     = $_GET['func'];
$hidden_vars['current_tab'] = $_GET['current_tab'];
$hidden_vars['gid']		 = abs($_GET['gid']);
$hidden_vars['course_id'] = $course_id;
$hidden_vars['csrftoken'] = $_SESSION['token'];
//get usernames of users about to be edited
$str = get_usernames($member_ids);

//Print appropriate confirm msg for action
if ($_GET['func'] == 'remove') {
	$confirm = array('REMOVE_STUDENT',   $str);
	$msg->addConfirm($confirm, $hidden_vars);
} else if ($_GET['func'] == 'enroll') {
	$confirm = array('ENROLL_STUDENT',   $str);
	$msg->addConfirm($confirm, $hidden_vars);
} else if ($_GET['func'] == 'unenroll') {
	if (check_roles($member_ids) == 1) {
		$confirm = array('UNENROLL_PRIV', $str);
		$msg->addConfirm($confirm, $hidden_vars);
	} else {
		$confirm = array('UNENROLL_STUDENT', $str);
		$msg->addConfirm($confirm, $hidden_vars);
	}
} else if ($_GET['func'] == 'alumni') {
	$confirm = array('ALUMNI',   $str);
	$msg->addConfirm($confirm, $hidden_vars);
} else if ($_GET['func'] == 'group') {
	$sql = "SELECT title FROM %sgroups WHERE group_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $hidden_vars['gid']), TRUE);

	$confirm = array('STUDENT_GROUP', $row['title'], $str);
	$msg->addConfirm($confirm, $hidden_vars);
} else if ($_GET['func'] == 'group_remove') {
	$sql = "SELECT title FROM %sgroups WHERE group_id=%d";
	$rowt = queryDB($sql, array(TABLE_PREFIX, $hidden_vars['gid']), TRUE);

	$confirm = array('STUDENT_REMOVE_GROUP', $row['title'], $str);
	$msg->addConfirm($confirm, $hidden_vars);
}
		
$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');

?>