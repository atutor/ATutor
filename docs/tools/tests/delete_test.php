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
	$_section[2][0] = _AT('delete_test');

	if ($_GET['d']) {
	
		/* We must ensure that any previous feedback is flushed, since AT_FEEDBACK_CANCELLED might be present
		 * if Yes/Delete was chosen below
		 */
		$msg->deleteFeedback('CANCELLED'); // makes sure its not there
		
		$tid = intval($_GET['tid']);

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests WHERE test_id=$tid AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $db);

		if (mysql_affected_rows($db) == 1) {
			$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions_assoc WHERE test_id=$tid";
			$result	= mysql_query($sql, $db);

			/* it has to delete the results as well... */
			$sql	= "SELECT result_id FROM ".TABLE_PREFIX."tests_results WHERE test_id=$tid";
			$result	= mysql_query($sql, $db);
			if ($row = mysql_fetch_array($result)) {
				$result_list = '('.$row['result_id'];

				while ($row = mysql_fetch_array($result)) {
					$result_list .= ','.$row['result_id'];
				}
				$result_list .= ')';
			}

			if ($result_list != '') {
				$sql	= "DELETE FROM ".TABLE_PREFIX."tests_answers WHERE result_id IN $result_list";
				$result	= mysql_query($sql, $db);


				$sql	= "DELETE FROM ".TABLE_PREFIX."tests_results WHERE test_id=$tid";
				$result	= mysql_query($sql, $db);
			}
		}

		$msg->addFeedback('TEST_DELETED');
		header('Location: '.$_base_href.'tools/tests/index.php');
		exit;

	} else {
		require(AT_INCLUDE_PATH.'header.inc.php');
echo '<h2>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '<a href="tools/" class="hide"><img src="images/icons/default/square-large-tools.gif" class="menuimageh2" border="0" vspace="2" width="42" height="40" alt="" /></a>';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo ' <a href="tools/" class="hide">'._AT('tools').'</a>';
	}
echo '</h2>';

echo '<h3>';
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2) {
		echo '&nbsp;<img src="images/icons/default/test-manager-large.gif" class="menuimageh3" width="42" height="38" alt="" /> ';
	}
	if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 1) {
		echo '<a href="tools/tests/">'._AT('test_manager').'</a>';
	}
echo '</h3>';

		$sql	= "SELECT title FROM ".TABLE_PREFIX."tests WHERE test_id=$_GET[tid] AND course_id=$_SESSION[course_id]";
		$result	= mysql_query($sql, $db);
		$row	= mysql_fetch_array($result);

		echo '<h3>'._AT('delete_test').'</h3>';
		
		$warnings=array('DELETE_TEST', $row['title']);
		$msg->printWarnings($warnings);

		/* Since we do not know which choice will be taken, assume it No/Cancel, addFeedback('CENCELLED)
		 * If sent to index.php then OK, else if sent back here & if $_GET['d']=1 then assumed choice was not taken
		 * ensure that addFeeback('CANCELLED') is properly cleaned up, see above
		 */
		$msg->addFeedback('CANCELLED');
		echo '<div align="center"><a href="tools/tests/delete_test.php?tid='.$_GET['tid'].SEP.'d=1">'._AT('yes_delete').'</a>, <a href="tools/tests/index.php">'._AT('no_cancel').'</a></div>';
	}
 
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>