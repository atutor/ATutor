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
// $Id: accessibility.inc.php,v 1.1 2004/02/20 19:14:34 heidi Exp $
if (!defined('AT_INCLUDE_PATH')) { exit; }

?>
	<tr>
		<td colspan="2" valign="top" align="left" class="row1"><?php 

		//save temp file		
		if ($_POST['text'] != '') { 

			$temp_file = write_temp_file();
			$pg_url = $_base_href.'content/achecker/'.$temp_file;

			if ($handle = fopen($pg_url, 'r')) {	
				$checker_url = 'http://tile-cridpath.atrc.utoronto.ca/acheck/servlet/Checkacc?file='.urlencode($pg_url).'&guide=wcag-2-0-html-techs&output=chunk&line=6';
				echo file_get_contents($checker_url);

				//delete file
				fclose($handle);
				$del = unlink('../content/achecker/'.$temp_file);
				//debug($del);

			} else {
				$errors[] = AT_ERROR_FILE_NOT_SAVED;
				print_errors($errors);
			}

		} else { 
			$infos[] = AT_INFOS_NO_PAGE_CONTENT;
			print_infos($infos);	
		} 
		//debug($pg_url);	
		?>
		
		</td>
	</tr>