<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$
define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');

/* should only be in here if you are enrolled in the course!!!!!! */
if ($_SESSION['enroll'] == '') {
	require(AT_INCLUDE_PATH.'header.inc.php');
	$msg->addInfo('NOT_ENROLLED');
	$msg->printAll();
	require(AT_INCLUDE_PATH.'footer.inc.php');
	exit;
}

if ($_GET['reset_filter']) {
	unset($_GET);
}

if (isset($_GET['online_status']) && ($_GET['online_status'] != '')) {
	if ($_GET['online_status'] == 1) {
		$on = 'checked="checked"';
	} else if ($_GET['online_status'] == 2) {
		$all = 'checked="checked"';
	} else if ($_GET['online_status'] == 0) {
		$off = 'checked="checked"';
	}
} else {
	$all = 'checked="checked"';
}

$group = abs($_GET['group']);

$sql_groups = implode(',', $_SESSION['groups']);
$sql = "SELECT G.title, G.group_id, T.title AS type_title FROM ".TABLE_PREFIX."groups G INNER JOIN ".TABLE_PREFIX."groups_types T USING (type_id) WHERE T.course_id=$_SESSION[course_id] AND G.group_id IN ($sql_groups) ORDER BY T.title";
$result_groups = mysql_query($sql, $db);

if ($_GET['order'] == 'asc') {
	$order = 'desc';
} else {
	$order = 'asc';
}

$group_members = '';
if ($group) {
	$group_members = array();
	$sql = "SELECT member_id FROM ".TABLE_PREFIX."groups_members WHERE group_id=$group";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$group_members[] = $row['member_id'];
	}
	$group_members = ' AND C.member_id IN (' . implode(',', $group_members) . ')';
}

/* look through enrolled students list */
$sql_members = "SELECT C.member_id, C.approved, C.privileges, M.login, M.first_name, M.second_name, M.last_name FROM ".TABLE_PREFIX."course_enrollment C, ".TABLE_PREFIX."members M	WHERE C.course_id=$_SESSION[course_id] AND C.member_id=M.member_id AND (C.approved='y' OR C.approved='a')	$group_members ORDER BY M.login $order";

$result_members = mysql_query($sql_members, $db);

while ($row_members = mysql_fetch_assoc($result_members)) {
	$all_[$row_members['member_id']] = $row_members;
	$all_[$row_members['member_id']]['online'] = FALSE;
}

$sql_online = "SELECT member_id FROM ".TABLE_PREFIX."users_online WHERE course_id = $_SESSION[course_id] AND expiry>".time();
$result_online = mysql_query($sql_online, $db);

while ($row_online = mysql_fetch_assoc($result_online)) {
	if ($all_[$row_online['member_id']] != '') {
		$all_[$row_online['member_id']]['online'] = TRUE;
		$online[$row_online['member_id']] = $all_[$row_online['member_id']];
	}
}

if ($all) {
	$final = $all_;
} else if ($on) {
	$final = $online;
} else {
	foreach ($all_ as $id=>$attrs) {
		if ($attrs['online'] == FALSE) {
			$final[$id] = $attrs;
		}
	}
}

require(AT_INCLUDE_PATH.'header.inc.php');

$savant->assign('result_groups', $result_groups);
$savant->assign('final', $final);
$savant->assign('base_href', $_base_href);
$savant->assign('on', $on);
$savant->assign('off', $off);
$savant->assign('all', $all);

$savant->display('directory.tmpl.php');

require(AT_INCLUDE_PATH.'footer.inc.php');
?>
