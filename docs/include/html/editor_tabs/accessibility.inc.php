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
// $Id: accessibility.inc.php,v 1.21 2004/05/03 19:58:18 boonhau Exp $
if (!defined('AT_INCLUDE_PATH')) { exit; }

//make decisions
if ($_POST['desc_submit']) {
	//get list of decisions	
	$desc_query = '';
	if (is_array($_POST['d'])) {
		for($i=0; $i<count($_POST['d']); $i++) {
			$desc_query .= '&'.$i.'='.$_POST['d'][$i];
		}

		$checker_url = 'http://tile-cridpath.atrc.utoronto.ca/acheck/servlet/decisions;'
					.'jsessionid='.$_POST['sessionid']
					.'?file='.urlencode($_POST['pg_url'])
					.'&output=chunk'
					.'&name='.$_SESSION['login']
					.'&email='.urlencode($_base_href)
					.$desc_query;
		if (@file_get_contents($checker_url) === false) {
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
		if ($_POST['body_text'] != '') {
			//save temp file
			$_POST['content_path'] = $content_row['content_path'];
			$temp_file = write_temp_file();

			$pg_url =$_base_href.'content/'.$temp_file;	$checker_url='http://tile-cridpath.atrc.utoronto.ca/acheck/servlet/Checkacc?file='.urlencode($pg_url).'&guide=wcag-1-0-aa&output=chunk&line=5';
			$report = @file_get_contents($checker_url);

			if ($report == 1) {
				$errors = 'Localhost is not a valid URL';
				print_errors($errors);
			} else if ($report === false) {
				$infos = "Service currently unavailable.";
				print_infos($infos);
			} else {
				echo '<input type="hidden" name="pg_url" value="'.$pg_url.'" />';
				echo $report;	

				echo '<p>Accessibility report provided by <a title="ATRC Web Accessibility Checker (link opens in a new window)" target="_new" href="http://checker.atrc.utoronto.ca?lang=eng">ATRC Web Accessibility Checker</a>.</p>';
			}
			//delete file
			@unlink('../content/'.$temp_file);

		} else {
			$infos[] = AT_INFOS_NO_PAGE_CONTENT;
			print_infos($infos);	
		} 

	?>
		</td>
	</tr>
