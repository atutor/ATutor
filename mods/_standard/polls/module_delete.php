<?php

function polls_delete($course) {

	$sql	= "SELECT poll_id FROM %spolls WHERE course_id=%d";
	$rows_polls = queryDB($sql, array(TABLE_PREFIX, $course));

	if (count($rows_polls) == 0) {
		return;
	}
	foreach($rows_polls as $row){
		$sql	 = "DELETE FROM %spolls_members WHERE poll_id=%d";
		queryDB($sql, array(TABLE_PREFIX, $row['poll_id']));
	}

	$sql	= "DELETE FROM %spolls WHERE course_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $course));
}

?>