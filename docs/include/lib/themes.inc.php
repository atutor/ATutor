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


// Returns an array of information on all available themes found from config.inc.php
function get_available_themes () {
	$theme_list = explode(', ' , AVAILABLE_THEMES);
	foreach ($theme_list as $theme) {
		$theme_info [$theme] = get_theme_info($theme);
		$theme_info [$theme]['filename'] = $theme;
	}
	return $theme_info;
}


?>