<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

global $available_languages, $_my_uri;

if(count($available_languages) < 2){
	return;
}
?>
<div align="center" id="language"><?php

	if (count($available_languages) > 5) {
		echo '<form method="get" action="'.$_my_uri.'">';
		echo '<small><label for="lang">'._AT('translate_to').'</label> <select name="lang" id="lang">';
		foreach ($available_languages as $temp_key => $val) {
			echo '<option value="'.$temp_key.'"';
			if ($temp_key == $_SESSION['lang']) {
				echo ' selected="selected"';
			}
			echo '>';
			
			echo $val[3].'</option>';
		}
		echo '</select>';
		echo '<input type="submit" name="submit" class="button" value="'._AT('submit').'" /></small>';
		echo '</form>';
	} else {
		echo '<small>'._AT('translate_to').' </small>';
		$l = 0;
		foreach ($available_languages as $temp_key => $val) {

			if ($l){
				echo ' | ';
			}

			if ($temp_key == $_SESSION['lang']) {
				echo '<strong>'.$val[3].'</strong>';
			} else {
				echo '<a href="'.$_my_uri.'lang='.$temp_key.'">'.$val[3].'</a> ';
			}

			$l++;
		}
	}
?></div>