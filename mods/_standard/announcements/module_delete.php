<?php

function announcements_delete($course) {
	/* announcement RSS: */
	if (file_exists(AT_CONTENT_DIR . 'feeds/' . $course . '/RSS1.0.xml')) {
		@unlink(AT_CONTENT_DIR . 'feeds/' . $course . '/RSS1.0.xml');
	}
	if (file_exists(AT_CONTENT_DIR . 'feeds/' . $course . '/RSS2.0.xml')) {
		@unlink(AT_CONTENT_DIR . 'feeds/' . $course . '/RSS2.0.xml');
	}

	//announcements
	$sql	= "DELETE FROM %snews WHERE course_id=%d";
	$result = queryDB($sql, array(TABLE_PREFIX, $course));
}

?>