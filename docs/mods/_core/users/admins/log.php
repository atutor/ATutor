<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

define('AT_INCLUDE_PATH', '../../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_ADMIN);

require(AT_INCLUDE_PATH.'header.inc.php');

$operations[AT_ADMIN_LOG_UPDATE] = _AT('update_to');
$operations[AT_ADMIN_LOG_DELETE] = _AT('delete_from');
$operations[AT_ADMIN_LOG_INSERT] = _AT('insert_into');
$operations[AT_ADMIN_LOG_REPLACE] = _AT('replace_into');
$operations[AT_ADMIN_LOG_OTHER] = _AT('other');

$login_where = '';
if (isset($_GET['login']) && $_GET['login']) {
	$_GET['login'] = $addslashes($_GET['login']);

	$login_where = ' WHERE login=\''.$_GET['login'].'\'';
}

$sql	= "SELECT COUNT(login) FROM ".TABLE_PREFIX."admin_log $login_where";
$result = mysql_query($sql, $db);

if (($row = mysql_fetch_row($result))==0) {
	echo '<tr><td colspan="7" class="row1">'._AT('no_log_found_').'</td></tr>';
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

	$num_results = $row[0];
	$results_per_page = 50;
	$num_pages = max(ceil($num_results / $results_per_page), 1);
	$page = intval($_GET['p']);
	if (!$page) {
		$page = 1;
	}	
	$count = (($page-1) * $results_per_page) + 1;

	echo '<div class="paging">';
	echo '<ul>';
	for ($i=1; $i<=$num_pages; $i++) {
		echo '<li>';
		if ($i == $page) {
			echo '<a class="current" href="'.$_SERVER['PHP_SELF'].'?p='.$i.SEP.'login='.$_GET['login'].'"><strong>'.$i.'</strong></a>';
		} else {
			echo '<a href="'.$_SERVER['PHP_SELF'].'?p='.$i.SEP.'login='.$_GET['login'].'#list">'.$i.'</a>';
		}
		echo '</li>';
	}
	echo '</ul>';
	echo '</div>';

	$offset = ($page-1)*$results_per_page;

	$sql    = "SELECT * FROM ".TABLE_PREFIX."admin_log $login_where ORDER BY `time` DESC LIMIT $offset, $results_per_page";
	$result = mysql_query($sql, $db);
?>


<?php 
$savant->assign('result', $result);
$savant->assign('operations', $operations);
$savant->display('admin/users/log.tmpl.php');
require(AT_INCLUDE_PATH.'footer.inc.php'); ?>