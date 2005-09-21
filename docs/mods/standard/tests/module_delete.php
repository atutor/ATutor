<?php

function tests_delete($course) {
	global $db;

	$sql	= "SELECT test_id FROM ".TABLE_PREFIX."tests WHERE course_id=$course";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions_assoc WHERE test_id=$row[test_id]";
		$result2 = mysql_query($sql, $db);
	
		$sql2	= "SELECT result_id FROM ".TABLE_PREFIX."tests_results WHERE test_id=$row[test_id]";
		$result2 = mysql_query($sql2, $db);
		while ($row2 = mysql_fetch_assoc($result2)) {
			$sql3	= "DELETE FROM ".TABLE_PREFIX."tests_answers WHERE result_id=$row2[result_id]";
			$result3 = mysql_query($sql3, $db);
		}
		$sql	= "DELETE FROM ".TABLE_PREFIX."tests_results WHERE test_id=$row[test_id]";
		$result2 = mysql_query($sql, $db);

		$sql	= "DELETE FROM ".TABLE_PREFIX."tests_groups WHERE test_id=$row[test_id]";
		$result2 = mysql_query($sql, $db);
	}

	$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions WHERE course_id=$course";
	$result = mysql_query($sql, $db);

	$sql	= "DELETE FROM ".TABLE_PREFIX."tests_questions_categories WHERE course_id=$course";
	$result = mysql_query($sql, $db);

	$sql	= "DELETE FROM ".TABLE_PREFIX."tests WHERE course_id=$course";
	$result = mysql_query($sql, $db);

}

?>