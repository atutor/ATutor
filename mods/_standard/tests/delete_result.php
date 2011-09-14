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
foreach ($rids as $k => $id) {
	$rids[$k] = intval($id);
}
$rid = implode(',', $rids);

// Check that the user deletes submissions in his own test; if not, exit like authenticate()
$sql	= "SELECT count(*) AS cnt FROM ".TABLE_PREFIX."tests_results R LEFT JOIN ".TABLE_PREFIX."tests USING (test_id) WHERE result_id IN ($rid) AND course_id = $_SESSION[course_id] AND R.test_id = $tid";

$result	= mysql_query($sql, $db);
$row = mysql_fetch_array($result);
if ($row['cnt'] < count($rids)) {
	exit;
}

if (isset($_POST['submit_no'])) {
	$msg->addFeedback('CANCELLED');
	header('Location: '.AT_BASE_HREF.'mods/_standard/tests/results.php?tid='.$tid);
	exit;

} else if (isset($_POST['submit_yes'])) {
	$sql	= "DELETE FROM ".TABLE_PREFIX."tests_answers WHERE result_id IN ($rid)";
	$result	= mysql_query($sql, $db);

	$sql	= "DELETE FROM ".TABLE_PREFIX."tests_results WHERE result_id IN ($rid)";
	$result	= mysql_query($sql, $db);
		
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
$hidden_vars['tid'] = $tid;
$hidden_vars['rid'] = $rid;
$msg->addConfirm(array('DELETE', _AT('submissions') .': <strong>'. count($rids) .'</strong>'), $hidden_vars);

$msg->printConfirm();

require(AT_INCLUDE_PATH.'footer.inc.php');
?>