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


function get_tabs() {
	//these are the _AT(x) variable names and their include file
	$tabs = array( 
		array("content","edit_tab.inc.php"), 
		array("properties","properties_tab.inc.php"), 
		array("keywords","keywords_tab.inc.php"), 
		array("preview","edit_tab.inc.php")
	);
	return $tabs;
}

function output_tabs($current_tab) { 
	$tabs = get_tabs();	
	echo '<table cellspacing="0" cellpadding="0" width="90%" border="0" summary="" align="center"><tr height="25">';

	echo '<td>&nbsp;</td>';
	foreach($tabs as $tab) {
		if ($current_tab == $tab[0]) {
			echo '<td class="etabself" width="25%">'._AT($tab[0]).'</td>';
		} else {
			echo '<td class="etab" width="25%"><input type="submit" name="submit" value="'._AT($tab[0]).'" class="buttontab" accesskey="s" /></td>';
		}	
		echo '<td>&nbsp;</td>';
	}	
	echo '</tr><table>';
}

// save all changes to the DB
function tab_process($current_tab) {
	$tabs = get_tabs();	
	global $contentManager;
	$changes = false;
	$errors="";

	$_POST['pid']	= intval($_POST['pid']);
	$_POST['cid']	= intval($_POST['cid']);

	$_POST['title'] = trim($_POST['title']);
	$_POST['text']	= trim($_POST['text']);
	$_POST['formatting'] = intval($_POST['formatting']);

	$_POST['keywords']	= trim($_POST['keywords']);

	$day	= intval($_POST['day']);
	$month	= intval($_POST['month']);
	$year	= intval($_POST['year']);
	$hour	= intval($_POST['hour']);
	$min	= intval($_POST['min']);

	if (!checkdate($month, $day, $year)) {
		$errors[] = AT_ERROR_BAD_DATE;		
	}

	if (strlen($month) == 1){
		$month = "0$month";
	}
	if (strlen($day) == 1){
		$day = "0$day";
	}
	if (strlen($hour) == 1){
		$hour = "0$hour";
	}
	if (strlen($min) == 1){
		$min = "0$min";
	}
	$release_date = "$year-$month-$day $hour:$min:00";

	if( ($_POST['submit_file'] == 'Upload') && ($_FILES['uploadedfile']['name'] == ''))	{
		$errors[] = AT_ERROR_FILE_NOT_SELECTED;
	} else if ($_POST['submit_file']) {
		if ($_FILES['uploadedfile']['name']
			&& (($_FILES['uploadedfile']['type'] == 'text/plain')
			|| ($_FILES['uploadedfile']['type'] == 'text/html')) )
		{
			$_POST['text'] = file_get_contents($_FILES['uploadedfile']['tmp_name']);

			$path_parts = pathinfo($_FILES['uploadedfile']['name']);
			$ext = strtolower($path_parts['extension']);
			if (in_array($ext, array('html', 'htm'))) {
				/* get the <title></title> of this page				*/

				$start_pos	= strpos(strtolower($_POST['text']), '<title>');
				$end_pos	= strpos(strtolower($_POST['text']), '</title>');

				if (($start_pos !== false) && ($end_pos !== false)) {
					$start_pos += strlen('<title>');
					$_POST['title'] = trim(substr($_POST['text'], $start_pos, $end_pos-$start_pos));
				}

				unset($start_pos);
				unset($end_pos);

				/* strip everything before <body> */
				$start_pos	= strpos(strtolower($_POST['text']), '<body');
				if ($start_pos !== false) {
					$start_pos	+= strlen('<body');
					$end_pos	= strpos(strtolower($_POST['text']), '>', $start_pos);
					$end_pos	+= strlen('>');

					$_POST['text'] = substr($_POST['text'], $end_pos);
				}

				/* strip everything after </body> */
				$end_pos	= strpos(strtolower($_POST['text']), '</body>');
				if ($end_pos !== false) {
					$_POST['text'] = trim(substr($_POST['text'], 0, $end_pos));
				}

				/* change formatting to HTML? */
				/* $_POST['formatting']	= 1; */
			}
			$feedback[]=AT_FEEDBACK_FILE_PASTED;
		} else {
			$errors[] = AT_ERROR_BAD_FILE_TYPE;
		}
	}

	if ($_POST['save']) {
		if ($_POST['title'] == '') {
			$errors[] = AT_ERROR_NO_TITLE;
		}
		
		if ($errors == '') {
			if($_POST['cid']) {
				$release_date = date('Y-m-d');
				$err = $contentManager->editContent($_POST['cid'], $_POST['title'], $_POST['text'], $_POST['keywords'], $_POST['new_ordering'], $_POST['related'], $_POST['formatting'], $_POST['move'], $release_date);
			}
		
			/* check if a definition is being used that isn't already in the glossary */
			$r = count(find_terms(&$_POST['text']));				
			if ($r != 0) {
				/* redirect to add glossery terms, but we do not know if those have been defined or not */
				Header('Location: add_new_glossary.php?pcid='.$_POST['cid']);
				exit;
			} 
		}	
	} 
	// check for changes between content and DB
	$result = $contentManager->getContentPage($_POST['cid']);

	if ($row = @mysql_fetch_assoc($result) ) {
		//compare post to db variable
		$_POST['title']		!= $row['title']			? $changes=true : '' ;
		$_POST['text']		!= $row['text']				? $changes=true : '' ;
		$_POST['formatting']!= $row['formatting']		? $changes=true : '' ;
		//$_POST['new_ordering']!= $row['new_ordering']	? $changes=true : '' ;

		//$release_date		!= $row['release_date']		? $changes=true : '' ;
		$_POST['related']	!= $row['related']			? $changes=true : '' ;

		$_POST['keywords']	!= $row['keywords']			? $changes=true : '' ;
	}

	return $changes;
}

?>