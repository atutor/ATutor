<?php
/****************************************************************/
/* Forum-style codes											*/
/****************************************************************/
/* Copyright (c) 2002 by Joel Kronenberg						*/
/* http://purerave.com											*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
/* These functions were written by Joel Kronenberg for          */
/* purerave.com, and used as-is for ATutor.                     */
/****************************************************************/

/* @See: ./tools/search.php & ./index.php */
function &highlight(&$x, &$var) {//$x is the string, $var is the text to be highlighted
	if ($var != "") {
		$xtemp = "";
		$i=0;
		while($i<strlen($x)){
			if((($i + strlen($var)) <= strlen($x)) && (strcasecmp($var, substr($x, $i, strlen($var))) == 0)) {
				$xtemp .= '<strong class="highlight">' . substr($x, $i , strlen($var)) . '</strong>';
				$i += strlen($var);
			}
			else {
				$xtemp .= $x{$i};
				$i++;
			}
		}
		$x = $xtemp;
	}
	return $x;
}

/* @See: ./index.php */
function format_content($input, $html = 0, $use_glossary = true) {
	global $glossary, $_base_path;

	if (!$html) {
		$input = str_replace('<', '&lt;', $input);
	}

	/* do the glossary search and replace: */
	if (is_array($glossary) && $use_glossary) {
		foreach ($glossary as $k => $v) {
			/* escape special characters */
			$k = preg_quote($k);

			$k = str_replace('&lt;', '<', $k);
			$k = str_replace('/', '\/', $k);

			$original_term = $k;
			$term = $original_term;

	 		$term = '(\s*'.$term.'\s*)';
			$term = str_replace(' ','((<br \/>)*\s*)', $term); 

			$def = htmlspecialchars($v);		
			if ($_SESSION['prefs'][PREF_CONTENT_ICONS] != 2){
				$input = preg_replace
							("/(\[\?\])$term(\[\/\?\])/i",
							'\\2<sup><a href="'.$_base_path.'glossary/?g=24#'.urlencode($original_term).'" onmouseover="return overlib(\''.$def.'\', CAPTION, \''._AT('definition').'\', AUTOSTATUS);" onmouseout="return nd();"><img src="'.$_base_path.'images/glossary_small.gif" height="15" width="16" border="0" class="menuimage9" alt="glossary item"/></a></sup>',
							$input);
			} else {
				$input = preg_replace
							("/(\[\?\])$term(\[\/\?\])/i",
							'\\2<sup>[<a href="'.$_base_path.'glossary/?g=24#'.urlencode($original_term).'" onmouseover="return overlib(\''.$def.'\', CAPTION, \''._AT('definition').'\', AUTOSTATUS);" onmouseout="return nd();">?</a>]</sup>',
							$input);
			}
		}
	} else if (!$user_glossary) {
		$input = str_replace(array('[?]','[/?]'), '', $input);
	}

	if (BACKWARDS_COMPATIBILITY) {
		$input = str_replace('CONTENT_DIR', 'content/'.$_SESSION['course_id'], $input);
	}

	if ($html) {
		return format_final_output($input, false);
	}

	$output = format_final_output($input);

	if (!$html) {
		$output = '<p>'.$output.'</p>';
	}

	return $output;
}

function find_terms($find_text) {
	preg_match_all("/(\[\?\])(.[^\?]*)(\[\/\?\])/i", $find_text, $found_terms, PREG_PATTERN_ORDER);
	return $found_terms;
}

?>