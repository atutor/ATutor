<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
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
<div align="center" id="language" style="clear: left"><br /><?php

	if ($languageManager->getNumLanguages() > 5) {
		echo '<form method="get" action="'.htmlspecialchars($_my_uri, ENT_QUOTES).'">';		
		$languageManager->printDropdown($_SESSION['lang'], 'lang', 'lang');

		echo ' <input type="submit" name="submit_language" class="button" value="'._AT('translate').'" />';
		echo '</form>';
	} else {
		echo '<small>'._AT('translate_to').' </small>';
		$languageManager->printList($_SESSION['lang'], 'lang', 'lang', htmlspecialchars($_my_uri));
	}
?></div>