<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2010                                      */
/* Inclusive Design Institute                                   */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

global $languageManager, $_my_uri;
if($languageManager->getNumLanguages() < 2){
	return;
}
?>
<div id="langdiv"><br /><?php

	if ($languageManager->getNumLanguages() > 5) {
		echo '<form method="get" action="'.AT_print($_my_uri,'url.base').'">';
		echo '<label for="lang" style="display:none;">'._AT('translate_to').' </label>';
		$languageManager->printDropdown($_SESSION['lang'], 'lang', 'lang');
		echo ' <input type="submit" name="submit_language" class="button" value="'._AT('translate').'" />';
		echo '</form>';
	} else {
		echo '<small>'._AT('translate_to').' </small>';
		$languageManager->printList($_SESSION['lang'], 'lang', 'lang',AT_print($_my_uri,'url.base'));
	}
?></div>