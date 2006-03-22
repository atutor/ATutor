<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006												*/
/* Written by Greg Gay & Joel Kronenberg & Chris Ridpath				*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
define('AT_INCLUDE_PATH', 'include/');
require(AT_INCLUDE_PATH . 'vitals.inc.php');

//exit;

if (isset ($_GET['cid'])){
	$cid = intval ($_GET['cid']);

	$sql = "SELECT * FROM ".TABLE_PREFIX."head WHERE course_id=$_SESSION[course_id] AND content_id=$cid";
	$result = mysql_query($sql, $db);

	if (($result != 0) && ($row = mysql_fetch_assoc($result))){
		// get the CSS used
		$styleText = get_styles ($row['text']);

		echo($styleText);
	}
}
exit;

// Takes the contents of the 'head' section and returns the 'style' and 'link' elements.
function get_styles ($headText) {
	$styleText = '';

	// get the contents of all the 'style' elements
	$styleStartPos = strpos ($headText, '<style');
	while ($styleStartPos !== false) {
		$styleEndPos = strPos ($headText, '</style', $styleStartPos);
		$styleEndPos = strPos ($headText, '>', $styleEndPos);

		$styleText = $styleText."\n".substr ($headText, $styleStartPos, ($styleEndPos - $styleStartPos) + 1);

		// look for another 'style' element
		$styleStartPos = strpos ($headText, '<style', $styleEndPos);
	}

	// get all the links to external stylesheets
	$linkStartPos = strpos ($headText, '<link');
	while ($linkStartPos !== false) {
		$linkEndPos = strPos ($headText, '>', $linkStartPos);

		// ensure this is a 'stylesheet' link
		$stylesheetPos = strpos ($headText, "\"text/css\"", $linkStartPos);
		if (($stylesheetPos !== false) && ($stylesheetPos < $linkEndPos)){
			$styleText = $styleText."\n".substr ($headText, $linkStartPos, ($linkEndPos - $linkStartPos) + 1);
		}

		// look for another 'link' element
		$linkStartPos = strpos ($headText, '<link', $linkEndPos);
	}

	return $styleText;
}


?>