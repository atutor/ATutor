<?php

function tests_delete($course) {

	$sql	= "SELECT test_id FROM %stests WHERE course_id=%d";
	$rows_tests = queryDB($sql, array(TABLE_PREFIX, $course));
	
    foreach($rows_tests as $row){

		$sql	= "DELETE FROM %stests_questions_assoc WHERE test_id=%d";
		$result2 = queryDB($sql, array(TABLE_PREFIX, $row['test_id']));

		$sql2	= "SELECT result_id FROM %stests_results WHERE test_id=%d";
		$rows_results = queryDB($sql2, array(TABLE_PREFIX, $row['test_id']));
		
		foreach($rows_results as $row2){

			$sql3	= "DELETE FROM %stests_answers WHERE result_id=%d";
			$result3 = queryDB($sql3, array(TABLE_PREFIX, $row2['result_id']));
		}

		$sql	= "DELETE FROM %stests_results WHERE test_id=%d";
		$result2 = queryDB($sql, array(TABLE_PREFIX, $row['test_id']));
	}

	$sql	= "DELETE FROM %stests_questions WHERE course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $course));

	$sql	= "DELETE FROM %stests_questions_categories WHERE course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $course));

	$sql	= "DELETE FROM %stests WHERE course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $course));
}

?>