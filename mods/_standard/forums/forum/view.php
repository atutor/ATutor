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

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

$fid = intval($_REQUEST['fid']);
$_REQUEST['reply'] = isset($_REQUEST['reply']) ? $_REQUEST['reply'] : '';


if (!isset($_REQUEST['fid']) || !$fid ) {
	header('Location: list.php');
	exit;
}

require(AT_INCLUDE_PATH.'../mods/_standard/forums/lib/forums.inc.php'); // for print_entry et al

if (!valid_forum_user($fid)) {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->printErrors('FORUM_DENIED');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

// set default thread display order to ascending
if (!isset($_SESSION['thread_order']))
{
	$_SESSION['thread_order'] = 'a';
}
else if (isset($_GET['order']))
{
	$_SESSION['thread_order'] = $_GET['order'];
}

$forum_info = get_forum($fid);

$_pages[url_rewrite('mods/_standard/forums/forum/index.php?fid='.$fid)]['title']    = get_forum_name($fid);
$_pages[url_rewrite('mods/_standard/forums/forum/index.php?fid='.$fid)]['parent']   = 'mods/_standard/forums/forum/list.php';
$_pages[url_rewrite('mods/_standard/forums/forum/index.php?fid='.$fid)]['children'] = array(url_rewrite('mods/_standard/forums/forum/new_thread.php?fid='.$fid), 'search.php?search_within=forums');

$_pages[url_rewrite('mods/_standard/forums/forum/new_thread.php?fid='.$fid)]['title_var'] = 'new_thread';
$_pages[url_rewrite('mods/_standard/forums/forum/new_thread.php?fid='.$fid)]['parent']    = url_rewrite('mods/_standard/forums/forum/index.php?fid='.$fid);

$_pages['mods/_standard/forums/forum/view.php']['parent'] = url_rewrite('mods/_standard/forums/forum/index.php?fid='.$fid);
$_pages['search.php?search_within=forums']['title_var'] = 'search';
$_pages['search.php?search_within=forums']['parent']    = url_rewrite('mods/_standard/forums/forum/index.php');

if ($_REQUEST['reply']) {
	$onload = 'document.form.subject.focus();';
}

$pid = intval($_GET['pid']);

$num_per_page = 10;
if (!$_GET['page']) {
	$page = 1;
} else {
	$page = (intval($_GET['page'])>0)?(intval($_GET['page'])):1;
}
$start = ($page-1)*$num_per_page;
	
/* get the first thread first */
$sql	= "SELECT *, DATE_FORMAT(date, '%%Y-%%m-%%d %%H:%%i:%%s') AS date, UNIX_TIMESTAMP(date) AS udate FROM %sforums_threads WHERE post_id=%d AND forum_id=%d";
$post_row	= queryDB($sql, array(TABLE_PREFIX, $pid, $fid), TRUE);

if(count($post_row) == 0){
	require(AT_INCLUDE_PATH.'header.inc.php');
	$_pages['mods/_standard/forums/forum/view.php']['title']  = _AT('no_post');

	echo _AT('no_post');
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

$_pages['mods/_standard/forums/forum/view.php']['title']  = $post_row['subject'];

require(AT_INCLUDE_PATH.'header.inc.php');

?>
	<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>#post" style="border: 0px;"><img src="<?php echo $_base_path; ?>images/clr.gif" height="1" width="1" border="0" alt="<?php echo _AT('reply'); ?>" /></a>
<?php
	/**
	* Jacek M.
 	* Protect data consistency
 	* Make sure the pid we are inserting is actually a thread post_id, otherwise we get dangling pointers
 	* in the case of injection
	*/

	if ($_SESSION['valid_user'] === true) {

		$sql2 = "SELECT member_id FROM %sforums_accessed WHERE member_id = %d AND post_id =%d";
		$row = queryDB($sql2, array(TABLE_PREFIX, $_SESSION[member_id], $pid));
		
		if(count($row) > 0){
			$sql2 = "UPDATE %sforums_accessed SET last_accessed=NOW() WHERE post_id=%d AND member_id=%d";
			$result = queryDB($sql2, array(TABLE_PREFIX, $pid, $_SESSION['member_id']));
		} else {
			$sql2 = "INSERT INTO %sforums_accessed VALUES ($pid, $_SESSION[member_id], NOW(), 0)";
		    $result = queryDB($sql2, array(TABLE_PREFIX));
		}
	}
	
	$num_threads = $post_row['num_comments']+1;
	$num_pages = ceil($num_threads/$num_per_page);
	$locked = $post_row['locked'];
	if ($locked == 1) {
		echo '<p><strong>'._AT('lock_no_read1').'</strong></p>';
		require(AT_INCLUDE_PATH.'footer.inc.php');
		exit;
	}

	$parent_name = $post_row['subject'];
	echo '<ul class="forum-thread">';
	print_entry($post_row);
	$subject = $post_row['subject'];
	if ($_GET['reply'] == $post_row['post_id']) {
		$saved_post = $post_row;
	}
	echo '
	    </ul>
	  <div class="forum-paginator">&nbsp;
	  </div><br />';

	$sql	= "SELECT *, DATE_FORMAT(date, '%%Y-%%m-%%d %%H-%%i:%%s') AS date, UNIX_TIMESTAMP(date) AS udate FROM %sforums_threads WHERE parent_id=%d AND forum_id=%d ORDER BY date ";


	if ($_SESSION['thread_order'] == 'a')
		$sql .= "ASC LIMIT %d, %d";
	else
		$sql .= "DESC LIMIT %d, %d";

	$rows_threads	= queryDB($sql, array(TABLE_PREFIX, $pid, $fid, $start, $num_per_page));
	
	if(count($rows_threads) > 0)
	{
		echo '<div class="forum-paginator">';
		echo '<div style="float:right;">';
		if ($_SESSION['thread_order'] == 'a')
			echo '<a href="'.url_rewrite($_SERVER['PHP_SELF'].'?fid='.$fid.SEP.'pid='.$pid.SEP.'page='.$page.SEP.'order=d').'">
		          <img src="'.AT_BASE_HREF.'images/up.png" border="0" alt="">&nbsp;'._AT('recent_first').'
		        </a>';
		else
			echo '<a href="'.url_rewrite($_SERVER['PHP_SELF'].'?fid='.$fid.SEP.'pid='.$pid.SEP.'page='.$page.SEP.'order=a').'">
		          <img src="'.AT_BASE_HREF.'images/down.png" border="0" alt="">&nbsp;'._AT('recent_last').'
		        </a>';
		
		echo '</div>';
		
		echo _AT('page').': ';
		for ($i=1; $i<=$num_pages; $i++) {
			if ($i == $page) {
				echo '<span class="forum-paginator-active">'.$i.'</span>';
			} else {
				echo '<a href="'.url_rewrite($_SERVER['PHP_SELF'].'?fid='.$fid.SEP.'pid='.$pid.SEP.'page='.$i.SEP.'order='.$_SESSION['thread_order']).'">'.$i.'</a>';
			}
	
			if ($i<$num_pages){
				echo ' <span class="spacer">|</span> ';
			}
		}
		echo '</div>';
		echo '<ul class="forum-thread">';
	     foreach($rows_threads as $row){
			print_entry($row);
			$subject = $row['subject'];
			if ($_GET['reply'] == $row['post_id']) {
				$saved_post = $row;
			}
		}
		echo '</ul>';
		
		echo '<div  class="forum-paginator">';
        echo '<a name="post" id="post"></a>';
		echo _AT('page').': ';
		for ($i=1; $i<=$num_pages; $i++) {
			if ($i == $page) {
				echo '<span class="forum-paginator-active">'.$i.'</span>';
			} else {
				echo '<a href="'.url_rewrite($_SERVER['PHP_SELF'].'?fid='.$fid.SEP.'pid='.$pid.SEP.'page='.$i.SEP.'order='.$_SESSION['thread_order']).'">'.$i.'</a>';
			}
	
			if ($i<$num_pages){
				echo ' <span class="spacer">|</span> ';
			}
		}
		
		echo '</div>';
	}

	$parent_id = $pid;
	$body	   = '';
	if (substr($subject,0,3) != 'Re:') {
		$subject = 'Re: '.$subject;
	}
	
	if ($_SESSION['valid_user'] === true && $_SESSION['enroll'] && !$locked) {
		$sql	= "SELECT subscribe FROM %sforums_accessed WHERE post_id=%d AND member_id=%d";
		$row = queryDB($sql, array(TABLE_PREFIX, $_GET['pid'], $_SESSION['member_id']), TRUE);

		if ($row['subscribe'] == 1) {
			echo '<p><a href="mods/_standard/forums/forum/subscribe.php?fid='.$fid.SEP.'pid='.$_GET['pid'].SEP.'us=1">'._AT('unsubscribe').'</a></p>';
			$subscribed = true;
		} else {
			echo '<p><a href="mods/_standard/forums/forum/subscribe.php?fid='.$fid.SEP.'pid='.$_GET['pid'].'">'._AT('subscribe').'</a></p>';
			$subscribed = false;
		}
	}
	if ($_SESSION['valid_user'] === true && !$_SESSION['enroll']) {
		echo '<p><strong>'._AT('enroll_to_post').'</strong></p>';
	} else if ($locked == 0) {
		require(AT_INCLUDE_PATH.'../mods/_standard/forums/html/new_thread.inc.php');
	} else {
		echo '<p><strong>'._AT('lock_no_post1').'</strong></p>';
	}

require(AT_INCLUDE_PATH.'footer.inc.php');
?>