<?php
/****************************************************************************/
/* ATutor																	*/
/****************************************************************************/
/* Copyright (c) 2002-2010                                                  */
/* Inclusive Design Institute                                               */
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
* @see     is_shared_forum()
* @author  Heidi Hazelton
* @author  Joel Kronenberg
*/
function get_forums($course) {
	if ($course) {
		$sql	= "SELECT F.*, DATE_FORMAT(F.last_post, '%%Y-%%m-%%d %%H:%%i:%%s') AS last_post FROM %sforums_courses FC INNER JOIN %sforums F USING (forum_id) WHERE FC.course_id=%d GROUP BY FC.forum_id ORDER BY F.title";
	    $rows_forums = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $course));
	} else {
		$sql	= "SELECT F.*, FC.course_id, DATE_FORMAT(F.last_post, '%%Y-%%m-%%d %%H:%%i:%%s') AS last_post FROM %sforums_courses FC INNER JOIN %sforums F USING (forum_id) GROUP BY FC.forum_id ORDER BY F.title";
	    $rows_forums = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX));
	}

	// 'nonshared' forums are always listed first:
	$forums['nonshared'] = array();
	$forums['shared']    = array();
	$forums['group']     = array();

	foreach($rows_forums as $row){
		// for each forum, check if it's shared or not:

		if (is_shared_forum($row['forum_id'])) {
			$forums['shared'][] = $row;
		} else {
			$forums['nonshared'][] = $row;
		}
	}
		
	// retrieve the group forums if course is given

	if (!$_SESSION['groups'] || !$course) {
		return $forums;
	}

	// filter out the groups that do not belong to the given course
	foreach ($_SESSION['groups'] as $group) {
		$sql = "SELECT * FROM %sgroups g, %sgroups_types gt
		         WHERE g.group_id=%d
		           AND g.type_id = gt.type_id
		           AND gt.course_id=%d";
		$rows_forums = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $group, $course));
		if($rows_forums > 0){
			$groups .= $group .',';
		}
	}

	if (isset($groups)) {
		$groups = substr($groups, 0, -1);
		$sql = "SELECT F.*, G.group_id FROM %sforums_groups G 
		         INNER JOIN %sforums F 
		         USING (forum_id) 
		         WHERE G.group_id IN (%s) 
		         ORDER BY F.title";
		$rows_gforums = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $groups));

		foreach($rows_gforums as $row){
			$row['title'] = get_group_title($row['group_id']);
			$forums['group'][] = $row;
		}
	}
	return $forums;	
}

/**
* Returns true/false whether or not this forum is shared.
* @access  public
* @param   integer $forum_id	id of the forum
* @return  boolean				true if this forum is shared, false otherwise
* @author  Joel Kronenberg
*/
function is_shared_forum($forum_id) {
	$sql = "SELECT COUNT(*) AS cnt FROM %sforums_courses WHERE forum_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $forum_id), TRUE);

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
* @author  Heidi Hazelton
*/
function get_forum($forum_id, $course = '') {

	if (!empty($course)) {
	
		$sql	= "SELECT * FROM %sforums_courses fc, %sforums f WHERE (fc.course_id=%d OR fc.course_id=0) AND fc.forum_id=f.forum_id and fc.forum_id=%d ORDER BY title";
		$forum = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $course, $forum_id), TRUE);

	} else if (empty($course)) {  	//only admins should be retrieving forums w/o a course!  add this check
	
		$sql = "SELECT * FROM %sforums WHERE forum_id=%d";
		$forum = queryDB($sql, array(TABLE_PREFIX, $forum_id), TRUE);
		
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
* @author  Heidi Hazelton
*/
function valid_forum_user($forum_id) {

	$sql	= "SELECT forum_id FROM %sforums_courses WHERE (course_id=%d OR course_id=0) AND forum_id=%d";
	$row = queryDB($sql, array(TABLE_PREFIX, $_SESSION['course_id'], $forum_id), TRUE);

	if (count($row) == 0) {
		// not a course forum, let's check group:
		if (!empty($_SESSION['groups'])){
			$groups = implode(',', $_SESSION['groups']);
			
			$sql	= "SELECT forum_id FROM %sforums_groups WHERE group_id IN (%s) AND forum_id=%d";
			$row= queryDB($sql, array(TABLE_PREFIX, $groups, $forum_id));
			
			if(count($row) >0){
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
* @author  Heidi Hazelton
*/
function add_forum($forum_prop) {

	$sql	= "INSERT INTO %sforums VALUES (NULL,'%s', '%s', 0, 0, NOW(), %d)";
	$result = queryDB($sql, array(TABLE_PREFIX, $forum_prop['title'], $forum_prop['body'], $forum_prop['edit']));

	$sql	= "INSERT INTO ".TABLE_PREFIX."forums_courses VALUES (LAST_INSERT_ID(),  $_SESSION[course_id])";
	$result = queryDB($sql, array(TABLE_PREFIX, $_SESSION[course_id]));
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
function edit_forum($forum_prop) {

	$sql	= "UPDATE %sforums SET title='%s', description='%s', last_post=last_post, mins_to_edit=%d WHERE forum_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $forum_prop['title'], $forum_prop['body'], $forum_prop['edit'], $forum_prop['fid']));
	
	return;
}

/**
* Deletes a forum (checks if its shared).
* Assumes the forum is not shared.
* Assumes the user has the priv to delete this forum.
* @access  public
* @param   array $_POST			add-forum form variables
* @author  Heidi Hazelton
*/
function delete_forum($forum_id) {

	$sql	= "SELECT post_id FROM %sforums_threads WHERE forum_id=%d";
	$rows_threads = queryDB($sql, array(TABLE_PREFIX, $forum_id));
	
	foreach($rows_threads as $row){

		$sql	 = "DELETE FROM %sforums_accessed WHERE post_id=%d";
		$result2 = queryDB($sql, array(TABLE_PREFIX, $row['post_id']));

	}

	$sql	= "DELETE FROM %sforums_subscriptions WHERE forum_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $forum_id));
	
	$sql    = "DELETE FROM %sforums_threads WHERE forum_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $forum_id));
	
	$sql    = "DELETE FROM %sforums_courses WHERE forum_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $forum_id));

	$sql    = "DELETE FROM %sforums WHERE forum_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $forum_id));

	$sql    = "DELETE FROM %scontent_forums_assoc WHERE forum_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $forum_id));

	$sql = "OPTIMIZE TABLE %sforums_threads";
	$result = queryDB($sql, array(TABLE_PREFIX));
}

function print_entry($row) {
	global $page, $system_courses, $forum_info;
	static $counter;
	$counter++;
	?>
	<script type="text/javascript">
	/*
	// script to control popin reply/edit boxs, disabled for now due to ID conflicts
	jQuery(document).ready( function () { 
	        $("a#reply-<?php echo $row['post_id']; ?>").click(function() {
            $("div#reply-<?php echo $row['post_id']; ?>").toggle('slow');
            return false;
            });
	        $("a#reply-<?php echo $row['post_id']; ?>").keypress(function(e) {
	            var code = e.keyCode || e.which;
	            if(code == 13 || code == 32) { 
                    $("div#reply-<?php echo $row['post_id']; ?>").toggle('slow');
                    $("div#reply-<?php echo $row['post_id']; ?> #subject" ).focus();
                    return false;
                }

            });
	        $("a#edit-<?php echo $row['post_id']; ?>").click(function() {
            $("div#edit-<?php echo $row['post_id']; ?>").toggle('slow');
            return false;
            });
	        $("a#edit-<?php echo $row['post_id']; ?>").keypress(function(e) {
	            var code = e.keyCode || e.which;
	            if(code == 13 || code == 32) { 
                    $("div#edit-<?php echo $row['post_id']; ?>").toggle('slow');
                    $("div#edit-<?php echo $row['post_id']; ?> #subject" ).focus();
                    return false;
                }

            });
        }); 
        */
	</script>
	<?php
    $reply_link = '<a href="#" id="reply-'.$row['post_id'].'">';
	$reply_link = '<a href="mods/_standard/forums/forum/view.php?fid='.$row['forum_id'].SEP.'pid=';
	if ($row['parent_id'] == 0) {
		$reply_link .= $row['post_id'];
	} else {
		  $reply_link .= $row['parent_id'];
	}
	//$reply_link .= SEP.'reply='.$row['post_id'].SEP.'page='.$page.'#post" >';
	$reply_link .= '#post" onClick="javascript:document.getElementById(\'subject\').value = \'Re: '.$row['subject'].'\'; " >';
	$reply_link .='<img src="images/forum/forum_reply.png" alt="'._AT('reply').'" title="'._AT('reply').'"/></a>';

?>

	<li class="<?php if ($counter %2) { echo 'odd'; } else { echo 'even'; } ?>">
		<a name="<?php echo $row['post_id']; ?>"></a>
		<div class="forum-post-author">
			<a href="profile.php?id=<?php echo $row['member_id']; ?>" class="title"><?php echo htmlspecialchars(get_display_name($row['member_id'])); ?></a><br />
			<?php print_profile_img($row['member_id']); ?>
		</div>

		<div class="forum-post-content">
			<h3><?php echo AT_print($row['subject'], 'forums_threads.subject'); ?></h3>
			<div>
				<div class="forum-post-ctrl">
					<?php if (authenticate(AT_PRIV_FORUMS, AT_PRIV_RETURN)): ?>
						<?php echo $reply_link; ?>  
						<a href="mods/_standard/forums/edit_post.php?fid=<?php echo $row['forum_id'].SEP.'pid='.$row['post_id']; ?>"><img src="images/forum/forum_edit.png" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>"/></a>  <a href="mods/_standard/forums/forum/delete_thread.php?fid=<?php echo $row['forum_id'].SEP.'pid='.$row['post_id'].SEP.'ppid='.$row['parent_id'].SEP; ?>nest=1"><img src="images/forum/forum_delete.png" alt="<?php echo _AT('delete'); ?>" title="<?php echo _AT('delete'); ?>"/></a>
					    <!-- <?php echo $reply_link; ?>  <a href="#" id="edit-<?php echo $row['post_id']; ?>"><img src="images/forum/forum_edit.png" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>"/></a>  <a href="mods/_standard/forums/forum/delete_thread.php?fid=<?php echo $row['forum_id'].SEP.'pid='.$row['post_id'].SEP.'ppid='.$row['parent_id'].SEP; ?>nest=1"><img src="images/forum/forum_delete.png" alt="<?php echo _AT('delete'); ?>" title="<?php echo _AT('delete'); ?>"/></a> -->
				
					<?php elseif (($row['member_id'] == $_SESSION['member_id']) && (($row['udate'] + $forum_info['mins_to_edit'] * 60) > time())): ?>
					<?php echo $reply_link; ?>  <a href="mods/_standard/forums/edit_post.php?fid=<?php echo $row['forum_id'].SEP.'pid='.$row['post_id']; ?>"><img src="images/forum/forum_edit.png" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>"></a> <span>(<?php echo _AT('edit_for_minutes', round((($row['udate'] + $forum_info['mins_to_edit'] * 60) - time())/60)); ?>)</span> 
					<!--	<?php echo $reply_link; ?>  <a href="#" id="edit-<?php echo $row['post_id']; ?>"><img src="images/forum/forum_edit.png" alt="<?php echo _AT('edit'); ?>" title="<?php echo _AT('edit'); ?>"></a> <span>(<?php echo _AT('edit_for_minutes', round((($row['udate'] + $forum_info['mins_to_edit'] * 60) - time())/60)); ?>)</span> -->
					<?php elseif ($_SESSION['valid_user'] == true): ?>
						<?php echo $reply_link; ?>
					<?php endif; ?>
				</div>
				<p class="date">&nbsp;&nbsp;<?php echo AT_date(_AT('forum_date_format'), $row['date'], AT_DATE_MYSQL_DATETIME); ?></p>

			</div>

			<div class="body">
				<p><?php echo apply_customized_format(AT_print($row['body'], 'forums_threads.body')); ?></p>
			</div>
		</div>
	<?php
	// popin edit/reply forms / disabled until ID conflict issue can be resolved
	//echo '<div class="forum_reply" id="reply-'.$row['post_id'].'">';
	//require(AT_INCLUDE_PATH.'../mods/_standard/forums/html/new_thread.inc.php');
	//echo '</div>';
	//echo '<div class="forum_edit" id="edit-'.$row['post_id'].'">';
	//require(AT_INCLUDE_PATH.'../mods/_standard/forums/edit_post.php');
	//echo '</div>';
	?>
	</li>
<?php
}
?>