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
// $Id: accessibility.inc.php,v 1.4 2004/02/23 19:33:28 joel Exp $
if (!defined('AT_INCLUDE_PATH')) { exit; }

?>
	<tr>
		<td colspan="2" valign="top" align="left" class="row1"><?php 					
		
		if ($_POST['text'] != '') { 
			$checker_url = 'http://tile-cridpath.atrc.utoronto.ca/acheck/servlet/Checkacc';
			
			//check that a-checker page is available
			if (@fopen($checker_url, 'r')) {
				//save temp file
				$temp_file = write_temp_file();
				$pg_url = $_base_href.'content/'.$temp_file;
				
				if ($handle = @fopen($pg_url, 'r')) {	
					$checker_url .= '?file='.urlencode($pg_url).'&guide=wcag-2-0-html-techs&output=chunk&line=6';
					echo file_get_contents($checker_url);
				
					//delete file
					@fclose($handle);
					@unlink('../content/'.$temp_file);

				} else {
					$errors[] = AT_ERROR_FILE_NOT_SAVED;
					print_errors($errors);
				}

			} else {
				$infos = "Service currently unavailable.";
				print_infos($infos);
			}
			
		} else { 
			$infos[] = AT_INFOS_NO_PAGE_CONTENT;
			print_infos($infos);	
		} 
		//debug($pg_url);	
		?>
		
		</td>
	</tr>