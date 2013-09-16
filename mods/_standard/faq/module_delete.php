<?php

function faq_delete($course) {

	$sql = "SELECT topic_id FROM %sfaq_topics WHERE course_id=%d";
	$rows_faqs = queryDB($sql, array(TABLE_PREFIX, $course));
	foreach($rows_faqs as $row){

		$sql = "DELETE FROM %sfaq_entries WHERE topic_id=%d";
		queryDB($sql, array(TABLE_PREFIX, $row['topic_id']));
	}

	$sql = "DELETE FROM %sfaq_topics WHERE course_id=%d";
	queryDB($sql, array(TABLE_PREFIX, $course));
}

?>