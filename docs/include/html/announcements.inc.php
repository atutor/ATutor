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
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

	require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');
	require_once(AT_INCLUDE_PATH.'lib/test_result_functions.inc.php');

	global $savant;
	$msg =& new Message($savant);
	
	echo '<h2><img src="'.$_base_path.'images/icons/default/square-large-home.gif" class="menuimageh1" border="0" alt="" />'.$_SESSION['course_title'];
	if (!authenticate(AT_PRIV_ANNOUNCEMENTS, AT_PRIV_RETURN) && $_SESSION['enroll'] == AT_ENROLL_NO) {
		echo '<small> - ';
		echo '<a href="'.$_base_path.'enroll.php?course='.$_SESSION['course_id'].'">'._AT('enroll').'</a></small>';
	}
	echo '</h2>';
	/* help for content pages */
	if (authenticate(AT_PRIV_ANNOUNCEMENTS, AT_PRIV_RETURN) && ($_SESSION['prefs'][PREF_EDIT] == 1)) {
		if ($_SESSION['prefs'][PREF_MENU]==1){
			$msg->addHelp('ADD_ANNOUNCEMENT2');
		} else {
			$msg->addHelp('ADD_ANNOUNCEMENT');
		}
		$msg->addHelp('ADD_TOP_PAGE');
	}
	if ($_SESSION['prefs'][PREF_EDIT] == 1) {
		$msg->printHelps();
	}

	// print new available tests
	$sql	= "SELECT T.test_id, T.title FROM ".TABLE_PREFIX."tests T WHERE T.course_id=$_SESSION[course_id] AND T.start_date<=NOW() AND T.end_date>= NOW() ORDER BY T.start_date, T.title";
	$result	= mysql_query($sql, $db);
	$num_tests = mysql_num_rows($result);
	$tests = '';
	while (($row = mysql_fetch_assoc($result)) && authenticate_test($row['test_id'])) {
		$tests .= '<a href="'.$_base_path.'tools/take_test.php?tid='.$row['test_id'].SEP.'tt='.urlencode($row['title']).'">'.$row['title'].'</a><br />';
	} 

	if ($tests) { ?>
			<table border="0" cellspacing="0" cellpadding="0" align="center">
			<tr>
				<td class="test-box"><small><a href="<?php echo $_base_href ?>tools/my_tests.php?g=32"><?php echo _AT('curren_tests_surveys'); ?></a></small></td>
			</tr>
			<tr>
				<td class="dropdown"><?php echo $tests; ?></td>
			</tr>
			</table>
	<?php 
	}

	unset($editors);
	$editors[] = array(	'priv'  => AT_PRIV_ANNOUNCEMENTS, 
						'title' => _AT('add_announcement'), 
						'url'   => $_base_path.'editor/add_news.php');

	$editors[] = array(	'priv'  => AT_PRIV_CONTENT,
						'title' => _AT('add_top_page'), 
						'url'   => $_base_path.'editor/edit_content.php');
	$editors[] = array('priv' => AT_PRIV_CONTENT, 'title' => _AT('import_content_package'), 'url' => $_base_path.'tools/ims/index.php');

	print_editor($editors, $large_editor = true);

	$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."news WHERE course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);

	if ($row = mysql_fetch_assoc($result)) {	
		$num_results = $row['cnt'];
		$results_per_page = NUM_ANNOUNCEMENTS;
		$num_pages = ceil($num_results / $results_per_page);
		$page = intval($_GET['p']);
		if (!$page) {
			$page = 1;
		}	
		$count = (($page-1) * $results_per_page) + 1;

		$offset = ($page-1)*$results_per_page;

		$sql = "SELECT N.* FROM ".TABLE_PREFIX."news N WHERE N.course_id=$_SESSION[course_id] ORDER BY date DESC LIMIT $offset, $results_per_page";
	}

	$result = mysql_query($sql, $db);
	if (mysql_num_rows($result) == 0) {
		echo '<em>'._AT('no_announcements').'</em>';
	} else {
		$news = array();
		while ($row = mysql_fetch_assoc($result)) {
			/* this can't be cached because it called _AT */

			$news[$row['news_id']] = array(
							'date'		=> AT_date(	_AT('announcement_date_format'), 
													$row['date'], 
													AT_DATE_MYSQL_DATETIME),
 							'title'		=> AT_print($row['title'], 'news.title'),
							'body'		=> AT_print($row['body'], 'news.body', $row['formatting']));
					

		}

		echo '<table border="0" cellspacing="1" cellpadding="0" width="98%" summary="">';
		
		foreach ($news as $news_id => $news_item) {
			echo '<tr>';
			echo '<td>';
			echo '<br /><h4>'.$news_item['title'];
			unset($editors);
			$editors[] = array('priv' => AT_PRIV_ANNOUNCEMENTS, 'title' => _AT('edit'), 'url' => $_base_path.'editor/edit_news.php?aid='.$news_id);
			$editors[] = array('priv' => AT_PRIV_ANNOUNCEMENTS, 'title' => _AT('delete'), 'url' => $_base_path.'editor/delete_news.php?aid='.$news_id);
			print_editor($editors , $large = false);

			echo '</h4>';

			echo $news_item['body'];

			echo '<br /><small class="date">'._AT('posted').' '.$news_item['date'].'</small>';
			echo '</td>';
			echo '</tr>';
			echo '<tr><td class="row3" height="1"><img src="'.$_base_path.'images/clr.gif" height="1" width="1" alt="" /></td></tr>';
		}
		echo '</table><br />';
		if($num_pages>1) {
			echo _AT('page').': | ';
			for ($i=1; $i<=$num_pages; $i++) {
				if ($i == $page) {
					echo '<strong>'.$i.'</strong>';
				} else {
					echo '<a href="'.$_SERVER['PHP_SELF'].'?p='.$i.'">'.$i.'</a>';
				}
				echo ' | ';
			}
		}
	}


?>
