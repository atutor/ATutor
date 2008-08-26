<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton	*/
/* Adaptive Technology Resource Centre / University of Toronto				*/
/* http://atutor.ca															*/
/*																			*/
/* This program is free software. You can redistribute it and/or			*/
/* modify it under the terms of the GNU General Public License				*/
/* as published by the Free Software Foundation.							*/
/****************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

/**
* Returns an array of (shared and non-shared) forums belonging to the given course
* @access  public
* @param   integer $course		id of the course
* @return  string array			each row is a forum 
* @see     $db					in include/vitals.inc.php
* @see     is_shared_forum()
* @author  Heidi Hazelton
* @author  Joel Kronenberg
*/
function get_forums($course) {
	global $db;

	if ($course) {
		$sql	= "SELECT F.*, DATE_FORMAT(F.last_post, '%Y-%m-%d %H:%i:%s') AS last_post FROM ".TABLE_PREFIX."forums_courses FC INNER JOIN ".TABLE_PREFIX."forums F USING (forum_id) WHERE FC.course_id=$course GROUP BY FC.forum_id ORDER BY F.title";
	} else {
		$sql	= "SELECT F.*, FC.course_id, DATE_FORMAT(F.last_post, '%Y-%m-%d %H:%i:%s') AS last_post FROM ".TABLE_PREFIX."forums_courses FC INNER JOIN ".TABLE_PREFIX."forums F USING (forum_id) GROUP BY FC.forum_id ORDER BY F.title";
	}

	// 'nonshared' forums are always listed first:
	$forums['nonshared'] = array();
	$forums['shared']    = array();
	$forums['group']     = array();

	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		// for each forum, check if it's shared or not:

		if (is_shared_forum($row['forum_id'])) {
			$forums['shared'][] = $row;
		} else {
			$forums['nonshared'][] = $row;
		}
	}
		
	// retrieve the group forums:

	if (!$_SESSION['groups']) {
		return $forums;
	}

	$groups =  implode(',',$_SESSION['groups']);

	$sql = "SELECT F.*, G.group_id FROM ".TABLE_PREFIX."forums_groups G INNER JOIN ".TABLE_PREFIX."forums F USING (forum_id) WHERE G.group_id IN ($groups) ORDER BY F.title";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$row['title'] = get_group_title($row['group_id']);
		$forums['group'][] = $row;
	}

	return $forums;	
}

/**
* Returns true/false whether or not this forum is shared.
* @access  public
* @param   integer $forum_id	id of the forum
* @return  boolean				true if this forum is shared, false otherwise
* @see     $db					in include/vitals.inc.php
* @author  Joel Kronenberg
*/
function is_shared_forum($forum_id) {
	global $db;

	$sql = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."forums_courses WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	if ($row['cnt'] > 1) {
		return TRUE;
	} // else:
	
	return FALSE;
}


/**
* Returns forum information for given forum_id 
* @access  public
* @param   integer $forum_id	id of the forum
* @param   integer $course		id of the course (for non-admins)
* @return  string array			each row is a forum 
* @see     $db					in include/vitals.inc.php
* @author  Heidi Hazelton
*/
function get_forum($forum_id, $course = '') {
	global $db;

	if (!empty($course)) {
		$sql	= "SELECT * FROM ".TABLE_PREFIX."forums_courses fc, ".TABLE_PREFIX."forums f WHERE (fc.course_id=$course OR fc.course_id=0) AND fc.forum_id=f.forum_id and fc.forum_id=$forum_id ORDER BY title";
		$result = mysql_query($sql, $db);
		$forum = mysql_fetch_assoc($result);
	} else if (empty($course)) {  	//only admins should be retrieving forums w/o a course!  add this check
		$sql = "SELECT * FROM ".TABLE_PREFIX."forums WHERE forum_id=$forum_id";
		$result = mysql_query($sql, $db);
		$forum = mysql_fetch_assoc($result);
	} else {

		return;
	}

	return $forum;	
}

/**
* Checks to see if signed in member is allowed to view the forum page
* @access  public
* @param   integer $forum_id	id of the forum
* @return  boolean				view (true) or not view (false)
* @see     $db					in include/vitals.inc.php
* @author  Heidi Hazelton
*/
function valid_forum_user($forum_id) {
	global $db;

	$sql	= "SELECT forum_id FROM ".TABLE_PREFIX."forums_courses WHERE (course_id=$_SESSION[course_id] OR course_id=0) AND forum_id=$forum_id";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);

	if (empty($row)) {
		// not a course forum, let's check group:
		if (!empty($_SESSION['groups'])){
			$groups = implode(',', $_SESSION['groups']);
			$sql	= "SELECT forum_id FROM ".TABLE_PREFIX."forums_groups WHERE group_id IN ($groups) AND forum_id=$forum_id";
			$result = mysql_query($sql, $db);
			if ($row = mysql_fetch_assoc($result)) {
				return TRUE;
			}
		}

		return FALSE;
	}

	return TRUE;	
}

/**
* Adds a forum
* @access  public
* @param   array $_POST			add-forum form variables
* @see     $db					in include/vitals.inc.php
* @see     $addslashes			in include/vitals.inc.php
* @author  Heidi Hazelton
*/
function add_forum($_POST) {
	global $db;
	global $addslashes;

	$_POST['title'] = $addslashes($_POST['title']);
	$_POST['body']  = $addslashes($_POST['body']);
	$_POST['edit']  = intval($_POST['edit']);

	$sql	= "INSERT INTO ".TABLE_PREFIX."forums VALUES (NULL,'$_POST[title]', '$_POST[body]', 0, 0, NOW(), $_POST[edit])";
	$result = mysql_query($sql,$db);

	$sql	= "INSERT INTO ".TABLE_PREFIX."forums_courses VALUES (LAST_INSERT_ID(),  $_SESSION[course_id])";
	$result = mysql_query($sql,$db);

	return;
}

/**
* Edits a forum
* @access  public
* @param   array $_POST			add-forum form variables
* @see     $db					in include/vitals.inc.php
* @see     $addslashes			in include/vitals.inc.php
* @author  Heidi Hazelton
*/
function edit_forum($_POST) {
	global $db;
	global $addslashes;

	$_POST['title']  = $addslashes($_POST['title']);
	$_POST['body']   = $addslashes($_POST['body']);

	$_POST['fid']    = intval($_POST['fid']);
	$_POST['edit']    = intval($_POST['edit']);

	$sql	= "UPDATE ".TABLE_PREFIX."forums SET title='$_POST[title]', description='$_POST[body]', last_post=last_post, mins_to_edit=$_POST[edit] WHERE forum_id=$_POST[fid]";
	$result = mysql_query($sql,$db);

	return;
}

/**
* Deletes a forum (checks if its shared).
* Assumes the forum is not shared.
* Assumes the user has the priv to delete this forum.
* @access  public
* @param   array $_POST			add-forum form variables
* @see     $db					in include/vitals.inc.php
* @see     $addslashes			in include/vitals.inc.php
* @author  Heidi Hazelton
*/
function delete_forum($forum_id) {
	global $db;

	$sql	= "SELECT post_id FROM ".TABLE_PREFIX."forums_threads WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_array($result)) {
		$sql	 = "DELETE FROM ".TABLE_PREFIX."forums_accessed WHERE post_id=$row[post_id]";
		$result2 = mysql_query($sql, $db);
	}

	$sql	= "DELETE FROM ".TABLE_PREFIX."forums_subscriptions WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);

	$sql    = "DELETE FROM ".TABLE_PREFIX."forums_threads WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);

	$sql = "DELETE FROM ".TABLE_PREFIX."forums_courses WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);

	$sql    = "DELETE FROM ".TABLE_PREFIX."forums WHERE forum_id=$forum_id";
	$result = mysql_query($sql, $db);
	
	$sql = "OPTIMIZE TABLE ".TABLE_PREFIX."forums_threads";
	$result = mysql_query($sql, $db);

}

function print_entry($row) {
	global $page,$system_courses, $forum_info;
	static $counter;
	$counter++;

	$reply_link = '<a href="forum/view.php?fid='.$row['forum_id'].SEP.'pid=';
	if ($row['parent_id'] == 0) {
		$reply_link .= $row['post_id'];
	} else {
		$reply_link .= $row['parent_id'];
	}
	$reply_link .= SEP.'reply='.$row['post_id'].SEP.'page='.$page.'#post" >'._AT('reply').'</a>';

?>

	<li class="<?php if ($counter %2) { echo 'odd'; } else { echo 'even'; } ?>">
		<a name="<?php echo $row['post_id']; ?>"></a>
		<div class="forum-post-author">
			<a href="profile.php?id=<?php echo $row['member_id']; ?>" class="title"><?php echo htmlspecialchars(get_display_name($row['member_id'])); ?></a><br />
			<?php print_profile_img($row['member_id']); ?>
		</div>

		<div class="forum-post-content">
			<h3><?php echo AT_Print(htmlspecialchars($row['subject'], ENT_COMPAT, "UTF-8"), 'forums_threads.subject'); ?></h3>
			<div>
				<div class="forum-post-ctrl">
					<?php if (authenticate(AT_PRIV_FORUMS, AT_PRIV_RETURN)): ?>
						<?php echo $reply_link; ?> | <a href="editor/edit_post.php?fid=<?php echo $row['forum_id'].SEP.'pid='.$row['post_id']; ?>"><?php echo _AT('edit'); ?></a> | <a href="forum/delete_thread.php?fid=<?php echo $row['forum_id'].SEP.'pid='.$row['post_id'].SEP.'ppid='.$row['parent_id'].SEP; ?>nest=1"><?php echo _AT('delete'); ?></a>
					<?php elseif (($row['member_id'] == $_SESSION['member_id']) && (($row['udate'] + $forum_info['mins_to_edit'] * 60) > time())): ?>
						<?php echo $reply_link; ?> | <a href="editor/edit_post.php?fid=<?php echo $row['forum_id'].SEP.'pid='.$row['post_id']; ?>"><?php echo _AT('edit'); ?></a> <span>(<?php echo _AT('edit_for_minutes', round((($row['udate'] + $forum_info['mins_to_edit'] * 60) - time())/60)); ?>)</span>
					<?php elseif ($_SESSION['valid_user']): ?>
						<?php echo $reply_link; ?>
					<?php endif; ?>
				</div>
				<p class="date">&nbsp;&nbsp;<?php echo AT_date(_AT('forum_date_format'), at_timezone($row['date']), AT_DATE_MYSQL_DATETIME); ?></p>

			</div>

			<div class="body">
				<p><?php echo AT_print(htmlspecialchars($row['body'], ENT_COMPAT, "UTF-8"), 'forums_threads.body'); ?></p>
			</div>
		</div>
	</li>
<?php
}
?>