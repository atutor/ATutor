<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

	$delim = ' » ';

	echo '&nbsp;';

	if ($cid && is_array($path)) {
		echo '<a href="'.$_base_path.'?g=10" class="breadcrumbs">'._AT('home').'</a>';
		echo $delim;
		/* find the path to this cid */

		foreach ($path as $x => $content_info) {
			if ($content_info['content_id'] == $cid) {
				echo '<b>'.AT_print($content_info['title'], 'content.title').'</b>';
			} else {
				echo '<a href="'.$_base_path.'?cid='.$content_info['content_id'].SEP.'g=10" class="breadcrumbs">'.AT_print($content_info['title'], 'content.title').'</a>';
				echo $delim;
			}
		}
	} else if (is_array($_section)) {
		echo '<a href="'.$_base_path.'?g=10" class="breadcrumbs">'._AT('home').'</a>';

		$num_sections = count($_section);
		for($i = 0; $i < $num_sections-1; $i++) {
			echo $delim;
			echo '<a href="'.$_base_path.$_section[$i][1];
			
			if (strpos($_section[$i][1], '?') === false) {
				echo '?';
			} else {
				echo SEP;
			}

			echo 'g=10" class="breadcrumbs">';
			echo $_section[$i][0];
			echo '</a>';
		}

		echo $delim;
		echo $_section[$num_sections-1][0];
	} else {
		echo _AT('home');
	}

?>