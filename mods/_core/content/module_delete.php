<?php

function content_delete($course) {
	require(AT_INCLUDE_PATH.'../mods/_core/imsafa/classes/A4a.class.php');

	// related_content + content:
	$sql	= "SELECT content_id FROM %scontent WHERE course_id=%d";
	$rows_content = queryDB($sql, array(TABLE_PREFIX, $course));
	
	foreach($rows_content as $row){
		$sql	= "DELETE FROM %srelated_content WHERE content_id=%d";
		$result2 = queryDB($sql, array(TABLE_PREFIX, $row['0']));
		
		$sql3	 = "DELETE FROM %smember_track WHERE content_id=%d";
		$result3 = queryDB($sql3, array(TABLE_PREFIX, $row['0']));	
		
		$sql = "DELETE FROM %scontent_tests_assoc WHERE content_id=%d";
		$result4 = queryDB($sql, array(TABLE_PREFIX, $row['0']));
		
		// Delete all AccessForAll contents 
		$a4a = new A4a($row[0]);
		$a4a->deleteA4a();
	}

	$sql = "DELETE FROM %scontent WHERE course_id=%d";
	$result = queryDB($sql,array(TABLE_PREFIX, $course));
	
	$sql = "OPTIMIZE TABLE %scontent";
	$result = queryDB($sql, array(TABLE_PREFIX));

}

?>