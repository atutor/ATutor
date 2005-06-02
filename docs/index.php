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
// $Id$

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH . 'vitals.inc.php');

if (isset($_GET['cid'])) {
	header('Location: '.$_base_href.'content.php?cid='.$_GET['cid']);
	exit;
}

require(AT_INCLUDE_PATH . 'lib/test_result_functions.inc.php');
	
if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$course_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

require(AT_INCLUDE_PATH . 'header.inc.php');
		
$msg->printAll();

if (!authenticate(AT_PRIV_ADMIN, AT_PRIV_RETURN)) {
	$sql    = "SELECT preferences FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id] AND preferences<>''";
	$result = mysql_query($sql, $db);
	if ($row = mysql_fetch_assoc($result)) {
		$msg->printHelps('COURSE_PREF');
	}
}

/*
	if (!authenticate(AT_PRIV_ANNOUNCEMENTS, AT_PRIV_RETURN) && $_SESSION['enroll'] == AT_ENROLL_NO) {
		echo '<small> - ';
		echo '<a href="'.$_base_path.'enroll.php?course='.$_SESSION['course_id'].'">'._AT('enroll').'</a></small>';
	}
*/

if (FALSE && defined('AT_SHOW_TEST_BOX') && AT_SHOW_TEST_BOX) {
	// print new available tests
		
	$sql	= "SELECT T.test_id, T.title FROM ".TABLE_PREFIX."tests T WHERE T.course_id=$_SESSION[course_id] AND T.start_date<=NOW() AND T.end_date>= NOW() ORDER BY T.start_date, T.title";
	$result	= mysql_query($sql, $db);
	$num_tests = mysql_num_rows($result);
	$tests = '';
	while (($row = mysql_fetch_assoc($result)) && authenticate_test($row['test_id'])) {
		$tests .= '<a href="'.$_base_path.'tools/take_test.php?tid='.$row['test_id'].'">'.$row['title'].'</a><br />';
	} 

	if ($tests) { ?>
			<table border="0" cellspacing="0" cellpadding="0" align="center" summary="">
			<tr>
				<td class="test-box"><small><a href="<?php echo $_base_href ?>tools/my_tests.php?g=32"><?php echo _AT('curren_tests_surveys'); ?></a></small></td>
			</tr>
			<tr>
				<td class="dropdown"><?php echo $tests; ?></td>
			</tr>
			</table><br />
	<?php 
	}
}

/* the "home" links: */
$home_links = get_home_navigation();
$savant->assign('home_links', $home_links);

/* the news announcements: */
$news = array();
$num_pages = 1;
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	

$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."news WHERE course_id=$_SESSION[course_id]";
$result = mysql_query($sql, $db);

if ($row = mysql_fetch_assoc($result)) {	
	$num_results = $row['cnt'];
	$results_per_page = NUM_ANNOUNCEMENTS;
	$num_pages = ceil($num_results / $results_per_page);

	$count = (($page-1) * $results_per_page) + 1;

	$offset = ($page-1)*$results_per_page;

	$sql = "SELECT N.* FROM ".TABLE_PREFIX."news N WHERE N.course_id=$_SESSION[course_id] ORDER BY date DESC LIMIT $offset, $results_per_page";
	
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		/* this can't be cached because it called _AT */

		$news[$row['news_id']] = array(
						'date'		=> AT_date(	_AT('announcement_date_format'), 
												$row['date'], 
												AT_DATE_MYSQL_DATETIME),
						'title'		=> AT_print($row['title'], 'news.title'),
						'body'		=> format_content($row['body'], $row['formatting'], $glossary));

	}
}

$savant->assign('announcements', $news);
$savant->assign('num_pages', $num_pages);
$savant->assign('current_page', $page);
$savant->display('index.tmpl.php');

require(AT_INCLUDE_PATH.'footer.inc.php');

?>