<?php

function filemanager_delete($course) {
	$path = AT_CONTENT_DIR . $course . '/';
	clr_dir($path);

}

?>