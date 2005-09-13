<?php

function filemanager_delete($course) {
	global $db;

	$path = AT_CONTENT_DIR . $course . '/';
	clr_dir($path);

}

?>