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
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_USERS);

if ( (isset($_GET['edit']) || isset($_GET['password']) || isset($_GET['enrollment'])) && (isset($_GET['id']) && count($_GET['id']) > 1) ) {
	$msg->addError('SELECT_ONE_ITEM');
} else if (isset($_GET['edit'], $_GET['id'])) {
	header('Location: edit_user.php?id='.$_GET['id'][0]);
	exit;
} else if (isset($_GET['password'], $_GET['id'])) {
	header('Location: password_user.php?id='.$_GET['id'][0]);
	exit;
} else if (isset($_GET['enrollment'], $_GET['id'])) {
	header('Location: user_enrollment.php?id='.$_GET['id'][0]);
	exit;
} else if ( isset($_GET['apply']) && isset($_GET['id']) && $_GET['change_status'] >= -1) {
	$ids = implode(',', $_GET['id']);
	$status = intval($_GET['change_status']);
	if ($status == -1) {
		header('Location: admin_delete.php?id='.$ids);
		exit;
	} else {
		header('Location: user_status.php?ids='.$ids.'&status='.$status);
		exit;
	}
} else if ( (isset($_GET['apply']) || isset($_GET['apply_all'])) && $_GET['change_status'] < -1) {
	$msg->addError('NO_ACTION_SELECTED');
} else if (isset($_GET['apply']) || isset($_GET['edit']) || isset($_GET['delete']) || isset($_GET['password'])) {
	$msg->addError('NO_ITEM_SELECTED');
}

if ($_GET['reset_filter']) {
	unset($_GET);
}

$page_string = '';
$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('login' => 1, 'public_field' => 1, 'first_name' => 1, 'second_name' => 1, 'last_name' => 1, 'email' => 1, 'status' => 1, 'last_login' => 1, 'creation_date' => 1);

if (isset($_GET['asc'])) {
	$order = 'asc';
	$col   = isset($cols[$_GET['asc']]) ? $_GET['asc'] : 'login';
} else if (isset($_GET['desc'])) {
	$order = 'desc';
	$col   = isset($cols[$_GET['desc']]) ? $_GET['desc'] : 'login';
} else {
	// no order set
	$order = 'asc';
	$col   = 'login';
}
if (isset($_GET['status']) && ($_GET['status'] != '')) {
	$_GET['status'] = intval($_GET['status']);
	$status = '=' . intval($_GET['status']);
	$page_string .= SEP.'status'.$status;
} else {
	$status = '<>-1';
	$_GET['status'] = '';
}

if (isset($_GET['last_login_days'], $_GET['last_login_have']) && ($_GET['last_login_have'] >= 0) && $_GET['last_login_days']) {
	$have = intval($_GET['last_login_have']);
	$days = intval($_GET['last_login_days']);
	$page_string .= SEP.'last_login_have='.$have;
	$page_string .= SEP.'last_login_days='.$days;

	if ($have) {
		$ll =  " >= TO_DAYS(NOW())-$days)";
	} else {
		$ll =  " < TO_DAYS(NOW())-$days OR last_login+0=0)";
	}
	$last_login_days = '(TO_DAYS(last_login)'.$ll;
} else {
	$last_login_days = '1';
}

if (isset($_GET['include']) && $_GET['include'] == 'one') {
	$checked_include_one = ' checked="checked"';
	$page_string .= SEP.'include=one';
} else {
	$_GET['include'] = 'all';
	$checked_include_all = ' checked="checked"';
	$page_string .= SEP.'include=all';
}

if ($_GET['search']) {
	$page_string .= SEP.'search='.urlencode($stripslashes($_GET['search']));
	$search = $addslashes($_GET['search']);
	$search = explode(' ', $search);

	if ($_GET['include'] == 'all') {
		$predicate = 'AND ';
	} else {
		$predicate = 'OR ';
	}

	$sql = '';
	foreach ($search as $term) {
		$term = trim($term);
		$term = str_replace(array('%','_'), array('\%', '\_'), $term);
		if ($term) {
			$term = '%%'.$term.'%%';
			$sql .= "((M.first_name LIKE '$term') OR (M.second_name LIKE '$term') OR (M.last_name LIKE '$term') OR (M.email LIKE '$term') OR (M.login LIKE '$term')) $predicate";
		}
	}
	$sql = '('.substr($sql, 0, -strlen($predicate)).')';
	$search = $sql;
} else {
	$search = '1';
}

if ($_GET['searchid']) {
	$_GET['searchid'] = trim($_GET['searchid']);
	$page_string .= SEP.'searchid='.urlencode($_GET['searchid']);
	$searchid = $addslashes($_GET['searchid']);

	$searchid = explode(',', $searchid);

	$sql = '';
	foreach ($searchid as $term) {
		$term = trim($term);
		$term = str_replace(array('%','_'), array('\%', '\_'), $term);
		if ($term) {
			if (strpos($term, '-') === FALSE) {
				$term = '%%'.$term.'%%';
				$sql .= "(L.public_field LIKE '$term') OR ";
			} else {
				// range search
				$range = explode('-', $term, 2);
				$range[0] = trim($range[0]);
				$range[1] = trim($range[1]);
				if (is_numeric($range[0]) && is_numeric($range[1])) {
					$sql .= "(L.public_field >= $range[0] AND L.public_field <= $range[1]) OR ";
				} else {
					$sql .= "(L.public_field >= '$range[0]' AND L.public_field <= '$range[1]') OR ";
				}
			}
		}
	}
	$sql = '('.substr($sql, 0, -3).')';
	$searchid = $sql;
} else {
	$searchid = '1';
}

if (defined('AT_MASTER_LIST') && AT_MASTER_LIST) {
	$sql	= "SELECT COUNT(M.member_id) AS cnt FROM %smembers M LEFT JOIN (SELECT * FROM %smaster_list WHERE member_id <> 0) L USING (member_id) WHERE M.status $status AND $search AND $searchid AND $last_login_days";
    $row_count = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX), TRUE);
} else {
	$sql	= "SELECT COUNT(member_id) AS cnt FROM ".TABLE_PREFIX."members M WHERE status $status AND $search AND $last_login_days";
	$row_count = queryDB($sql, array(TABLE_PREFIX), TRUE);
}

if($row_count > 0){
	$num_results = $row_count['cnt'];
} else {
	$num_results = 0;
}

$results_per_page = 50;
$num_pages = max(ceil($num_results / $results_per_page), 1);
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	
$count  = (($page-1) * $results_per_page) + 1;
$offset = ($page-1)*$results_per_page;

if ( isset($_GET['apply_all']) && $_GET['change_status'] >= -1) {
	$offset = 0;
	$results_per_page = 999999;
}

if (defined('AT_MASTER_LIST') && AT_MASTER_LIST) {
	$sql	= "SELECT M.member_id, M.login, M.first_name, M.second_name, M.last_name, M.email, M.status, M.last_login+0 AS last_login, M.creation_date, L.public_field FROM %smembers M LEFT JOIN (SELECT * FROM %smaster_list WHERE member_id <> 0) L USING (member_id) WHERE M.status $status AND $search AND $searchid AND $last_login_days ORDER BY $col $order LIMIT $offset, $results_per_page";
    $rows_members = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX));
} else {
	$sql	= "SELECT M.member_id, M.login, M.first_name, M.second_name, M.last_name, M.email, M.status, M.last_login+0 AS last_login, M.creation_date FROM %smembers M WHERE M.status $status AND $search AND $last_login_days ORDER BY $col $order LIMIT $offset, $results_per_page";
    $rows_members = queryDB($sql, array(TABLE_PREFIX));
}


if ( isset($_GET['apply_all']) && $_GET['change_status'] >= -1) {
	$ids = '';

	foreach($rows_members as $row){
		$ids .= $row['member_id'].','; 
	}
	$ids = substr($ids,0,-1);
	$status = intval($_GET['change_status']);

	if ($status==-1) {
		header('Location: admin_delete.php?id='.$ids);
		exit;
	} else {
		header('Location: user_status.php?ids='.$ids.'&status='.$status);
		exit;
	}
}
require(AT_INCLUDE_PATH.'header.inc.php');

?>

<script language="JavaScript" type="text/javascript">
//<!--
function CheckAll() {
	for (var i=0;i<document.form.elements.length;i++)	{
		var e = document.form.elements[i];
		if ((e.name == 'id[]') && (e.type=='checkbox')) {
			e.checked = document.form.selectall.checked;
			togglerowhighlight(document.getElementById("r" + e.id), e.id);
		}
	}
}

function togglerowhighlight(obj, boxid) {
	if (document.getElementById(boxid).checked) {
		obj.className = 'selected';
	} else {
		obj.className = '';
	}
}
//-->
</script>
<?php 

$savant->assign('rows_members', $rows_members);
$savant->assign('results_per_page', $results_per_page);
$savant->assign('page', $page);
$savant->assign('orders', $orders);
$savant->assign('order', $order);
$savant->assign('page_string', $page_string);
$savant->assign('num_results', $num_results);
$savant->display('admin/users/users.tmpl.php');

require(AT_INCLUDE_PATH.'footer.inc.php'); ?>