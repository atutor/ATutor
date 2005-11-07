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
// $Id: glossary.inc.php 5252 2005-08-09 16:39:26Z heidi $

if (!defined('AT_INCLUDE_PATH')) { exit; }
global $contentManager;
global $_my_uri;
global $_base_path;
global $savant;
global $glossary;

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
				
				$def = AT_print($glossary[$v], 'glossary.definition');

				$count++;
				//echo '&#176; <a href="'.$_base_path.'glossary/index.php?g_cid='.$_SESSION['s_cid'].SEP.'w='.$v.'" title="'.$original_v.'">'.$v_formatted.'</a>';

				echo '<a href="'.$_base_path.'glossary/index.php?g_cid='.$_SESSION['s_cid'].SEP.'w='.urlencode($original_v).'#term" onmouseover="return overlib(\''.$def.'\', CAPTION, \''.addslashes($original_v).'\', AUTOSTATUS);" onmouseout="return nd();" onfocus="return overlib(\''.$def.'\', CAPTION, \''.addslashes($original_v).'\', AUTOSTATUS);" onblur="return nd();">'.AT_print($v_formatted, 'glossary.word').'</a>';
				echo '<br />';
			}
		}

		if ($count == 0) {
			/* there are defn's, but they're not defined in the glossary */
			echo '<em>'._AT('none_found').'</em>';
		}
	} else {
		/* there are no glossary terms on this page */
		echo '<em>'._AT('no_terms_found').'</em>';
	}
} else {
	/* there are no glossary terms in the system for this course or error */
	echo '<em>'._AT('na').'</em>';
}

$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('glossary'));
$savant->display('include/box.tmpl.php');
?>