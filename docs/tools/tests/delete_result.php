<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

	define('AT_INCLUDE_PATH', '../../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	$_section[0][0] = _AT('tools');
	$_section[0][1] = 'tools/';
	$_section[1][0] = _AT('test_manager');
	$_section[1][1] = 'tools/tests';
	$_section[2][0] = _AT('results');
	$_section[2][1] = 'tools/tests/results.php?tid='.$_GET['tid'];
	$_section[3][0] = _AT('delete_results');

	if ($_GET['d']) {
		$tid = intval($_GET['tid']);
		$rid = intval($_GET['rid']);

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$rid";
		$result	= mysql_query($sql, $db);

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests_results WHERE result_id=$rid";
		$result	= mysql_query($sql, $db);
		Header('Location: ../tests/results.php?tid='.$_GET['tid'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_RESULT_DELETED));
		exit;
	} else {
		require(AT_INCLUDE_PATH.'header.inc.php');
		echo '<h2>'._AT('delete_results').'</h2>';
		$warnings[]=array(AT_WARNING_DELETE_RESULTS, $_GET['tt']);
		print_warnings($warnings);

		echo '<a href="tools/tests/delete_result.php?tid='.$_GET['tid'].SEP.'rid='.$_GET['rid'].SEP.'d=1'.SEP.'tt='.$_GET['tt2'].SEP.'m='.$_GET['m'].'">'._AT('yes_delete').'</a>, <a href="tools/tests/results.php?tid='.$_GET['tid'].SEP.'tt='.$_GET['tt2'].SEP.'f='.urlencode_feedback(AT_FEEDBACK_CANCELLED).SEP.'m='.$_GET['m'].'">'._AT('no_cancel').'</a>';

	}

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>