<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."forums_threads WHERE parent_id=0 AND forum_id=$fid";
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
	$col = 'last_comment';
	$order = 'DESC';
}

$sql	= "SELECT *, last_comment + 0 AS stamp FROM ".TABLE_PREFIX."forums_threads WHERE parent_id=0 AND forum_id=$fid AND member_id>0 ORDER BY sticky DESC, $col $order LIMIT $start,$num_per_page";
$result	= mysql_query($sql, $db);

if (!($row = mysql_fetch_assoc($result))) {
	$msg->printInfos('NO_POSTS_FOUND');
	return;
}
?>
<table class="data static" summary="" rules="cols" style="width: 90%;">
<thead>
<tr>
	<th><?php echo _AT('topic').' <a href="'.$_SERVER['PHP_SELF'].'?col=subject'.SEP.'fid='.$fid.SEP.'order=asc#list" title="'._AT('id_ascending').'"><img src="images/asc.gif" alt="'._AT('id_ascending').'" border="0" height="7" width="11" /></a> <a href="'.$_SERVER['PHP_SELF'].'?col=subject'.SEP.'order=desc'.SEP.'fid='.$fid.'#list" title="'._AT('id_descending').'"><img src="images/desc.gif" alt="'._AT('id_descending').'" border="0" height="7" width="11" /></a></th>'; ?>

	<th><?php echo _AT('replies').' <a href="'.$_SERVER['PHP_SELF'].'?col=num_comments'.SEP.'fid='.$fid.SEP.'order=asc#list" title="'._AT('id_ascending').'"><img src="images/asc.gif" alt="'._AT('id_ascending').'" border="0" height="7" width="11" /></a> <a href="'.$_SERVER['PHP_SELF'].'?col=num_comments'.SEP.'order=desc'.SEP.'fid='.$fid.'#list" title="'._AT('id_descending').'"><img src="images/desc.gif" alt="'._AT('id_descending').'" border="0" height="7" width="11" /></a></th>'; ?>

	<th><?php echo _AT('started_by').' <a href="'.$_SERVER['PHP_SELF'].'?col=login'.SEP.'fid='.$fid.SEP.'order=asc#list" title="'._AT('id_ascending').'"><img src="images/asc.gif" alt="'._AT('id_ascending').'" border="0" height="7" width="11" /></a> <a href="'.$_SERVER['PHP_SELF'].'?col=login'.SEP.'order=desc'.SEP.'fid='.$fid.'#list" title="'._AT('id_descending').'"><img src="images/desc.gif" alt="'._AT('id_descending').'" border="0" height="7" width="11" /></a></th>'; ?>

	<th><?php echo _AT('last_comment'); ?> <a href="<?php echo $_SERVER['PHP_SELF'].'?col=last_comment'.SEP.'fid='.$fid.SEP; ?>order=asc#list" title="<?php echo _AT('id_ascending'); ?>"><img src="images/asc.gif" alt="<?php echo _AT('id_ascending'); ?>" border="0" height="7" width="11" /></a> <a href="<?php echo $_SERVER['PHP_SELF'].'?col=last_comment'.SEP.'order=desc'.SEP.'fid='.$fid; ?>#list" title="<?php echo _AT('id_descending'); ?>"><img src="images/desc.gif" alt="<?php echo _AT('id_descending'); ?>" border="0" height="7" width="11" /></a></th>
<?php
	$colspan = 4;
	if (authenticate(AT_PRIV_FORUMS, AT_PRIV_RETURN) && $_SESSION['prefs'][PREF_EDIT]) {
		echo '<th class="cat">&nbsp;</th>';
		$colspan++;
	}

	echo '</tr>';
	echo '</thead>';
	echo '<tfoot>';
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
	echo '</tfoot>';
	echo '<tbody>';

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
			if ($row['stamp'] > $last_accessed[$row['post_id']]['last_accessed']) {
				echo '<i style="color: green; font-weight: bold; font-size: .7em;" title="'._AT('new_thread').'">'._AT('new').'</i> ';
			}
		}

		if ($row['num_comments'] > 10) {
			echo '<em style="color: red; font-weight: bold; font-size: .7em;" title="'._AT('hot_thread').'">'._AT('hot').'</em> ';
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
			echo ' )</small> ';
		}
		if ($_SESSION['enroll'] && !$row['locked']) {
			if (isset($last_accessed[$row['post_id']]) && $last_accessed[$row['post_id']]['subscribe']){
				echo  ' <br /><small><a href="forum/subscribe.php?us=1'.SEP.'pid='.$row['post_id'].SEP.'fid='.$fid.SEP.'t=1">('._AT('unsubscribe1').')</a></small>';
			} else {
				echo  ' <br /><small><a href="forum/subscribe.php?pid='.$row['post_id'].SEP.'fid='.$fid.SEP.'t=1">('._AT('subscribe1').')</a></small>';
			}
		}
		echo '</td>';

		echo '<td class="row1" width="10%" align="center">'.$row['num_comments'].'</td>';

		echo '<td class="row1" width="10%"><a href="users/send_message.php?l='.$row['member_id'].'">'.AT_print($row['login'], 'members.login').'</a></td>';

		echo '<td class="row1" width="20%" align="right" nowrap="nowrap">';
		echo AT_date(_AT('forum_date_format'), $row['last_comment'], AT_DATE_MYSQL_DATETIME);
		echo '</td>';

		if (authenticate(AT_PRIV_FORUMS, AT_PRIV_RETURN) && $_SESSION['prefs'][PREF_EDIT]) {
			echo '<td class="row1" nowrap="nowrap">';
			echo ' <a href="forum/stick.php?fid='.$fid.SEP.'pid='.$row['post_id'].'"><img src="images/forum/sticky.gif" border="0" alt="'._AT('sticky_thread').'" title="'._AT('sticky_thread').'" /></a> ';

			if ($row['locked'] != 0) {
				echo '<a href="forum/lock_thread.php?fid='.$fid.SEP.'pid='.$row['post_id'].SEP.'unlock='.$row['locked'].'"><img src="images/unlock.gif" border="0"  alt="'._AT('unlock_thread').'" title="'._AT('unlock_thread').'"/></a>';
			} else {
				echo '<a href="forum/lock_thread.php?fid='.$fid.SEP.'pid='.$row['post_id'].'"><img src="images/lock.gif" border="0" alt="'._AT('lock_thread').'"   title="'._AT('lock_thread').'"/></a>';
			}
			echo ' <a href="forum/delete_thread.php?fid='.$fid.SEP.'pid='.$row['post_id'].SEP.'ppid=0"><img src="images/icon_delete.gif" border="0" alt="'._AT('delete_thread').'" title="'._AT('delete_thread').'"/></a>';
			
			echo '</td>';
		}
		echo '</tr>';

	} while ($row = mysql_fetch_assoc($result));
	echo '</tbody>';
	echo '</table>';

?>