<?php
exit('did not think this file gets used: '. __FILE__);
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

global $cid, $path, $_base_path, $_section;
	$delim = ' » ';

	echo '&nbsp;';

	if ($cid && is_array($path)) {
		echo '<a href="'.$_base_path.'?g=10" class="breadcrumbs">'._AT('home').'</a>';
		echo $delim;
		/* find the path to this cid */

		foreach ($path as $x => $content_info) {
			if ($content_info['content_id'] == $cid) {
				echo '<strong>'.AT_print($content_info['title'], 'content.title').'</strong>';
			} else {
				echo '<a href="'.$_base_path.'?cid='.$content_info['content_id'].SEP.'g=10" class="breadcrumbs">'.$content_info['title'].'</a>';
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