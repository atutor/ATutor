<?php
/************************************************************************/
/* ATutor                                                             */
/************************************************************************/
/* Copyright (c) 2008 by Greg Gay, Cindy Li                             */
/* Adaptive Technology Resource Centre / University of Toronto			    */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/

define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH . 'vitals.inc.php');

if (isset($_GET['cid'])) {
	header('Location: '.$_base_href.'content.php?cid='.intval($_GET['cid']));
	exit;
}

require(AT_INCLUDE_PATH . 'lib/test_result_functions.inc.php');
	
if (defined('AT_FORCE_GET_FILE') && AT_FORCE_GET_FILE) {
	$course_base_href = 'get.php/';
} else {
	$course_base_href = 'content/' . $_SESSION['course_id'] . '/';
}

require(AT_INCLUDE_PATH . 'header.inc.php');

/* the "home" links: */
$home_links = get_home_navigation();
$savant->assign('home_links', $home_links);


/* the news announcements: */
$news = array(); 
$num_pages = 1;
$page = isset($_GET['p']) ? intval($_GET['p']) : 1;
if (!$page) {
	$page = 1;
}	

$module =& $moduleFactory->getModule(AT_MODULE_DIR_STANDARD.'/announcements');
if (!$module->isEnabled()) {
	$result = FALSE;
	$news = array();
} else {
	$sql	= "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."news WHERE course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);
}

if ($result && ($row = mysql_fetch_assoc($result))) {
	$num_results = $row['cnt'];
	$results_per_page = NUM_ANNOUNCEMENTS;
	$num_pages = ceil($num_results / $results_per_page);

	$count = (($page-1) * $results_per_page) + 1;

	$offset = ($page-1)*$results_per_page;

	$sql = "SELECT N.*, DATE_FORMAT(N.date, '%Y-%m-%d %H:%i:%s') AS date, first_name, last_name 
	          FROM ".TABLE_PREFIX."news N, ".TABLE_PREFIX."members M 
	         WHERE N.course_id=$_SESSION[course_id] 
	           AND N.member_id = M.member_id
	         ORDER BY date DESC LIMIT $offset, $results_per_page";
	
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		/* this can't be cached because it called _AT */

		$news[$row['news_id']] = array(
						'date'		=> AT_date(	_AT('announcement_date_format'), 
						$row['date'],
						AT_DATE_MYSQL_DATETIME),
					  	'author'  => $row['first_name'] . ' ' . $row['last_name'],
						'title'		=> AT_print($row['title'], 'news.title'),
						'body'		=> format_content($row['body'], $row['formatting'], $glossary));

	}
}

$sql = "SELECT banner FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
$result = mysql_query($sql, $db);
if ($row = mysql_fetch_assoc($result)) {
	$savant->assign('banner', AT_print($row['banner'], 'courses.banner'));
} else {
	$savant->assign('banner', '');
}

$savant->assign('announcements', $news);
$savant->assign('num_pages', $num_pages);
$savant->assign('current_page', $page);
$savant->display('index.tmpl.php');

require(AT_INCLUDE_PATH.'footer.inc.php');

?>