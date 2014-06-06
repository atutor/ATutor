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
if (!defined('AT_INCLUDE_PATH')) { exit; }

if (isset($_POST['enroll'])) {

	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['tab'] = $_POST['tab'];
	} else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=enroll'.SEP.'tab=0'.SEP.'course_id='.$course_id);
		exit;
	}
} else if (isset($_POST['unenroll'])) {
	// different from a plain delete. This removes from groups as well.
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['tab'] = $_POST['tab'];
	} else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=unenroll'.SEP.'tab=1'.SEP.'course_id='.$course_id);
		exit;	
	}
} else if (isset($_POST['role'])) {
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['tab'] = $_POST['tab'];
	} else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'mid'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: privileges.php?'.$text.SEP.'course_id='.$course_id);
		exit;
	}
} else if (isset($_POST['alumni'])) {
	if (!$_POST['id']) 	{
		$msg->addError('NO_STUDENT_SELECTED');
		$_GET['tab'] = $_POST['tab'];
	} else {
		$i=0;
		foreach ($_POST['id'] as $elem) {
			$text .= 'id'.$i.'='.$elem.SEP;
			$i++;
		}
		header('Location: enroll_edit.php?'.$text.'func=alumni'.SEP.'tab=2'.SEP.'course_id='.$course_id);
		exit;
	}
}

//filter stuff:

if ($_GET['reset_filter']) {
	unset($_GET);
}

$filter=array();

if (isset($_GET['role']) && ($_GET['role'] != '')) {
	$filter['role'] = intval($_GET['role']);
} 

if (isset($_GET['status']) && ($_GET['status'] != '')) {
	$filter['status'] = intval($_GET['status']);
} 

if (isset($_GET['group']) && ($_GET['group'] != '')) {
	$filter['group'] = intval($_GET['group']);
} 


$orders = array('asc' => 'desc', 'desc' => 'asc');
$cols   = array('login' => 1, 'first_name' => 1, 'second_name' => 1, 'last_name' => 1, 'email' => 1);

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
$view_select = intval($_POST['view_select']);

// the possible tabs. order matters.
$tabs = array('enrolled', 'assistants', 'alumni', 'pending_enrollment', 'not_enrolled');


// Remove Not Enrolled tab if system preference is turned off 1.6.2
if($_config['allow_instructor_registration'] != 1){
	array_pop($tabs);
}

$num_tabs = count($tabs);
if (isset($_REQUEST['tab'])) {
	$current_tab = intval($_REQUEST['tab']);
}

if (!isset($current_tab)) {
	$current_tab = 0;
}

if (isset($_GET['match']) && $_GET['match'] == 'one') {
	$checked_match_one = ' checked="checked"';
	$page_string .= SEP.'match=one';
} else {
	$_GET['match'] = 'all';
	$checked_match_all = ' checked="checked"';
	$page_string .= SEP.'match=all';
}

if (admin_authenticate(AT_ADMIN_PRIV_ENROLLMENT, TRUE)) {
	$page_string .= SEP.'course_id='.$course_id;
}

if ($_GET['search']) {
	$page_string .= SEP.'search='.urlencode($_GET['search']);
	$search = $addslashes($_GET['search']);
	$search = explode(' ', $search);

	if ($_GET['match'] == 'all') {
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

$instructor_id = $system_courses[$course_id]['member_id'];
// retrieve all the members of this course (used later to get all those who aren't in this course)
$course_enrollment = get_group_concat('course_enrollment', 'member_id', "course_id=$course_id AND member_id<>$instructor_id");
$course_enrollment .= ','.$instructor_id;

$tab_counts     = array();
$tab_sql_counts = array();
$tab_sql_counts[0] = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id) WHERE CE.course_id=$course_id 
						AND CE.approved='y' AND M.member_id<>$instructor_id AND CE.privileges=0 AND $search";
$tab_sql_counts[1] = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id) WHERE CE.course_id=$course_id 
						AND CE.approved='y' AND CE.privileges>0 AND $search";
$tab_sql_counts[2] = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id) WHERE CE.course_id=$course_id 
						AND approved='a' AND $search";
$tab_sql_counts[3] = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id) WHERE CE.course_id=$course_id
						AND approved='n' AND $search";
$tab_sql_counts[4] = "SELECT COUNT(*) AS cnt FROM ".TABLE_PREFIX."members M WHERE M.status>1 AND M.member_id NOT IN ($course_enrollment) AND $search";

foreach ($tab_sql_counts as $tab => $sql) {
	if ($tab == 3 && $system_courses[$course_id]['access'] != 'private') {
		$tab_counts[$tab] = 0;
	} else {

		$row = queryDB($sql, array(), TRUE);
		$tab_counts[$tab] = $row['cnt'];
	}
}


if ($current_tab == 0) {
	// enrolled
	$sql	=  "SELECT CE.member_id, CE.privileges, CE.approved, M.login, M.first_name, M.second_name, M.last_name, M.email 
				FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id)
				WHERE CE.course_id=$course_id AND approved='y' AND M.member_id<>$instructor_id AND CE.privileges=0 AND $search
				ORDER BY $col $order";
} else if ($current_tab == 1) {
	// assistants
	$sql	=  "SELECT CE.member_id, CE.approved, CE.privileges, M.login, M.first_name, M.second_name, M.last_name, M.email 
				FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id)
				WHERE CE.course_id=$course_id AND CE.approved='y' AND CE.privileges>0 AND $search
				ORDER BY $col $order";

} else if ($current_tab == 3) {
	// pending
	if ($system_courses[$course_id]['access'] == 'private') {
		$sql	=  "SELECT CE.member_id, CE.approved, CE.privileges, M.login, M.first_name, M.second_name, M.last_name, M.email 
				FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id)
				WHERE CE.course_id=$course_id AND approved='n' AND $search
				ORDER BY $col $order";
	} else {
		// not sure what this is about
		$sql = "SELECT login FROM ".TABLE_PREFIX."members WHERE 0";
	}
} else if ($current_tab == 2) {
	// alumni
	$sql	=  "SELECT CE.member_id, CE.approved, CE.privileges, M.login, M.first_name, M.second_name, M.last_name, M.email 
				FROM ".TABLE_PREFIX."course_enrollment CE INNER JOIN ".TABLE_PREFIX."members M USING (member_id)
				WHERE CE.course_id=$course_id AND approved='a' AND $search
				ORDER BY $col $order";
} else { // current_tab == 4

	$sql	=  "SELECT M.member_id, M.login, M.first_name, M.second_name, M.last_name, M.email FROM ".TABLE_PREFIX."members M WHERE M.member_id NOT IN ($course_enrollment) AND M.status>1 AND $search ORDER BY $col $order";
}

$results_per_page = 50;

$num_pages = max(ceil($tab_counts[$current_tab] / $results_per_page), 1);
$page = intval($_GET['p']);
if (!$page) {
	$page = 1;
}	
$count  = (($page-1) * $results_per_page) + 1;
$offset = ($page-1)*$results_per_page;
$sql .= " LIMIT $offset, $results_per_page";

$rows_enrollment_result = queryDB($sql, array());
$page_string_w_tab = $page_string . SEP . 'tab='.$current_tab;

require(AT_INCLUDE_PATH.'header.inc.php');

?>

<script language="JavaScript" type="text/javascript">
//<!--
function CheckAll() {
	for (var i=0;i<document.selectform.elements.length;i++)	{
		var e = document.selectform.elements[i];
		if ((e.name == 'id[]') && (e.type=='checkbox')) {
			e.checked = document.selectform.selectall.checked;
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

$sql = "SELECT course_id, title FROM %scourses ORDER BY title";
$rows_courses = queryDB($sql, array(TABLE_PREFIX));

$savant->assign('rows_courses', $rows_courses);
$savant->assign('current_tab', $current_tab);
$savant->assign('course_id', $course_id);
$savant->assign('checked_match_all', $checked_match_all);
$savant->assign('checked_match_one', $checked_match_one);
$savant->assign('page', $page);
$savant->assign('tab_counts', $tab_counts);
$savant->assign('page_string_w_tab', $page_string_w_tab);
$savant->assign('order', $order);
$savant->assign('orders', $orders);
$savant->assign('col', $col);
$savant->assign('cols', $cols);
$savant->assign('results_per_page', $results_per_page);
$savant->assign('num_tabs', $num_tabs);
$savant->assign('tabs', $tabs);
$savant->assign('enrollment_result', $rows_enrollment_result);

if($_SESSION['is_admin'] === true || $_SESSION['privileges'] == AT_ADMIN_PRIV_ADMIN){
	$savant->display('instructor/enrolment/index.tmpl.php');
}

require(AT_INCLUDE_PATH.'footer.inc.php'); 
?>