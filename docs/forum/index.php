<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

define('AT_INCLUDE_PATH', '../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$fid = intval($_GET['fid']);

$_section[0][0] = _AT('discussions');
$_section[0][1] = 'discussions/';
$_section[1][0] = _AT('forums');
$_section[1][1] = 'forum/list.php';
$_section[2][0] = AT_print(get_forum($fid), 'forums.title');
$_section[2][1] = 'forum/';

if ($fid == 0) {
	Header('Location: ../discussions/');
	exit;
}

/* the last accessed field */
$last_accessed = array();
if ($_SESSION['valid_user']) {
	$sql	= "SELECT * FROM ".TABLE_PREFIX."forums_accessed WHERE member_id=$_SESSION[member_id]";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_array($result)) {
		$last_accessed[$row['post_id']] = $row['last_accessed'];
	}
}
if (authenticate(AT_PRIV_FORUMS, AT_PRIV_RETURN) && $_SESSION['prefs'][PREF_EDIT]) {
	$help[] = AT_HELP_FORUM_STICKY;
	$help[] = AT_HELP_FORUM_LOCK;
}
require(AT_INCLUDE_PATH.'header.inc.php');

echo '<h2>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/square-large-discussions.gif" width="42" height="38" hspace="2" vspace="2" border="0" alt="" class="menuimage" /> ';
}

echo '<a href="discussions/index.php?g=11">'._AC('discussions').'</a>';
echo '</h2>';
echo'<h3>';
if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
	echo '<img src="images/icons/default/forum-large.gif" width="42" height="38" border="0" alt="" class="menuimageh3" />';
}
echo AT_print(get_forum($fid), 'forums.title') . '</h3>';
print_help($help);
echo '<p><b><a href="forum/new_thread.php?fid='.$fid.SEP.'g=33">'._AT('new_thread').'</a></b></p>';

$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."forums_threads WHERE course_id=$_SESSION[course_id] AND parent_id=0 AND forum_id=$fid";
$result	= mysql_query($sql, $db);
$num_threads = mysql_fetch_assoc($result);
$num_threads = $num_threads['cnt'];

$num_per_page = 10;
if (!$_GET['page']) {
	$page = 1;
} else {
	$page = intval($_GET['page']);
}
$start = ($page-1)*$num_per_page;
$num_pages = ceil($num_threads/$num_per_page);

if ($_GET['col'] && $_GET['order']) {
	$col = $_GET['col'];
	$order = $_GET['order'];
}
else {
	$col = "last_comment";
	$order = "DESC";
}

$sql	= "SELECT *, last_comment + 0 AS stamp FROM ".TABLE_PREFIX."forums_threads WHERE course_id=$_SESSION[course_id] AND parent_id=0 AND forum_id=$fid ORDER BY sticky DESC, $col $order LIMIT $start,$num_per_page";
$result	= mysql_query($sql, $db);

if ($row = mysql_fetch_assoc($result)) {
	echo '<table cellspacing="1" cellpadding="0" border="0" class="bodyline" width="98%" align="center" summary="">';
	echo '<tr><th class="cyan" colspan="5">'._AT('forum_threads').'</th></tr>';
	echo '<tr>';
	echo '<th class="cat">'._AT('topic').' <a href="'.$_SERVER['PHP_SELF'].'?col=subject'.SEP.'fid='.$fid.SEP.'order=asc#list" title="'._AT('id_ascending').'"><img src="images/asc.gif" alt="'._AT('id_ascending').'" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="'.$_SERVER['PHP_SELF'].'?col=subject'.SEP.'order=desc'.SEP.'fid='.$fid.'#list" title="'._AT('id_descending').'"><img src="images/desc.gif" alt="'._AT('id_descending').'" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></th>';
	echo '<th class="cat">'._AT('replies').' <a href="'.$_SERVER['PHP_SELF'].'?col=num_comments'.SEP.'fid='.$fid.SEP.'order=asc#list" title="'._AT('id_ascending').'"><img src="images/asc.gif" alt="'._AT('id_ascending').'" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="'.$_SERVER['PHP_SELF'].'?col=num_comments'.SEP.'order=desc'.SEP.'fid='.$fid.'#list" title="'._AT('id_descending').'"><img src="images/desc.gif" alt="'._AT('id_descending').'" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></th>';
	echo '<th nowrap="nowrap" class="cat">'._AT('started_by').' <a href="'.$_SERVER['PHP_SELF'].'?col=login'.SEP.'fid='.$fid.SEP.'order=asc#list" title="'._AT('id_ascending').'"><img src="images/asc.gif" alt="'._AT('id_ascending').'" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="'.$_SERVER['PHP_SELF'].'?col=login'.SEP.'order=desc'.SEP.'fid='.$fid.'#list" title="'._AT('id_descending').'"><img src="images/desc.gif" alt="'._AT('id_descending').'" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></th>';
	echo '<th nowrap="nowrap" class="cat">'._AT('last_comment').' <a href="'.$_SERVER['PHP_SELF'].'?col=last_comment'.SEP.'fid='.$fid.SEP.'order=asc#list" title="'._AT('id_ascending').'"><img src="images/asc.gif" alt="'._AT('id_ascending').'" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a> <a href="'.$_SERVER['PHP_SELF'].'?col=last_comment'.SEP.'order=desc'.SEP.'fid='.$fid.'#list" title="'._AT('id_descending').'"><img src="images/desc.gif" alt="'._AT('id_descending').'" style="height:0.50em; width:0.83em" border="0" height="7" width="11" /></a></th>';
	$colspan = 4;
	if (authenticate(AT_PRIV_FORUMS, AT_PRIV_RETURN) && $_SESSION['prefs'][PREF_EDIT]) {
		echo '<th class="cat">&nbsp;</th>';
		$colspan++;
	}

	echo '</tr>';
	echo '<tr>';
	echo '<td class="row1" colspan="'.$colspan.'" align="right">'._AT('page').': ';

	for ($i=1; $i<=$num_pages; $i++) {
		if ($i == $page) {
			echo $i;
		} else {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?fid='.$fid.SEP.'page='.$i.'">'.$i.'</a>';
		}

		if ($i<$num_pages){
			echo ' <span class="spacer">|</span> ';
		}
	}
	
	echo '</td>';
	echo '</tr>';
	echo '<tr><td height="1" class="row2" colspan="5"></td></tr>';
	echo '<tr><td height="1" class="row2" colspan="'.$colspan.'"></td></tr>';

	$current_thread = $row['thread_id'];
	do {
		$row['subject'] = AT_print($row['subject'], 'forums_threads.subject');

		/* crop the subject, if needed */
		if (strlen($row['subject']) > 28) {
			$row['subject'] = substr($row['subject'], 0, 25).'...';
		}
		echo '<tr>';
		echo '<td class="row1" width="60%">';
	
		if ($_SESSION['valid_user']) {
			if ($row['stamp'] > $last_accessed[$row['post_id']]) {
				echo '<i style="color: green; font-weight: bold; font-size: .7em;" title="'._AT('new_thread').'">'._AT('new').'</i> ';
			}
		}

		if ($row['num_comments'] > 10) {
			echo '<i style="color: red; font-weight: bold; font-size: .7em;" title="'._AT('hot_thread').'">'._AT('hot').'</i> ';
		}

		if ($row['locked'] != 0) {
			echo '<img src="images/topic_lock.gif" alt="'._AT('thread_locked').'" class="menuimage3" title="'._AT('thread_locked').'" /> ';
		}
		
		if ($row['sticky'] != 0) {
			echo '<img src="images/forum/topic_stick.gif" alt="'._AT('sticky_thread').'" class="menuimage3"  title="'._AT('sticky_thread').'" /> ';
		}
		
		if ($row['locked'] != 1) {
			echo '<a href="forum/view.php?fid='.$fid.SEP.'pid='.$row['post_id'].SEP.'g=35">'.$row['subject'].'</a>';

			if ($row['locked'] == 2) {
				echo ' <i class="spacer">('._AT('post_lock').')</i>';
			}
		} else {
			echo $row['subject'].' <i class="spacer">('._AT('read_lock').')</i>';
		}

		/* print page numbers */
		$num_pages_2 = ceil(($row['num_comments']+1)/$num_per_page);

		if ($num_pages_2 > 1) {
			echo ' <small class="spacer">( Page: ';
			for ($i=2; $i<=$num_pages_2; $i++) {
				echo '<a href="forum/view.php?fid='.$fid.SEP.'pid='.$row['post_id'].SEP.'page='.$i.'">'.$i.'</a>';

				if ($i<$num_pages_2){
					echo ' | ';
				}
			}
			echo ' )</small>';
		}

		echo '</td>';

		echo '<td class="row1" width="10%" align="center">'.$row['num_comments'].'</td>';

		echo '<td class="row1" width="10%"><a href="send_message.php?l='.$row['member_id'].'">'.AT_print($row['login'], 'members.login').'</a></td>';

		echo '<td class="row1" width="20%" align="right" nowrap="nowrap"><small>';
		echo AT_date(_AT('forum_date_format'), $row['last_comment'], AT_DATE_MYSQL_DATETIME);
		echo '</small></td>';

		if (authenticate(AT_PRIV_FORUMS, AT_PRIV_RETURN) && $_SESSION['prefs'][PREF_EDIT]) {
			echo '<td class="row1" nowrap="nowrap">';
			echo ' <a href="forum/stick.php?fid='.$fid.SEP.'pid='.$row['post_id'].'"><img src="images/forum/sticky.gif" border="0" class="menuimage6"  alt="'._AT('sticky_thread').'" title="'._AT('sticky_thread').'" /></a> ';

			if ($row['locked'] != 0) {
				echo '<a href="forum/lock_thread.php?fid='.$fid.SEP.'pid='.$row['post_id'].SEP.'unlock='.$row['locked'].'"><img src="images/unlock.gif" border="0"  class="menuimage6" alt="'._AT('unlock_thread').'" title="'._AT('unlock_thread').'"/></a>';
			} else {
				echo '<a href="forum/lock_thread.php?fid='.$fid.SEP.'pid='.$row['post_id'].'"><img src="images/lock.gif" border="0" alt="'._AT('lock_thread').'"   class="menuimage6" title="'._AT('lock_thread').'"/></a>';
			}
			echo ' <a href="forum/delete_thread.php?fid='.$fid.SEP.'pid='.$row['post_id'].SEP.'ppid=0"><img src="images/icon_delete.gif" border="0" alt="'._AT('delete_thread').'"   class="menuimage6" title="'._AT('delete_thread').'"/></a>';
			
			echo '</td>';
		}
		echo '</tr>';
		echo '<tr><td height="1" class="row2" colspan="'.$colspan.'"></td></tr>';

	} while ($row = mysql_fetch_assoc($result));

	echo '<tr><td height="1" class="row2" colspan="'.$colspan.'"></td></tr>';
	echo '<tr>';
	echo '<td class="row1" colspan="'.$colspan.'" align="right">'._AT('page').': ';

	for ($i=1; $i<=$num_pages; $i++) {
		if ($i == $page) {
			echo $i;
		} else {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?fid='.$fid.SEP.'page='.$i.'">'.$i.'</a>';
		}

		if ($i<$num_pages){
			echo ' <span class="spacer">|</span> ';
		}
	}
	
	echo '</td>';
	echo '</tr>';

	echo '</table>';

} else {
	$infos[]=AT_INFOS_NO_POSTS_FOUND;
	print_infos($infos);
}

echo '<br />';
require(AT_INCLUDE_PATH.'footer.inc.php');
?>