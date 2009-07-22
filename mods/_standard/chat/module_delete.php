<?php

function chat_delete($course) {
	global $db;

	$path = AT_CONTENT_DIR . 'chat/' . $course . '/';
	if (is_dir($path)) {
		clr_dir($path);
	}

}

?>