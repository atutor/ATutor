<?php

function faq_delete($course) {
	global $db;

	$sql = "SELECT topic_id FROM ".TABLE_PREFIX."faq_topics WHERE course_id=$course";
	$result = mysql_query($sql, $db);
	while ($row = mysql_fetch_assoc($result)) {
		$sql = "DELETE FROM ".TABLE_PREFIX."faq_entries WHERE topic_id=$row[topic_id]";
		mysql_query($sql, $db);
	}

	$sql = "DELETE FROM ".TABLE_PREFIX."faq_topics WHERE course_id=$course";
	mysql_query($sql, $db);
}

?>