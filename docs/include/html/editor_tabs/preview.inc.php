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
// $Id$

if (!defined('AT_INCLUDE_PATH')) { exit; }

?>
	<tr>
		<td colspan="2" valign="top" align="left" class="row1"><?php 
	
		echo '<h2>'.AT_print(stripslashes($_POST['title']), 'content.title').'</h2>';

		if ($_POST['body_text']) {
			echo format_content(stripslashes($_POST['body_text']), $_POST['formatting'], $_POST['glossary_defs']);
		} else { 
			require_once(AT_INCLUDE_PATH.'classes/Message/Message.class.php');

			global $savant;
			$msg =& new Message($savant);
			
			$msg->printInfos('NO_PAGE_CONTENT');
	
		} ?>
		</td>
	</tr>
