<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
require(AT_INCLUDE_PATH.'lib/forum_codes.inc.php');

$fid = intval($_GET['fid']);

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/';
$_section[1][0] = get_forum($fid);
$_section[1][1] = 'forum/?fid='.$_GET['fid'];
$_section[2][0] = _AT('view_post');


function print_entry($row) {
	global $page;

	echo '<tr>';
	echo '<td class="row1"><a name="'.$row['post_id'].'"></a><p><b>'.$row['subject'].'</b>';
	if ($_SESSION['is_admin'] && $_SESSION['prefs'][PREF_EDIT]) {
	
	print_editor(_AT('edit'),'editor/edit_post.php?fid='.$row['forum_id'].SEP.'pid='.$row['post_id'], _AT('delete'),'forum/delete_thread.php?fid='.$row['forum_id'].SEP.'pid='.$row['post_id'].SEP.'ppid='.$row['parent_id']);
		//echo ' <span class="bigspacer">( <a href="forum/delete_thread.php?fid='.$row['forum_id'].SEP.'pid='.$row['post_id'].SEP.'ppid='.$row['parent_id'].'"><img src="images/icon_delete.gif" border="0" alt="'._AT('delete_thread').'"  title="'._AT('delete_thread').'" class="menuimage6"/></a> | <a href="editor/edit_post.php?fid='.$row['forum_id'].SEP.'pid='.$row['post_id'].'">'._AT('edit').'</a> )</span>';
	}
	echo ' <a href="forum/view.php?fid='.$row['forum_id'].SEP.'pid=';

	if ($row['parent_id'] == 0) {
		echo $row['post_id'];
	} else {
		echo $row['parent_id'];
	}
	echo SEP.'reply='.$row['post_id'].SEP.'page='.$page.SEP.'g=34#post" >'._AT('reply').'</a>';
	echo '<br />';

	$date = AT_date(_AT('forum_date_format'), $row['date'], AT_DATE_MYSQL_DATETIME);

	echo '<span class="bigspacer">'._AT('posted_by').' <a href="send_message.php?l='.$row['member_id'].'">'.$row['login'].'</a> '._AT('posted_on').' '.$date.'</span><br />';
	echo format_final_output(' '.$row['body'].' ');
	echo '</p>';
	echo '</td>';
	echo '</tr>';
	echo '<tr><td height="1" class="row2" colspan="'.$colspan.'"></td></tr>';
}

if ($_REQUEST['reply']) {
	$onload = 'onload="document.form.subject.focus()"';
}
require(AT_INCLUDE_PATH.'header.inc.php');
//echo '<a href="discussions/?g=11"><h2>'. _AT('discussions').'</h2></a>';
	echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/square-large-discussions.gif" width="42" height="38" border="0" alt="" class="menuimage" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="discussions/?g=11">'._AT('discussions').'</a>';
	}
	echo '</h2>';
	
	echo'<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<img src="images/icons/default/forum-large.gif" width="42" height="38" border="0" alt="" class="menuimageh3" />';
}
echo '<a href="forum/?fid='.$fid.SEP.'g=11">'.get_forum($fid).'</a>';

echo '</h3>';
$pid = intval($_GET['pid']);

if ($_SESSION['valid_user']) {
	$sql = "INSERT INTO ".TABLE_PREFIX."forums_accessed VALUES ($pid, $_SESSION[member_id], NOW())";
	$result = mysql_query($sql, $db);
	if (!$result) {
		$sql = "UPDATE ".TABLE_PREFIX."forums_accessed SET last_accessed=NOW() WHERE post_id=$pid AND member_id=$_SESSION[member_id]";
		$result = mysql_query($sql, $db);
	}
}

$num_per_page = 10;
if (!$_GET['page']) {
	$page = 1;
} else {
	$page = intval($_GET['page']);
}
$start = ($page-1)*$num_per_page;
	
/* get the first thread first */
$sql	= "SELECT * FROM ".TABLE_PREFIX."forums_threads WHERE course_id=$_SESSION[course_id] AND post_id=$pid AND forum_id=$fid";
$result	= mysql_query($sql, $db);

if ($row = mysql_fetch_array($result)) {
	$num_threads = $row['num_comments']+1;
	$num_pages = ceil($num_threads/$num_per_page);
	$locked = $row['locked'];
	if ($locked == 1) {
		echo '<p><b>'._AT('lock_no_read1').'</b></p>';
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	echo '<h2>'.$row['subject'];
	echo ' - <a href="forum/view.php?fid='.$row['forum_id'].SEP.'pid='.$pid;
	echo SEP.'page='.$page.SEP.'g=34#post">'._AT('reply').'</a>';

	echo '</h2>';

	$parent_name = $row['subject'];


	echo '<table border="0" cellpadding="0" cellspacing="1" width="97%" class="bodyline" align="center" summary="">';
	echo '<tr>';
	echo '<td class="row1" align="right">'._AT('page').': ';
	for ($i=1; $i<=$num_pages; $i++) {
		if ($i == $page) {
			echo $i;
		} else {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?fid='.$fid.SEP.'pid='.$pid.SEP.'page='.$i.'">'.$i.'</a>';
		}

		if ($i<$num_pages){
			echo ' <span class="spacer">|</span> ';
		}
	}
	echo '</td>';
	echo '</tr>';
	echo '<tr><td height="1" class="row2"></td></tr>';
	echo '<tr><td height="1" class="row2"></td></tr>';
	
	if ($page == 1) {
		print_entry($row);
		$subject   = $row['subject'];
		if ($_GET['reply'] == $row['post_id']) {
			$saved_post = $row;
		}
		$num_per_page--;
	} else {
		$start--;
	}
	$sql	= "SELECT * FROM ".TABLE_PREFIX."forums_threads WHERE course_id=$_SESSION[course_id] AND parent_id=$pid AND forum_id=$fid ORDER BY date ASC LIMIT $start, $num_per_page";
	$result	= mysql_query($sql, $db);

	while ($row = mysql_fetch_array($result)) {
		print_entry($row);
		$subject   = $row['subject'];
		if ($_GET['reply'] == $row['post_id']) {
			$saved_post = $row;
		}
	}
	echo '<tr><td height="1" class="row2"></td></tr>';
	echo '<tr>';
	echo '<td class="row1" align="right">'._AT('page').': ';
	for ($i=1; $i<=$num_pages; $i++) {
		if ($i == $page) {
			echo $i;
		} else {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?fid='.$fid.SEP.'pid='.$pid.SEP.'page='.$i.'">'.$i.'</a>';
		}

		if ($i<$num_pages){
			echo ' <span class="spacer">|</span> ';
		}
	}
	echo '</td>';
	echo '</tr>';
	echo '</table>';

	$parent_id = $pid;
	$body	   = '';
	if (substr($subject,0,3) != 'Re:') {
		$subject = 'Re: '.$subject;
	}
	
	if ($_SESSION['valid_user']) {
		$sql	= "SELECT * FROM ".TABLE_PREFIX."forums_subscriptions WHERE post_id=$_GET[pid] AND member_id=$_SESSION[member_id]";
		$result = mysql_query($sql, $db);

		if ($row = mysql_fetch_array($result)) {
			echo '<p><a href="forum/subscribe.php?fid='.$fid.SEP.'pid='.$_GET['pid'].SEP.'us=1">'._AT('unsubscribe').'</a></p>';

			$subscribed = true;
		} else {
			echo '<p><a href="forum/subscribe.php?fid='.$fid.SEP.'pid='.$_GET['pid'].'">'._AT('subscribe').'</a></p>';
		}
	}

	if ($locked == 0) {
		require(AT_INCLUDE_PATH.'lib/new_thread.inc.php');
	} else {
		echo '<p><b>'._AT('lock_no_post1').'</b></p>';
	}
} else {
	echo _AT('no_post');
}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>
