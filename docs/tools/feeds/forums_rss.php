<?php
/****************************************************************/
/* ATutor                                                       */
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca                                             */
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id: new_thread.php 2212 2004-11-09 17:09:43Z greg $

/* Read the Private course forum feed  */

if (!file_exists("../../pub/feeds/".$_SESSION[course_id]."/forum_feed.xml")) {
		$fp = @fopen("../../pub/feeds/".$_SESSION[course_id]."/forum_feed.xml", 'w+');
		return;
	}else{
		//don't do anything
	}

require_once(AT_INCLUDE_PATH.'rss/rss_parse.inc');
$rss_file = AT_CONTENT_DIR.$_SESSION['course_id']."/feeds/cache/forum_feed.xml";
$rss_string = read_file($rss_file);
$rss = new MagpieRSS( $rss_string );
//echo $rss_string;

function read_file($rss_file) {
    $fh = fopen($rss_file, 'r') or die($php_errormsg);
    $rss_string = fread($fh, filesize($rss_file) );
    fclose($fh);
    return $rss_string;
}

if ($rss and !$rss->ERROR) {
	$count=0;
	foreach ($rss->items as $item) {
			$href = $item['link'];
			$title = $item['title'];
			$author = $item['author'];
			$description = $item['description'];
			$date = $item['pubDate'];
 			echo '<small><span style="align:right;">*</span> <a href="'.$href.'" title="'.$author.'">'.$title.'</a></small><br />';

	}
}else {
    echo "Error: " . $rss->ERROR;
}
?>