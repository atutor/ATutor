<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* Inclusive Design Institute                                           */
/* http://atutor.ca                                                     */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $contentManager;
global $_base_path;
global $savant;
global $glossary;
global $strlen, $substr, $strtolower;

ob_start(); 
$result = false;
if (isset($_GET['cid'])) {
	$result = $contentManager->getContentPage($_GET['cid']);
}
if ($result && ($row = mysql_fetch_array($result))) {
	$matches = find_terms($row['text']);
	$matches = $matches[0];
	$words = str_replace(array('[?]', '[/?]'), '', $matches);
	$words = str_replace("\n", ' ', $words);

	//case-insensitive, unique array of words
	for($i=0;$i<count($words);$i++) {
		$words[$i] = $strtolower($words[$i]);
	}
	$words = array_unique($words);

	if (count($words) > 0) {
		$count = 0;

		$glossary_key_lower = array_change_key_case($glossary);
		foreach ($words as $k => $v) {
			$original_v = $v;
			$v = $strtolower($v);	//array_change_key_case change everything to lowercase, including encoding. 
			if (isset($glossary_key_lower[$v]) && $glossary_key_lower[$v] != '') {
				$v_formatted = urldecode(array_search($glossary_key_lower[$v], $glossary));

				$def = AT_print($glossary_key_lower[$v], 'glossary.definition');

				$count++;
				echo '<a class="tooltip" href="'.$_base_path.'mods/_core/glossary/index.php?g_cid='.$_SESSION['s_cid'].htmlentities(SEP).'w='.urlencode($original_v).'#term" title="'.htmlentities_utf8($v_formatted).': '.$def.'">';
				if ($strlen($original_v) > 26 ) {
					$v_formatted = $substr($v_formatted, 0, 26-4).'...';
				}
				echo AT_print($v_formatted, 'glossary.word').'</a>';
				echo '<br />';
			}
		}

		if ($count == 0) {
			/* there are defn's, but they're not defined in the glossary */
			echo '<strong>'._AT('no_terms_found').'</strong>';
		}
	} else {
		/* there are no glossary terms on this page */
		echo '<strong>'._AT('no_terms_found').'</strong>';
	}
} else {
	/* there are no glossary terms in the system for this course or error */
	echo '<strong>'._AT('na').'</strong>';
}

$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('glossary'));
$savant->display('include/box.tmpl.php');
?>
