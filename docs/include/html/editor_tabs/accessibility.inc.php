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
// $Id: accessibility.inc.php,v 1.7 2004/03/02 19:36:55 heidi Exp $
if (!defined('AT_INCLUDE_PATH')) { exit; }

//make decisions
if ($_POST['desc_submit']) {
	//get list of decisions	
	$desc_query = "";
	if (is_array($_POST['d'])) {
		for($i=0; $i<count($_POST['d']); $i++) {
			$desc_query .= '&'.$i.'='.$_POST['d'][$i];
		}		$checker_url='http://tile-cridpath.atrc.utoronto.ca/acheck/servlet/decisions?file='.urlencode($_POST['pg_url']).'&output=chunk&name='.$_SESSION['login'].'&email='.$_base_href.$desc_query;								
//debug($checker_url);
		if (!@file_get_contents($checker_url)) {	
			$infos = "Decisions could not be saved.";
		} 
	} else {
		$infos = "Decisions could not be saved.";
	}
}

?>
	<tr>
		<td colspan="2" valign="top" align="left" class="row1">
		<?php 					
		print_infos($infos);
		if ($_POST['text'] != '') { 
			//save temp file
			$temp_file = write_temp_file();
			
			$pg_url =$_base_href.'content/'.$temp_file;	
			//$pg_url = 'http://www.amazon.com:80/exec/obidos/subst/home/home.html';			
			$checker_url='http://tile-cridpath.atrc.utoronto.ca/acheck/servlet/Checkacc?file='.urlencode($pg_url).'&guide=wcag-2-0-html-techs&output=chunk&line=5';
							
			if ($report = @file_get_contents($checker_url)) {	
				if ($report == -1) {
					$errors = "Localhost is not a valid URL";
					print_errors($errors);
				} else {
					echo '<input type="hidden" name="pg_url" value="'.$pg_url.'" />';
					echo $report;	
					echo '<p align="center"><br /><input value="Make Decisions" type="submit" name="desc_submit" id="submit1" /></p><p>Accessibility report provided by <a title="ATRC Web Accessibility Checker (link opens in a new window)" target="_new" href="http://checker.atrc.utoronto.ca?lang=eng">ATRC Web Accessibility Checker</a>.</p>';
				}
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

//debug($pg_url);
		?>
		
		</td>
	</tr>