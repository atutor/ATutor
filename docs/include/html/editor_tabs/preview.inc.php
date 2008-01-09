<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2008 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

?>
	<div class="row"><?php 
	
		echo '<h2>'.AT_print($stripslashes($_POST['title']), 'content.title').'</h2>';

		if ($_POST['body_text']) {
			echo format_content($stripslashes($_POST['body_text']), $_POST['formatting'], $_POST['glossary_defs']);
		} else { 
			global $msg;
		
			$msg->printInfos('NO_PAGE_CONTENT');
	
		} ?>		
	</div>
