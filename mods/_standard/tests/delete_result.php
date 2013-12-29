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

authenticate(AT_PRIV_TESTS);

$tid = intval($_REQUEST['tid']);
$rids = explode(',', $_REQUEST['rid']);
debug($rids);
foreach ($rids as $k => $id) {
	$rids[$k] = intval($id);
}
$rid = implode(',', $rids);
debug($rid);
// Check that the user deletes submissions in his own test; if not, exit like authenticate()
$sql	= "SELECT count(*) AS cnt FROM %stests_results R LEFT JOIN %stests USING (test_id) WHERE result_id IN (%s) AND course_id = %d AND R.test_id = %d";
$row	= queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $rid, $_SESSION['course_id'], $tid), TRUE);

if ($row['cnt'] < count($rids)) {
	exit;
}

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/tests/results.php?tid='.$tid);
	exit;

} else if (isset($_POST['submit_yes'])) {

	$sql	= "DELETE FROM %stests_answers WHERE result_id IN (%s)";
	$result	= queryDB($sql, array(TABLE_PREFIX, $rid));

	$sql	= "DELETE FROM %stests_results WHERE result_id IN (%s)";
	$result	= queryDB($sql, array(TABLE_PREFIX, $rid));
			
	$msg->addFeedback('RESULT_DELETED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/tests/results.php?tid='.$tid);
	exit;
} 

$_pages['mods/_standard/tests/delete_result.php']['title_var']  = 'delete_results';
$_pages['mods/_standard/tests/delete_result.php']['parent'] = 'mods/_standard/tests/results.php?tid='.$tid;

$_pages['mods/_standard/tests/results.php?tid='.$tid]['title_var'] = 'submissions';
$_pages['mods/_standard/tests/results.php?tid='.$tid]['parent'] = 'mods/_standard/tests/index.php';

require(AT_INCLUDE_PATH.'header.inc.php');

unset($hidden_vars);

///// Get the test title and names of those who's tests are being deleted
$sql = "SELECT title FROM %stests WHERE test_id = %d";
$test_title = queryDB($sql, array(TABLE_PREFIX, $tid), TRUE);

foreach($rids as $rid_mem){
    $sql = "SELECT first_name, last_name FROM %smembers WHERE member_id =(SELECT member_id FROM %stests_results WHERE result_id = %d)";
    $delete_members[$rid_mem] = queryDB($sql, array(TABLE_PREFIX, TABLE_PREFIX, $rid_mem), TRUE);
}
foreach($delete_members as $delete_member){
    $delete_list .= "<li><strong>".$delete_member['first_name']." ".$delete_member['last_name']."</strong></li>";
}
////

$hidden_vars['tid'] = $tid;
$hidden_vars['rid'] = $rid;
$msg->addConfirm(array('DELETE', $test_title['title'] .': <ul>'. $delete_list .'</ul>'), $hidden_vars);

$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>