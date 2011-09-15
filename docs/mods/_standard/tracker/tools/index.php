<?php
/************************************************************************/
/* ATutor								*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                       */
/* http://atutor.ca							*/
/*									*/
/* This program is free software. You can redistribute it and/or	*/
/* modify it under the terms of the GNU General Public License		*/
/* as published by the Free Software Foundation.			*/
/************************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_CONTENT);

require(AT_INCLUDE_PATH.'header.inc.php');


$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('total_hits' => 1, 'unique_hits' => 1, 'average_duration' => 1, 'total_duration' => 1);

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'total_hits';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'total_hits';
} else {
	// no order set
	$order = 'desc';
	$col   = 'total_hits';
}

$page_string = SEP.$order.'='.$col;

if (!isset($_GET['cnt'])) {
	$sql	= "SELECT COUNT(DISTINCT content_id) AS cnt FROM ".TABLE_PREFIX."member_track WHERE course_id=$_SESSION[course_id]";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_assoc($result);
	$cnt = $row['cnt'];
} else {
	$cnt = intval($_GET['cnt']);
}

$num_results = $cnt;
$results_per_page = 15;
$num_pages = max(ceil($num_results / $results_per_page), 1);
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	
$count = (($page-1) * $results_per_page) + 1;

$offset = ($page-1)*$results_per_page;

/*create a table that lists all the content pages and the number of time they were viewed*/
$sql = "SELECT content_id, COUNT(*) AS unique_hits, SUM(counter) AS total_hits, SEC_TO_TIME(SUM(duration)/SUM(counter)) AS average_duration, SEC_TO_TIME(SUM(duration)) AS total_duration FROM ".TABLE_PREFIX."member_track WHERE course_id=$_SESSION[course_id] GROUP BY content_id ORDER BY $col $order LIMIT $offset, $results_per_page";
$result = mysql_query($sql, $db);
$savant->assign('result', $result);
$savant->assign('col', $col);
$savant->assign('page_string', $page_string);
$savant->assign('page', $page);
$savant->assign('num_pages', $num_pages);
$savant->display('instructor/content/tracker/index.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>