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

?>
	<table cellspacing="1" cellpadding="0" border="0" summary="">
<?php

	$num_tags = count($learning_concept_tags);
	echo '<tr>';
	foreach($learning_concept_tags as $tag) {
		echo '<td class="row1"><a href="javascript:smilie(\'['.$tag.']\')"><img src="'.$_base_path.'images/concepts/'. $tag.'.gif" alt="'._AT('lc_'.$tag.'_title').' ['.$tag.']" border="0" height="22" width="22" title="'._AT('lc_'.$tag.'_title').' ['.$tag.']" /></a></td>';
		$counter++;
	}
	echo '</tr>';
	echo '</table>';
?>