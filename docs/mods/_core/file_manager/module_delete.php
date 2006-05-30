<?php

function file_manager_delete($course) {
	$path = AT_CONTENT_DIR . $course . '/';
	clr_dir($path);

}

?>