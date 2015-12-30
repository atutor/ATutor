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
$page = 'tests';
define('AT_INCLUDE_PATH', '../../../include/');
require(AT_INCLUDE_PATH.'vitals.inc.php');
authenticate(AT_PRIV_TESTS);

	if (isset($_POST['submit_no'])) {
		$msg->addFeedback('CANCELLED');
		header('Location: index.php');
		exit;
	} else if (isset($_POST['submit_yes'])) {
		
		$tid = intval($_POST['tid']);

		$sql	= "DELETE FROM %stests WHERE test_id=%d AND course_id=%d";
		$result	= queryDB($sql, array(TABLE_PREFIX, $tid, $_SESSION['course_id']));
		
        if($result == 1){
			$sql	= "DELETE FROM %stests_questions_assoc WHERE test_id=%d";
			$result	= queryDB($sql, array(TABLE_PREFIX, $tid));
			
			// Delete any prerequisites 
			$sql	= "DELETE FROM %scontent_prerequisites WHERE type='%s' AND item_id=%d";
			$result	= queryDB($sql, array(TABLE_PREFIX, "test",$tid));
			
			//delete test content association as well
			$sql	= "DELETE FROM %scontent_tests_assoc WHERE test_id=%d";
			$result	= queryDB($sql, array(TABLE_PREFIX, $tid));

			/* it has to delete the results as well... */
			$sql	= "SELECT result_id FROM %stests_results WHERE test_id=%d";
			$rows_results	= queryDB($sql, array(TABLE_PREFIX, $tid));
			
			$count_results = count($rows_results);
            if($count_results > 0){
				$result_list = '(';
                foreach($rows_results as $row){
                     $result_count++;           
				    if($result_count > $count_results){
					    $result_list .= $row['result_id'].',';
					}else{
					    $result_list .= $row['result_id'];
					}
				}
				$result_list .= ')';
			}

			if ($result_list != '') {
				$sql	= "DELETE FROM %stests_answers WHERE result_id IN %s";
				$result	= queryDB($sql, array(TABLE_PREFIX, $result_list));

				$sql	= "DELETE FROM %stests_results WHERE test_id=%d";
				$result	= queryDB($sql, array(TABLE_PREFIX, $tid));
			}
		}

		$msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
		header('Location: '.AT_BASE_HREF.'mods/_standard/tests/index.php');
		exit;

	} /* else: */

	require(AT_INCLUDE_PATH.'header.inc.php');

	$_GET['tid'] = intval($_GET['tid']);

	$sql	= "SELECT title FROM %stests WHERE test_id=%d AND course_id=%d";
	$row	= queryDB($sql, array(TABLE_PREFIX, $_GET['tid'], $_SESSION['course_id']), TRUE);
	
	unset($hidden_vars);
	$hidden_vars['tid'] = $_GET['tid'];

	$msg->addConfirm(array('DELETE_TEST', $row['title']), $hidden_vars);
	$msg->printConfirm();

	require(AT_INCLUDE_PATH.'footer.inc.php');
?>