<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2008												*/
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

// hack for eXe content, remove now
header ('content-type: text/css');
echo('@import url(exestyles.css);');
exit;


if (isset ($_GET['cid'])){
	$cid = intval ($_GET['cid']);

	$sql = "SELECT * FROM ".TABLE_PREFIX."head WHERE course_id=$_SESSION[course_id] AND content_id=$cid";
	$result = mysql_query($sql, $db);

	if (($result != 0) && ($row = mysql_fetch_assoc($result))){
		// get the CSS used
		$styleText = get_styles ($row['text'], $_GET['path']);

		header ('content-type: text/css');
		echo($styleText);
	}
}

// Takes the contents of the 'head' section and returns the 'style' and 'link' elements.
function get_styles ($headText, $path) {
	$styleText = '';

	// get the contents of all the 'style' elements
	$styleStartPos = strpos ($headText, '<style');
	while ($styleStartPos !== false) {

		// remove the start and end 'style' elements
		// (we're using just the contents of the 'style' element)
		$styleStartPos = strPos ($headText, '>', $styleStartPos) + 1;
		$styleEndPos = strPos ($headText, '</style', $styleStartPos) -1;

		// contents of 'style' element
		$tempString = substr ($headText, $styleStartPos, ($styleEndPos - $styleStartPos) + 1);

		// add full path to any 'import' statements
		$importStartPos = strpos ($tempString, '@import');
		if ($importStartPos !== false){
			$bracketStartPos = strpos ($tempString, '(', $importStartPos);
			if ($bracketStartPos !== false){
				$bracketEndPos = strpos ($tempString, ')', $bracketStartPos);
				if ($bracketEndPos !== false){
					$uriCss = trim (substr ($tempString, $bracketStartPos + 1, ($bracketEndPos - $bracketStartPos)));

					$tempString2 = substr ($tempString, '0', $bracketStartPos);
					$tempString = $tempString2.'('.$path.$uriCss.substr ($tempString, $bracketEndPos + '1');
				}
			}
		}

		$styleText = $styleText."\n".$tempString;

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

			// get the 'href' attribute value
			$hrefPos = strpos ($headText, 'href', $linkStartPos);
			if ($hrefPos !== false){
				$hrefPos = strpos ($headText, '=', $hrefPos);
				if ($hrefPos !== false){
					$hrefPos += '1';

					// get first character in attribute value
					$index = $hrefPos;

					// find first attribute character
					while (($headText[$index] == " ") || ($headText[$index] == '\'') || ($headText[$index] == "\"")){
						$index++;
						if ($index > strlen($headText)){
							break;
						}
					}
					$indexStart = $index;

					// find end of attribute character
					$indexEnd = $indexStart;
					while (($headText[$indexEnd] != " ") && ($headText[$indexEnd] != '\'') && ($headText[$indexEnd] != "\"")){
						$indexEnd++;
						if ($index > strlen($headText)){
							break;
						}
					}
				}
			}

			// convert the href attribute value to an "import url" statement
			$importStatement = '@import url('.$path.substr ($headText, $indexStart, ($indexEnd - $indexStart)).');';
			$styleText = $styleText."\n".$importStatement;
		}

		// look for another 'link' element
		$linkStartPos = strpos ($headText, '<link', $linkEndPos);
	}
	return $styleText;
}
?>