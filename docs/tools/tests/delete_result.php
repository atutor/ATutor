<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
	$page = 'tests';
	define('AT_INCLUDE_PATH', '../../include/');
	require(AT_INCLUDE_PATH.'vitals.inc.php');
	require(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

	global $savant;
	$msg =& new Message($savant);
	
	$_section[0][0] = _AT('tools');
	$_section[0][1] = 'tools/';
	$_section[1][0] = _AT('test_manager');
	$_section[1][1] = 'tools/tests';
	$_section[2][0] = _AT('results');
	$_section[2][1] = 'tools/tests/results.php?tid='.$_GET['tid'];
	$_section[3][0] = _AT('delete_results');

	authenticate(AT_PRIV_TEST_MARK);

	if ($_GET['d']) {
	
		/* We must ensure that any previous feedback is flushed, since AT_FEEDBACK_CANCELLED might be present
		 * if Yes/Delete was chosen below
		 */
		$msg->deleteFeedback('CANCELLED'); // makes sure its not there 
		
		$tid = intval($_GET['tid']);
		$rid = intval($_GET['rid']);

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$rid";
		$result	= mysql_query($sql, $db);

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests_results WHERE result_id=$rid";
		$result	= mysql_query($sql, $db);
		
		/* avman */
		if ($_GET['tt'] == 'Automatic' && $_GET['auto'] == '1') {
			$msg->addFeedback('RESULT_DELETED');
			Header('Location: ../my_tests.php');
		}
		else {
			$msg->addFeedback('RESULT_DELETED');
			Header('Location: ../tests/results.php?tid='.$_GET['tid']);
		}
		
		exit;
	} else {
		require(AT_INCLUDE_PATH.'header.inc.php');
		echo '<h2>'._AT('delete_results').'</h2>';
		$warnings=array('DELETE_RESULTS', $_GET['tt']);
		$msg->printWarnings($warnings);

		/* avman */
		if ($_GET['tt'] == 'Automatic' && $_GET['auto'] == '1') {
			echo '<a href="tools/tests/delete_result.php?tid='.$_GET['tid'].SEP.'rid='.$_GET['rid'].SEP.'d=1'.SEP.'tt=Automatic'.SEP.'auto=1'.SEP.'m='.$_GET['m'].'">'._AT('yes_delete').'</a>, <a href="tools/my_tests.php?">'._AT('no_cancel').'</a>';
				}
		else {
			/* Since we do not know which choice will be taken, assume it No/Cancel, addFeedback('CENCELLED)
			 * If sent to results.php then OK, else if sent back here & if $_GET['d']=1 then assumed choice was not taken
			 * ensure that addFeeback('CANCELLED') is properly cleaned up, see above
			 */
			$msg->addFeedback('CANCELLED');
			echo '<a href="tools/tests/delete_result.php?tid='.$_GET['tid'].SEP.'rid='.$_GET['rid'].SEP.'d=1'.SEP.'tt='.$_GET['tt2'].SEP.'m='.$_GET['m'].'">'._AT('yes_delete').'</a>, <a href="tools/tests/results.php?tid='.$_GET['tid'].SEP.'tt='.$_GET['tt2'].SEP.'m='.$_GET['m'].'">'._AT('no_cancel').'</a>';
		}
	}

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>