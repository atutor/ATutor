<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

if ($_SESSION['prefs'][PREF_GLOSSARY] == 1){
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
	echo '<tr><td class="catf" valign="top">';
	print_popup_help(AT_HELP_GLOSSARY_MENU);
	if($_GET['menu_jump']){
		echo '<a name="menu_jump5"></a>';
	}
	echo '<a class="white" href="'.$_my_uri.'disable='.PREF_GLOSSARY.SEP.'menu_jump=5">';
	echo _AT('close_glossary_terms');
	echo '</a>';
	echo '</td></tr>';
	echo '<tr>';
	echo '<td class="row1" align="left">';

	$result =& $contentManager->getContentPage($_GET['cid']);

	if ($result && ($row = mysql_fetch_array($result))) {
		$num_terms = preg_match_all("/(\[\?\])(.[^\?]*)(\[\/\?\])/i", $row['text'], $matches, PREG_PATTERN_ORDER);

		$matches = $matches[0];

		$word = str_replace(array('[?]', '[/?]'), '', $matches);
		$word = str_replace("\n", ' ', $word);
		$word = array_unique($word);

		if (count($word) > 0) {
			$count = 0;
			foreach ($word as $k => $v) {
				if ($glossary[$v] != '') {

					if (strlen($v) > 26 ) {
						$v_formatted = substr($v, 0, 26-4).'...';
					}else{
						$v_formatted = $v;
					}

					$count++;
					echo '&#176; <a href="'.$_base_path.'glossary/?g=25#'.urlencode($v).'" title="'.$v.'">'.$v_formatted.'</a>';
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

	echo '</td></tr></table>';

} else {
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="cat2" summary="">';
	echo '<tr><td class="catf" valign="top">';
	print_popup_help(AT_HELP_GLOSSARY_MENU);
	if($_GET['menu_jump']){
		echo '<a name="menu_jump5"></a>';
	}
	echo '<a class="white" href="'.$_my_uri.'enable='.PREF_GLOSSARY.SEP.'menu_jump=5">';
	echo _AT('open_glossary_terms').'';
	echo '</a>';
	echo '</td></tr></table>';
}

?>
