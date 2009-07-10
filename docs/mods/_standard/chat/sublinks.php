<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $_base_path;

$record_limit = 3;	// Number of sublinks to display for this module on course home page -> detail view
$cnt = 0;           // count number of sublinks pushed into $list

if ($dir = @opendir(AT_CONTENT_DIR . 'chat/'.$_SESSION['course_id'].'/tran/')) {
	while (($file = readdir($dir)) !== false) {
		if ($cnt >= $record_limit) break;       // quit the loop when reaching the record limit
		
		if (substr($file, -strlen('.html')) == '.html') {
			$file = str_replace('.html', '', $file);

			$list[] = '<a href="'.AT_BASE_HREF.url_rewrite('chat/view_transcript.php?t='.$file).'"'.
			          (strlen($file) > SUBLINK_TEXT_LEN ? ' title="'.$file.'"' : '') .'>'. 
			          validate_length($file, SUBLINK_TEXT_LEN, VALIDATE_LENGTH_FOR_DISPLAY) .'</a>'; 
			
			$cnt++;
		}
	}
}

if (count($list) > 0) {
	return $list;
} else {
	return 0;
}


?>