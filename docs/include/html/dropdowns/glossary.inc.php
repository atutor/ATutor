<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2005 by Greg Gay & Joel Kronenberg        */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $contentManager;
global $_my_uri;
global $_base_path;
global $savant;
global $glossary;

$savant->assign('tmpl_popup_help', 'GLOSSARY_MENU');
$savant->assign('tmpl_access_key', '');

if ($_GET['menu_jump']) {
	$savant->assign('tmpl_menu_url', '<a name="menu_jump5"></a>');	
} else {
	$savant->assign('tmpl_menu_url', '');	
}
	
if ($_SESSION['prefs'][PREF_GLOSSARY] == 1){
	ob_start(); 

	$result = $contentManager->getContentPage($_GET['cid']);
	if ($result && ($row = mysql_fetch_array($result))) {
		$matches = find_terms($row['text']);
		$matches = $matches[0];
		$word = str_replace(array('[?]', '[/?]'), '', $matches);
		$word = str_replace("\n", ' ', $word);
		$word = array_unique($word);

		if (count($word) > 0) {
			$count = 0;
			foreach ($word as $k => $v) {
				$original_v = $v;
				$v = urlencode($v);
				if ($glossary[$v] != '') {
					if (strlen($original_v) > 26 ) {
						$v_formatted = substr($original_v, 0, 26-4).'...';
					}else{
						$v_formatted = $original_v;
					}

					$count++;
					echo '&#176; <a href="'.$_base_path.'glossary/index.php?g=25#'.$v.'" title="'.$original_v.'">'.$v_formatted.'</a>';
					echo '<br />';
				}
			}
			if ($count == 0) {
				/* there are defn's, but they're not defined in the glossary */
				echo 'A <small><i>'._AT('none_found').'</i></small>';
			}
		} else {
			/* there are no glossary terms on this page */
			echo '<small><i>'._AT('none_found').'</i></small>';
		}
	} else {
		/* there are no glossary terms in the system for this course or error */
		echo '<small><i>'._AT('none_found').'</i></small>';
	}

	$savant->assign('tmpl_dropdown_contents', ob_get_contents());
	ob_end_clean();
	$savant->assign('tmpl_close_url', $_my_uri.'disable='.PREF_GLOSSARY.SEP.'menu_jump=5');
	$savant->assign('tmpl_dropdown_close', _AT('close_glossary_terms'));
	$savant->display('dropdown_open.tmpl.php');

} else {		
	$savant->assign('tmpl_open_url', $_my_uri.'enable='.PREF_GLOSSARY.SEP.'menu_jump=5');
	$savant->assign('tmpl_dropdown_open', _AT('open_glossary_terms'));
	$savant->display('dropdown_closed.tmpl.php');
}

?>