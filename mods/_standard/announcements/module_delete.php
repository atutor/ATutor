<?php

function announcements_delete($course) {
	global $db;

	/* announcement RSS: */
	if (file_exists(AT_CONTENT_DIR . 'feeds/' . $course . '/RSS1.0.xml')) {
		@unlink(AT_CONTENT_DIR . 'feeds/' . $course . '/RSS1.0.xml');
	}
	if (file_exists(AT_CONTENT_DIR . 'feeds/' . $course . '/RSS2.0.xml')) {
		@unlink(AT_CONTENT_DIR . 'feeds/' . $course . '/RSS2.0.xml');
	}

	//announcements
	$sql	= "DELETE FROM ".TABLE_PREFIX."news WHERE course_id=$course";
	$result = mysql_query($sql, $db);

}

?>