<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id: accessibility.inc.php,v 1.5 2004/02/23 20:00:22 heidi Exp $
if (!defined('AT_INCLUDE_PATH')) { exit; }

?>
	<tr>
		<td colspan="2" valign="top" align="left" class="row1"><?php 					
		
		if ($_POST['text'] != '') { 
			//save temp file
			$temp_file = write_temp_file();
			$pg_url = $_base_href.'content/'.$temp_file;

			$checker_url = 'http://tile-cridpath.atrc.utoronto.ca/acheck/servlet/Checkacc?file='.urlencode($pg_url).'&guide=wcag-2-0-html-techs&output=chunk&line=5';
								
			if ($report = @file_get_contents($checker_url)) {	
				echo $report;								
			} else {
				$infos = "Service currently unavailable.";
				print_infos($infos);
			}
			//delete file
			$del = unlink('../content/'.$temp_file);
			
		} else { 
			$infos[] = AT_INFOS_NO_PAGE_CONTENT;
			print_infos($infos);	
		} 
		?>
		
		</td>
	</tr>